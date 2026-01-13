<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bina;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BinaController extends Controller
{
    public function index()
    {
        $binalar = Bina::with('kontrolMaddeleri')
            ->latest()
            ->get();

        return view('admin.binalar.index', compact('binalar'));
    }

    public function create()
    {
        return view('admin.binalar.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateBina($request);
        Bina::create($validated);

        return $this->redirectWithSuccess('admin.binalar.index', 'Bina başarıyla oluşturuldu.');
    }

    public function edit(Bina $bina)
    {
        return view('admin.binalar.edit', compact('bina'));
    }

    public function update(Request $request, Bina $bina)
    {
        $validated = $this->validateBina($request);
        $bina->update($validated);

        return $this->redirectWithSuccess('admin.binalar.index', 'Bina başarıyla güncellendi.');
    }

    public function destroy(Bina $bina)
    {
        $bina->delete();

        return $this->redirectWithSuccess('admin.binalar.index', 'Bina başarıyla silindi.');
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);
        
        if (empty($ids)) {
            return redirect()
                ->route('admin.binalar.index')
                ->with('error', 'Silmek için en az bir bina seçmelisiniz.');
        }

        $deletedCount = Bina::whereIn('id', $ids)->delete();

        return $this->redirectWithSuccess('admin.binalar.index', "{$deletedCount} bina başarıyla silindi.");
    }
    
    public function regenerateQr(Bina $bina)
    {
        $bina->update(['uuid' => (string) Str::uuid()]);
        
        return redirect()
            ->back()
            ->with('success', 'QR kod başarıyla yenilendi.');
    }

    private function validateBina(Request $request): array
    {
        return $request->validate([
            'bina_adi' => 'required|string|max:255',
            'aktif_mi' => 'boolean',
        ]);
    }

    private function redirectWithSuccess(string $route, string $message)
    {
        return redirect()
            ->route($route)
            ->with('success', $message);
    }
}
