@extends('layouts.app')

@section('title', 'User Profile')

@section('page-title', 'Profil User')

@push('breadcrumbs')
    <li class="breadcrumb-item active">
        <strong>Profil User</strong>
    </li>
@endpush

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        {{-- Error Messages for Subscription/Package Issues --}}
        @if (session('subscriptionError'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="fa fa-exclamation-triangle"></i>
                <strong>Langganan Diperlukan!</strong> {{ session('subscriptionError') }}
                <a href="{{ route('subscription.packages') }}" class="btn btn-sm btn-primary ml-2">
                    <i class="fa fa-shopping-cart"></i> Berlangganan Sekarang
                </a>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if (session('packageError'))
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="fa fa-info-circle"></i>
                <strong>Upgrade Paket!</strong> {{ session('packageError') }}
                <a href="{{ route('subscription.packages') }}" class="btn btn-sm btn-success ml-2">
                    <i class="fa fa-arrow-up"></i> Upgrade Paket
                </a>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        {{-- Profile Header --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary text-white"
                                    style="width: 100px; height: 100px; font-size: 2rem;">
                                    <span class="font-weight-bold">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                                </div>
                            </div>
                            <div class="col">
                                <h2 class="mb-1 font-weight-bold">{{ $user->name }}</h2>
                                <p class="text-muted mb-2">
                                    <i class="fa fa-envelope mr-2"></i>{{ $user->email }}
                                </p>
                                <div class="d-flex align-items-center">
                                    @if ($user->hasActiveSubscription())
                                        <span class="badge badge-success badge-lg mr-2">
                                            <i class="fa fa-check-circle mr-1"></i>Langganan Aktif
                                        </span>
                                    @else
                                        <span class="badge badge-warning badge-lg mr-2">
                                            <i class="fa fa-exclamation-triangle mr-1"></i>Belum Berlangganan
                                        </span>
                                    @endif
                                    @if ($user->package && $user->package !== 'free')
                                        <span class="badge badge-info badge-lg">
                                            <i class="fa fa-crown mr-1"></i>{{ $user->getPackageDisplayName() }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-auto">
                                @if (!$user->hasActiveSubscription())
                                    <a href="{{ route('subscription.packages') }}" class="btn btn-primary btn-lg">
                                        <i class="fa fa-rocket mr-2"></i>Berlangganan
                                    </a>
                                @elseif ($user->package !== 'lengkap')
                                    <a href="{{ route('subscription.packages') }}" class="btn btn-outline-primary">
                                        <i class="fa fa-arrow-up mr-2"></i>Upgrade Paket
                                    </a>
                                @else
                                    <span class="badge badge-success badge-lg">
                                        <i class="fa fa-crown mr-2"></i>Paket Tertinggi
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            {{-- Quick Stats Sidebar --}}
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fa fa-chart-bar mr-2"></i>Statistik
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center mb-3">
                            <div class="col-6">
                                <div class="border-right">
                                    <h3 class="text-primary mb-1">{{ $user->hasilTes()->count() }}</h3>
                                    <small class="text-muted">Total Tes</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <h3 class="text-success mb-1">{{ $user->userTryoutSoal()->where('sudah_dijawab', true)->count() }}</h3>
                                <small class="text-muted">Soal Dikerjakan</small>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="mb-3">
                            <h6 class="text-muted mb-2">Paket Akses</h6>
                            @if ($user->package && $user->package !== 'free')
                                <span class="badge badge-info badge-lg w-100 py-2">
                                    <i class="fa fa-crown mr-2"></i>{{ $user->getPackageDisplayName() }}
                                </span>
                            @else
                                <span class="badge badge-secondary badge-lg w-100 py-2">
                                    <i class="fa fa-user mr-2"></i>Free
                                </span>
                            @endif
                        </div>

                        <div class="mb-3">
                            <h6 class="text-muted mb-2">Status Langganan</h6>
                            @if ($user->hasActiveSubscription())
                                <div class="text-success">
                                    <i class="fa fa-check-circle mr-2"></i>Aktif
                                </div>
                                @if ($subscription)
                                    <small class="text-muted">
                                        Sampai {{ date('d M Y', strtotime($subscription->end_date)) }}
                                    </small>
                                @endif
                            @else
                                <div class="text-warning">
                                    <i class="fa fa-exclamation-triangle mr-2"></i>Tidak Aktif
                                </div>
                            @endif
                        </div>

                        <hr>

                        <div>
                            @if (!$user->hasActiveSubscription())
                                <a href="{{ route('subscription.packages') }}" class="btn btn-primary btn-sm btn-block mb-2">
                                    <i class="fa fa-shopping-cart mr-2"></i>Berlangganan
                                </a>
                            @elseif ($user->package !== 'lengkap')
                                <a href="{{ route('subscription.packages') }}" class="btn btn-outline-primary btn-sm btn-block mb-2">
                                    <i class="fa fa-arrow-up mr-2"></i>Upgrade Paket
                                </a>
                            @else
                                <div class="text-center mb-2">
                                    <span class="badge badge-success badge-lg">
                                        <i class="fa fa-crown mr-2"></i>Paket Tertinggi
                                    </span>
                                </div>
                            @endif
                            
                            @if ($user->hasilTes()->count() > 0)
                                <a href="#" class="btn btn-outline-info btn-sm btn-block" onclick="showTestHistory()">
                                    <i class="fa fa-history mr-2"></i>Riwayat Tes
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Main Content with Tabs --}}
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" id="profileTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="personal-tab" data-toggle="tab" href="#personal" role="tab">
                                    <i class="fa fa-user mr-2"></i>Informasi Pribadi
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="subscription-tab" data-toggle="tab" href="#subscription" role="tab">
                                    <i class="fa fa-credit-card mr-2"></i>Langganan
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="history-tab" data-toggle="tab" href="#history" role="tab">
                                    <i class="fa fa-history mr-2"></i>Riwayat Tes
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="profileTabsContent">
                            {{-- Personal Information Tab --}}
                            <div class="tab-pane fade show active" id="personal" role="tabpanel">
                                <form id="profile-form" action="{{ route('profile.update') }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    <div id="update-status" class="alert" style="display: none;"></div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="font-weight-bold">Nama Lengkap</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fa fa-user"></i></span>
                                                    </div>
                                                    <input type="text" class="form-control" name="name" value="{{ $user->name }}" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="font-weight-bold">Email</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fa fa-envelope"></i></span>
                                                    </div>
                                                    <input type="email" class="form-control" name="email" value="{{ $user->email }}" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="font-weight-bold">Nomor Telepon</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fa fa-phone"></i></span>
                                                    </div>
                                                    <input type="tel" class="form-control" name="phone_number" value="{{ $user->phone_number }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="font-weight-bold">Provinsi</label>
                                                <select id="province" name="province" class="form-control">
                                                    <option value="" {{ !$user->province ? 'selected' : '' }} disabled>Pilih Provinsi</option>
                                                    @if(isset($provinces) && count($provinces) > 0)
                                                        @foreach ($provinces as $province)
                                                            <option value="{{ $province['id'] }}"
                                                                {{ $user->province == $province['name'] ? 'selected' : '' }}>
                                                                {{ $province['name'] }}
                                                            </option>
                                                        @endforeach
                                                    @else
                                                        <option disabled>Gagal memuat data provinsi</option>
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="font-weight-bold">Kota/Kabupaten</label>
                                                <select id="regency" name="regency" class="form-control" {{ !$user->province ? 'disabled' : '' }}>
                                                    <option value="" {{ !$user->regency ? 'selected' : '' }} disabled>Pilih Kota/Kabupaten</option>
                                                    @if ($regencies)
                                                        @foreach ($regencies as $regency)
                                                            <option value="{{ $regency['id'] }}"
                                                                {{ $user->regency == $regency['name'] ? 'selected' : '' }}>
                                                                {{ $regency['name'] }}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-right">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa fa-save mr-2"></i>Simpan Perubahan
                                        </button>
                                    </div>
                                </form>
                            </div>

                            {{-- Subscription Tab --}}
                            <div class="tab-pane fade" id="subscription" role="tabpanel">
                                @if ($user->hasActiveSubscription())
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="card border-left-success">
                                                <div class="card-body">
                                                    <div class="row no-gutters align-items-center">
                                                        <div class="col mr-2">
                                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Status Langganan</div>
                                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                                <i class="fa fa-check-circle mr-2"></i>Aktif
                                                            </div>
                                                        </div>
                                                        <div class="col-auto">
                                                            <i class="fa fa-check-circle fa-2x text-success"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card border-left-info">
                                                <div class="card-body">
                                                    <div class="row no-gutters align-items-center">
                                                        <div class="col mr-2">
                                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Berlaku Sampai</div>
                                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                                {{ $subscription ? date('d M Y', strtotime($subscription->end_date)) : 'Tidak Ada' }}
                                                            </div>
                                                        </div>
                                                        <div class="col-auto">
                                                            <i class="fa fa-calendar fa-2x text-info"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-3">
                                        <div class="col-md-6">
                                            <div class="card border-left-warning">
                                                <div class="card-body">
                                                    <div class="row no-gutters align-items-center">
                                                        <div class="col mr-2">
                                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Metode Pembayaran</div>
                                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                                {{ $subscription ? ucfirst($subscription->payment_method) : 'Tidak Ada' }}
                                                            </div>
                                                        </div>
                                                        <div class="col-auto">
                                                            <i class="fa fa-credit-card fa-2x text-warning"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card border-left-primary">
                                                <div class="card-body">
                                                    <div class="row no-gutters align-items-center">
                                                        <div class="col mr-2">
                                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Detail Pembayaran</div>
                                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                                @if ($subscription && $subscription->payment_details)
                                                                    @php
                                                                        $paymentDetails = json_decode($subscription->payment_details, true);
                                                                    @endphp
                                                                    @if (isset($paymentDetails['bank']))
                                                                        {{ strtoupper($paymentDetails['bank']) }}
                                                                    @elseif(isset($paymentDetails['e_wallet']))
                                                                        {{ strtoupper($paymentDetails['e_wallet']) }}
                                                                    @elseif(isset($paymentDetails['payment_type']))
                                                                        {{ ucfirst($paymentDetails['payment_type']) }}
                                                                    @else
                                                                        Tidak Ada
                                                                    @endif
                                                                @else
                                                                    Tidak Ada
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="col-auto">
                                                            <i class="fa fa-info-circle fa-2x text-primary"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    @if($user->paket_akses === 'lengkap')
                                        <div class="row mt-3" id="paket-lengkap-summary" data-user-id="{{ $user->id }}">
                                            <div class="col-12">
                                                <div class="card border-left-danger">
                                                    <div class="card-body">
                                                        <div class="d-flex align-items-center">
                                                            <i class="fa fa-spinner fa-spin text-danger mr-3"></i>
                                                            <div>
                                                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Memuat Ringkasan Paket Lengkap…</div>
                                                                <small class="text-muted">Data akan tampil sebentar lagi.</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @else
                                    <div class="text-center py-5">
                                        <i class="fa fa-credit-card text-muted" style="font-size: 4rem;"></i>
                                        <h4 class="mt-3 text-muted">Belum Berlangganan</h4>
                                        <p class="text-muted">Daftar langganan untuk mengakses semua fitur premium</p>
                                        <a href="{{ route('subscription.packages') }}" class="btn btn-primary btn-lg">
                                            <i class="fa fa-rocket mr-2"></i>Pilih Paket
                                        </a>
                                    </div>
                                @endif
                            </div>

                            {{-- Test History Tab --}}
                            <div class="tab-pane fade" id="history" role="tabpanel">
                                @if ($user->hasilTes()->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>Jenis Tes</th>
                                                    <th>Tanggal</th>
                                                    <th>Skor</th>
                                                    <th>Status</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($user->hasilTes()->latest()->take(10)->get() as $hasil)
                                                    <tr>
                                                        <td>
                                                            @if($hasil->jenis_tes === 'bahasa_inggris')
                                                                <span class="badge badge-info text-white">
                                                                    <i class="fa fa-language"></i> Bahasa Inggris
                                                                </span>
                                                            @elseif($hasil->jenis_tes === 'pu')
                                                                <span class="badge badge-primary text-white">
                                                                    <i class="fa fa-book"></i> Pengetahuan Umum
                                                                </span>
                                                            @elseif($hasil->jenis_tes === 'twk')
                                                                <span class="badge badge-warning text-white">
                                                                    <i class="fa fa-flag"></i> Tes Wawasan Kebangsaan
                                                                </span>
                                                            @elseif($hasil->jenis_tes === 'numerik')
                                                                <span class="badge badge-secondary text-white">
                                                                    <i class="fa fa-calculator"></i> Penalaran Numerik
                                                                </span>
                                                            @elseif($hasil->jenis_tes === 'lengkap')
                                                                <span class="badge badge-danger text-white">
                                                                    <i class="fa fa-star"></i> Paket Lengkap
                                                                </span>
                                                            @elseif($hasil->jenis_tes === 'kecermatan')
                                                                <span class="badge badge-primary text-white">
                                                                    <i class="fa fa-eye"></i> {{ ucfirst($hasil->jenis_tes) }}
                                                                </span>
                                                            @else
                                                                <span class="badge badge-light text-dark">
                                                                    {{ ucfirst($hasil->jenis_tes) }}
                                                                </span>
                                                            @endif
                                                        </td>
                                                        <td>{{ date('d M Y H:i', strtotime($hasil->tanggal_tes)) }}</td>
                                                        <td>
                                                            @if ($hasil->skor_akhir)
                                                                <span class="font-weight-bold text-primary">{{ $hasil->skor_akhir }}
                                                                @if(in_array($hasil->jenis_tes, ['bahasa_inggris', 'pu', 'twk', 'numerik', 'lengkap']))
                                                                    %
                                                                @endif
                                                                </span>
                                                            @else
                                                                <span class="text-muted">-</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($hasil->kategori_skor)
                                                                @php
                                                                    $badgeClass = 'badge-secondary';
                                                                    switch($hasil->kategori_skor) {
                                                                        case 'Sangat Tinggi':
                                                                            $badgeClass = 'badge-success';
                                                                            break;
                                                                        case 'Tinggi':
                                                                            $badgeClass = 'badge-success';
                                                                            break;
                                                                        case 'Cukup Tinggi':
                                                                        case 'Cukup':
                                                                            $badgeClass = 'badge-info';
                                                                            break;
                                                                        case 'Sedang':
                                                                            $badgeClass = 'badge-warning';
                                                                            break;
                                                                        case 'Rendah':
                                                                            $badgeClass = 'badge-danger';
                                                                            break;
                                                                        case 'Sangat Rendah':
                                                                            $badgeClass = 'badge-danger';
                                                                            break;
                                                                        case 'Baik':
                                                                            $badgeClass = 'badge-info';
                                                                            break;
                                                                        default:
                                                                            $badgeClass = 'badge-secondary';
                                                                    }
                                                                @endphp
                                                                <span class="badge {{ $badgeClass }}">{{ $hasil->kategori_skor }}</span>
                                                            @else
                                                                <span class="badge badge-secondary">Belum Dinilai</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <button class="btn btn-sm btn-outline-info" onclick="viewTestDetail({{ $hasil->id }})">
                                                                <i class="fa fa-eye"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center py-5">
                                        <i class="fa fa-clipboard-list text-muted" style="font-size: 4rem;"></i>
                                        <h4 class="mt-3 text-muted">Belum Ada Riwayat Tes</h4>
                                        <p class="text-muted">Mulai mengerjakan tes untuk melihat riwayat di sini</p>
                                        <a href="{{ route('user.dashboard') }}" class="btn btn-primary">
                                            <i class="fa fa-play mr-2"></i>Mulai Tes
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    /* Badge styles for test types - simple and consistent */
    .badge {
        font-size: 0.75em;
        padding: 0.4em 0.6em;
        border-radius: 0.25rem;
        font-weight: 500;
    }
    
    .badge i {
        margin-right: 0.3em;
    }
    
    /* Consistent colors for each test type */
    .badge-primary {
        background-color: #007bff;
        color: white;
    }
    
    .badge-success {
        background-color: #28a745;
        color: white;
    }
    
    .badge-warning {
        background-color: #ffc107;
        color: #212529;
    }
    
    .badge-info {
        background-color: #17a2b8;
        color: white;
    }
    
    .badge-danger {
        background-color: #dc3545;
        color: white;
    }
    
    .badge-secondary {
        background-color: #6c757d;
        color: white;
    }
    .border-left-success {
        border-left: 4px solid #28a745 !important;
    }
    .border-left-info {
        border-left: 4px solid #17a2b8 !important;
    }
    .border-left-warning {
        border-left: 4px solid #ffc107 !important;
    }
    .border-left-primary {
        border-left: 4px solid #007bff !important;
    }
    .border-left-danger {
        border-left: 4px solid #dc3545 !important;
    }
    .text-gray-800 {
        color: #5a5c69 !important;
    }
    .badge-lg {
        font-size: 0.9rem;
        padding: 0.5rem 1rem;
    }
    .card {
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15) !important;
    }
    .nav-tabs .nav-link {
        border: none;
        border-bottom: 2px solid transparent;
    }
    .nav-tabs .nav-link.active {
        border-bottom-color: #007bff;
        background: none;
    }
    .nav-tabs .nav-link:hover {
        border-bottom-color: #007bff;
    }
</style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const provinceDropdown = document.getElementById('province');
            const regencyDropdown = document.getElementById('regency');
            const profileForm = document.getElementById('profile-form');
            const updateStatus = document.getElementById('update-status');
            const submitButton = document.querySelector('button[type="submit"]');

            // Handle province selection change
            if (provinceDropdown) {
                provinceDropdown.addEventListener('change', async function() {
                    const provinceId = this.value;
                    regencyDropdown.innerHTML = '<option selected disabled>Memuat data...</option>';
                    regencyDropdown.disabled = true;

                    try {
                        const response = await fetch(`/api/regencies/${provinceId}`);
                        const regencies = await response.json();

                        regencyDropdown.innerHTML = '<option selected disabled>Pilih Kota/Kabupaten</option>';
                        regencies.forEach(regency => {
                            const option = document.createElement('option');
                            option.value = regency.id;
                            option.textContent = regency.name;
                            regencyDropdown.appendChild(option);
                        });
                        regencyDropdown.disabled = false;
                    } catch (error) {
                        console.error('Error fetching regencies:', error);
                        regencyDropdown.innerHTML = '<option selected disabled>Gagal memuat data</option>';
                    }
                });
            }

            // Handle form submission
            if (profileForm) {
                profileForm.addEventListener('submit', async function(e) {
                    e.preventDefault();

                    const formData = new FormData(this);
                    formData.append('_method', 'PUT');

                    // Disable submit button
                    if (submitButton) {
                        submitButton.disabled = true;
                        submitButton.innerHTML = '<i class="fa fa-spinner fa-spin mr-2"></i>Menyimpan...';
                    }

                    try {
                        const response = await fetch(this.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                            }
                        });

                        const result = await response.json();

                        if (!response.ok) {
                            throw new Error(result.message || `HTTP error! status: ${response.status}`);
                        }

                        updateStatus.className = 'alert alert-success';
                        updateStatus.innerHTML = '<i class="fa fa-check-circle mr-2"></i>' + result.message;
                        updateStatus.style.display = 'block';

                        setTimeout(() => {
                            updateStatus.style.display = 'none';
                        }, 3000);
                    } catch (error) {
                        console.error('Error detail:', error);
                        updateStatus.className = 'alert alert-danger';
                        updateStatus.innerHTML = '<i class="fa fa-exclamation-triangle mr-2"></i>Terjadi kesalahan: ' + error.message;
                        updateStatus.style.display = 'block';
                    } finally {
                        // Enable submit button
                        if (submitButton) {
                            submitButton.disabled = false;
                            submitButton.innerHTML = '<i class="fa fa-save mr-2"></i>Simpan Perubahan';
                        }
                    }
                });
            }

            // Auto-hide alerts after 5 seconds
            const alerts = document.querySelectorAll('.alert-dismissible');
            alerts.forEach(alert => {
                setTimeout(() => {
                    if (alert && alert.parentNode) {
                        alert.style.opacity = '0';
                        setTimeout(() => {
                            alert.remove();
                        }, 300);
                    }
                }, 5000);
            });
        });

        // Lazy load Paket Lengkap summary
        (function() {
            const container = document.getElementById('paket-lengkap-summary');
            if (!container) return;
            const userId = container.getAttribute('data-user-id');
            if (!userId) return;
            fetch(`/profile/${userId}/paket-lengkap-summary`)
                .then(async (res) => {
                    const data = await res.json().catch(() => ({ success: false }));
                    if (!res.ok || !data.success || !data.data) {
                        container.innerHTML = `
                            <div class="col-12">
                                <div class="alert alert-warning mb-0">
                                    <i class="fa fa-exclamation-triangle mr-2"></i>Gagal memuat ringkasan paket lengkap.
                                </div>
                            </div>`;
                        return;
                    }
                    const s = data.data;
                    const isCompleted = s.status === 'completed';
                    const isPassed = s.passed === true;
                    const borderColor = isCompleted ? (isPassed ? 'success' : 'warning') : 'danger';
                    const textColor = isCompleted ? (isPassed ? 'success' : 'warning') : 'danger';
                    const iconColor = isCompleted ? (isPassed ? 'success' : 'warning') : 'danger';
                    const buttonClass = isCompleted ? (isPassed ? 'outline-success' : 'outline-warning') : 'outline-danger';
                    
                    container.innerHTML = `
                        <div class="col-12">
                            <div class="card border-left-${borderColor}">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-${textColor} text-uppercase mb-1">${s.title || 'Paket Lengkap'}</div>
                                            <div class="h6 mb-1 font-weight-bold text-gray-800">${s.message || ''}</div>
                                            ${s.final_score ? `<div class=\"h4 mb-0 font-weight-bold text-${textColor}\">Skor Akhir: ${s.final_score}</div>` : ''}
                                            ${isCompleted && s.passed !== undefined ? `
                                                <div class="mt-2">
                                                    <span class="badge badge-${isPassed ? 'success' : 'danger'} badge-lg">${isPassed ? 'LULUS' : 'TIDAK LULUS'}</span>
                                                    <small class="text-muted ml-2">Standar: ${s.passing_grade || 'N/A'}</small>
                                                </div>
                                            ` : ''}
                                            <div class="progress mt-2" style="height: 8px;">
                                                <div class="progress-bar bg-${borderColor}" role="progressbar" style="width: ${(s.progress || 0)}%"></div>
                                            </div>
                                            <small class="text-muted">${s.progress || 0}% selesai</small>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fa fa-trophy fa-2x text-${iconColor}"></i>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <a href="${`{{ route('user.tryout.paket-lengkap.status') }}`}" class="btn btn-sm btn-${buttonClass}">
                                            <i class="fa fa-eye"></i> Lihat Detail
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>`;
                })
                .catch(() => {
                    container.innerHTML = `
                        <div class="col-12">
                            <div class="alert alert-warning mb-0">
                                <i class="fa fa-exclamation-triangle mr-2"></i>Gagal memuat ringkasan paket lengkap.
                            </div>
                        </div>`;
                });
        })();

        // Function to show test history tab
        function showTestHistory() {
            const historyTab = document.getElementById('history-tab');
            if (historyTab) {
                historyTab.click();
            }
        }

        // Function to view test detail
        function viewTestDetail(testId) {
            window.location.href = '/test-result/' + testId;
        }
    </script>
@endpush
