@extends('layouts.app')

@section('content')
    <div class="back-button-container mt-2">
        <a href="{{ route('subscription.packages') }}" class="btn btn-default">
            <i class="fa fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="text-center mb-5">
                    <img src="{{ asset('img/logo.png') }}" alt="Logo" class="img-fluid" style="max-width: 100px;">
                    <h2 class="mt-3">Checkout Paket Berlangganan</h2>
                    <p>Silakan lakukan pembayaran manual sesuai instruksi di bawah ini</p>
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

                <div class="row">
                    <!-- Detail Paket -->
                    <div class="col-md-6 mb-4">
                        <div class="checkout-card">
                            <div class="card-header">
                                <h4 class="mb-0">
                                    <i class="fa fa-package text-primary"></i> Detail Paket
                                </h4>
                            </div>
                            <div class="card-body">
                                <div class="package-summary">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div>
                                            <h5 class="package-name">{{ $package['name'] }}</h5>
                                            <p class="package-description text-muted mb-0">{{ $package['description'] }}</p>
                                        </div>
                                        <div class="text-right">
                                            <div class="package-price">Rp
                                                {{ number_format($package['price'], 0, ',', '.') }}</div>
                                            <small class="text-muted">Berlaku {{ $package['duration'] }} hari</small>
                                        </div>
                                    </div>

                                    <hr>

                                    <h6 class="mb-3">Fitur yang Didapatkan:</h6>
                                    <div class="features-list">
                                        @foreach ($package['features'] as $feature)
                                            <div class="feature-item">
                                                <i class="fa fa-check text-success"></i>
                                                {{ $feature }}
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Informasi Pengguna -->
                                <hr>
                                <h6 class="mb-3">
                                    <i class="fa fa-user text-info"></i> Informasi Pengguna
                                </h6>
                                <div class="user-info">
                                    <div class="info-item">
                                        <strong>Nama:</strong> {{ $user->name }}
                                    </div>
                                    <div class="info-item">
                                        <strong>Email:</strong> {{ $user->email }}
                                    </div>
                                    <div class="info-item">
                                        <strong>Order ID:</strong>
                                        <span
                                            class="order-id">{{ strtoupper($package['key']) }}-{{ $user->id }}-{{ date('Ymd') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Instruksi Pembayaran -->
                    <div class="col-md-6">
                        <div class="checkout-card">
                            <div class="card-header">
                                <h4 class="mb-0">
                                    <i class="fa fa-credit-card text-warning"></i> Instruksi Pembayaran
                                </h4>
                            </div>
                            <div class="card-body">
                                <!-- Nomor Rekening -->
                                <div class="bank-accounts mb-4">
                                    <h6 class="mb-3">
                                        <i class="fa fa-university text-primary"></i> Transfer ke Rekening Berikut:
                                    </h6>

                                    <div class="bank-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="bank-info">
                                                <img src="https://upload.wikimedia.org/wikipedia/commons/5/5c/Bank_Central_Asia.svg"
                                                    alt="BCA" class="bank-logo">
                                                <div class="bank-details">
                                                    <strong>BCA (Bank Central Asia)</strong><br>
                                                    <span class="account-number">1234567890</span><br>
                                                    <small class="account-name">A.n. BINTARA POLRI ACADEMY</small>
                                                </div>
                                            </div>
                                            <button class="btn btn-sm btn-outline-primary copy-btn"
                                                onclick="copyToClipboard('1234567890')">
                                                <i class="fa fa-copy"></i> Copy
                                            </button>
                                        </div>
                                    </div>

                                    <div class="bank-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="bank-info">
                                                <img src="https://upload.wikimedia.org/wikipedia/commons/2/2e/BNI_logo.svg"
                                                    alt="BNI" class="bank-logo">
                                                <div class="bank-details">
                                                    <strong>BNI (Bank Negara Indonesia)</strong><br>
                                                    <span class="account-number">0987654321</span><br>
                                                    <small class="account-name">A.n. BINTARA POLRI ACADEMY</small>
                                                </div>
                                            </div>
                                            <button class="btn btn-sm btn-outline-primary copy-btn"
                                                onclick="copyToClipboard('0987654321')">
                                                <i class="fa fa-copy"></i> Copy
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Langkah Pembayaran -->
                                <div class="payment-steps mb-4">
                                    <h6 class="mb-3">
                                        <i class="fa fa-list-ol text-success"></i> Langkah Pembayaran:
                                    </h6>
                                    <ol class="steps-list">
                                        <li>Transfer sesuai nominal <strong>Rp
                                                {{ number_format($package['price'], 0, ',', '.') }}</strong></li>
                                        <li>Simpan bukti transfer/struk pembayaran</li>
                                        <li>Kirim bukti transfer via WhatsApp</li>
                                        <li>Tunggu konfirmasi aktivasi paket (1x24 jam)</li>
                                    </ol>
                                </div>

                                <!-- WhatsApp Konfirmasi -->
                                <div class="whatsapp-section">
                                    <div class="whatsapp-box">
                                        <div class="text-center mb-3">
                                            <i class="fab fa-whatsapp whatsapp-icon"></i>
                                            <h6 class="mb-2">Konfirmasi Pembayaran</h6>
                                            <p class="mb-3 text-muted">Kirim bukti transfer ke WhatsApp kami</p>
                                        </div>

                                        <a href="https://wa.me/6282339150860?text={{ urlencode(
                                            'Halo, saya sudah melakukan pembayaran untuk:                                                                                                                                                                                                                                                                                                                                                                      Paket: ' .
                                                $package['name'] .
                                                '                                                                                                                                                                                                                                                                                                                               Order ID: ' .
                                                strtoupper($package['key']) .
                                                '-' .
                                                $user->id .
                                                '-' .
                                                date('Ymd') .
                                                '                                                                                                                                                                                                                                                                                                                  Nama: ' .
                                                $user->name .
                                                '                                                                                                                                                                                                                                                                                                                             Email: ' .
                                                $user->email .
                                                '                                                                                                                                                                                                                                                                                                                                Total: Rp ' .
                                                number_format($package['price'], 0, ',', '.') .
                                                '                                                                                                                                                                                                                                                                                                                                                                        Mohon diaktivasi paketnya. Terima kasih!',
                                        ) }}"
                                            target="_blank" class="btn btn-success btn-lg btn-block whatsapp-btn">
                                            <i class="fab fa-whatsapp"></i> Kirim ke WhatsApp
                                        </a>

                                        <div class="text-center mt-2">
                                            <small class="text-muted">
                                                <i class="fa fa-clock"></i> Respon dalam 1x24 jam
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Catatan Penting -->
                                <div class="alert alert-info mt-4">
                                    <h6><i class="fa fa-info-circle"></i> Catatan Penting:</h6>
                                    <ul class="mb-0">
                                        <li>Transfer harus sesuai dengan nominal yang tertera</li>
                                        <li>Sertakan Order ID saat konfirmasi</li>
                                        <li>Paket akan diaktivasi setelah pembayaran dikonfirmasi</li>
                                        <li>Simpan bukti transfer untuk keperluan klaim</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Status Aktivasi dan Button Continue -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="activation-status-section">
                            <div class="alert alert-warning text-center">
                                <h5><i class="fa fa-clock text-warning"></i> Status Aktivasi Paket</h5>
                                <p class="mb-3">
                                    <strong>Harap menunggu aktivasi paket Anda.</strong><br>
                                    Jika paket sudah aktif, Anda akan diberitahu melalui WhatsApp.
                                </p>
                            </div>

                            <div class="text-center mt-3">
                                <a href="{{ route('user.profile', $user->id) }}" class="btn btn-primary btn-lg">
                                    <i class="fa fa-arrow-right"></i> Continue ke Profile
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bantuan -->
                <div class="row mt-4" style="margin-bottom:50px;">
                    <div class="col-12">
                        <div class="help-section text-center">
                            <h3>Butuh Bantuan?</h3>
                            <p class="text-muted mb-3">Tim customer service kami siap membantu Anda</p>
                            <div class="contact-options">
                                <a href="https://wa.me/6281234567890" target="_blank"
                                    class="btn btn-outline-success me-2">
                                    <i class="fab fa-whatsapp"></i> WhatsApp
                                </a>
                                <a href="mailto:support@bintarapolri.com" class="btn btn-outline-primary">
                                    <i class="fa fa-envelope"></i> Email Support
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .checkout-card {
            border: 1px solid #e9ecef;
            border-radius: 12px;
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .checkout-card .card-header {
            background: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
            padding: 1.25rem;
        }

        .checkout-card .card-body {
            padding: 1.5rem;
        }

        .package-name {
            color: #2c3e50;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .package-price {
            font-size: 1.5rem;
            font-weight: 700;
            color: #18a689;
        }

        .feature-item {
            display: flex;
            align-items: center;
            padding: 0.5rem 0;
            font-size: 0.95rem;
        }

        .feature-item i {
            margin-right: 0.75rem;
            font-size: 1rem;
        }

        .user-info .info-item {
            margin-bottom: 0.5rem;
        }

        .order-id {
            background: #f8f9fa;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            color: #18a689;
            font-weight: 600;
        }

        .payment-total-box {
            background: linear-gradient(135deg, #18a689, #0f6e58);
            color: white;
            padding: 1.5rem;
            border-radius: 8px;
            text-align: center;
        }

        .total-amount {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .bank-item {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            background: #fafafa;
        }

        .bank-info {
            display: flex;
            align-items: center;
        }

        .bank-logo {
            width: 40px;
            height: 40px;
            margin-right: 1rem;
            object-fit: contain;
        }

        .account-number {
            font-family: 'Courier New', monospace;
            font-size: 1.1rem;
            font-weight: 600;
            color: #18a689;
        }

        .account-name {
            color: #6c757d;
        }

        .copy-btn {
            border: 1px solid #18a689;
            color: #18a689;
            transition: all 0.3s ease;
        }

        .copy-btn:hover {
            background: #18a689;
            color: white;
        }

        .steps-list {
            padding-left: 1.5rem;
        }

        .steps-list li {
            margin-bottom: 0.5rem;
            line-height: 1.5;
        }

        .whatsapp-section {
            background: #f0f9f7;
            border: 1px solid #d4edda;
            border-radius: 8px;
            padding: 1.5rem;
        }

        .whatsapp-icon {
            font-size: 3rem;
            color: #25D366;
            margin-bottom: 1rem;
        }

        .whatsapp-btn {
            background: #25D366;
            border: none;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            transition: all 0.3s ease;
        }

        .whatsapp-btn:hover {
            background: #128C7E;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(37, 211, 102, 0.3);
        }

        .help-section {
            background: #f8f9fa;
            padding: 2rem;
            border-radius: 12px;
            border: 1px solid #e9ecef;
        }

        .contact-options .btn {
            margin: 0 0.5rem;
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
            .bank-info {
                flex-direction: column;
                text-align: center;
            }

            .bank-logo {
                margin-bottom: 0.5rem;
                margin-right: 0;
            }

            .contact-options .btn {
                display: block;
                margin: 0.5rem 0;
            }

            .d-flex.justify-content-between {
                flex-direction: column;
                gap: 1rem;
            }
        }
    </style>

    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                // Success notification
                const btn = event.target.closest('.copy-btn');
                const originalText = btn.innerHTML;
                btn.innerHTML = '<i class="fa fa-check"></i> Copied!';
                btn.classList.add('btn-success');
                btn.classList.remove('btn-outline-primary');

                setTimeout(function() {
                    btn.innerHTML = originalText;
                    btn.classList.remove('btn-success');
                    btn.classList.add('btn-outline-primary');
                }, 2000);
            }, function(err) {
                alert('Gagal menyalin nomor rekening. Silakan salin manual: ' + text);
            });
        }

        // Auto-select text when clicked
        document.querySelectorAll('.account-number').forEach(function(element) {
            element.addEventListener('click', function() {
                const range = document.createRange();
                range.selectNodeContents(this);
                const selection = window.getSelection();
                selection.removeAllRanges();
                selection.addRange(range);
            });
        });
    </script>
@endsection
