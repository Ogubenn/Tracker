@extends('layouts.app')

@section('title', 'Kullanıcı Düzenle')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Kullanıcı Düzenle</h1>
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
                <form action="{{ route('admin.users.update', $user) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="ad" class="form-label">Ad Soyad <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('ad') is-invalid @enderror" 
                               id="ad" name="ad" value="{{ old('ad', $user->ad) }}" required>
                        @error('ad')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">E-posta <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                               id="email" name="email" value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Yeni Şifre</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" 
                               id="password" name="password">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Şifreyi değiştirmek istemiyorsanız boş bırakın. Minimum 6 karakter</small>
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Yeni Şifre Tekrar</label>
                        <input type="password" class="form-control" 
                               id="password_confirmation" name="password_confirmation">
                    </div>

                    <div class="mb-3">
                        <label for="rol" class="form-label">Rol <span class="text-danger">*</span></label>
                        <select class="form-select @error('rol') is-invalid @enderror" id="rol" name="rol" required>
                            <option value="">Seçiniz...</option>
                            <option value="admin" {{ old('rol', $user->rol) === 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="personel" {{ old('rol', $user->rol) === 'personel' ? 'selected' : '' }}>Personel</option>
                        </select>
                        @error('rol')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="aktif_mi" name="aktif_mi" 
                                   {{ old('aktif_mi', $user->aktif_mi) ? 'checked' : '' }}>
                            <label class="form-check-label" for="aktif_mi">
                                Aktif
                            </label>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Değişiklikleri Kaydet
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
                <h5 class="card-title">Kullanıcı Bilgileri</h5>
                <ul class="list-unstyled">
                    <li><strong>ID:</strong> {{ $user->id }}</li>
                    <li><strong>Kayıt Tarihi:</strong> {{ $user->created_at->format('d.m.Y H:i') }}</li>
                    <li><strong>Son Güncelleme:</strong> {{ $user->updated_at->format('d.m.Y H:i') }}</li>
                </ul>
            </div>
        </div>

        @if($user->id === auth()->id())
        <div class="alert alert-warning mt-3">
            <i class="bi bi-exclamation-triangle"></i> 
            <strong>Uyarı:</strong> Bu sizin hesabınız. Dikkatli olun!
        </div>
        @endif
    </div>
</div>
@endsection
