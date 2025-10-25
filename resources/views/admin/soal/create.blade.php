@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Tambah Soal</h4>
                    </div>
                    <div class="card-body">
                        <!-- Success/Error Messages -->
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fa fa-check-circle"></i>
                                {{ session('success') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fa fa-exclamation-triangle"></i>
                                {{ session('error') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fa fa-exclamation-triangle"></i>
                                <strong>Terjadi kesalahan:</strong>
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        <form action="{{ route('admin.soal.store') }}" method="POST" id="soalForm"
                            enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="kategori_id">Kategori Soal <span class="text-danger">*</span></label>
                                        <select class="form-control @error('kategori_id') is-invalid @enderror"
                                            id="kategori_id" name="kategori_id" required>
                                            <option value="">Pilih Kategori</option>
                                            @foreach ($kategoris as $kategori)
                                                <option value="{{ $kategori->id }}"
                                                    {{ old('kategori_id') == $kategori->id ? 'selected' : '' }}>
                                                    {{ $kategori->nama }} ({{ $kategori->kode }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('kategori_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tipe">Tipe Soal <span class="text-danger">*</span></label>
                                        <select class="form-control @error('tipe') is-invalid @enderror" id="tipe"
                                            name="tipe" required>
                                            <option value="">Pilih Tipe</option>
                                            @foreach ($tipes as $tipe)
                                                <option value="{{ $tipe }}"
                                                    {{ old('tipe') == $tipe ? 'selected' : '' }}>
                                                    @switch($tipe)
                                                        @case('benar_salah')
                                                            Benar / Salah
                                                        @break

                                                        @case('pg_satu')
                                                            Pilihan Ganda (Satu Jawaban)
                                                        @break

                                                        @case('pg_bobot')
                                                            Pilihan Ganda (Berbobot)
                                                        @break

                                                        @case('pg_pilih_2')
                                                            Pilihan Ganda (Pilih 2)
                                                        @break

                                                        @case('gambar')
                                                            Soal dengan Gambar
                                                        @break
                                                    @endswitch
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('tipe')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="level">Level Kesulitan <span class="text-danger">*</span></label>
                                        <select class="form-control @error('level') is-invalid @enderror" id="level"
                                            name="level" required>
                                            <option value="">Pilih Level</option>
                                            <option value="dasar" {{ old('level') == 'dasar' ? 'selected' : '' }}>Dasar
                                            </option>
                                            <option value="mudah" {{ old('level') == 'mudah' ? 'selected' : '' }}>Mudah
                                            </option>
                                            <option value="sedang" {{ old('level') == 'sedang' ? 'selected' : '' }}>Sedang
                                            </option>
                                            <option value="sulit" {{ old('level') == 'sulit' ? 'selected' : '' }}>Sulit
                                            </option>
                                            <option value="tersulit" {{ old('level') == 'tersulit' ? 'selected' : '' }}>Tersulit
                                            </option>
                                            <option value="ekstrem" {{ old('level') == 'ekstrem' ? 'selected' : '' }}>Ekstrem
                                            </option>
                                        </select>
                                        @error('level')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Image Upload Section -->
                            <div class="form-group" id="gambar-group" style="display: none;">
                                <label for="gambar">Upload Gambar <span class="text-danger">*</span></label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input @error('gambar') is-invalid @enderror"
                                        id="gambar" name="gambar" accept="image/*">
                                    <label class="custom-file-label" for="gambar">Pilih gambar...</label>
                                </div>
                                <small class="form-text text-muted">
                                    Format yang didukung: JPEG, PNG, JPG, GIF. Maksimal ukuran: 2MB
                                </small>
                                @error('gambar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                <!-- Image Preview -->
                                <div id="image-preview" class="mt-3" style="display: none;">
                                    <img id="preview-img" src="" alt="Preview" class="img-thumbnail"
                                        style="max-width: 300px; max-height: 200px;">
                                    <button type="button" class="btn btn-sm btn-danger ml-2" id="remove-image">
                                        <i class="fa fa-times"></i> Hapus
                                    </button>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="pertanyaan">Pertanyaan <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('pertanyaan') is-invalid @enderror" id="pertanyaan" name="pertanyaan"
                                    rows="4" required>{{ old('pertanyaan') }}</textarea>
                                @error('pertanyaan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div id="opsi-container">
                                <div class="form-group">
                                    <label>Opsi Jawaban <span class="text-danger">*</span></label>
                                    <div id="opsi-wrapper">
                                        <!-- Opsi akan digenerate dengan JavaScript -->
                                    </div>
                                    <button type="button" class="btn btn-sm btn-success" id="add-opsi"
                                        style="display: none;">
                                        <i class="fa fa-plus"></i> Tambah Opsi
                                    </button>
                                </div>
                            </div>

                            <div class="form-group" id="jawaban-benar-group" style="display: none;">
                                <label for="jawaban_benar">Jawaban Benar</label>
                                <select class="form-control @error('jawaban_benar') is-invalid @enderror" id="jawaban_benar"
                                    name="jawaban_benar">
                                    <option value="">Pilih Jawaban Benar</option>
                                </select>
                                @error('jawaban_benar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label class="d-block">Tipe Pembahasan</label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="pembahasan_type"
                                        id="pembahasan_type_text" value="text"
                                        {{ old('pembahasan_type', 'text') == 'text' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="pembahasan_type_text">Text</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="pembahasan_type"
                                        id="pembahasan_type_image" value="image"
                                        {{ old('pembahasan_type') == 'image' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="pembahasan_type_image">Gambar</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="pembahasan_type"
                                        id="pembahasan_type_both" value="both"
                                        {{ old('pembahasan_type') == 'both' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="pembahasan_type_both">Keduanya</label>
                                </div>
                                @error('pembahasan_type')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group" id="pembahasan-text-group" style="display: none;">
                                <label for="pembahasan">Pembahasan</label>
                                <textarea class="form-control @error('pembahasan') is-invalid @enderror" id="pembahasan" name="pembahasan"
                                    rows="3">{{ old('pembahasan') }}</textarea>
                                <small class="form-text text-muted">Penjelasan jawaban (opsional)</small>
                                @error('pembahasan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group" id="pembahasan-image-group" style="display: none;">
                                <label for="pembahasan_image">Upload Gambar Pembahasan</label>
                                <div class="custom-file">
                                    <input type="file"
                                        class="custom-file-input @error('pembahasan_image') is-invalid @enderror"
                                        id="pembahasan_image" name="pembahasan_image" accept="image/*">
                                    <label class="custom-file-label" for="pembahasan_image">Pilih gambar...</label>
                                </div>
                                <small class="form-text text-muted">Format: JPEG, PNG, JPG, GIF. Maks: 1MB</small>
                                @error('pembahasan_image')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <div id="pembahasan-image-preview" class="mt-3" style="display: none;">
                                    <img id="pembahasan-preview-img" src="" alt="Preview Pembahasan"
                                        class="img-thumbnail" style="max-width: 300px; max-height: 200px;">
                                    <button type="button" class="btn btn-sm btn-danger ml-2"
                                        id="remove-pembahasan-image">
                                        <i class="fa fa-times"></i> Hapus
                                    </button>
                                </div>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save"></i> Simpan
                                </button>
                                <a href="{{ route('admin.soal.index') }}" class="btn btn-secondary">
                                    <i class="fa fa-arrow-left"></i> Kembali
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                let opsiCount = 0;
                let akademikCodes = []; // Will be loaded dynamically

                // Load akademik categories from server
                function loadKepribadianCodes() {
                    $.get('{{ route('admin.soal.akademik-categories') }}')
                        .done(function(data) {
                            akademikCodes = data;
                            console.log('Loaded akademik codes from API:', akademikCodes);
                        })
                        .fail(function() {
                            console.error('Failed to load akademik codes from API');
                            // Fallback to comprehensive list including all possible akademik codes
                            akademikCodes = ['', 'PSIKOTES', 'ID', 'OPY', 'KB', 'PGD', 'KBS', 'KDDJ', 'IDK',
                                'KDS', 'TJ', 'OPP'
                            ];
                            console.log('Using fallback akademik codes:', akademikCodes);
                        });
                }

                // Function to check if category is in akademik package
                function checkIfKepribadianCategory(kategoriId) {
                    if (!kategoriId) return false;

                    // Get category code from selected option
                    const selectedOption = $(`#kategori_id option[value="${kategoriId}"]`);
                    const optionText = selectedOption.text();

                    console.log('Checking category:', optionText, 'against akademik package codes:',
                        akademikCodes);

                    // Check if category code is in akademik package mapping
                    const isKepribadian = akademikCodes.some(code => optionText.includes(code));
                    console.log('Is in akademik package:', isKepribadian);

                    return isKepribadian;
                }

                // Load akademik codes on page load
                loadKepribadianCodes();

                // Handle tipe change
                $('#tipe').on('change', function() {
                    const tipe = $(this).val();
                    generateOpsi(tipe);
                    toggleJawabanBenar(tipe);
                    toggleGambarUpload(tipe);
                    setupJawabanHandling(tipe);
                });

                // Handle kategori change to update bobot validation
                $('#kategori_id').on('change', function() {
                    const tipe = $('#tipe').val();
                    if (tipe === 'pg_bobot') {
                        // Update existing bobot inputs
                        updateBobotInputs();
                    }
                });

                // Function to update bobot inputs based on current category
                function updateBobotInputs() {
                    const kategoriId = $('#kategori_id').val();
                    const isKepribadian = checkIfKepribadianCategory(kategoriId);

                    $('.bobot-input').each(function() {
                        if ($(this).is('input[type="number"]')) {
                            $(this).attr('step', isKepribadian ? '1' : '0.01');
                            $(this).attr('min', isKepribadian ? '1' : '0');
                            // Remove max attribute to avoid HTML5 validation
                            $(this).removeAttr('max');
                        }
                    });
                }

                function togglePembahasanControls() {
                    const type = $('input[name="pembahasan_type"]:checked').val();
                    if (type === 'text') {
                        $('#pembahasan-text-group').show();
                        $('#pembahasan-image-group').hide();
                        $('#pembahasan_image').val('');
                        $('#pembahasan-image-preview').hide();
                    } else if (type === 'image') {
                        $('#pembahasan-text-group').hide();
                        $('#pembahasan').val('');
                        $('#pembahasan-image-group').show();
                    } else {
                        $('#pembahasan-text-group').show();
                        $('#pembahasan-image-group').show();
                    }
                }

                $('input[name="pembahasan_type"]').on('change', togglePembahasanControls);
                togglePembahasanControls();

                $('#pembahasan_image').on('change', function() {
                    const file = this.files[0];
                    if (file) {
                        // Check file size (1MB = 1024 * 1024 bytes)
                        const maxSize = 1024 * 1024; // 1MB in bytes
                        if (file.size > maxSize) {
                            alert('Ukuran file gambar pembahasan maksimal 1MB. File yang dipilih: ' + (file
                                .size / (1024 * 1024)).toFixed(2) + 'MB');
                            $(this).val('');
                            $('.custom-file-label[for="pembahasan_image"]').text('Pilih gambar...');
                            $('#pembahasan-image-preview').hide();
                            return;
                        }

                        $('.custom-file-label[for="pembahasan_image"]').text(file.name);
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            $('#pembahasan-preview-img').attr('src', e.target.result);
                            $('#pembahasan-image-preview').show();
                        };
                        reader.readAsDataURL(file);
                    }
                });

                $('#remove-pembahasan-image').on('click', function() {
                    $('#pembahasan_image').val('');
                    $('.custom-file-label[for="pembahasan_image"]').text('Pilih gambar...');
                    $('#pembahasan-image-preview').hide();
                    $('#pembahasan-preview-img').attr('src', '');
                });

                // Toggle gambar upload visibility
                function toggleGambarUpload(tipe) {
                    if (tipe === 'gambar') {
                        $('#gambar-group').show();
                        $('#gambar').prop('required', true);
                    } else {
                        $('#gambar-group').hide();
                        $('#gambar').prop('required', false);
                        $('#image-preview').hide();
                        $('#preview-img').attr('src', '');
                        $('#gambar').val('');
                        $('.custom-file-label').text('Pilih gambar...');
                    }
                }

                // Handle file input change
                $('#gambar').on('change', function() {
                    const file = this.files[0];
                    if (file) {
                        const fileName = file.name;
                        $('.custom-file-label').text(fileName);

                        // Show preview
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            $('#preview-img').attr('src', e.target.result);
                            $('#image-preview').show();
                        };
                        reader.readAsDataURL(file);
                    }
                });

                // Remove image
                $('#remove-image').on('click', function() {
                    $('#gambar').val('');
                    $('.custom-file-label').text('Pilih gambar...');
                    $('#image-preview').hide();
                    $('#preview-img').attr('src', '');
                });

                // Generate opsi based on tipe
                function generateOpsi(tipe) {
                    const wrapper = $('#opsi-wrapper');
                    wrapper.empty();
                    opsiCount = 0;

                    switch (tipe) {
                        case 'benar_salah':
                            addOpsiItem('A', 'Benar', false);
                            addOpsiItem('B', 'Salah', false);
                            $('#add-opsi').hide();
                            break;
                        case 'pg_satu':
                        case 'pg_pilih_2':
                        case 'gambar':
                            // Start with 4 default options
                            for (let i = 0; i < 4; i++) {
                                addOpsiItem(String.fromCharCode(65 + i), '', false);
                            }
                            $('#add-opsi').show();
                            break;
                        case 'pg_bobot':
                            // Start with 4 default options with bobot
                            for (let i = 0; i < 4; i++) {
                                addOpsiItem(String.fromCharCode(65 + i), '', true);
                            }
                            $('#add-opsi').show();
                            break;
                    }

                    updateJawabanBenarOptions();
                    // Update bobot inputs after generating opsi
                    updateBobotInputs();
                }

                // Add opsi item
                function addOpsiItem(letter, defaultText = '', showBobot = false) {
                    // Check if current category is akademik (, PSIKOTES)
                    const kategoriId = $('#kategori_id').val();
                    const isKepribadian = checkIfKepribadianCategory(kategoriId);

                    const opsiHtml = `
            <div class="row mb-2 opsi-item" data-letter="${letter}">
                <div class="col-1">
                    <span class="badge badge-primary">${letter}</span>
                </div>
                <div class="col-${showBobot ? '6' : '8'}">
                    <input type="text" class="form-control" name="opsi[${opsiCount}][teks]"
                           value="${defaultText}" placeholder="Teks opsi ${letter}" required>
                </div>
                ${showBobot ? `
                                                                                                        <div class="col-2">
                                                                                                            <input type="number" class="form-control bobot-input" name="opsi[${opsiCount}][bobot]"
                                                                                                                   step="${isKepribadian ? '1' : '0.01'}"
                                                                                                                   min="${isKepribadian ? '1' : '0'}"
                                                                                                                   placeholder="Bobot" value="${isKepribadian ? '1' : '0'}">
                                                                                                        </div>
                                                                                                        ` : `
                                                                                                        <div class="col-2">
                                                                                                            <input type="hidden" class="bobot-input" name="opsi[${opsiCount}][bobot]" value="0">
                                                                                                            <div class="form-check">
                                                                                                                <input type="checkbox" class="form-check-input jawaban-checkbox"
                                                                                                                       id="jawaban_${letter}" data-letter="${letter}">
                                                                                                                <label class="form-check-label" for="jawaban_${letter}">
                                                                                                                    <small>Benar</small>
                                                                                                                </label>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                        `}
                <div class="col-1">
                    ${opsiCount > 1 ? `<button type="button" class="btn btn-sm btn-danger remove-opsi"><i class="fa fa-trash"></i></button>` : ''}
                </div>
            </div>
        `;

                    $('#opsi-wrapper').append(opsiHtml);
                    opsiCount++;
                }

                // Add opsi button click
                $('#add-opsi').on('click', function() {
                    const currentTipe = $('#tipe').val();
                    if (opsiCount < 10) { // Maximum 10 options
                        const letter = String.fromCharCode(65 + opsiCount);
                        const showBobot = currentTipe === 'pg_bobot';
                        addOpsiItem(letter, '', showBobot);
                        updateJawabanBenarOptions();
                    }
                });

                // Remove opsi
                $(document).on('click', '.remove-opsi', function() {
                    $(this).closest('.opsi-item').remove();
                    reorderOpsi();
                    updateJawabanBenarOptions();
                });

                // Reorder opsi after removal
                function reorderOpsi() {
                    opsiCount = 0;
                    $('.opsi-item').each(function(index) {
                        const letter = String.fromCharCode(65 + index);
                        $(this).attr('data-letter', letter);
                        $(this).find('.badge').text(letter);
                        $(this).find('input[name*="[teks]"]').attr('name', `opsi[${index}][teks]`);
                        $(this).find('input[name*="[bobot]"]').attr('name', `opsi[${index}][bobot]`);
                        $(this).find('.bobot-input').attr('name', `opsi[${index}][bobot]`);
                        $(this).find('input[name*="[teks]"]').attr('placeholder', `Teks opsi ${letter}`);
                        $(this).find('.jawaban-checkbox').attr('id', `jawaban_${letter}`).attr('data-letter',
                            letter);
                        $(this).find('.form-check-label').attr('for', `jawaban_${letter}`);
                        opsiCount++;
                    });
                    updateJawabanBenarOptions();
                }

                // Toggle jawaban benar visibility
                function toggleJawabanBenar(tipe) {
                    // Semua tipe menggunakan checkbox, tidak ada yang menggunakan dropdown
                    $('#jawaban-benar-group').hide();
                }

                // Setup jawaban handling based on tipe
                function setupJawabanHandling(tipe) {
                    // Remove existing hidden inputs
                    $('input[name="jawaban_benar_hidden"]').remove();

                    // Semua tipe menggunakan checkbox untuk konsistensi
                    $(document).off('change', '.jawaban-checkbox').on('change', '.jawaban-checkbox', function() {
                        updateJawabanBenarFromCheckboxes(tipe);
                    });
                }

                // Update jawaban benar from checkboxes
                function updateJawabanBenarFromCheckboxes(tipe) {
                    const checkedBoxes = $('.jawaban-checkbox:checked');

                    // Validasi berdasarkan tipe
                    if (tipe === 'pg_satu' || tipe === 'gambar') {
                        if (checkedBoxes.length > 1) {
                            // Untuk pg_satu dan gambar, hanya boleh 1 jawaban
                            checkedBoxes.not(':last').prop('checked', false);
                            alert('Hanya boleh memilih 1 jawaban benar untuk tipe ini');
                            return;
                        }
                    } else if (tipe === 'pg_pilih_2') {
                        if (checkedBoxes.length > 2) {
                            // Uncheck the last checked if more than 2
                            checkedBoxes.last().prop('checked', false);
                            alert('Maksimal pilih 2 jawaban benar');
                            return;
                        }
                    } else if (tipe === 'benar_salah') {
                        if (checkedBoxes.length > 1) {
                            // For benar_salah, only allow one selection
                            $('.jawaban-checkbox').not(':last').prop('checked', false);
                        }
                    }

                    // Update bobot values berdasarkan tipe soal
                    $('.opsi-item').each(function() {
                        const checkbox = $(this).find('.jawaban-checkbox');
                        const bobotInput = $(this).find('.bobot-input');

                        if (checkbox.is(':checked')) {
                            // Set bobot berdasarkan tipe soal
                            switch (tipe) {
                                case 'pg_pilih_2':
                                    bobotInput.val(0.5); // 0.5 untuk pilih 2
                                    break;
                                case 'pg_bobot':
                                    // Untuk pg_bobot, biarkan user input sendiri
                                    if (bobotInput.val() === '0' || bobotInput.val() === '') {
                                        bobotInput.val(1); // Default 1 jika kosong
                                    }
                                    break;
                                default:
                                    bobotInput.val(1); // 1 untuk tipe lain
                                    break;
                            }
                        } else {
                            // Set bobot to 0 for incorrect answers
                            bobotInput.val(0);
                        }
                    });

                    // Update hidden input for jawaban_benar
                    const selectedAnswers = [];
                    $('.jawaban-checkbox:checked').each(function() {
                        selectedAnswers.push($(this).data('letter'));
                    });

                    // Remove old hidden input and add new one
                    $('input[name="jawaban_benar"]').remove();
                    if (selectedAnswers.length > 0) {
                        $('#soalForm').append(
                            `<input type="hidden" name="jawaban_benar" value="${selectedAnswers.join(',')}" class="jawaban_benar_hidden">`
                        );
                    }
                }

                // Update jawaban benar options
                function updateJawabanBenarOptions() {
                    const select = $('#jawaban_benar');
                    const currentValue = select.val();
                    select.empty().append('<option value="">Pilih Jawaban Benar</option>');

                    $('.opsi-item').each(function() {
                        const letter = $(this).attr('data-letter');
                        const text = $(this).find('input[name*="[teks]"]').val();
                        const displayText = text ?
                            `${letter}. ${text.substring(0, 30)}${text.length > 30 ? '...' : ''}` :
                            `${letter}.`;
                        select.append(`<option value="${letter}">${displayText}</option>`);
                    });

                    select.val(currentValue);
                }

                // Update jawaban benar options when opsi text changes
                $(document).on('input', 'input[name*="[teks]"]', function() {
                    updateJawabanBenarOptions();
                });

                // Update bobot when jawaban_benar dropdown changes (DIHAPUS karena tidak dipakai lagi)
                // Fungsi ini dihapus karena kita hanya menggunakan checkbox

                // Load old values if validation fails
                @if (old('tipe'))
                    $('#tipe').trigger('change');

                    // Restore old opsi values
                    @if (old('opsi'))
                        @foreach (old('opsi') as $index => $opsi)
                            setTimeout(function() {
                                $('input[name="opsi[{{ $index }}][teks]"]').val(
                                    '{{ addslashes($opsi['teks'] ?? '') }}');
                                @if (isset($opsi['bobot']))
                                    $('input[name="opsi[{{ $index }}][bobot]"]').val(
                                        '{{ $opsi['bobot'] }}');
                                @endif
                            }, 100);
                        @endforeach
                    @endif

                    @if (old('jawaban_benar'))
                        setTimeout(function() {
                            // Handle checkbox selection for old values (semua tipe menggunakan checkbox)
                            const oldJawaban = '{{ old('jawaban_benar') }}';
                            if (oldJawaban) {
                                const letters = oldJawaban.split(',');
                                letters.forEach(letter => {
                                    $(`.jawaban-checkbox[data-letter="${letter.trim()}"]`).prop(
                                        'checked', true);
                                });
                                // Trigger update to create hidden input
                                updateJawabanBenarFromCheckboxes('{{ old('tipe') }}');
                            }
                        }, 200);
                    @endif
                @endif

                // Form validation
                $('#soalForm').on('submit', function(e) {
                    const tipe = $('#tipe').val();
                    let valid = true;

                    // Check gambar upload for gambar type
                    if (tipe === 'gambar' && !$('#gambar')[0].files.length) {
                        alert('Gambar harus diupload untuk tipe Soal dengan Gambar');
                        valid = false;
                    }

                    // Check if at least 2 opsi filled for non benar_salah
                    if (tipe !== 'benar_salah') {
                        let filledOpsi = 0;
                        $('input[name*="[teks]"]').each(function() {
                            if ($(this).val().trim() !== '') {
                                filledOpsi++;
                            }
                        });

                        if (filledOpsi < 2) {
                            alert('Minimal harus ada 2 opsi jawaban yang diisi');
                            valid = false;
                        }
                    }

                    // Check jawaban benar untuk semua tipe (menggunakan checkbox)
                    if (tipe === 'pg_satu' || tipe === 'gambar') {
                        const checkedCount = $('.jawaban-checkbox:checked').length;
                        if (checkedCount !== 1) {
                            alert('Harus memilih tepat 1 jawaban benar untuk tipe ini');
                            valid = false;
                        }
                    }

                    // Check jawaban benar for pg_pilih_2
                    if (tipe === 'pg_pilih_2') {
                        const checkedCount = $('.jawaban-checkbox:checked').length;
                        if (checkedCount !== 2) {
                            alert('Harus memilih tepat 2 jawaban benar untuk tipe Pilihan Ganda (Pilih 2)');
                            valid = false;
                        }
                    }

                    // Check jawaban benar for benar_salah
                    if (tipe === 'benar_salah') {
                        const checkedCount = $('.jawaban-checkbox:checked').length;
                        if (checkedCount !== 1) {
                            alert('Harus memilih 1 jawaban benar untuk tipe Benar/Salah');
                            valid = false;
                        }
                    }

                    // Check bobot total for pg_bobot
                    if (tipe === 'pg_bobot') {
                        const kategoriId = $('#kategori_id').val();
                        const isKepribadian = checkIfKepribadianCategory(kategoriId);

                        console.log('Form validation - kategoriId:', kategoriId, 'isInKepribadianPackage:',
                            isKepribadian);

                        if (isKepribadian) {
                            // For categories in akademik package, validate each bobot is between 1-5
                            let hasInvalidBobot = false;
                            $('input[name*="[bobot]"]').each(function() {
                                const bobot = parseInt($(this).val()) || 0;
                                if (bobot < 1 || bobot > 5) {
                                    hasInvalidBobot = true;
                                    return false; // break loop
                                }
                            });

                            if (hasInvalidBobot) {
                                alert(
                                    'Bobot untuk kategori dalam paket akademik harus berupa bilangan bulat antara 1-5'
                                );
                                valid = false;
                            }
                        } else {
                            // For categories not in akademik package, just validate that bobot values are valid numbers
                            let hasInvalidBobot = false;
                            $('input[name*="[bobot]"]').each(function() {
                                const bobot = parseFloat($(this).val());
                                if (isNaN(bobot) || bobot <= 0) {
                                    hasInvalidBobot = true;
                                    return false; // break loop
                                }
                            });

                            if (hasInvalidBobot) {
                                alert('Semua bobot harus berupa angka positif yang valid');
                                valid = false;
                            }
                        }
                    }

                    if (!valid) {
                        e.preventDefault();
                    }

                    const pembType = $('input[name="pembahasan_type"]:checked').val();
                    if (pembType === 'image' || pembType === 'both') {
                        if (!$('#pembahasan_image')[0].files.length) {
                            alert('Gambar pembahasan harus diupload untuk tipe pembahasan ini');
                            e.preventDefault();
                        }
                    }
                    if (pembType === 'image') {
                        $('#pembahasan').val('');
                    }
                });
            });
        </script>
    @endpush

    @push('styles')
        <style>
            .opsi-item {
                align-items: center;
            }

            .badge {
                font-size: 14px;
                padding: 8px 12px;
            }

            .form-control:focus {
                border-color: #80bdff;
                box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
            }

            .form-check {
                display: flex;
                align-items: center;
                justify-content: center;
                height: 100%;
                padding: 8px;
            }

            .form-check-input {
                margin-right: 20px;
                transform: scale(1.2);
            }

            .form-check-label {
                font-size: 14px;
                font-weight: 500;
                margin-left: 10px;
            }

            input[type="number"] {
                text-align: center;
            }

            #opsi-container {
                background-color: #f8f9fa;
                border-radius: 5px;
                padding: 15px;
                margin: 15px 0;
            }

            .opsi-item:last-child {
                margin-bottom: 20px !important;
            }

            #add-opsi {
                margin-top: 15px;
                margin-bottom: 10px;
            }

            #gambar-group {
                background-color: #f0f8ff;
                border: 2px dashed #007bff;
                border-radius: 8px;
                padding: 20px;
                margin: 15px 0;
                transition: all 0.3s ease;
            }

            #gambar-group:hover {
                background-color: #e6f3ff;
                border-color: #0056b3;
            }

            .custom-file-label {
                cursor: pointer;
            }

            #image-preview {
                border: 1px solid #dee2e6;
                border-radius: 5px;
                padding: 10px;
                background-color: #fff;
            }

            #preview-img {
                border-radius: 5px;
            }
        </style>
    @endpush
@endsection
