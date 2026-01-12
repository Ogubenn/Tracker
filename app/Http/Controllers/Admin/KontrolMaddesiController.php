<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KontrolMaddesi;
use App\Models\Bina;
use Illuminate\Http\Request;

class KontrolMaddesiController extends Controller
{
    public function index()
    {
        $kontrolMaddeleri = KontrolMaddesi::with('bina')
            ->orderBy('sira')
            ->get();
        return view('admin.kontrol_maddeleri.index', compact('kontrolMaddeleri'));
    }

    public function create()
    {
        $binalar = Bina::where('aktif_mi', true)->get();
        return view('admin.kontrol_maddeleri.create', compact('binalar'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'bina_id' => 'required|exists:binalar,id',
            'kontrol_adi' => 'required|string|max:255',
            'kontrol_tipi' => 'required|in:checkbox,sayisal,metin',
            'periyot' => 'required|in:gunluk,haftalik,15_gun,aylik',
            'haftalik_gun' => 'nullable|in:pazartesi,sali,carsamba,persembe,cuma,cumartesi,pazar',
            'aktif_mi' => 'boolean',
            'sira' => 'integer|min:0',
        ]);

        // Haftalık değilse, haftalık_gun'u null yap
        if ($validated['periyot'] !== 'haftalik') {
            $validated['haftalik_gun'] = null;
        }

        KontrolMaddesi::create($validated);

        return redirect()->route('admin.kontrol-maddeleri.index')
            ->with('success', 'Kontrol maddesi başarıyla oluşturuldu.');
    }

    public function edit(KontrolMaddesi $kontrolMaddesi)
    {
        $binalar = Bina::where('aktif_mi', true)->get();
        return view('admin.kontrol_maddeleri.edit', compact('kontrolMaddesi', 'binalar'));
    }

    public function update(Request $request, KontrolMaddesi $kontrolMaddesi)
    {
        $validated = $request->validate([
            'bina_id' => 'required|exists:binalar,id',
            'kontrol_adi' => 'required|string|max:255',
            'kontrol_tipi' => 'required|in:checkbox,sayisal,metin',
            'periyot' => 'required|in:gunluk,haftalik,15_gun,aylik',
            'haftalik_gun' => 'nullable|in:pazartesi,sali,carsamba,persembe,cuma,cumartesi,pazar',
            'aktif_mi' => 'boolean',
            'sira' => 'integer|min:0',
        ]);

        // Haftalık değilse, haftalık_gun'u null yap
        if ($validated['periyot'] !== 'haftalik') {
            $validated['haftalik_gun'] = null;
        }

        $kontrolMaddesi->update($validated);

        return redirect()->route('admin.kontrol-maddeleri.index')
            ->with('success', 'Kontrol maddesi başarıyla güncellendi.');
    }

    public function destroy(KontrolMaddesi $kontrolMaddesi)
    {
        $kontrolMaddesi->delete();

        return redirect()->route('admin.kontrol-maddeleri.index')
            ->with('success', 'Kontrol maddesi başarıyla silindi.');
    }

    public function bulkDestroy(Request $request)
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
}
