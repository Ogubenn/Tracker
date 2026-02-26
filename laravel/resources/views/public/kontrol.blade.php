<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ $bina->bina_adi }} - Kontrol Formu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            /* Site Renkleri - Admin panel ile uyumlu */
            --primary: #d9041e;
            --primary-dark: #b3031a;
            --primary-light: #ff1a32;
            --success: #10B981;
            --warning: #F59E0B;
            --danger: #EF4444;
            --info: #3B82F6;
            
            /* Gri Tonlar */
            --gray-50: #F9FAFB;
            --gray-100: #F3F4F6;
            --gray-200: #E5E7EB;
            --gray-300: #D1D5DB;
            --gray-600: #4B5563;
            --gray-700: #374151;
            --gray-900: #111827;
            
            /* Shadows */
            --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.07);
            --shadow-lg: 0 10px 15px rgba(0, 0, 0, 0.1);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: var(--gray-100);
            min-height: 100vh;
            padding-bottom: 100px;
            -webkit-font-smoothing: antialiased;
        }
        
        /* Header - Daha compact */
        .header-compact {
            background: white;
            padding: 1rem;
            box-shadow: var(--shadow-md);
            position: sticky;
            top: 0;
            z-index: 1000;
            border-bottom: 3px solid var(--primary);
        }
        
        .header-compact h1 {
            font-size: 1rem;
            font-weight: 700;
            color: var(--gray-900);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .header-compact h1 i {
            color: var(--primary);
            font-size: 1.125rem;
        }
        
        .header-date {
            font-size: 0.75rem;
            color: var(--gray-600);
            margin-top: 0;
            display: flex;
            align-items: center;
            gap: 0.375rem;
        }
        
        /* Progress Bar - Minimal ve sticky */
        .progress-sticky {
            background: white;
            padding: 0.75rem 1rem;
            box-shadow: var(--shadow-sm);
            position: sticky;
            top: 94px;
            z-index: 999;
            border-bottom: 1px solid var(--gray-200);
        }
        
        .progress-text-compact {
            font-size: 0.8125rem;
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: 0.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .progress-number {
            color: var(--primary);
            font-size: 1rem;
        }
        
        .progress-bar-modern {
            height: 6px;
            background: var(--gray-200);
            border-radius: 10px;
            overflow: hidden;
        }
        
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--primary), var(--primary-light));
            border-radius: 10px;
            transition: width 0.4s ease;
            position: relative;
        }
        
        .progress-fill::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            animation: shimmer 2s infinite;
        }
        
        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
        
        /* Container */
        .content-container {
            max-width: 640px;
            margin: 0 auto;
            padding: 0.75rem;
        }
        
        /* Personel Seçimi - Daha compact */
        .personel-select-card {
            background: white;
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 0.75rem;
            box-shadow: var(--shadow-sm);
            border-left: 4px solid var(--primary);
        }
        
        .personel-select-card label {
            font-size: 0.875rem;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 0.5rem;
            display: block;
        }
        
        .personel-select-card label i {
            color: var(--primary);
        }
        
        .personel-select-card select {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid var(--gray-200);
            border-radius: 8px;
            font-size: 0.9375rem;
            font-weight: 600;
            background: white;
            color: var(--gray-900);
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 16 16'%3E%3Cpath fill='%23d9041e' d='M13.707 6.293L8 11.586 2.293 6.293A1 1 0 00.879 7.707l6 6a1 1 0 001.414 0l6-6a1 1 0 10-1.414-1.414z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            padding-right: 2.5rem;
            transition: all 0.2s;
        }
        
        .personel-select-card select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(217, 4, 30, 0.1);
        }
        
        /* Kontrol Kartı - Modern ve compact */
        .control-card {
            background: white;
            border-radius: 12px;
            margin-bottom: 0.75rem;
            box-shadow: var(--shadow-sm);
            overflow: hidden;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        
        .control-card.active {
            border-color: var(--primary);
            box-shadow: 0 4px 12px rgba(217, 4, 30, 0.15);
        }
        
        .control-card.completed {
            border-color: var(--success);
            background: linear-gradient(to right, #ECFDF5, white);
        }
        
        /* Kontrol Header - Daha küçük */
        .control-header {
            padding: 1rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            background: white;
        }
        
        .control-number {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: var(--gray-100);
            color: var(--gray-700);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.875rem;
            flex-shrink: 0;
            border: 2px solid var(--gray-200);
        }
        
        .control-card.completed .control-number {
            background: var(--success);
            color: white;
            border-color: var(--success);
        }
        
        .control-info {
            flex: 1;
            min-width: 0;
        }
        
        .control-title {
            font-size: 0.9375rem;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 0.25rem;
            line-height: 1.3;
        }
        
        .control-badge {
            display: inline-block;
            font-size: 0.6875rem;
            font-weight: 600;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .badge-gunluk { background: #DBEAFE; color: #1E40AF; }
        .badge-haftalik { background: #D1FAE5; color: #065F46; }
        .badge-aylik { background: #FEF3C7; color: #92400E; }
        .badge-15_gun { background: #FED7AA; color: #9A3412; }
        
        .control-icon {
            font-size: 1.125rem;
            color: var(--gray-400);
            flex-shrink: 0;
        }
        
        .control-card.active .control-icon {
            color: var(--primary);
        }
        
        /* Kontrol Body */
        .control-body {
            padding: 0 1rem 1rem 1rem;
            display: none;
        }
        
        .control-card.active .control-body {
            display: block;
        }
        
        /* Checkbox - Daha belirgin */
        .checkbox-modern {
            background: var(--gray-50);
            border: 2px solid var(--gray-300);
            border-radius: 8px;
            padding: 0.875rem;
            margin-bottom: 1rem;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .checkbox-modern:hover {
            background: white;
            border-color: var(--primary);
        }
        
        .checkbox-modern.checked {
            background: #ECFDF5;
            border-color: var(--success);
        }
        
        .checkbox-modern label {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            cursor: pointer;
            margin: 0;
            font-weight: 600;
            font-size: 0.9375rem;
            color: var(--gray-900);
        }
        
        .checkbox-modern input[type="checkbox"] {
            width: 24px;
            height: 24px;
            cursor: pointer;
            accent-color: var(--success);
            flex-shrink: 0;
        }
        
        /* Input Group - Compact */
        .input-group-modern {
            margin-bottom: 1rem;
        }
        
        .input-label {
            font-size: 0.8125rem;
            font-weight: 700;
            color: var(--gray-700);
            margin-bottom: 0.5rem;
            display: block;
        }
        
        .input-modern {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid var(--gray-200);
            border-radius: 8px;
            font-size: 0.9375rem;
            font-weight: 500;
            background: var(--gray-50);
            transition: all 0.2s;
        }
        
        .input-modern:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(217, 4, 30, 0.1);
            background: white;
        }
        
        textarea.input-modern {
            min-height: 80px;
            resize: vertical;
        }
        
        /* Durum Seçimi - Kompakt */
        .status-group {
            margin-bottom: 1rem;
        }
        
        .status-label {
            font-size: 0.8125rem;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 0.625rem;
            display: flex;
            align-items: center;
            gap: 0.375rem;
        }
        
        .status-required {
            color: var(--danger);
        }
        
        .status-options {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.5rem;
        }
        
        .status-option input {
            display: none;
        }
        
        .status-option label {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.375rem;
            padding: 0.75rem 0.5rem;
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 700;
            background: var(--gray-100);
            color: var(--gray-600);
            cursor: pointer;
            transition: all 0.2s;
            border: 2px solid transparent;
            text-align: center;
        }
        
        .status-option label i {
            font-size: 1.5rem;
        }
        
        .status-option input:checked + label {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .status-option input:checked + label.success {
            background: #ECFDF5;
            color: var(--success);
            border-color: var(--success);
        }
        
        .status-option input:checked + label.warning {
            background: #FFFBEB;
            color: var(--warning);
            border-color: var(--warning);
        }
        
        .status-option input:checked + label.danger {
            background: #FEF2F2;
            color: var(--danger);
            border-color: var(--danger);
        }
        
        /* Zaman Seçimi */
        .time-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
            margin-bottom: 1rem;
        }
        
        .time-input-group label {
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--gray-600);
            margin-bottom: 0.375rem;
            display: block;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .time-input-group input[type="time"] {
            width: 100%;
            padding: 0.625rem 0.75rem;
            border: 2px solid var(--gray-200);
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            background: var(--gray-50);
            transition: all 0.2s;
        }
        
        /* Fotoğraf - Sadece kamera */
        .camera-btn {
            border: 2px dashed var(--gray-300);
            border-radius: 8px;
            padding: 0.75rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
            background: var(--gray-50);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        
        .camera-btn:hover {
            border-color: var(--primary);
            background: white;
        }
        
        .camera-btn i {
            font-size: 1.25rem;
            color: var(--primary);
        }
        
        .camera-btn span {
            font-weight: 600;
            color: var(--gray-700);
            font-size: 0.875rem;
        }
        
        .photo-preview {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.5rem;
            margin-top: 0.75rem;
        }
        
        .photo-preview img {
            width: 100%;
            height: 80px;
            object-fit: cover;
            border-radius: 6px;
            border: 2px solid var(--gray-200);
        }
        
        /* Genel Not Kartı */
        .notes-card {
            background: white;
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 0.75rem;
            box-shadow: var(--shadow-sm);
        }
        
        .notes-card h3 {
            font-size: 1rem;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        /* Submit Button - Sabit alt */
        .submit-fixed {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            padding: 1rem;
            box-shadow: 0 -4px 12px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            border-top: 1px solid var(--gray-200);
        }
        
        .submit-btn {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, var(--success), #059669);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .submit-btn:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);
        }
        
        .submit-btn:active:not(:disabled) {
            transform: scale(0.98);
        }
        
        .submit-btn:disabled {
            background: var(--gray-300);
            cursor: not-allowed;
        }
        
        /* Alert */
        .alert-modern {
            background: #ECFDF5;
            border: 2px solid var(--success);
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: #065F46;
            font-weight: 600;
        }
        
        .alert-modern i {
            font-size: 1.5rem;
        }
        
        /* Empty State */
        .empty-state {
            background: white;
            border-radius: 12px;
            padding: 3rem 1.5rem;
            text-align: center;
            margin-top: 2rem;
        }
        
        .empty-state i {
            font-size: 4rem;
            color: var(--success);
            margin-bottom: 1rem;
        }
        
        .empty-state h3 {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 0.5rem;
        }
        
        .empty-state p {
            color: var(--gray-600);
        }
        
        /* Responsive */
        @media (max-width: 360px) {
            .status-options {
                grid-template-columns: 1fr;
            }
            
            .status-option label {
                flex-direction: row;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header-compact">
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
            <img src="{{ asset('images/logo.jpg') }}" alt="Bulancak Belediyesi" style="width: 48px; height: 48px; border-radius: 8px; object-fit: cover; box-shadow: var(--shadow-sm);">
            <div style="flex: 1;">
                <h1 style="margin: 0; font-size: 1rem;">
                    <i class="bi bi-building-fill"></i>
                    {{ $bina->bina_adi }}
                </h1>
                <div class="header-date" style="margin-top: 0.125rem;">
                    <i class="bi bi-calendar3"></i>
                    {{ $bugun->translatedFormat('d F Y') }}
                </div>
            </div>
        </div>
        
        @if(isset($sonKontrol) && $sonKontrol && $sonKontrol->yapanKullanici)
        <div style="background: var(--gray-50); border-radius: 6px; padding: 0.5rem 0.75rem; margin-top: 0.5rem; border-left: 3px solid var(--success);">
            <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.75rem; color: var(--gray-600);">
                <i class="bi bi-clock-history" style="color: var(--success);"></i>
                <span style="font-weight: 600;">Son Kontrol:</span>
                <span style="color: var(--gray-700);">{{ $sonKontrol->yapanKullanici->ad }} {{ $sonKontrol->yapanKullanici->soyad }}</span>
                <span style="color: var(--gray-500);">•</span>
                <span>{{ $sonKontrol->created_at->diffForHumans() }}</span>
            </div>
        </div>
        @endif
    </div>

    @if(session('success'))
        <div class="content-container" style="padding-top: 1rem;">
            <div class="alert-modern">
                <i class="bi bi-check-circle-fill"></i>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if($kontrolMaddeleri->isEmpty())
        <div class="content-container">
            <div class="empty-state">
                <i class="bi bi-check-circle-fill"></i>
                <h3>✨ Tüm Kontroller Tamamlandı!</h3>
                <p>Bu bina için bugün yapılması gereken kontrol bulunmuyor.</p>
            </div>
        </div>
    @else
        <!-- Progress Bar -->
        <div class="progress-sticky">
            <div class="progress-text-compact">
                <span>İlerleme Durumu</span>
                <span class="progress-number">
                    <span id="completedCount">0</span> / {{ $kontrolMaddeleri->count() }}
                </span>
            </div>
            <div class="progress-bar-modern">
                <div class="progress-fill" id="progressBar" style="width: 0%"></div>
            </div>
        </div>

        <form action="{{ route('public.kontrol.store', $bina->uuid) }}" method="POST" enctype="multipart/form-data" id="kontrolForm">
            @csrf
            
            <div class="content-container">
                <!-- Personel Seçimi -->
                <div class="personel-select-card">
                    <label>
                        <i class="bi bi-person-fill me-1"></i>
                        Kim Kontrol Yapıyor? <span class="status-required">*</span>
                    </label>
                    <select name="personel_id" id="personel_id" required>
                        <option value="">Adınızı seçin</option>
                        @foreach($personeller as $personel)
                            <option value="{{ $personel->id }}">
                                {{ $personel->ad }} {{ $personel->soyad }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Kontrol Listesi -->
                @foreach($kontrolMaddeleri as $index => $madde)
                    <div class="control-card active" id="card{{ $index }}">
                        <div class="control-header" onclick="toggleCard({{ $index }})">
                            <div class="control-number">{{ $index + 1 }}</div>
                            <div class="control-info">
                                <div class="control-title">{{ $madde->kontrol_adi }}</div>
                                <span class="control-badge badge-{{ $madde->periyot }}">
                                    @if($madde->periyot === 'gunluk') Günlük
                                    @elseif($madde->periyot === 'haftalik') Haftalık
                                    @elseif($madde->periyot === '15_gun') 15 Günlük
                                    @elseif($madde->periyot === 'aylik') Aylık
                                    @endif
                                </span>
                            </div>
                            <i class="bi bi-chevron-down control-icon" id="icon{{ $index }}" style="transform: rotate(180deg);"></i>
                        </div>
                        
                        <div class="control-body">
                            <input type="hidden" name="kayitlar[{{ $index }}][kontrol_maddesi_id]" value="{{ $madde->id }}">
                            
                            <!-- Checkbox -->
                            <div class="checkbox-modern" id="checkbox{{ $index }}">
                                <label>
                                    <input type="checkbox" id="yapildi_{{ $madde->id }}" onchange="handleCheckbox({{ $index }})">
                                    <span><i class="bi bi-check-circle me-1"></i> Kontrol Yapıldı</span>
                                </label>
                            </div>
                            
                            <!-- Değer Girişi -->
                            @if($madde->kontrol_tipi === 'sayisal')
                                <div class="input-group-modern">
                                    <label class="input-label">
                                        <i class="bi bi-123 me-1"></i> Ölçüm Değeri
                                        @if($madde->birim) ({{ $madde->birim }}) @endif
                                    </label>
                                    <input type="number" step="0.01" class="input-modern"
                                           name="kayitlar[{{ $index }}][girilen_deger]" 
                                           placeholder="Değer girin">
                                </div>
                            @elseif($madde->kontrol_tipi === 'metin')
                                <div class="input-group-modern">
                                    <label class="input-label">
                                        <i class="bi bi-pencil me-1"></i> Kontrol Sonucu
                                    </label>
                                    <input type="text" class="input-modern"
                                           name="kayitlar[{{ $index }}][girilen_deger]" 
                                           placeholder="Gözleminizi yazın">
                                </div>
                            @endif
                            
                            <!-- Zaman Seçimi -->
                            @if($madde->zaman_secimi)
                                <div class="time-grid">
                                    <div class="time-input-group">
                                        <label><i class="bi bi-clock text-success"></i> Başlangıç</label>
                                        <input type="time" name="kayitlar[{{ $index }}][baslangic_saati]">
                                    </div>
                                    <div class="time-input-group">
                                        <label><i class="bi bi-clock-fill text-danger"></i> Bitiş</label>
                                        <input type="time" name="kayitlar[{{ $index }}][bitis_saati]">
                                    </div>
                                </div>
                            @endif
                            
                            <!-- Durum Seçimi -->
                            <div class="status-group">
                                <div class="status-label">
                                    <i class="bi bi-clipboard-check"></i>
                                    <span>Durum</span>
                                    <span class="status-required">*</span>
                                </div>
                                <div class="status-options">
                                    <div class="status-option">
                                        <input type="radio" id="uygun_{{ $madde->id }}" 
                                               name="kayitlar[{{ $index }}][durum]" value="uygun" 
                                               onchange="updateProgress()">
                                        <label for="uygun_{{ $madde->id }}" class="success">
                                            <i class="bi bi-check-circle-fill"></i>
                                            <span>Uygun</span>
                                        </label>
                                    </div>
                                    <div class="status-option">
                                        <input type="radio" id="duzeltme_{{ $madde->id }}" 
                                               name="kayitlar[{{ $index }}][durum]" value="duzeltme_gerekli"
                                               onchange="updateProgress()">
                                        <label for="duzeltme_{{ $madde->id }}" class="warning">
                                            <i class="bi bi-exclamation-triangle-fill"></i>
                                            <span>Düzeltme</span>
                                        </label>
                                    </div>
                                    <div class="status-option">
                                        <input type="radio" id="uygun_degil_{{ $madde->id }}" 
                                               name="kayitlar[{{ $index }}][durum]" value="uygun_degil"
                                               onchange="updateProgress()">
                                        <label for="uygun_degil_{{ $madde->id }}" class="danger">
                                            <i class="bi bi-x-circle-fill"></i>
                                            <span>Uygun Değil</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Fotoğraf Çekme -->
                            <div class="input-group-modern">
                                <label class="input-label">
                                    <i class="bi bi-camera me-1"></i> Fotoğraf Çek
                                    <span style="font-weight: 400; color: var(--gray-500);">(İsteğe bağlı)</span>
                                </label>
                                <div class="camera-btn" onclick="document.getElementById('foto{{ $index }}').click()">
                                    <i class="bi bi-camera-fill"></i>
                                    <span>Fotoğraf Çek</span>
                                    <input type="file" id="foto{{ $index }}"
                                           name="kayitlar[{{ $index }}][fotograflar][]" 
                                           accept="image/*" 
                                           multiple 
                                           capture="environment"
                                           style="display: none;"
                                           onchange="showPreview(this, {{ $index }})">
                                </div>
                                <div class="photo-preview" id="preview{{ $index }}"></div>
                            </div>
                        </div>
                    </div>
                @endforeach

                <!-- Genel Not -->
                <div class="notes-card">
                    <h3><i class="bi bi-chat-left-text"></i> Genel Not</h3>
                    <div class="input-group-modern">
                        <label class="input-label">Açıklama (İsteğe bağlı)</label>
                        <textarea name="genel_aciklama" class="input-modern" 
                                  placeholder="Genel gözlem veya notlarınızı yazın..."></textarea>
                    </div>
                    
                    <div class="input-group-modern">
                        <label class="input-label">
                            <i class="bi bi-camera me-1"></i> Fotoğraf Ekle
                        </label>
                        <div class="camera-btn" onclick="document.getElementById('genelFoto').click()">
                            <i class="bi bi-camera-fill"></i>
                            <span>Fotoğraf Çek</span>
                            <input type="file" id="genelFoto"
                                   name="genel_dosyalar[]" 
                                   accept="image/*" 
                                   multiple 
                                   capture="environment"
                                   style="display: none;"
                                   onchange="showGenelPreview(this)">
                        </div>
                        <div class="photo-preview" id="genelPreview"></div>
                    </div>
                </div>
            </div>
        </form>

        <!-- Submit Button -->
        <div class="submit-fixed">
            <button type="button" class="submit-btn" id="submitBtn" onclick="handleSubmit()">
                <i class="bi bi-check-circle-fill"></i>
                <span>Kaydet <small style="opacity: 0.8; font-size: 0.75rem;">(Yarım Bırakabilirsiniz)</small></span>
            </button>
        </div>
    @endif

    <script>
        let completedCount = 0;
        const totalControls = {{ $kontrolMaddeleri->count() }};
        let currentCard = -1; // Hiçbiri seçili değil

        function toggleCard(index) {
            const card = document.getElementById('card' + index);
            const icon = document.getElementById('icon' + index);
            
            // Eğer kart zaten açıksa, sadece aç (kapatma)
            const isActive = card.classList.contains('active');
            
            // Tüm kartları kapat
            document.querySelectorAll('.control-card').forEach((c, i) => {
                if (i !== index) {
                    c.classList.remove('active');
                    const otherIcon = document.getElementById('icon' + i);
                    if (otherIcon) {
                        otherIcon.style.transform = 'rotate(0deg)';
                    }
                }
            });
            
            // Seçili kartı toggle et
            if (isActive) {
                // Zaten açıksa, kapat
                card.classList.remove('active');
                icon.style.transform = 'rotate(0deg)';
                currentCard = -1;
            } else {
                // Kapalıysa, aç
                card.classList.add('active');
                icon.style.transform = 'rotate(180deg)';
                currentCard = index;
            }
        }

        function handleCheckbox(index) {
            const checkbox = document.getElementById('checkbox' + index);
            const isChecked = checkbox.querySelector('input').checked;
            
            if (isChecked) {
                checkbox.classList.add('checked');
            } else {
                checkbox.classList.remove('checked');
            }
            
            updateProgress();
        }

        function updateProgress() {
            completedCount = 0;
            
            document.querySelectorAll('.control-card').forEach((card, index) => {
                const checkbox = card.querySelector('input[type="checkbox"]');
                const radios = card.querySelectorAll('input[type="radio"]');
                const isComplete = checkbox?.checked && Array.from(radios).some(r => r.checked);
                
                if (isComplete) {
                    completedCount++;
                    card.classList.add('completed');
                } else {
                    card.classList.remove('completed');
                }
            });
            
            document.getElementById('completedCount').textContent = completedCount;
            const percentage = (completedCount / totalControls) * 100;
            document.getElementById('progressBar').style.width = percentage + '%';
        }

        function showPreview(input, index) {
            const preview = document.getElementById('preview' + index);
            preview.innerHTML = '';
            
            if (input.files) {
                Array.from(input.files).forEach(file => {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        preview.appendChild(img);
                    }
                    reader.readAsDataURL(file);
                });
            }
        }

        function showGenelPreview(input) {
            const preview = document.getElementById('genelPreview');
            preview.innerHTML = '';
            
            if (input.files) {
                Array.from(input.files).forEach(file => {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        preview.appendChild(img);
                    }
                    reader.readAsDataURL(file);
                });
            }
        }

        function handleSubmit() {
            const personel = document.getElementById('personel_id');
            
            if (!personel.value) {
                alert('⚠️ Lütfen adınızı seçin!');
                personel.focus();
                personel.scrollIntoView({ behavior: 'smooth', block: 'center' });
                return;
            }
            
            if (completedCount === 0) {
                alert('⚠️ En az bir kontrol yapmalısınız!');
                return;
            }
            
            // Yarım bırakma uyarısı
            if (completedCount < totalControls) {
                const kalanKontrol = totalControls - completedCount;
                if (!confirm(`${kalanKontrol} kontrol tamamlanmadı.\n\nYarım bırakıp daha sonra devam etmek ister misiniz?`)) {
                    return;
                }
            }
            
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i> Kaydediliyor...';
            
            document.getElementById('kontrolForm').submit();
        }
    </script>
</body>
</html>
