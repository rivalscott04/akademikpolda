<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <title>@yield('title', 'Tryout')</title>

    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('font-awesome/css/font-awesome.css') }}" rel="stylesheet">
    <link href="{{ asset('css/animate.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('css/plugins/dataTables/datatables.min.css') }}" rel="stylesheet">
    
    <style>
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
    </style>
    
    @stack('styles')
</head>

<body class="tryout-mode">

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

            {{-- Impersonate Warning Banner --}}
            @if(auth()->user() && app('impersonate')->isImpersonating())
                <div class="alert alert-warning alert-dismissible fade show impersonate-banner" role="alert" style="margin: 0; border-radius: 0; border-left: none; border-right: none;">
                    <div class="container">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <i class="fa fa-user-secret"></i>
                                <strong>Mode Impersonate Aktif!</strong> 
                                Anda sedang login sebagai <strong>{{ auth()->user()->name }}</strong>
                                @if(app('impersonate')->getImpersonator())
                                    (Admin: {{ app('impersonate')->getImpersonator()->name }})
                                @endif
                            </div>
                            <div class="col-md-4 text-right">
                                <button onclick="confirmStopImpersonate()" class="btn btn-sm btn-outline-danger stop-impersonate-btn">
                                    <i class="fa fa-stop"></i> Stop Impersonate
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Tryout Content Container -->
            <div class="tryout-content">
                @yield('content')
            </div>
            
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
    <script src="{{ asset('js/plugins/dataTables/datatables.min.js') }}"></script>
    <script src="{{ asset('js/plugins/metisMenu/jquery.metisMenu.js') }}"></script>
    <script src="{{ asset('js/plugins/slimscroll/jquery.slimscroll.min.js') }}"></script>
    <script src="{{ asset('js/inspinia.js') }}"></script>
    
    <!-- Tryout Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Show welcome message for first-time users
            if (!sessionStorage.getItem('tryoutWelcomeShown')) {
                setTimeout(() => {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Selamat Datang di Mode Tryout!',
                            html: `
                                <div class="text-left">
                                    <p><strong>Tips untuk pengalaman optimal:</strong></p>
                                    <ul class="list-unstyled">
                                        <li><i class="fa fa-mouse-pointer text-success"></i> Klik nomor soal untuk navigasi</li>
                                        <li><i class="fa fa-save text-warning"></i> Tekan <kbd>Ctrl+S</kbd> untuk simpan jawaban</li>
                                        <li><i class="fa fa-flag text-info"></i> Gunakan tombol 'Tandai' untuk menandai soal</li>
                                    </ul>
                                </div>
                            `,
                            icon: 'info',
                            confirmButtonText: 'Mengerti',
                            confirmButtonColor: '#3085d6',
                            showCloseButton: true
                        });
                    }
                    sessionStorage.setItem('tryoutWelcomeShown', 'true');
                }, 1000);
            }
            
            // Ctrl+S to save answer (only when not in input fields)
            document.addEventListener('keydown', function(e) {
                if (e.ctrlKey && e.key === 's' && !e.target.matches('input, textarea, select')) {
                    e.preventDefault();
                    const saveBtn = document.querySelector('button[type="submit"]');
                    if (saveBtn) saveBtn.click();
                }
            });
        });
    </script>
    
    @stack('scripts')
</body>

</html> 