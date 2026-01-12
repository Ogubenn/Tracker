@extends('layouts.app')

@section('title', 'Kontrol Maddesi Düzenle')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Kontrol Maddesi Düzenle</h1>
    <a href="{{ route('admin.kontrol-maddeleri.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Geri Dön
    </a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.kontrol-maddeleri.update', $kontrolMaddesi) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="bina_id" class="form-label">Bina *</label>
                        <select class="form-select @error('bina_id') is-invalid @enderror" 
                                id="bina_id" name="bina_id" required>
                            <option value="">Seçiniz...</option>
                            @foreach($binalar as $bina)
                                <option value="{{ $bina->id }}" {{ old('bina_id', $kontrolMaddesi->bina_id) == $bina->id ? 'selected' : '' }}>
                                    {{ $bina->bina_adi }}
                                </option>
                            @endforeach
                        </select>
                        @error('bina_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="kontrol_adi" class="form-label">Kontrol Adı *</label>
                        <input type="text" class="form-control @error('kontrol_adi') is-invalid @enderror" 
                               id="kontrol_adi" name="kontrol_adi" value="{{ old('kontrol_adi', $kontrolMaddesi->kontrol_adi) }}" required>
                        @error('kontrol_adi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="kontrol_tipi" class="form-label">Kontrol Tipi *</label>
                            <select class="form-select @error('kontrol_tipi') is-invalid @enderror" 
                                    id="kontrol_tipi" name="kontrol_tipi" required>
                                <option value="checkbox" {{ old('kontrol_tipi', $kontrolMaddesi->kontrol_tipi) == 'checkbox' ? 'selected' : '' }}>Checkbox</option>
                                <option value="sayisal" {{ old('kontrol_tipi', $kontrolMaddesi->kontrol_tipi) == 'sayisal' ? 'selected' : '' }}>Sayısal</option>
                                <option value="metin" {{ old('kontrol_tipi', $kontrolMaddesi->kontrol_tipi) == 'metin' ? 'selected' : '' }}>Metin</option>
                            </select>
                            @error('kontrol_tipi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="periyot" class="form-label">Periyot *</label>
                            <select class="form-select @error('periyot') is-invalid @enderror" 
                                    id="periyot" name="periyot" required>
                                <option value="gunluk" {{ old('periyot', $kontrolMaddesi->periyot) == 'gunluk' ? 'selected' : '' }}>Günlük</option>
                                <option value="haftalik" {{ old('periyot', $kontrolMaddesi->periyot) == 'haftalik' ? 'selected' : '' }}>Haftalık</option>
                                <option value="15_gun" {{ old('periyot', $kontrolMaddesi->periyot) == '15_gun' ? 'selected' : '' }}>15 Günlük</option>
                                <option value="aylik" {{ old('periyot', $kontrolMaddesi->periyot) == 'aylik' ? 'selected' : '' }}>Aylık</option>
                            </select>
                            @error('periyot')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3" id="haftalik_gun_div" style="display: none;">
                        <label for="haftalik_gun" class="form-label">Haftalık Gün</label>
                        <select class="form-select" id="haftalik_gun" name="haftalik_gun">
                            <option value="">Seçiniz...</option>
                            <option value="pazartesi" {{ old('haftalik_gun', $kontrolMaddesi->haftalik_gun) == 'pazartesi' ? 'selected' : '' }}>Pazartesi</option>
                            <option value="sali" {{ old('haftalik_gun', $kontrolMaddesi->haftalik_gun) == 'sali' ? 'selected' : '' }}>Salı</option>
                            <option value="carsamba" {{ old('haftalik_gun', $kontrolMaddesi->haftalik_gun) == 'carsamba' ? 'selected' : '' }}>Çarşamba</option>
                            <option value="persembe" {{ old('haftalik_gun', $kontrolMaddesi->haftalik_gun) == 'persembe' ? 'selected' : '' }}>Perşembe</option>
                            <option value="cuma" {{ old('haftalik_gun', $kontrolMaddesi->haftalik_gun) == 'cuma' ? 'selected' : '' }}>Cuma</option>
                            <option value="cumartesi" {{ old('haftalik_gun', $kontrolMaddesi->haftalik_gun) == 'cumartesi' ? 'selected' : '' }}>Cumartesi</option>
                            <option value="pazar" {{ old('haftalik_gun', $kontrolMaddesi->haftalik_gun) == 'pazar' ? 'selected' : '' }}>Pazar</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="sira" class="form-label">Sıra</label>
                        <input type="number" class="form-control" id="sira" name="sira" value="{{ old('sira', $kontrolMaddesi->sira) }}" min="0">
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="aktif_mi" name="aktif_mi" 
                               value="1" {{ old('aktif_mi', $kontrolMaddesi->aktif_mi) ? 'checked' : '' }}>
                        <label class="form-check-label" for="aktif_mi">Aktif</label>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Güncelle
                        </button>
                        <a href="{{ route('admin.kontrol-maddeleri.index') }}" class="btn btn-secondary">İptal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('periyot').addEventListener('change', function() {
    const haftalikDiv = document.getElementById('haftalik_gun_div');
    if (this.value === 'haftalik') {
        haftalikDiv.style.display = 'block';
    } else {
        haftalikDiv.style.display = 'none';
    }
});

// Sayfa yüklendiğinde kontrol et
if (document.getElementById('periyot').value === 'haftalik') {
    document.getElementById('haftalik_gun_div').style.display = 'block';
}
</script>
@endpush

