@extends('layouts.app')

@section('title', 'Aktivite Logları')

@section('content')
<div class="page-header mb-3">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="h4"><i class="bi bi-clock-history me-2"></i>Aktivite Logları</h1>
        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#clearLogsModal">
            <i class="bi bi-trash me-1"></i>Eski Logları Temizle
        </button>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Filtreler -->
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.activity-logs.index') }}" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Kullanıcı</label>
                <select name="user_id" class="form-select form-select-sm">
                    <option value="">Tümü</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->ad }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label">Aksiyon</label>
                <select name="action" class="form-select form-select-sm">
                    <option value="">Tümü</option>
                    <option value="created" {{ request('action') == 'created' ? 'selected' : '' }}>Oluşturuldu</option>
                    <option value="updated" {{ request('action') == 'updated' ? 'selected' : '' }}>Güncellendi</option>
                    <option value="deleted" {{ request('action') == 'deleted' ? 'selected' : '' }}>Silindi</option>
                    <option value="approved" {{ request('action') == 'approved' ? 'selected' : '' }}>Onaylandı</option>
                    <option value="rejected" {{ request('action') == 'rejected' ? 'selected' : '' }}>Reddedildi</option>
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label">Model</label>
                <select name="model" class="form-select form-select-sm">
                    <option value="">Tümü</option>
                    @foreach($models as $model)
                        <option value="{{ $model }}" {{ request('model') == $model ? 'selected' : '' }}>
                            {{ class_basename($model) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label">Başlangıç</label>
                <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
            </div>

            <div class="col-md-2">
                <label class="form-label">Bitiş</label>
                <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
            </div>

            <div class="col-md-1 d-flex align-items-end">
                <button type="submit" class="btn btn-primary btn-sm w-100">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Log Listesi -->
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="50">#</th>
                        <th width="150">Tarih/Saat</th>
                        <th width="150">Kullanıcı</th>
                        <th width="100">Aksiyon</th>
                        <th width="120">Model</th>
                        <th>Açıklama</th>
                        <th width="100">IP</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td>{{ $log->id }}</td>
                            <td>
                                <small>{{ $log->created_at->format('d.m.Y H:i') }}</small>
                            </td>
                            <td>
                                @if($log->user)
                                    <small>{{ $log->user->ad }}</small>
                                @else
                                    <small class="text-muted">Sistem</small>
                                @endif
                            </td>
                            <td>
                                <i class="{{ $log->icon }} me-1"></i>
                                <small>{{ $log->action_name }}</small>
                            </td>
                            <td>
                                <small>{{ class_basename($log->model) }}</small>
                            </td>
                            <td>
                                <small>{{ $log->description }}</small>
                            </td>
                            <td>
                                <small class="text-muted">{{ $log->ip_address }}</small>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                <div class="mt-2">Log kaydı bulunamadı</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    @if($logs->hasPages())
        <div class="card-footer">
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
