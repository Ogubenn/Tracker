@extends('layouts.app')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h1><i class="bi bi-clipboard-check"></i> Kontrol Kayıtları</h1>
        <form action="{{ route('admin.kontrol-kayitlari.toplu-onayla') }}" method="POST" id="topluOnayForm">
            @csrf
            <button type="submit" class="btn btn-success" id="topluOnayBtn" style="display: none;">
                <i class="bi bi-check-all"></i> <span class="d-none d-sm-inline">Toplu </span>Onayla (<span id="seciliSayisi">0</span>)
            </button>
        </form>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="content-card mb-4">
    <div class="content-card-body">
        <form action="{{ route('admin.kontrol-kayitlari.index') }}" method="GET">
            <div class="row g-2">
                <div class="col-md-3 col-6">
                    <label class="form-label small">Başlangıç</label>
                    <input type="date" name="tarih_baslangic" class="form-control form-control-sm" value="{{ request('tarih_baslangic') }}">
                </div>
                <div class="col-md-3 col-6">
                    <label class="form-label small">Bitiş</label>
                    <input type="date" name="tarih_bitis" class="form-control form-control-sm" value="{{ request('tarih_bitis') }}">
                </div>
                <div class="col-md-3 col-6">
                    <label class="form-label small">Bina</label>
                    <select name="bina_id" class="form-select form-select-sm">
                        <option value="">Tümü</option>
                        @foreach($binalar as $bina)
                            <option value="{{ $bina->id }}" {{ request('bina_id') == $bina->id ? 'selected' : '' }}>
                                {{ $bina->bina_adi }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 col-6">
                    <label class="form-label small">Onay</label>
                    <select name="onay_durumu" class="form-select form-select-sm">
                        <option value="">Tümü</option>
                        <option value="bekliyor" {{ request('onay_durumu') == 'bekliyor' ? 'selected' : '' }}>Bekliyor</option>
                        <option value="onaylandi" {{ request('onay_durumu') == 'onaylandi' ? 'selected' : '' }}>Onaylandı</option>
                        <option value="reddedildi" {{ request('onay_durumu') == 'reddedildi' ? 'selected' : '' }}>Reddedildi</option>
                    </select>
                </div>
                <div class="col-md-3 col-6">
                    <label class="form-label small">Durum</label>
                    <select name="durum" class="form-select form-select-sm">
                        <option value="">Tümü</option>
                        <option value="uygun" {{ request('durum') == 'uygun' ? 'selected' : '' }}>Uygun</option>
                        <option value="uygun_degil" {{ request('durum') == 'uygun_degil' ? 'selected' : '' }}>Uygun Değil</option>
                        <option value="duzeltme_gerekli" {{ request('durum') == 'duzeltme_gerekli' ? 'selected' : '' }}>Düzeltme Gerekli</option>
                    </select>
                </div>
                <div class="col-md-3 col-6">
                    <label class="form-label small">&nbsp;</label>
                    <button type="submit" class="btn btn-primary btn-sm d-block w-100">
                        <i class="bi bi-search"></i> Filtrele
                    </button>
                </div>
                <div class="col-md-3 col-6">
                    <label class="form-label small d-none d-md-block">&nbsp;</label>
                    <a href="{{ route('admin.kontrol-kayitlari.index') }}" class="btn btn-secondary btn-sm d-block w-100">
                        <i class="bi bi-x-circle"></i> Temizle
                    </a>
                </div>
            </div>
        </form>
    </div>
<div class="content-card">
    <div class="content-card-body">
        @if($kayitlar->isEmpty())
            <div class="text-center text-muted py-5">
                <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                <p class="mb-0">Henüz kontrol kaydı yok</p>
            </div>
        @else
            <div class="d-none d-lg-block">
                <div class="table-responsive">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th width="40">
                                    <input type="checkbox" class="form-check-input" id="selectAll">
                                </th>
                                <th width="100">Tarih</th>
                                <th width="150">Bina</th>
                                <th>Kontrol Maddesi</th>
                                <th width="150">Personel</th>
                                <th width="120">Durum</th>
                                <th width="120">Onay</th>
                                <th width="120">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($kayitlar as $kayit)
                                <tr>
                                    <td>
                                        @if($kayit->onay_durumu === 'bekliyor')
                                            <input type="checkbox" class="form-check-input kayit-checkbox" value="{{ $kayit->id }}">
                                        @endif
                                    </td>
                                    <td>{{ $kayit->tarih->format('d.m.Y') }}</td>
                                    <td>{{ $kayit->bina->bina_adi }}</td>
                                    <td>{{ $kayit->kontrolMaddesi->kontrol_adi }}</td>
                                    <td>{{ $kayit->yapanKullanici->name }}</td>
                                    <td>
                                        @if($kayit->durum === 'uygun')
                                            <span class="badge bg-success">Uygun</span>
                                        @elseif($kayit->durum === 'uygun_degil')
                                            <span class="badge bg-danger">Uygun Değil</span>
                                        @else
                                            <span class="badge bg-warning">Düzeltme</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($kayit->onay_durumu === 'bekliyor')
                                            <span class="badge bg-warning">Bekliyor</span>
                                        @elseif($kayit->onay_durumu === 'onaylandi')
                                            <span class="badge bg-success">Onaylandı</span>
                                        @else
                                            <span class="badge bg-danger">Reddedildi</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1 justify-content-center">
                                            <a href="{{ route('admin.kontrol-kayitlari.show', $kayit->id) }}" 
                                               class="btn btn-info btn-sm">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            @if($kayit->onay_durumu === 'bekliyor')
                                                <form action="{{ route('admin.kontrol-kayitlari.onayla', $kayit->id) }}" 
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success btn-sm" title="Onayla">
                                                        <i class="bi bi-check-lg"></i>
                                                    </button>
                                                </form>
                                                <button type="button" class="btn btn-danger btn-sm" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#redModal{{ $kayit->id }}"
                                                        title="Reddet">
                                                    <i class="bi bi-x-lg"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="d-lg-none">
                @foreach($kayitlar as $kayit)
                    <div class="content-card mb-3 shadow-sm">
                        <div class="content-card-body">
                            <div class="d-flex align-items-start gap-2 mb-3">
                                @if($kayit->onay_durumu === 'bekliyor')
                                    <input type="checkbox" class="form-check-input mt-1 kayit-checkbox" value="{{ $kayit->id }}">
                                @endif
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ $kayit->kontrolMaddesi->kontrol_adi }}</h6>
                                    <small class="text-muted d-block">
                                        <i class="bi bi-building"></i> {{ $kayit->bina->bina_adi }}
                                    </small>
                                    <small class="text-muted d-block">
                                        <i class="bi bi-person"></i> {{ $kayit->yapanKullanici->name }}
                                    </small>
                                    <small class="text-muted d-block mb-2">
                                        <i class="bi bi-calendar"></i> {{ $kayit->tarih->format('d.m.Y') }}
                                    </small>
                                    <div class="d-flex flex-wrap gap-1">
                                        @if($kayit->durum === 'uygun')
                                            <span class="badge bg-success">Uygun</span>
                                        @elseif($kayit->durum === 'uygun_degil')
                                            <span class="badge bg-danger">Uygun Değil</span>
                                        @else
                                            <span class="badge bg-warning">Düzeltme Gerekli</span>
                                        @endif
                                        
                                        @if($kayit->onay_durumu === 'bekliyor')
                                            <span class="badge bg-warning">Bekliyor</span>
                                        @elseif($kayit->onay_durumu === 'onaylandi')
                                            <span class="badge bg-success">Onaylandı</span>
                                        @else
                                            <span class="badge bg-danger">Reddedildi</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.kontrol-kayitlari.show', $kayit->id) }}" 
                                   class="btn btn-info btn-sm flex-fill">
                                    <i class="bi bi-eye"></i> Görüntüle
                                </a>
                                @if($kayit->onay_durumu === 'bekliyor')
                                    <form action="{{ route('admin.kontrol-kayitlari.onayla', $kayit->id) }}" 
                                          method="POST" class="flex-fill">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm w-100">
                                            <i class="bi bi-check-lg"></i> Onayla
                                        </button>
                                    </form>
                                    <button type="button" class="btn btn-danger btn-sm flex-fill" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#redModal{{ $kayit->id }}">
                                        <i class="bi bi-x-lg"></i> Reddet
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @foreach($kayitlar as $kayit)
                @if($kayit->onay_durumu === 'bekliyor')
                    <div class="modal fade" id="redModal{{ $kayit->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Kaydı Reddet</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('admin.kontrol-kayitlari.reddet', $kayit->id) }}" method="POST">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">Reddetme Sebebi *</label>
                                            <textarea name="admin_notu" class="form-control" rows="4" 
                                                      required placeholder="Neden reddedildiğini açıklayın"></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                                        <button type="submit" class="btn btn-danger">Reddet</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
            
            <div class="mt-3">
                {{ $kayitlar->links() }}
            </div>
        @endif
    </div>
</div>

<style>
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
    }
    
    .empty-state i {
        font-size: 4rem;
        color: #9CA3AF;
        margin-bottom: 1rem;
    }
    
    .empty-state h3 {
        color: #6B7280;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    
    .empty-state p {
        color: #9CA3AF;
    }
</style>

<script>
    // Tümünü Seç
    document.getElementById('selectAll')?.addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.kayit-checkbox');
        checkboxes.forEach(checkbox => checkbox.checked = this.checked);
        toggleTopluOnayBtn();
    });
    
    // Tekil checkbox değişimi
    document.querySelectorAll('.kayit-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', toggleTopluOnayBtn);
    });
    
    function toggleTopluOnayBtn() {
        const checkedBoxes = document.querySelectorAll('.kayit-checkbox:checked');
        const btn = document.getElementById('topluOnayBtn');
        const count = document.getElementById('seciliSayisi');
        
        if (checkedBoxes.length > 0) {
            btn.style.display = 'block';
            count.textContent = checkedBoxes.length;
            
            // Form'a ID'leri ekle
            const form = document.getElementById('topluOnayForm');
            document.querySelectorAll('input[name="ids[]"]').forEach(el => el.remove());
            checkedBoxes.forEach(checkbox => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = checkbox.value;
                form.appendChild(input);
            });
        } else {
            btn.style.display = 'none';
        }
    }
</script>
@endsection
