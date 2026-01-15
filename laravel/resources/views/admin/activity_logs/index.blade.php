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
<div class="modal fade" id="clearLogsModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.activity-logs.clear') }}">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title">Eski Logları Temizle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Kaç gün önceki logları silelim?</label>
                        <input type="number" name="days" class="form-control" value="90" min="1" max="365" required>
                        <div class="form-text">Örneğin: 90 gün önceki tüm loglar silinir.</div>
                    </div>
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Bu işlem geri alınamaz!
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-danger">Temizle</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
