<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bugünkü İşleriniz</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px;
        }
        .greeting {
            font-size: 16px;
            margin-bottom: 20px;
        }
        .is-list {
            margin: 20px 0;
        }
        .is-item {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 4px;
        }
        .is-item.gece {
            border-left-color: #764ba2;
        }
        .is-baslik {
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 5px;
        }
        .is-detay {
            font-size: 14px;
            color: #666;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
            margin-right: 5px;
        }
        .badge-normal {
            background: #e7f3ff;
            color: #0066cc;
        }
        .badge-gece {
            background: #f3e7ff;
            color: #764ba2;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        .ozet {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📋 Bugünkü İşleriniz</h1>
            <p>{{ $tarih }}</p>
        </div>
        
        <div class="content">
            <div class="greeting">
                Merhaba <strong>{{ $kullanici->ad }}</strong>,
            </div>
            
            <div class="ozet">
                <strong>{{ count($isler) }} iş</strong> bugün tamamlanmayı bekliyor.
            </div>
            
            <div class="is-list">
                @foreach($isler as $is)
                    <div class="is-item {{ $is->renk_kategori }}">
                        <div class="is-baslik">
                            {{ $is->baslik }}
                        </div>
                        <div class="is-detay">
                            <span class="badge badge-{{ $is->renk_kategori }}">
                                {{ $is->renk_kategori === 'gece' ? 'Gece Çalışanları' : 'Normal İş' }}
                            </span>
                            
                            @if($is->tekrarli_mi)
                                <span class="badge" style="background: #e7ffe7; color: #00aa00;">
                                    🔄 Tekrarlı İş
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            
            <p style="text-align: center; margin-top: 30px;">
                <a href="{{ url('/admin/is-takvimi') }}" 
                   style="display: inline-block; background: #667eea; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px;">
                    📅 Takvimi Görüntüle
                </a>
            </p>
        </div>
        
        <div class="footer">
            <p>Bu bir otomatik mail'dir. Lütfen yanıtlamayınız.</p>
            <p>&copy; {{ date('Y') }} Atıksu Takip Sistemi</p>
        </div>
    </div>
</body>
</html>
