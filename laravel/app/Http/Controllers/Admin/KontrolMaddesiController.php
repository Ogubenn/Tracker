<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreKontrolMaddesiRequest;
use App\Http\Requests\UpdateKontrolMaddesiRequest;
use App\Models\KontrolMaddesi;
use App\Models\Bina;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KontrolMaddesiController extends Controller
{
    public function index(): View
    {
        $kontrolMaddeleri = KontrolMaddesi::with('bina')
            ->orderBy('sira')
            ->paginate(15);
            
        return view('admin.kontrol_maddeleri.index', compact('kontrolMaddeleri'));
    }

    public function create(): View
    {
        $binalar = $this->getAktifBinalar();
        return view('admin.kontrol_maddeleri.create', compact('binalar'));
    }

    public function store(StoreKontrolMaddesiRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $this->normalizeHaftalikGun($validated);
        
        KontrolMaddesi::create($validated);

        return redirect()->route('admin.kontrol-maddeleri.index')
            ->with('success', 'Kontrol maddesi başarıyla oluşturuldu.');
    }

    public function edit(KontrolMaddesi $kontrolMaddesi): View
    {
        $binalar = $this->getAktifBinalar();
        return view('admin.kontrol_maddeleri.edit', compact('kontrolMaddesi', 'binalar'));
    }

    public function update(UpdateKontrolMaddesiRequest $request, KontrolMaddesi $kontrolMaddesi): RedirectResponse
    {
        $validated = $request->validated();
        $this->normalizeHaftalikGun($validated);
        
        $kontrolMaddesi->update($validated);

        return redirect()->route('admin.kontrol-maddeleri.index')
            ->with('success', 'Kontrol maddesi başarıyla güncellendi.');
    }

    public function destroy(KontrolMaddesi $kontrolMaddesi): RedirectResponse
    {
        $kontrolMaddesi->delete();

        return redirect()->route('admin.kontrol-maddeleri.index')
            ->with('success', 'Kontrol maddesi başarıyla silindi.');
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $ids = $request->input('ids', []);
        
        if (empty($ids)) {
            return redirect()->route('admin.kontrol-maddeleri.index')
                ->with('error', 'Silmek için en az bir kontrol maddesi seçmelisiniz.');
        }

        $count = KontrolMaddesi::whereIn('id', $ids)->delete();

        return redirect()->route('admin.kontrol-maddeleri.index')
            ->with('success', "{$count} kontrol maddesi başarıyla silindi.");
    }

    private function getAktifBinalar()
    {
        return Bina::where('aktif_mi', true)->get();
    }

    private function normalizeHaftalikGun(array &$validated): void
    {
        if ($validated['periyot'] !== 'haftalik') {
            $validated['haftalik_gun'] = null;
        }
    }
}
