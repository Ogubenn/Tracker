# 🔐 Şifre Sıfırlama Sistemi - Kullanım Kılavuzu

## ✅ Kurulum Tamamlandı!

Şifre sıfırlama özelliği başarıyla eklendi. Sistem şu bileşenleri içeriyor:

### 📦 Eklenen Dosyalar

1. **Controller:** `app/Http/Controllers/PasswordResetController.php`
   - 60 dakika token geçerlilik süresi
   - Email validasyonu
   - Aktif kullanıcı kontrolü
   - Güvenli token hash sistemi

2. **Notification:** `app/Notifications/ResetPasswordNotification.php`
   - Türkçe email şablonu
   - Otomatik kuyruk desteği (ShouldQueue)
   - Kullanıcı adı personalizasyonu

3. **Views:**
   - `resources/views/auth/forgot-password.blade.php` (Email giriş formu)
   - `resources/views/auth/reset-password.blade.php` (Yeni şifre formu)

4. **Routes:** 4 yeni route eklendi
   - `GET /forgot-password` → Email formu
   - `POST /forgot-password` → Email gönder
   - `GET /reset-password/{token}` → Şifre sıfırlama formu
   - `POST /reset-password` → Şifreyi güncelle

5. **Login Sayfası:** "Şifremi Unuttum" linki eklendi

---

## 🧪 Test Etme (Development)

### 1. Mailpit'i Başlat (Laragon)
```
Laragon → Menu → Mailpit → Start
```
Mailpit arayüzü: http://localhost:8025

### 2. Şifre Sıfırlama Akışı
1. Login sayfasına git: http://atiksu_takip.test/login
2. "Şifremi Unuttum" linkine tıkla
3. Geçerli bir email adresi gir (örn: admin@test.com)
4. Mailpit'te emaili kontrol et: http://localhost:8025
5. "Şifremi Sıfırla" butonuna tıkla
6. Yeni şifre oluştur (min 8 karakter)
7. Yeni şifre ile login ol

### 3. Test Komutları
```powershell
# Cache temizle
c:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe artisan cache:clear

# Route'ları kontrol et
c:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe artisan route:list --name=password

# Veritabanında token kontrolü
# password_reset_tokens tablosunda email ve token hash'ini görebilirsin
```

---

## 🚀 Production Ayarları

### Email Provider Seçenekleri

#### **Seçenek 1: Gmail**
`.env` dosyasını düzenle:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password  # Google App Password oluştur!
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="Atıksu Takip Sistemi"
```

**Gmail App Password nasıl oluşturulur:**
1. Google Hesabı → Güvenlik
2. 2 Adımlı Doğrulama'yı aç
3. "Uygulama şifreleri" → "Posta" seç
4. 16 haneli şifreyi `.env` dosyasına ekle

#### **Seçenek 2: SendGrid (Önerilen)**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your-sendgrid-api-key
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="Atıksu Takip Sistemi"
```

#### **Seçenek 3: AWS SES**
```env
MAIL_MAILER=ses
AWS_ACCESS_KEY_ID=your-access-key
AWS_SECRET_ACCESS_KEY=your-secret-key
AWS_DEFAULT_REGION=eu-west-1
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="Atıksu Takip Sistemi"
```

### Güvenlik Kontrol Listesi
- ✅ Token geçerlilik süresi: 60 dakika
- ✅ Token hash ile saklanıyor
- ✅ Email validasyonu yapılıyor
- ✅ Aktif kullanıcı kontrolü var
- ✅ Rate limiting (Login'de zaten var: 5/5dk)
- ✅ CSRF koruması aktif

---

## 🎨 Özellikler

### Kullanıcı Deneyimi
- ✨ Modern, responsive tasarım
- 🎨 Gradient background
- 👁️ Şifre göster/gizle toggle
- ✅ Gerçek zamanlı validasyon
- 📱 Mobil uyumlu
- 🌐 Türkçe arayüz

### Güvenlik
- 🔒 Hashed token storage
- ⏱️ Token expiry (60dk)
- 🛡️ CSRF protection
- 🔐 Password confirmation
- 🚫 Inactive user blocking

### Email Şablonu
- 📧 Profesyonel görünüm
- 🎯 Tek tıkla sıfırlama
- ⏰ Geçerlilik süresi bildirimi
- 🇹🇷 Türkçe içerik

---

## 🐛 Sorun Giderme

### Email Gönderilmiyor
1. **Mailpit çalışıyor mu?** → http://localhost:8025
2. **Queue çalışıyor mu?** → `.env` dosyasında `QUEUE_CONNECTION=sync`
3. **Cache temizle:** `php artisan config:clear`

### Token Geçersiz Hatası
- Token 60 dakika sonra geçersiz olur
- Her yeni istek eski token'ı iptal eder
- Tarayıcı cache'ini temizle

### Email Adresi Bulunamadı
- Kullanıcı `users` tablosunda mevcut olmalı
- Email adresi doğru yazılmalı
- Kullanıcı aktif olmalı (`aktif_mi = 1`)

---

## 📊 Veritabanı

`password_reset_tokens` tablosu:
```sql
CREATE TABLE password_reset_tokens (
    email VARCHAR(255) PRIMARY KEY,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL
);
```

Token temizleme (opsiyonel cron job):
```powershell
# Eski token'ları temizle (60 dakikadan eski)
php artisan tinker
>>> DB::table('password_reset_tokens')->where('created_at', '<', now()->subHour())->delete();
```

---

## 📝 Sonraki Adımlar

✅ **Tamamlandı:** Şifre Sıfırlama Sistemi

🔜 **Sırada:**
2. Email Bildirimleri (Kontrol sonuçları)
3. PDF Rapor Export

---

## 💡 İpuçları

- **Development:** Mailpit kullan (kolay test)
- **Production:** SendGrid kullan (güvenilir, ücretsiz plan)
- **Email template'i özelleştir:** `app/Notifications/ResetPasswordNotification.php`
- **Token süresini değiştir:** `PasswordResetController::TOKEN_EXPIRY_MINUTES`

---

**Hazırlayan:** GitHub Copilot  
**Tarih:** 13 Ocak 2026  
**Versiyon:** 1.0
