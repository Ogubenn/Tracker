@extends('layouts.app')

@section('title', 'Kontrol Maddeleri')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <h1>Kontrol Maddeleri</h1>
    <div class="d-flex gap-2">
        <button type="button" id="deleteSelectedBtn" class="btn btn-danger" style="display:none;">
            <i class="bi bi-trash"></i> Seçilenleri Sil
        </button>
        <a href="{{ route('admin.kontrol-maddeleri.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Yeni Kontrol Maddesi Ekle
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="content-card">
    <div class="content-card-body">
        <form id="bulkDeleteForm" action="{{ route('admin.kontrol-maddeleri.bulk-delete') }}" method="POST">
            @csrf
            @method('DELETE')
            <div class="table-wrapper">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th width="40">
                                <input type="checkbox" id="selectAll" class="form-check-input">
                            </th>
                            <th>Sıra</th>
                            <th>Kontrol Adı</th>
                            <th>Bina</th>
                            <th>Tip</th>
                            <th>Periyot</th>
                            <th>Durum</th>
                            <th width="120">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kontrolMaddeleri as $madde)
                            <tr>
                                <td>
                                    <input type="checkbox" name="ids[]" value="{{ $madde->id }}" class="form-check-input row-checkbox">
                                </td>
                                <td>{{ $madde->sira }}</td>
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
                                            <br><small class="text-muted">Birim: <strong>{{ $madde->birim }}</strong></small>
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
                                        <span class="badge bg-warning">Haftalık ({{ ucfirst($madde->haftalik_gun) }})</span>
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
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('admin.kontrol-maddeleri.edit', $madde) }}" class="btn btn-sm btn-warning" title="Düzenle">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger" onclick="deleteSingle({{ $madde->id }}, '{{ $madde->kontrol_adi }}')" title="Sil">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="bi bi-check2-square"></i>
                                        <h3>Henüz kontrol maddesi eklenmemiş</h3>
                                        <p>Yeni kontrol maddesi eklemek için yukarıdaki butonu kullanın</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>
    </div>
</div>

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

