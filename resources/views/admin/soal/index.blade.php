@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4><i class="fa fa-graduation-cap"></i> Manajemen Soal AKADEMIK</h4>
                        <div>
                            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#uploadModal">
                                <i class="fa fa-upload"></i> Upload Word
                            </button>
                            <a href="{{ route('admin.soal.create', ['type' => 'akademik']) }}" class="btn btn-primary">
                                <i class="fa fa-plus"></i> Tambah Soal
                            </a>
                        </div>
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

                        <!-- Filter -->
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <select class="form-control" id="filterKategori">
                                    <option value="">Semua Kategori</option>
                                    @foreach ($kategoris as $kategori)
                                        <option value="{{ $kategori->id }}"
                                            {{ request('kategori') == $kategori->id ? 'selected' : '' }}>
                                            {{ $kategori->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select class="form-control" id="filterTipe">
                                    <option value="">Semua Tipe</option>
                                    <option value="benar_salah" {{ request('tipe') == 'benar_salah' ? 'selected' : '' }}>
                                        Benar/Salah</option>
                                    <option value="pg_satu" {{ request('tipe') == 'pg_satu' ? 'selected' : '' }}>Pilihan
                                        Ganda (1 Jawaban)</option>
                                    <option value="pg_bobot" {{ request('tipe') == 'pg_bobot' ? 'selected' : '' }}>Pilihan
                                        Ganda (Bobot)</option>
                                    <option value="pg_pilih_2" {{ request('tipe') == 'pg_pilih_2' ? 'selected' : '' }}>Pilih
                                        2 Jawaban</option>
                                    <option value="gambar" {{ request('tipe') == 'gambar' ? 'selected' : '' }}>Soal Gambar
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select class="form-control" id="filterLevel">
                                    <option value="">Semua Level</option>
                                    <option value="dasar" {{ request('level') == 'dasar' ? 'selected' : '' }}>Dasar
                                    </option>
                                    <option value="mudah" {{ request('level') == 'mudah' ? 'selected' : '' }}>Mudah
                                    </option>
                                    <option value="sedang" {{ request('level') == 'sedang' ? 'selected' : '' }}>Sedang
                                    </option>
                                    <option value="sulit" {{ request('level') == 'sulit' ? 'selected' : '' }}>Sulit
                                    </option>
                                    <option value="tersulit" {{ request('level') == 'tersulit' ? 'selected' : '' }}>Tersulit
                                    </option>
                                    <option value="ekstrem" {{ request('level') == 'ekstrem' ? 'selected' : '' }}>Ekstrem
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Filter Info -->
                        @if (request('kategori') || request('tipe') || request('level'))
                            <div class="alert alert-info mb-3">
                                <strong>Filter Aktif:</strong>
                                @if (request('kategori'))
                                    Kategori:
                                    {{ $kategoris->where('id', request('kategori'))->first()->nama ?? 'Tidak ditemukan' }}
                                @endif
                                @if (request('kategori') && (request('tipe') || request('level')))
                                    |
                                @endif
                                @if (request('tipe'))
                                    Tipe:
                                    @switch(request('tipe'))
                                        @case('benar_salah')
                                            Benar/Salah
                                        @break

                                        @case('pg_satu')
                                            Pilihan Ganda (1 Jawaban)
                                        @break

                                        @case('pg_bobot')
                                            Pilihan Ganda (Bobot)
                                        @break

                                        @case('pg_pilih_2')
                                            Pilih 2 Jawaban
                                        @break

                                        @case('gambar')
                                            Soal Gambar
                                        @break
                                    @endswitch
                                @endif
                                @if ((request('kategori') || request('tipe')) && request('level'))
                                    |
                                @endif
                                @if (request('level'))
                                    Level:
                                    @switch(request('level'))
                                        @case('dasar')
                                            Dasar
                                        @break

                                        @case('mudah')
                                            Mudah
                                        @break

                                        @case('sedang')
                                            Sedang
                                        @break

                                        @case('sulit')
                                            Sulit
                                        @break

                                        @case('tersulit')
                                            Tersulit
                                        @break

                                        @case('ekstrem')
                                            Ekstrem
                                        @break
                                    @endswitch
                                @endif
                                | <strong>Total: {{ $soals->total() }} soal</strong>
                                <a href="{{ route('admin.soal.index') }}"
                                    class="btn btn-sm btn-outline-secondary ml-2">Hapus Filter</a>
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Pertanyaan</th>
                                        <th>Kategori</th>
                                        <th>Tipe</th>
                                        <th>Level</th>
                                        <th>Opsi</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($soals as $soal)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <div class="soal-preview">
                                                    {{ Str::limit(strip_tags($soal->pertanyaan), 100) }}
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge badge-info">{{ $soal->kategori->kode }}</span>
                                            </td>
                                            <td>
                                                @switch($soal->tipe)
                                                    @case('benar_salah')
                                                        <span class="badge badge-warning">Benar/Salah</span>
                                                    @break

                                                    @case('pg_satu')
                                                        <span class="badge badge-primary">PG (1 Jawaban)</span>
                                                    @break

                                                    @case('pg_bobot')
                                                        <span class="badge badge-success">PG (Bobot)</span>
                                                    @break

                                                    @case('pg_pilih_2')
                                                        <span class="badge badge-secondary">Pilih 2</span>
                                                    @break

                                                    @case('gambar')
                                                        <span class="badge badge-secondary">Soal Gambar</span>
                                                    @break
                                                @endswitch
                                            </td>
                                            <td>
                                                @switch($soal->level)
                                                    @case('dasar')
                                                        <span class="badge badge-info">Dasar</span>
                                                    @break

                                                    @case('mudah')
                                                        <span class="badge badge-success">Mudah</span>
                                                    @break

                                                    @case('sedang')
                                                        <span class="badge badge-warning">Sedang</span>
                                                    @break

                                                    @case('sulit')
                                                        <span class="badge badge-danger">Sulit</span>
                                                    @break

                                                    @case('tersulit')
                                                        <span class="badge badge-warning" style="background-color: #ff6b35 !important;">Tersulit</span>
                                                    @break

                                                    @case('ekstrem')
                                                        <span class="badge badge-danger" style="background-color: #8b0000 !important; color: white !important; font-weight: bold !important;">EKSTREM</span>
                                                    @break

                                                    @default
                                                        <span class="badge badge-secondary">Dasar</span>
                                                    @break
                                                @endswitch
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-outline-info"
                                                    data-toggle="modal" data-target="#opsiModal{{ $soal->id }}">
                                                    Lihat Opsi ({{ $soal->opsi->count() }})
                                                </button>
                                            </td>
                                            <td>
                                                @if ($soal->is_active)
                                                    <span class="badge badge-success">Aktif</span>
                                                @else
                                                    <span class="badge badge-danger">Nonaktif</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.soal.edit', $soal) }}"
                                                        class="btn btn-sm btn-warning">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-danger"
                                                        onclick="showDeleteModal({{ $soal->id }}, '{{ addslashes(Str::limit(strip_tags($soal->pertanyaan), 50)) }}')">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center">Tidak ada data soal</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            @if ($soals->hasPages())
                                <div class="row mt-4">
                                    <div class="col-sm-5">
                                        <div class="dataTables_info" role="status" aria-live="polite">
                                            Menampilkan {{ $soals->firstItem() ?? 0 }} sampai {{ $soals->lastItem() ?? 0 }} dari {{ $soals->total() }} data
                                        </div>
                                    </div>
                                    <div class="col-sm-7">
                                        <div class="dataTables_paginate paging_simple_numbers">
                                            <ul class="pagination">
                                                {{-- Previous Page Link --}}
                                                @if ($soals->onFirstPage())
                                                    <li class="paginate_button page-item previous disabled">
                                                        <a href="#" class="page-link" aria-controls="editable" data-dt-idx="0" tabindex="0">
                                                            <i class="fa fa-angle-double-left"></i>
                                                        </a>
                                                    </li>
                                                @else
                                                    <li class="paginate_button page-item previous">
                                                        <a href="{{ $soals->appends(request()->query())->previousPageUrl() }}" class="page-link" aria-controls="editable" data-dt-idx="0" tabindex="0" rel="prev">
                                                            <i class="fa fa-angle-double-left"></i>
                                                        </a>
                                                    </li>
                                                @endif

                                                {{-- Pagination Elements --}}
                                                @foreach ($soals->getUrlRange(1, $soals->lastPage()) as $page => $url)
                                                    @if ($page == $soals->currentPage())
                                                        <li class="paginate_button page-item active">
                                                            <a href="#" class="page-link" aria-controls="editable" data-dt-idx="{{ $page }}" tabindex="0">{{ $page }}</a>
                                                        </li>
                                                    @else
                                                        <li class="paginate_button page-item">
                                                            <a href="{{ $soals->appends(request()->query())->url($page) }}" class="page-link" aria-controls="editable" data-dt-idx="{{ $page }}" tabindex="0">{{ $page }}</a>
                                                        </li>
                                                    @endif
                                                @endforeach

                                                {{-- Next Page Link --}}
                                                @if ($soals->hasMorePages())
                                                    <li class="paginate_button page-item next">
                                                        <a href="{{ $soals->appends(request()->query())->nextPageUrl() }}" class="page-link" aria-controls="editable" data-dt-idx="0" tabindex="0" rel="next">
                                                            <i class="fa fa-angle-double-right"></i>
                                                        </a>
                                                    </li>
                                                @else
                                                    <li class="paginate_button page-item next disabled">
                                                        <a href="#" class="page-link" aria-controls="editable" data-dt-idx="0" tabindex="0">
                                                            <i class="fa fa-angle-double-right"></i>
                                                        </a>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Opsi untuk setiap soal -->
        @foreach ($soals as $soal)
            <div class="modal fade" id="opsiModal{{ $soal->id }}" tabindex="-1" role="dialog"
                aria-labelledby="opsiModalLabel{{ $soal->id }}" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="opsiModalLabel{{ $soal->id }}">Opsi Soal</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <h6>Pertanyaan:</h6>
                            <div class="mb-3 p-2 bg-light rounded">
                                {!! $soal->pertanyaan !!}
                            </div>

                            {{-- Tampilkan gambar jika tipe soal adalah gambar --}}
                            @if ($soal->tipe === 'gambar' && $soal->gambar)
                                <h6>Gambar Soal:</h6>
                                <div class="mb-3 text-center">
                                    <img src="{{ asset('storage/' . $soal->gambar) }}" alt="Gambar Soal"
                                        class="img-fluid rounded shadow-sm"
                                        style="max-height: 400px; max-width: 100%; object-fit: contain;">
                                </div>
                            @endif

                            <h6>Opsi Jawaban:</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead class="thead-light">
                                        <tr>
                                            <th width="10%">Opsi</th>
                                            <th width="70%">Teks</th>
                                            <th width="20%">Bobot</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($soal->opsi as $opsi)
                                            <tr class="{{ $opsi->bobot > 0 ? 'table-success' : '' }}">
                                                <td class="text-center">
                                                    <strong>{{ $opsi->opsi }}</strong>
                                                </td>
                                                <td>{{ $opsi->teks }}</td>
                                                <td class="text-center">
                                                    <span
                                                        class="badge badge-{{ $opsi->bobot > 0 ? 'success' : 'secondary' }}">
                                                        {{ $opsi->bobot }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            {{-- Tampilkan jawaban benar jika ada --}}
                            @if ($soal->jawaban_benar)
                                <h6>Jawaban Benar:</h6>
                                <div class="mb-3 p-2 bg-success text-white rounded">
                                    <strong>{{ $soal->jawaban_benar }}</strong>
                                </div>
                            @endif

                            {{-- Tampilkan pembahasan jika ada --}}
                            @if ($soal->pembahasan)
                                <h6>Pembahasan:</h6>
                                <div class="mb-3 p-2 bg-info text-white rounded">
                                    {!! $soal->pembahasan !!}
                                </div>
                            @endif
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        <!-- Upload Modal -->
        <div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="uploadModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="uploadModalLabel">Upload Soal dari Word</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('admin.soal.upload-word') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="file">File Word (.docx) <span class="text-danger">*</span></label>
                                <input type="file" class="form-control-file" name="file" accept=".docx" required>
                                <small class="form-text text-muted">
                                    Format file harus sesuai dengan template yang telah ditentukan
                                </small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-upload"></i> Upload
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Hapus Soal -->
        <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="deleteModalLabel">
                            <i class="fa fa-exclamation-triangle"></i> Konfirmasi Hapus Soal
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center mb-3">
                            <i class="fa fa-trash fa-3x text-danger"></i>
                        </div>
                        <p class="text-center">
                            Apakah Anda yakin ingin menghapus soal berikut?
                        </p>
                        <div class="alert alert-warning">
                            <strong>Pertanyaan:</strong>
                            <div id="soalPreview" class="mt-2 p-2 bg-light rounded"></div>
                        </div>
                        <div class="alert alert-danger">
                            <i class="fa fa-warning"></i>
                            <strong>Peringatan:</strong> Tindakan ini tidak dapat dibatalkan. Semua data terkait soal ini akan
                            dihapus permanen.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fa fa-times"></i> Batal
                        </button>
                        <form id="deleteForm" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fa fa-trash"></i> Ya, Hapus Soal
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @push('styles')
        <style>
            /* Ensure proper spacing from footer */
            .card {
                margin-bottom: 30px;
            }

            .table-responsive {
                margin-bottom: 20px;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            $(document).ready(function() {
                // Filter functionality
                $('#filterKategori, #filterTipe, #filterLevel').change(function() {
                    var kategori = $('#filterKategori').val();
                    var tipe = $('#filterTipe').val();
                    var level = $('#filterLevel').val();

                    // Build query string
                    var params = new URLSearchParams();
                    if (kategori) params.append('kategori', kategori);
                    if (tipe) params.append('tipe', tipe);
                    if (level) params.append('level', level);

                    // Redirect with filters
                    var url = window.location.pathname;
                    if (params.toString()) {
                        url += '?' + params.toString();
                    }
                    window.location.href = url;
                });

                // Handle modal events
                $('.modal').on('show.bs.modal', function() {
                    $('body').addClass('modal-open');
                });

                $('.modal').on('hidden.bs.modal', function() {
                    if (!$('.modal:visible').length) {
                        $('body').removeClass('modal-open');
                    }
                });
            });

            // Function to show delete modal
            function showDeleteModal(soalId, soalPertanyaan) {
                $('#deleteForm').attr('action', `/admin/soal/${soalId}`);
                $('#soalPreview').text(soalPertanyaan);
                $('#deleteModal').modal('show');
            }
        </script>
    @endpush
