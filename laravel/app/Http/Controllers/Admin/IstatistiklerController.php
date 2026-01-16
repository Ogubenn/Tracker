<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alan;
use App\Models\Bina;
use App\Models\KontrolKaydi;
use App\Models\KontrolMaddesi;
use Illuminate\Http\Request;
use Carbon\Carbon;

class IstatistiklerController extends Controller
{
    // Sayısal Veri Analizi
    public function sayisalAnaliz(Request $request)
    {
        try {
            $kontrolMaddesiId = $request->get('kontrol_maddesi_id');
            $startDate = $request->get('start_date', Carbon::now()->subMonths(3)->format('Y-m-d'));
            $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));
            $binaId = $request->get('bina_id');

            // Sayısal kontrol maddeleri (alan üzerinden bina filtreleme)
            $sayisalMaddeler = KontrolMaddesi::where('kontrol_tipi', 'sayisal')
                ->when($binaId, function($q) use ($binaId) {
                    $q->whereHas('alan', function($q2) use ($binaId) {
                        $q2->where('bina_id', $binaId);
                    });
                })
                ->with(['alan.bina'])
                ->orderBy('kontrol_adi')
                ->get();

            $binalar = Bina::orderBy('bina_adi')->get();

            if (!$kontrolMaddesiId && $sayisalMaddeler->isNotEmpty()) {
                $kontrolMaddesiId = $sayisalMaddeler->first()->id;
            }

            $analizData = null;
            $secilenMadde = null;

            if ($kontrolMaddesiId) {
                $secilenMadde = KontrolMaddesi::with(['alan.bina'])->find($kontrolMaddesiId);
                if ($secilenMadde) {
                    $analizData = $this->getSayisalAnalizData($kontrolMaddesiId, $startDate, $endDate);
                }
            }

            return view('admin.istatistikler.sayisal-analiz', compact(
                'sayisalMaddeler',
                'binalar',
                'kontrolMaddesiId',
                'startDate',
                'endDate',
                'binaId',
                'analizData',
                'secilenMadde'
            ));
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    private function getSayisalAnalizData($kontrolMaddesiId, $startDate, $endDate)
    {
        // Veriler
        $kayitlar = KontrolKaydi::where('kontrol_maddesi_id', $kontrolMaddesiId)
            ->whereBetween('tarih', [$startDate, $endDate])
            ->whereNotNull('girilen_deger')
            ->orderBy('tarih')
            ->get();

        if ($kayitlar->isEmpty()) {
            return null;
        }

        // Sayısal değerlere dönüştür
        $degerler = $kayitlar->map(fn($k) => floatval($k->girilen_deger))->toArray();

        // Zaman serisi (grafik için)
        $zamanSerisi = [
            'labels' => $kayitlar->map(fn($k) => Carbon::parse($k->tarih)->format('d.m.Y'))->toArray(),
            'values' => $degerler
        ];

        // İstatistiksel metrikler
        $toplam = count($degerler);
        $ortalama = $toplam > 0 ? array_sum($degerler) / $toplam : 0;
        $min = $toplam > 0 ? min($degerler) : 0;
        $max = $toplam > 0 ? max($degerler) : 0;

        // Medyan
        $sortedValues = $degerler;
        sort($sortedValues);
        $mid = floor($toplam / 2);
        $medyan = $toplam > 0 ? ($toplam % 2 == 0 ? ($sortedValues[$mid-1] + $sortedValues[$mid]) / 2 : $sortedValues[$mid]) : 0;

        // Standart sapma
        $varyans = 0;
        if ($toplam > 1) {
            foreach ($degerler as $deger) {
                $varyans += pow($deger - $ortalama, 2);
            }
            $varyans = $varyans / ($toplam - 1);
        }
        $standartSapma = sqrt($varyans);

        // Aylık ortalamalar
        $aylikOrtalamalar = $kayitlar
            ->groupBy(fn($k) => Carbon::parse($k->tarih)->format('Y-m'))
            ->map(function($grup) {
                $degerler = $grup->map(fn($k) => floatval($k->girilen_deger))->toArray();
                return count($degerler) > 0 ? array_sum($degerler) / count($degerler) : 0;
            });

        $aylikGrafik = [
            'labels' => $aylikOrtalamalar->keys()->map(fn($k) => Carbon::createFromFormat('Y-m', $k)->locale('tr')->translatedFormat('F Y'))->toArray(),
            'values' => $aylikOrtalamalar->values()->toArray()
        ];

        // Son 7 gün trendi
        $son7Gun = $kayitlar->filter(function($k) {
            return Carbon::parse($k->tarih)->isAfter(Carbon::now()->subDays(7));
        });

        $trendLabels = $son7Gun->map(fn($k) => Carbon::parse($k->tarih)->format('d.m'))->toArray();
        $trendValues = $son7Gun->map(fn($k) => floatval($k->girilen_deger))->toArray();

        return [
            'metrikler' => [
                'toplam_olcum' => $toplam,
                'ortalama' => round($ortalama, 2),
                'medyan' => round($medyan, 2),
                'min' => round($min, 2),
                'max' => round($max, 2),
                'standart_sapma' => round($standartSapma, 2),
                'son_deger' => $toplam > 0 ? round(end($degerler), 2) : 0,
            ],
            'zaman_serisi' => $zamanSerisi,
            'aylik_grafik' => $aylikGrafik,
            'son7gun_trend' => [
                'labels' => $trendLabels,
                'values' => $trendValues
            ]
        ];
    }
}
