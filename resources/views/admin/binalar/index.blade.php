@extends('layouts.app')

@section('title', 'Binalar')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <h1>Binalar</h1>
    <div class="d-flex gap-2">
        <button type="button" id="deleteSelectedBtn" class="btn btn-danger" style="display:none;">
            <i class="bi bi-trash"></i> Seçilenleri Sil
        </button>
        <a href="{{ route('admin.binalar.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Yeni Bina Ekle
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
        <form id="bulkDeleteForm" action="{{ route('admin.binalar.bulk-delete') }}" method="POST">
            @csrf
            @method('DELETE')
            <div class="table-wrapper">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th width="40">
                                <input type="checkbox" id="selectAll" class="form-check-input">
                            </th>
                            <th>Bina Adı</th>
                            <th>Kontrol Maddeleri</th>
                            <th>Durum</th>                            <th>QR Kod</th>                            <th>Oluşturulma</th>
                            <th width="120">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($binalar as $bina)
                            <tr>
                                <td>
                                    <input type="checkbox" name="ids[]" value="{{ $bina->id }}" class="form-check-input row-checkbox">
                                </td>
                                <td><strong>{{ $bina->bina_adi }}</strong></td>
                                <td>
                                    <span class="badge bg-info">{{ $bina->kontrolMaddeleri->count() }} Kontrol</span>
                                </td>
                                <td>
                                    @if($bina->aktif_mi)
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary">Pasif</span>
                                    @endif
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-info" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#qrModal{{ $bina->id }}"
                                            title="QR Kodu Görüntüle">
                                        <i class="bi bi-qr-code"></i>
                                    </button>
                                </td>
                                <td>{{ $bina->created_at->format('d.m.Y') }}</td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('admin.binalar.edit', $bina) }}" class="btn btn-sm btn-warning" title="Düzenle">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger" onclick="deleteSingle({{ $bina->id }}, '{{ $bina->bina_adi }}')" title="Sil">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            
                            <!-- QR Kod Modal -->
                            <div class="modal fade" id="qrModal{{ $bina->id }}" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">{{ $bina->bina_adi }} - QR Kod</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body text-center">
                                            <div class="mb-3" id="qrcode{{ $bina->id }}">
                                                {!! QrCode::size(300)->generate(route('public.kontrol.index', $bina->uuid)) !!}
                                            </div>
                                            <p class="text-muted">Bu QR kodu okutarak kontrol kaydı girebilirsiniz</p>
                                            <div class="alert alert-info">
                                                <strong>Link:</strong><br>
                                                <small>{{ route('public.kontrol.index', $bina->uuid) }}</small>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                                            <button type="button" class="btn btn-info" onclick="printQR({{ $bina->id }})">
                                                <i class="bi bi-printer"></i> Yazdır
                                            </button>
                                            @if(auth()->user()->rol === 'admin')
                                                <button type="button" class="btn btn-warning" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#confirmRegenerateModal{{ $bina->id }}"
                                                        data-bs-dismiss="modal">
                                                    <i class="bi bi-arrow-clockwise"></i> QR Değiştir
                                                </button>
                                            @endif
                                            <a href="{{ route('public.kontrol.index', $bina->uuid) }}" 
                                               class="btn btn-primary" target="_blank">
                                                <i class="bi bi-box-arrow-up-right"></i> Yeni Sekmede Aç
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- QR Değiştir Onay Modal -->
                            @if(auth()->user()->rol === 'admin')
                                <div class="modal fade" id="confirmRegenerateModal{{ $bina->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header bg-warning text-dark">
                                                <h5 class="modal-title">
                                                    <i class="bi bi-exclamation-triangle me-2"></i>QR Kod Değiştir
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p class="mb-0">
                                                    <strong>{{ $bina->bina_adi }}</strong> için QR kodu değiştirmek istediğinizden emin misiniz?
                                                </p>
                                                <div class="alert alert-warning mt-3 mb-0">
                                                    <i class="bi bi-info-circle me-2"></i>
                                                    <small>Eski QR kod çalışmayacak ve yeni QR kod oluşturulacaktır.</small>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                                                <form action="{{ route('admin.binalar.regenerate-qr', $bina) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-warning">
                                                        <i class="bi bi-arrow-clockwise me-2"></i>Evet, Değiştir
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="bi bi-building"></i>
                                        <h3>Henüz bina eklenmemiş</h3>
                                        <p>Yeni bina eklemek için yukarıdaki butonu kullanın</p>
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
    btn.textContent = `Seçilenleri Sil (${checked})`;
    btn.innerHTML = `<i class="bi bi-trash"></i> Seçilenleri Sil (${checked})`;
}

// Bulk delete
document.getElementById('deleteSelectedBtn').addEventListener('click', function() {
    const checked = document.querySelectorAll('.row-checkbox:checked').length;
    if(confirm(`Seçili ${checked} binayı silmek istediğinize emin misiniz?`)) {
        document.getElementById('bulkDeleteForm').submit();
    }
});

// Single delete
function deleteSingle(id, name) {
    if(confirm(`"${name}" isimli binayı silmek istediğinize emin misiniz?`)) {
        const form = document.getElementById('deleteSingleForm');
        form.action = `/admin/binalar/${id}`;
        form.submit();
    }
}

// QR Yazdırma
function printQR(binaId) {
    const qrElement = document.getElementById('qrcode' + binaId);
    const printWindow = window.open('', '', 'width=800,height=600');
    printWindow.document.write('<html><head><title>QR Kod</title>');
    printWindow.document.write('<style>body{display:flex;align-items:center;justify-content:center;height:100vh;margin:0;}</style>');
    printWindow.document.write('</head><body>');
    printWindow.document.write(qrElement.innerHTML);
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.focus();
    setTimeout(() => {
        printWindow.print();
        printWindow.close();
    }, 250);
}
</script>
@endpush
@endsection
