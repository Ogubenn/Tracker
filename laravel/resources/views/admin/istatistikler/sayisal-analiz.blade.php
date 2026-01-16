@extends('layouts.app')

@section('title', 'Sayısal Veri Analizi')

@push('styles')
<style>
.metric-card { background: linear-gradient(135deg, var(--white) 0%, var(--gray-50) 100%); border-radius: var(--border-radius); padding: 1.5rem; box-shadow: var(--shadow); border-left: 4px solid var(--primary); transition: var(--transition); }
.metric-card:hover { transform: translateY(-2px); box-shadow: var(--shadow-md); }
.metric-card .metric-label { font-size: 0.8125rem; font-weight: 600; color: var(--gray-600); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem; }
.metric-card .metric-value { font-size: 2.25rem; font-weight: 700; color: var(--gray-900); line-height: 1; }
.metric-card .metric-unit { font-size: 1rem; font-weight: 500; color: var(--gray-600); margin-left: 0.5rem; }
.metric-card.primary { border-left-color: var(--primary); }
.metric-card.success { border-left-color: var(--success); }
.metric-card.warning { border-left-color: var(--warning); }
.metric-card.danger { border-left-color: var(--danger); }
.chart-container { position: relative; height: 400px; }
.empty-state { text-align: center; padding: 4rem 2rem; }
.empty-state i { font-size: 5rem; color: var(--gray-300); margin-bottom: 1.5rem; }
@media (max-width: 991px) {
    .chart-container { height: 300px; }
    .metric-card .metric-value { font-size: 1.75rem; }
}
@media (max-width: 576px) {
    .chart-container { height: 250px; }
    .metric-card { padding: 1rem; }
}
</style>
@endpush

@section('content')
<div class="page-header">
    <h1><i class="bi bi-graph-up-arrow"></i> Sayısal Veri Analizi</h1>
</div>

<!-- Filtreleme ve Seçim -->
<div class="content-card mb-4">
    <div class="content-card-body">
        <form method="GET" action="{{ route('admin.sayisal-analiz') }}" class="row g-3">
            <div class="col-lg-3 col-md-6">
                <label class="form-label">Bina Filtresi</label>
                <select name="bina_id" class="form-select" onchange="this.form.submit()">
                    <option value="">Tüm Binalar</option>
                    @foreach($binalar as $bina)
                        <option value="{{ $bina->id }}" {{ $binaId == $bina->id ? 'selected' : '' }}>{{ $bina->bina_adi }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-lg-3 col-md-6">
                <label class="form-label">Kontrol Maddesi</label>
                <select name="kontrol_maddesi_id" class="form-select" required>
                    <option value="">Seçiniz...</option>
                    @foreach($sayisalMaddeler as $madde)
                        <option value="{{ $madde->id }}" {{ $kontrolMaddesiId == $madde->id ? 'selected' : '' }}>
                            {{ $madde->kontrol_adi }} @if($madde->alan && $madde->alan->bina) ({{ $madde->alan->bina->bina_adi }}) @endif
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-lg-2 col-md-6">
                <label class="form-label">Başlangıç</label>
                <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
            </div>

            <div class="col-lg-2 col-md-6">
                <label class="form-label">Bitiş</label>
                <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
            </div>

            <div class="col-lg-2 col-md-12 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-funnel"></i> Filtrele
                </button>
            </div>
        </form>
    </div>
</div>

@if($analizData)
    <!-- Seçilen Madde Bilgisi -->
    <div class="alert alert-info mb-4">
        <div class="d-flex align-items-center">
            <i class="bi bi-info-circle fs-4 me-3"></i>
            <div>
                <strong>{{ $secilenMadde->kontrol_adi }}</strong>
                @if($secilenMadde->birim)
                    <span class="badge bg-primary ms-2">{{ $secilenMadde->birim }}</span>
                @endif
                @if($secilenMadde->alan && $secilenMadde->alan->bina)
                    <span class="text-muted ms-2">• {{ $secilenMadde->alan->bina->bina_adi }}</span>
                @endif
            </div>
        </div>
    </div>

    <!-- İstatistiksel Metrikler -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
            <div class="metric-card primary">
                <div class="metric-label"><i class="bi bi-calculator"></i> Ortalama</div>
                <div class="metric-value">
                    {{ $analizData['metrikler']['ortalama'] }}
                    @if($secilenMadde->birim)<span class="metric-unit">{{ $secilenMadde->birim }}</span>@endif
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
            <div class="metric-card success">
                <div class="metric-label"><i class="bi bi-arrow-up-circle"></i> Maximum</div>
                <div class="metric-value">
                    {{ $analizData['metrikler']['max'] }}
                    @if($secilenMadde->birim)<span class="metric-unit">{{ $secilenMadde->birim }}</span>@endif
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
            <div class="metric-card warning">
                <div class="metric-label"><i class="bi bi-arrow-down-circle"></i> Minimum</div>
                <div class="metric-value">
                    {{ $analizData['metrikler']['min'] }}
                    @if($secilenMadde->birim)<span class="metric-unit">{{ $secilenMadde->birim }}</span>@endif
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
            <div class="metric-card danger">
                <div class="metric-label"><i class="bi bi-activity"></i> Son Değer</div>
                <div class="metric-value">
                    {{ $analizData['metrikler']['son_deger'] }}
                    @if($secilenMadde->birim)<span class="metric-unit">{{ $secilenMadde->birim }}</span>@endif
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
            <div class="metric-card">
                <div class="metric-label"><i class="bi bi-sort-numeric-down"></i> Medyan</div>
                <div class="metric-value">{{ $analizData['metrikler']['medyan'] }}</div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
            <div class="metric-card">
                <div class="metric-label"><i class="bi bi-bar-chart"></i> Standart Sapma</div>
                <div class="metric-value">{{ $analizData['metrikler']['standart_sapma'] }}</div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
            <div class="metric-card">
                <div class="metric-label"><i class="bi bi-hash"></i> Toplam Ölçüm</div>
                <div class="metric-value">{{ $analizData['metrikler']['toplam_olcum'] }}</div>
            </div>
        </div>
    </div>

    <!-- Grafikler -->
    <div class="row">
        <!-- Zaman Serisi - Tüm Veriler -->
        <div class="col-lg-12 mb-4">
            <div class="content-card">
                <div class="content-card-header">
                    <h5><i class="bi bi-graph-up"></i> Zaman Serisi Analizi</h5>
                </div>
                <div class="content-card-body">
                    <div class="chart-container">
                        <canvas id="zamanSerisiChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Aylık Ortalamalar -->
        <div class="col-lg-6 mb-4">
            <div class="content-card">
                <div class="content-card-header">
                    <h5><i class="bi bi-calendar3"></i> Aylık Ortalamalar</h5>
                </div>
                <div class="content-card-body">
                    <div class="chart-container">
                        <canvas id="aylikOrtalamaChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Son 7 Gün Trend -->
        <div class="col-lg-6 mb-4">
            <div class="content-card">
                <div class="content-card-header">
                    <h5><i class="bi bi-bar-chart-line"></i> Son 7 Gün Trendi</h5>
                </div>
                <div class="content-card-body">
                    <div class="chart-container">
                        <canvas id="son7GunChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
@elseif($sayisalMaddeler->isEmpty())
    <div class="content-card">
        <div class="content-card-body">
            <div class="empty-state">
                <i class="bi bi-inbox"></i>
                <h4>Sayısal Kontrol Maddesi Bulunamadı</h4>
                <p class="text-muted">Henüz sayısal tip kontrol maddesi tanımlanmamış.</p>
            </div>
        </div>
    </div>
@else
    <div class="content-card">
        <div class="content-card-body">
            <div class="empty-state">
                <i class="bi bi-box-seam"></i>
                <h4>Kontrol Maddesi Seçiniz</h4>
                <p class="text-muted">Analiz görmek için yukarıdan bir kontrol maddesi seçin.</p>
            </div>
        </div>
    </div>
@endif
@endsection

@if($analizData)
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
Chart.defaults.font.family = "'Inter', sans-serif";
Chart.defaults.font.size = 13;
Chart.defaults.color = '#4B5563';

// 1. Zaman Serisi (Line Chart with Area)
const zamanSerisiCtx = document.getElementById('zamanSerisiChart').getContext('2d');
new Chart(zamanSerisiCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($analizData['zaman_serisi']['labels']) !!},
        datasets: [{
            label: '{{ $secilenMadde->kontrol_adi }}',
            data: {!! json_encode($analizData['zaman_serisi']['values']) !!},
            borderColor: 'rgba(59, 130, 246, 1)',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointRadius: 5,
            pointHoverRadius: 7,
            pointBackgroundColor: 'rgba(59, 130, 246, 1)',
            pointBorderColor: '#ffffff',
            pointBorderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: true, position: 'top' },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12,
                borderRadius: 8,
                callbacks: {
                    label: function(context) {
                        let label = context.dataset.label || '';
                        let value = context.parsed.y;
                        return label + ': ' + value + ' {{ $secilenMadde->birim ?? "" }}';
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: false,
                grid: { color: 'rgba(0, 0, 0, 0.05)' },
                ticks: {
                    callback: function(value) {
                        return value + ' {{ $secilenMadde->birim ?? "" }}';
                    }
                }
            },
            x: {
                grid: { display: false }
            }
        }
    }
});

// 2. Aylık Ortalamalar (Bar Chart)
const aylikOrtalamaCtx = document.getElementById('aylikOrtalamaChart').getContext('2d');
new Chart(aylikOrtalamaCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($analizData['aylik_grafik']['labels']) !!},
        datasets: [{
            label: 'Aylık Ortalama',
            data: {!! json_encode($analizData['aylik_grafik']['values']) !!},
            backgroundColor: 'rgba(16, 185, 129, 0.8)',
            borderColor: 'rgba(16, 185, 129, 1)',
            borderWidth: 1,
            borderRadius: 6
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12,
                borderRadius: 8
            }
        },
        scales: {
            y: {
                beginAtZero: false,
                grid: { color: 'rgba(0, 0, 0, 0.05)' },
                ticks: {
                    callback: function(value) {
                        return value + ' {{ $secilenMadde->birim ?? "" }}';
                    }
                }
            },
            x: {
                grid: { display: false }
            }
        }
    }
});

// 3. Son 7 Gün Trend (Line Chart)
const son7GunCtx = document.getElementById('son7GunChart').getContext('2d');
new Chart(son7GunCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($analizData['son7gun_trend']['labels']) !!},
        datasets: [{
            label: 'Son 7 Gün',
            data: {!! json_encode($analizData['son7gun_trend']['values']) !!},
            borderColor: 'rgba(245, 158, 11, 1)',
            backgroundColor: 'rgba(245, 158, 11, 0.2)',
            borderWidth: 3,
            fill: true,
            tension: 0.3,
            pointRadius: 6,
            pointHoverRadius: 8,
            pointBackgroundColor: 'rgba(245, 158, 11, 1)',
            pointBorderColor: '#ffffff',
            pointBorderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12,
                borderRadius: 8
            }
        },
        scales: {
            y: {
                beginAtZero: false,
                grid: { color: 'rgba(0, 0, 0, 0.05)' },
                ticks: {
                    callback: function(value) {
                        return value + ' {{ $secilenMadde->birim ?? "" }}';
                    }
                }
            },
            x: {
                grid: { display: false }
            }
        }
    }
});
</script>
@endpush
@endif
