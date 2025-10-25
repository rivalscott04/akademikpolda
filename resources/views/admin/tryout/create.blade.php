@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Buat Tryout Baru</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.tryout.store') }}" method="POST">
                            @csrf

                            <div class="form-group">
                                <label for="judul">Judul Tryout <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('judul') is-invalid @enderror"
                                    id="judul" name="judul" value="{{ old('judul') }}" required>
                                @error('judul')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="deskripsi">Deskripsi</label>
                                <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="3">{{ old('deskripsi') }}</textarea>
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
                                            id="durasi_menit" name="durasi_menit" value="{{ old('durasi_menit') }}"
                                            min="1" required>
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
                                            <option value="kecerdasan"
                                                {{ old('jenis_paket') == 'kecerdasan' ? 'selected' : '' }}>Kecerdasan
                                            </option>
                                            <option value="kepribadian"
                                                {{ old('jenis_paket') == 'kepribadian' ? 'selected' : '' }}>Kepribadian
                                            </option>
                                            <option value="lengkap"
                                                {{ old('jenis_paket') == 'lengkap' ? 'selected' : '' }}>Lengkap</option>
                                        </select>
                                        @error('jenis_paket')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">
                                            <strong>Kecerdasan:</strong> User paket kecerdasan bisa akses<br>
                                            <strong>Kepribadian:</strong> User paket kepribadian bisa akses<br>
                                            <strong>Lengkap:</strong> User paket lengkap bisa akses
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

                            <!-- Summary Box untuk menampilkan soal yang sudah ditambahkan -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>Ringkasan Soal yang Ditambahkan:</label>
                                        <div id="soal-summary" class="alert alert-secondary">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <i class="fa fa-list"></i>
                                                    <span id="summary-text">Belum ada soal yang ditambahkan</span>
                                                </div>
                                                <div>
                                                    <strong>Total: <span id="total-soal"
                                                            class="badge badge-primary">0</span></strong>
                                                </div>
                                            </div>
                                            <div id="detail-summary" class="mt-2" style="display: none;">
                                                <small class="text-muted">Detail per kategori:</small>
                                                <div id="kategori-details" class="mt-1"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="shuffle_questions"
                                        name="shuffle_questions" value="1"
                                        {{ old('shuffle_questions') ? 'checked' : '' }}>
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

                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Kategori</th>
                                                @foreach ($difficultyLevels as $level)
                                                    <th class="text-center">{{ ucfirst($level) }}</th>
                                                @endforeach
                                                <th>Tersedia</th>
                                            </tr>
                                        </thead>
                                        <tbody id="kategori-table-body">
                                            @foreach ($kategoris as $kategori)
                                                <tr class="kategori-row" data-kode="{{ $kategori->kode }}"
                                                    data-nama="{{ $kategori->nama }}">
                                                    <td>
                                                        {{ $kategori->nama }} ({{ $kategori->kode }})
                                                    </td>
                                                    @foreach ($difficultyLevels as $level)
                                                        <td width="120">
                                                            <input type="number" class="form-control blueprint-input"
                                                                min="0" max="100"
                                                                name="blueprint[{{ $kategori->id }}][{{ $level }}]"
                                                                value="{{ old("blueprint.{$kategori->id}.{$level}", 0) }}"
                                                                data-kategori="{{ $kategori->nama }}" data-level="{{ $level }}">
                                                        </td>
                                                    @endforeach
                                                    <td>
                                                        <small class="text-muted">
                                                            @php
                                                                $availableCounts = [];
                                                                foreach ($difficultyLevels as $level) {
                                                                    $count = \App\Models\Soal::where('kategori_id', $kategori->id)
                                                                        ->where('level', $level)
                                                                        ->where('is_used', false)
                                                                        ->where('is_active', true)
                                                                        ->count();
                                                                    $availableCounts[] = ucfirst($level) . ': ' . $count;
                                                                }
                                                            @endphp
                                                            {{ implode(' | ', $availableCounts) }}
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
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save"></i> Buat Tryout
                                </button>
                                <a href="{{ route('admin.tryout.index') }}" class="btn btn-secondary">
                                    <i class="fa fa-arrow-left"></i> Kembali
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Dynamic package mapping from backend (uses kategori.kode values)
            const packageMapping = @json($packageMappings ?? []);

            // Function to update soal summary
            function updateSoalSummary() {
                let totalSoal = 0;
                let detailSummary = {};

                $('.blueprint-input:enabled').each(function() {
                    const value = parseInt($(this).val()) || 0;
                    if (value > 0) {
                        const kategori = $(this).data('kategori');
                        const level = $(this).data('level');

                        totalSoal += value;

                        if (!detailSummary[kategori]) {
                            detailSummary[kategori] = {
                                total: 0
                            };
                        }

                        if (!detailSummary[kategori][level]) {
                            detailSummary[kategori][level] = 0;
                        }
                        detailSummary[kategori][level] = value;
                        detailSummary[kategori].total += value;
                    }
                });

                // Update total badge
                $('#total-soal').text(totalSoal);

                if (totalSoal === 0) {
                    $('#summary-text').text('Belum ada soal yang ditambahkan');
                    $('#detail-summary').hide();
                    $('#soal-summary').removeClass('alert-success alert-warning').addClass('alert-secondary');
                } else {
                    // Create summary text
                    const summaryItems = [];
                    Object.keys(detailSummary).forEach(kategori => {
                        summaryItems.push(`${kategori}: ${detailSummary[kategori].total}`);
                    });

                    $('#summary-text').html(`<strong>Soal ditambahkan:</strong> ${summaryItems.join(', ')}`);

                    // Create detailed breakdown
                    let detailHtml = '';
                    Object.keys(detailSummary).forEach(kategori => {
                        const detail = detailSummary[kategori];
                        const levelDetails = [];
                        const difficultyLevels = @json($difficultyLevels);

                        difficultyLevels.forEach(level => {
                            if (detail[level] > 0) {
                                levelDetails.push(`${level.charAt(0).toUpperCase() + level.slice(1)}: ${detail[level]}`);
                            }
                        });

                        if (levelDetails.length > 0) {
                            detailHtml += `
                                <div class="d-inline-block mr-3 mb-1">
                                    <span class="badge badge-outline-primary">${kategori}</span>
                                    <small class="ml-1">(${levelDetails.join(', ')})</small>
                                </div>
                            `;
                        }
                    });

                    $('#kategori-details').html(detailHtml);
                    $('#detail-summary').show();

                    if (totalSoal >= 50) {
                        $('#soal-summary').removeClass('alert-secondary alert-warning').addClass('alert-success');
                    } else {
                        $('#soal-summary').removeClass('alert-secondary alert-success').addClass('alert-warning');
                    }
                }
            }

            // Function to update kategori display (simplified version)
            function updateKategoriDisplay(selectedPackage) {
                const allowedCategories = packageMapping[selectedPackage] || [];

                // Update preview
                if (selectedPackage && allowedCategories.length > 0) {
                    $('#kategori-preview').html(`
                        <i class="fa fa-check text-success"></i>
                        <strong>Kategori yang akan muncul:</strong><br>
                        ${allowedCategories.map(cat => `<span class="badge badge-primary mr-1">${cat}</span>`).join('')}
                    `).removeClass('alert-info alert-warning alert-danger').addClass('alert-success');
                } else if (selectedPackage === '') {
                    $('#kategori-preview').html(`
                        <i class="fa fa-info-circle"></i> Pilih jenis paket untuk melihat kategori yang akan muncul
                    `).removeClass('alert-success alert-warning alert-danger').addClass('alert-info');
                } else {
                    $('#kategori-preview').html(`
                        <i class="fa fa-exclamation-triangle text-warning"></i> Tidak ada kategori yang tersedia untuk paket ini
                    `).removeClass('alert-success alert-info alert-danger').addClass('alert-warning');
                }

                // Filter kategori table
                $('.kategori-row').each(function() {
                    const kode = $(this).data('kode');

                    if (selectedPackage === '' || allowedCategories.includes(kode)) {
                        // Show row and enable inputs
                        $(this).show();
                        $(this).find('input.blueprint-input').prop('disabled', false);
                    } else {
                        // Hide row, disable inputs, and reset values
                        $(this).hide();
                        $(this).find('input.blueprint-input').prop('disabled', true).val(0);
                    }
                });

                // Update summary after kategori changes
                updateSoalSummary();

                // Re-validate after changes
                setTimeout(function() {
                    checkAllValidations();
                }, 100);
            }

            // Package change event handler
            $('#jenis_paket').on('change', function() {
                const selectedPackage = $(this).val();
                updateKategoriDisplay(selectedPackage);
            });

            // Initialize on page load
            $('#jenis_paket').trigger('change');

            // Calculate total questions
            function calculateTotal() {
                let total = 0;
                $('.blueprint-input:enabled').each(function() {
                    total += parseInt($(this).val()) || 0;
                });
                return total;
            }

            // Validate input against available questions
            function validateInput(input) {
                const value = parseInt(input.val()) || 0;
                const row = input.closest('tr');
                const tersediaText = row.find('td:last-child small').text();

                // Extract available count from text like "Mudah: 10 | Sedang: 10 | Sulit: 10"
                const inputName = input.attr('name');
                const levelData = input.data('level');
                const level = levelData.charAt(0).toUpperCase() + levelData.slice(1);
                const match = tersediaText.match(new RegExp(level + ':\\s*(\\d+)'));
                const available = match ? parseInt(match[1]) : 0;

                // Remove previous validation classes and messages for this row
                input.removeClass('is-valid is-invalid');
                row.nextAll('.validation-message').first().remove();

                if (value > available && value > 0) {
                    input.addClass('is-invalid');
                    const validationRow = $(`
                        <tr class="validation-message">
                            <td colspan="5" class="text-danger small">
                                <i class="fa fa-exclamation-triangle"></i>
                                Jumlah soal ${level.toLowerCase()} yang diminta (${value}) melebihi soal yang tersedia (${available})
                            </td>
                        </tr>
                    `);
                    row.after(validationRow);
                    return false;
                } else if (value > 0) {
                    input.addClass('is-valid');
                }

                return true;
            }

            // Check if all inputs are valid
            function checkAllValidations() {
                let allValid = true;
                let hasQuestions = false;

                $('.blueprint-input:enabled').each(function() {
                    const isValid = validateInput($(this));
                    if (!isValid) {
                        allValid = false;
                    }
                    if (parseInt($(this).val()) > 0) {
                        hasQuestions = true;
                    }
                });

                // Update submit button state
                const submitBtn = $('button[type="submit"]');
                if (allValid && hasQuestions) {
                    submitBtn.prop('disabled', false).removeClass('btn-secondary').addClass('btn-primary');
                } else {
                    submitBtn.prop('disabled', true).removeClass('btn-primary').addClass('btn-secondary');
                }

                return allValid && hasQuestions;
            }

            // Update total and validate when blueprint input changes
            $(document).on('input', '.blueprint-input', function() {
                if (!$(this).prop('disabled')) {
                    validateInput($(this));
                    updateSoalSummary();
                    checkAllValidations();
                }
            });

            // Initial validation and summary on page load
            setTimeout(function() {
                updateSoalSummary();
                checkAllValidations();
            }, 200);
        });
    </script>

    <style>
        .badge-outline-primary {
            color: #007bff;
            border: 1px solid #007bff;
            background-color: transparent;
        }

        #kategori-details {
            max-height: 100px;
            overflow-y: auto;
        }

        .alert-secondary {
            background-color: #f8f9fa;
            border-color: #dee2e6;
            color: #6c757d;
        }
    </style>
@endpush
