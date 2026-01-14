@extends('layouts.app')

@section('title', 'Günlük Kontroller')

@push('styles')
<style>
    .kontrol-card {
        margin-bottom: 2rem;
    }
    .kontrol-item {
        padding: 1rem;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        margin-bottom: 0.75rem;
        background-color: #fff;
    }
    .kontrol-item:hover {
        background-color: #f8f9fa;
    }
    .btn-kontrol {
        min-height: 60px;
        font-size: 1.1rem;
    }
</style>
@endpush

@section('content')
<div class="container py-4">
    <div class="text-center mb-4">
        <h2><i class="bi bi-calendar-check text-primary"></i> Bugünün Kontrolleri</h2>
        <p class="text-muted">{{ \Carbon\Carbon::today()->translatedFormat('d F Y l') }}</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(count($bugunYapilacakKontroller) > 0)
        @foreach($bugunYapilacakKontroller as $binaData)
            <div class="card kontrol-card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-building"></i> {{ $binaData['bina']->bina_adi }}
                    </h5>
                </div>
                <div class="card-body">
                    @foreach($binaData['kontroller'] as $kontrol)
                        <div class="kontrol-item">
                            <form action="{{ route('personel.kontrol.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="kontrol_maddesi_id" value="{{ $kontrol->id }}">

                                <div class="row align-items-center">
                                    <div class="col-md-6 mb-2 mb-md-0">
                                        <strong>{{ $kontrol->kontrol_adi }}</strong>
                                        <div class="small text-muted">
                                            @if($kontrol->periyot == 'gunluk')
                                                <span class="badge bg-success">Günlük</span>
                                            @elseif($kontrol->periyot == 'haftalik')
                                                <span class="badge bg-warning">Haftalık</span>
                                            @elseif($kontrol->periyot == '15_gun')
                                                <span class="badge bg-info">15 Günlük</span>
                                            @else
                                                <span class="badge bg-primary">Aylık</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="d-flex gap-2 align-items-center">
                                            @if($kontrol->kontrol_tipi == 'checkbox')
                                                    <button type="submit" name="girilen_deger" value="1" 
                                                            class="btn btn-success btn-kontrol flex-fill">
                                                        <i class="bi bi-check-lg"></i> Yapıldı
                                                    </button>
                                                    <button type="submit" name="girilen_deger" value="0" 
                                                            class="btn btn-outline-danger flex-fill">
                                                        <i class="bi bi-x-lg"></i> Yapılmadı
                                                    </button>
                                                @elseif($kontrol->kontrol_tipi == 'sayisal')
                                                    <input type="number" step="0.01" class="form-control" 
                                                           name="girilen_deger" placeholder="Değer girin" required>
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="bi bi-save"></i> Kaydet
                                                    </button>
                                                @else
                                                    <input type="text" class="form-control" 
                                                           name="girilen_deger" placeholder="Not girin" required>
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="bi bi-save"></i> Kaydet
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        @endforeach
                </div>
            </div>
        @endforeach
    @else
        <div class="card text-center py-5">
            <div class="card-body">
                <i class="bi bi-check-circle text-success" style="font-size: 4rem;"></i>
                <h3 class="mt-3">Tebrikler!</h3>
                <p class="text-muted">Bugün için tüm kontroller tamamlanmış durumda.</p>
            </div>
        </div>
    @endif
</div>
@endsection
