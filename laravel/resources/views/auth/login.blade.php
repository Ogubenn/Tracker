<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap - Atıksu Takip Sistemi</title>
    
    <!-- PWA Manifest -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#667eea">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Atıksu Takip">
    <link rel="apple-touch-icon" href="{{ asset('images/icon-192.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('images/icon-192.png') }}">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #667eea;
            --primary-dark: #5568d3;
            --success: #10B981;
            --gray-50: #F9FAFB;
            --gray-100: #F3F4F6;
            --gray-600: #6B7280;
            --gray-700: #374151;
            --gray-900: #111827;
        }
        
        * { 
            margin: 0; 
            padding: 0; 
            box-sizing: border-box; 
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        
        .login-container {
            width: 100%;
            max-width: 440px;
        }
        
        .login-card {
            background: white;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 3rem 2.5rem;
            animation: slideUp 0.5s ease;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .logo-container {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .logo-container img {
            width: 120px;
            height: 120px;
            object-fit: contain;
            margin-bottom: 1rem;
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .logo-container h1 {
            font-size: 1.75rem;
            font-weight: 800;
            color: var(--gray-900);
            margin-bottom: 0.5rem;
        }
        
        .logo-container p {
            color: var(--gray-600);
            font-size: 0.9375rem;
            font-weight: 500;
        }
        
        .alert-modern {
            background: #FEF2F2;
            border: 2px solid #EF4444;
            color: #991B1B;
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            font-weight: 600;
            font-size: 0.875rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            display: block;
            font-weight: 700;
            font-size: 0.9375rem;
            color: var(--gray-900);
            margin-bottom: 0.5rem;
        }
        
        .form-input {
            width: 100%;
            min-height: 52px;
            padding: 0 1rem;
            border: 2px solid var(--gray-100);
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 500;
            transition: all 0.2s;
            background: var(--gray-50);
        }
        
        .form-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            background: white;
        }
        
        .form-input.is-invalid {
            border-color: #EF4444;
            background: #FEF2F2;
        }
        
        .invalid-feedback {
            display: block;
            color: #EF4444;
            font-size: 0.875rem;
            font-weight: 600;
            margin-top: 0.5rem;
        }
        
        .password-wrapper {
            position: relative;
        }
        
        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--gray-600);
            cursor: pointer;
            font-size: 1.25rem;
            padding: 0.25rem;
            transition: color 0.2s;
        }
        
        .password-toggle:hover {
            color: var(--primary);
        }
        
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }
        
        .checkbox-group input[type="checkbox"] {
            width: 20px;
            height: 20px;
            cursor: pointer;
            accent-color: var(--primary);
        }
        
        .checkbox-group label {
            font-weight: 600;
            font-size: 0.9375rem;
            color: var(--gray-700);
            cursor: pointer;
            user-select: none;
        }
        
        .btn-login {
            width: 100%;
            min-height: 56px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            border: none;
            border-radius: 14px;
            font-size: 1.125rem;
            font-weight: 800;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        .forgot-password {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
        }
        
        .forgot-password a {
            color: #667eea;
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
        }
        
        .forgot-password a:hover {
            color: #764ba2;
        }
        
        .footer-text {
            text-align: center;
            color: white;
            font-size: 0.875rem;
            font-weight: 500;
            margin-top: 2rem;
            opacity: 0.9;
        }
        
        @media (max-width: 576px) {
            .login-card {
                padding: 2rem 1.5rem;
            }
            
            .logo-container img {
                width: 100px;
                height: 100px;
            }
            
            .logo-container h1 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="logo-container">
                <img src="{{ asset('images/logo.jpg') }}" alt="Atıksu Takip Sistemi Logo">
                <h1>Atıksu Takip Sistemi</h1>
                <p><i class="bi bi-shield-lock me-1"></i> Güvenli Giriş</p>
            </div>

            @if ($errors->any())
                <div class="alert-modern">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    @foreach ($errors->all() as $error)
                        {{ $error }}
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="form-group">
                    <label for="email" class="form-label">
                        <i class="bi bi-envelope me-1"></i> E-posta Adresi
                    </label>
                    <input type="email" 
                           class="form-input @error('email') is-invalid @enderror" 
                           id="email" 
                           name="email" 
                           value="{{ old('email', request()->cookie('remember_email')) }}" 
                           placeholder="ornek@email.com"
                           required 
                           autofocus>
                    @error('email')
                        <span class="invalid-feedback">
                            <i class="bi bi-x-circle me-1"></i>{{ $message }}
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">
                        <i class="bi bi-lock me-1"></i> Şifre
                    </label>
                    <div class="password-wrapper">
                        <input type="password" 
                               class="form-input @error('password') is-invalid @enderror" 
                               id="password" 
                               name="password"
                               placeholder="••••••••"
                               required>
                        <button type="button" class="password-toggle" onclick="togglePassword()">
                            <i class="bi bi-eye" id="passwordIcon"></i>
                        </button>
                    </div>
                    @error('password')
                        <span class="invalid-feedback">
                            <i class="bi bi-x-circle me-1"></i>{{ $message }}
                        </span>
                    @enderror
                </div>

                <div class="checkbox-group">
                    <input type="checkbox" 
                           id="remember" 
                           name="remember"
                           {{ request()->cookie('remember_email') ? 'checked' : '' }}>
                    <label for="remember">
                        <i class="bi bi-check-circle me-1"></i> Beni Hatırla
                    </label>
                </div>

                <button type="submit" class="btn-login">
                    <i class="bi bi-box-arrow-in-right me-2"></i> Giriş Yap
                </button>
            </form>

            <div class="forgot-password">
                <a href="{{ route('password.request') }}">
                    <i class="bi bi-key me-1"></i>
                    Şifremi Unuttum
                </a>
            </div>
        </div>
        
        <div class="footer-text">
            <i class="bi bi-droplet-fill me-1"></i> {{ date('Y') }} - Atıksu Takip Sistemi
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const passwordIcon = document.getElementById('passwordIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.classList.remove('bi-eye');
                passwordIcon.classList.add('bi-eye-slash');
            } else {
                passwordInput.type = 'password';
                passwordIcon.classList.remove('bi-eye-slash');
                passwordIcon.classList.add('bi-eye');
            }
        }
    </script>
    
    <!-- PWA Service Worker -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/service-worker.js')
                    .then(registration => {
                        console.log('ServiceWorker kayıt başarılı:', registration.scope);
                    })
                    .catch(err => {
                        console.log('ServiceWorker kayıt hatası:', err);
                    });
            });
        }
    </script>
</body>
</html>
