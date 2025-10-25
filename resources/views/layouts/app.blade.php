<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <title>@yield('title', 'Dashboard')</title>

    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('font-awesome/css/font-awesome.css') }}" rel="stylesheet">
    <link href="{{ asset('css/animate.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('css/plugins/dataTables/datatables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/laporan-kemampuan.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: white;
        }

        .logo {
            max-width: 100%;
            height: auto;
            display: none;
            filter: invert(10%) brightness(200%);
        }

        .mini-navbar .logo {
            display: block;
            max-width: 50px;
            margin: 5px auto;
        }

        .mini-navbar .logo-element {
            display: block;
            padding: 10px 0;
        }

        .impersonate-banner {
            background: linear-gradient(45deg, #ffc107, #ff9800);
            color: #000;
            border: none;
            font-weight: 500;
        }

        .impersonate-banner .btn-outline-warning {
            border-color: #000;
            color: #000;
        }

        .impersonate-banner .btn-outline-warning:hover {
            background-color: #000;
            color: #fff;
        }

        /* SweetAlert Custom Styling */
        .swal-wide {
            width: 500px !important;
        }

        .admin-info {
            border-left: 4px solid #ffc107;
        }

        .user-info {
            border-left: 4px solid #28a745;
            padding-left: 10px;
        }

        .swal2-popup {
            font-size: 14px;
        }

        .swal2-confirm {
            margin-right: 10px;
        }

        .stop-impersonate-btn:hover {
            transform: scale(1.05);
            transition: transform 0.2s ease;
        }

        /* Global Impersonate Styling */
        .impersonate-banner {
            border-left: 4px solid #ffc107 !important;
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
            margin: 0;
            border-radius: 0;
            border-left: none;
            border-right: none;
        }

        .stop-impersonate-btn {
            transition: all 0.3s ease;
            border-radius: 6px;
            font-weight: 500;
            border: 2px solid #dc3545;
            color: #dc3545;
            background: transparent;
        }

        .stop-impersonate-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
            background: #dc3545;
            color: white;
        }

        .stop-impersonate-btn i {
            margin-right: 5px;
        }

        /* Fix footer spacing */
        #page-wrapper {
            padding-bottom: 80px !important;
            position: relative;
            min-height: 100vh;
        }

        .footer {
            position: absolute !important;
            bottom: 0 !important;
            left: 0;
            right: 0;
            margin-top: 0;
            border-top: 1px solid #e7eaec;
            background: #f8f9fa;
            padding: 15px 20px;
        }

        .container-fluid {
            margin-bottom: 20px;
        }
    </style>
    @stack('styles')
</head>

<body>
    <div id="wrapper">
        @include('components.sidenav')
        <div id="page-wrapper" class="gray-bg">
            @include('components.topnav')
            @if (session('message'))
                <div class="alert alert-info">
                    {{ session('message') }}
                </div>
            @endif
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('impersonate_taken'))
                <script>
                    document.addEventListener('DOMContentLoaded', () => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Impersonate Berhasil!',
                            text: '{{ session('impersonate_taken') }}',
                            showConfirmButton: true,
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#28a745',
                            timer: 3000,
                            timerProgressBar: true
                        });
                    });
                </script>
            @endif
            @if (session('impersonate_leave'))
                <script>
                    document.addEventListener('DOMContentLoaded', () => {
                        Swal.fire({
                            icon: 'info',
                            title: 'Kembali ke Admin!',
                            text: '{{ session('impersonate_leave') }}',
                            showConfirmButton: true,
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#ffc107',
                            timer: 3000,
                            timerProgressBar: true
                        });
                    });
                </script>
            @endif

            {{-- Impersonate Warning Banner --}}
            @if (auth()->user() && app('impersonate')->isImpersonating())
                <div class="alert alert-warning alert-dismissible fade show impersonate-banner" role="alert"
                    style="margin: 0; border-radius: 0; border-left: none; border-right: none;">
                    <div class="container">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <i class="fa fa-user-secret"></i>
                                <strong>Mode Impersonate Aktif!</strong>
                                Anda sedang login sebagai <strong>{{ auth()->user()->name }}</strong>
                                @if (app('impersonate')->getImpersonator())
                                    (Admin: {{ app('impersonate')->getImpersonator()->name }})
                                @endif
                            </div>
                            <div class="col-md-4 text-right">
                                <button onclick="confirmStopImpersonate()"
                                    class="btn btn-sm btn-outline-danger stop-impersonate-btn">
                                    <i class="fa fa-stop"></i> Stop Impersonate
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            @yield('content')
            @include('components.footer')
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        window.appRoutes = {
            simpanHasil: "/tryout/placeholder/submit-answer",
        };

        // Stop impersonate confirmation function
        function confirmStopImpersonate() {
            Swal.fire({
                title: '<i class="fa fa-stop" style="color: #dc3545;"></i> Stop Impersonate',
                html: `
                    <div class="text-left">
                        <p><strong>Anda akan keluar dari mode impersonate dan kembali ke dashboard admin.</strong></p>
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i>
                            <strong>Info:</strong> Semua perubahan yang dilakukan akan tetap tersimpan.
                        </div>
                    </div>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fa fa-stop"></i> Ya, Stop Impersonate',
                cancelButtonText: '<i class="fa fa-times"></i> Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: 'Memproses...',
                        html: 'Sedang keluar dari mode impersonate...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Redirect to leave impersonation route
                    window.location.href = "{{ route('leave.impersonation') }}";
                }
            });
        }
    </script>
    <script src="{{ asset('js/jquery-3.1.1.min.js') }}"></script>
    <script src="{{ asset('js/popper.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.js') }}"></script>
    <script src="{{ asset('js/generateSoal.js') }}"></script>
    {{-- <script src="{{ asset('js/kecermatanSoal.js') }}"></script> --}}
    @stack('datatables-scripts')
    <script src="{{ asset('js/plugins/metisMenu/jquery.metisMenu.js') }}"></script>
    <script src="{{ asset('js/plugins/slimscroll/jquery.slimscroll.min.js') }}"></script>
    <script src="{{ asset('js/inspinia.js') }}"></script>

    @stack('scripts')
</body>

</html>
