@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Manajemen Tryout</h4>
                    <a href="{{ route('admin.tryout.create') }}" class="btn btn-primary">
                        <i class="fa fa-plus"></i> Buat Tryout
                    </a>
                </div>
                <div class="card-body">
                    
                    <!-- Filter -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <select class="form-control" id="filterJenis">
                                <option value="">Semua Jenis</option>
                                <option value="kecerdasan" {{ request('jenis') == 'kecerdasan' ? 'selected' : '' }}>Kecerdasan</option>
                                <option value="kepribadian" {{ request('jenis') == 'kepribadian' ? 'selected' : '' }}>Kepribadian</option>
                                <option value="lengkap" {{ request('jenis') == 'lengkap' ? 'selected' : '' }}>Lengkap</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-control" id="filterAkses">
                                <option value="">Semua Akses</option>
                                <option value="free" {{ request('akses') == 'free' ? 'selected' : '' }}>Free</option>
                                <option value="premium" {{ request('akses') == 'premium' ? 'selected' : '' }}>Premium</option>
                                <option value="vip" {{ request('akses') == 'vip' ? 'selected' : '' }}>VIP</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-control" id="filterStatus">
                                <option value="">Semua Status</option>
                                <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="nonaktif" {{ request('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <input type="text" class="form-control" id="filterQ" placeholder="Cari judul..." value="{{ request('q') }}">
                                <div class="input-group-append">
                                    <button class="btn btn-secondary" type="button" id="btnSearch"><i class="fa fa-search"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filter Info -->
                    @if (request('jenis') || request('akses') || request('status') || request('q'))
                        <div class="alert alert-info mb-3">
                            <strong>Filter Aktif:</strong>
                            @php $first = true; @endphp
                            @if (request('jenis'))
                                Jenis: {{ ucfirst(request('jenis')) }} @php $first = false; @endphp
                            @endif
                            @if (request('akses'))
                                @if(!$first) | @endif Akses: {{ ucfirst(request('akses')) }} @php $first = false; @endphp
                            @endif
                            @if (request('status'))
                                @if(!$first) | @endif Status: {{ request('status') == 'aktif' ? 'Aktif' : 'Nonaktif' }} @php $first = false; @endphp
                            @endif
                            @if (request('q'))
                                @if(!$first) | @endif Cari: "{{ request('q') }}"
                            @endif
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Judul</th>
                                    <th>Deskripsi</th>
                                    <th>Struktur Soal</th>
                                    <th>Durasi</th>
                                    <th>Akses Paket</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tryouts as $tryout)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <strong>{{ $tryout->judul }}</strong>
                                    </td>
                                    <td>{{ Str::limit($tryout->deskripsi, 50) }}</td>
                                    <td>
                                        @if($tryout->blueprints && $tryout->blueprints->count() > 0)
                                            @php
                                                $groupedBlueprints = $tryout->blueprints->groupBy('kategori_id');
                                            @endphp
                                            @foreach($groupedBlueprints as $kategoriId => $blueprints)
                                                @php
                                                    $kategori = \App\Models\KategoriSoal::find($kategoriId);
                                                    $totalKategori = $blueprints->sum('jumlah');
                                                @endphp
                                                @if($kategori && $totalKategori > 0)
                                                    <span class="badge badge-info">
                                                        {{ $kategori->kode }}: {{ $totalKategori }}
                                                    </span>
                                                @endif
                                            @endforeach
                                        @else
                                            @foreach($tryout->struktur as $kategoriId => $jumlah)
                                                @php
                                                    $kategori = \App\Models\KategoriSoal::find($kategoriId);
                                                @endphp
                                                @if($kategori && $jumlah > 0)
                                                    <span class="badge badge-info">
                                                        {{ $kategori->kode }}: {{ $jumlah }}
                                                    </span>
                                                @endif
                                            @endforeach
                                        @endif
                                        <br>
                                        <small class="text-muted">
                                            Total: {{ $tryout->total_soal }} soal
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge badge-warning">
                                            {{ $tryout->durasi_menit }} menit
                                        </span>
                                    </td>
                                    <td>
                                        @switch($tryout->akses_paket)
                                            @case('free')
                                                <span class="badge badge-success">Free</span>
                                                @break
                                            @case('premium')
                                                <span class="badge badge-primary">Premium</span>
                                                @break
                                            @case('vip')
                                                <span class="badge badge-danger">VIP</span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td>
                                        @if($tryout->is_active)
                                            <span class="badge badge-success">Aktif</span>
                                        @else
                                            <span class="badge badge-danger">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1" role="group">
                                            <a href="{{ route('admin.tryout.show', $tryout) }}" 
                                               class="btn btn-sm btn-info" title="Lihat Detail">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.tryout.edit', $tryout) }}" 
                                               class="btn btn-sm btn-warning" title="Edit">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            @if ($tryout->is_active)
                                                <button type="button" class="btn btn-sm btn-secondary" 
                                                        onclick="toggleStatus({{ $tryout->id }}, false)" title="Nonaktifkan">
                                                    <i class="fa fa-pause"></i>
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-sm btn-success" 
                                                        onclick="toggleStatus({{ $tryout->id }}, true)" title="Aktifkan">
                                                    <i class="fa fa-play"></i>
                                                </button>
                                            @endif
                                            <button type="button" class="btn btn-sm btn-danger" 
                                                    onclick="showDeleteModal({{ $tryout->id }}, '{{ $tryout->judul }}')" title="Hapus">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">Tidak ada data tryout</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center">
                        {{ $tryouts->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Hapus Tryout -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="fa fa-exclamation-triangle"></i> Konfirmasi Hapus Tryout
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
                    Apakah Anda yakin ingin menghapus tryout <strong id="tryoutTitle"></strong>?
                </p>
                <div class="alert alert-warning">
                    <i class="fa fa-warning"></i>
                    <strong>Peringatan:</strong> Tindakan ini tidak dapat dibatalkan. Semua data terkait tryout ini akan dihapus permanen.
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
                        <i class="fa fa-trash"></i> Ya, Hapus Tryout
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Toggle Status -->
<div class="modal fade" id="toggleStatusModal" tabindex="-1" role="dialog" aria-labelledby="toggleStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="toggleStatusModalLabel">
                    <i class="fa fa-toggle-on"></i> Konfirmasi Ubah Status
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="fa fa-question-circle fa-3x text-primary"></i>
                </div>
                <p class="text-center" id="toggleStatusMessage">
                    <!-- Message will be inserted here -->
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fa fa-times"></i> Batal
                </button>
                <button type="button" class="btn btn-primary" id="confirmToggleStatus">
                    <i class="fa fa-check"></i> Konfirmasi
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    function applyFilters() {
        var jenis = $('#filterJenis').val();
        var akses = $('#filterAkses').val();
        var status = $('#filterStatus').val();
        var q = $('#filterQ').val();

        var params = new URLSearchParams();
        if (jenis) params.append('jenis', jenis);
        if (akses) params.append('akses', akses);
        if (status) params.append('status', status);
        if (q) params.append('q', q);

        var url = window.location.pathname;
        if (params.toString()) {
            url += '?' + params.toString();
        }
        window.location.href = url;
    }

    $('#filterJenis, #filterAkses, #filterStatus').change(applyFilters);
    $('#btnSearch').click(applyFilters);
    $('#filterQ').keypress(function(e) { if (e.which === 13) applyFilters(); });

    // Fix modal close functionality
    $('.modal .close, .modal [data-dismiss="modal"]').on('click', function() {
        $(this).closest('.modal').modal('hide');
    });

    // Close modal on escape key
    $(document).on('keydown', function(e) {
        if (e.keyCode === 27) { // Escape key
            $('.modal.show').modal('hide');
        }
    });

    // Close modal when clicking outside
    $('.modal').on('click', function(e) {
        if (e.target === this) {
            $(this).modal('hide');
        }
    });
});

function showDeleteModal(tryoutId, tryoutTitle) {
    $('#tryoutTitle').text(tryoutTitle);
    $('#deleteForm').attr('action', `/admin/tryout/${tryoutId}`);
    $('#deleteModal').modal('show');
}

function toggleStatus(tryoutId, newStatus) {
    const message = newStatus ?
        'Apakah Anda yakin ingin mengaktifkan tryout ini?' :
        'Apakah Anda yakin ingin menonaktifkan tryout ini?';

    $('#toggleStatusMessage').text(message);

    // Hapus event listener sebelumnya untuk menghindari duplikasi
    $('#confirmToggleStatus').off('click');

    $('#toggleStatusModal').modal('show');

    $('#confirmToggleStatus').on('click', function() {
        const $button = $(this);
        $button.prop('disabled', true).text('Memproses...');

        $.ajax({
            url: `/admin/tryout/${tryoutId}/toggle-status`,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                is_active: newStatus
            },
            success: function(response) {
                $('#toggleStatusModal').modal('hide');
                // Tambahkan delay kecil sebelum reload
                setTimeout(function() {
                    location.reload();
                }, 300);
            },
            error: function(xhr) {
                $button.prop('disabled', false).text('Konfirmasi');
                
                let errorMessage = 'Terjadi kesalahan. Silakan coba lagi.';
                
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.status === 422) {
                    errorMessage = 'Data tidak valid. Pastikan semua field diisi dengan benar.';
                } else if (xhr.status === 404) {
                    errorMessage = 'Tryout tidak ditemukan.';
                } else if (xhr.status === 500) {
                    errorMessage = 'Terjadi kesalahan server. Silakan hubungi administrator.';
                }
                
                alert(errorMessage);
            }
        });
    });
}
</script>
@endpush 