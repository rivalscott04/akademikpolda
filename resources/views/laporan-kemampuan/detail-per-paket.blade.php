@extends('layouts.app')

@section('title', 'Detail Laporan Per Paket')

@section('content')
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-title">
                    <h5><i class="fa fa-layer-group"></i> Laporan {{ $packageType }} - {{ $user->name }}</h5>
                    <div class="ibox-tools">
                        <a href="{{ route('admin.laporan.kemampuan.per-paket') }}" class="btn btn-default btn-sm">
                            <i class="fa fa-arrow-left"></i> Kembali
                        </a>
                        <button class="btn btn-success btn-sm" onclick="printLaporan()">
                            <i class="fa fa-print"></i> Print
                        </button>
                    </div>
                </div>
                <div class="ibox-content">
                    <!-- Header Info Siswa -->
                    <div class="row mb-4">
                        <div class="col-lg-12">
                            <div class="siswa-header">
                                <div class="siswa-info">
                                    <h3>{{ $user->name }}</h3>
                                    <p class="text-muted">{{ $user->email }}</p>
                                    <p class="text-muted">Paket: {{ $packageType }} | Total Kategori: {{ count($analisis) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Summary Cards -->
                    <div class="row mb-4">
                        <div class="col-lg-3 col-md-6">
                            <div class="summary-card">
                                <div class="card-body">
                                    <div class="summary-icon">
                                        <i class="fa fa-layer-group text-info"></i>
                                    </div>
                                    <div class="summary-content">
                                        <h4>{{ count($analisis) }}</h4>
                                        <p>Kategori dalam Paket</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="summary-card">
                                <div class="card-body">
                                    <div class="summary-icon">
                                        <i class="fa fa-arrow-up text-success"></i>
                                    </div>
                                    <div class="summary-content">
                                        <h4>{{ collect($analisis)->where('selisih_skor', '>', 0)->count() }}</h4>
                                        <p>Kategori Meningkat</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="summary-card">
                                <div class="card-body">
                                    <div class="summary-icon">
                                        <i class="fa fa-arrow-down text-danger"></i>
                                    </div>
                                    <div class="summary-content">
                                        <h4>{{ collect($analisis)->where('selisih_skor', '<', 0)->count() }}</h4>
                                        <p>Kategori Menurun</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="summary-card">
                                <div class="card-body">
                                    <div class="summary-icon">
                                        <i class="fa fa-chart-bar text-warning"></i>
                                    </div>
                                    <div class="summary-content">
                                        <h4>{{ number_format(collect($analisis)->avg('selisih_skor'), 1) }}</h4>
                                        <p>Rata-rata Peningkatan</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Detail Analisis -->
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="analisis-section">
                                <h4><i class="fa fa-chart-pie"></i> Analisis Detail per Kategori dalam Paket {{ $packageType }}</h4>
                                
                                @foreach($analisis as $item)
                                    <div class="kategori-card">
                                        <div class="kategori-header">
                                            <h5>{{ ucfirst($item['jenis_tes']) }}</h5>
                                            <div class="kategori-stats">
                                                <span class="stat-badge {{ $item['selisih_skor'] >= 0 ? 'success' : 'danger' }}">
                                                    {{ $item['selisih_skor'] >= 0 ? '+' : '' }}{{ $item['selisih_skor'] }} poin
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <div class="kategori-content">
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="stat-row">
                                                        <span class="stat-label">Tes Pertama:</span>
                                                        <span class="stat-value">{{ $item['tes_pertama']->skor }} poin</span>
                                                    </div>
                                                    <div class="stat-row">
                                                        <span class="stat-label">Tes Terakhir:</span>
                                                        <span class="stat-value">{{ $item['tes_terakhir']->skor }} poin</span>
                                                    </div>
                                                    <div class="stat-row">
                                                        <span class="stat-label">Total Tes:</span>
                                                        <span class="stat-value">{{ $item['total_tes'] }} kali</span>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="stat-row">
                                                        <span class="stat-label">Skor Tertinggi:</span>
                                                        <span class="stat-value">{{ $item['skor_tertinggi'] }} poin</span>
                                                    </div>
                                                    <div class="stat-row">
                                                        <span class="stat-label">Skor Terendah:</span>
                                                        <span class="stat-value">{{ $item['skor_terendah'] }} poin</span>
                                                    </div>
                                                    <div class="stat-row">
                                                        <span class="stat-label">Rata-rata:</span>
                                                        <span class="stat-value">{{ number_format($item['rata_rata'], 1) }} poin</span>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Rekomendasi Spesifik Paket -->
                                            <div class="rekomendasi-section">
                                                <h6><i class="fa fa-lightbulb"></i> Rekomendasi untuk Paket {{ $packageType }}:</h6>
                                                <p class="rekomendasi-text">
                                                    @if($item['selisih_skor'] > 0)
                                                        <span class="text-success">✓ Jenis Tes {{ ucfirst($item['jenis_tes']) }} dalam paket {{ $packageType }} menunjukkan peningkatan yang baik. Pertahankan konsistensi latihan untuk mempertahankan performa.</span>
                                                    @elseif($item['selisih_skor'] < 0)
                                                        <span class="text-danger">⚠ Jenis Tes {{ ucfirst($item['jenis_tes']) }} dalam paket {{ $packageType }} mengalami penurunan. Perbanyak latihan dan fokus pada area yang lemah dalam paket ini.</span>
                                                    @else
                                                        <span class="text-warning">→ Jenis Tes {{ ucfirst($item['jenis_tes']) }} dalam paket {{ $packageType }} stagnan. Coba variasi metode belajar untuk meningkatkan performa dalam paket ini.</span>
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
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
.siswa-header {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
}

.siswa-info h3 {
    margin-bottom: 5px;
    color: #2c3e50;
}

.summary-card {
    background: #fff;
    border: 1px solid #e7eaec;
    border-radius: 8px;
    margin-bottom: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.summary-card .card-body {
    padding: 20px;
    display: flex;
    align-items: center;
}

.summary-icon {
    font-size: 2rem;
    margin-right: 15px;
}

.summary-content h4 {
    margin: 0;
    font-size: 1.5rem;
    font-weight: 600;
    color: #2c3e50;
}

.summary-content p {
    margin: 0;
    color: #6c757d;
    font-size: 0.9rem;
}

.analisis-section {
    margin-top: 30px;
}

.kategori-card {
    background: #fff;
    border: 1px solid #e7eaec;
    border-radius: 8px;
    margin-bottom: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.kategori-header {
    background: #f8f9fa;
    padding: 15px 20px;
    border-bottom: 1px solid #e7eaec;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.kategori-header h5 {
    margin: 0;
    color: #2c3e50;
}

.stat-badge {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.8rem;
    font-weight: 600;
}

.stat-badge.success {
    background: #d4edda;
    color: #155724;
}

.stat-badge.danger {
    background: #f8d7da;
    color: #721c24;
}

.kategori-content {
    padding: 20px;
}

.stat-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.stat-label {
    color: #6c757d;
    font-size: 0.9rem;
}

.stat-value {
    font-weight: 600;
    color: #2c3e50;
}

.rekomendasi-section {
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid #e7eaec;
}

.rekomendasi-section h6 {
    color: #2c3e50;
    margin-bottom: 10px;
}

.rekomendasi-text {
    margin: 0;
    font-size: 0.9rem;
    line-height: 1.5;
}

@media print {
    .ibox-tools {
        display: none !important;
    }
    
    .summary-card {
        break-inside: avoid;
    }
    
    .kategori-card {
        break-inside: avoid;
        margin-bottom: 15px;
    }
}
</style>
@endpush

@push('scripts')
<script src="{{ asset('js/laporan-kemampuan.js') }}"></script>
<script>
function printLaporan() {
    window.print();
}
</script>
@endpush
