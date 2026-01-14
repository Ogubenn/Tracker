<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alan;
use App\Models\Bina;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AlanController extends Controller
{
    public function index(): View
    {
        $alanlar = Alan::with('bina')
            ->latest()
            ->get();

        return view('admin.alanlar.index', compact('alanlar'));
    }

    public function create(): View
    {
        $binalar = Bina::aktif()->get();
        return view('admin.alanlar.create', compact('binalar'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateAlan($request);
        Alan::create($validated);

        return redirect()
            ->route('admin.alanlar.index')
            ->with('success', 'Alan başarıyla oluşturuldu.');
    }

    public function edit(Alan $alan): View
    {
        $binalar = Bina::aktif()->get();
        return view('admin.alanlar.edit', compact('alan', 'binalar'));
    }

    public function update(Request $request, Alan $alan): RedirectResponse
    {
        $validated = $this->validateAlan($request);
        $alan->update($validated);

        return redirect()
            ->route('admin.alanlar.index')
            ->with('success', 'Alan başarıyla güncellendi.');
    }

    public function destroy(Alan $alan): RedirectResponse
    {
        if ($alan->kontrolMaddeleri()->exists()) {
            return back()->with('error', 'Bu alana ait kontrol maddeleri olduğu için silinemez.');
        }

        $alan->delete();

        return redirect()
            ->route('admin.alanlar.index')
            ->with('success', 'Alan başarıyla silindi.');
    }

    public function toggleAktif(Alan $alan): RedirectResponse
    {
        $alan->update(['aktif_mi' => !$alan->aktif_mi]);

        $durum = $alan->aktif_mi ? 'aktif' : 'pasif';
        return back()->with('success', "Alan {$durum} hale getirildi.");
    }

    private function validateAlan(Request $request): array
    {
        return $request->validate([
            'bina_id' => 'required|exists:binalar,id',
            'alan_adi' => 'required|string|max:255',
            'aktif_mi' => 'boolean',
        ]);
    }
}
