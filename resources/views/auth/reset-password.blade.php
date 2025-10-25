<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Mahir Cermat | Reset Password</title>

  <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('font-awesome/css/font-awesome.css') }}" rel="stylesheet">
  <link href="{{ asset('css/animate.css') }}" rel="stylesheet">
  <link href="{{ asset('css/style.css') }}" rel="stylesheet">
</head>

<body class="gray-bg">
  <div class="middle-box loginscreen animated fadeInDown text-center">
    <div>
      <div>
        <img src="{{ asset('img/login-removebg-preview.png') }}" alt="dashboard" class="img-fluid float-right">
      </div>
      <h3>Reset Password</h3>

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

      <form class="m-t" role="form" action="{{ route('post.reset-password') }}" method="POST">
        @csrf
        <div class="form-group">
          <input type="email" name="email" class="form-control" placeholder="Email" value="{{ old('email') }}"
            required>
        </div>
        <div class="form-group">
          <input type="password" name="new_password" class="form-control" placeholder="Password Baru" required>
        </div>
        <div class="form-group">
          <input type="password" name="new_password_confirmation" class="form-control"
            placeholder="Konfirmasi Password Baru" required>
        </div>
        <button type="submit" class="btn btn-primary full-width m-b block">Reset Password</button>

        <a class="btn btn-sm btn-white btn-block" href="{{ route('login') }}">Kembali ke Login</a>
      </form>
      <p class="m-t"> <small>Mahir Cermat &copy; 2025</small> </p>
    </div>
  </div>

  <!-- Mainly scripts -->
  <script src="{{ asset('js/jquery-3.1.1.min.js') }}"></script>
  <script src="{{ asset('js/popper.min.js') }}"></script>
  <script src="{{ asset('js/bootstrap.js') }}"></script>
</body>

</html>
