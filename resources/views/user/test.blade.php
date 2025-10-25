@extends('layouts.app')

@section('content')
    <div class="back-button-container mt-2">
        <a href="{{ route('user.profile', ['userId' => Auth::user()->id]) }}" class="btn btn-default">
            <i class="fa fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="text-center mb-5">
                    <img src="{{ asset('img/logo.png') }}" alt="Logo" class="img-fluid" style="max-width: 100px;">
                    <h2 class="mt-3">Pilih Jenis Tes</h2>
                    <p>Akses tes sesuai dengan paket berlangganan Anda</p>
                </div>

                <div class="row justify-content-center">

                    {{-- Card Tryout AKADEMIK --}}
                    <div class="col-md-3 mb-4">
                        <div class="test-card h-100 {{ !Auth::user()->canAccessTryout() ? 'disabled-card' : '' }}">
                            @if (!Auth::user()->canAccessTryout())
                                <div class="lock-overlay">
                                    <i class="fa fa-lock"></i>
                                </div>
                            @endif
                            <div class="test-icon-container">
                                <i class="fa fa-graduation-cap test-icon"></i>
                            </div>
                            <div class="test-content">
                                <h3 class="test-title">Tryout AKADEMIK</h3>
                                <p class="test-description">
                                    Bank soal akademik lengkap dengan berbagai kategori dan level kesulitan.
                                    Latihan soal unlimited dengan sistem CAT (Computer Adaptive Testing).
                                </p>
                                <div class="test-features">
                                    <div class="feature-badge">
                                        <i class="fa fa-clock-o"></i> Timer Simulasi
                                    </div>
                                    <div class="feature-badge">
                                        <i class="fa fa-chart-line"></i> Analisis Progress
                                    </div>
                                    <div class="feature-badge">
                                        <i class="fa fa-infinity"></i> Latihan Unlimited
                                    </div>
                                </div>
                            </div>
                            <div class="test-button-container">
                                @if (Auth::user()->canAccessTryout())
                                    <a href="{{ route('user.tryout.index') }}" class="btn btn-test-primary">
                                        <i class="fa fa-play"></i> Mulai Tryout AKADEMIK
                                    </a>
                                @else
                                    <button class="btn btn-disabled" disabled>
                                        <i class="fa fa-lock"></i> Butuh Paket AKADEMIK
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Card Tes Kecerdasan (Tryout CBT filtered) --}}
                    <div class="col-md-3 mb-4">
                        @php
                            $canAccessKecerdasan = in_array(Auth::user()->paket_akses, ['free', 'kecerdasan', 'lengkap']);
                        @endphp
                        <div class="test-card h-100 {{ !$canAccessKecerdasan ? 'disabled-card' : '' }}">
                            @if (!$canAccessKecerdasan)
                                <div class="lock-overlay">
                                    <i class="fa fa-lock"></i>
                                </div>
                            @endif
                            <div class="test-icon-container">
                                <i class="fa fa-brain test-icon"></i>
                            </div>
                            <div class="test-content">
                                <h3 class="test-title">Tes Kecerdasan</h3>
                                <p class="test-description">
                                    Tryout CBT fokus kecerdasan umum. Materi logika, numerik, dan verbal.
                                </p>
                                <div class="test-features">
                                    <div class="feature-badge">
                                        <i class="fa fa-calculator"></i> Numerik
                                    </div>
                                    <div class="feature-badge">
                                        <i class="fa fa-comments-o"></i> Verbal
                                    </div>
                                    <div class="feature-badge">
                                        <i class="fa fa-cube"></i> Logika
                                    </div>
                                </div>
                            </div>
                            <div class="test-button-container">
                                @if ($canAccessKecerdasan)
                                    <a href="{{ route('user.tryout.index', ['type' => 'kecerdasan']) }}" class="btn btn-test-success">
                                        <i class="fa fa-play"></i> Mulai Tes Kecerdasan
                                    </a>
                                @else
                                    <button class="btn btn-disabled" disabled>
                                        <i class="fa fa-lock"></i> Butuh Paket Kecerdasan
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Card Tes Kepribadian (Tryout CBT filtered) --}}
                    <div class="col-md-3 mb-4">
                        @php
                            $canAccessKepribadian = in_array(Auth::user()->paket_akses, ['free', 'kepribadian', 'lengkap']);
                        @endphp
                        <div class="test-card h-100 {{ !$canAccessKepribadian ? 'disabled-card' : '' }}">
                            @if (!$canAccessKepribadian)
                                <div class="lock-overlay">
                                    <i class="fa fa-lock"></i>
                                </div>
                            @endif
                            <div class="test-icon-container">
                                <i class="fa fa-graduation-cap test-icon"></i>
                            </div>
                            <div class="test-content">
                                <h3 class="test-title">Tes Kepribadian</h3>
                                <p class="test-description">
                                    Tryout CBT dengan tes kepribadian dan karakter. Simulasi wawancara
                                    dengan analisis profil lengkap.
                                </p>
                                <div class="test-features">
                                    <div class="feature-badge">
                                        <i class="fa fa-user"></i> Tes Kepribadian
                                    </div>
                                    <div class="feature-badge">
                                        <i class="fa fa-comments"></i> Simulasi Wawancara
                                    </div>
                                    <div class="feature-badge">
                                        <i class="fa fa-file-text"></i> Profil Psikologi
                                    </div>
                                </div>
                            </div>
                            <div class="test-button-container">
                                @if ($canAccessKepribadian)
                                    <a href="{{ route('user.tryout.index', ['type' => 'kepribadian']) }}" class="btn btn-test-success">
                                        <i class="fa fa-play"></i> Mulai Tes Kepribadian
                                    </a>
                                @else
                                    <button class="btn btn-disabled" disabled>
                                        <i class="fa fa-lock"></i> Butuh Paket Kepribadian
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Card Tes Lengkap - Hanya tampil untuk user yang bukan paket lengkap --}}
                    @if (Auth::user()->paket_akses !== 'lengkap')
                    <div class="col-md-3 mb-4">
                        @php
                            $canAccessLengkap = Auth::user()->canAccessTryout() && Auth::user()->paket_akses === 'free';
                        @endphp
                        <div class="test-card h-100 {{ !$canAccessLengkap ? 'disabled-card' : '' }}">
                            @if (!$canAccessLengkap)
                                <div class="lock-overlay">
                                    <i class="fa fa-lock"></i>
                                </div>
                            @endif
                            <div class="test-icon-container">
                                <i class="fa fa-star test-icon"></i>
                            </div>
                            <div class="test-content">
                                <h3 class="test-title">Tes Lengkap</h3>
                                <p class="test-description">
                                    Paket lengkap yang menggabungkan tes kecermatan dan psikologi.
                                    Simulasi ujian komprehensif dengan analisis mendalam.
                                </p>
                                <div class="test-features">
                                    <div class="feature-badge">
                                        <i class="fa fa-eye"></i> Tes Kecermatan
                                    </div>
                                    <div class="feature-badge">
                                        <i class="fa fa-graduation-cap"></i> Tes Psikologi
                                    </div>
                                    <div class="feature-badge">
                                        <i class="fa fa-trophy"></i> Analisis Komprehensif
                                    </div>
                                </div>
                            </div>
                            <div class="test-button-container">
                                @if ($canAccessLengkap)
                                    <a href="{{ route('user.tryout.index', ['type' => 'lengkap']) }}" class="btn btn-test-warning">
                                        <i class="fa fa-play"></i> Mulai Tes Lengkap
                                    </a>
                                @else
                                    <button class="btn btn-disabled" disabled>
                                        <i class="fa fa-lock"></i> Butuh Paket Lengkap
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Info paket berlangganan jika ada yang terkunci --}}
                @if (!Auth::user()->canAccessTryout())
                    <div class="row justify-content-center mt-4">
                        <div class="col-md-10">
                            <div class="subscription-info-card">
                                <div class="text-center">
                                    <i class="fa fa-info-circle text-info"
                                        style="font-size: 2rem; margin-bottom: 1rem;"></i>
                                    <h5>Upgrade Paket untuk Akses Lebih Lengkap</h5>
                                    <p class="text-muted mb-3">
                                        Dapatkan akses ke semua jenis tes dengan berlangganan paket premium.
                                        Nikmati fitur lengkap untuk persiapan ujian yang maksimal.
                                    </p>
                                    <a href="{{ route('subscription.packages') }}" class="btn btn-primary">
                                        <i class="fa fa-shopping-cart"></i> Lihat Paket Berlangganan
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="text-center mt-4">
                    <small class="text-muted">
                        Pastikan koneksi internet stabil untuk pengalaman tes yang optimal
                    </small>
                </div>
            </div>
        </div>
    </div>

    <style>
        .test-card {
            border: 2px solid #e9ecef;
            border-radius: 16px;
            padding: 2rem;
            transition: all 0.3s ease;
            background: white;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            display: flex;
            flex-direction: column;
            position: relative;
            overflow: hidden;
        }

        .test-card:hover {
            border-color: #18a689;
            transform: translateY(-8px);
            box-shadow: 0 12px 30px rgba(24, 166, 137, 0.15);
        }

        .test-card:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(45deg, #18a689, #0f6e58);
        }

        .test-icon-container {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .test-icon {
            font-size: 3rem;
            color: #18a689;
            padding: 1rem;
            border: 3px solid #e9f7f4;
            border-radius: 50%;
            background: linear-gradient(135deg, #f8fffe, #e9f7f4);
            transition: all 0.3s ease;
        }

        .test-card:hover .test-icon {
            color: white;
            background: linear-gradient(135deg, #18a689, #0f6e58);
            border-color: #18a689;
            transform: scale(1.1);
        }

        .test-content {
            flex: 1;
            text-align: center;
        }

        .test-title {
            color: #2c3e50;
            font-weight: 700;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .test-description {
            color: #6c757d;
            font-size: 1rem;
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }

        .test-features {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 0.5rem;
            margin-bottom: 2rem;
        }

        .feature-badge {
            background: #f8f9fa;
            color: #495057;
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
            border: 1px solid #e9ecef;
            transition: all 0.3s ease;
        }

        .test-card:hover .feature-badge {
            background: #e9f7f4;
            border-color: #18a689;
            color: #0f6e58;
        }

        .feature-badge i {
            margin-right: 0.4rem;
        }

        .test-button-container {
            margin-top: auto;
            text-align: center;
        }

        .btn-test-primary {
            background: linear-gradient(45deg, #007bff, #0056b3);
            border: none;
            color: white;
            padding: 0.8rem 2rem;
            border-radius: 25px;
            font-weight: 600;
            font-size: 1rem;
            box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-test-primary:hover {
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 123, 255, 0.4);
            text-decoration: none;
        }

        .btn-test-success {
            background: linear-gradient(45deg, #28a745, #1e7e34);
            border: none;
            color: white;
            padding: 0.8rem 2rem;
            border-radius: 25px;
            font-weight: 600;
            font-size: 1rem;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-test-success:hover {
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
            text-decoration: none;
        }

        .btn-test-warning {
            background: linear-gradient(45deg, #ffc107, #e0a800);
            border: none;
            color: #212529;
            padding: 0.8rem 2rem;
            border-radius: 25px;
            font-weight: 600;
            font-size: 1rem;
            box-shadow: 0 4px 15px rgba(255, 193, 7, 0.3);
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-test-warning:hover {
            color: #212529;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 193, 7, 0.4);
            text-decoration: none;
        }

        .disabled-card {
            opacity: 0.6;
            position: relative;
        }

        .disabled-card .test-icon,
        .disabled-card .test-title,
        .disabled-card .test-description,
        .disabled-card .feature-badge {
            color: #6c757d !important;
        }

        .disabled-card:hover {
            border-color: #e9ecef;
            transform: none;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .disabled-card:hover .test-icon {
            color: #6c757d !important;
            background: #f8f9fa !important;
            border-color: #e9ecef !important;
            transform: none;
        }

        .disabled-card .feature-badge {
            background: #f1f3f4 !important;
            border-color: #e9ecef !important;
            color: #6c757d !important;
        }

        .disabled-card:hover .feature-badge {
            background: #f1f3f4 !important;
            border-color: #e9ecef !important;
            color: #6c757d !important;
        }

        .lock-overlay {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(108, 117, 125, 0.9);
            color: white;
            padding: 0.5rem;
            border-radius: 50%;
            font-size: 1rem;
            z-index: 5;
        }

        .btn-disabled {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            color: #6c757d;
            padding: 0.8rem 2rem;
            border-radius: 25px;
            font-weight: 600;
            font-size: 1rem;
            cursor: not-allowed;
        }

        .subscription-info-card {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border: 1px solid #dee2e6;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .btn-default {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            color: #495057;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            transition: all 0.3s ease;
        }

        .btn-default:hover {
            background: #e9ecef;
            color: #495057;
            text-decoration: none;
        }

        @media (max-width: 768px) {
            .test-card {
                margin-bottom: 2rem;
            }

            .test-features {
                flex-direction: column;
                align-items: center;
            }

            .feature-badge {
                margin: 0.2rem 0;
            }

            .btn-test-primary,
            .btn-test-success,
            .btn-test-warning {
                padding: 0.7rem 1.5rem;
                font-size: 0.9rem;
            }
        }

        @media (max-width: 576px) {
            .col-md-4 {
                margin-bottom: 1.5rem;
            }

            .test-card {
                padding: 1.5rem;
            }

            .test-icon {
                font-size: 2.5rem;
                padding: 0.8rem;
            }

            .test-title {
                font-size: 1.3rem;
            }
        }
    </style>
@endsection

