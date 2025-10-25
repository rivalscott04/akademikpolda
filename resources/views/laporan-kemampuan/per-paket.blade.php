@extends('layouts.app')

@section('title', 'Laporan Per Paket')

@section('content')
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-title">
                    <h5><i class="fa fa-layer-group"></i> Laporan Per Paket</h5>
                    <div class="ibox-tools">
                        <a href="{{ route('admin.laporan.kemampuan.index') }}" class="btn btn-default btn-sm">
                            <i class="fa fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="text-center mb-4">
                                <h3>Pilih Paket untuk Laporan</h3>
                                <p class="text-muted">Pilih paket tes untuk melihat analisis kemampuan siswa</p>
                            </div>
                        </div>
                    </div>
                    
                    @if($packages->count() > 0)
                        <div class="row">
                            @foreach($packages as $package)
                                @php
                                    $packageNames = [
                                        'bahasa_inggris' => 'Bahasa Inggris',
                                        'pu' => 'Pengetahuan Umum', 
                                        'twk' => 'TWK',
                                        'numerik' => 'Numerik',
                                        'lengkap' => 'Lengkap'
                                    ];
                                    $packageIcons = [
                                        'bahasa_inggris' => 'language',
                                        'pu' => 'book',
                                        'twk' => 'flag', 
                                        'numerik' => 'calculator',
                                        'lengkap' => 'star'
                                    ];
                                    $packageColors = [
                                        'bahasa_inggris' => 'primary',
                                        'pu' => 'success',
                                        'twk' => 'info',
                                        'numerik' => 'warning', 
                                        'lengkap' => 'danger'
                                    ];
                                    $packageName = $packageNames[$package->package_type] ?? $package->package_type;
                                    $packageIcon = $packageIcons[$package->package_type] ?? 'layer-group';
                                    $packageColor = $packageColors[$package->package_type] ?? 'info';
                                @endphp
                                <div class="col-lg-4 col-md-6 mb-4">
                                    <div class="paket-card">
                                        <div class="card-body">
                                            <div class="paket-icon">
                                                <i class="fa fa-{{ $packageIcon }} fa-3x text-{{ $packageColor }}"></i>
                                            </div>
                                            <h5 class="paket-name">{{ $packageName }}</h5>
                                            
                                            <div class="paket-info">
                                                <p class="text-muted">
                                                    Analisis kemampuan siswa untuk paket {{ $packageName }} 
                                                    dengan breakdown per kategori soal.
                                                </p>
                                            </div>
                                            
                            <a href="{{ route('admin.laporan.kemampuan.per-paket-detail', ['package' => $package->package_type]) }}" 
                               class="btn btn-{{ $packageColor }} btn-block">
                                                <i class="fa fa-chart-pie"></i> Lihat Laporan {{ $packageName }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center">
                            <div class="empty-state">
                                <i class="fa fa-layer-group fa-4x text-muted"></i>
                                <h4 class="mt-3">Belum Ada Paket Tersedia</h4>
                                <p class="text-muted">Belum ada paket tes yang tersedia untuk dianalisis.</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="{{ asset('css/laporan-kemampuan.css') }}" rel="stylesheet">
<style>
.paket-card {
    background: #fff;
    border: 1px solid #e7eaec;
    border-radius: 8px;
    margin-bottom: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.paket-card:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    transform: translateY(-2px);
}

.paket-card .card-body {
    padding: 25px;
    text-align: center;
}

.paket-icon {
    margin-bottom: 15px;
}

.paket-name {
    font-weight: 600;
    margin-bottom: 15px;
    color: #2c3e50;
}

.paket-info {
    margin-bottom: 20px;
}

.empty-state {
    padding: 60px 20px;
}

.empty-state i {
    margin-bottom: 20px;
}

.btn-block {
    width: 100%;
}
</style>
@endpush

@push('scripts')
<script src="{{ asset('js/laporan-kemampuan.js') }}"></script>
@endpush
