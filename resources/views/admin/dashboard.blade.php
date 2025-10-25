@extends('layouts.app')

@push('styles')
<!-- Dashboard Accordion Custom Styles -->
<link href="{{ asset('css/dashboard-accordion.css') }}" rel="stylesheet">

<style>
.kategori-performance-list {
    max-height: 400px;
    overflow-y: auto;
}

.kategori-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 15px 0;
    border-bottom: 1px solid #f0f0f0;
}

.kategori-item:last-child {
    border-bottom: none;
}

.kategori-info {
    display: flex;
    align-items: center;
    flex: 1;
}

.kategori-color {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    margin-right: 12px;
    flex-shrink: 0;
}

.kategori-details {
    flex: 1;
}

.kategori-details strong {
    display: block;
    margin-bottom: 5px;
    color: #333;
    font-size: 14px;
}

.kategori-stats {
    display: flex;
    gap: 5px;
}

.kategori-stats .badge {
    font-size: 10px;
    padding: 2px 6px;
}

.kategori-score {
    margin-left: 15px;
}

.score-circle {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    background: #e9ecef;
    color: #495057;
    font-weight: bold;
    font-size: 12px;
    transition: all 0.3s ease;
}

.score-circle::before {
    content: '';
    position: absolute;
    top: 3px;
    left: 3px;
    right: 3px;
    bottom: 3px;
    border-radius: 50%;
    background: white;
    z-index: 1;
}

.score-text {
    position: relative;
    z-index: 2;
    text-align: center;
    line-height: 1;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .kategori-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
    
    .kategori-score {
        margin-left: 0;
        align-self: flex-end;
    }
    
    .score-circle {
        width: 40px;
        height: 40px;
        font-size: 11px;
    }
}

/* Chart improvements */
.flot-chart {
    height: 300px;
}

.flot-chart-content {
    width: 100%;
    height: 100%;
}

/* Top performers table improvements */
.table th {
    background-color: #f8f9fa;
    border-top: none;
    font-weight: 600;
    color: #495057;
}

.table td {
    vertical-align: middle;
}

.badge-success {
    background-color: #28a745;
}

.badge-warning {
    background-color: #ffc107;
    color: #212529;
}

.badge-danger {
    background-color: #dc3545;
}

/* Feed activity improvements */
.feed-activity-list {
    max-height: 300px;
    overflow-y: auto;
}

.feed-element {
    padding: 10px 0;
    border-bottom: 1px solid #f0f0f0;
}

.feed-element:last-child {
    border-bottom: none;
}

.media-body strong {
    color: #333;
    font-size: 13px;
}

.media-body small {
    color: #6c757d;
    font-size: 11px;
}

/* Pelanggan Baru Styles */
.stat-item {
    padding: 10px 0;
}

.stat-item h3 {
    margin: 0;
    font-weight: bold;
}

/* Subscription Analysis Styles - Inspinia Theme */
.subscription-card {
    border: 1px solid #e7eaec;
    border-radius: 3px;
    padding: 15px;
    background: #ffffff;
    transition: all 0.3s ease;
    margin-bottom: 15px;
}

.subscription-card:hover {
    box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
    border-color: #1ab394;
}

.subscription-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.package-name {
    margin: 0;
    font-size: 14px;
    font-weight: 600;
    color: #333;
    line-height: 1.4;
}

.package-stats {
    display: flex;
    gap: 5px;
    flex-wrap: wrap;
}

.package-stats .badge {
    font-size: 10px;
    padding: 3px 8px;
    border-radius: 2px;
}

.subscription-users {
    margin-top: 10px;
}

.user-item {
    padding: 5px 0;
    border-bottom: 1px solid #e9ecef;
}

.user-item:last-child {
    border-bottom: none;
}

.user-item strong {
    display: block;
    font-size: 12px;
    color: #333;
}

.user-item small {
    font-size: 10px;
    color: #6c757d;
}

/* Responsive adjustments for new sections */
@media (max-width: 768px) {
    .subscription-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
    
    .package-stats {
        align-self: flex-end;
    }
    
    .stat-item h3 {
        font-size: 1.5rem;
    }
}

</style>
@endpush

@section('content')
    <div class="wrapper wrapper-content">
        {{-- Page Header --}}
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox">
                    <div class="ibox-title">
                        <h3><i class="fa fa-graduation-cap"></i> Dashboard Pendidikan & Ujian</h3>
                        <div class="ibox-tools">
                            <span class="label label-primary">Sistem CBT POLRI</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Accordion Dashboard --}}
        <div class="panel-group" id="dashboardAccordion" role="tablist" aria-multiselectable="true">
            
            {{-- Statistik Utama (Default Open) --}}
            <div class="panel panel-default">
                <div class="panel-heading" role="tab" id="headingStats">
                    <h4 class="panel-title">
                        <a role="button" href="#collapseStats" aria-expanded="true" aria-controls="collapseStats" class="accordion-toggle">
                            <i class="fa fa-dashboard text-primary"></i> <strong>Statistik Utama</strong>
                            <i class="fa fa-chevron-up pull-right accordion-icon"></i>
                        </a>
                    </h4>
                </div>
                <div id="collapseStats" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingStats">
                    <div class="panel-body">
                        {{-- Statistik Utama --}}
                        <div class="row">
                            {{-- Total Soal Card --}}
                            <div class="col-lg-3">
                                <div class="ibox">
                                    <div class="ibox-title">
                                        <div class="ibox-tools">
                                            <span class="label label-success float-right">Aktif</span>
                                        </div>
                                        <h5><i class="fa fa-book"></i> Total Soal</h5>
                                    </div>
                                    <div class="ibox-content">
                                        <h1 class="no-margins">{{ number_format($totalSoal, 0, ',', '.') }}</h1>
                                        <div class="stat-percent text-success font-bold">
                                            {{ $soalGrowth }}% <i class="fa {{ $soalGrowth >= 0 ? 'fa-level-up' : 'fa-level-down' }}"></i>
                                        </div>
                                        <small>Soal dalam bank soal</small>
                                    </div>
                                </div>
                            </div>

                            {{-- Tryout Aktif Card --}}
                            <div class="col-lg-3">
                                <div class="ibox">
                                    <div class="ibox-title">
                                        <div class="ibox-tools">
                                            <span class="label label-info float-right">Berjalan</span>
                                        </div>
                                        <h5><i class="fa fa-tasks"></i> Tryout Aktif</h5>
                                    </div>
                                    <div class="ibox-content">
                                        <h1 class="no-margins">{{ number_format($tryoutAktif, 0, ',', '.') }}</h1>
                                        <div class="stat-percent text-info font-bold">
                                            <i class="fa fa-play-circle"></i>
                                        </div>
                                        <small>Tryout yang sedang berjalan</small>
                                    </div>
                                </div>
                            </div>

                            {{-- Peserta Aktif Card --}}
                            <div class="col-lg-3">
                                <div class="ibox">
                                    <div class="ibox-title">
                                        <div class="ibox-tools">
                                            <span class="label label-warning float-right">Online</span>
                                        </div>
                                        <h5><i class="fa fa-users"></i> Peserta Aktif</h5>
                                    </div>
                                    <div class="ibox-content">
                                        <h1 class="no-margins">{{ number_format($pesertaAktif, 0, ',', '.') }}</h1>
                                        <div class="stat-percent text-warning font-bold">
                                            <i class="fa fa-user-circle"></i>
                                        </div>
                                        <small>Sedang mengerjakan tryout</small>
                                    </div>
                                </div>
                            </div>

                            {{-- Selesai Hari Ini Card --}}
            <div class="col-lg-3">
                <div class="ibox">
                    <div class="ibox-title">
                        <div class="ibox-tools">
                                            <span class="label label-primary float-right">Hari Ini</span>
                                        </div>
                                        <h5><i class="fa fa-check-circle"></i> Selesai Hari Ini</h5>
                                    </div>
                                    <div class="ibox-content">
                                        <h1 class="no-margins">{{ number_format($selesaiHariIni, 0, ',', '.') }}</h1>
                                        <div class="stat-percent text-primary font-bold">
                                            {{ $pesertaGrowth }}% <i class="fa {{ $pesertaGrowth >= 0 ? 'fa-level-up' : 'fa-level-down' }}"></i>
                                        </div>
                                        <small>Tryout selesai hari ini</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Statistik Pelanggan Baru --}}
                        <div class="row">
                            {{-- Pelanggan Baru Hari Ini --}}
                            <div class="col-lg-3">
                                <div class="ibox">
                                    <div class="ibox-title">
                                        <div class="ibox-tools">
                                            <span class="label label-success float-right">Hari Ini</span>
                                        </div>
                                        <h5><i class="fa fa-user-plus"></i> Pelanggan Baru</h5>
                                    </div>
                                    <div class="ibox-content">
                                        <h1 class="no-margins">{{ $pelangganBaruHariIni->count() }}</h1>
                                        <div class="stat-percent text-success font-bold">
                                            <i class="fa fa-plus-circle"></i>
                                        </div>
                                        <small>Registrasi hari ini</small>
                                    </div>
                                </div>
                            </div>

                            {{-- Pelanggan Baru Minggu Ini --}}
                            <div class="col-lg-3">
                                <div class="ibox">
                                    <div class="ibox-title">
                                        <div class="ibox-tools">
                                            <span class="label label-info float-right">Minggu</span>
                                        </div>
                                        <h5><i class="fa fa-calendar"></i> Minggu Ini</h5>
                    </div>
                    <div class="ibox-content">
                                        <h1 class="no-margins">{{ $pelangganBaruMingguIni }}</h1>
                        <div class="stat-percent text-info font-bold">
                                            <i class="fa fa-calendar-week"></i>
                                        </div>
                                        <small>Registrasi minggu ini</small>
                                    </div>
                                </div>
                            </div>

                            {{-- Pelanggan Baru Bulan Ini --}}
                            <div class="col-lg-3">
                                <div class="ibox">
                                    <div class="ibox-title">
                                        <div class="ibox-tools">
                                            <span class="label label-primary float-right">Bulan</span>
                                        </div>
                                        <h5><i class="fa fa-calendar-alt"></i> Bulan Ini</h5>
                                    </div>
                                    <div class="ibox-content">
                                        <h1 class="no-margins">{{ $pelangganBaruBulanIni }}</h1>
                                        <div class="stat-percent text-primary font-bold">
                                            <i class="fa fa-calendar-month"></i>
                        </div>
                                        <small>Registrasi bulan ini</small>
                    </div>
                </div>
            </div>

                            {{-- Total Revenue --}}
            <div class="col-lg-3">
                <div class="ibox">
                    <div class="ibox-title">
                        <div class="ibox-tools">
                                            <span class="label label-warning float-right">Revenue</span>
                                        </div>
                                        <h5><i class="fa fa-money"></i> Revenue 7 Hari</h5>
                                    </div>
                                    <div class="ibox-content">
                                        <h1 class="no-margins">Rp {{ number_format($subscriptionAnalysis->sum('total_revenue'), 0, ',', '.') }}</h1>
                                        <div class="stat-percent text-warning font-bold">
                                            <i class="fa fa-chart-line"></i>
                                        </div>
                                        <small>Pendapatan 7 hari terakhir</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="panel panel-default">
                <div class="panel-heading" role="tab" id="headingActions">
                    <h4 class="panel-title">
                        <a role="button" href="#collapseActions" aria-expanded="false" aria-controls="collapseActions" class="accordion-toggle collapsed">
                            <i class="fa fa-bolt text-warning"></i> <strong>Quick Actions</strong>
                            <i class="fa fa-chevron-down pull-right accordion-icon"></i>
                        </a>
                    </h4>
                </div>
                <div id="collapseActions" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingActions">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-lg-3 col-md-6">
                                <a href="{{ route('admin.tryout.create') }}" class="btn btn-primary btn-block">
                                    <i class="fa fa-plus"></i> Buat Tryout Baru
                                </a>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <a href="{{ route('admin.soal.index') }}" class="btn btn-success btn-block">
                                    <i class="fa fa-upload"></i> Import Soal
                                </a>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <a href="{{ route('admin.kategori.index') }}" class="btn btn-info btn-block">
                                    <i class="fa fa-tags"></i> Manajemen Kategori
                                </a>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <a href="{{ route('admin.users.index') }}" class="btn btn-warning btn-block">
                                    <i class="fa fa-eye"></i> Lihat Hasil Terbaru
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Grafik dan Visualisasi --}}
            <div class="panel panel-default">
                <div class="panel-heading" role="tab" id="headingCharts">
                    <h4 class="panel-title">
                        <a role="button" href="#collapseCharts" aria-expanded="false" aria-controls="collapseCharts" class="accordion-toggle collapsed">
                            <i class="fa fa-line-chart text-info"></i> <strong>Grafik & Visualisasi</strong>
                            <i class="fa fa-chevron-down pull-right accordion-icon"></i>
                        </a>
                    </h4>
                </div>
                <div id="collapseCharts" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingCharts">
                    <div class="panel-body">
                        {{-- Tren Partisipasi --}}
                        <div class="row">
                            <div class="col-lg-8">
                                <div class="ibox">
                                    <div class="ibox-title">
                                        <h5><i class="fa fa-line-chart"></i> Tren Partisipasi Tryout (30 Hari Terakhir)</h5>
                                    </div>
                                    <div class="ibox-content">
                                        <div class="flot-chart">
                                            <div class="flot-chart-content" id="tren-partisipasi-chart"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Performa Kategori --}}
                            <div class="col-lg-4">
                <div class="ibox">
                    <div class="ibox-title">
                                        <h5><i class="fa fa-bar-chart"></i> Performa Kategori</h5>
                                    </div>
                                    <div class="ibox-content">
                                        @if($kategoriPerformansi->count() > 0)
                                            <div class="kategori-performance-list">
                                                @foreach($kategoriPerformansi as $kategori)
                                                    <div class="kategori-item">
                                                        <div class="kategori-info">
                                                            <div class="kategori-color" style="background-color: {{ $kategori['warna'] }}"></div>
                                                            <div class="kategori-details">
                                                                <strong>{{ $kategori['nama'] }}</strong>
                                                                <div class="kategori-stats">
                                                                    <span class="badge badge-primary">{{ $kategori['total_soal'] }} soal</span>
                                                                    <span class="badge badge-info">{{ $kategori['total_peserta'] }} peserta</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="kategori-score">
                                                            <div class="score-circle" data-score="{{ $kategori['rata_skor'] }}">
                                                                <span class="score-text">{{ $kategori['rata_skor'] }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="text-center text-muted">
                                                <i class="fa fa-info-circle fa-2x"></i>
                                                <p>Belum ada data kategori</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Distribusi Skor dan Top Performers --}}
                        <div class="row">
                            {{-- Distribusi Skor --}}
                            <div class="col-lg-6">
                                <div class="ibox">
                                    <div class="ibox-title">
                                        <h5><i class="fa fa-bar-chart"></i> Distribusi Skor Peserta</h5>
                                    </div>
                                    <div class="ibox-content">
                                        <div class="flot-chart">
                                            <div class="flot-chart-content" id="distribusi-skor-chart"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Top Performers --}}
                            <div class="col-lg-6">
                                <div class="ibox">
                                    <div class="ibox-title">
                                        <h5><i class="fa fa-trophy"></i> Top Performers</h5>
                    </div>
                    <div class="ibox-content">
                                        @if($topPerformers->count() > 0)
                                            <div class="table-responsive">
                                                <table class="table table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>Rank</th>
                                                            <th>Nama</th>
                                                            <th>Skor</th>
                                                            <th>Tanggal</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($topPerformers as $index => $performer)
                                                            <tr>
                                                                <td>
                                                                    @if($index == 0)
                                                                        <i class="fa fa-trophy text-warning"></i> 1
                                                                    @elseif($index == 1)
                                                                        <i class="fa fa-medal text-muted"></i> 2
                                                                    @elseif($index == 2)
                                                                        <i class="fa fa-award text-warning"></i> 3
                                                                    @else
                                                                        {{ $index + 1 }}
                                                                    @endif
                                                                </td>
                                                                <td>{{ $performer['nama'] }}</td>
                                                                <td>
                                                                    <span class="badge badge-{{ $performer['skor'] >= 80 ? 'success' : ($performer['skor'] >= 60 ? 'warning' : 'danger') }}">
                                                                        {{ $performer['skor'] }}
                                                                    </span>
                                                                </td>
                                                                <td>{{ $performer['tanggal'] ? \Carbon\Carbon::parse($performer['tanggal'])->format('d/m/Y') : '-' }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <div class="text-center text-muted">
                                                <i class="fa fa-info-circle fa-2x"></i>
                                                <p>Belum ada data performa peserta</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Analisis Subscription --}}
            <div class="panel panel-default">
                <div class="panel-heading" role="tab" id="headingSubscription">
                    <h4 class="panel-title">
                        <a role="button" href="#collapseSubscription" aria-expanded="false" aria-controls="collapseSubscription" class="accordion-toggle collapsed">
                            <i class="fa fa-shopping-cart text-success"></i> <strong>Analisis Langganan</strong>
                            <i class="fa fa-chevron-down pull-right accordion-icon"></i>
                        </a>
                    </h4>
        </div>
                <div id="collapseSubscription" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingSubscription">
                    <div class="panel-body">
                        @if($subscriptionAnalysis->count() > 0)
        <div class="row">
                                @foreach($subscriptionAnalysis as $packageType => $data)
                                    <div class="col-lg-3 col-md-6 mb-3">
                                        <div class="subscription-card">
                                            <div class="subscription-header">
                                                <h6 class="package-name">
                                                    @switch($packageType)
                                                        @case('kecerdasan')
                                                            <i class="fa fa-brain text-primary"></i> Paket Kecerdasan
                                                            @break
                                                        @case('akademik')
                                                            <i class="fa fa-graduation-cap text-info"></i> Paket AKADEMIK
                                                            @break
                                                        @case('lengkap')
                                                            <i class="fa fa-star text-warning"></i> Paket Lengkap
                                                            @break
                                                        @default
                                                            <i class="fa fa-box text-secondary"></i> {{ ucfirst($packageType) }}
                                                    @endswitch
                                                </h6>
                                                <div class="package-stats">
                                                    <span class="badge badge-primary">{{ $data['count'] }} pembeli</span>
                                                    @if($data['total_revenue'] > 0)
                                                        <span class="badge badge-success">Rp {{ number_format($data['total_revenue'], 0, ',', '.') }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                            
                                            @if($data['users']->count() > 0)
                                                <div class="subscription-users">
                                                    <small class="text-muted">Pembeli terbaru:</small>
                                                    @foreach($data['users']->take(3) as $user)
                                                        <div class="user-item">
                                                            <strong>{{ $user['user_name'] }}</strong>
                                                            <small class="text-muted">
                                                                {{ \Carbon\Carbon::parse($user['created_at'])->diffForHumans() }}
                                                                @if($user['amount_paid'] > 0)
                                                                    - Rp {{ number_format($user['amount_paid'], 0, ',', '.') }}
                                                                @endif
                                                            </small>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center text-muted">
                                <i class="fa fa-info-circle fa-2x"></i>
                                <p>Belum ada data subscription dalam 7 hari terakhir</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Tren Pelanggan Baru --}}
            <div class="panel panel-default">
                <div class="panel-heading" role="tab" id="headingTrenPelanggan">
                    <h4 class="panel-title">
                        <a role="button" href="#collapseTrenPelanggan" aria-expanded="false" aria-controls="collapseTrenPelanggan" class="accordion-toggle collapsed">
                            <i class="fa fa-line-chart text-info"></i> <strong>Tren Pelanggan Baru</strong>
                            <i class="fa fa-chevron-down pull-right accordion-icon"></i>
                        </a>
                    </h4>
                </div>
                <div id="collapseTrenPelanggan" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTrenPelanggan">
                    <div class="panel-body">
                <div class="ibox">
                    <div class="ibox-title">
                                <h5><i class="fa fa-line-chart"></i> Tren Pelanggan Baru (7 Hari Terakhir)</h5>
                            </div>
                            <div class="ibox-content">
                                <div class="flot-chart">
                                    <div class="flot-chart-content" id="tren-pelanggan-baru-chart"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Recent Activity --}}
            <div class="panel panel-default">
                <div class="panel-heading" role="tab" id="headingRecent">
                    <h4 class="panel-title">
                        <a role="button" href="#collapseRecent" aria-expanded="false" aria-controls="collapseRecent" class="accordion-toggle collapsed">
                            <i class="fa fa-clock-o text-warning"></i> <strong>Recent Activity</strong>
                            <i class="fa fa-chevron-down pull-right accordion-icon"></i>
                        </a>
                    </h4>
                </div>
                <div id="collapseRecent" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingRecent">
                    <div class="panel-body">
                        <div class="row">
                            {{-- Pelanggan Baru Hari Ini --}}
                            <div class="col-lg-3">
                                <div class="ibox">
                                    <div class="ibox-title">
                                        <h5><i class="fa fa-user-plus"></i> Pelanggan Baru Hari Ini</h5>
                                    </div>
                                    <div class="ibox-content">
                                        @if($pelangganBaruHariIni->count() > 0)
                                            <div class="feed-activity-list">
                                                @foreach($pelangganBaruHariIni->take(5) as $user)
                                                    <div class="feed-element">
                                                        <div class="media-body">
                                                            <strong>{{ $user->name }}</strong><br>
                                                            <small class="text-muted">
                                                                <i class="fa fa-envelope"></i> {{ $user->email }}<br>
                                                                <i class="fa fa-clock-o"></i> {{ $user->created_at->diffForHumans() }}
                                                            </small>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="text-center text-muted">
                                                <i class="fa fa-info-circle"></i>
                                                <p>Belum ada pelanggan baru hari ini</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Tryout Terbaru --}}
                            <div class="col-lg-3">
                                <div class="ibox">
                                    <div class="ibox-title">
                                        <h5><i class="fa fa-clock-o"></i> Tryout Terbaru</h5>
                                    </div>
                                    <div class="ibox-content">
                                        @if($tryoutTerbaru->count() > 0)
                                            <div class="feed-activity-list">
                                                @foreach($tryoutTerbaru as $tryout)
                                                    <div class="feed-element">
                                                        <div class="media-body">
                                                            <strong>{{ $tryout->judul }}</strong><br>
                                                            <small class="text-muted">
                                                                <i class="fa fa-clock-o"></i> {{ $tryout->created_at->diffForHumans() }}
                                                            </small>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="text-center text-muted">
                                                <i class="fa fa-info-circle"></i>
                                                <p>Tidak ada tryout baru</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Soal Terbaru --}}
                            <div class="col-lg-3">
                                <div class="ibox">
                                    <div class="ibox-title">
                                        <h5><i class="fa fa-question-circle"></i> Soal Terbaru</h5>
                                    </div>
                                    <div class="ibox-content">
                                        @if($soalTerbaru->count() > 0)
                                            <div class="feed-activity-list">
                                                @foreach($soalTerbaru as $soal)
                                                    <div class="feed-element">
                                                        <div class="media-body">
                                                            <strong>{{ $soal->kategori->nama ?? 'Kategori Tidak Ditemukan' }}</strong><br>
                                                            <small class="text-muted">
                                                                <i class="fa fa-clock-o"></i> {{ $soal->created_at->diffForHumans() }}
                                                            </small>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="text-center text-muted">
                                                <i class="fa fa-info-circle"></i>
                                                <p>Tidak ada soal baru</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Peserta Selesai Hari Ini --}}
                            <div class="col-lg-3">
                                <div class="ibox">
                                    <div class="ibox-title">
                                        <h5><i class="fa fa-check-circle"></i> Selesai Hari Ini</h5>
                                    </div>
                                    <div class="ibox-content">
                                        @if($pesertaSelesaiHariIni->count() > 0)
                                            <div class="feed-activity-list">
                                                @foreach($pesertaSelesaiHariIni as $peserta)
                                                    <div class="feed-element">
                                                        <div class="media-body">
                                                            <strong>{{ $peserta->user->name }}</strong><br>
                                                            <small class="text-muted">
                                                                <i class="fa fa-clock-o"></i> {{ $peserta->finished_at->diffForHumans() }}
                                                            </small>
                                        </div>
                                        </div>
                                                @endforeach
                                        </div>
                                        @else
                                            <div class="text-center text-muted">
                                                <i class="fa fa-info-circle"></i>
                                                <p>Belum ada yang selesai hari ini</p>
                                        </div>
                                        @endif
                                        </div>
                                        </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/jquery-3.1.1.min.js') }}"></script>
    <script src="{{ asset('js/popper.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.js') }}"></script>
    <script src="{{ asset('js/plugins/metisMenu/jquery.metisMenu.js') }}"></script>
    <script src="{{ asset('js/plugins/slimscroll/jquery.slimscroll.min.js') }}"></script>

    <!-- Flot -->
    <script src="{{ asset('js/plugins/flot/jquery.flot.js') }}"></script>
    <script src="{{ asset('js/plugins/flot/jquery.flot.tooltip.min.js') }}"></script>
    <script src="{{ asset('js/plugins/flot/jquery.flot.spline.js') }}"></script>
    <script src="{{ asset('js/plugins/flot/jquery.flot.resize.js') }}"></script>
    <script src="{{ asset('js/plugins/flot/jquery.flot.pie.js') }}"></script>
    <script src="{{ asset('js/plugins/flot/jquery.flot.symbol.js') }}"></script>
    <script src="{{ asset('js/plugins/flot/jquery.flot.time.js') }}"></script>

    <!-- Peity -->
    <script src="{{ asset('js/plugins/peity/jquery.peity.min.js') }}"></script>
    <script src="{{ asset('js/demo/peity-demo.js') }}"></script>

    <!-- Custom and plugin javascript -->
    <script src="{{ asset('js/inspinia.js') }}"></script>
    <script src="{{ asset('js/plugins/pace/pace.min.js') }}"></script>

    <!-- jQuery UI -->
    <script src="{{ asset('js/plugins/jquery-ui/jquery-ui.min.js') }}"></script>

    <!-- Jvectormap -->
    <script src="{{ asset('js/plugins/jvectormap/jquery-jvectormap-2.0.2.min.js') }}"></script>
    <script src="{{ asset('js/plugins/jvectormap/jquery-jvectormap-world-mill-en.js') }}"></script>

    <!-- EayPIE -->
    <script src="{{ asset('js/plugins/easypiechart/jquery.easypiechart.js') }}"></script>

    <!-- Sparkline -->
    <script src="{{ asset('js/plugins/sparkline/jquery.sparkline.min.js') }}"></script>

    <!-- Sparkline demo data  -->
    <script src="{{ asset('js/demo/sparkline-demo.js') }}"></script>

    <script>
        $(document).ready(function() {
            // === TREN PARTISIPASI CHART ===
            var trenData = @json($trenPartisipasi);
            
            var trenDataset = [{
                label: "Tryout Selesai",
                data: trenData,
                    color: "#1ab394",
                    lines: {
                    lineWidth: 2,
                        show: true,
                        fill: true,
                        fillColor: {
                            colors: [{
                            opacity: 0.1
                        }, {
                            opacity: 0.3
                        }]
                    }
                },
                points: {
                    show: true,
                    radius: 3,
                    fillColor: "#1ab394"
                }
            }];

            var trenOptions = {
                xaxis: {
                    mode: "time",
                    tickSize: [3, "day"],
                    tickLength: 0,
                    axisLabel: "Tanggal",
                    axisLabelUseCanvas: true,
                    axisLabelFontSizePixels: 12,
                    axisLabelFontFamily: "Arial",
                    axisLabelPadding: 10,
                    color: "#d5d5d5"
                },
                yaxis: {
                        position: "left",
                        color: "#d5d5d5",
                        axisLabelUseCanvas: true,
                        axisLabelFontSizePixels: 12,
                        axisLabelFontFamily: "Arial",
                    axisLabelPadding: 3,
                    axisLabel: "Jumlah Peserta"
                },
                legend: {
                    noColumns: 1,
                    labelBoxBorderColor: "#000000",
                    position: "nw"
                },
                grid: {
                    hoverable: true,
                    borderWidth: 0,
                    backgroundColor: "#ffffff"
                },
                tooltip: {
                    show: true,
                    content: "Tanggal: %x<br/>Peserta: %y"
                }
            };

            $.plot($("#tren-partisipasi-chart"), trenDataset, trenOptions);

            // === PERFORMANSI KATEGORI - Animate Score Circles ===
            $('.score-circle').each(function() {
                var $this = $(this);
                var score = parseFloat($this.data('score'));
                var percentage = Math.min(score, 100);
                
                // Animate the circle
                $this.css({
                    'background': 'conic-gradient(from 0deg, #667eea 0deg ' + (percentage * 3.6) + 'deg, #e9ecef ' + (percentage * 3.6) + 'deg 360deg)'
                });
                
                // Add score-based color
                if (score >= 80) {
                    $this.css('background', 'conic-gradient(from 0deg, #28a745 0deg ' + (percentage * 3.6) + 'deg, #e9ecef ' + (percentage * 3.6) + 'deg 360deg)');
                } else if (score >= 60) {
                    $this.css('background', 'conic-gradient(from 0deg, #ffc107 0deg ' + (percentage * 3.6) + 'deg, #e9ecef ' + (percentage * 3.6) + 'deg 360deg)');
                } else {
                    $this.css('background', 'conic-gradient(from 0deg, #dc3545 0deg ' + (percentage * 3.6) + 'deg, #e9ecef ' + (percentage * 3.6) + 'deg 360deg)');
                }
            });

            // === DISTRIBUSI SKOR CHART ===
            var distribusiData = @json($distribusiSkor);
            
            var barData = [
                ["0-39", distribusiData["0-39"] || 0],
                ["40-59", distribusiData["40-59"] || 0],
                ["60-79", distribusiData["60-79"] || 0],
                ["80-100", distribusiData["80-100"] || 0]
            ];

            var barDataset = [{
                label: "Jumlah Peserta",
                data: barData,
                color: "#1c84c6",
                bars: {
                    show: true,
                    align: "center",
                    barWidth: 0.6,
                    lineWidth: 0
                }
            }];

            var barOptions = {
                xaxis: {
                    mode: "categories",
                    tickLength: 0,
                    axisLabel: "Rentang Skor",
                    axisLabelUseCanvas: true,
                    axisLabelFontSizePixels: 12,
                    axisLabelFontFamily: "Arial",
                    axisLabelPadding: 10,
                    color: "#d5d5d5"
                },
                yaxis: {
                    position: "left",
                    color: "#d5d5d5",
                        axisLabelUseCanvas: true,
                        axisLabelFontSizePixels: 12,
                    axisLabelFontFamily: "Arial",
                    axisLabelPadding: 3,
                    axisLabel: "Jumlah Peserta"
                },
                legend: {
                    noColumns: 1,
                    labelBoxBorderColor: "#000000",
                    position: "nw"
                },
                grid: {
                    hoverable: true,
                    borderWidth: 0,
                    backgroundColor: "#ffffff"
                },
                tooltip: {
                    show: true,
                    content: "Skor: %x<br/>Peserta: %y"
                }
            };

            $.plot($("#distribusi-skor-chart"), barDataset, barOptions);

            // === TREN PELANGGAN BARU CHART ===
            var trenPelangganData = @json($trenPelangganBaru);
            
            var trenPelangganDataset = [{
                label: "Pelanggan Baru",
                data: trenPelangganData.map(function(item, index) {
                    return [index, item.count];
                }),
                color: "#17a2b8",
                lines: {
                    lineWidth: 3,
                    show: true,
                    fill: true,
                    fillColor: {
                        colors: [{
                            opacity: 0.1
                        }, {
                            opacity: 0.3
                        }]
                    }
                },
                points: {
                    show: true,
                    radius: 4,
                    fillColor: "#17a2b8"
                }
            }];

            var trenPelangganOptions = {
                xaxis: {
                    ticks: trenPelangganData.map(function(item, index) {
                        return [index, item.date];
                    }),
                    tickLength: 0,
                    axisLabel: "Tanggal",
                    axisLabelUseCanvas: true,
                    axisLabelFontSizePixels: 12,
                    axisLabelFontFamily: "Arial",
                    axisLabelPadding: 10,
                    color: "#d5d5d5"
                },
                yaxis: {
                    position: "left",
                    color: "#d5d5d5",
                    axisLabelUseCanvas: true,
                    axisLabelFontSizePixels: 12,
                    axisLabelFontFamily: "Arial",
                    axisLabelPadding: 3,
                    axisLabel: "Jumlah Pelanggan",
                    min: 0
                },
                legend: {
                    noColumns: 1,
                    labelBoxBorderColor: "#000000",
                    position: "nw"
                },
                grid: {
                    hoverable: true,
                    borderWidth: 0,
                    backgroundColor: "#ffffff"
                },
                tooltip: {
                    show: true,
                    content: "Tanggal: %x<br/>Pelanggan: %y"
                }
            };

            $.plot($("#tren-pelanggan-baru-chart"), trenPelangganDataset, trenPelangganOptions);

            // === RESPONSIVE CHARTS ===
            $(window).resize(function() {
                $.plot($("#tren-partisipasi-chart"), trenDataset, trenOptions);
                $.plot($("#distribusi-skor-chart"), barDataset, barOptions);
                $.plot($("#tren-pelanggan-baru-chart"), trenPelangganDataset, trenPelangganOptions);
            });

        });
    </script>
    
    <!-- Dashboard Accordion Custom Script - Load after jQuery and Bootstrap -->
    <script src="{{ asset('js/dashboard-accordion.js') }}"></script>
@endpush
