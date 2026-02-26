<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IsTakvimi;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class IsTakvimiController extends Controller
{
    /**
     * Takvim sayfası
     */
    public function index(Request $request)
    {
        $kullanicilar = User::orderBy('ad')->get();
        
        return view('admin.is-takvimi.index', compact('kullanicilar'));
    }

    /**
     * Takvim verileri (JSON)
     */
    public function getEvents(Request $request)
    {
        $start = Carbon::parse($request->start);
        $end = Carbon::parse($request->end);
        
        // Gecikenleri güncelle
        IsTakvimi::gecikenleriGuncelle();
        
        try {
            $isler = IsTakvimi::with(['atananKullanici', 'atananKullanicilar'])
                ->whereBetween('tarih', [$start, $end])
                ->get();
        } catch (\Exception $e) {
            // Pivot tablo henüz yoksa sadece tek kullanıcı ile çalış
            $isler = IsTakvimi::with('atananKullanici')
                ->whereBetween('tarih', [$start, $end])
                ->get();
        }
        
        $events = $isler->map(function($is) {
            $atananlar = [];
            $atananIds = [];
            
            // Pivot tablo varsa çoklu kullanıcıları al
            try {
                if (method_exists($is, 'atananKullanicilar') && $is->relationLoaded('atananKullanicilar')) {
                    $atananlar = $is->atananKullanicilar->pluck('ad')->toArray();
                    $atananIds = $is->atananKullanicilar->pluck('id')->toArray();
                }
            } catch (\Exception $e) {
                // Pivot tablo yoksa boş bırak
            }
            
            // Eğer çoklu kullanıcı yoksa tek kullanıcıyı kullan
            if (empty($atananlar) && $is->atananKullanici) {
                $atananlar = [$is->atananKullanici->ad];
            }
            if (empty($atananIds) && $is->atananKullanici) {
                $atananIds = [$is->atanan_kullanici_id];
            }
            
            return [
                'id' => $is->id,
                'title' => $is->baslik,
                'start' => $is->tarih->format('Y-m-d'),
                'backgroundColor' => $is->renk,
                'borderColor' => $is->renk,
                'extendedProps' => [
                    'atananlar' => $atananlar,
                    'atananIds' => $atananIds,
                    'durum' => $is->durum,
                    'renk_kategori' => $is->renk_kategori,
                    'tekrarli_mi' => $is->tekrarli_mi,
                    'tekrar_gun' => $is->tekrar_gun,
                ],
            ];
        });
        
        return response()->json($events);
    }

    /**
     * Otomatik tamamlama için iş başlıklarını getir
     */
    public function getBasliklar()
    {
        $basliklar = IsTakvimi::select('baslik')
            ->distinct()
            ->orderBy('baslik')
            ->pluck('baslik')
            ->toArray();
        
        return response()->json($basliklar);
    }

    /**
     * Yeni iş oluştur
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'baslik' => 'required|string|max:255',
            'tarih' => 'required|date',
            'atanan_kullanici_ids' => 'nullable|array',
            'atanan_kullanici_ids.*' => 'exists:users,id',
            'atanan_kullanici_id' => 'nullable|exists:users,id',
            'renk_kategori' => 'required|in:normal,gece',
            'tekrarli_mi' => 'boolean',
        ]);
        
        // Kullanıcı ID'lerini belirle
        $kullaniciIds = $validated['atanan_kullanici_ids'] ?? [];
        if (empty($kullaniciIds) && isset($validated['atanan_kullanici_id'])) {
            $kullaniciIds = [$validated['atanan_kullanici_id']];
        }
        
        // İş oluştur (kullanıcı seçimi opsiyonel)
        $is = IsTakvimi::create([
            'baslik' => $validated['baslik'],
            'tarih' => $validated['tarih'],
            'atanan_kullanici_id' => !empty($kullaniciIds) ? $kullaniciIds[0] : null,
            'renk_kategori' => $validated['renk_kategori'],
            'tekrarli_mi' => $validated['tekrarli_mi'] ?? false,
            'tekrar_gun' => $validated['tekrarli_mi'] ? Carbon::parse($validated['tarih'])->day : null,
        ]);
        
        // Tüm kullanıcıları pivot tabloya ekle
        try {
            if (method_exists($is, 'atananKullanicilar') && count($kullaniciIds) > 0) {
                $is->atananKullanicilar()->attach($kullaniciIds);
            }
        } catch (\Exception $e) {
            \Log::error('Pivot attach hatası: ' . $e->getMessage());
        }
        
        return response()->json([
            'success' => true,
            'message' => 'İş başarıyla eklendi',
            'is' => $is->load('atananKullanici'),
        ]);
    }

    /**
     * İş güncelle
     */
    public function update(Request $request, $id)
    {
        $isTakvimi = IsTakvimi::findOrFail($id);
        
        $validated = $request->validate([
            'baslik' => 'sometimes|required|string|max:255',
            'tarih' => 'sometimes|required|date',
            'atanan_kullanici_ids' => 'sometimes|nullable|array|min:1',
            'atanan_kullanici_ids.*' => 'exists:users,id',
            'atanan_kullanici_id' => 'sometimes|nullable|exists:users,id',
            'renk_kategori' => 'sometimes|required|in:normal,gece',
            'tekrarli_mi' => 'boolean',
        ]);
        
        // Ana alanları güncelle
        $updateData = [];
        if (isset($validated['baslik'])) $updateData['baslik'] = $validated['baslik'];
        if (isset($validated['tarih'])) $updateData['tarih'] = $validated['tarih'];
        if (isset($validated['renk_kategori'])) $updateData['renk_kategori'] = $validated['renk_kategori'];
        if (isset($validated['tekrarli_mi'])) {
            $updateData['tekrarli_mi'] = $validated['tekrarli_mi'];
            $updateData['tekrar_gun'] = $validated['tekrarli_mi'] ? Carbon::parse($validated['tarih'] ?? $isTakvimi->tarih)->day : null;
        }
        
        // Kullanıcı ID'lerini belirle
        $kullaniciIds = $validated['atanan_kullanici_ids'] ?? [];
        if (empty($kullaniciIds) && isset($validated['atanan_kullanici_id'])) {
            $kullaniciIds = [$validated['atanan_kullanici_id']];
        }
        
        // Ana kullanıcıyı güncelle (boş olabilir)
        if (isset($validated['atanan_kullanici_ids']) || isset($validated['atanan_kullanici_id'])) {
            $updateData['atanan_kullanici_id'] = !empty($kullaniciIds) ? $kullaniciIds[0] : null;
        }
        
        $isTakvimi->update($updateData);
        
        // Kullanıcıları güncelle (pivot tablo varsa) - boş array sync de yapılabilir
        if (isset($validated['atanan_kullanici_ids'])) {
            try {
                if (method_exists($isTakvimi, 'atananKullanicilar')) {
                    $isTakvimi->atananKullanicilar()->sync($kullaniciIds);
                }
            } catch (\Exception $e) {
                \Log::error('Pivot sync hatası: ' . $e->getMessage());
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => 'İş başarıyla güncellendi',
            'is' => $isTakvimi->fresh()->load('atananKullanici'),
        ]);
    }

    /**
     * Durum değiştir (tamamlandı/bekliyor)
     */
    public function toggleDurum($id)
    {
        $isTakvimi = IsTakvimi::findOrFail($id);
        if ($isTakvimi->durum === 'tamamlandi') {
            $isTakvimi->update(['durum' => 'bekliyor']);
        } else {
            $isTakvimi->update(['durum' => 'tamamlandi']);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Durum güncellendi',
            'durum' => $isTakvimi->durum,
            'renk' => $isTakvimi->renk,
        ]);
    }

    /**
     * İş sil
     */
    public function destroy($id)
    {
        $isTakvimi = IsTakvimi::findOrFail($id);
        $isTakvimi->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'İş başarıyla silindi',
        ]);
    }

    /**
     * Tarihi değiştir (drag & drop)
     */
    public function updateTarih(Request $request, $id)
    {
        $isTakvimi = IsTakvimi::findOrFail($id);
        $validated = $request->validate([
            'tarih' => 'required|date',
        ]);
        
        $yeniTarih = Carbon::parse($validated['tarih']);
        
        $isTakvimi->update([
            'tarih' => $yeniTarih,
            'durum' => 'bekliyor',
            'tekrar_gun' => $isTakvimi->tekrarli_mi ? $yeniTarih->day : $isTakvimi->tekrar_gun,
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Tarih güncellendi',
        ]);
    }

    /**
     * Tekrarlı işleri bir sonraki aya kopyala
     */
    public function copyTekrarliIsler()
    {
        $tekrarliIsler = IsTakvimi::where('tekrarli_mi', true)->get();
        
        $kopyalananSayisi = 0;
        
        foreach ($tekrarliIsler as $is) {
            $sonrakiAy = Carbon::now()->addMonth();
            $yeniTarih = Carbon::create(
                $sonrakiAy->year,
                $sonrakiAy->month,
                min($is->tekrar_gun, $sonrakiAy->daysInMonth)
            );
            
            // Bu tarihte zaten varsa oluşturma
            $mevcutMu = IsTakvimi::where('baslik', $is->baslik)
                ->whereDate('tarih', $yeniTarih)
                ->exists();
            
            if (!$mevcutMu) {
                IsTakvimi::create([
                    'baslik' => $is->baslik,
                    'aciklama' => $is->aciklama,
                    'tarih' => $yeniTarih,
                    'atanan_kullanici_id' => $is->atanan_kullanici_id,
                    'renk_kategori' => $is->renk_kategori,
                    'tekrarli_mi' => true,
                    'tekrar_gun' => $is->tekrar_gun,
                    'durum' => 'bekliyor',
                ]);
                
                $kopyalananSayisi++;
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => "$kopyalananSayisi tekrarlı iş sonraki aya kopyalandı",
        ]);
    }
}
