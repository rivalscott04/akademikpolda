<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mahir Akademik | Pembayaran</title>
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('font-awesome/css/font-awesome.css') }}" rel="stylesheet">
    <link href="{{ asset('css/animate.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">

    <style>
        .back-button-container {
            position: relative;
            width: 100%;
            padding: 20px;
            z-index: 1000;
        }

        .payment-summary {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }

        @media (max-width: 768px) {
            .back-button-container {
                position: sticky;
                top: 0;
            }
        }
    </style>
</head>

<body class="gray-bg">
    <div class="back-button-container">
        <a href="{{ url()->previous() }}" class="btn btn-default">
            <i class="fa fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <div class="mb-4 text-center">
                            <img src="{{ asset('img/regis-removebg-preview.png') }}" alt="dashboard" class="img-fluid"
                                style="max-width: 200px;">
                            <h3 class="mt-3">Pembayaran Paket Cermat</h3>
                        </div>

                        <div class="payment-summary">
                            <h5>Ringkasan Pembayaran</h5>
                            <div class="row mt-3">
                                <div class="col-6">Paket</div>
                                <div class="col-6 text-end">Paket Cermat</div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-6">Total Pembayaran</div>
                                <div class="col-6 text-end">
                                    <strong>Rp {{ number_format($subscription->amount_paid, 0, ',', '.') }}</strong>
                                </div>
                            </div>
                        </div>

                        <div class="text-center">
                            <button id="pay-button" class="btn btn-primary btn-lg">
                                Bayar Sekarang
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/jquery-3.1.1.min.js') }}"></script>
    <script src="{{ asset('js/popper.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.js') }}"></script>
    <script src="{{ config('midtrans.snap_url') }}" data-client-key="{{ config('midtrans.client_key') }}"></script>
    <script>
        document.getElementById('pay-button').onclick = function() {
            snap.pay('{{ $snap_token }}', {
                onSuccess: function(result) {
                    window.location.href = '{{ route('subscription.finish') }}';
                },
                onPending: function(result) {
                    window.location.href = '{{ route('subscription.unfinish') }}';
                },
                onError: function(result) {
                    window.location.href = '{{ route('subscription.error') }}';
                },
                onClose: function() {
                    alert('Anda menutup popup tanpa menyelesaikan pembayaran');
                }
            });
        };
    </script>
</body>
