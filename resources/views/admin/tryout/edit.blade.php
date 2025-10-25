@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Edit Tryout</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.tryout.update', $tryout) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="form-group">
                                <label for="judul">Judul Tryout <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('judul') is-invalid @enderror"
                                    id="judul" name="judul" value="{{ old('judul', $tryout->judul) }}" required>
                                @error('judul')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="deskripsi">Deskripsi</label>
                                <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="3">{{ old('deskripsi', $tryout->deskripsi) }}</textarea>
                                @error('deskripsi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="durasi_menit">Durasi (Menit) <span class="text-danger">*</span></label>
                                        <input type="number"
                                            class="form-control @error('durasi_menit') is-invalid @enderror"
                                            id="durasi_menit" name="durasi_menit"
                                            value="{{ old('durasi_menit', $tryout->durasi_menit) }}" min="1"
                                            required>
                                        @error('durasi_menit')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="jenis_paket">Jenis Paket <span class="text-danger">*</span></label>
                                        <select class="form-control @error('jenis_paket') is-invalid @enderror"
                                            id="jenis_paket" name="jenis_paket" required>
                                            <option value="">Pilih Jenis Paket</option>
                                            <option value="bahasa_inggris"
                                                {{ old('jenis_paket', $tryout->jenis_paket) == 'bahasa_inggris' ? 'selected' : '' }}>
                                                Bahasa Inggris</option>
                                            <option value="pu"
                                                {{ old('jenis_paket', $tryout->jenis_paket) == 'pu' ? 'selected' : '' }}>
                                                Pengetahuan Umum</option>
                                            <option value="twk"
                                                {{ old('jenis_paket', $tryout->jenis_paket) == 'twk' ? 'selected' : '' }}>
                                                Tes Wawasan Kebangsaan</option>
                                            <option value="numerik"
                                                {{ old('jenis_paket', $tryout->jenis_paket) == 'numerik' ? 'selected' : '' }}>
                                                Penalaran Numerik</option>
                                            <option value="lengkap"
                                                {{ old('jenis_paket', $tryout->jenis_paket) == 'lengkap' ? 'selected' : '' }}>
                                                Paket Lengkap</option>
                                        </select>
                                        @error('jenis_paket')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">
                                            Pilih jenis paket akademik. User dapat mengakses sesuai paket yang dimiliki.
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>Preview Kategori yang Akan Muncul:</label>
                                        <div id="kategori-preview" class="alert alert-info">
                                            <i class="fa fa-info-circle"></i> Pilih jenis paket untuk melihat kategori yang
                                            akan muncul
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="shuffle_questions"
                                        name="shuffle_questions" value="1"
                                        {{ old('shuffle_questions', $tryout->shuffle_questions) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="shuffle_questions">Acak Urutan Nomor
                                        Soal</label>
                                </div>
                                <small class="text-muted">Jika aktif, urutan nomor soal diacak per user secara
                                    deterministik. Opsi jawaban tetap diacak seperti biasa.</small>
                            </div>

                            <div class="form-group">
                                <label>Blueprint Per Kategori & Level <span class="text-danger">*</span></label>
                                <div class="alert alert-info">
                                    <i class="fa fa-info-circle"></i>
                                    Tentukan jumlah soal untuk setiap kombinasi kategori dan level.
                                </div>
                                <div class="alert alert-warning">
                                    <i class="fa fa-warning"></i>
                                    <strong>Perhatian:</strong> Mengubah blueprint soal akan menghapus semua jawaban user
                                    yang sudah ada untuk tryout ini.
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Kategori</th>
                                                <th class="text-center">Dasar</th>
                                                <th class="text-center">Mudah</th>
                                                <th class="text-center">Sedang</th>
                                                <th class="text-center">Sulit</th>
                                                <th class="text-center">Tersulit</th>
                                                <th class="text-center">Ekstrem</th>
                                                <th>Tersedia</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $bp = $tryout->blueprints
                                                    ->groupBy('kategori_id')
                                                    ->map(function ($rows) {
                                                        return [
                                                            'dasar' =>
                                                                optional($rows->firstWhere('level', 'dasar'))->jumlah ??
                                                                0,
                                                            'mudah' =>
                                                                optional($rows->firstWhere('level', 'mudah'))->jumlah ??
                                                                0,
                                                            'sedang' =>
                                                                optional($rows->firstWhere('level', 'sedang'))
                                                                    ->jumlah ?? 0,
                                                            'sulit' =>
                                                                optional($rows->firstWhere('level', 'sulit'))->jumlah ??
                                                                0,
                                                            'tersulit' =>
                                                                optional($rows->firstWhere('level', 'tersulit'))->jumlah ??
                                                                0,
                                                            'ekstrem' =>
                                                                optional($rows->firstWhere('level', 'ekstrem'))->jumlah ??
                                                                0,
                                                        ];
                                                    });
                                            @endphp
                                            @foreach ($kategoris as $kategori)
                                                @php
                                                    $row = $bp[$kategori->id] ?? [
                                                        'dasar' => 0,
                                                        'mudah' => 0,
                                                        'sedang' => 0,
                                                        'sulit' => 0,
                                                        'tersulit' => 0,
                                                        'ekstrem' => 0,
                                                    ];
                                                @endphp
                                                <tr class="kategori-row" data-kode="{{ $kategori->kode }}">
                                                    <td>{{ $kategori->nama }} ({{ $kategori->kode }})</td>
                                                    <td width="120">
                                                        <input type="number" class="form-control blueprint-input"
                                                            min="0" max="100"
                                                            name="blueprint[{{ $kategori->id }}][dasar]"
                                                            value="{{ old("blueprint.{$kategori->id}.dasar", $row['dasar']) }}"
                                                            data-original="{{ $row['dasar'] }}">
                                                    </td>
                                                    <td width="120">
                                                        <input type="number" class="form-control blueprint-input"
                                                            min="0" max="100"
                                                            name="blueprint[{{ $kategori->id }}][mudah]"
                                                            value="{{ old("blueprint.{$kategori->id}.mudah", $row['mudah']) }}"
                                                            data-original="{{ $row['mudah'] }}">
                                                    </td>
                                                    <td width="120">
                                                        <input type="number" class="form-control blueprint-input"
                                                            min="0" max="100"
                                                            name="blueprint[{{ $kategori->id }}][sedang]"
                                                            value="{{ old("blueprint.{$kategori->id}.sedang", $row['sedang']) }}"
                                                            data-original="{{ $row['sedang'] }}">
                                                    </td>
                                                    <td width="120">
                                                        <input type="number" class="form-control blueprint-input"
                                                            min="0" max="100"
                                                            name="blueprint[{{ $kategori->id }}][sulit]"
                                                            value="{{ old("blueprint.{$kategori->id}.sulit", $row['sulit']) }}"
                                                            data-original="{{ $row['sulit'] }}">
                                                    </td>
                                                    <td width="120">
                                                        <input type="number" class="form-control blueprint-input"
                                                            min="0" max="100"
                                                            name="blueprint[{{ $kategori->id }}][tersulit]"
                                                            value="{{ old("blueprint.{$kategori->id}.tersulit", $row['tersulit']) }}"
                                                            data-original="{{ $row['tersulit'] }}">
                                                    </td>
                                                    <td width="120">
                                                        <input type="number" class="form-control blueprint-input"
                                                            min="0" max="100"
                                                            name="blueprint[{{ $kategori->id }}][ekstrem]"
                                                            value="{{ old("blueprint.{$kategori->id}.ekstrem", $row['ekstrem']) }}"
                                                            data-original="{{ $row['ekstrem'] }}">
                                                    </td>
                                                    <td>
                                                        <small class="text-muted">
                                                            Dasar: {{ $kategori->soals()->where('level', 'dasar')->where('is_used', false)->count() }} |
                                                            Mudah: {{ $kategori->soals()->where('level', 'mudah')->where('is_used', false)->count() }} |
                                                            Sedang: {{ $kategori->soals()->where('level', 'sedang')->where('is_used', false)->count() }} |
                                                            Sulit: {{ $kategori->soals()->where('level', 'sulit')->where('is_used', false)->count() }} |
                                                            Tersulit: {{ $kategori->soals()->where('level', 'tersulit')->where('is_used', false)->count() }} |
                                                            Ekstrem: {{ $kategori->soals()->where('level', 'ekstrem')->where('is_used', false)->count() }}
                                                        </small>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                @error('blueprint')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror

                                <div class="mt-3">
                                    <div class="alert alert-secondary">
                                        <strong>Total Soal: <span id="total-soal">0</span></strong>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save"></i> Update Tryout
                                </button>
                                <a href="{{ route('admin.tryout.show', $tryout) }}" class="btn btn-info">
                                    <i class="fa fa-eye"></i> Lihat Detail
                                </a>
                                <a href="{{ route('admin.tryout.index') }}" class="btn btn-secondary">
                                    <i class="fa fa-arrow-left"></i> Kembali
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Information Card -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5>Informasi Tryout</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <strong>Status:</strong>
                                <span class="badge badge-{{ $tryout->is_active ? 'success' : 'danger' }}">
                                    {{ $tryout->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                </span>
                            </div>
                            <div class="col-md-3">
                                <strong>Dibuat:</strong><br>
                                {{ $tryout->created_at->format('d/m/Y H:i') }}
                            </div>
                            <div class="col-md-3">
                                <strong>Terakhir Diubah:</strong><br>
                                {{ $tryout->updated_at->format('d/m/Y H:i') }}
                            </div>
                            <div class="col-md-3">
                                <strong>Peserta:</strong><br>
                                {{ $tryout->userTryoutSoal()->distinct('user_id')->count() }} orang
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi -->
    <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmModalLabel">Konfirmasi Perubahan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Anda telah mengubah struktur soal. Ini akan menghapus semua jawaban user yang sudah ada.</p>
                    <p><strong>Apakah Anda yakin ingin melanjutkan?</strong></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-warning" id="confirmSubmit">Ya, Lanjutkan</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            let blueprintChanged = false;

            // Package mapping - Dynamic from database
            const packageMapping = @json($packageMappings);

            // Update kategori preview and filter table when jenis paket changes
            $('#jenis_paket').on('change', function() {
                const selectedPackage = $(this).val();
                const allowedCategories = packageMapping[selectedPackage] || [];

                // Update preview
                if (allowedCategories.length > 0) {
                    $('#kategori-preview').html(`
                <i class="fa fa-check text-success"></i>
                <strong>Kategori yang akan muncul:</strong><br>
                ${allowedCategories.map(cat => `<span class="badge badge-primary mr-1">${cat}</span>`).join('')}
            `).removeClass('alert-info').addClass('alert-success');
                } else {
                    $('#kategori-preview').html(`
                <i class="fa fa-info-circle"></i> Pilih jenis paket untuk melihat kategori yang akan muncul
            `).removeClass('alert-success').addClass('alert-info');
                }

                // Filter kategori table
                $('.kategori-row').each(function() {
                    const kode = $(this).data('kode');
                    if (allowedCategories.includes(kode)) {
                        $(this).show();
                        $(this).find('input').prop('disabled', false);
                    } else {
                        $(this).hide();
                        $(this).find('input').prop('disabled', true).val(0);
                    }
                });
            });

            // Initialize preview on page load
            $('#jenis_paket').trigger('change');

            // Calculate total questions
            function calculateTotal() {
                let total = 0;
                $('.blueprint-input').each(function() {
                    total += parseInt($(this).val()) || 0;
                });
                $('#total-soal').text(total);
                return total;
            }

            // Check if structure changed
            function checkBlueprintChange() {
                let changed = false;
                $('.blueprint-input').each(function() {
                    const current = parseInt($(this).val()) || 0;
                    const original = parseInt($(this).data('original')) || 0;
                    if (current !== original) {
                        changed = true;
                        return false; // break loop
                    }
                });
                blueprintChanged = changed;
            }

            // Validate input against available questions - FIXED VERSION
            function validateInput(input) {
                const value = parseInt(input.val()) || 0;
                const row = input.closest('tr');
                const tersediaText = row.find('td:last-child small').text();

                // Debug: log the tersedia text
                console.log('Tersedia text:', tersediaText);

                // Get level from input name - perbaiki deteksi level
                let level = '';
                let levelKey = '';
                if (input.attr('name').includes('[dasar]')) {
                    level = 'Dasar';
                    levelKey = 'dasar';
                } else if (input.attr('name').includes('[mudah]')) {
                    level = 'Mudah';
                    levelKey = 'mudah';
                } else if (input.attr('name').includes('[sedang]')) {
                    level = 'Sedang';
                    levelKey = 'sedang';
                } else if (input.attr('name').includes('[sulit]')) {
                    level = 'Sulit';
                    levelKey = 'sulit';
                } else if (input.attr('name').includes('[tersulit]')) {
                    level = 'Tersulit';
                    levelKey = 'tersulit';
                } else if (input.attr('name').includes('[ekstrem]')) {
                    level = 'Ekstrem';
                    levelKey = 'ekstrem';
                }

                // Extract available count - perbaiki regex parsing
                // Format text: "Dasar: 0 | Mudah: 0 | Sedang: 0 | Sulit: 0 | Tersulit: 0 | Ekstrem: 0"
                let availableInDatabase = 0;
                const patterns = [
                    new RegExp(level + ':\\s*(\\d+)', 'i'), // "Mudah: 0"
                    new RegExp(levelKey + ':\\s*(\\d+)', 'i') // "mudah: 0"
                ];

                for (let pattern of patterns) {
                    const match = tersediaText.match(pattern);
                    if (match) {
                        availableInDatabase = parseInt(match[1]);
                        break;
                    }
                }

                console.log(`Level: ${level}, Available: ${availableInDatabase}`);

                // Get original blueprint value
                const original = parseInt(input.data('original')) || 0;

                // Calculate actual available: soal di database + soal yang sedang digunakan blueprint lama
                const actualAvailable = availableInDatabase + original;

                // Clear previous validation
                input.removeClass('is-valid is-invalid');
                row.find('.validation-message').remove();

                // Validate: input value should not exceed actual available
                if (value > actualAvailable) {
                    input.addClass('is-invalid');
                    row.after(`
                <tr class="validation-message">
                    <td colspan="5" class="text-danger small">
                        <i class="fa fa-exclamation-triangle"></i>
                        Jumlah soal ${level.toLowerCase()} (${value}) melebihi total tersedia (${actualAvailable}: ${availableInDatabase} unused + ${original} from current blueprint)
                    </td>
                </tr>
            `);
                    return false;
                } else if (value > 0) {
                    input.addClass('is-valid');
                }

                return true;
            }

            // Check if all inputs are valid
            function checkAllValidations() {
                let allValid = true;
                $('.blueprint-input:visible').each(function() {
                    if (!validateInput($(this))) {
                        allValid = false;
                    }
                });

                // Update submit button state
                const submitBtn = $('button[type="submit"]');
                if (allValid) {
                    submitBtn.prop('disabled', false).removeClass('btn-secondary').addClass('btn-primary');
                } else {
                    submitBtn.prop('disabled', true).removeClass('btn-primary').addClass('btn-secondary');
                }

                return allValid;
            }

            // Update total when structure changes
            $('.blueprint-input').on('input', function() {
                validateInput($(this));
                checkAllValidations();
                calculateTotal();
                checkBlueprintChange();
            });

            // Initial validation on page load
            checkAllValidations();

            // Form submission with confirmation if structure changed
            $('form').on('submit', function(e) {
                if (blueprintChanged) {
                    e.preventDefault();
                    $('#confirmModal').modal('show');
                }
            });

            // Confirm submission
            $('#confirmSubmit').on('click', function() {
                $('#confirmModal').modal('hide');
                $('form').off('submit').submit();
            });

            // Initialize total calculation
            calculateTotal();
        });
    </script>
@endpush
