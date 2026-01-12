<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bina;
use Illuminate\Http\Request;

class BinaController extends Controller
{
    public function index()
    {
        $binalar = Bina::with('kontrolMaddeleri')->latest()->get();
        return view('admin.binalar.index', compact('binalar'));
    }

    public function create()
    {
        return view('admin.binalar.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'bina_adi' => 'required|string|max:255',
            'aktif_mi' => 'boolean',
        ]);

        Bina::create($validated);

        return redirect()->route('admin.binalar.index')
            ->with('success', 'Bina başarıyla oluşturuldu.');
    }

    public function edit(Bina $bina)
    {
        return view('admin.binalar.edit', compact('bina'));
    }

    public function update(Request $request, Bina $bina)
    {
        $validated = $request->validate([
            'bina_adi' => 'required|string|max:255',
            'aktif_mi' => 'boolean',
        ]);

        $bina->update($validated);

        return redirect()->route('admin.binalar.index')
            ->with('success', 'Bina başarıyla güncellendi.');
    }

    public function destroy(Bina $bina)
    {
        $bina->delete();

        return redirect()->route('admin.binalar.index')
            ->with('success', 'Bina başarıyla silindi.');
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);
        
        if (empty($ids)) {
            return redirect()->route('admin.binalar.index')
                ->with('error', 'Silmek için en az bir bina seçmelisiniz.');
        }

        $count = Bina::whereIn('id', $ids)->delete();

        return redirect()->route('admin.binalar.index')
            ->with('success', "{$count} bina başarıyla silindi.");
    }
    
    /**
     * QR kodu yenile (sadece admin)
     */
    public function regenerateQr(Bina $bina)
    {
        $bina->uuid = (string) \Illuminate\Support\Str::uuid();
        $bina->save();
        
        return redirect()->back()->with('success', 'QR kod başarıyla yenilendi.');
    }
}
