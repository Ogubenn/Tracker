<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArsivlenmisIs;
use App\Models\Bina;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ArsivlenmisIsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ArsivlenmisIs::with(['bina', 'olusturan'])
            ->orderBy('is_tarihi', 'desc');

        // Bina filtresi
        if ($request->filled('bina_id')) {
            $query->where('bina_id', $request->bina_id);
        }

        // Tarih filtresi
        if ($request->filled('baslangic_tarihi')) {
            $query->whereDate('is_tarihi', '>=', $request->baslangic_tarihi);
        }
        if ($request->filled('bitis_tarihi')) {
            $query->whereDate('is_tarihi', '<=', $request->bitis_tarihi);
        }

        // Arama
        if ($request->filled('arama')) {
            $arama = $request->arama;
            $query->where(function ($q) use ($arama) {
                $q->where('is_aciklamasi', 'like', "%{$arama}%")
                  ->orWhere('detayli_aciklama', 'like', "%{$arama}%");
            });
        }

        $isler = $query->paginate(20);
        $binalar = Bina::orderBy('bina_adi')->get();

        return view('admin.arsivlenmis-isler.index', compact('isler', 'binalar'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $binalar = Bina::orderBy('bina_adi')->get();
        return view('admin.arsivlenmis-isler.create', compact('binalar'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'bina_id' => 'nullable|exists:binalar,id',
            'is_tarihi' => 'required|date',
            'is_aciklamasi' => 'required|string|max:255',
            'detayli_aciklama' => 'nullable|string',
            'fotograflar.*' => 'nullable|image|mimes:jpeg,jpg,png|max:5120', // 5MB max
        ], [
            'bina_id.exists' => 'Seçilen bina bulunamadı',
            'is_tarihi.required' => 'İş tarihi zorunludur',
            'is_tarihi.date' => 'Geçerli bir tarih girin',
            'is_aciklamasi.required' => 'İş açıklaması zorunludur',
            'is_aciklamasi.max' => 'İş açıklaması en fazla 255 karakter olabilir',
            'fotograflar.*.image' => 'Sadece resim dosyaları yüklenebilir',
            'fotograflar.*.mimes' => 'Sadece JPEG, JPG ve PNG formatları desteklenmektedir',
            'fotograflar.*.max' => 'Her fotoğraf en fazla 5MB olabilir',
        ]);

        // Fotoğrafları yükle
        $fotografYollari = [];
        if ($request->hasFile('fotograflar')) {
            foreach ($request->file('fotograflar') as $fotograf) {
                $path = $fotograf->store('arsivlenmis-isler', 'public');
                $fotografYollari[] = $path;
            }
        }

        $validated['fotograflar'] = $fotografYollari;
        $validated['olusturan_kullanici_id'] = Auth::id();

        try {
            $arsivlenmisIs = ArsivlenmisIs::create($validated);
            
            // Aktivite log kaydı
            ActivityLog::log(
                'created',
                'ArsivlenmisIs',
                $arsivlenmisIs->id,
                null,
                $arsivlenmisIs->toArray(),
                'İş arşive eklendi: ' . $arsivlenmisIs->is_aciklamasi
            );
        } catch (\Exception $e) {
            \Log::error('Arşivlenmiş iş oluşturma hatası: ' . $e->getMessage());
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'İş eklenirken bir hata oluştu: ' . $e->getMessage());
        }

        return redirect()
            ->route('admin.arsivlenmis-isler.index')
            ->with('success', 'Arşivlenmiş iş başarıyla eklendi');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $arsivlenmisIs = ArsivlenmisIs::with(['bina', 'olusturan'])->findOrFail($id);
        return view('admin.arsivlenmis-isler.show', compact('arsivlenmisIs'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $arsivlenmisIs = ArsivlenmisIs::findOrFail($id);
        $binalar = Bina::orderBy('bina_adi')->get();
        return view('admin.arsivlenmis-isler.edit', compact('arsivlenmisIs', 'binalar'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $arsivlenmisIs = ArsivlenmisIs::findOrFail($id);
        $validated = $request->validate([
            'bina_id' => 'nullable|exists:binalar,id',
            'is_tarihi' => 'required|date',
            'is_aciklamasi' => 'required|string|max:255',
            'detayli_aciklama' => 'nullable|string',
            'fotograflar.*' => 'nullable|image|mimes:jpeg,jpg,png|max:5120',
            'eski_fotograflar' => 'nullable|array',
        ], [
            'is_tarihi.required' => 'İş tarihi zorunludur',
            'is_aciklamasi.required' => 'İş açıklaması zorunludur',
        ]);

        // Eski fotoğrafları koru
        $fotografYollari = $request->input('eski_fotograflar', []);

        // Yeni fotoğrafları yükle
        if ($request->hasFile('fotograflar')) {
            foreach ($request->file('fotograflar') as $fotograf) {
                $path = $fotograf->store('arsivlenmis-isler', 'public');
                $fotografYollari[] = $path;
            }
        }

        $validated['fotograflar'] = $fotografYollari;
        
        try {
            $oldValues = $arsivlenmisIs->getOriginal();
            $arsivlenmisIs->update($validated);
            
            // Aktivite log kaydı
            ActivityLog::log(
                'updated',
                'ArsivlenmisIs',
                $arsivlenmisIs->id,
                $oldValues,
                $arsivlenmisIs->fresh()->toArray(),
                'İş güncellendi: ' . $arsivlenmisIs->is_aciklamasi
            );
        } catch (\Exception $e) {
            \Log::error('Arşivlenmiş iş güncelleme hatası: ' . $e->getMessage());
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'İş güncellenirken bir hata oluştu: ' . $e->getMessage());
        }

        return redirect()
            ->route('admin.arsivlenmis-isler.index')
            ->with('success', 'Arşivlenmiş iş başarıyla güncellendi');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $arsivlenmisIs = ArsivlenmisIs::findOrFail($id);
            
            // Bilgileri kaydet (log için)
            $isAciklamasi = $arsivlenmisIs->is_aciklamasi;
            $oldValues = $arsivlenmisIs->toArray();
            
            // Fotoğrafları sil
            if ($arsivlenmisIs->fotograflar && is_array($arsivlenmisIs->fotograflar)) {
                foreach ($arsivlenmisIs->fotograflar as $fotograf) {
                    if ($fotograf) {
                        Storage::disk('public')->delete($fotograf);
                    }
                }
            }

            $arsivlenmisIs->delete();
            
            // Aktivite log kaydı
            ActivityLog::log(
                'deleted',
                'ArsivlenmisIs',
                $id,
                $oldValues,
                null,
                'İş arşivden silindi: ' . $isAciklamasi
            );
        } catch (\Exception $e) {
            \Log::error('Arşivlenmiş iş silme hatası: ' . $e->getMessage());
            return redirect()
                ->back()
                ->with('error', 'İş silinirken bir hata oluştu: ' . $e->getMessage());
        }

        return redirect()
            ->route('admin.arsivlenmis-isler.index')
            ->with('success', 'Arşivlenmiş iş başarıyla silindi');
    }

    /**
     * Fotoğraf silme (AJAX)
     */
    public function deleteFotograf(Request $request, $id)
    {
        $arsivlenmisIs = ArsivlenmisIs::findOrFail($id);
        $fotografPath = $request->input('fotograf_path');
        
        if (!$fotografPath) {
            return response()->json(['success' => false, 'message' => 'Fotoğraf bulunamadı'], 400);
        }

        $fotograflar = $arsivlenmisIs->fotograflar ?? [];
        $index = array_search($fotografPath, $fotograflar);

        if ($index !== false) {
            // Fotoğrafı storage'dan sil
            Storage::disk('public')->delete($fotografPath);
            
            // Array'den kaldır
            unset($fotograflar[$index]);
            $arsivlenmisIs->fotograflar = array_values($fotograflar);
            $arsivlenmisIs->save();

            return response()->json(['success' => true, 'message' => 'Fotoğraf silindi']);
        }

        return response()->json(['success' => false, 'message' => 'Fotoğraf bulunamadı'], 404);
    }
}
