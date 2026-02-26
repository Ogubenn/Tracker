@extends('layouts.app')

@section('title', 'Aylık İş Takvimi')

@push('styles')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css' rel='stylesheet' />
<style>
    .calendar-container {
        background: white;
        border-radius: 12px;
        padding: 15px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        margin-top: 10px;
    }
    
    .fc {
        font-family: 'Inter', sans-serif;
    }
    
    .fc .fc-toolbar-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1f2937;
    }
    
    .fc .fc-button {
        background-color: #667eea;
        border-color: #667eea;
        text-transform: none;
        padding: 6px 12px;
        font-weight: 500;
        font-size: 0.9rem;
    }
    
    .fc .fc-button:hover {
        background-color: #5568d3;
        border-color: #5568d3;
    }
    
    .fc .fc-button-primary:not(:disabled).fc-button-active {
        background-color: #4c51bf;
        border-color: #4c51bf;
    }
    
    .fc-daygrid-event {
        cursor: pointer;
        margin: 3px;
        border-radius: 8px !important;
        padding: 6px 10px;
        font-size: 0.85rem;
        position: relative;
        transition: all 0.2s;
        min-height: 40px !important;
        max-height: 60px !important;
        overflow: visible;
        white-space: normal !important;
    }
    
    /* FullCalendar event main içeriği */
    .fc-daygrid-event-harness {
        margin-bottom: 2px !important;
    }
    
    .fc-event-main {
        overflow: visible !important;
        white-space: normal !important;
        padding: 2px 4px !important;
    }
    
    .fc-daygrid-event:hover {
        transform: scale(1.02);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 10;
    }
    
    /* İçerik (yazı) her zaman görünür */
    .event-content {
        position: relative;
        z-index: 2;
        pointer-events: none;
        transition: opacity 0.2s;
    }
    
    /* İş başlığı 2 satıra sığdır, fazlası ... ile kesilsin */
    .fc-event-title,
    .fc-daygrid-event .fc-event-title,
    .fc-event-main .fc-event-title-container .fc-event-title {
        display: -webkit-box !important;
        -webkit-line-clamp: 2 !important;
        -webkit-box-orient: vertical !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        line-height: 1.3 !important;
        word-break: break-word !important;
        white-space: normal !important;
        max-height: 2.6em !important;
    }
    
    /* Event time gizle (sadece başlık göster) */
    .fc-daygrid-event .fc-event-time {
        display: none !important;
    }
    
    /* Hover'da yazılar kaybolsun */
    .fc-daygrid-event:hover .event-content {
        opacity: 0;
    }
    
    /* Hover'da 2'ye bölünen butonlar */
    .event-split-actions {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        display: flex;
        z-index: 3;
        border-radius: 8px;
        overflow: hidden;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.2s;
    }
    
    .fc-daygrid-event:hover .event-split-actions {
        opacity: 1;
        pointer-events: auto;
    }
    
    .event-action-half {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        color: white;
        transition: all 0.15s;
        cursor: pointer;
        font-weight: bold;
        text-shadow: 0 1px 3px rgba(0,0,0,0.3);
        pointer-events: auto;
    }
    
    .event-action-half:hover {
        transform: scale(1.05);
        filter: brightness(1.1);
    }
    
    .event-action-half:active {
        transform: scale(0.95);
        filter: brightness(0.95);
    }
    
    .event-action-complete {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        border-radius: 8px 0 0 8px;
    }
    
    .event-action-delete {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        border-radius: 0 8px 8px 0;
    }
    
    /* Mobil için alt buton paneli */
    .event-mobile-actions {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 0 0 8px 8px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        z-index: 1000;
        margin-top: -1px;
    }
    
    .event-mobile-actions.active {
        display: flex;
    }
    
    .event-mobile-btn {
        flex: 1;
        padding: 8px;
        border: none;
        background: transparent;
        font-size: 0.9rem;
        cursor: pointer;
        transition: background 0.2s;
    }
    
    .event-mobile-btn:first-child {
        border-right: 1px solid #dee2e6;
        color: #28a745;
    }
    
    .event-mobile-btn:last-child {
        color: #dc3545;
    }
    
    .event-mobile-btn:hover {
        background: #f8f9fa;
    }
    
    /* Mobilde hover devre dışı */
    @media (max-width: 768px) {
        .fc-daygrid-event:hover .event-split-actions {
            display: none;
        }
        
        .fc-daygrid-event:hover {
            transform: none;
        }
    }
    
    .event-tooltip {
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(0,0,0,0.9);
        color: white;
        padding: 8px 12px;
        border-radius: 6px;
        font-size: 0.75rem;
        white-space: nowrap;
        z-index: 1000;
        pointer-events: none;
        opacity: 0;
        transition: opacity 0.2s;
        margin-bottom: 5px;
    }
    
    .event-tooltip::after {
        content: '';
        position: absolute;
        top: 100%;
        left: 50%;
        transform: translateX(-50%);
        border: 5px solid transparent;
        border-top-color: rgba(0,0,0,0.9);
    }
    
    .fc-daygrid-event:hover .event-tooltip {
        opacity: 1;
    }
    
    .renk-legend {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        font-size: 0.75rem;
        padding: 8px 0;
        border-top: 1px solid #e5e7eb;
        margin-top: 10px;
    }
    
    .renk-item {
        display: flex;
        align-items: center;
        gap: 4px;
    }
    
    .renk-box {
        width: 12px;
        height: 12px;
        border-radius: 2px;
    }
    
    .modal-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .modal-header .btn-close {
        filter: brightness(0) invert(1);
    }
    
    .modal {
        z-index: 9999 !important;
    }
    
    .modal-backdrop {
        z-index: 9998 !important;
    }
    
    .modal-dialog {
        max-height: 90vh;
    }
    
    .modal-body {
        max-height: calc(90vh - 200px);
        overflow-y: auto;
    }
    
    .badge-durum {
        font-size: 0.85rem;
        padding: 6px 12px;
    }
    
    .form-select[multiple] {
        height: 150px;
    }
    
    .form-select[multiple] option {
        padding: 8px;
    }
    
    .form-select[multiple] option:checked {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    @media print {
        @page {
            size: landscape;
            margin: 10mm;
        }
        
        body {
            margin: 0;
            padding: 0;
        }
        
        .no-print,
        .sidebar,
        .navbar,
        .fc-toolbar,
        .calendar-header-custom {
            display: none !important;
        }
        
        .calendar-container {
            box-shadow: none;
            padding: 0;
            page-break-inside: avoid;
        }
        
        .fc {
            width: 100% !important;
            font-size: 10px !important;
        }
        
        .fc-daygrid-day-number {
            font-size: 10px !important;
        }
        
        .fc-daygrid-event {
            font-size: 8px !important;
            padding: 1px 3px !important;
        }
    }
    
    @media (max-width: 768px) {
        .calendar-container {
            padding: 10px;
        }
        
        .fc .fc-toolbar-title {
            font-size: 1.1rem;
        }
        
        .fc .fc-button {
            padding: 4px 8px;
            font-size: 0.75rem;
        }
        
        .fc-daygrid-event {
            font-size: 0.7rem;
        }
        
        .renk-legend {
            font-size: 0.7rem;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center no-print">
                <h1 class="h4 mb-0">
                    <i class="bi bi-calendar-check text-primary"></i> Aylık İş Takvimi
                </h1>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-secondary" onclick="window.print()">
                        <i class="bi bi-printer"></i> Yazdır
                    </button>
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addEventModal">
                        <i class="bi bi-plus-circle"></i> Yeni İş
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Takvim -->
    <div class="calendar-container">
        <div id="calendar"></div>
        
        <!-- Renk Açıklamaları (Takvimin altında) -->
        <div class="renk-legend">
            <div class="renk-item"><div class="renk-box" style="background:#10B981"></div><span>Tamamlandı</span></div>
            <div class="renk-item"><div class="renk-box" style="background:#F59E0B"></div><span>Bugün</span></div>
            <div class="renk-item"><div class="renk-box" style="background:#EF4444"></div><span>Gecikti</span></div>
            <div class="renk-item"><div class="renk-box" style="background:#3B82F6"></div><span>Gece</span></div>
            <div class="renk-item"><div class="renk-box" style="background:#6B7280"></div><span>Bekliyor</span></div>
        </div>
    </div>
</div>

<!-- Yeni İş Ekle Modal -->
<div class="modal fade" id="addEventModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-plus-circle"></i> Yeni İş Ekle
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addEventForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">İş Başlığı *</label>
                        <input type="text" 
                               class="form-control" 
                               name="baslik" 
                               id="addBaslik"
                               list="baslikListesi"
                               autocomplete="off"
                               required>
                        <datalist id="baslikListesi">
                            <!-- Dinamik olarak doldurulacak -->
                        </datalist>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Tarih *</label>
                        <input type="date" class="form-control" name="tarih" id="addTarih" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Atanan Kullanıcılar <small class="text-muted">(Opsiyonel - Birden fazla seçebilirsiniz)</small></label>
                        <select class="form-select" name="atanan_kullanici_ids[]" id="addKullanicilar" multiple>
                            @foreach($kullanicilar as $kullanici)
                                <option value="{{ $kullanici->id }}">{{ $kullanici->ad }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">İş Tipi *</label>
                        <select class="form-select" name="renk_kategori" required>
                            <option value="normal">Normal İş</option>
                            <option value="gece">Gece Çalışanları İşi</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="tekrarli_mi" id="tekrarliCheck">
                            <label class="form-check-label" for="tekrarliCheck">
                                Her ay seçilen günde tekrarla
                            </label>
                        </div>
                        <small class="text-muted">İşaretlerseniz, bu iş her ay seçtiğiniz tarihte otomatik tekrarlanır</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">Kaydet</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- İş Detay Modal -->
<div class="modal fade" id="eventDetailModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-info-circle"></i> İş Detayları
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <h5 id="detailBaslik" class="mb-3"></h5>
                
                <div class="mb-3">
                    <label class="text-muted d-block mb-1"><small>Atanan Kullanıcılar</small></label>
                    <div id="detailAtananlar"></div>
                </div>
                
                <div class="mb-3">
                    <label class="text-muted d-block mb-1"><small>Durum</small></label>
                    <span id="detailDurum"></span>
                </div>
                
                <div class="mb-3" id="detailTekrarDiv" style="display:none;">
                    <label class="text-muted d-block mb-1"><small>Tekrarlı İş</small></label>
                    <p id="detailTekrar" class="mb-0"><i class="bi bi-arrow-repeat"></i> Her ay tekrarlanır</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="btnToggleDurum">
                    <i class="bi bi-check-circle"></i> Durumu Değiştir
                </button>
                <button type="button" class="btn btn-primary" id="btnEdit">
                    <i class="bi bi-pencil"></i> Düzenle
                </button>
                <button type="button" class="btn btn-danger" id="btnDelete">
                    <i class="bi bi-trash"></i> Sil
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Düzenle Modal -->
<div class="modal fade" id="editEventModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-pencil"></i> İş Düzenle
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editEventForm">
                <input type="hidden" name="is_id" id="editIsId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">İş Başlığı *</label>
                        <input type="text" 
                               class="form-control" 
                               name="baslik" 
                               id="editBaslik"
                               list="baslikListesi"
                               autocomplete="off"
                               required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Tarih *</label>
                        <input type="date" class="form-control" name="tarih" id="editTarih" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Atanan Kullanıcılar <small class="text-muted">(Opsiyonel - Birden fazla seçebilirsiniz)</small></label>
                        <select class="form-select" name="atanan_kullanici_ids[]" id="editKullanicilar" multiple>
                            @foreach($kullanicilar as $kullanici)
                                <option value="{{ $kullanici->id }}">{{ $kullanici->ad }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">İş Tipi *</label>
                        <select class="form-select" name="renk_kategori" id="editRenkKategori" required>
                            <option value="normal">Normal İş</option>
                            <option value="gece">Gece Çalışanları İşi</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="tekrarli_mi" id="editTekrarliCheck">
                            <label class="form-check-label" for="editTekrarliCheck">
                                Her ay seçilen günde tekrarla
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">Güncelle</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/locales/tr.global.min.js'></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    
    if (!calendarEl) {
        console.error('Calendar div bulunamadı!');
        return;
    }
    
    let currentEventId = null;
    let currentEventData = null;
    let calendar = null; // Global calendar değişkeni
    
    // İş başlıklarını yükle (LocalStorage cache ile)
    async function loadIsBasliklari() {
        const CACHE_KEY = 'isBasliklari';
        const CACHE_TIME_KEY = 'isBasliklariTime';
        const CACHE_DURATION = 24 * 60 * 60 * 1000; // 24 saat
        
        // Cache kontrolü
        const cachedData = localStorage.getItem(CACHE_KEY);
        const cachedTime = localStorage.getItem(CACHE_TIME_KEY);
        const now = new Date().getTime();
        
        if (cachedData && cachedTime && (now - parseInt(cachedTime)) < CACHE_DURATION) {
            return JSON.parse(cachedData);
        }
        
        // Cache yoksa veya eski ise backend'den çek
        try {
            const response = await fetch('{{ route("admin.is-takvimi.basliklar") }}');
            const basliklar = await response.json();
            
            // Cache'e kaydet
            localStorage.setItem(CACHE_KEY, JSON.stringify(basliklar));
            localStorage.setItem(CACHE_TIME_KEY, now.toString());
            
            return basliklar;
        } catch (error) {
            console.error('Başlıklar yüklenemedi:', error);
            return [];
        }
    }
    
    // Datalist'i doldur
    async function populateDatalist() {
        const basliklar = await loadIsBasliklari();
        const datalist = document.getElementById('baslikListesi');
        datalist.innerHTML = '';
        
        basliklar.forEach(baslik => {
            const option = document.createElement('option');
            option.value = baslik;
            datalist.appendChild(option);
        });
    }
    
    // Sayfa yüklendiğinde başlıkları yükle
    populateDatalist();
    
    // Tab tuşu ile otomatik tamamlama
    function setupTabCompletion(inputId) {
        const input = document.getElementById(inputId);
        if (!input) return;
        
        input.addEventListener('keydown', async function(e) {
            if (e.key === 'Tab') {
                const basliklar = await loadIsBasliklari();
                const inputValue = this.value.toLowerCase();
                
                if (inputValue.length > 0) {
                    const match = basliklar.find(baslik => 
                        baslik.toLowerCase().startsWith(inputValue)
                    );
                    
                    if (match && match.toLowerCase() !== inputValue) {
                        e.preventDefault();
                        this.value = match;
                    }
                }
            }
        });
    }
    
    setupTabCompletion('addBaslik');
    setupTabCompletion('editBaslik');
    
    // Multi-select için ctrl gerektirmeyen sistem
    function enableMultiSelect(selectElement) {
        if (!selectElement) return;
        selectElement.addEventListener('mousedown', function(e) {
            if (e.target.tagName === 'OPTION') {
                e.preventDefault();
                e.target.selected = !e.target.selected;
                return false;
            }
        });
    }
    
    enableMultiSelect(document.getElementById('addKullanicilar'));
    enableMultiSelect(document.getElementById('editKullanicilar'));
    
    // Yeni iş ekle form
    document.getElementById('addEventForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        // Seçili kullanıcıları topla
        const select = document.getElementById('addKullanicilar');
        const selectedIds = Array.from(select.selectedOptions).map(opt => opt.value);
        
        const data = {
            baslik: formData.get('baslik'),
            tarih: formData.get('tarih'),
            atanan_kullanici_ids: selectedIds,
            renk_kategori: formData.get('renk_kategori'),
            tekrarli_mi: formData.has('tekrarli_mi') ? 1 : 0
        };
        
        fetch('{{ route("admin.is-takvimi.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                bootstrap.Modal.getInstance(document.getElementById('addEventModal')).hide();
                calendar.refetchEvents();
                this.reset();
                
                // Yeni başlık eklendiyse cache'i güncelle
                const newBaslik = formData.get('baslik');
                const cachedData = localStorage.getItem('isBasliklari');
                if (cachedData) {
                    const basliklar = JSON.parse(cachedData);
                    if (!basliklar.includes(newBaslik)) {
                        basliklar.push(newBaslik);
                        basliklar.sort();
                        localStorage.setItem('isBasliklari', JSON.stringify(basliklar));
                        populateDatalist(); // Datalist'i güncelle
                    }
                }
            } else {
                showToast(data.message || 'Hata oluştu', 'danger');
            }
        })
        .catch(err => {
            console.error(err);
            showToast('Hata oluştu', 'danger');
        });
    });
    
    // FullCalendar başlat
    calendar = new FullCalendar.Calendar(calendarEl, {
        locale: 'tr',
        initialView: 'dayGridMonth',
        firstDay: 1, // Pazartesi
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth'
        },
        buttonText: {
            today: 'Bugün',
            month: 'Ay'
        },
        height: 'auto',
        editable: true,
        droppable: true,
        eventDurationEditable: false,
        events: {
            url: '{{ route("admin.is-takvimi.events") }}',
            failure: function(error) {
                console.error('FullCalendar events yüklenemedi:', error);
                showToast('Takvim verileri yüklenemedi', 'danger');
            }
        },
        
        // Event render'da split button yapısı ekle
        eventDidMount: function(info) {
            const props = info.event.extendedProps;
            
            // Tam başlığı title attribute ile göster (hover'da görünür)
            info.el.title = info.event.title;
            if (props.atananlar && props.atananlar.length > 0) {
                info.el.title += '\n👤 ' + props.atananlar.join(', ');
            }
            
            // Güzel tooltip ekle
            if (props.atananlar && props.atananlar.length > 0) {
                const tooltip = document.createElement('div');
                tooltip.className = 'event-tooltip';
                tooltip.innerHTML = `<strong>Atananlar:</strong> ${props.atananlar.join(', ')}`;
                info.el.appendChild(tooltip);
            }
            
            // İçeriği wrap et
            const titleEl = info.el.querySelector('.fc-event-title');
            if (titleEl) {
                titleEl.classList.add('event-content');
            }
            
            // Split actions container (Desktop hover)
            const splitActions = document.createElement('div');
            splitActions.className = 'event-split-actions';
            
            // Sol yarı: Tamamla/Geri Al
            const completeHalf = document.createElement('div');
            completeHalf.className = 'event-action-half event-action-complete';
            completeHalf.innerHTML = props.durum === 'tamamlandi' ? '↺' : '✓';
            completeHalf.title = props.durum === 'tamamlandi' ? 'Geri Al' : 'Tamamla';
            completeHalf.addEventListener('click', function(e) {
                e.stopPropagation();
                e.preventDefault();
                console.log('Tamamla/Geri Al tıklandı:', info.event.id);
                toggleDurum(info.event.id);
            });
            
            // Sağ yarı: Sil
            const deleteHalf = document.createElement('div');
            deleteHalf.className = 'event-action-half event-action-delete';
            deleteHalf.innerHTML = '✕';
            deleteHalf.title = 'Sil';
            deleteHalf.addEventListener('click', function(e) {
                e.stopPropagation();
                e.preventDefault();
                console.log('Sil tıklandı:', info.event.id);
                deleteEvent(info.event.id);
            });
            
            splitActions.appendChild(completeHalf);
            splitActions.appendChild(deleteHalf);
            info.el.appendChild(splitActions);
            
            // Mobil buton paneli
            const mobileActions = document.createElement('div');
            mobileActions.className = 'event-mobile-actions';
            mobileActions.id = `mobile-actions-${info.event.id}`;
            
            const mobileCompleteBtn = document.createElement('button');
            mobileCompleteBtn.className = 'event-mobile-btn';
            mobileCompleteBtn.innerHTML = props.durum === 'tamamlandi' ? '↺ Geri Al' : '✓ Tamamla';
            mobileCompleteBtn.onclick = function(e) {
                e.stopPropagation();
                toggleDurum(info.event.id);
                mobileActions.classList.remove('active');
            };
            
            const mobileDeleteBtn = document.createElement('button');
            mobileDeleteBtn.className = 'event-mobile-btn';
            mobileDeleteBtn.innerHTML = '✕ Sil';
            mobileDeleteBtn.onclick = function(e) {
                e.stopPropagation();
                deleteEvent(info.event.id);
            };
            
            mobileActions.appendChild(mobileCompleteBtn);
            mobileActions.appendChild(mobileDeleteBtn);
            info.el.appendChild(mobileActions);
            
            // Mobilde tıklama ile aç
            if (window.innerWidth <= 768) {
                info.el.addEventListener('click', function(e) {
                    e.preventDefault();
                    // Diğer açık panelleri kapat
                    document.querySelectorAll('.event-mobile-actions.active').forEach(panel => {
                        if (panel.id !== mobileActions.id) {
                            panel.classList.remove('active');
                        }
                    });
                    mobileActions.classList.toggle('active');
                });
            }
        },
        
        // Drag & drop ile tarih değiştir
        eventDrop: function(info) {
            const year = info.event.start.getFullYear();
            const month = String(info.event.start.getMonth() + 1).padStart(2, '0');
            const day = String(info.event.start.getDate()).padStart(2, '0');
            const formattedDate = `${year}-${month}-${day}`;
            
            fetch(`/admin/is-takvimi/${info.event.id}/tarih`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ tarih: formattedDate })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast('İş taşındı', 'success');
                    calendar.refetchEvents();
                }
            })
            .catch(err => {
                console.error(err);
                info.revert();
                showToast('Hata oluştu', 'danger');
            });
        }
    });
    
    calendar.render();
    
    // Dışarıya tıklayınca mobil panelleri kapat
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.fc-daygrid-event')) {
            document.querySelectorAll('.event-mobile-actions.active').forEach(panel => {
                panel.classList.remove('active');
            });
        }
    });
    
    // Toast bildirimi
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `alert alert-${type} position-fixed top-0 end-0 m-3`;
        toast.style.zIndex = '9999';
        toast.textContent = message;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    }
    
    // Durumu değiştir (Global function)
    window.toggleDurum = function(eventId) {
        fetch(`/admin/is-takvimi/${eventId}/toggle-durum`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                calendar.refetchEvents();
            }
        })
        .catch(err => {
            console.error(err);
            showToast('Hata oluştu', 'danger');
        });
    };
    
    // İşi sil (Global function)
    window.deleteEvent = function(eventId) {
        fetch(`/admin/is-takvimi/${eventId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                    calendar.refetchEvents();
                }
            })
            .catch(err => {
                console.error(err);
                showToast('Hata oluştu', 'danger');
            });
    };
    
    // Durumu değiştir
    document.getElementById('btnToggleDurum').addEventListener('click', function() {
        toggleDurum(currentEventId);
        bootstrap.Modal.getInstance(document.getElementById('eventDetailModal')).hide();
    });
    
    // Düzenle butonu
    document.getElementById('btnEdit').addEventListener('click', function() {
        const props = currentEventData.extendedProps;
        
        // Form doldur
        document.getElementById('editIsId').value = currentEventId;
        document.getElementById('editBaslik').value = currentEventData.title;
        document.getElementById('editTarih').value = currentEventData.start.toISOString().split('T')[0];
        document.getElementById('editRenkKategori').value = props.renk_kategori;
        document.getElementById('editTekrarliCheck').checked = props.tekrarli_mi;
        
        // Atananları yükle
        const editSelect = document.getElementById('editKullanicilar');
        if (props.atananIds && props.atananIds.length > 0) {
            Array.from(editSelect.options).forEach(option => {
                option.selected = props.atananIds.includes(parseInt(option.value));
            });
        }
        
        // Modal değiştir
        bootstrap.Modal.getInstance(document.getElementById('eventDetailModal')).hide();
        new bootstrap.Modal(document.getElementById('editEventModal')).show();
    });
    
    // Güncelleme form
    document.getElementById('editEventForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        // Seçili kullanıcıları topla
        const editSelect = document.getElementById('editKullanicilar');
        const selectedIds = Array.from(editSelect.selectedOptions).map(opt => opt.value);
        
        const data = {
            baslik: formData.get('baslik'),
            tarih: formData.get('tarih'),
            atanan_kullanici_ids: selectedIds,
            renk_kategori: formData.get('renk_kategori'),
            tekrarli_mi: formData.has('tekrarli_mi') ? 1 : 0
        };
        const isId = formData.get('is_id');
        
        fetch(`/admin/is-takvimi/${isId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                bootstrap.Modal.getInstance(document.getElementById('editEventModal')).hide();
                calendar.refetchEvents();
            }
        })
        .catch(err => {
            console.error(err);
            showToast('Hata oluştu', 'danger');
        });
    });
    
    // Sil butonu
    document.getElementById('btnDelete').addEventListener('click', function() {
        deleteEvent(currentEventId);
        bootstrap.Modal.getInstance(document.getElementById('eventDetailModal')).hide();
    });
});
</script>
@endpush
