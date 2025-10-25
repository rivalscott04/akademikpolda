<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Mahir Akademik | Login</title>

    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('font-awesome/css/font-awesome.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    
    <!-- Optimasi: Load animate.css hanya jika diperlukan -->
    <style>
        .animated {
            animation-duration: 1s;
            animation-fill-mode: both;
        }
        .fadeInDown {
            animation-name: fadeInDown;
        }
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translate3d(0, -100%, 0);
            }
            to {
                opacity: 1;
                transform: translate3d(0, 0, 0);
            }
        }
    </style>

</head>

<body class="gray-bg">

    <div class="middle-box loginscreen animated fadeInDown text-center">
        <div>
            <div>
                <img src="{{ asset('img/login-removebg-preview.png') }}" alt="dashboard" class="img-fluid float-right">
            </div>
            <h3>Mahir Akademik</h3>

            @if (session('message'))
                <div class="alert alert-info">
                    {{ session('message') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form class="m-t" role="form" action="{{ route('post.login') }}" method="POST">
                @csrf
                <div class="form-group">
                    <input type="email" name="email" class="form-control" placeholder="Email"
                        value="{{ old('email') }}" required>
                </div>
                <div class="form-group">
                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                </div>
                <button type="submit" class="btn btn-primary full-width m-b block">Masuk</button>

                <a href="{{ route('reset-password') }}">Lupa Password?</a>
                <p class="text-muted text-center">
                    Belum memiliki akun?
                </p>
                <a class="btn btn-sm btn-white btn-block" href="{{ url('register') }}">Buat Akun</a>
            </form>
            <p class="m-t"> <small>Mahir Akademik &copy; 2025</small> </p>
        </div>
    </div>

    <!-- Mainly scripts - Optimasi: Load minimal scripts untuk login -->
    <script src="{{ asset('js/jquery-3.1.1.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.js') }}"></script>
    
    <!-- Optimasi: Defer loading untuk performa -->
    <script>
        // Preload critical resources
        document.addEventListener('DOMContentLoaded', function() {
            // Lazy load popper.js hanya jika diperlukan
            if (typeof Popper === 'undefined') {
                var script = document.createElement('script');
                script.src = '{{ asset("js/popper.min.js") }}';
                script.async = true;
                document.head.appendChild(script);
            }
        });
    </script>

</body>

</html>
