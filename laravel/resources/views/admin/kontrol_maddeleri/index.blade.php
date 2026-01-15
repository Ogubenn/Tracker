@extends('layouts.app')

@section('title', 'Kontrol Maddeleri')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center flex-wrap">
    <h1>Kontrol Maddeleri</h1>
    <div class="d-flex gap-2">
        <button type="button" id="deleteSelectedBtn" class="btn btn-danger" style="display:none;">
            <i class="bi bi-trash"></i> <span class="d-none d-md-inline">Seç</span><span class="d-md-none">Sil</span>
        </button>
        <a href="{{ route('admin.kontrol-maddeleri.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> <span class="d-none d-sm-inline">Yeni Madde</span>
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<form id="bulkDeleteForm" action="{{ route('admin.kontrol-maddeleri.bulk-delete') }}" method="POST">
    @csrf
    @method('DELETE')

    <div class="d-none d-lg-block">
        <div class="content-card">
            <div class="content-card-body">
                <div class="table-wrapper">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th width="40">
                                    <input type="checkbox" id="selectAll" class="form-check-input">
                                </th>
                                <th width="60">Sıra</th>
                                <th>Kontrol Adı</th>
                                <th width="120">Bina</th>
                                <th width="150">Tip</th>
                                <th width="150">Periyot</th>
                                <th width="80">Durum</th>
                                <th width="120">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($kontrolMaddeleri as $madde)
                                <tr>
                                    <td>
                                        <input type="checkbox" name="ids[]" value="{{ $madde->id }}" class="form-check-input row-checkbox">
                                    </td>
                                    <td><span class="badge bg-light text-dark">#{{ $madde->sira }}</span></td>
                                    <td><strong>{{ $madde->kontrol_adi }}</strong></td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $madde->bina ? $madde->bina->bina_adi : 'Bina Yok' }}
                                        </small>
                                    </td>
                                    <td>
                                        @if($madde->kontrol_tipi == 'checkbox')
                                            <span class="badge bg-primary">Checkbox</span>
                                        @elseif($madde->kontrol_tipi == 'sayisal')
                                            <span class="badge bg-info">Sayısal</span>
                                            @if($madde->birim)
                                                <br><small class="text-muted">{{ $madde->birim }}</small>
                                            @endif
                                        @else
                                            <span class="badge bg-secondary">Metin</span>
                                        @endif
                                        @if($madde->zaman_secimi)
                                            <br><small class="text-success"><i class="bi bi-clock"></i> Zaman</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($madde->periyot == 'gunluk')
                                            <span class="badge bg-success">Günlük</span>
                                        @elseif($madde->periyot == 'haftalik')
                                            <span class="badge bg-warning">Haftalık<br>({{ ucfirst($madde->haftalik_gun) }})</span>
                                        @elseif($madde->periyot == '15_gun')
                                            <span class="badge bg-info">15 Günlük</span>
                                        @else
                                            <span class="badge bg-primary">Aylık</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($madde->aktif_mi)
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-secondary">Pasif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.kontrol-maddeleri.edit', $madde) }}" class="btn btn-warning" title="Düzenle">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-danger" onclick="deleteSingle({{ $madde->id }}, '{{ $madde->kontrol_adi }}')" title="Sil">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        <i class="bi bi-check2-square fs-1 d-block mb-2"></i>
                                        Henüz kontrol maddesi eklenmemiş.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="d-lg-none">
        @forelse($kontrolMaddeleri as $madde)
            <div class="content-card mb-3 shadow-sm">
                <div class="content-card-body">
                    <div class="d-flex align-items-start gap-2 mb-3">
                        <input type="checkbox" name="ids[]" value="{{ $madde->id }}" class="form-check-input mt-1 row-checkbox">
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <h6 class="mb-0">{{ $madde->kontrol_adi }}</h6>
                                <span class="badge bg-light text-dark ms-2">#{{ $madde->sira }}</span>
                            </div>
                            <small class="text-muted d-block mb-2">
                                <i class="bi bi-building"></i> {{ $madde->bina ? $madde->bina->bina_adi : 'Bina Yok' }}
                            </small>
                            <div class="d-flex flex-wrap gap-1 mb-2">
                                @if($madde->kontrol_tipi == 'checkbox')
                                    <span class="badge bg-primary">Checkbox</span>
                                @elseif($madde->kontrol_tipi == 'sayisal')
                                    <span class="badge bg-info">Sayısal @if($madde->birim)({{ $madde->birim }})@endif</span>
                                @else
                                    <span class="badge bg-secondary">Metin</span>
                                @endif
                                
                                @if($madde->periyot == 'gunluk')
                                    <span class="badge bg-success">Günlük</span>
                                @elseif($madde->periyot == 'haftalik')
                                    <span class="badge bg-warning">Haftalık ({{ ucfirst($madde->haftalik_gun) }})</span>
                                @elseif($madde->periyot == '15_gun')
                                    <span class="badge bg-info">15 Günlük</span>
                                @else
                                    <span class="badge bg-primary">Aylık</span>
                                @endif
                                
                                @if($madde->aktif_mi)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Pasif</span>
                                @endif
                                
                                @if($madde->zaman_secimi)
                                    <span class="badge bg-success"><i class="bi bi-clock"></i> Zaman</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex gap-1">
                        <a href="{{ route('admin.kontrol-maddeleri.edit', $madde) }}" class="btn btn-warning btn-sm flex-fill">
                            <i class="bi bi-pencil"></i> Düzenle
                        </a>
                        <button type="button" class="btn btn-danger btn-sm flex-fill" onclick="deleteSingle({{ $madde->id }}, '{{ $madde->kontrol_adi }}')">
                            <i class="bi bi-trash"></i> Sil
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="content-card">
                <div class="content-card-body text-center text-muted py-5">
                    <i class="bi bi-check2-square fs-1 d-block mb-3"></i>
                    <p class="mb-0">Henüz kontrol maddesi eklenmemiş.</p>
                </div>
            </div>
        @endforelse
    </div>
</form>

<form id="deleteSingleForm" action="" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
// Select All
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.row-checkbox');
    checkboxes.forEach(cb => cb.checked = this.checked);
    updateDeleteButton();
});

// Row checkboxes
document.querySelectorAll('.row-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', updateDeleteButton);
});

function updateDeleteButton() {
    const checked = document.querySelectorAll('.row-checkbox:checked').length;
    const btn = document.getElementById('deleteSelectedBtn');
    btn.style.display = checked > 0 ? 'block' : 'none';
    btn.innerHTML = `<i class="bi bi-trash"></i> Seçilenleri Sil (${checked})`;
}

// Bulk delete
document.getElementById('deleteSelectedBtn').addEventListener('click', function() {
    const checked = document.querySelectorAll('.row-checkbox:checked').length;
    if(confirm(`Seçili ${checked} kontrol maddesini silmek istediğinize emin misiniz?`)) {
        document.getElementById('bulkDeleteForm').submit();
    }
});

// Single delete
function deleteSingle(id, name) {
    if(confirm(`"${name}" isimli kontrol maddesini silmek istediğinize emin misiniz?`)) {
        const form = document.getElementById('deleteSingleForm');
        form.action = `/admin/kontrol-maddeleri/${id}`;
        form.submit();
    }
}
</script>
@endpush
@endsection

