@extends('layouts.app')

@section('title', 'Giriş Yap')

@section('content')
<div class="container">
    <div class="row justify-content-center align-items-center" style="min-height: 80vh;">
        <div class="col-md-5">
            <div class="card shadow">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="bi bi-droplet-fill text-primary" style="font-size: 3rem;"></i>
                        <h3 class="mt-2">Atıksu Takip Sistemi</h3>
                        <p class="text-muted">Giriş yapın</p>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label">E-posta</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email') }}" required autofocus>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Şifre</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                   id="password" name="password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">
                                Beni Hatırla
                            </label>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                Giriş Yap
                            </button>
                        </div>
                    </form>

                    <div class="mt-4 text-center text-muted small">
                        <p>Demo Kullanıcılar:</p>
                        <p class="mb-1"><strong>Admin:</strong> admin@atiksu.com / password</p>
                        <p><strong>Personel:</strong> personel@atiksu.com / password</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
