@extends('layouts.app')

@section('content')
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            {{-- Back Button --}}
            <div class="ibox">
                <div class="ibox-content">
                    <a href="{{ route('user.profile', ['userId' => Auth::user()->id]) }}" class="btn btn-white">
                        <i class="fa fa-arrow-left"></i> Kembali ke Profile
                    </a>
                </div>
            </div>

            {{-- Header Section --}}
            <div class="ibox">
                <div class="ibox-content text-center">
                    <div class="row">
                        <div class="col-md-12">
                            <h2 class="font-bold text-navy">Status Paket Berlangganan</h2>
                            <p class="text-muted">Kelola dan pantau paket berlangganan Anda</p>
                        </div>
                    </div>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success">
                    <i class="fa fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">
                    <i class="fa fa-exclamation-circle"></i> {{ session('error') }}
                </div>
            @endif

            {{-- Status Cards Grid --}}
            <div class="row">
                {{-- Status Paket Card --}}
                <div class="col-lg-6">
                    <div class="ibox">
                        <div class="ibox-content">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="status-icon-container">
                                        <i class="fa fa-{{ $hasActiveSubscription ? 'check' : 'exclamation-triangle' }}-circle fa-4x text-{{ $hasActiveSubscription ? 'success' : 'warning' }}"></i>
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <div class="status-text-container">
                                        <h3 class="m-t-none m-b">{{ $packageDisplayName }}</h3>
                                        <p class="text-{{ $hasActiveSubscription ? 'success' : 'warning' }} font-bold">
                                            @if ($hasActiveSubscription)
                                                <i class="fa fa-check"></i> Aktif
                                            @else
                                                <i class="fa fa-exclamation-triangle"></i> Belum Berlangganan
                                            @endif
                                        </p>
                                        
                                        @if ($hasActiveSubscription && $subscription)
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <small class="text-muted">Mulai:</small><br>
                                                    <strong>{{ $additionalInfo['subscription_start_date']->format('d M Y') }}</strong>
                                                </div>
                                                <div class="col-md-6">
                                                    <small class="text-muted">Berakhir:</small><br>
                                                    <strong>{{ $subscription->end_date->format('d M Y') }}</strong>
                                                    @if($additionalInfo['days_remaining'] > 0)
                                                        <br><span class="text-info">({{ $additionalInfo['days_remaining'] }} hari lagi)</span>
                                                    @else
                                                        <br><span class="text-danger">(Kadaluarsa)</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Statistik Penggunaan Card --}}
                <div class="col-lg-6">
                    <div class="ibox">
                        <div class="ibox-content">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="stats-icon-container">
                                        <i class="fa fa-bar-chart fa-4x text-info"></i>
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <div class="stats-text-container">
                                        <h3 class="m-t-none m-b">Statistik Penggunaan</h3>
                                        <p class="text-muted">Aktivitas dan limit paket Anda</p>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="m-b">
                                                    <small class="text-muted">Tryout Selesai:</small><br>
                                                    <strong class="text-primary">{{ $additionalInfo['total_tryouts_completed'] }}</strong>
                                                    @if ($maxTryouts != 999)
                                                        / {{ $maxTryouts }}
                                                    @endif
                                                </div>
                                                <div class="m-b">
                                                    <small class="text-muted">Soal Dikerjakan:</small><br>
                                                    <strong class="text-success">{{ $additionalInfo['total_questions_answered'] }}</strong>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="m-b">
                                                    <small class="text-muted">Kategori Tersedia:</small><br>
                                                    <strong class="text-info">{{ $allowedCategories ? count($allowedCategories) : 0 }}</strong>
                                                </div>
                                                @if($additionalInfo['last_activity'])
                                                    <div class="m-b">
                                                        <small class="text-muted">Aktivitas Terakhir:</small><br>
                                                        <strong class="text-warning">{{ \Carbon\Carbon::parse($additionalInfo['last_activity'])->format('d M Y H:i') }}</strong>
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

            {{-- Progress Section --}}
            @if ($userPackage === 'lengkap' && $paketLengkapStatus)
            <div class="ibox">
                <div class="ibox-title">
                    <h5><i class="fa fa-trophy"></i> Progress Paket Lengkap</h5>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="progress progress-large">
                                <div class="progress-bar progress-bar-success" style="width: {{ $paketLengkapProgress }}%">
                                    <span class="sr-only">{{ $paketLengkapProgress }}% Complete</span>
                                </div>
                            </div>
                            <p class="text-muted">Penyelesaian: {{ $paketLengkapProgress }}%</p>
                        </div>
                        <div class="col-md-4 text-right">
                            <h2 class="text-success">{{ $paketLengkapProgress }}%</h2>
                        </div>
                    </div>
                    
                    <div class="row m-t-md">
                        <div class="col-md-12">
                            <div class="widget style1 {{ $paketLengkapStatus['akademik']['completed'] ? 'navy-bg' : 'gray-bg' }}">
                                <div class="row">
                                    <div class="col-xs-4 text-center">
                                        <div class="progress-icon-container">
                                            <i class="fa fa-{{ $paketLengkapStatus['akademik']['completed'] ? 'check' : 'times' }}-circle fa-3x"></i>
                                        </div>
                                    </div>
                                    <div class="col-xs-8">
                                        <div class="progress-text-container">
                                            <span class="progress-label">Nilai Akhir</span>
                                            @if($paketLengkapStatus['akademik']['completed'])
                                                <h2 class="font-bold">{{ $paketLengkapStatus['akademik']['score'] }}</h2>
                                                <small style="font-size: 10px; opacity: 0.8;">{{ \Carbon\Carbon::parse($paketLengkapStatus['akademik']['tanggal'])->format('d M Y H:i') }}</small>
                                            @else
                                                <h2 class="font-bold">-</h2>
                                                <small style="font-size: 10px; opacity: 0.8;">{{ $paketLengkapStatus['akademik']['message'] }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Individual Academic Category Cards --}}
                    <div class="row m-t-md">
                        <div class="col-md-3">
                            <div class="widget style1 {{ $paketLengkapStatus['bahasa_inggris']['completed'] ? 'navy-bg' : 'gray-bg' }}">
                                <div class="row">
                                    <div class="col-xs-4 text-center">
                                        <div class="progress-icon-container">
                                            <i class="fa fa-{{ $paketLengkapStatus['bahasa_inggris']['completed'] ? 'check' : 'times' }}-circle fa-2x"></i>
                                        </div>
                                    </div>
                                    <div class="col-xs-8">
                                        <div class="progress-text-container">
                                            <span class="progress-label">Bahasa Inggris</span>
                                            @if($paketLengkapStatus['bahasa_inggris']['completed'])
                                                <h3 class="font-bold">{{ number_format($paketLengkapStatus['bahasa_inggris']['score'], 1) }}</h3>
                                                <small style="font-size: 10px; opacity: 0.8;">{{ \Carbon\Carbon::parse($paketLengkapStatus['bahasa_inggris']['tanggal'])->format('d M Y H:i') }}</small>
                                            @else
                                                <h3 class="font-bold">-</h3>
                                                <small style="font-size: 10px; opacity: 0.8;">Belum selesai</small>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="widget style1 {{ $paketLengkapStatus['pu']['completed'] ? 'navy-bg' : 'gray-bg' }}">
                                <div class="row">
                                    <div class="col-xs-4 text-center">
                                        <div class="progress-icon-container">
                                            <i class="fa fa-{{ $paketLengkapStatus['pu']['completed'] ? 'check' : 'times' }}-circle fa-2x"></i>
                                        </div>
                                    </div>
                                    <div class="col-xs-8">
                                        <div class="progress-text-container">
                                            <span class="progress-label">Pengetahuan Umum</span>
                                            @if($paketLengkapStatus['pu']['completed'])
                                                <h3 class="font-bold">{{ number_format($paketLengkapStatus['pu']['score'], 1) }}</h3>
                                                <small style="font-size: 10px; opacity: 0.8;">{{ \Carbon\Carbon::parse($paketLengkapStatus['pu']['tanggal'])->format('d M Y H:i') }}</small>
                                            @else
                                                <h3 class="font-bold">-</h3>
                                                <small style="font-size: 10px; opacity: 0.8;">Belum selesai</small>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="widget style1 {{ $paketLengkapStatus['twk']['completed'] ? 'navy-bg' : 'gray-bg' }}">
                                <div class="row">
                                    <div class="col-xs-4 text-center">
                                        <div class="progress-icon-container">
                                            <i class="fa fa-{{ $paketLengkapStatus['twk']['completed'] ? 'check' : 'times' }}-circle fa-2x"></i>
                                        </div>
                                    </div>
                                    <div class="col-xs-8">
                                        <div class="progress-text-container">
                                            <span class="progress-label">TWK</span>
                                            @if($paketLengkapStatus['twk']['completed'])
                                                <h3 class="font-bold">{{ number_format($paketLengkapStatus['twk']['score'], 1) }}</h3>
                                                <small style="font-size: 10px; opacity: 0.8;">{{ \Carbon\Carbon::parse($paketLengkapStatus['twk']['tanggal'])->format('d M Y H:i') }}</small>
                                            @else
                                                <h3 class="font-bold">-</h3>
                                                <small style="font-size: 10px; opacity: 0.8;">Belum selesai</small>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="widget style1 {{ $paketLengkapStatus['numerik']['completed'] ? 'navy-bg' : 'gray-bg' }}">
                                <div class="row">
                                    <div class="col-xs-4 text-center">
                                        <div class="progress-icon-container">
                                            <i class="fa fa-{{ $paketLengkapStatus['numerik']['completed'] ? 'check' : 'times' }}-circle fa-2x"></i>
                                        </div>
                                    </div>
                                    <div class="col-xs-8">
                                        <div class="progress-text-container">
                                            <span class="progress-label">Numerik</span>
                                            @if($paketLengkapStatus['numerik']['completed'])
                                                <h3 class="font-bold">{{ number_format($paketLengkapStatus['numerik']['score'], 1) }}</h3>
                                                <small style="font-size: 10px; opacity: 0.8;">{{ \Carbon\Carbon::parse($paketLengkapStatus['numerik']['tanggal'])->format('d M Y H:i') }}</small>
                                            @else
                                                <h3 class="font-bold">-</h3>
                                                <small style="font-size: 10px; opacity: 0.8;">Belum selesai</small>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Features Section --}}
            <div class="ibox">
                <div class="ibox-title">
                    <h5><i class="fa fa-star text-warning"></i> Fitur Paket {{ $packageDisplayName }}</h5>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        @foreach($packageFeatures['features'] as $feature)
                        <div class="col-md-6">
                            <div class="feature-item">
                                <i class="fa fa-check text-success"></i> {{ $feature }}
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="ibox">
                <div class="ibox-content text-center">
                    @if (!$hasActiveSubscription)
                        <a href="{{ route('subscription.packages') }}" class="btn btn-primary btn-lg">
                            <i class="fa fa-gem"></i> Berlangganan Sekarang
                        </a>
                        <p class="text-muted m-t-md">Mulai perjalanan persiapan tes Anda</p>
                    @else
                        <div class="row">
                            @if ($userPackage !== 'lengkap')
                                <div class="col-md-4">
                                    <a href="{{ route('subscription.packages') }}" class="btn btn-success btn-block">
                                        <i class="fa fa-arrow-up"></i> Upgrade Paket
                                    </a>
                                </div>
                            @endif
                            @if ($canAccessTryout)
                                <div class="col-md-4">
                                    <a href="{{ route('user.tryout.index') }}" class="btn btn-info btn-block">
                                        <i class="fa fa-play"></i> Mulai Tryout
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.feature-item {
    padding: 8px 0;
    margin-bottom: 5px;
}

.progress-large {
    height: 20px;
}

.widget.style1 {
    padding: 20px;
    margin-bottom: 20px;
    border-radius: 5px;
}

.widget.style1.navy-bg {
    background-color: #1ab394;
    color: white;
}

.widget.style1.gray-bg {
    background-color: #f3f3f4;
    color: #676a6c;
}

.widget.style1 .font-bold {
    font-weight: 600;
}

.ibox {
    margin-bottom: 20px;
}

.ibox-title {
    border-bottom: 1px solid #e7eaec;
    padding: 15px 20px;
    background-color: #f8f9fa;
}

.ibox-content {
    padding: 20px;
}

.btn-block {
    display: block;
    width: 100%;
}

.m-t-md {
    margin-top: 20px;
}

.m-b {
    margin-bottom: 15px;
}

.text-navy {
    color: #1ab394 !important;
}

.progress-icon-container {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
    min-height: 80px;
}

.progress-text-container {
    padding-left: 15px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    height: 100%;
    min-height: 80px;
}

.progress-label {
    font-size: 14px;
    font-weight: 500;
    margin-bottom: 8px;
    display: block;
}

.widget.style1 .row {
    min-height: 80px;
    align-items: center;
}

.widget.style1 .col-xs-4 {
    padding: 0 10px;
}

.widget.style1 .col-xs-8 {
    padding: 0 10px;
}

.status-icon-container,
.stats-icon-container {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
    min-height: 100px;
}

.status-text-container,
.stats-text-container {
    padding-left: 20px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    height: 100%;
    min-height: 100px;
}
</style>
@endsection

