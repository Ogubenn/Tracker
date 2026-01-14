<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBinaRequest;
use App\Http\Requests\UpdateBinaRequest;
use App\Models\Bina;
use App\Models\ActivityLog;
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

    public function store(StoreBinaRequest $request)
    {
        $validated = $request->validated();
        $bina = Bina::create($validated);
        
        ActivityLog::log('created', 'App\Models\Bina', $bina->id, null, $bina->toArray(), 'Yeni bina oluşturuldu: ' . $bina->bina_adi);

        return $this->redirectWithSuccess('admin.binalar.index', 'Bina başarıyla oluşturuldu.');
    }

    public function edit(Bina $bina)
    {
        return view('admin.binalar.edit', compact('bina'));
    }

    public function update(UpdateBinaRequest $request, Bina $bina)
    {
        $oldData = $bina->toArray();
        $validated = $request->validated();
        $bina->update($validated);
        
        ActivityLog::log('updated', 'App\Models\Bina', $bina->id, $oldData, $bina->toArray(), 'Bina güncellendi: ' . $bina->bina_adi);

        return $this->redirectWithSuccess('admin.binalar.index', 'Bina başarıyla güncellendi.');
    }

    public function destroy(Bina $bina)
    {
        $binaAdi = $bina->bina_adi;
        $oldData = $bina->toArray();
        $bina->delete();
        
        ActivityLog::log('deleted', 'App\Models\Bina', $bina->id, $oldData, null, 'Bina silindi: ' . $binaAdi);

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

    private function redirectWithSuccess(string $route, string $message)
    {
        return redirect()
            ->route($route)
            ->with('success', $message);
    }
}
