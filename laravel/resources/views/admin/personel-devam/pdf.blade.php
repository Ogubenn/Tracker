<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personel Devam Çizelgesi - {{ $tarih->translatedFormat('F Y') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9px;
            padding: 15px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        
        .header h1 {
            font-size: 16px;
            margin-bottom: 5px;
        }
        
        .header h2 {
            font-size: 14px;
            color: #666;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        th, td {
            border: 1px solid #333;
            padding: 4px 2px;
            text-align: center;
        }
        
        thead th {
            background-color: #333;
            color: white;
            font-weight: bold;
            font-size: 8px;
        }
        
        tbody td:first-child {
            text-align: left;
            font-weight: bold;
            padding-left: 5px;
            width: 120px;
        }
        
        .weekend {
            background-color: #f0f0f0;
        }
        
        .summary-col {
            background-color: #e8e8e8;
            font-weight: bold;
        }
        
        .calisma {
            color: #28a745;
            font-weight: bold;
        }
        
        .izinli {
            color: #17a2b8;
            font-weight: bold;
        }
        
        .raporlu {
            color: #ffc107;
            font-weight: bold;
        }
        
        .gelmedi {
            color: #dc3545;
            font-weight: bold;
        }
        
        .legend {
            margin-top: 15px;
            font-size: 8px;
            page-break-inside: avoid;
        }
        
        .legend h4 {
            font-size: 10px;
            margin-bottom: 5px;
        }
        
        .legend-item {
            display: inline-block;
            margin-right: 15px;
            margin-bottom: 3px;
        }
        
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 8px;
            color: #666;
            border-top: 1px solid #ccc;
            padding-top: 10px;
        }
        
        @page {
            margin: 10mm;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>PERSONEL DEVAM ÇİZELGESİ</h1>
        <h2>{{ $tarih->translatedFormat('F Y') }}</h2>
    </div>
    
    <table>
        <thead>
            <tr>
                <th rowspan="2" style="vertical-align: middle; width: 120px;">PERSONEL ADI</th>
                <th colspan="{{ count($gunler) }}">GÜNLER</th>
                <th colspan="4">ÖZET</th>
            </tr>
            <tr>
                @foreach($gunler as $gun)
                    <th style="width: 22px;">
                        {{ $gun->format('d') }}<br>
                        {{ substr($gun->translatedFormat('D'), 0, 2) }}
                    </th>
                @endforeach
                <th style="width: 30px;">Ç</th>
                <th style="width: 30px;">İ</th>
                <th style="width: 30px;">R</th>
                <th style="width: 30px;">G</th>
            </tr>
        </thead>
        <tbody>
            @foreach($personeller as $personel)
                <tr>
                    <td>{{ $personel->ad }}</td>
                    
                    @foreach($gunler as $gun)
                        @php
                            $key = $personel->id . '_' . $gun->format('Y-m-d');
                            $kayit = $devamKayitlari[$key] ?? null;
                            $haftaSonu = in_array($gun->dayOfWeek, [0, 6]);
                        @endphp
                        
                        <td class="{{ $haftaSonu ? 'weekend' : '' }}">
                            @if($kayit)
                                @if($kayit->durum == 'calisma')
                                    <span class="calisma">
                                        @if($kayit->giris_yapti && $kayit->cikis_yapti)
                                            ●
                                        @elseif($kayit->giris_yapti)
                                            →
                                        @elseif($kayit->cikis_yapti)
                                            ←
                                        @else
                                            -
                                        @endif
                                    </span>
                                @elseif($kayit->durum == 'izinli')
                                    <span class="izinli">İ</span>
                                @elseif($kayit->durum == 'raporlu')
                                    <span class="raporlu">R</span>
                                @elseif($kayit->durum == 'gelmedi')
                                    <span class="gelmedi">G</span>
                                @endif
                            @else
                                -
                            @endif
                        </td>
                    @endforeach
                    
                    <td class="summary-col">{{ $istatistikler[$personel->id]['calisma'] }}</td>
                    <td class="summary-col">{{ $istatistikler[$personel->id]['izin'] }}</td>
                    <td class="summary-col">{{ $istatistikler[$personel->id]['rapor'] }}</td>
                    <td class="summary-col">{{ $istatistikler[$personel->id]['gelmedi'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="legend">
        <h4>AÇIKLAMALAR:</h4>
        <div>
            <span class="legend-item"><strong>●</strong> Giriş ve Çıkış yapıldı</span>
            <span class="legend-item"><strong>→</strong> Sadece Giriş yapıldı</span>
            <span class="legend-item"><strong>←</strong> Sadece Çıkış yapıldı</span>
            <span class="legend-item"><strong>İ</strong> İzinli</span>
            <span class="legend-item"><strong>R</strong> Raporlu</span>
            <span class="legend-item"><strong>G</strong> Gelmedi</span>
        </div>
        <div style="margin-top: 5px;">
            <span class="legend-item"><strong>Ç:</strong> Çalışma Günü</span>
            <span class="legend-item"><strong>İ:</strong> İzin</span>
            <span class="legend-item"><strong>R:</strong> Rapor</span>
            <span class="legend-item"><strong>G:</strong> Gelmedi</span>
        </div>
    </div>
    
    <div class="footer">
        <p>Yazdırma Tarihi: {{ now()->translatedFormat('d F Y H:i') }}</p>
        <p>Bu belge elektronik ortamda oluşturulmuştur.</p>
    </div>
</body>
</html>
