<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LaboratuvarRapor;
use App\Models\LaboratuvarParametre;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
            'rapor_no' => 'nullable|string|unique:laboratuvar_raporlar,rapor_no',
            'rapor_tarihi' => 'nullable|date',
            'teklif_tarihi' => 'nullable|date',
            'teklif_no' => 'nullable|string',
            'olusturan_id' => 'nullable|exists:users,id',
            'tesis_adi' => 'nullable|string',
            'numune_cinsi_adi' => 'nullable|string',
            'numune_alma_noktasi' => 'nullable|string',
            'numune_alma_noktasi_sayisi' => 'nullable|string',
            'numune_alma_tarihi' => 'nullable|date',
            'numune_alma_tarihi_bitis' => 'nullable|date',
            'numune_alma_sekli' => 'nullable|in:24 Saatlik Kompozit,12 Saatlik Kompozit,Anlık Numune',
            'numune_gelis_sekli' => 'nullable|string',
            'numune_ambalaj' => 'nullable|string',
            'numune_numarasi' => 'nullable|string',
            'lab_gelis_tarihi' => 'nullable|date',
            'sahit_numune' => 'nullable|in:Var,Yok',
            'analiz_baslangic' => 'nullable|date',
            'analiz_bitis' => 'nullable|date',
            'notlar' => 'nullable|string',
            'pdf_dosya' => 'nullable|file|mimes:pdf|max:10240',
            'parametreler' => 'nullable|array',
            'parametreler.*.parametre_adi' => 'nullable|string',
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
                'teklif_tarihi' => $validated['teklif_tarihi'] ?? null,
                'teklif_no' => $validated['teklif_no'] ?? null,
                'tesis_adi' => $validated['tesis_adi'],
                'numune_cinsi_adi' => $validated['numune_cinsi_adi'] ?? null,
                'numune_alma_noktasi' => $validated['numune_alma_noktasi'] ?? null,
                'numune_alma_noktasi_sayisi' => $validated['numune_alma_noktasi_sayisi'] ?? null,
                'numune_alma_tarihi' => $validated['numune_alma_tarihi'] ?? null,
                'numune_alma_tarihi_bitis' => $validated['numune_alma_tarihi_bitis'] ?? null,
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
                'olusturan_id' => $validated['olusturan_id'] ?? null,
            ]);

            // Parametreleri ekle
            foreach (($validated['parametreler'] ?? []) as $paramData) {
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
            'rapor_tarihi' => 'nullable|date',
            'teklif_tarihi' => 'nullable|date',
            'teklif_no' => 'nullable|string',
            'tesis_adi' => 'nullable|string',
            'numune_cinsi_adi' => 'nullable|string',
            'numune_alma_noktasi' => 'nullable|string',
            'numune_alma_noktasi_sayisi' => 'nullable|string',
            'numune_alma_tarihi' => 'nullable|date',
            'numune_alma_tarihi_bitis' => 'nullable|date',
            'numune_alma_sekli' => 'nullable|in:24 Saatlik Kompozit,12 Saatlik Kompozit,Anlık Numune',
            'numune_gelis_sekli' => 'nullable|string',
            'numune_ambalaj' => 'nullable|string',
            'numune_numarasi' => 'nullable|string',
            'lab_gelis_tarihi' => 'nullable|date',
            'sahit_numune' => 'nullable|in:Var,Yok',
            'analiz_baslangic' => 'nullable|date',
            'analiz_bitis' => 'nullable|date',
            'notlar' => 'nullable|string',
            'pdf_dosya' => 'nullable|file|mimes:pdf|max:10240',
            'parametreler' => 'nullable|array',
            'parametreler.*.id' => 'nullable|exists:laboratuvar_parametreler,id',
            'parametreler.*.parametre_adi' => 'nullable|string',
            'parametreler.*.birim' => 'nullable|string',
            'parametreler.*.analiz_sonucu' => 'nullable|numeric',
            'parametreler.*.limit_degeri' => 'nullable|string',
            'parametreler.*.analiz_metodu' => 'nullable|string',
            'parametreler.*.tablo_no' => 'nullable|integer',
        ]);

        try {
            // PDF yükleme — transaction DIŞINDA, bağımsız kaydedilir
            if ($request->hasFile('pdf_dosya')) {
                if ($rapor->pdf_dosya) {
                    Storage::disk('public')->delete($rapor->pdf_dosya);
                }
                $rapor->pdf_dosya = $request->file('pdf_dosya')->store('laboratuvar_raporlar', 'public');
                $rapor->save();
            }

            DB::beginTransaction();

            // Raporu güncelle (pdf_dosya hariç)
            $updateData = collect($validated)->except(['parametreler', 'pdf_dosya'])->toArray();
            $rapor->update($updateData);

            // Mevcut parametreleri sil
            $rapor->parametreler()->delete();

            // Yeni parametreleri ekle
            foreach (($validated['parametreler'] ?? []) as $paramData) {
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

    public function pdfGoster($id)
    {
        $rapor = LaboratuvarRapor::findOrFail($id);

        if (!$rapor->pdf_dosya || !Storage::disk('public')->exists($rapor->pdf_dosya)) {
            abort(404, 'PDF bulunamadı.');
        }

        $path = Storage::disk('public')->path($rapor->pdf_dosya);
        $dosyaAdi = 'rapor-' . ($rapor->rapor_no ?? $rapor->id) . '.pdf';

        return response()->file($path, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $dosyaAdi . '"',
        ]);
    }

    public function destroy($id)
    {        try {
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

}
