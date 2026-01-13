<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KontrolKaydi;
use App\Models\Bina;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KontrolKaydiController extends Controller
{
    public function index(Request $request): View
    {
        $query = $this->getBaseQuery();
        $this->applyFilters($query, $request);
        
        $kayitlar = $query->paginate(20);
        $binalar = Bina::aktif()->orderBy('bina_adi')->get();
        
        return view('admin.kontrol_kayitlari.index', compact('kayitlar', 'binalar'));
    }
    
    public function show(int $id): View
    {
        $kayit = KontrolKaydi::with(['bina', 'kontrolMaddesi', 'yapanKullanici', 'onaylayan'])
            ->findOrFail($id);
        
        return view('admin.kontrol_kayitlari.show', compact('kayit'));
    }
    
    public function onayla(int $id): RedirectResponse
    {
        $kayit = KontrolKaydi::findOrFail($id);
        $this->updateOnayDurumu($kayit, 'onaylandi');
        
        return redirect()->back()->with('success', 'Kayıt onaylandı.');
    }
    
    public function reddet(Request $request, int $id): RedirectResponse
    {
        $this->validateReddetForm($request);
        
        $kayit = KontrolKaydi::findOrFail($id);
        $this->updateOnayDurumuWithNote($kayit, 'reddedildi', $request->admin_notu);
        
        return redirect()->back()->with('success', 'Kayıt reddedildi.');
    }
    
    public function topluOnayla(Request $request): RedirectResponse
    {
        $ids = $request->input('ids', []);
        $this->bulkUpdateOnayDurumu($ids, 'onaylandi');
        
        return redirect()->back()->with('success', count($ids) . ' kayıt onaylandı.');
    }

    private function getBaseQuery(): Builder
    {
        return KontrolKaydi::with(['bina', 'kontrolMaddesi', 'yapanKullanici', 'onaylayan'])
            ->latest('tarih');
    }

    private function applyFilters(Builder $query, Request $request): void
    {
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
    }

    private function updateOnayDurumu(KontrolKaydi $kayit, string $durum): void
    {
        $kayit->update([
            'onay_durumu' => $durum,
            'onaylayan_id' => auth()->id(),
            'onay_tarihi' => Carbon::now(),
        ]);
    }

    private function updateOnayDurumuWithNote(KontrolKaydi $kayit, string $durum, string $note): void
    {
        $kayit->update([
            'onay_durumu' => $durum,
            'admin_notu' => $note,
            'onaylayan_id' => auth()->id(),
            'onay_tarihi' => Carbon::now(),
        ]);
    }

    private function bulkUpdateOnayDurumu(array $ids, string $durum): void
    {
        KontrolKaydi::whereIn('id', $ids)->update([
            'onay_durumu' => $durum,
            'onaylayan_id' => auth()->id(),
            'onay_tarihi' => Carbon::now(),
        ]);
    }

    private function validateReddetForm(Request $request): void
    {
        $request->validate([
            'admin_notu' => 'required|string|max:500',
        ]);
    }
}
