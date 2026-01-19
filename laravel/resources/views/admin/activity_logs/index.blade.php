@extends('layouts.app')

@section('title', 'Aktivite Logları')

@section('content')
<div class="page-header mb-3">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h1 class="h4"><i class="bi bi-clock-history me-2"></i>Aktivite Logları</h1>
        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#clearLogsModal">
            <i class="bi bi-trash me-1"></i><span class="d-none d-sm-inline">Temizle</span>
        </button>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="content-card mb-3">
    <div class="content-card-body">
        <form method="GET" action="{{ route('admin.activity-logs.index') }}">
            <div class="row g-2">
                <div class="col-md-3 col-6">
                    <label class="form-label small">Kullanıcı</label>
                    <select name="user_id" class="form-select form-select-sm">
                        <option value="">Tümü</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->ad }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 col-6">
                    <label class="form-label small">Aksiyon</label>
                    <select name="action" class="form-select form-select-sm">
                        <option value="">Tümü</option>
                        <option value="created" {{ request('action') == 'created' ? 'selected' : '' }}>Oluşturuldu</option>
                        <option value="updated" {{ request('action') == 'updated' ? 'selected' : '' }}>Güncellendi</option>
                        <option value="deleted" {{ request('action') == 'deleted' ? 'selected' : '' }}>Silindi</option>
                        <option value="approved" {{ request('action') == 'approved' ? 'selected' : '' }}>Onaylandı</option>
                        <option value="rejected" {{ request('action') == 'rejected' ? 'selected' : '' }}>Reddedildi</option>
                    </select>
                </div>
                <div class="col-md-2 col-6">
                    <label class="form-label small">Model</label>
                    <select name="model" class="form-select form-select-sm">
                        <option value="">Tümü</option>
                        @foreach($models as $model)
                            <option value="{{ $model }}" {{ request('model') == $model ? 'selected' : '' }}>
                                {{ class_basename($model) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 col-6">
                    <label class="form-label small">Başlangıç</label>
                    <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2 col-6">
                    <label class="form-label small">Bitiş</label>
                    <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-1 col-6">
                    <label class="form-label small d-none d-md-block">&nbsp;</label>
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Log Listesi -->
<div class="content-card">
    <div class="content-card-body p-0">
        <!-- Desktop Tablo Görünümü -->
        <div class="d-none d-lg-block">
            <div class="table-responsive">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th width="60">#</th>
                            <th width="140">Tarih/Saat</th>
                            <th width="150">Kullanıcı</th>
                            <th width="120">Aksiyon</th>
                            <th width="120">Model</th>
                            <th>Açıklama</th>
                            <th width="100">IP</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr>
                                <td><span class="badge bg-light text-dark">#{{ $log->id }}</span></td>
                                <td><small>{{ $log->created_at->format('d.m.Y H:i') }}</small></td>
                                <td>
                                    @if($log->user)
                                        <small>{{ $log->user->ad }}</small>
                                    @else
                                        <small class="text-muted">Sistem</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $log->badge_class ?? 'secondary' }}">
                                        <i class="{{ $log->icon }} me-1"></i>{{ $log->action_name }}
                                    </span>
                                </td>
                                <td><small class="text-muted">{{ class_basename($log->model) }}</small></td>
                                <td><small>{{ $log->description }}</small></td>
                                <td><small class="text-muted">{{ $log->ip_address }}</small></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    Log kaydı bulunamadı
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Mobil Kart Görünümü -->
        <div class="d-lg-none p-3">
            @forelse($logs as $log)
                <div class="card mb-3 shadow-sm">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="badge bg-light text-dark">#{{ $log->id }}</span>
                            <span class="badge bg-{{ $log->badge_class ?? 'secondary' }}">
                                <i class="{{ $log->icon }} me-1"></i>{{ $log->action_name }}
                            </span>
                        </div>
                        
                        <h6 class="mb-2">{{ $log->description }}</h6>
                        
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <small class="text-muted d-block">Kullanıcı</small>
                                <small class="fw-semibold">
                                    @if($log->user)
                                        {{ $log->user->ad }}
                                    @else
                                        <span class="text-muted">Sistem</span>
                                    @endif
                                </small>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">Model</small>
                                <small class="fw-semibold">{{ class_basename($log->model) }}</small>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                            <small class="text-muted">
                                <i class="bi bi-clock me-1"></i>{{ $log->created_at->format('d.m.Y H:i') }}
                            </small>
                            <small class="text-muted">
                                <i class="bi bi-globe me-1"></i>{{ $log->ip_address }}
                            </small>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center text-muted py-5">
                    <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                    <p class="mb-0">Log kaydı bulunamadı</p>
                </div>
            @endforelse
        </div>
    </div>
    
    @if($logs->hasPages())
        <div class="content-card-body border-top">
            {{ $logs->links() }}
        </div>
    @endif
</div>

<!-- Temizle Modal -->
<div class="modal fade" id="clearLogsModal" tabindex="-1" aria-labelledby="clearLogsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.activity-logs.clear') }}" id="clearLogsForm">
                @csrf
                @method('DELETE')
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="clearLogsModalLabel">
                        <i class="bi bi-trash3 me-2"></i>Logları Toplu Temizle
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Kapat"></button>
                </div>
                <div class="modal-body">
                    <!-- Temizleme Türü Seçimi -->
                    <div class="mb-4">
                        <label class="form-label fw-bold mb-3">Temizleme Yöntemi Seçin:</label>
                        
                        <!-- Seçenek 1: Belirli Gün Öncesi -->
                        <div class="form-check mb-3 p-3 border rounded clear-option">
                            <input class="form-check-input" type="radio" name="clear_type" id="clearOlderThan" 
                                   value="older_than" checked onchange="updateClearForm()">
                            <label class="form-check-label w-100 cursor-pointer" for="clearOlderThan">
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-calendar-x text-warning me-2 fs-5"></i>
                                    <div class="flex-grow-1">
                                        <strong class="d-block mb-1">Belirli Gün Öncesini Sil</strong>
                                        <p class="text-muted small mb-3">Girdiğiniz gün sayısından eski logları siler</p>
                                        <div id="olderThanInput">
                                            <div class="input-group input-group-sm">
                                                <input type="number" name="days" class="form-control" value="90" min="1" max="365" id="daysInput">
                                                <span class="input-group-text">gün öncesi</span>
                                            </div>
                                            <small class="text-muted d-block mt-1">
                                                <i class="bi bi-info-circle me-1"></i>Örnek: 90 yazarsanız → <strong>90 günden eski</strong> tüm loglar silinir
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>

                        <!-- Seçenek 2: Tarih Aralığı -->
                        <div class="form-check mb-3 p-3 border rounded clear-option">
                            <input class="form-check-input" type="radio" name="clear_type" id="clearDateRange" 
                                   value="date_range" onchange="updateClearForm()">
                            <label class="form-check-label w-100 cursor-pointer" for="clearDateRange">
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-calendar-range text-info me-2 fs-5"></i>
                                    <div class="flex-grow-1">
                                        <strong class="d-block mb-1">Belirli Tarih Aralığını Sil</strong>
                                        <p class="text-muted small mb-3">Başlangıç ve bitiş tarihleri arasındaki logları siler</p>
                                        <div id="dateRangeInput" style="display: none;">
                                            <div class="row g-2">
                                                <div class="col-sm-6">
                                                    <label class="form-label small mb-1">Başlangıç Tarihi</label>
                                                    <input type="date" name="date_from" class="form-control form-control-sm" id="dateFromInput">
                                                </div>
                                                <div class="col-sm-6">
                                                    <label class="form-label small mb-1">Bitiş Tarihi</label>
                                                    <input type="date" name="date_to" class="form-control form-control-sm" id="dateToInput">
                                                </div>
                                            </div>
                                            <small class="text-muted d-block mt-1">
                                                <i class="bi bi-info-circle me-1"></i>Örnek: 01.01.2025 - 31.03.2025 arasındaki tüm loglar silinir
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>

                        <!-- Seçenek 3: Tümünü Sil -->
                        <div class="form-check mb-2 p-3 border border-danger rounded clear-option">
                            <input class="form-check-input" type="radio" name="clear_type" id="clearAll" 
                                   value="all" onchange="updateClearForm()">
                            <label class="form-check-label w-100 cursor-pointer" for="clearAll">
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-trash3-fill text-danger me-2 fs-5"></i>
                                    <div class="flex-grow-1">
                                        <strong class="d-block mb-1">Tüm Logları Sil</strong>
                                        <p class="text-muted small mb-0">Veritabanındaki tüm log kayıtlarını siler</p>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Uyarı -->
                    <div class="alert alert-danger d-flex align-items-center mb-0">
                        <i class="bi bi-exclamation-triangle-fill me-2 fs-4"></i>
                        <div>
                            <strong class="d-block">DİKKAT!</strong>
                            <small>Bu işlem geri alınamaz. Silinen loglar kurtarılamaz.</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer flex-column flex-sm-row">
                    <button type="button" class="btn btn-secondary w-100 w-sm-auto order-2 order-sm-1" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>İptal
                    </button>
                    <button type="submit" class="btn btn-danger w-100 w-sm-auto order-1 order-sm-2 mb-2 mb-sm-0" id="submitBtn">
                        <i class="bi bi-trash3 me-1"></i>Logları Temizle
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Modal Z-Index Fix - Navbar'ın üstünde görünsün */
.modal {
    z-index: 1060 !important;
}

.modal-backdrop {
    z-index: 1055 !important;
}

/* Modal scroll düzeltmesi */
.modal-dialog-scrollable .modal-body {
    max-height: calc(100vh - 250px);
    overflow-y: auto;
}

/* Radio buton seçim alanları */
.clear-option {
    transition: all 0.2s ease;
    cursor: pointer;
}

.clear-option:hover {
    background-color: #f8f9fa;
    border-color: #dee2e6 !important;
}

.clear-option input[type="radio"]:checked ~ label {
    font-weight: 500;
}

.cursor-pointer {
    cursor: pointer;
}

/* Mobile responsive ayarlar */
@media (max-width: 576px) {
    .modal-dialog {
        margin: 0.5rem;
    }
    
    .modal-dialog-scrollable .modal-body {
        max-height: calc(100vh - 200px);
    }
    
    .modal-body {
        padding: 1rem;
    }
    
    .clear-option {
        padding: 0.75rem !important;
    }
    
    .clear-option .fs-5 {
        font-size: 1rem !important;
    }
    
    .clear-option strong {
        font-size: 0.9rem;
    }
    
    .clear-option .small {
        font-size: 0.75rem !important;
    }
    
    .modal-footer {
        gap: 0.5rem;
    }
}

/* Input grupları için mobile düzeltme */
@media (max-width: 576px) {
    .input-group-text {
        font-size: 0.875rem;
        padding: 0.375rem 0.5rem;
    }
}

/* Scrollbar styling */
.modal-body::-webkit-scrollbar {
    width: 8px;
}

.modal-body::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.modal-body::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 10px;
}

.modal-body::-webkit-scrollbar-thumb:hover {
    background: #555;
}
</style>

<script>
function updateClearForm() {
    const olderThan = document.getElementById('clearOlderThan').checked;
    const dateRange = document.getElementById('clearDateRange').checked;
    const all = document.getElementById('clearAll').checked;
    
    // Input alanlarını göster/gizle
    document.getElementById('olderThanInput').style.display = olderThan ? 'block' : 'none';
    document.getElementById('dateRangeInput').style.display = dateRange ? 'block' : 'none';
    
    // Required özelliklerini ayarla
    document.getElementById('daysInput').required = olderThan;
    document.getElementById('dateFromInput').required = dateRange;
    document.getElementById('dateToInput').required = dateRange;
    
    // Buton metnini güncelle
    const submitBtn = document.getElementById('submitBtn');
    if (all) {
        submitBtn.innerHTML = '<i class="bi bi-trash3-fill me-1"></i><span class="d-none d-sm-inline">TÜM LOGLARI </span>SİL';
    } else {
        submitBtn.innerHTML = '<i class="bi bi-trash3 me-1"></i><span class="d-none d-sm-inline">Logları </span>Temizle';
    }
}

// Onay mesajı ekle
document.getElementById('clearLogsForm').addEventListener('submit', function(e) {
    const clearType = document.querySelector('input[name="clear_type"]:checked').value;
    let message = '';
    
    if (clearType === 'older_than') {
        const days = document.getElementById('daysInput').value;
        message = days + ' günden eski tüm loglar silinecek. Emin misiniz?';
    } else if (clearType === 'date_range') {
        const dateFrom = document.getElementById('dateFromInput').value;
        const dateTo = document.getElementById('dateToInput').value;
        
        if (!dateFrom || !dateTo) {
            alert('Lütfen başlangıç ve bitiş tarihlerini seçin!');
            e.preventDefault();
            return;
        }
        
        message = dateFrom + ' ile ' + dateTo + ' arasındaki tüm loglar silinecek. Emin misiniz?';
    } else if (clearType === 'all') {
        message = '⚠️ TÜM LOGLAR SİLİNECEK!\n\nBu işlem geri alınamaz ve tüm log geçmişiniz kaybolacak.\n\nDevam etmek istediğinizden emin misiniz?';
    }
    
    if (!confirm(message)) {
        e.preventDefault();
    }
});

// Modal açıldığında ilk seçeneği güncelle
document.getElementById('clearLogsModal').addEventListener('shown.bs.modal', function () {
    updateClearForm();
});
</script>
@endsection
