<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Kontrol - {{ $bina->bina_adi }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #667eea;
            --primary-dark: #5568d3;
            --success: #10B981;
            --warning: #F59E0B;
            --danger: #EF4444;
            --gray-50: #F9FAFB;
            --gray-100: #F3F4F6;
            --gray-200: #E5E7EB;
            --gray-300: #D1D5DB;
            --gray-600: #6B7280;
            --gray-700: #374151;
            --gray-900: #111827;
            --bg-main: #F0F4F8;
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg-main);
            min-height: 100vh;
            padding: 0 0 100px 0;
            margin: 0;
        }
        
        .container-wrapper {
            max-width: 480px;
            margin: 0 auto;
            padding: 1rem;
        }
        
        .header-card {
            background: white;
            padding: 1.5rem;
            border-radius: 16px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            margin-bottom: 1rem;
        }
        
        .header-card h1 {
            font-size: 1.375rem;
            font-weight: 800;
            color: var(--gray-900);
            margin: 0 0 0.5rem 0;
        }
        
        .header-card .info {
            color: var(--gray-600);
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .progress-card {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            padding: 1.25rem;
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
            margin-bottom: 1rem;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .progress-text {
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            opacity: 0.95;
        }
        
        .progress-bar-wrapper {
            background: rgba(255,255,255,0.2);
            height: 8px;
            border-radius: 10px;
            overflow: hidden;
        }
        
        .progress-bar-fill {
            background: white;
            height: 100%;
            border-radius: 10px;
            transition: width 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
        }
        
        @keyframes progressPulse {
            0%, 100% { box-shadow: 0 0 10px rgba(255, 255, 255, 0.5); }
            50% { box-shadow: 0 0 20px rgba(255, 255, 255, 0.8); }
        }
        
        .progress-bar-fill {
            animation: progressPulse 2s ease-in-out infinite;
        }
        
        .personel-card {
            background: white;
            padding: 1.25rem;
            border-radius: 16px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            margin-bottom: 1rem;
        }
        
        .personel-card label {
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 0.75rem;
            display: block;
            font-size: 1rem;
        }
        
        .personel-card select {
            width: 100%;
            min-height: 52px;
            padding: 0 1rem;
            border: 2px solid var(--gray-200);
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            background: white;
            color: var(--gray-900);
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 16 16'%3E%3Cpath fill='%236B7280' d='M13.707 6.293L8 11.586 2.293 6.293A1 1 0 00.879 7.707l6 6a1 1 0 001.414 0l6-6a1 1 0 10-1.414-1.414z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            padding-right: 3rem;
        }
        
        .personel-card select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .kontrol-accordion {
            background: white;
            border-radius: 16px;
            margin-bottom: 0.75rem;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .kontrol-accordion.completed {
            border: 3px solid var(--success);
            background: linear-gradient(to right, #D1FAE5 0%, white 20%);
            box-shadow: 0 4px 16px rgba(16, 185, 129, 0.25);
            transform: scale(1.01);
        }
        
        @keyframes completePulse {
            0% { box-shadow: 0 4px 16px rgba(16, 185, 129, 0.25); }
            50% { box-shadow: 0 4px 24px rgba(16, 185, 129, 0.45); }
            100% { box-shadow: 0 4px 16px rgba(16, 185, 129, 0.25); }
        }
        
        .kontrol-accordion.completed {
            animation: completePulse 1s ease-in-out;
        }
        
        .accordion-header {
            padding: 1.25rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: background 0.2s;
        }
        
        .accordion-header:hover {
            background: var(--gray-50);
        }
        
        .accordion-header.active {
            background: var(--gray-50);
        }
        
        .accordion-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            flex-shrink: 0;
        }
        
        .accordion-content-wrapper {
            flex: 1;
            min-width: 0;
        }
        
        .accordion-title {
            font-size: 1.0625rem;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 0.25rem;
            line-height: 1.3;
        }
        
        .accordion-meta {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .badge-small {
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.25rem 0.625rem;
            border-radius: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .badge-gunluk { background: #DBEAFE; color: #1E40AF; }
        .badge-haftalik { background: #D1FAE5; color: #065F46; }
        .badge-aylik { background: #FEF3C7; color: #92400E; }
        .badge-_15_gun { background: #FED7AA; color: #9A3412; }
        
        .accordion-arrow {
            font-size: 1.25rem;
            color: var(--gray-600);
            transition: transform 0.3s;
            flex-shrink: 0;
        }
        
        .accordion-arrow.open {
            transform: rotate(180deg);
        }
        
        .accordion-body {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }
        
        .accordion-body.open {
            max-height: 1000px;
        }
        
        .accordion-content {
            padding: 0 1.25rem 1.25rem 1.25rem;
        }
        
        .input-group {
            margin-bottom: 1.25rem;
        }
        
        .input-group label {
            font-weight: 700;
            font-size: 0.9375rem;
            color: var(--gray-900);
            margin-bottom: 0.5rem;
            display: block;
        }
        
        .checkbox-group {
            margin-bottom: 1.25rem;
            padding: 1rem;
            background: var(--gray-50);
            border-radius: 12px;
            border: 2px solid var(--gray-200);
            transition: all 0.3s ease;
        }
        
        .checkbox-group.checked {
            background: #D1FAE5;
            border-color: var(--success);
            box-shadow: 0 2px 12px rgba(16, 185, 129, 0.2);
        }
        
        .checkbox-group label {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-weight: 700;
            font-size: 1.0625rem;
            color: var(--gray-900);
            cursor: pointer;
            user-select: none;
            transition: all 0.2s;
        }
        
        .checkbox-group.checked label {
            color: #065F46;
        }
        
        .checkbox-group input[type="checkbox"] {
            width: 28px;
            height: 28px;
            cursor: pointer;
            flex-shrink: 0;
            accent-color: var(--success);
            transition: transform 0.2s;
        }
        
        .checkbox-group input[type="checkbox"]:checked {
            transform: scale(1.1);
        }
        
        .input-group input[type="text"],
        .input-group input[type="number"],
        .input-group textarea {
            width: 100%;
            min-height: 52px;
            padding: 0 1rem;
            border: 2px solid var(--gray-200);
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 500;
            transition: all 0.2s;
            background: var(--gray-50);
        }
        
        .input-group textarea {
            min-height: 100px;
            padding: 1rem;
            resize: vertical;
        }
        
        .input-group input:focus,
        .input-group textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            background: white;
        }
        
        .durum-group {
            margin: 1.5rem 0;
        }
        
        .durum-label {
            font-weight: 800;
            font-size: 1rem;
            color: var(--gray-900);
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .durum-required {
            color: var(--danger);
            font-size: 1.125rem;
        }
        
        .durum-segmented {
            display: flex;
            gap: 0.5rem;
            padding: 4px;
            background: var(--gray-100);
            border-radius: 12px;
        }
        
        .durum-option {
            flex: 1;
        }
        
        .durum-option input[type="radio"] {
            display: none;
        }
        
        .durum-option label {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.375rem;
            padding: 0.875rem 0.5rem;
            border-radius: 10px;
            font-size: 0.875rem;
            font-weight: 700;
            background: transparent;
            color: var(--gray-600);
            cursor: pointer;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            text-align: center;
            border: 2px solid transparent;
        }
        
        .durum-option label:hover {
            background: rgba(255, 255, 255, 0.5);
            transform: translateY(-2px);
        }
        
        .durum-option label:active {
            transform: scale(0.95);
        }
        
        .durum-option label i {
            font-size: 1.75rem;
            transition: transform 0.2s;
        }
        
        .durum-option input[type="radio"]:checked + label i {
            transform: scale(1.2);
        }
        
        .durum-option input[type="radio"]:checked + label {
            background: white;
            box-shadow: 0 4px 16px rgba(0,0,0,0.15);
            transform: scale(1.05);
        }
        
        .durum-option input[type="radio"]:checked + label.success {
            color: var(--success);
            border-color: var(--success);
            background: #ECFDF5;
        }
        
        .durum-option input[type="radio"]:checked + label.warning {
            color: var(--warning);
            border-color: var(--warning);
            background: #FFFBEB;
        }
        
        .durum-option input[type="radio"]:checked + label.danger {
            color: var(--danger);
            border-color: var(--danger);
            background: #FEF2F2;
        }
        
        .genel-card {
            background: white;
            padding: 1.5rem;
            border-radius: 16px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            margin-bottom: 1rem;
        }
        
        .genel-card h3 {
            font-size: 1.125rem;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .file-upload-btn {
            width: 100%;
            min-height: 120px;
            border: 2px dashed var(--gray-300);
            border-radius: 12px;
            background: var(--gray-50);
            cursor: pointer;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: all 0.2s;
            padding: 1rem;
        }
        
        .file-upload-btn:hover {
            border-color: var(--primary);
            background: white;
        }
        
        .file-upload-btn i {
            font-size: 2.5rem;
            color: var(--primary);
        }
        
        .file-upload-btn input[type="file"] {
            display: none;
        }
        
        .file-preview {
            margin-top: 0.75rem;
            font-size: 0.875rem;
            color: var(--success);
            font-weight: 600;
        }
        
        .fixed-bottom-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            padding: 1rem;
            box-shadow: 0 -4px 12px rgba(0,0,0,0.1);
            z-index: 1000;
        }
        
        .fixed-bottom-bar .container-wrapper {
            padding: 0;
        }
        
        .warning-text {
            font-size: 0.875rem;
            color: var(--danger);
            font-weight: 600;
            margin-bottom: 0.75rem;
            display: none;
            align-items: center;
            gap: 0.5rem;
        }
        
        .warning-text.show {
            display: flex;
        }
        
        .submit-btn {
            width: 100%;
            min-height: 56px;
            background: linear-gradient(135deg, var(--success), #059669);
            color: white;
            border: none;
            border-radius: 14px;
            font-size: 1.125rem;
            font-weight: 800;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
            transition: all 0.3s;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .submit-btn:hover:not(:disabled) {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 8px 24px rgba(16, 185, 129, 0.5);
        }
        
        .submit-btn:active:not(:disabled) {
            transform: translateY(-1px) scale(0.98);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
        }
        
        .submit-btn:disabled {
            background: var(--gray-300);
            cursor: not-allowed;
            box-shadow: none;
        }
        
        .alert {
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .alert-success {
            background: #D1FAE5;
            border: 2px solid var(--success);
            color: #065F46;
        }
        
        .empty-state {
            text-align: center;
            padding: 3rem 1.5rem;
            background: white;
            border-radius: 16px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        
        .empty-state i {
            font-size: 4rem;
            color: var(--success);
            margin-bottom: 1rem;
        }
        
        .empty-state h3 {
            color: var(--gray-900);
            font-weight: 800;
            font-size: 1.375rem;
            margin-bottom: 0.5rem;
        }
        
        .empty-state p {
            color: var(--gray-600);
            font-size: 1rem;
        }
    </style>
</head>
<body>
    <div class="container-wrapper">
        <div class="header-card">
            <h1><i class="bi bi-building-fill me-2" style="color: var(--primary);"></i>{{ $bina->bina_adi }}</h1>
            <div class="info">
                <i class="bi bi-calendar3"></i>
                <span>{{ $bugun->translatedFormat('d F Y') }}</span>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                <i class="bi bi-check-circle-fill"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($kontrolMaddeleri->isEmpty())
            <div class="empty-state">
                <i class="bi bi-check-circle-fill"></i>
                <h3>🎉 Tüm Kontroller Tamamlandı!</h3>
                <p>Bu bina için bugün yapılması gereken kontrol bulunmuyor.</p>
            </div>
        @else
            <form action="{{ route('public.kontrol.store', $bina->uuid) }}" method="POST" enctype="multipart/form-data" id="kontrolForm">
                @csrf
                
                <div class="progress-card">
                    <div class="progress-text">
                        <span id="completedCount">0</span> / {{ $kontrolMaddeleri->count() }} Kontrol Tamamlandı
                    </div>
                    <div class="progress-bar-wrapper">
                        <div class="progress-bar-fill" id="progressBar" style="width: 0%"></div>
                    </div>
                </div>
                
                <div class="personel-card">
                    <label for="personel_id">
                        <i class="bi bi-person-fill me-2" style="color: var(--primary);"></i>
                        Kim Kontrol Yapıyor? <span class="durum-required">*</span>
                    </label>
                    <select name="personel_id" id="personel_id" required>
                        <option value="">👤 Adınızı Seçin</option>
                        @foreach($personeller as $personel)
                            <option value="{{ $personel->id }}">
                                {{ $personel->ad }} 
                                @if($personel->rol === 'admin')
                                    (Yönetici)
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                @foreach($kontrolMaddeleri as $index => $madde)
                    <div class="kontrol-accordion" data-index="{{ $index }}">
                        <div class="accordion-header">
                            <div class="accordion-icon">
                                @if($madde->kontrol_tipi === 'checkbox')
                                    <i class="bi bi-eye"></i>
                                @elseif($madde->kontrol_tipi === 'sayi')
                                    <i class="bi bi-thermometer-half"></i>
                                @else
                                    <i class="bi bi-pencil"></i>
                                @endif
                            </div>
                            <div class="accordion-content-wrapper">
                                <div class="accordion-title">{{ $madde->kontrol_adi }}</div>
                                <div class="accordion-meta">
                                    <span class="badge-small badge-{{ str_replace('_', '_', $madde->periyot) }}">
                                        @if($madde->periyot === 'gunluk')
                                            Günlük
                                        @elseif($madde->periyot === 'haftalik')
                                            Haftalık
                                        @elseif($madde->periyot === '15_gun')
                                            15 Günde Bir
                                        @elseif($madde->periyot === 'aylik')
                                            Aylık
                                        @else
                                            {{ $madde->periyot }}
                                        @endif
                                    </span>
                                </div>
                            </div>
                            <i class="bi bi-chevron-down accordion-arrow open" id="arrow{{ $index }}"></i>
                        </div>
                        
                        <div class="accordion-body open" id="body{{ $index }}">
                            <div class="accordion-content">
                                <input type="hidden" name="kayitlar[{{ $index }}][kontrol_maddesi_id]" value="{{ $madde->id }}">
                                
                                <div class="checkbox-group" id="checkboxGroup{{ $index }}">
                                    <label>
                                        <input type="checkbox" 
                                               id="yapildi_{{ $madde->id }}" 
                                               onchange="updateProgress(); toggleCheckboxStyle({{ $index }})">
                                        <span><i class="bi bi-check-circle me-1"></i> Kontrol Yapıldı</span>
                                    </label>
                                </div>
                                
                                @if($madde->kontrol_tipi === 'sayi')
                                    <div class="input-group">
                                        <label><i class="bi bi-123 me-1"></i> Ölçüm Değeri</label>
                                        <input type="number" step="0.01" 
                                               name="kayitlar[{{ $index }}][girilen_deger]" 
                                               placeholder="Sayısal değer girin (örn: 25.5)">
                                    </div>
                                @elseif($madde->kontrol_tipi === 'metin')
                                    <div class="input-group">
                                        <label><i class="bi bi-pencil me-1"></i> Kontrol Sonucu</label>
                                        <input type="text" 
                                               name="kayitlar[{{ $index }}][girilen_deger]" 
                                               placeholder="Gözleminizi buraya yazın">
                                    </div>
                                @endif
                                
                                <div class="durum-group">
                                    <div class="durum-label">
                                        <i class="bi bi-clipboard-check"></i>
                                        <span>Durum Değerlendirmesi</span>
                                        <span class="durum-required">*</span>
                                    </div>
                                    <div class="durum-segmented">
                                        <div class="durum-option">
                                            <input type="radio" id="uygun_{{ $madde->id }}" 
                                                   name="kayitlar[{{ $index }}][durum]" value="uygun" 
                                                   required onchange="updateProgress()">
                                            <label for="uygun_{{ $madde->id }}" class="success">
                                                <i class="bi bi-check-circle-fill"></i>
                                                <span>Uygun</span>
                                            </label>
                                        </div>
                                        <div class="durum-option">
                                            <input type="radio" id="duzeltme_{{ $madde->id }}" 
                                                   name="kayitlar[{{ $index }}][durum]" value="duzeltme_gerekli"
                                                   onchange="updateProgress()">
                                            <label for="duzeltme_{{ $madde->id }}" class="warning">
                                                <i class="bi bi-exclamation-triangle-fill"></i>
                                                <span>Düzeltme</span>
                                            </label>
                                        </div>
                                        <div class="durum-option">
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
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="genel-card">
                    <h3><i class="bi bi-chat-left-text"></i> Genel Not</h3>
                    <div class="input-group">
                        <label>Açıklama <span style="font-weight: 400; color: var(--gray-600); font-size: 0.875rem;">(İsteğe bağlı)</span></label>
                        <textarea name="genel_aciklama" 
                                  placeholder="Varsa genel gözlem veya notlarınızı buraya yazın..."></textarea>
                    </div>
                    
                    <label style="display: block; font-weight: 700; margin-bottom: 0.75rem;">
                        <i class="bi bi-camera me-1"></i> Fotoğraf Ekle
                    </label>
                    <div class="file-upload-btn" onclick="document.getElementById('genel_dosyalar').click();">
                        <i class="bi bi-camera-fill"></i>
                        <div style="font-weight: 600; color: var(--gray-700);">Kameradan Çek</div>
                        <small style="color: var(--gray-600);">veya galeriden seç</small>
                        <input type="file" 
                               id="genel_dosyalar"
                               name="genel_dosyalar[]" 
                               accept="image/*" 
                               multiple 
                               capture="environment"
                               onchange="showFilePreview(this)">
                    </div>
                    <div class="file-preview" id="filePreview"></div>
                </div>
            </form>
        @endif
    </div>

    @if(!$kontrolMaddeleri->isEmpty())
        <div class="fixed-bottom-bar">
            <div class="container-wrapper">
                <div class="warning-text" id="warningText">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <span>Lütfen tüm kontrolleri tamamlayın</span>
                </div>
                <button type="button" class="submit-btn" id="submitBtn" onclick="validateAndSubmit()">
                    <i class="bi bi-check-circle-fill me-2"></i> Kaydet
                </button>
            </div>
        </div>
    @endif

    <script>
        let completedCount = 0;
        const totalControls = {{ $kontrolMaddeleri->count() }};
        
        function updateProgress() {
            completedCount = 0;
            document.querySelectorAll('.kontrol-accordion').forEach((accordion, index) => {
                const checkbox = accordion.querySelector('input[type="checkbox"]');
                const radios = accordion.querySelectorAll('input[type="radio"]');
                const isChecked = checkbox?.checked && Array.from(radios).some(r => r.checked);
                if (isChecked) {
                    completedCount++;
                    accordion.classList.add('completed');
                } else {
                    accordion.classList.remove('completed');
                }
            });
            
            document.getElementById('completedCount').textContent = completedCount;
            const percentage = (completedCount / totalControls) * 100;
            document.getElementById('progressBar').style.width = percentage + '%';
        }
        
        function toggleCheckboxStyle(index) {
            const checkboxGroup = document.getElementById('checkboxGroup' + index);
            const checkbox = checkboxGroup.querySelector('input[type="checkbox"]');
            if (checkbox.checked) {
                checkboxGroup.classList.add('checked');
            } else {
                checkboxGroup.classList.remove('checked');
            }
        }
        
        function showFilePreview(input) {
            const preview = document.getElementById('filePreview');
            if (input.files.length > 0) {
                preview.innerHTML = '<i class="bi bi-check-circle-fill me-2"></i>' + input.files.length + ' fotoğraf seçildi';
            } else {
                preview.innerHTML = '';
            }
        }
        
        function validateAndSubmit() {
            const personelSelect = document.getElementById('personel_id');
            const warningText = document.getElementById('warningText');
            
            if (!personelSelect.value) {
                alert('⚠️ Lütfen adınızı seçin!');
                personelSelect.focus();
                return;
            }
            
            if (completedCount < totalControls) {
                warningText.classList.add('show');
                setTimeout(() => {
                    warningText.classList.remove('show');
                }, 3000);
                return;
            }
            
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i> Kaydediliyor...';
            
            document.getElementById('kontrolForm').submit();
        }
    </script>
</body>
</html>
