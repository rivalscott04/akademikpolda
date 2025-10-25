@extends('layouts.app')

@section('title', 'Laporan Kemampuan Siswa')

@section('content')
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-title">
                    <h5><i class="fa fa-chart-line"></i> Laporan Kemampuan Siswa</h5>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="text-center mb-4">
                                <h3>Pilih Jenis Laporan</h3>
                                <p class="text-muted">Pilih jenis laporan yang ingin Anda lihat</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Laporan Paket Lengkap -->
                        <div class="col-lg-6">
                            <div class="laporan-card">
                                <div class="card-body text-center">
                                    <div class="laporan-icon">
                                        <i class="fa fa-graduation-cap fa-4x text-primary"></i>
                                    </div>
                                    <h4 class="mt-3">Laporan Paket Lengkap</h4>
                                    <p class="text-muted">
                                        Analisis komprehensif kemampuan siswa dari semua paket tes yang telah diambil. 
                                        Meliputi perbandingan tes pertama vs terakhir, identifikasi kelemahan dan kekuatan, 
                                        serta rekomendasi strategi belajar.
                                    </p>
                                    <div class="laporan-features">
                                        <ul class="list-unstyled">
                                            <li><i class="fa fa-check text-success"></i> Analisis semua kategori</li>
                                            <li><i class="fa fa-check text-success"></i> Trend perkembangan</li>
                                            <li><i class="fa fa-check text-success"></i> Rekomendasi holistik</li>
                                            <li><i class="fa fa-check text-success"></i> Grafik progress</li>
                                        </ul>
                                    </div>
                                    <a href="{{ route('admin.laporan.kemampuan.paket-lengkap') }}" class="btn btn-primary btn-lg btn-block">
                                        <i class="fa fa-chart-bar"></i> Lihat Laporan Paket Lengkap
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Laporan Per Paket -->
                        <div class="col-lg-6">
                            <div class="laporan-card">
                                <div class="card-body text-center">
                                    <div class="laporan-icon">
                                        <i class="fa fa-layer-group fa-4x text-info"></i>
                                    </div>
                                    <h4 class="mt-3">Laporan Per Paket</h4>
                                    <p class="text-muted">
                                        Analisis mendalam kemampuan siswa per paket tes (Bahasa Inggris, PU, TWK, Numerik, dll). 
                                        Fokus pada kategori-kategori dalam paket tertentu dengan rekomendasi spesifik 
                                        untuk peningkatan performa.
                                    </p>
                                    <div class="laporan-features">
                                        <ul class="list-unstyled">
                                            <li><i class="fa fa-check text-success"></i> Analisis per paket</li>
                                            <li><i class="fa fa-check text-success"></i> Breakdown kategori</li>
                                            <li><i class="fa fa-check text-success"></i> Rekomendasi spesifik</li>
                                            <li><i class="fa fa-check text-success"></i> Fokus perbaikan</li>
                                        </ul>
                                    </div>
                                    <a href="{{ route('admin.laporan.kemampuan.per-paket') }}" class="btn btn-info btn-lg btn-block">
                                        <i class="fa fa-chart-pie"></i> Lihat Laporan Per Paket
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Informasi Tambahan -->
                    <div class="row mt-4">
                        <div class="col-lg-12">
                            <div class="alert alert-info">
                                <h5><i class="fa fa-info-circle"></i> Informasi Laporan</h5>
                                <ul class="mb-0">
                                    <li><strong>Laporan Paket Lengkap:</strong> Menampilkan analisis menyeluruh dari semua tes yang telah diambil siswa</li>
                                    <li><strong>Laporan Per Paket:</strong> Menampilkan analisis spesifik untuk paket tertentu (Bahasa Inggris, PU, TWK, Numerik, dll)</li>
                                    <li>Semua laporan membandingkan performa tes pertama vs terakhir untuk mengidentifikasi progress</li>
                                    <li>Rekomendasi disesuaikan dengan hasil analisis untuk memberikan strategi belajar yang tepat</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="{{ asset('css/laporan-kemampuan.css') }}" rel="stylesheet">
<style>
.laporan-card {
    background: #fff;
    border: 1px solid #e7eaec;
    border-radius: 8px;
    padding: 0;
    margin-bottom: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.laporan-card:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    transform: translateY(-2px);
}

.laporan-card .card-body {
    padding: 30px;
}

.laporan-icon {
    margin-bottom: 20px;
}

.laporan-features {
    margin: 20px 0;
    text-align: left;
}

.laporan-features ul li {
    padding: 5px 0;
    font-size: 14px;
}

.laporan-features ul li i {
    margin-right: 8px;
}

.btn-lg {
    padding: 12px 30px;
    font-size: 16px;
    font-weight: 500;
}

.alert-info {
    border-left: 4px solid #1ab394;
}

.alert-info h5 {
    color: #1ab394;
    margin-bottom: 15px;
}

.alert-info ul li {
    margin-bottom: 8px;
}
</style>
@endpush

@push('scripts')
<script src="{{ asset('js/laporan-kemampuan.js') }}"></script>
@endpush
