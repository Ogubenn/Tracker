@extends('layouts.app')

@section('title', 'Yeni Kullanıcı Ekle')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Yeni Kullanıcı Ekle</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Geri Dön
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.users.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="ad" class="form-label">Ad Soyad <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('ad') is-invalid @enderror" 
                               id="ad" name="ad" value="{{ old('ad') }}" required>
                        @error('ad')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">E-posta <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                               id="email" name="email" value="{{ old('email') }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Şifre <span class="text-danger">*</span></label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" 
                               id="password" name="password" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Minimum 6 karakter</small>
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Şifre Tekrar <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" 
                               id="password_confirmation" name="password_confirmation" required>
                    </div>

                    <div class="mb-3">
                        <label for="rol" class="form-label">Rol <span class="text-danger">*</span></label>
                        <select class="form-select @error('rol') is-invalid @enderror" id="rol" name="rol" required>
                            <option value="">Seçiniz...</option>
                            <option value="admin" {{ old('rol') === 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="personel" {{ old('rol') === 'personel' ? 'selected' : '' }}>Personel</option>
                        </select>
                        @error('rol')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="aktif_mi" name="aktif_mi" 
                                   {{ old('aktif_mi', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="aktif_mi">
                                Aktif
                            </label>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Kullanıcı Oluştur
                        </button>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                            İptal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Bilgilendirme</h5>
                <p class="card-text">
                    <i class="bi bi-info-circle text-info"></i> 
                    Yeni kullanıcı eklerken aşağıdaki bilgilere dikkat edin:
                </p>
                <ul class="small">
                    <li><strong>Admin:</strong> Tüm işlemleri yapabilir</li>
                    <li><strong>Personel:</strong> Sadece günlük kontrolleri yapabilir</li>
                    <li>Şifre en az 6 karakter olmalıdır</li>
                    <li>E-posta adresi benzersiz olmalıdır</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
