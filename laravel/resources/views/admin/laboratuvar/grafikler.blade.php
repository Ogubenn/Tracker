@extends('layouts.app')

@section('title', 'Laboratuvar Grafikleri')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="bi bi-graph-up"></i> Laboratuvar Parametre Grafikleri
        </h1>
        <div class="d-flex gap-2">
            @if($seciliParametre && count($veriler) > 0)
            <button class="btn btn-success" onclick="exportToCSV()">
                <i class="bi bi-file-earmark-spreadsheet"></i> CSV İndir
            </button>
            @endif
            <a href="{{ route('admin.laboratuvar.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Geri
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Kompakt Filtre Paneli -->
        <div class="col-12 mb-3">
            <div class="card">
                <div class="card-body p-3">
                    <form method="GET" action="{{ route('admin.laboratuvar.grafikler') }}" id="filterForm">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label mb-1 small fw-bold">Parametre</label>
                                <select name="parametre" class="form-select form-select-sm">
                                    <option value="">Seçin...</option>
                                    @foreach($parametreListesi as $param)
                                        <option value="{{ $param }}" {{ $param == $seciliParametre ? 'selected' : '' }}>
                                            {{ Str::limit($param, 40) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label mb-1 small fw-bold">Başlangıç</label>
                                <input type="date" name="baslangic_tarihi" class="form-control form-control-sm" 
                                       value="{{ request('baslangic_tarihi') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label mb-1 small fw-bold">Bitiş</label>
                                <input type="date" name="bitis_tarihi" class="form-control form-control-sm" 
                                       value="{{ request('bitis_tarihi') }}">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary btn-sm w-100">
                                    <i class="bi bi-search"></i> Filtrele
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @if($seciliParametre && count($veriler) > 0)
        <!-- Grafikler Grid -->
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-graph-up-arrow"></i> {{ $seciliParametre }} - Zaman Serisi
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="lineChart" style="height: 350px;"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-pie-chart"></i> Uygunluk Dağılımı
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="pieChart" style="height: 350px;"></canvas>
                </div>
            </div>
        </div>

        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="bi bi-bar-chart-fill"></i> Analiz Sonuçları - Bar Grafik
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="barChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>

        <!-- Gelişmiş İstatistikler -->
        <div class="col-md-2 mb-4">
            <div class="card text-center bg-primary text-white">
                <div class="card-body">
                    <i class="bi bi-bar-chart-line fs-1"></i>
                    <h3 class="mt-2">{{ count($veriler) }}</h3>
                    <p class="mb-0 small">Toplam Ölçüm</p>
                </div>
            </div>
        </div>
        <div class="col-md-2 mb-4">
            <div class="card text-center bg-success text-white">
                <div class="card-body">
                    <i class="bi bi-arrow-up-circle fs-1"></i>
                    <h3 class="mt-2">
                        {{ number_format(collect($veriler)->avg('deger'), 2) }}
                    </h3>
                    <p class="mb-0 small">Ortalama</p>
                </div>
            </div>
        </div>
        <div class="col-md-2 mb-4">
            <div class="card text-center bg-warning text-dark">
                <div class="card-body">
                    <i class="bi bi-arrow-up fs-1"></i>
                    <h3 class="mt-2">
                        {{ number_format(collect($veriler)->max('deger'), 2) }}
                    </h3>
                    <p class="mb-0 small">Maksimum</p>
                </div>
            </div>
        </div>
        <div class="col-md-2 mb-4">
            <div class="card text-center bg-info text-white">
                <div class="card-body">
                    <i class="bi bi-arrow-down fs-1"></i>
                    <h3 class="mt-2">
                        {{ number_format(collect($veriler)->min('deger'), 2) }}
                    </h3>
                    <p class="mb-0 small">Minimum</p>
                </div>
            </div>
        </div>
        <div class="col-md-2 mb-4">
            <div class="card text-center" style="background: #28a745; color: white;">
                <div class="card-body">
                    <i class="bi bi-check-circle fs-1"></i>
                    <h3 class="mt-2">
                        {{ collect($veriler)->where('uygunluk', 'uygun')->count() }}
                    </h3>
                    <p class="mb-0 small">Uygun</p>
                </div>
            </div>
        </div>
        <div class="col-md-2 mb-4">
            <div class="card text-center bg-danger text-white">
                <div class="card-body">
                    <i class="bi bi-x-circle fs-1"></i>
                    <h3 class="mt-2">
                        {{ collect($veriler)->where('uygunluk', 'uygun_degil')->count() }}
                    </h3>
                    <p class="mb-0 small">Uygun Değil</p>
                </div>
            </div>
        </div>

        <!-- Veri Tablosu -->
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="bi bi-table"></i> Detaylı Veriler</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Tarih</th>
                                    <th>Analiz Sonucu</th>
                                    <th>Limit Değeri</th>
                                    <th>Uygunluk</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($veriler as $veri)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($veri['tarih'])->format('d.m.Y') }}</td>
                                    <td><strong>{{ number_format($veri['deger'], 4) }}</strong></td>
                                    <td>{{ $veri['limit'] ?? '-' }}</td>
                                    <td>
                                        @if($veri['uygunluk'] == 'uygun')
                                            <span class="badge bg-success">Uygun</span>
                                        @elseif($veri['uygunluk'] == 'uygun_degil')
                                            <span class="badge bg-danger">Uygun Değil</span>
                                        @else
                                            <span class="badge bg-secondary">Limit Yok</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @elseif($seciliParametre)
        <div class="col-12">
            <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle"></i> <strong>{{ $seciliParametre }}</strong> için henüz veri bulunmuyor.
            </div>
        </div>
        @else
        <div class="col-12">
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> Grafik görüntülemek için yukarıdan bir parametre seçin.
            </div>
        </div>
        @endif
    </div>
</div>

@if($seciliParametre && count($veriler) > 0)
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const veriler = @json($veriler);
    
    const tarihler = veriler.map(v => {
        const d = new Date(v.tarih);
        return d.toLocaleDateString('tr-TR', { day: '2-digit', month: '2-digit', year: 'numeric' });
    });
    
    const degerler = veriler.map(v => v.deger);
    
    // Limit değerlerini parse et
    const limitler = veriler.map(v => {
        if (!v.limit || v.limit === '-') return null;
        const sayi = parseFloat(v.limit);
        return isNaN(sayi) ? null : sayi;
    });
    
    // Renk kodlaması
    const renkler = veriler.map(v => {
        if (v.uygunluk === 'uygun') return 'rgba(40, 167, 69, 0.8)';
        if (v.uygunluk === 'uygun_degil') return 'rgba(220, 53, 69, 0.8)';
        return 'rgba(13, 110, 253, 0.8)';
    });
    
    // 1. LINE CHART - Zaman Serisi
    const ctxLine = document.getElementById('lineChart').getContext('2d');
    new Chart(ctxLine, {
        type: 'line',
        data: {
            labels: tarihler,
            datasets: [
                {
                    label: 'Analiz Sonucu',
                    data: degerler,
                    backgroundColor: 'rgba(13, 110, 253, 0.2)',
                    borderColor: 'rgba(13, 110, 253, 1)',
                    borderWidth: 3,
                    pointBackgroundColor: renkler,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8,
                    tension: 0.3,
                    fill: true
                },
                {
                    label: 'Limit Değeri',
                    data: limitler,
                    borderColor: 'rgba(255, 99, 71, 0.8)',
                    borderWidth: 2,
                    borderDash: [10, 5],
                    pointRadius: 0,
                    fill: false
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: { font: { size: 13, weight: 'bold' } }
                },
                title: {
                    display: true,
                    text: '{{ $seciliParametre }} - Trend Analizi',
                    font: { size: 16, weight: 'bold' }
                },
                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    titleFont: { size: 14 },
                    bodyFont: { size: 13 },
                    padding: 12,
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) label += ': ';
                            if (context.parsed.y !== null) {
                                label += context.parsed.y.toFixed(4);
                            }
                            return label;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: false,
                    title: {
                        display: true,
                        text: 'Değer',
                        font: { size: 14, weight: 'bold' }
                    },
                    ticks: {
                        callback: function(value) {
                            return value.toFixed(2);
                        }
                    },
                    grid: { color: 'rgba(0,0,0,0.05)' }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Tarih',
                        font: { size: 14, weight: 'bold' }
                    },
                    grid: { display: false }
                }
            }
        }
    });
    
    // 2. PIE CHART - Uygunluk Dağılımı
    const uygunSayisi = veriler.filter(v => v.uygunluk === 'uygun').length;
    const uygunDegilSayisi = veriler.filter(v => v.uygunluk === 'uygun_degil').length;
    const limitYokSayisi = veriler.filter(v => !v.uygunluk || v.uygunluk === 'limit_yok').length;
    
    const ctxPie = document.getElementById('pieChart').getContext('2d');
    new Chart(ctxPie, {
        type: 'doughnut',
        data: {
            labels: ['Uygun', 'Uygun Değil', 'Limit Yok'],
            datasets: [{
                data: [uygunSayisi, uygunDegilSayisi, limitYokSayisi],
                backgroundColor: [
                    'rgba(40, 167, 69, 0.8)',
                    'rgba(220, 53, 69, 0.8)',
                    'rgba(108, 117, 125, 0.8)'
                ],
                borderColor: '#fff',
                borderWidth: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { 
                        font: { size: 12 },
                        padding: 15
                    }
                },
                title: {
                    display: true,
                    text: 'Uygunluk Oranları',
                    font: { size: 15, weight: 'bold' }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return `${label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
    
    // 3. BAR CHART - Son Ölçümler
    const sonOlcumler = veriler.slice(-10); // Son 10 ölçüm
    const barTarihler = sonOlcumler.map(v => {
        const d = new Date(v.tarih);
        return d.toLocaleDateString('tr-TR', { day: '2-digit', month: 'short' });
    });
    const barDegerler = sonOlcumler.map(v => v.deger);
    const barRenkler = sonOlcumler.map(v => {
        if (v.uygunluk === 'uygun') return 'rgba(40, 167, 69, 0.8)';
        if (v.uygunluk === 'uygun_degil') return 'rgba(220, 53, 69, 0.8)';
        return 'rgba(13, 110, 253, 0.8)';
    });
    
    const ctxBar = document.getElementById('barChart').getContext('2d');
    new Chart(ctxBar, {
        type: 'bar',
        data: {
            labels: barTarihler,
            datasets: [{
                label: 'Analiz Sonucu',
                data: barDegerler,
                backgroundColor: barRenkler,
                borderColor: barRenkler.map(c => c.replace('0.8', '1')),
                borderWidth: 2,
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                title: {
                    display: true,
                    text: 'Son 10 Ölçüm Karşılaştırması',
                    font: { size: 16, weight: 'bold' }
                },
                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    padding: 12,
                    callbacks: {
                        label: function(context) {
                            return 'Değer: ' + context.parsed.y.toFixed(4);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Analiz Sonucu',
                        font: { size: 14, weight: 'bold' }
                    },
                    grid: { color: 'rgba(0,0,0,0.05)' }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });
});

// CSV Export Fonksiyonu
function exportToCSV() {
    const veriler = @json($veriler);
    let csv = 'Tarih,Analiz Sonucu,Limit Değeri,Uygunluk\n';
    
    veriler.forEach(v => {
        const tarih = new Date(v.tarih).toLocaleDateString('tr-TR');
        const deger = v.deger;
        const limit = v.limit || '-';
        const uygunluk = v.uygunluk === 'uygun' ? 'Uygun' : 
                         v.uygunluk === 'uygun_degil' ? 'Uygun Değil' : 'Limit Yok';
        csv += `${tarih},${deger},${limit},${uygunluk}\n`;
    });
    
    const blob = new Blob(['\ufeff' + csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    
    link.setAttribute('href', url);
    link.setAttribute('download', '{{ $seciliParametre }}_' + new Date().toISOString().split('T')[0] + '.csv');
    link.style.visibility = 'hidden';
    
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
</script>
@endif
@endsection
