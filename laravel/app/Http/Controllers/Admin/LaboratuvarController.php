<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LaboratuvarRapor;
use App\Models\LaboratuvarParametre;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class LaboratuvarController extends Controller
{
    public function index(Request $request)
    {
        $query = LaboratuvarRapor::with(['parametreler', 'olusturan']);

        // Filtreleme
        if ($request->filled('baslangic_tarihi')) {
            $query->where('rapor_tarihi', '>=', $request->baslangic_tarihi);
        }

        if ($request->filled('bitis_tarihi')) {
            $query->where('rapor_tarihi', '<=', $request->bitis_tarihi);
        }

        if ($request->filled('tesis')) {
            $query->where('tesis_adi', 'like', '%' . $request->tesis . '%');
        }

        if ($request->filled('rapor_no')) {
            $query->where('rapor_no', 'like', '%' . $request->rapor_no . '%');
        }

        $raporlar = $query->orderBy('rapor_tarihi', 'desc')
            ->paginate(20)
            ->appends($request->all());

        return view('admin.laboratuvar.index', compact('raporlar'));
    }

    public function create()
    {
        return view('admin.laboratuvar.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'rapor_no' => 'required|string|unique:laboratuvar_raporlar,rapor_no',
            'rapor_tarihi' => 'required|date',
            'olusturan_id' => 'required|exists:users,id',
            'tesis_adi' => 'required|string',
            'numune_alma_noktasi' => 'nullable|string',
            'numune_alma_tarihi' => 'nullable|date',
            'numune_alma_sekli' => 'nullable|string',
            'numune_gelis_sekli' => 'nullable|string',
            'numune_ambalaj' => 'nullable|string',
            'numune_numarasi' => 'nullable|string',
            'lab_gelis_tarihi' => 'nullable|date',
            'sahit_numune' => 'nullable|string',
            'analiz_baslangic' => 'nullable|date',
            'analiz_bitis' => 'nullable|date',
            'notlar' => 'nullable|string',
            'pdf_dosya' => 'nullable|file|mimes:pdf|max:10240',
            'parametreler' => 'required|array|min:1',
            'parametreler.*.parametre_adi' => 'required|string',
            'parametreler.*.birim' => 'nullable|string',
            'parametreler.*.analiz_sonucu' => 'nullable|numeric',
            'parametreler.*.limit_degeri' => 'nullable|string',
            'parametreler.*.analiz_metodu' => 'nullable|string',
            'parametreler.*.tablo_no' => 'nullable|integer',
        ]);

        try {
            DB::beginTransaction();

            // PDF yükleme
            $pdfPath = null;
            if ($request->hasFile('pdf_dosya')) {
                $pdfPath = $request->file('pdf_dosya')->store('laboratuvar_raporlar', 'public');
            }

            // Rapor oluştur
            $rapor = LaboratuvarRapor::create([
                'rapor_no' => $validated['rapor_no'],
                'rapor_tarihi' => $validated['rapor_tarihi'],
                'tesis_adi' => $validated['tesis_adi'],
                'numune_alma_noktasi' => $validated['numune_alma_noktasi'] ?? null,
                'numune_alma_tarihi' => $validated['numune_alma_tarihi'] ?? null,
                'numune_alma_sekli' => $validated['numune_alma_sekli'] ?? null,
                'numune_gelis_sekli' => $validated['numune_gelis_sekli'] ?? null,
                'numune_ambalaj' => $validated['numune_ambalaj'] ?? null,
                'numune_numarasi' => $validated['numune_numarasi'] ?? null,
                'lab_gelis_tarihi' => $validated['lab_gelis_tarihi'] ?? null,
                'sahit_numune' => $validated['sahit_numune'] ?? null,
                'analiz_baslangic' => $validated['analiz_baslangic'] ?? null,
                'analiz_bitis' => $validated['analiz_bitis'] ?? null,
                'notlar' => $validated['notlar'] ?? null,
                'pdf_dosya' => $pdfPath,
                'olusturan_id' => $validated['olusturan_id'],
            ]);

            // Parametreleri ekle
            foreach ($validated['parametreler'] as $paramData) {
                $parametre = $rapor->parametreler()->create([
                    'parametre_adi' => $paramData['parametre_adi'],
                    'birim' => $paramData['birim'] ?? null,
                    'analiz_sonucu' => $paramData['analiz_sonucu'] ?? null,
                    'limit_degeri' => $paramData['limit_degeri'] ?? null,
                    'analiz_metodu' => $paramData['analiz_metodu'] ?? null,
                    'tablo_no' => $paramData['tablo_no'] ?? null,
                ]);

                // Uygunluk hesapla
                $uygunluk = $parametre->hesaplaUygunluk();
                $parametre->update(['uygunluk' => $uygunluk]);
            }

            DB::commit();

            return redirect()
                ->route('admin.laboratuvar.show', $rapor->id)
                ->with('success', '✅ Rapor başarıyla kaydedildi.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            if (isset($pdfPath) && $pdfPath) {
                Storage::disk('public')->delete($pdfPath);
            }

            \Log::error('Laboratuvar raporu kaydetme hatası: ' . $e->getMessage());
            
            return redirect()
                ->back()
                ->with('error', 'Hata: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show($id)
    {
        $rapor = LaboratuvarRapor::with(['parametreler', 'olusturan'])->findOrFail($id);
        
        return view('admin.laboratuvar.show', compact('rapor'));
    }

    public function edit($id)
    {
        $rapor = LaboratuvarRapor::with('parametreler')->findOrFail($id);
        
        return view('admin.laboratuvar.edit', compact('rapor'));
    }

    public function update(Request $request, $id)
    {
        $rapor = LaboratuvarRapor::findOrFail($id);

        $validated = $request->validate([
            'rapor_no' => 'required|string|unique:laboratuvar_raporlar,rapor_no,' . $id,
            'rapor_tarihi' => 'required|date',
            'tesis_adi' => 'required|string',
            'numune_alma_noktasi' => 'nullable|string',
            'numune_alma_tarihi' => 'nullable|date',
            'numune_alma_sekli' => 'nullable|string',
            'numune_gelis_sekli' => 'nullable|string',
            'numune_ambalaj' => 'nullable|string',
            'numune_numarasi' => 'nullable|string',
            'lab_gelis_tarihi' => 'nullable|date',
            'sahit_numune' => 'nullable|string',
            'analiz_baslangic' => 'nullable|date',
            'analiz_bitis' => 'nullable|date',
            'notlar' => 'nullable|string',
            'pdf_dosya' => 'nullable|file|mimes:pdf|max:10240',
            'parametreler' => 'required|array|min:1',
            'parametreler.*.id' => 'nullable|exists:laboratuvar_parametreler,id',
            'parametreler.*.parametre_adi' => 'required|string',
            'parametreler.*.birim' => 'nullable|string',
            'parametreler.*.analiz_sonucu' => 'nullable|numeric',
            'parametreler.*.limit_degeri' => 'nullable|string',
            'parametreler.*.analiz_metodu' => 'nullable|string',
            'parametreler.*.tablo_no' => 'nullable|integer',
        ]);

        try {
            DB::beginTransaction();

            // PDF yükleme
            if ($request->hasFile('pdf_dosya')) {
                // Eski PDF'yi sil
                if ($rapor->pdf_dosya) {
                    Storage::disk('public')->delete($rapor->pdf_dosya);
                }
                
                $validated['pdf_dosya'] = $request->file('pdf_dosya')->store('laboratuvar_raporlar', 'public');
            }

            // Raporu güncelle
            $rapor->update($validated);

            // Mevcut parametreleri sil
            $rapor->parametreler()->delete();

            // Yeni parametreleri ekle
            foreach ($validated['parametreler'] as $paramData) {
                $parametre = $rapor->parametreler()->create([
                    'parametre_adi' => $paramData['parametre_adi'],
                    'birim' => $paramData['birim'] ?? null,
                    'analiz_sonucu' => $paramData['analiz_sonucu'] ?? null,
                    'limit_degeri' => $paramData['limit_degeri'] ?? null,
                    'analiz_metodu' => $paramData['analiz_metodu'] ?? null,
                    'tablo_no' => $paramData['tablo_no'] ?? null,
                ]);

                $uygunluk = $parametre->hesaplaUygunluk();
                $parametre->update(['uygunluk' => $uygunluk]);
            }

            DB::commit();

            return redirect()
                ->route('admin.laboratuvar.show', $rapor->id)
                ->with('success', '✅ Rapor başarıyla güncellendi.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Laboratuvar raporu güncelleme hatası: ' . $e->getMessage());
            
            return redirect()
                ->back()
                ->with('error', 'Hata: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $rapor = LaboratuvarRapor::findOrFail($id);

            // PDF'yi sil
            if ($rapor->pdf_dosya) {
                Storage::disk('public')->delete($rapor->pdf_dosya);
            }

            $rapor->delete();

            return redirect()
                ->route('admin.laboratuvar.index')
                ->with('success', '✅ Rapor başarıyla silindi.');

        } catch (\Exception $e) {
            \Log::error('Laboratuvar raporu silme hatası: ' . $e->getMessage());
            
            return redirect()
                ->back()
                ->with('error', 'Hata: ' . $e->getMessage());
        }
    }

    public function excelImport()
    {
        return view('admin.laboratuvar.excel-import');
    }

    public function excelImportStore(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            DB::beginTransaction();

            $file = $request->file('excel_file');
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            // İlk satır başlık
            $header = array_shift($rows);

            $kaydedilenSayisi = 0;
            $hatalar = [];

            foreach ($rows as $rowIndex => $row) {
                $rowNumber = $rowIndex + 2; // Excel'de satır numarası

                // Boş satırları atla
                if (empty(array_filter($row))) {
                    continue;
                }

                try {
                    // Excel'den veri çek
                    $raporNo = $row[0] ?? null;
                    $raporTarihi = $row[1] ?? null;
                    $tesisAdi = $row[2] ?? null;
                    $parametreAdi = $row[3] ?? null;
                    $birim = $row[4] ?? null;
                    $analizSonucu = $row[5] ?? null;
                    $limitDegeri = $row[6] ?? null;
                    $analizMetodu = $row[7] ?? null;
                    $tabloNo = $row[8] ?? null;

                    if (empty($raporNo) || empty($parametreAdi)) {
                        $hatalar[] = "Satır {$rowNumber}: Rapor No veya Parametre Adı boş";
                        continue;
                    }

                    // Rapor var mı kontrol et
                    $rapor = LaboratuvarRapor::where('rapor_no', $raporNo)->first();

                    if (!$rapor) {
                        // Yeni rapor oluştur
                        $rapor = LaboratuvarRapor::create([
                            'rapor_no' => $raporNo,
                            'rapor_tarihi' => $raporTarihi ? Carbon::parse($raporTarihi) : now(),
                            'tesis_adi' => $tesisAdi ?? 'Belirtilmemiş',
                            'olusturan_id' => auth()->id(),
                        ]);
                    }

                    // Parametre ekle
                    $parametre = $rapor->parametreler()->create([
                        'parametre_adi' => $parametreAdi,
                        'birim' => $birim,
                        'analiz_sonucu' => is_numeric($analizSonucu) ? $analizSonucu : null,
                        'limit_degeri' => $limitDegeri,
                        'analiz_metodu' => $analizMetodu,
                        'tablo_no' => is_numeric($tabloNo) ? $tabloNo : null,
                    ]);

                    $uygunluk = $parametre->hesaplaUygunluk();
                    $parametre->update(['uygunluk' => $uygunluk]);

                    $kaydedilenSayisi++;

                } catch (\Exception $e) {
                    $hatalar[] = "Satır {$rowNumber}: " . $e->getMessage();
                }
            }

            DB::commit();

            $mesaj = "✅ {$kaydedilenSayisi} kayıt başarıyla içe aktarıldı.";
            
            if (count($hatalar) > 0) {
                $mesaj .= " " . count($hatalar) . " satırda hata oluştu.";
            }

            return redirect()
                ->route('admin.laboratuvar.index')
                ->with('success', $mesaj)
                ->with('hatalar', $hatalar);

        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Excel import hatası: ' . $e->getMessage());
            
            return redirect()
                ->back()
                ->with('error', 'Hata: ' . $e->getMessage());
        }
    }

    public function grafikler(Request $request)
    {
        // Tüm benzersiz parametreleri getir
        $parametreListesi = LaboratuvarParametre::select('parametre_adi')
            ->distinct()
            ->orderBy('parametre_adi')
            ->pluck('parametre_adi');

        $seciliParametre = $request->filled('parametre') ? $request->parametre : $parametreListesi->first();

        // Seçili parametrenin verilerini getir
        $veriler = [];
        if ($seciliParametre) {
            $veriler = LaboratuvarParametre::with('rapor')
                ->where('parametre_adi', $seciliParametre)
                ->whereHas('rapor')
                ->get()
                ->map(function($param) {
                    return [
                        'tarih' => $param->rapor->rapor_tarihi->format('Y-m-d'),
                        'deger' => $param->analiz_sonucu,
                        'limit' => $param->limit_degeri,
                        'uygunluk' => $param->uygunluk,
                    ];
                })
                ->sortBy('tarih')
                ->values();
        }

        return view('admin.laboratuvar.grafikler', compact('parametreListesi', 'seciliParametre', 'veriler'));
    }

    public function excelTemplate()
    {
        try {
            // CSV formatında indir (PhpSpreadsheet yok)
            $fileName = 'laboratuvar_rapor_template_' . now()->format('Y-m-d') . '.csv';
            
            $headers = [
                'Rapor No',
                'Rapor Tarihi',
                'Tesis Adı',
                'Parametre Adı',
                'Birim',
                'Analiz Sonucu',
                'Limit Değeri',
                'Analiz Metodu',
                'Tablo No'
            ];
            
            $ornek = [
                'T-79051-2025-03',
                '15.05.2025',
                'Bulancak Belediyesi Su ve Kanalizasyon İşletme Müdürlüğü',
                'Biyokimyasal Oksijen İhtiyacı',
                'mg/L',
                '4.05',
                '25',
                'SM 5210 B',
                '1'
            ];
            
            $callback = function() use ($headers, $ornek) {
                $file = fopen('php://output', 'w');
                
                // UTF-8 BOM ekle (Excel için)
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                
                // Başlıklar
                fputcsv($file, $headers, ';');
                
                // Örnek veri
                fputcsv($file, $ornek, ';');
                
                fclose($file);
            };
            
            return response()->streamDownload($callback, $fileName, [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Cache-Control' => 'max-age=0',
                'Pragma' => 'public',
            ]);

        } catch (\Exception $e) {
            \Log::error('Excel template hatası: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Excel şablonu oluşturulurken hata oluştu: ' . $e->getMessage());
        }
    }
}
