@extends('layouts.app')

@section('title', 'Excel İçe Aktar - Laboratuvar')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="bi bi-file-earmark-excel"></i> Toplu Veri İçe Aktarma
        </h1>
        <a href="{{ route('admin.laboratuvar.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Geri
        </a>
    </div>

    <!-- Açıklama -->
    <div class="alert alert-info">
        <h5><i class="bi bi-info-circle"></i> Nasıl Kullanılır?</h5>
        <ol class="mb-0">
            <li>Aşağıdaki <strong>"Excel Şablonunu İndir"</strong> butonuna tıklayın</li>
            <li>Şablonu doldurun (Her satır bir parametre)</li>
            <li>Doldurduğunuz dosyayı yükleyin</li>
            <li>Sistem otomatik olarak raporları ve parametreleri oluşturacaktır</li>
        </ol>
    </div>

    <!-- Şablon İndirme -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="bi bi-download"></i> Excel Şablonu</h5>
        </div>
        <div class="card-body">
            <p class="mb-3">Excel şablonunu indirin ve doldurun:</p>
            
            <a href="{{ route('admin.laboratuvar.excel-template') }}" class="btn btn-success btn-lg">
                <i class="bi bi-file-earmark-excel-fill"></i> Excel Şablonunu İndir
            </a>
            
            <div class="mt-4">
                <h6>Şablon Kolonları:</h6>
                <table class="table table-sm table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Kolon</th>
                            <th>Açıklama</th>
                            <th>Örnek</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Rapor No</strong></td>
                            <td>Raporun benzersiz numar numarası</td>
                            <td>T-79051-2025-03</td>
                        </tr>
                        <tr>
                            <td><strong>Rapor Tarihi</strong></td>
                            <td>Raporun tarihi (GG.AA.YYYY)</td>
                            <td>15.05.2025</td>
                        </tr>
                        <tr>
                            <td><strong>Tesis Adı</strong></td>
                            <td>Tesis/Kurum adı</td>
                            <td>Bulancak Belediyesi Su ve Kanalizasyon...</td>
                        </tr>
                        <tr>
                            <td><strong>Parametre Adı</strong></td>
                            <td>Analiz edilen parametre</td>
                            <td>Biyokimyasal Oksijen İhtiyacı</td>
                        </tr>
                        <tr>
                            <td><strong>Birim</strong></td>
                            <td>Ölçüm birimi</td>
                            <td>mg/L, °C, -</td>
                        </tr>
                        <tr>
                            <td><strong>Analiz Sonucu</strong></td>
                            <td>Ölçülen değer</td>
                            <td>4.05</td>
                        </tr>
                        <tr>
                            <td><strong>Limit Değeri</strong></td>
                            <td>İzin verilen limit</td>
                            <td>25</td>
                        </tr>
                        <tr>
                            <td><strong>Analiz Metodu</strong></td>
                            <td>Kullanılan test metodu</td>
                            <td>SM 5210 B</td>
                        </tr>
                        <tr>
                            <td><strong>Tablo No</strong></td>
                            <td>EK-IV tablo numarası (opsiyonel)</td>
                            <td>1</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Yükleme Formu -->
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-upload"></i> Excel Dosyası Yükle</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.laboratuvar.excel-import-store') }}" enctype="multipart/form-data">
                @csrf
                
                <div class="mb-3">
                    <label class="form-label">Excel Dosyası Seçin <span class="text-danger">*</span></label>
                    <input type="file" name="excel_file" class="form-control" accept=".xlsx,.xls,.csv" required>
                    <small class="text-muted">Maksimum 10 MB - Desteklenen formatlar: .xlsx, .xls, .csv</small>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle"></i> <strong>Dikkat:</strong>
                    <ul class="mb-0 mt-2">
                        <li>Aynı rapor numarasına sahip satırlar tek rapor altında birleştirilir</li>
                        <li>Her satır bir parametre kaydıdır</li>
                        <li>Hatalı satırlar atlanır, doğru olanlar kaydedilir</li>
                    </ul>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-upload"></i> Yükle ve İçe Aktar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
