<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mahir Cermat | Buat Akun</title>
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('font-awesome/css/font-awesome.css') }}" rel="stylesheet">
    <link href="{{ asset('css/plugins/iCheck/custom.css') }}" rel="stylesheet">
    <link href="{{ asset('css/animate.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    

    <style>
        .back-button-container {
            position: relative;
            width: 100%;
            padding: 20px;
            z-index: 1000;
        }

        @media (max-width: 768px) {
            .back-button-container {
                position: sticky;
                top: 0;
            }
        }

        .form-control.is-invalid {
            border-color: #dc3545;
        }

        .form-control.is-valid {
            border-color: #28a745;
        }

        .invalid-feedback {
            display: block;
            width: 100%;
            margin-top: 0.25rem;
            font-size: 0.875rem;
            color: #dc3545;
        }

        .valid-feedback {
            display: block;
            width: 100%;
            margin-top: 0.25rem;
            font-size: 0.875rem;
            color: #28a745;
        }

        .form-control:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
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
                            <h3 class="mt-3">Buat Akun Mahir Cermat</h3>
                        </div>

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif


                        <form class="m-t" role="form" method="POST" action="{{ route('post.register') }}" id="registerForm">
                            @csrf

                            <!-- Honeypot field untuk anti-bot -->
                            <input type="text" name="website" style="display:none" tabindex="-1" autocomplete="off">

                            <!-- Data Diri -->
                            <h5 class="mb-3">Data Diri</h5>
                            <div class="mb-4">
                                <div class="form-group mb-3">
                                    <input type="text" class="form-control" placeholder="Nama" name="name"
                                        value="{{ old('name') }}" required>
                                    <div class="invalid-feedback" id="name-error"></div>
                                </div>
                                <div class="form-group mb-3">
                                    <input type="email" class="form-control" placeholder="Email" name="email"
                                        value="{{ old('email') }}" required>
                                    <div class="invalid-feedback" id="email-error"></div>
                                </div>
                                <div class="form-group mb-3">
                                    <input type="text" class="form-control" placeholder="No Hp" name="phone_number"
                                        value="{{ old('phone_number') }}" required>
                                    <div class="invalid-feedback" id="phone-error"></div>
                                </div>
                                <div class="form-group mb-3">
                                    <select class="form-control" id="province" name="province" required>
                                        <option value="">Pilih Provinsi</option>
                                    </select>
                                    <div class="invalid-feedback" id="province-error"></div>
                                </div>
                                <div class="form-group mb-3">
                                    <select class="form-control" id="regency" name="regency" required disabled>
                                        <option value="">Pilih Kabupaten/Kota</option>
                                    </select>
                                    <div class="invalid-feedback" id="regency-error"></div>
                                </div>
                                <div class="form-group mb-3">
                                    <input type="password" class="form-control" placeholder="Password" name="password"
                                        required>
                                    <div class="invalid-feedback" id="password-error"></div>
                                </div>
                                <div class="form-group mb-3">
                                    <input type="password" class="form-control" placeholder="Konfirmasi Password"
                                        name="password_confirmation" required>
                                    <div class="invalid-feedback" id="password-confirmation-error"></div>
                                </div>
                            </div>


                            <!-- Terms & Submit -->
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                                <label class="form-check-label" for="terms">
                                    Dengan mengklik tombol ini, anda setuju dengan syarat & ketentuan
                                </label>
                                <div class="invalid-feedback" id="terms-error"></div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100" id="submitBtn">Daftar</button>

                            <p class="mt-3 text-center">
                                <small>Sudah Memiliki Akun? <a href="{{ route('login') }}">Masuk</a></small>
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/jquery-3.1.1.min.js') }}"></script>
    <script src="{{ asset('js/popper.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.js') }}"></script>
    <script src="{{ asset('js/plugins/iCheck/icheck.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            // Province & Regency API
            fetch('https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json')
                .then(response => response.json())
                .then(provinces => {
                    const provinceSelect = document.getElementById('province');
                    provinces.forEach(province => {
                        const option = document.createElement('option');
                        option.value = province.name;
                        option.textContent = province.name;
                        option.dataset.id = province.id;
                        provinceSelect.appendChild(option);
                    });
                    
                    // Set old value if exists
                    const oldProvince = '{{ old("province") }}';
                    if (oldProvince) {
                        provinceSelect.value = oldProvince;
                        $('#province').trigger('change');
                    }
                })
                .catch(error => console.error('Error fetching provinces:', error));

            $('#province').change(function() {
                const selectedOption = this.options[this.selectedIndex];
                const provinceId = selectedOption.dataset.id;
                const regencySelect = $('#regency');

                regencySelect.empty().append('<option value="">Pilih Kabupaten/Kota</option>');

                if (provinceId) {
                    regencySelect.prop('disabled', false);

                    fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/regencies/${provinceId}.json`)
                        .then(response => response.json())
                        .then(regencies => {
                            regencies.forEach(regency => {
                                const option = document.createElement('option');
                                option.value = regency.name;
                                option.textContent = regency.name;
                                regencySelect.append(option);
                            });
                            
                            // Set old value if exists
                            const oldRegency = '{{ old("regency") }}';
                            if (oldRegency) {
                                regencySelect.val(oldRegency);
                            }
                        })
                        .catch(error => console.error('Error fetching regencies:', error));
                } else {
                    regencySelect.prop('disabled', true);
                }
            });

            // Real-time validation
            const validators = {
                name: {
                    required: true,
                    minLength: 2,
                    maxLength: 255,
                    message: 'Nama harus 2-255 karakter'
                },
                email: {
                    required: true,
                    pattern: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
                    message: 'Format email tidak valid'
                },
                phone_number: {
                    required: true,
                    pattern: /^[0-9]{10,15}$/,
                    message: 'Nomor HP harus 10-15 digit angka'
                },
                password: {
                    required: true,
                    minLength: 8,
                    message: 'Password minimal 8 karakter'
                },
                password_confirmation: {
                    required: true,
                    match: 'password',
                    message: 'Konfirmasi password tidak cocok'
                }
            };

            // Validate individual field
            function validateField(fieldName, value) {
                const validator = validators[fieldName];
                if (!validator) return true;

                const field = $(`[name="${fieldName}"]`);
                const errorDiv = $(`#${fieldName.replace('_', '-')}-error`);

                // Clear previous validation
                field.removeClass('is-valid is-invalid');
                errorDiv.text('');

                // Required check
                if (validator.required && (!value || value.trim() === '')) {
                    field.addClass('is-invalid');
                    errorDiv.text('Field ini wajib diisi');
                    return false;
                }

                // Skip other validations if empty and not required
                if (!value || value.trim() === '') {
                    return true;
                }

                // Pattern check
                if (validator.pattern && !validator.pattern.test(value)) {
                    field.addClass('is-invalid');
                    errorDiv.text(validator.message);
                    return false;
                }

                // Length checks
                if (validator.minLength && value.length < validator.minLength) {
                    field.addClass('is-invalid');
                    errorDiv.text(validator.message);
                    return false;
                }

                if (validator.maxLength && value.length > validator.maxLength) {
                    field.addClass('is-invalid');
                    errorDiv.text(validator.message);
                    return false;
                }

                // Match check (for password confirmation)
                if (validator.match) {
                    const matchValue = $(`[name="${validator.match}"]`).val();
                    if (value !== matchValue) {
                        field.addClass('is-invalid');
                        errorDiv.text(validator.message);
                        return false;
                    }
                }

                // Valid
                field.addClass('is-valid');
                return true;
            }

            // Validate all fields
            function validateForm() {
                let isValid = true;
                
                // Validate each field
                Object.keys(validators).forEach(fieldName => {
                    const value = $(`[name="${fieldName}"]`).val();
                    if (!validateField(fieldName, value)) {
                        isValid = false;
                    }
                });

                // Validate province and regency
                const province = $('#province').val();
                const regency = $('#regency').val();
                
                if (!province) {
                    $('#province').addClass('is-invalid');
                    $('#province-error').text('Pilih provinsi');
                    isValid = false;
                } else {
                    $('#province').removeClass('is-invalid').addClass('is-valid');
                    $('#province-error').text('');
                }

                if (!regency) {
                    $('#regency').addClass('is-invalid');
                    $('#regency-error').text('Pilih kabupaten/kota');
                    isValid = false;
                } else {
                    $('#regency').removeClass('is-invalid').addClass('is-valid');
                    $('#regency-error').text('');
                }

                // Validate terms checkbox
                if (!$('#terms').is(':checked')) {
                    $('#terms').addClass('is-invalid');
                    $('#terms-error').text('Anda harus menyetujui syarat & ketentuan');
                    isValid = false;
                } else {
                    $('#terms').removeClass('is-invalid');
                    $('#terms-error').text('');
                }


                return isValid;
            }

            // Real-time validation on input
            Object.keys(validators).forEach(fieldName => {
                $(`[name="${fieldName}"]`).on('blur keyup', function() {
                    validateField(fieldName, $(this).val());
                });
            });

            // Province and regency validation
            $('#province, #regency').on('change', function() {
                validateForm();
            });

            // Terms checkbox validation
            $('#terms').on('change', function() {
                validateForm();
            });

            // Form submission
            $('#registerForm').on('submit', function(e) {
                e.preventDefault();
                
                if (!validateForm()) {
                    // Focus on first invalid field
                    const firstInvalid = $('.is-invalid').first();
                    if (firstInvalid.length) {
                        firstInvalid.focus();
                    }
                    return false;
                }

                // Disable submit button to prevent double submission
                $('#submitBtn').prop('disabled', true).text('Mendaftar...');

                // Submit form
                this.submit();
            });

            // iCheck initialization
            $('.i-checks').iCheck({
                checkboxClass: 'icheckbox_square-green',
                radioClass: 'iradio_square-green',
            });
        });
    </script>
</body>

</html>
