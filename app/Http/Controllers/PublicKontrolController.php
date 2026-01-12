<?php

namespace App\Http\Controllers;

use App\Models\Bina;
use App\Models\KontrolKaydi;
use App\Models\KontrolMaddesi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PublicKontrolController extends Controller
{
    /**
     * QR kod ile kontrol ekranı
     */
    public function index($uuid)
    {
        $bina = Bina::where('uuid', $uuid)->firstOrFail();
        
        // Aktif kullanıcılar (hem admin hem personel)
        $personeller = User::where('aktif_mi', true)
            ->where('qr_gorunur', true)
            ->orderBy('rol', 'asc') // admin önce
            ->orderBy('ad', 'asc')
            ->get();
        
        // Bugünkü tarihi al
        $bugun = Carbon::today();
        
        // Bu binaya ait aktif kontrol maddeleri
        $kontrolMaddeleri = KontrolMaddesi::where('bina_id', $bina->id)
            ->where('aktif_mi', true)
            ->get()
            ->filter(function ($madde) use ($bina, $bugun) {
                // Bugün için kontrol gerekli mi?
                return $this->kontrolGerekliMi($madde, $bina, $bugun);
            });
        
        return view('public.kontrol', compact('bina', 'kontrolMaddeleri', 'personeller', 'bugun'));
    }
    
    /**
     * Kontrol kaydı oluştur
     */
    public function store(Request $request, $uuid)
    {
        $bina = Bina::where('uuid', $uuid)->firstOrFail();
        
        $validated = $request->validate([
            'personel_id' => 'required|exists:users,id',
            'kayitlar' => 'required|array',
            'kayitlar.*.kontrol_maddesi_id' => 'required|exists:kontrol_maddeleri,id',
            'kayitlar.*.girilen_deger' => 'nullable|string',
            'kayitlar.*.durum' => 'required|in:uygun,uygun_degil,duzeltme_gerekli',
            'genel_aciklama' => 'nullable|string',
            'genel_dosyalar.*' => 'nullable|file|image|max:5120', // 5MB
        ]);
        
        $ipAdresi = $request->ip();
        $tarih = Carbon::today();
        
        // Genel dosyaları yükle
        $genelDosyalar = [];
        if ($request->hasFile('genel_dosyalar')) {
            $files = $request->file('genel_dosyalar');
            foreach ($files as $file) {
                $path = $file->store(
                    "kontrol_kayitlari/{$bina->bina_adi}/" . $tarih->format('Y-m-d'),
                    'public'
                );
                $genelDosyalar[] = $path;
            }
        }
        
        foreach ($validated['kayitlar'] as $kayit) {
            KontrolKaydi::create([
                'bina_id' => $bina->id,
                'kontrol_maddesi_id' => $kayit['kontrol_maddesi_id'],
                'tarih' => $tarih,
                'girilen_deger' => $kayit['girilen_deger'] ?? null,
                'yapan_kullanici_id' => $validated['personel_id'],
                'durum' => $kayit['durum'],
                'aciklama' => $validated['genel_aciklama'] ?? null, // Genel açıklama her kayda
                'onay_durumu' => 'bekliyor',
                'ip_adresi' => $ipAdresi,
                'dosyalar' => $genelDosyalar, // Genel fotoğraflar her kayda
            ]);
        }
        
        return redirect()->back()->with('success', 'Kontrol kayıtları başarıyla oluşturuldu. Admin onayı bekleniyor.');
    }
    
    /**
     * Bugün bu kontrol gerekli mi?
     */
    private function kontrolGerekliMi($madde, $bina, $tarih)
    {
        // Bugün için zaten kayıt yapılmış mı?
        $mevcutKayit = KontrolKaydi::where('bina_id', $bina->id)
            ->where('kontrol_maddesi_id', $madde->id)
            ->whereDate('tarih', $tarih)
            ->exists();
            
        if ($mevcutKayit) {
            return false;
        }
        
        // Periyoda göre kontrol gerekli mi?
        switch ($madde->periyot) {
            case 'gunluk':
                return true;
            case 'haftalik':
                // Son kayıt 7 günden eski mi?
                $sonKayit = KontrolKaydi::where('bina_id', $bina->id)
                    ->where('kontrol_maddesi_id', $madde->id)
                    ->latest('tarih')
                    ->first();
                return !$sonKayit || $sonKayit->tarih->diffInDays($tarih) >= 7;
            case '15_gun':
                $sonKayit = KontrolKaydi::where('bina_id', $bina->id)
                    ->where('kontrol_maddesi_id', $madde->id)
                    ->latest('tarih')
                    ->first();
                return !$sonKayit || $sonKayit->tarih->diffInDays($tarih) >= 15;
            case 'aylik':
                $sonKayit = KontrolKaydi::where('bina_id', $bina->id)
                    ->where('kontrol_maddesi_id', $madde->id)
                    ->latest('tarih')
                    ->first();
                return !$sonKayit || $sonKayit->tarih->diffInMonths($tarih) >= 1;
            default:
                return false;
        }
    }
}
