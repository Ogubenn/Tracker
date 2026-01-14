@extends('layouts.app')

@section('title', 'Yeni Bina Ekle')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Yeni Bina Ekle</h1>
    <a href="{{ route('admin.binalar.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Geri Dön
    </a>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.binalar.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="bina_adi" class="form-label">Bina Adı *</label>
                        <input type="text" class="form-control @error('bina_adi') is-invalid @enderror" 
                               id="bina_adi" name="bina_adi" value="{{ old('bina_adi') }}" required>
                        @error('bina_adi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="aktif_mi" name="aktif_mi" 
                               value="1" {{ old('aktif_mi', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="aktif_mi">
                            Aktif
                        </label>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Kaydet
                        </button>
                        <a href="{{ route('admin.binalar.index') }}" class="btn btn-secondary">
                            İptal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
