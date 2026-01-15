@extends('layouts.app')

@section('title', 'Kullanıcı Yönetimi')

@section('content')
<div class="d-flex justify-content-between flex-wrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Kullanıcı Yönetimi</h1>
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> <span class="d-none d-sm-inline">Yeni Kullanıcı</span>
    </a>
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

<div class="d-none d-lg-block">
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="60">ID</th>
                            <th>Ad Soyad</th>
                            <th>E-posta</th>
                            <th width="100">Rol</th>
                            <th width="100">Durum</th>
                            <th width="130">Kayıt Tarihi</th>
                            <th width="200">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td><span class="badge bg-light text-dark">#{{ $user->id }}</span></td>
                                <td>
                                    <i class="bi bi-person-circle text-primary"></i>
                                    <strong>{{ $user->ad }}</strong>
                                </td>
                                <td><small>{{ $user->email }}</small></td>
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
                                <td><small>{{ $user->created_at->format('d.m.Y H:i') }}</small></td>
                                <td>
                                    <div class="d-flex gap-1 justify-content-center">
                                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning btn-sm" title="Düzenle">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        
                                        <form action="{{ route('admin.users.toggle-qr', $user) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm {{ $user->qr_gorunur ? 'btn-success' : 'btn-secondary' }}" 
                                                    title="{{ $user->qr_gorunur ? 'QR görünür' : 'QR gizli' }}">
                                                <i class="bi bi-{{ $user->qr_gorunur ? 'eye' : 'eye-slash' }}"></i>
                                            </button>
                                        </form>

                                        <form action="{{ route('admin.users.toggle-mail', $user) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm {{ $user->mail_alsin ? 'btn-primary' : 'btn-outline-secondary' }}" 
                                                    title="{{ $user->mail_alsin ? 'Mail açık' : 'Mail kapalı' }}">
                                                <i class="bi bi-{{ $user->mail_alsin ? 'envelope-check' : 'envelope-slash' }}"></i>
                                            </button>
                                        </form>
                                        
                                        @if($user->id !== auth()->id())
                                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline" 
                                                  onsubmit="return confirm('Bu kullanıcıyı silmek istediğinizden emin misiniz?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" title="Sil">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @else
                                            <button class="btn btn-secondary btn-sm" disabled title="Kendi hesabınızı silemezsiniz">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">Henüz kullanıcı bulunmamaktadır.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="d-lg-none">
    @forelse($users as $user)
        <div class="card mb-3 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <h5 class="card-title mb-1">
                            <i class="bi bi-person-circle text-primary"></i>
                            {{ $user->ad }}
                        </h5>
                        <p class="card-text text-muted small mb-2">
                            <i class="bi bi-envelope"></i> {{ $user->email }}
                        </p>
                    </div>
                    <span class="badge bg-light text-dark">#{{ $user->id }}</span>
                </div>
                
                <div class="d-flex gap-2 mb-3 flex-wrap">
                    @if($user->rol === 'admin')
                        <span class="badge bg-danger">Admin</span>
                    @else
                        <span class="badge bg-info">Personel</span>
                    @endif
                    
                    @if($user->aktif_mi)
                        <span class="badge bg-success">Aktif</span>
                    @else
                        <span class="badge bg-secondary">Pasif</span>
                    @endif
                    
                    <span class="badge bg-light text-dark">
                        <i class="bi bi-calendar"></i> {{ $user->created_at->format('d.m.Y') }}
                    </span>
                </div>
                
                <div class="d-flex gap-1 flex-wrap">
                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-warning flex-fill">
                        <i class="bi bi-pencil"></i> Düzenle
                    </a>
                    
                    <form action="{{ route('admin.users.toggle-qr', $user) }}" method="POST" class="flex-fill">
                        @csrf
                        <button type="submit" class="btn btn-sm {{ $user->qr_gorunur ? 'btn-success' : 'btn-secondary' }} w-100" 
                                title="{{ $user->qr_gorunur ? 'QR görünür' : 'QR gizli' }}">
                            <i class="bi bi-{{ $user->qr_gorunur ? 'eye' : 'eye-slash' }}"></i>
                        </button>
                    </form>

                    <form action="{{ route('admin.users.toggle-mail', $user) }}" method="POST" class="flex-fill">
                        @csrf
                        <button type="submit" class="btn btn-sm {{ $user->mail_alsin ? 'btn-primary' : 'btn-outline-secondary' }} w-100" 
                                title="{{ $user->mail_alsin ? 'Mail açık' : 'Mail kapalı' }}">
                            <i class="bi bi-{{ $user->mail_alsin ? 'envelope-check' : 'envelope-slash' }}"></i>
                        </button>
                    </form>
                    
                    @if($user->id !== auth()->id())
                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="flex-fill" 
                              onsubmit="return confirm('Bu kullanıcıyı silmek istediğinizden emin misiniz?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger w-100">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> Henüz kullanıcı bulunmamaktadır.
        </div>
    @endforelse
</div>
@endsection
