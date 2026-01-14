<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KontrolKaydi;
use App\Models\Bina;
use App\Services\PdfService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class RaporController extends Controller
{
    public function __construct(private PdfService $pdfService)
    {
    }
    public function index(Request $request): View
    {
        $binalar = $this->getAktifBinalar();
        $tarihBaslangic = $request->get('tarih_baslangic', Carbon::today()->format('Y-m-d'));
        $tarihBitis = $request->get('tarih_bitis', Carbon::today()->format('Y-m-d'));
        $binaId = $request->get('bina_id');

        $kayitlar = $binaId ? $this->getKayitlarByFilters($tarihBaslangic, $tarihBitis, $binaId) : null;

        return view('admin.raporlar.index', compact('binalar', 'tarihBaslangic', 'tarihBitis', 'binaId', 'kayitlar'));
    }

    public function exportPdf(Request $request): Response
    {
        $tarihBaslangic = $request->get('tarih_baslangic', Carbon::today()->format('Y-m-d'));
        $tarihBitis = $request->get('tarih_bitis', Carbon::today()->format('Y-m-d'));
        $binaId = $request->get('bina_id');

        if (!$binaId) {
            return back()->with('error', 'Lütfen bir bina seçin.');
        }

        $binalar = $this->getAktifBinalar();
        $kayitlar = $this->getKayitlarByFilters($tarihBaslangic, $tarihBitis, $binaId);
        $secilenBina = $binaId === 'all' ? 'Tüm Binalar' : $binalar->find($binaId)?->bina_adi;

        $tarihAralik = Carbon::parse($tarihBaslangic)->format('d.m.Y');
        if ($tarihBaslangic !== $tarihBitis) {
            $tarihAralik .= ' - ' . Carbon::parse($tarihBitis)->format('d.m.Y');
        }

        // PDF Service kullanarak oluştur
        $pdfContent = $this->pdfService->generateRaporPdf($kayitlar, $tarihAralik, $secilenBina);

        $filename = 'Kontrol_Raporu_' . Carbon::parse($tarihBaslangic)->format('d-m-Y');
        if ($tarihBaslangic !== $tarihBitis) {
            $filename .= '_' . Carbon::parse($tarihBitis)->format('d-m-Y');
        }
        $filename .= '.pdf';

        // PDF'i indir
        return response($pdfContent, 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    private function getAktifBinalar(): Collection
    {
        return Bina::aktif()->get();
    }


    private function getKayitlarByFilters(string $tarihBaslangic, string $tarihBitis, string $binaId): Collection
    {
        $query = $this->buildKayitlarQuery($tarihBaslangic, $tarihBitis);
        $this->applyBinaFilter($query, $binaId);
        
        return $query->get()->groupBy('bina.bina_adi');
    }

    private function buildKayitlarQuery(string $tarihBaslangic, string $tarihBitis): Builder
    {
        return KontrolKaydi::with(['kontrolMaddesi', 'bina', 'yapanKullanici'])
            ->whereDate('tarih', '>=', $tarihBaslangic)
            ->whereDate('tarih', '<=', $tarihBitis)
            ->orderBy('tarih', 'desc');
    }

    private function applyBinaFilter(Builder $query, string $binaId): void
    {
        if ($binaId !== 'all') {
            $query->where('bina_id', $binaId);
        }
    }
}
