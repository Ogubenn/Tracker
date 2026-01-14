@extends('layouts.app')

@section('title', 'Yeni Kontrol Maddesi Ekle')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Yeni Kontrol Maddesi Ekle</h1>
    <a href="{{ route('admin.kontrol-maddeleri.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Geri Dön
    </a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.kontrol-maddeleri.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="bina_id" class="form-label">Bina *</label>
                        <select class="form-select @error('bina_id') is-invalid @enderror" 
                                id="bina_id" name="bina_id" required>
                            <option value="">Seçiniz...</option>
                            @foreach($binalar as $bina)
                                <option value="{{ $bina->id }}" {{ old('bina_id') == $bina->id ? 'selected' : '' }}>
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
                               id="kontrol_adi" name="kontrol_adi" value="{{ old('kontrol_adi') }}" required>
                        @error('kontrol_adi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="kontrol_tipi" class="form-label">Kontrol Tipi *</label>
                            <select class="form-select @error('kontrol_tipi') is-invalid @enderror" 
                                    id="kontrol_tipi" name="kontrol_tipi" required>
                                <option value="checkbox" {{ old('kontrol_tipi') == 'checkbox' ? 'selected' : '' }}>Checkbox</option>
                                <option value="sayisal" {{ old('kontrol_tipi') == 'sayisal' ? 'selected' : '' }}>Sayısal</option>
                                <option value="metin" {{ old('kontrol_tipi') == 'metin' ? 'selected' : '' }}>Metin</option>
                            </select>
                            @error('kontrol_tipi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3" id="birim_div" style="display: none;">
                            <label for="birim" class="form-label">Birim <small class="text-muted">(Opsiyonel)</small></label>
                            <input type="text" class="form-control @error('birim') is-invalid @enderror" 
                                   id="birim" name="birim" value="{{ old('birim') }}" 
                                   placeholder="Örn: m3, kg, lt, kWh">
                            <div class="form-text">Sayısal değerin yanında gösterilecek birim</div>
                            @error('birim')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="periyot" class="form-label">Periyot *</label>
                            <select class="form-select @error('periyot') is-invalid @enderror" 
                                    id="periyot" name="periyot" required>
                                <option value="gunluk" {{ old('periyot') == 'gunluk' ? 'selected' : '' }}>Günlük</option>
                                <option value="haftalik" {{ old('periyot') == 'haftalik' ? 'selected' : '' }}>Haftalık</option>
                                <option value="15_gun" {{ old('periyot') == '15_gun' ? 'selected' : '' }}>15 Günlük</option>
                                <option value="aylik" {{ old('periyot') == 'aylik' ? 'selected' : '' }}>Aylık</option>
                            </select>
                            @error('periyot')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label d-block">Zaman Seçimi</label>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="zaman_secimi" name="zaman_secimi" 
                                       value="1" {{ old('zaman_secimi') ? 'checked' : '' }}>
                                <label class="form-check-label" for="zaman_secimi">
                                    Başlangıç ve bitiş saati al
                                </label>
                            </div>
                            <div class="form-text">Örn: Dekantör çalışma saatleri</div>
                        </div>
                    </div>

                    <div class="mb-3" id="haftalik_gun_div" style="display: none;">
                        <label for="haftalik_gun" class="form-label">Haftalık Gün</label>
                        <select class="form-select" id="haftalik_gun" name="haftalik_gun">
                            <option value="">Seçiniz...</option>
                            <option value="pazartesi" {{ old('haftalik_gun') == 'pazartesi' ? 'selected' : '' }}>Pazartesi</option>
                            <option value="sali" {{ old('haftalik_gun') == 'sali' ? 'selected' : '' }}>Salı</option>
                            <option value="carsamba" {{ old('haftalik_gun') == 'carsamba' ? 'selected' : '' }}>Çarşamba</option>
                            <option value="persembe" {{ old('haftalik_gun') == 'persembe' ? 'selected' : '' }}>Perşembe</option>
                            <option value="cuma" {{ old('haftalik_gun') == 'cuma' ? 'selected' : '' }}>Cuma</option>
                            <option value="cumartesi" {{ old('haftalik_gun') == 'cumartesi' ? 'selected' : '' }}>Cumartesi</option>
                            <option value="pazar" {{ old('haftalik_gun') == 'pazar' ? 'selected' : '' }}>Pazar</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="sira" class="form-label">Sıra</label>
                        <input type="number" class="form-control" id="sira" name="sira" value="{{ old('sira', 0) }}" min="0">
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="aktif_mi" name="aktif_mi" 
                               value="1" {{ old('aktif_mi', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="aktif_mi">Aktif</label>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Kaydet
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

document.getElementById('kontrol_tipi').addEventListener('change', function() {
    const birimDiv = document.getElementById('birim_div');
    if (this.value === 'sayisal') {
        birimDiv.style.display = 'block';
    } else {
        birimDiv.style.display = 'none';
    }
});

// Sayfa yüklendiğinde kontrol et
if (document.getElementById('periyot').value === 'haftalik') {
    document.getElementById('haftalik_gun_div').style.display = 'block';
}

if (document.getElementById('kontrol_tipi').value === 'sayisal') {
    document.getElementById('birim_div').style.display = 'block';
}
</script>
@endpush

