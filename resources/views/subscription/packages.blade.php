@extends('layouts.app')

@section('content')
    @auth
    <div class="back-button-container mt-2">
        <a href="{{ route('user.profile', ['userId' => Auth::user()->id]) }}" class="btn btn-default">
            <i class="fa fa-arrow-left"></i> Kembali
        </a>
    </div>
    @endauth

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="text-center mb-5">
                    <img src="{{ asset('img/logo.png') }}" alt="Logo" class="img-fluid" style="max-width: 100px;">
                    <h2 class="mt-3">Pilih Paket Berlangganan</h2>
                    <p>Akses penuh ke semua fitur persiapan tes BINTARA POLRI</p>
                </div>

                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                @if (session('warning'))
                    <div class="alert alert-warning">
                        {{ session('warning') }}
                    </div>
                @endif

                <div class="row">
                    <!-- Paket Bahasa Inggris -->
                    <div class="col-md-3 mb-4">
                        <div class="package-card h-100">
                            <div class="package-header text-center">
                                <h3 class="mb-0">Paket Bahasa Inggris</h3>
                                <p class="mb-0">Fokus Tes Bahasa Inggris</p>
                            </div>

                            <div class="package-features">
                                <div class="text-center mb-4">
                                    <div class="package-price">Rp 75.000</div>
                                    <p class="text-muted">Berlaku 30 hari</p>
                                </div>

                                <div class="features-list">
                                    <div class="feature-item">
                                        <i class="fa fa-check text-navy"></i> Bank soal lengkap
                                    </div>
                                    <div class="feature-item">
                                        <i class="fa fa-check text-navy"></i> Analisis kemampuan linguistik
                                    </div>
                                    <div class="feature-item">
                                        <i class="fa fa-check text-navy"></i> Timer simulasi ujian
                                    </div>
                                    <div class="feature-item">
                                        <i class="fa fa-check text-navy"></i> Riwayat progress
                                    </div>
                                </div>

                                <div class="button-container">
                                    <a href="{{ route('subscription.process.packages', ['package' => 'bahasa_inggris']) }}"
                                        class="btn btn-outline-primary btn-package">
                                        Pilih Paket
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Paket Pengetahuan Umum -->
                    <div class="col-md-3 mb-4">
                        <div class="package-card h-100">
                            <div class="package-header text-center">
                                <h3 class="mb-0">Paket Pengetahuan Umum</h3>
                                <p class="mb-0">Fokus Tes Pengetahuan Umum</p>
                            </div>

                            <div class="package-features">
                                <div class="text-center mb-4">
                                    <div class="package-price">Rp 75.000</div>
                                    <p class="text-muted">Berlaku 30 hari</p>
                                </div>

                                <div class="features-list">
                                    <div class="feature-item">
                                        <i class="fa fa-check text-navy"></i> Bank soal pengetahuan umum
                                    </div>
                                    <div class="feature-item">
                                        <i class="fa fa-check text-navy"></i> Analisis kemampuan kognitif
                                    </div>
                                    <div class="feature-item">
                                        <i class="fa fa-check text-navy"></i> Timer simulasi ujian
                                    </div>
                                    <div class="feature-item">
                                        <i class="fa fa-check text-navy"></i> Riwayat progress
                                    </div>
                                </div>

                                <div class="button-container">
                                    <a href="{{ route('subscription.process.packages', ['package' => 'pu']) }}"
                                        class="btn btn-outline-info btn-package">
                                        Pilih Paket
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Paket TWK -->
                    <div class="col-md-3 mb-4">
                        <div class="package-card h-100">
                            <div class="package-header text-center">
                                <h3 class="mb-0">Paket TWK</h3>
                                <p class="mb-0">Tes Wawasan Kebangsaan</p>
                            </div>

                            <div class="package-features">
                                <div class="text-center mb-4">
                                    <div class="package-price">Rp 75.000</div>
                                    <p class="text-muted">Berlaku 30 hari</p>
                                </div>

                                <div class="features-list">
                                    <div class="feature-item">
                                        <i class="fa fa-check text-navy"></i> Bank soal TWK
                                    </div>
                                    <div class="feature-item">
                                        <i class="fa fa-check text-navy"></i> Analisis kebangsaan
                                    </div>
                                    <div class="feature-item">
                                        <i class="fa fa-check text-navy"></i> Timer simulasi ujian
                                    </div>
                                    <div class="feature-item">
                                        <i class="fa fa-check text-navy"></i> Riwayat progress
                                    </div>
                                </div>

                                <div class="button-container">
                                    <a href="{{ route('subscription.process.packages', ['package' => 'twk']) }}"
                                        class="btn btn-outline-warning btn-package">
                                        Pilih Paket
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Paket Numerik -->
                    <div class="col-md-3 mb-4">
                        <div class="package-card h-100">
                            <div class="package-header text-center">
                                <h3 class="mb-0">Paket Numerik</h3>
                                <p class="mb-0">Fokus Tes Numerik</p>
                            </div>

                            <div class="package-features">
                                <div class="text-center mb-4">
                                    <div class="package-price">Rp 75.000</div>
                                    <p class="text-muted">Berlaku 30 hari</p>
                                </div>

                                <div class="features-list">
                                    <div class="feature-item">
                                        <i class="fa fa-check text-navy"></i> Bank soal numerik
                                    </div>
                                    <div class="feature-item">
                                        <i class="fa fa-check text-navy"></i> Analisis logika dan angka
                                    </div>
                                    <div class="feature-item">
                                        <i class="fa fa-check text-navy"></i> Timer simulasi ujian
                                    </div>
                                    <div class="feature-item">
                                        <i class="fa fa-check text-navy"></i> Riwayat progress
                                    </div>
                                </div>

                                <div class="button-container">
                                    <a href="{{ route('subscription.process.packages', ['package' => 'numerik']) }}"
                                        class="btn btn-outline-success btn-package">
                                        Pilih Paket
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row justify-content-center">
                    <!-- Paket Lengkap -->
                    <div class="col-md-6 mb-4">
                        <div class="package-card h-100 popular-package">
                            <div class="popular-badge">
                                <span>Terpopuler</span>
                            </div>
                            <div class="package-header text-center">
                                <h3 class="mb-0">Paket Lengkap</h3>
                                <p class="mb-0">Semua tes dalam satu paket</p>
                            </div>

                            <div class="package-features">
                                <div class="text-center mb-4">
                                    <div class="package-price">Rp 250.000</div>
                                    <p class="text-muted">Berlaku 45 hari</p>
                                </div>

                                <div class="features-list">
                                    <div class="feature-item">
                                        <i class="fa fa-check text-navy"></i> Semua kategori AKADEMIK
                                    </div>
                                    <div class="feature-item">
                                        <i class="fa fa-check text-navy"></i> Simulasi penuh
                                    </div>
                                    <div class="feature-item">
                                        <i class="fa fa-check text-navy"></i> Analisis lengkap
                                    </div>
                                    <div class="feature-item">
                                        <i class="fa fa-check text-navy"></i> Riwayat & laporan performa
                                    </div>
                                </div>

                                <div class="button-container">
                                    <a href="{{ route('subscription.process.packages', ['package' => 'lengkap']) }}"
                                        class="btn btn-success btn-package">
                                        Pilih Paket
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <small class="text-muted">
                        Dengan membeli paket ini, Anda menyetujui
                        <a href="#">Syarat & Ketentuan</a> yang berlaku
                    </small>
                </div>
            </div>
        </div>
    </div>

    <style>
        .package-card {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 2rem;
            transition: all 0.3s ease;
            background: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
            position: relative;
            display: flex;
            flex-direction: column;
        }

        .package-card:hover {
            border-color: #18a689;
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 123, 255, 0.15);
        }

        .popular-package {
            border-color: #18a689;
            position: relative;
        }

        .popular-badge {
            position: absolute;
            top: -12px;
            left: 50%;
            transform: translateX(-50%);
            background: linear-gradient(45deg, #18a689, #0f6e58);
            color: white;
            padding: 8px 20px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
        }

        .package-header h3 {
            color: #2c3e50;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .package-header p {
            color: #6c757d;
            font-size: 0.9rem;
        }

        /* Flexbox untuk package-features */
        .package-features {
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        /* Flexbox untuk features-list agar mengisi ruang yang tersisa */
        .features-list {
            flex: 1;
            margin-bottom: 1rem;
        }

        /* Container button di bagian bawah */
        .button-container {
            margin-top: auto;
        }

        .package-price {
            font-size: 2rem;
            font-weight: 700;
            color: #18a689;
            margin-bottom: 0.5rem;
        }

        .feature-item {
            display: flex;
            align-items: center;
            padding: 0.5rem 0;
            font-size: 1rem;
        }

        .feature-item i {
            margin-right: 0.75rem;
            font-size: 1.1rem;
        }

        .btn-success {
            background: linear-gradient(45deg, #28a745, #1e7e34);
            border: none;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
        }

        .btn-outline-info {
            border-color: #17a2b8;
            color: #17a2b8;
        }

        .btn-outline-info:hover {
            background-color: #17a2b8;
            border-color: #17a2b8;
            transform: translateY(-2px);
        }

        .btn-outline-primary {
            border-color: #007bff;
            color: #007bff;
        }

        .btn-outline-primary:hover {
            background-color: #007bff;
            border-color: #007bff;
            transform: translateY(-2px);
        }

        .btn-outline-warning {
            border-color: #ffc107;
            color: #ffc107;
        }

        .btn-outline-warning:hover {
            background-color: #ffc107;
            border-color: #ffc107;
            color: #212529;
            transform: translateY(-2px);
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
            .package-card {
                margin-bottom: 2rem;
            }

            .package-price {
                font-size: 2rem;
            }
        }
    </style>
@endsection
