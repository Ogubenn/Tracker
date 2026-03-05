<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BinaCalismaDurumu;
use App\Models\Bina;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BinaCalismaDurumuController extends Controller
{
    /**
     * Bina için bugün çalışmadı kaydı oluştur
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'bina_id' => 'required|exists:binalar,id',
            'tarih' => 'required|date',
            'aciklama' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $bina = Bina::findOrFail($validated['bina_id']);
            $tarih = Carbon::parse($validated['tarih']);

            // Aynı gün için kayıt var mı kontrol et
            $mevcutKayit = BinaCalismaDurumu::where('bina_id', $validated['bina_id'])
                ->where('tarih', $tarih->format('Y-m-d'))
                ->first();

            if ($mevcutKayit) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bu tarih için zaten bir kayıt mevcut.',
                ], 400);
            }

            // Yeni kayıt oluştur
            $kayit = BinaCalismaDurumu::create([
                'bina_id' => $validated['bina_id'],
                'tarih' => $tarih,
                'durum' => 'calismadi',
                'kullanici_id' => auth()->id(),
                'aciklama' => $validated['aciklama'] ?? null,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "$bina->bina_adi için {$tarih->format('d.m.Y')} tarihinde \"Çalışmadı\" kaydı oluşturuldu.",
                'data' => $kayit,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Bir hata oluştu: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Çalışmadı kaydını iptal et (sil)
     */
    public function destroy(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'bina_id' => 'required|exists:binalar,id',
            'tarih' => 'required|date',
        ]);

        try {
            $tarih = Carbon::parse($validated['tarih']);
            
            $kayit = BinaCalismaDurumu::where('bina_id', $validated['bina_id'])
                ->where('tarih', $tarih->format('Y-m-d'))
                ->first();

            if (!$kayit) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bu tarih için "Çalışmadı" kaydı bulunamadı.',
                ], 404);
            }

            $bina = $kayit->bina;
            $kayit->delete();

            return response()->json([
                'success' => true,
                'message' => "$bina->bina_adi için {$tarih->format('d.m.Y')} tarihindeki \"Çalışmadı\" kaydı iptal edildi.",
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Bir hata oluştu: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Belirli bir bina ve tarih için çalışma durumunu kontrol et
     */
    public function check(int $binaId, string $tarih): JsonResponse
    {
        try {
            $tarihObj = Carbon::parse($tarih);
            
            $kayit = BinaCalismaDurumu::where('bina_id', $binaId)
                ->where('tarih', $tarihObj->format('Y-m-d'))
                ->where('durum', 'calismadi')
                ->first();

            return response()->json([
                'success' => true,
                'calismadi' => $kayit !== null,
                'kayit' => $kayit,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Bir hata oluştu: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * Belirli bir bina için tarih aralığındaki çalışmadı kayıtlarını listele
     */
    public function liste(Request $request, int $binaId): JsonResponse
    {
        try {
            $baslangic = $request->get('baslangic', Carbon::today()->subDays(30)->format('Y-m-d'));
            $bitis = $request->get('bitis', Carbon::today()->format('Y-m-d'));
            
            $kayitlar = BinaCalismaDurumu::where('bina_id', $binaId)
                ->whereBetween('tarih', [$baslangic, $bitis])
                ->where('durum', 'calismadi')
                ->orderBy('tarih', 'desc')
                ->get(['tarih', 'aciklama', 'created_at']);

            return response()->json([
                'success' => true,
                'kayitlar' => $kayitlar,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Bir hata oluştu: ' . $e->getMessage(),
            ], 500);
        }
    }
}
