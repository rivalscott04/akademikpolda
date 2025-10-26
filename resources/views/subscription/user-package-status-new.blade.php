@extends('layouts.app')

@section('content')
<div class="back-button-container mt-3">
    <a href="{{ route('user.profile', ['userId' => Auth::user()->id]) }}" class="btn btn-outline-secondary">
        <i class="fa fa-arrow-left me-2"></i> Kembali
    </a>
</div>

<div class="container-fluid px-4 py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            {{-- Header Section --}}
            <div class="header-section mb-5">
                <div class="d-flex align-items-center">
                    <div class="header-icon me-3">
                        <i class="fa fa-gem fa-2x text-primary"></i>
                    </div>
                    <div>
                        <h1 class="mb-2 fw-bold text-dark">Status Paket Berlangganan</h1>
                        <p class="text-muted mb-0">Kelola dan pantau paket berlangganan Anda</p>
                    </div>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fa fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fa fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Status Cards Grid --}}
            <div class="row g-4 mb-5">
                {{-- Status Paket Card --}}
                <div class="col-lg-6">
                    <div class="card status-card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="status-icon me-3">
                                    <i class="fa fa-{{ $hasActiveSubscription ? 'check' : 'exclamation-triangle' }}-circle fa-2x text-{{ $hasActiveSubscription ? 'success' : 'warning' }}"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1 text-{{ $hasActiveSubscription ? 'success' : 'warning' }} text-uppercase fw-bold">
                                        Status Langganan
                                    </h6>
                                    <h4 class="mb-0 fw-bold text-dark">
                                        @if ($hasActiveSubscription)
                                            {{ $packageDisplayName }}
                                        @else
                                            Belum Berlangganan
                                        @endif
                                    </h4>
                                </div>
                            </div>
                            @if ($hasActiveSubscription && $subscription && $subscription->end_date)
                                <div class="subscription-info">
                                    <div class="d-flex align-items-center text-muted">
                                        <i class="fa fa-calendar me-2"></i>
                                        <div>
                                            <small>Berakhir: {{ $subscription->end_date->format('d M Y') }}</small>
                                            <br><small class="text-muted">({{ $subscription->end_date->diffForHumans() }})</small>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Limit & Kuota Card --}}
                <div class="col-lg-6">
                    <div class="card stats-card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="stats-icon me-3">
                                    <i class="fa fa-bar-chart fa-2x text-primary"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1 text-primary text-uppercase fw-bold">
                                        Limit & Kuota
                                    </h6>
                                    <p class="mb-0 text-muted">Penggunaan paket Anda</p>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="stat-item text-center">
                                        <div class="stat-number h4 mb-1 fw-bold text-primary">
                                            @if ($maxTryouts == 999)
                                                ∞
                                            @else
                                                {{ $maxTryouts }}
                                            @endif
                                        </div>
                                        <small class="text-muted">Max Tryout</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="stat-item text-center">
                                        <div class="stat-number h4 mb-1 fw-bold text-success">{{ $allowedCategories ? count($allowedCategories) : 0 }}</div>
                                        <small class="text-muted">Kategori</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Progress Section --}}
            @if ($userPackage === 'lengkap' && $paketLengkapStatus)
            <div class="row mb-5">
                <div class="col-12">
                    <div class="card progress-card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div>
                                    <h5 class="mb-1 fw-bold text-dark">Progress Paket Lengkap</h5>
                                    <p class="mb-0 text-muted">Lacak penyelesaian tes Anda</p>
                                </div>
                                <div class="progress-percentage">
                                    <span class="h3 mb-0 fw-bold text-success">{{ $paketLengkapProgress }}%</span>
                                </div>
                            </div>
                            <div class="progress mb-3" style="height: 12px;">
                                <div class="progress-bar bg-success" role="progressbar" 
                                     style="width: {{ $paketLengkapProgress }}%">
                                </div>
                            </div>
                            <div class="row g-3">
                                {{-- Card Bahasa Inggris --}}
                                <div class="col-md-3">
                                    <div class="progress-item">
                                        <div class="d-flex align-items-center">
                                            <i class="fa fa-{{ $paketLengkapStatus['bahasa_inggris']['completed'] ? 'check' : 'times' }}-circle me-2 text-{{ $paketLengkapStatus['bahasa_inggris']['completed'] ? 'success' : 'danger' }}"></i>
                                            <span class="fw-medium">Bahasa Inggris</span>
                                        </div>
                                        @if($paketLengkapStatus['bahasa_inggris']['completed'])
                                            <small class="text-muted">Skor: {{ number_format($paketLengkapStatus['bahasa_inggris']['score'], 1) }}</small>
                                        @else
                                            <small class="text-muted">-</small>
                                        @endif
                                    </div>
                                </div>
                                
                                {{-- Card PU --}}
                                <div class="col-md-3">
                                    <div class="progress-item">
                                        <div class="d-flex align-items-center">
                                            <i class="fa fa-{{ $paketLengkapStatus['pu']['completed'] ? 'check' : 'times' }}-circle me-2 text-{{ $paketLengkapStatus['pu']['completed'] ? 'success' : 'danger' }}"></i>
                                            <span class="fw-medium">PU</span>
                                        </div>
                                        @if($paketLengkapStatus['pu']['completed'])
                                            <small class="text-muted">Skor: {{ number_format($paketLengkapStatus['pu']['score'], 1) }}</small>
                                        @else
                                            <small class="text-muted">-</small>
                                        @endif
                                    </div>
                                </div>
                                
                                {{-- Card TWK --}}
                                <div class="col-md-3">
                                    <div class="progress-item">
                                        <div class="d-flex align-items-center">
                                            <i class="fa fa-{{ $paketLengkapStatus['twk']['completed'] ? 'check' : 'times' }}-circle me-2 text-{{ $paketLengkapStatus['twk']['completed'] ? 'success' : 'danger' }}"></i>
                                            <span class="fw-medium">TWK</span>
                                        </div>
                                        @if($paketLengkapStatus['twk']['completed'])
                                            <small class="text-muted">Skor: {{ number_format($paketLengkapStatus['twk']['score'], 1) }}</small>
                                        @else
                                            <small class="text-muted">-</small>
                                        @endif
                                    </div>
                                </div>
                                
                                {{-- Card Numerik --}}
                                <div class="col-md-3">
                                    <div class="progress-item">
                                        <div class="d-flex align-items-center">
                                            <i class="fa fa-{{ $paketLengkapStatus['numerik']['completed'] ? 'check' : 'times' }}-circle me-2 text-{{ $paketLengkapStatus['numerik']['completed'] ? 'success' : 'danger' }}"></i>
                                            <span class="fw-medium">Numerik</span>
                                        </div>
                                        @if($paketLengkapStatus['numerik']['completed'])
                                            <small class="text-muted">Skor: {{ number_format($paketLengkapStatus['numerik']['score'], 1) }}</small>
                                        @else
                                            <small class="text-muted">-</small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            {{-- Final Score Section - hanya muncul jika semua paket selesai --}}
                            @if($paketLengkapStatus['is_complete'] && $paketLengkapStatus['final_score'])
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="card border-success">
                                        <div class="card-body text-center">
                                            <h5 class="card-title text-success">
                                                <i class="fa fa-trophy"></i> Nilai Akhir Paket Lengkap
                                            </h5>
                                            <h2 class="text-success mb-2">{{ number_format($paketLengkapStatus['final_score'], 2) }}</h2>
                                            <p class="text-muted mb-0">
                                                Formula: (Skor × Bobot) ÷ 4
                                            </p>
                                            @if(isset($paketLengkapStatus['scoring_info']))
                                            <div class="mt-3">
                                                <span class="badge badge-{{ $paketLengkapStatus['scoring_info']['passed'] ? 'success' : 'danger' }} badge-lg">
                                                    {{ $paketLengkapStatus['scoring_info']['passed'] ? 'LULUS' : 'TIDAK LULUS' }}
                                                </span>
                                                <small class="text-muted ml-2">
                                                    Standar: {{ $paketLengkapStatus['scoring_info']['passing_grade'] }}
                                                </small>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Features Section --}}
            <div class="row mb-5">
                <div class="col-12">
                    <div class="card features-card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h5 class="mb-4 fw-bold text-dark">
                                <i class="fa fa-star me-2 text-warning"></i>
                                Fitur Paket {{ $packageDisplayName }}
                            </h5>
                            <div class="row g-3">
                                @foreach($packageFeatures['features'] as $feature)
                                <div class="col-md-6">
                                    <div class="feature-item d-flex align-items-center">
                                        <i class="fa fa-check-circle me-2 text-success"></i>
                                        <span>{{ $feature }}</span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="row">
                <div class="col-12">
                    <div class="d-flex gap-3 justify-content-center">
                        @if (!$hasActiveSubscription)
                            <a href="{{ route('subscription.packages') }}" class="btn btn-primary btn-lg px-4">
                                <i class="fa fa-gem me-2"></i>Berlangganan Sekarang
                            </a>
                        @else
                            @if ($userPackage !== 'lengkap')
                                <a href="{{ route('subscription.packages') }}" class="btn btn-success btn-lg px-4">
                                    <i class="fa fa-arrow-up me-2"></i>Upgrade Paket
                                </a>
                            @endif
                            @if ($canAccessTryout)
                                <a href="{{ route('user.tryout.index') }}" class="btn btn-info btn-lg px-4">
                                    <i class="fa fa-play me-2"></i>Mulai Tryout
                                </a>
                            @endif
                            @if ($canAccessKecermatan)
                                <a href="{{ route('kecermatan.index') }}" class="btn btn-warning btn-lg px-4">
                                    <i class="fa fa-tachometer me-2"></i>Tes Kecermatan
                                </a>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.status-card, .stats-card, .progress-card, .features-card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.status-card:hover, .stats-card:hover, .progress-card:hover, .features-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important;
}

.status-icon, .stats-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(0,123,255,0.1);
}

.stats-icon {
    background: rgba(40,167,69,0.1);
}

.header-icon {
    width: 80px;
    height: 80px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(0,123,255,0.1);
}

.stat-number {
    font-size: 1.5rem;
}

.progress-item {
    padding: 12px;
    border-radius: 8px;
    background: rgba(0,0,0,0.02);
}

.feature-item {
    padding: 8px 0;
}

@media (max-width: 768px) {
    .container-fluid {
        padding-left: 1rem;
        padding-right: 1rem;
    }
    
    .header-section h1 {
        font-size: 1.5rem;
    }
    
    .btn-lg {
        font-size: 1rem;
        padding: 0.75rem 1.5rem;
    }
}
</style>
@endsection
