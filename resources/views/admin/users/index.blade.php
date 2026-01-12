@extends('layouts.app')

@section('title', 'Kullanıcı Yönetimi')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Kullanıcı Yönetimi</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Yeni Kullanıcı Ekle
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Ad Soyad</th>
                        <th>E-posta</th>
                        <th>Rol</th>
                        <th>Durum</th>
                        <th>Kayıt Tarihi</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>
                                <i class="bi bi-person-circle"></i>
                                {{ $user->ad }}
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if($user->rol === 'admin')
                                    <span class="badge bg-danger">Admin</span>
                                @else
                                    <span class="badge bg-info">Personel</span>
                                @endif
                            </td>
                            <td>
                                @if($user->aktif_mi)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Pasif</span>
                                @endif
                            </td>
                            <td>{{ $user->created_at->format('d.m.Y H:i') }}</td>
                            <td>
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i> Düzenle
                                </a>
                                
                                <form action="{{ route('admin.users.toggle-qr', $user) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm {{ $user->qr_gorunur ? 'btn-success' : 'btn-secondary' }}" 
                                            title="{{ $user->qr_gorunur ? 'QR sayfasında görünür' : 'QR sayfasında gizli' }}">
                                        <i class="bi bi-{{ $user->qr_gorunur ? 'eye' : 'eye-slash' }}"></i>
                                    </button>
                                </form>
                                
                                @if($user->id !== auth()->id())
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline" 
                                          onsubmit="return confirm('Bu kullanıcıyı silmek istediğinizden emin misiniz?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="bi bi-trash"></i> Sil
                                        </button>
                                    </form>
                                @else
                                    <button class="btn btn-sm btn-secondary" disabled title="Kendi hesabınızı silemezsiniz">
                                        <i class="bi bi-trash"></i> Sil
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">Henüz kullanıcı bulunmamaktadır.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
