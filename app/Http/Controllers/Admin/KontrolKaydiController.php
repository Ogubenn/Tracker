<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KontrolKaydi;
use App\Models\Bina;
use Carbon\Carbon;
use Illuminate\Http\Request;

class KontrolKaydiController extends Controller
{
    /**
     * Kontrol kayıtları listesi (onay sistemi)
     */
    public function index(Request $request)
    {
        $query = KontrolKaydi::with(['bina', 'kontrolMaddesi', 'yapanKullanici', 'onaylayan'])
            ->latest('tarih');
        
        // Filtreler
        if ($request->filled('onay_durumu')) {
            $query->where('onay_durumu', $request->onay_durumu);
        }
        
        if ($request->filled('durum')) {
            $query->where('durum', $request->durum);
        }
        
        if ($request->filled('bina_id')) {
            $query->where('bina_id', $request->bina_id);
        }
        
        if ($request->filled('tarih_baslangic')) {
            $query->whereDate('tarih', '>=', $request->tarih_baslangic);
        }
        
        if ($request->filled('tarih_bitis')) {
            $query->whereDate('tarih', '<=', $request->tarih_bitis);
        }
        
        $kayitlar = $query->paginate(20);
        $binalar = Bina::aktif()->orderBy('bina_adi')->get();
        
        return view('admin.kontrol_kayitlari.index', compact('kayitlar', 'binalar'));
    }
    
    /**
     * Kayıt detayı
     */
    public function show($id)
    {
        $kayit = KontrolKaydi::with(['bina', 'kontrolMaddesi', 'yapanKullanici', 'onaylayan'])
            ->findOrFail($id);
        
        return view('admin.kontrol_kayitlari.show', compact('kayit'));
    }
    
    /**
     * Kaydı onayla
     */
    public function onayla($id)
    {
        $kayit = KontrolKaydi::findOrFail($id);
        
        $kayit->update([
            'onay_durumu' => 'onaylandi',
            'onaylayan_id' => auth()->id(),
            'onay_tarihi' => Carbon::now(),
        ]);
        
        return redirect()->back()->with('success', 'Kayıt onaylandı.');
    }
    
    /**
     * Kaydı reddet
     */
    public function reddet(Request $request, $id)
    {
        $request->validate([
            'admin_notu' => 'required|string|max:500',
        ]);
        
        $kayit = KontrolKaydi::findOrFail($id);
        
        $kayit->update([
            'onay_durumu' => 'reddedildi',
            'admin_notu' => $request->admin_notu,
            'onaylayan_id' => auth()->id(),
            'onay_tarihi' => Carbon::now(),
        ]);
        
        return redirect()->back()->with('success', 'Kayıt reddedildi.');
    }
    
    /**
     * Toplu onaylama
     */
    public function topluOnayla(Request $request)
    {
        $ids = $request->input('ids', []);
        
        KontrolKaydi::whereIn('id', $ids)->update([
            'onay_durumu' => 'onaylandi',
            'onaylayan_id' => auth()->id(),
            'onay_tarihi' => Carbon::now(),
        ]);
        
        return redirect()->back()->with('success', count($ids) . ' kayıt onaylandı.');
    }
}
