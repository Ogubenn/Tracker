<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontrol Raporu - {{ $tarihAralik }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            padding: 20px;
        }

        .header {
            display: table;
            width: 100%;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 3px solid #667eea;
        }

        .header .logo-section {
            display: table-cell;
            width: 80px;
            vertical-align: middle;
            padding-right: 15px;
        }

        .header .logo-box {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 24px;
        }

        .header .title-section {
            display: table-cell;
            vertical-align: middle;
        }

        .header h1 {
            font-size: 22px;
            color: #667eea;
            margin-bottom: 3px;
        }

        .header h2 {
            font-size: 16px;
            color: #333;
            font-weight: normal;
        }

        .info-section {
            background: #f8f9fa;
            padding: 15px;
            margin-bottom: 25px;
            border-radius: 5px;
            border-left: 4px solid #667eea;
        }

        .info-section .info-row {
            margin-bottom: 8px;
        }

        .info-section strong {
            color: #667eea;
            display: inline-block;
            min-width: 130px;
        }

        .bina-section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }

        .bina-header {
            background: #667eea;
            color: white;
            padding: 12px 15px;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 15px;
            border-radius: 5px;
        }

        .kontrol-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .kontrol-table thead {
            background: #f1f3f5;
        }

        .kontrol-table th {
            padding: 10px 8px;
            text-align: left;
            font-weight: bold;
            border-bottom: 2px solid #dee2e6;
            color: #495057;
        }

        .kontrol-table td {
            padding: 10px 8px;
            border-bottom: 1px solid #dee2e6;
        }

        .kontrol-table tbody tr:hover {
            background: #f8f9fa;
        }

        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
        }

        .badge-success {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-warning {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-danger {
            background: #fee2e2;
            color: #991b1b;
        }

        .no-data {
            text-align: center;
            padding: 30px;
            color: #6c757d;
            font-style: italic;
        }

        .footer {
            margin-top: 40px;
            padding-top: 15px;
            border-top: 2px solid #dee2e6;
            text-align: center;
            font-size: 10px;
            color: #6c757d;
        }

        .summary-box {
            background: #e7f3ff;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            border-left: 4px solid #0d6efd;
        }

        .summary-box h3 {
            font-size: 14px;
            margin-bottom: 10px;
            color: #0d6efd;
        }

        .summary-item {
            display: inline-block;
            margin-right: 30px;
            padding: 5px 0;
        }

        .summary-item strong {
            color: #0d6efd;
        }

        @page {
            margin: 100px 50px 80px 50px;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo-section">
            <div class="logo-box">BB</div>
        </div>
        <div class="title-section">
            <h1>Bulancak Belediyesi</h1>
            <h2>Atıksu Takip Sistemi - Kontrol Raporu</h2>
        </div>
    </div>

    <div class="info-section">
        <div class="info-row">
            <strong>Rapor Tarihi:</strong> {{ $tarihAralik }}
        </div>
        <div class="info-row">
            <strong>Bina:</strong> {{ $secilenBina }}
        </div>
        <div class="info-row">
            <strong>Oluşturma Zamanı:</strong> {{ now()->format('d.m.Y H:i') }}
        </div>
    </div>

    @if($kayitlar && $kayitlar->isNotEmpty())
        @php
            $toplamKontrol = 0;
            $uygunSayisi = 0;
            $uygunDegilSayisi = 0;
        @endphp

        @foreach($kayitlar as $binaAdi => $binaKayitlari)
            @php
                $toplamKontrol += $binaKayitlari->count();
                $uygunSayisi += $binaKayitlari->where('durum', 'uygun')->count();
                $uygunDegilSayisi += $binaKayitlari->where('durum', 'uygun_degil')->count();
            @endphp

            <div class="bina-section">
                <div class="bina-header">
                    {{ $binaAdi }}
                </div>

                <table class="kontrol-table">
                    <thead>
                        <tr>
                            <th style="width: 35%;">Kontrol Maddesi</th>
                            <th style="width: 20%;">Girilen Değer</th>
                            <th style="width: 15%;">Durum</th>
                            <th style="width: 20%;">Yapan</th>
                            <th style="width: 10%;">Saat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($binaKayitlari as $kayit)
                            <tr>
                                <td>{{ $kayit->kontrolMaddesi->kontrol_adi }}</td>
                                <td>
                                    @if($kayit->kontrolMaddesi->kontrol_tipi == 'checkbox')
                                        @if($kayit->girilen_deger == '1' || $kayit->durum)
                                            <span class="badge badge-success">✓ Yapıldı</span>
                                        @else
                                            <span class="badge badge-danger">✗ Yapılmadı</span>
                                        @endif
                                    @elseif($kayit->kontrolMaddesi->kontrol_tipi == 'sayisal')
                                        {{ $kayit->girilen_deger ?? '-' }}
                                        @if($kayit->kontrolMaddesi->birim)
                                            <strong style="color: #0d6efd;">{{ $kayit->kontrolMaddesi->birim }}</strong>
                                        @endif
                                    @else
                                        {{ $kayit->girilen_deger ?? '-' }}
                                    @endif
                                    
                                    @if($kayit->baslangic_saati || $kayit->bitis_saati)
                                        <br>
                                        <small style="color: #6c757d;">
                                            @if($kayit->baslangic_saati)
                                                🕐 {{ \Carbon\Carbon::parse($kayit->baslangic_saati)->format('H:i') }}
                                            @endif
                                            @if($kayit->baslangic_saati && $kayit->bitis_saati)
                                                -
                                            @endif
                                            @if($kayit->bitis_saati)
                                                🕐 {{ \Carbon\Carbon::parse($kayit->bitis_saati)->format('H:i') }}
                                            @endif
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    @if($kayit->durum === 'uygun')
                                        <span class="badge badge-success">✓ Uygun</span>
                                    @elseif($kayit->durum === 'uygun_degil')
                                        <span class="badge badge-danger">✗ Uygun Değil</span>
                                    @else
                                        <span class="badge badge-warning">⚠ Düzeltme</span>
                                    @endif
                                </td>
                                <td>{{ $kayit->yapanKullanici->ad }}</td>
                                <td>{{ \Carbon\Carbon::parse($kayit->created_at)->format('H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach

        <div class="summary-box">
            <h3>📊 Özet Bilgiler</h3>
            <div class="summary-item">
                <strong>Toplam Kontrol:</strong> {{ $toplamKontrol }}
            </div>
            <div class="summary-item">
                <strong>Uygun:</strong> <span style="color: #065f46;">{{ $uygunSayisi }}</span>
            </div>
            <div class="summary-item">
                <strong>Uygun Değil:</strong> <span style="color: #991b1b;">{{ $uygunDegilSayisi }}</span>
            </div>
            <div class="summary-item">
                <strong>Başarı Oranı:</strong> 
                @php
                    $basariOrani = $toplamKontrol > 0 ? round(($uygunSayisi / $toplamKontrol) * 100) : 0;
                @endphp
                <span style="color: {{ $basariOrani >= 80 ? '#065f46' : '#92400e' }};">%{{ $basariOrani }}</span>
            </div>
        </div>
    @else
        <div class="no-data">
            <p>Seçilen tarih ve bina için kontrol kaydı bulunmamaktadır.</p>
        </div>
    @endif

    <div class="footer">
        <p>Bu rapor Bulancak Belediyesi Atıksu Takip Sistemi tarafından otomatik olarak oluşturulmuştur.</p>
        <p>{{ now()->format('d.m.Y H:i:s') }}</p>
    </div>
</body>
</html>
