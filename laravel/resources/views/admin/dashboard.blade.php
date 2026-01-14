@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="page-header mb-3">
    <h1 class="h4"><i class="bi bi-speedometer2 me-2"></i>Dashboard</h1>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- İstatistik Kartları -->
<div class="row g-2 g-md-3 mb-3">
    <div class="col-6 col-md-3">
        <div class="stat-card-mini">
            <div class="stat-icon blue">
                <i class="bi bi-building"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Binalar</div>
                <div class="stat-value">{{ $binaSayisi }}</div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="stat-card-mini">
            <div class="stat-icon purple">
                <i class="bi bi-check2-square"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Kontrol Maddeleri</div>
                <div class="stat-value">{{ $kontrolMaddesiSayisi }}</div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="stat-card-mini">
            <div class="stat-icon green">
                <i class="bi bi-people"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Aktif Personel</div>
                <div class="stat-value">{{ $personelSayisi }}</div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="stat-card-mini">
            <div class="stat-icon {{ $bugunYapilanKontroller > 0 ? 'orange' : 'gray' }}">
                <i class="bi bi-calendar-check"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Bugün Yapılan</div>
                <div class="stat-value">{{ $bugunYapilanKontroller }}</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <!-- Sol Kolon: Takvim -->
    <div class="col-lg-5">
        <div class="content-card">
            <div class="content-card-header">
                <h5 class="mb-0"><i class="bi bi-calendar3 me-2"></i>{{ $calendar['monthName'] }}</h5>
            </div>
            <div class="content-card-body p-2 p-md-3">
                <div class="calendar-compact">
                    <div class="calendar-header">
                        <div class="calendar-day-name">Pzt</div>
                        <div class="calendar-day-name">Sal</div>
                        <div class="calendar-day-name">Çar</div>
                        <div class="calendar-day-name">Per</div>
                        <div class="calendar-day-name">Cum</div>
                        <div class="calendar-day-name">Cmt</div>
                        <div class="calendar-day-name">Paz</div>
                    </div>
                    <div class="calendar-grid">
                        @php
                            $offset = $calendar['startDayOfWeek'] == 0 ? 6 : $calendar['startDayOfWeek'] - 1;
                        @endphp
                        
                        @for($i = 0; $i < $offset; $i++)
                            <div class="calendar-day-cell empty"></div>
                        @endfor
                        
                        @foreach($calendar['days'] as $dayData)
                            <div class="calendar-day-cell status-{{ $dayData['status'] }} {{ $dayData['day'] == $today ? 'today' : '' }}"
                                 data-date="{{ $dayData['date']->format('Y-m-d') }}"
                                 data-status="{{ $dayData['status'] }}"
                                 onclick="showDayDetails('{{ $dayData['date']->format('Y-m-d') }}')">
                                <span class="day-number">{{ $dayData['day'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
                
                <!-- Takvim Açıklaması -->
                <div class="calendar-legend mt-2">
                    <div class="legend-item">
                        <span class="legend-color bg-success"></span>
                        <span class="legend-text">Tamamlandı</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-color bg-warning"></span>
                        <span class="legend-text">Devam Ediyor</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-color bg-danger"></span>
                        <span class="legend-text">Eksik/Uygunsuz</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hızlı Erişim -->
        <div class="content-card mt-3">
            <div class="content-card-header">
                <h5 class="mb-0"><i class="bi bi-lightning-charge me-2"></i>Hızlı Erişim</h5>
            </div>
            <div class="content-card-body p-2">
                <div class="row g-2">
                    <div class="col-6">
                        <a href="{{ route('admin.binalar.create') }}" class="quick-link-btn">
                            <i class="bi bi-building-add"></i>
                            <span>Yeni Bina</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('admin.kontrol-maddeleri.create') }}" class="quick-link-btn">
                            <i class="bi bi-plus-square"></i>
                            <span>Kontrol Maddesi</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('admin.raporlar.index') }}" class="quick-link-btn">
                            <i class="bi bi-file-earmark-text"></i>
                            <span>Raporlar</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('admin.users.index') }}" class="quick-link-btn">
                            <i class="bi bi-people"></i>
                            <span>Kullanıcılar</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('admin.kontrol-kayitlari.index') }}" class="quick-link-btn">
                            <i class="bi bi-list-check"></i>
                            <span>Kontrol Kayıtları</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('admin.binalar.index') }}" class="quick-link-btn">
                            <i class="bi bi-buildings"></i>
                            <span>Binalar</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Orta Kolon: Bugünkü Fotoğraflar -->
    <div class="col-lg-4">
        <div class="content-card">
            <div class="content-card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-images me-2"></i>Bugünkü Fotoğraflar</h5>
                @if($bugunFotograflar->isNotEmpty())
                    <span class="badge bg-primary">{{ $bugunFotograflar->count() }}</span>
                @endif
            </div>
            <div class="content-card-body p-2">
                @if($bugunFotograflar->isEmpty())
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-camera" style="font-size: 2rem;"></i>
                        <p class="small mb-0 mt-2">Bugün henüz fotoğraf eklenmemiş</p>
                    </div>
                @else
                    <div class="foto-gallery-grid">
                        @foreach($bugunFotograflar as $foto)
                            <div class="foto-gallery-item">
                                <a href="{{ $foto['url'] }}" 
                                   data-lightbox="dashboard-fotograflar" 
                                   data-title="{{ $foto['bina'] }} - {{ $foto['madde'] }}">
                                    <img src="{{ $foto['url'] }}" 
                                         alt="{{ $foto['bina'] }}" 
                                         loading="lazy">
                                    <div class="foto-overlay">
                                        <i class="bi bi-zoom-in"></i>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                    <div class="text-center mt-2">
                        <a href="{{ route('admin.kontrol-kayitlari.index', ['tarih_baslangic' => now()->format('Y-m-d'), 'tarih_bitis' => now()->format('Y-m-d')]) }}" 
                           class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye me-1"></i>Tümünü Gör
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Sağ Kolon: Notlar -->
    <div class="col-lg-3">
        <div class="content-card">
            <div class="content-card-header">
                <h5 class="mb-0"><i class="bi bi-sticky me-2"></i>Notlar</h5>
            </div>
            <div class="content-card-body p-2">
                <!-- Not Ekleme Formu -->
                <form action="{{ route('admin.dashboard.notes.store') }}" method="POST" class="mb-3">
                    @csrf
                    <div class="input-group input-group-sm">
                        <textarea name="note" class="form-control form-control-sm" rows="2" placeholder="Yeni not..." required maxlength="1000"></textarea>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus-lg"></i>
                        </button>
                    </div>
                    @error('note')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </form>

                <!-- Not Listesi -->
                <div class="notes-list-compact">
                    @forelse($latestNotes as $note)
                        <div class="note-item-compact">
                            <div class="note-header">
                                <span class="note-author">{{ $note->user->ad }}</span>
                                <span class="note-date">{{ $note->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="note-content">{{ Str::limit($note->note, 80) }}</div>
                            <div class="note-actions">
                                @if($note->mail_sent_at)
                                    <span class="badge bg-success badge-sm">
                                        <i class="bi bi-envelope-check"></i> Gönderildi
                                    </span>
                                @else
                                    <form action="{{ route('admin.dashboard.notes.send', $note->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-send"></i>
                                        </button>
                                    </form>
                                @endif
                                
                                <form action="{{ route('admin.dashboard.notes.delete', $note->id) }}" method="POST" class="d-inline" 
                                      onsubmit="return confirm('Notu silmek istediğinizden emin misiniz?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="text-muted text-center py-3 small">
                            <i class="bi bi-inbox"></i>
                            <div>Henüz not eklenmemiş</div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Gün Detayları Modal -->
<div class="modal fade" id="dayDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-calendar-day me-2"></i><span id="modalDate"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalBody">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Yükleniyor...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Kompakt İstatistik Kartları */
.stat-card-mini {
    background: white;
    border-radius: 8px;
    padding: 12px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    display: flex;
    align-items: center;
    gap: 12px;
    transition: transform 0.2s;
}

.stat-card-mini:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.stat-icon {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    flex-shrink: 0;
}

.stat-icon.blue { background: #e7f3ff; color: #0066cc; }
.stat-icon.purple { background: #f3e7ff; color: #6600cc; }
.stat-icon.green { background: #e7ffe7; color: #00aa00; }
.stat-icon.orange { background: #fff3e7; color: #ff8800; }
.stat-icon.gray { background: #f0f0f0; color: #666; }

.stat-info {
    flex: 1;
    min-width: 0;
}

.stat-label {
    font-size: 0.75rem;
    color: #666;
    margin-bottom: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.stat-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: #333;
    line-height: 1;
}

/* Kompakt Takvim */
.calendar-compact {
    width: 100%;
}

.calendar-header {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 3px;
    margin-bottom: 5px;
    padding: 0 2px;
}

.calendar-day-name {
    text-align: center;
    font-weight: 600;
    font-size: 0.65rem;
    color: #666;
    padding: 4px 0;
}

.calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 3px;
}

.calendar-day-cell {
    aspect-ratio: 1;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    border: 2px solid transparent;
    padding: 2px;
}

.calendar-day-cell.empty {
    cursor: default;
    background: transparent;
}

.calendar-day-cell:not(.empty):hover {
    transform: scale(1.05);
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}

.calendar-day-cell.today {
    border-color: #0066cc !important;
    font-weight: 700;
}

.calendar-day-cell.status-success {
    background: #d1e7dd;
    color: #0f5132;
}

.calendar-day-cell.status-warning {
    background: #fff3cd;
    color: #856404;
}

.calendar-day-cell.status-danger {
    background: #f8d7da;
    color: #842029;
}

.calendar-day-cell.status-future {
    background: #f8f9fa;
    color: #adb5bd;
    cursor: not-allowed;
}

.calendar-day-cell.status-none {
    background: #ffffff;
    color: #dee2e6;
    border: 1px solid #e9ecef;
}

.calendar-legend {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    font-size: 0.7rem;
    justify-content: center;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 4px;
}

.legend-color {
    width: 12px;
    height: 12px;
    border-radius: 3px;
}

/* Hızlı Erişim Butonları */
.quick-link-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 12px 8px;
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    text-decoration: none;
    color: #333;
    transition: all 0.2s;
    gap: 6px;
    min-height: 70px;
}

.quick-link-btn i {
    font-size: 1.5rem;
    color: #0066cc;
}

.quick-link-btn span {
    font-size: 0.75rem;
    font-weight: 500;
    text-align: center;
}

.quick-link-btn:hover {
    background: #f8f9fa;
    border-color: #0066cc;
    color: #0066cc;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

/* Kompakt Notlar */
.notes-list-compact {
    max-height: 500px;
    overflow-y: auto;
}

.note-item-compact {
    background: #f8f9fa;
    border-radius: 6px;
    padding: 10px;
    margin-bottom: 8px;
    border-left: 3px solid #0066cc;
}

.note-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 6px;
    font-size: 0.75rem;
}

.note-author {
    font-weight: 600;
    color: #333;
}

.note-date {
    color: #999;
}

.note-content {
    font-size: 0.85rem;
    color: #555;
    margin-bottom: 8px;
    line-height: 1.4;
}

.note-actions {
    display: flex;
    gap: 6px;
    align-items: center;
}

.note-actions .badge-sm {
    font-size: 0.7rem;
    padding: 3px 8px;
}

/* Fotoğraf Galerisi */
.foto-gallery-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 8px;
}

.foto-gallery-item {
    position: relative;
    aspect-ratio: 1;
    overflow: hidden;
    border-radius: 8px;
    cursor: pointer;
}

.foto-gallery-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s;
}

.foto-gallery-item:hover img {
    transform: scale(1.1);
}

.foto-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s;
}

.foto-gallery-item:hover .foto-overlay {
    opacity: 1;
}

.foto-overlay i {
    color: white;
    font-size: 1.5rem;
}

/* Responsive */
@media (max-width: 768px) {
    .stat-value {
        font-size: 1.25rem;
    }
    
    .stat-label {
        font-size: 0.7rem;
    }
    
    .stat-icon {
        width: 35px;
        height: 35px;
        font-size: 1rem;
    }
    
    .calendar-day-cell {
        font-size: 0.7rem;
    }
    
    .calendar-day-name {
        font-size: 0.6rem;
    }
    
    .quick-link-btn {
        min-height: 60px;
        padding: 8px 4px;
    }
    
    .quick-link-btn i {
        font-size: 1.2rem;
    }
    
    .quick-link-btn span {
        font-size: 0.7rem;
    }
    
    .foto-gallery-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
</style>

<script>
function showDayDetails(date) {
    const modal = new bootstrap.Modal(document.getElementById('dayDetailsModal'));
    const modalBody = document.getElementById('modalBody');
    const modalDate = document.getElementById('modalDate');
    
    // Loading göster
    modalBody.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Yükleniyor...</span>
            </div>
        </div>
    `;
    
    modal.show();
    
    // AJAX ile detayları getir
    fetch(`{{ route('admin.dashboard.day-details') }}?date=${date}`)
        .then(response => response.json())
        .then(data => {
            modalDate.textContent = data.date;
            
            let html = `
                <div class="row g-3 mb-3">
                    <div class="col-4">
                        <div class="text-center p-3 bg-light rounded">
                            <div class="h3 mb-0 text-primary">${data.yapilmasi_gereken}</div>
                            <div class="small text-muted">Toplam</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="text-center p-3 bg-light rounded">
                            <div class="h3 mb-0 text-success">${data.yapilan}</div>
                            <div class="small text-muted">Yapılan</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="text-center p-3 bg-light rounded">
                            <div class="h3 mb-0 text-danger">${data.eksik}</div>
                            <div class="small text-muted">Eksik</div>
                        </div>
                    </div>
                </div>
            `;
            
            if (data.eksik > 0) {
                html += `
                    <div class="alert alert-warning">
                        <h6 class="alert-heading"><i class="bi bi-exclamation-triangle me-2"></i>Eksik Kontroller</h6>
                        <ul class="mb-0 small">
                            ${data.eksik_kontroller.map(k => `<li>${k}</li>`).join('')}
                        </ul>
                    </div>
                `;
            }
            
            if (data.uygunsuz_kontroller.length > 0) {
                html += `
                    <div class="alert alert-danger">
                        <h6 class="alert-heading"><i class="bi bi-x-circle me-2"></i>Uygunsuz Kontroller</h6>
                        <ul class="mb-0 small">
                            ${data.uygunsuz_kontroller.map(k => `<li>${k}</li>`).join('')}
                        </ul>
                    </div>
                `;
            }
            
            if (data.eksik == 0 && data.uygunsuz_kontroller.length == 0 && data.yapilmasi_gereken > 0) {
                html += `
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle me-2"></i>
                        Tüm kontroller tamamlandı ve uygun!
                    </div>
                `;
            }
            
            if (data.yapilmasi_gereken == 0) {
                html += `
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        Bu gün için planlanmış kontrol yok.
                    </div>
                `;
            }
            
            html += `
                <div class="text-center mt-3">
                    <a href="{{ route('admin.kontrol-kayitlari.index') }}?tarih_baslangic=${date}&tarih_bitis=${date}" 
                       class="btn btn-primary btn-sm">
                        <i class="bi bi-list-check me-2"></i>Kayıtları Görüntüle
                    </a>
                </div>
            `;
            
            modalBody.innerHTML = html;
        })
        .catch(error => {
            modalBody.innerHTML = `
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Veriler yüklenirken bir hata oluştu.
                </div>
            `;
        });
}
</script>

@endsection
