@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4><i class="fa fa-graduation-cap"></i> Kategori Soal AKADEMIK</h4>
                        <a href="{{ route('admin.kategori.create', ['type' => 'akademik']) }}" class="btn btn-primary">
                            <i class="fa fa-plus"></i> Tambah Kategori
                        </a>
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        <!-- Filter -->
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <select class="form-control" id="filterStatus">
                                    <option value="">Semua Status</option>
                                    <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                    <option value="nonaktif" {{ request('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="filterQ" placeholder="Cari nama/kode..." value="{{ request('q') }}">
                                    <div class="input-group-append">
                                        <button class="btn btn-secondary" type="button" id="btnSearch"><i class="fa fa-search"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <input type="number" min="0" class="form-control" id="filterMinSoal" placeholder="Min soal" value="{{ request('min_soal') }}">
                            </div>
                            <div class="col-md-3">
                                <input type="number" min="0" class="form-control" id="filterMaxSoal" placeholder="Max soal" value="{{ request('max_soal') }}">
                            </div>
                        </div>

                        <!-- Filter Info -->
                        @if (request('status') || request('q') || request('min_soal') || request('max_soal'))
                            <div class="alert alert-info mb-3">
                                <strong>Filter Aktif:</strong>
                                @php $first = true; @endphp
                                @if (request('status'))
                                    Status: {{ request('status') == 'aktif' ? 'Aktif' : 'Nonaktif' }} @php $first = false; @endphp
                                @endif
                                @if (request('q'))
                                    @if(!$first) | @endif Cari: "{{ request('q') }}" @php $first = false; @endphp
                                @endif
                                @if (request('min_soal'))
                                    @if(!$first) | @endif Min Soal: {{ request('min_soal') }} @php $first = false; @endphp
                                @endif
                                @if (request('max_soal'))
                                    @if(!$first) | @endif Max Soal: {{ request('max_soal') }}
                                @endif
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Kode</th>
                                        <th>Nama</th>
                                        <th>Deskripsi</th>
                                        <th>Jumlah Soal</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($kategoris as $kategori)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td><span class="badge badge-info">{{ $kategori->kode }}</span></td>
                                            <td>{{ $kategori->nama }}</td>
                                            <td>{{ Str::limit($kategori->deskripsi, 50) }}</td>
                                            <td>
                                                <span class="badge badge-secondary">{{ $kategori->soals_count }}</span>
                                            </td>
                                            <td>
                                                @if ($kategori->is_active)
                                                    <span class="badge badge-success">Aktif</span>
                                                @else
                                                    <span class="badge badge-danger">Nonaktif</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.kategori.edit', $kategori) }}"
                                                        class="btn btn-sm btn-warning">
                                                        <i class="fa fa-edit"></i>
                                                    </a>

                                                    <form action="{{ route('admin.kategori.toggle-status', $kategori) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-info">
                                                            <i class="fa fa-toggle-on"></i>
                                                        </button>
                                                    </form>

                                                    @if ($kategori->soals_count == 0)
                                                        <form action="{{ route('admin.kategori.destroy', $kategori) }}"
                                                            method="POST" class="d-inline"
                                                            onsubmit="return confirm('Yakin ingin menghapus kategori ini?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">Tidak ada data kategori</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if ($kategoris->hasPages())
                            <div class="row">
                                <div class="col-sm-5">
                                    <div class="dataTables_info" role="status" aria-live="polite">
                                        Menampilkan {{ $kategoris->firstItem() ?? 0 }} sampai {{ $kategoris->lastItem() ?? 0 }} dari {{ $kategoris->total() }} data
                                    </div>
                                </div>
                                <div class="col-sm-7">
                                    <div class="dataTables_paginate paging_simple_numbers">
                                        <ul class="pagination">
                                            {{-- Previous Page Link --}}
                                            @if ($kategoris->onFirstPage())
                                                <li class="paginate_button page-item previous disabled">
                                                    <a href="#" class="page-link" aria-controls="editable" data-dt-idx="0" tabindex="0">
                                                        <i class="fa fa-angle-double-left"></i>
                                                    </a>
                                                </li>
                                            @else
                                                <li class="paginate_button page-item previous">
                                                    <a href="{{ $kategoris->previousPageUrl() }}" class="page-link" aria-controls="editable" data-dt-idx="0" tabindex="0" rel="prev">
                                                        <i class="fa fa-angle-double-left"></i>
                                                    </a>
                                                </li>
                                            @endif

                                            {{-- Pagination Elements --}}
                                            @foreach ($kategoris->getUrlRange(1, $kategoris->lastPage()) as $page => $url)
                                                @if ($page == $kategoris->currentPage())
                                                    <li class="paginate_button page-item active">
                                                        <a href="#" class="page-link" aria-controls="editable" data-dt-idx="{{ $page }}" tabindex="0">{{ $page }}</a>
                                                    </li>
                                                @else
                                                    <li class="paginate_button page-item">
                                                        <a href="{{ $url }}" class="page-link" aria-controls="editable" data-dt-idx="{{ $page }}" tabindex="0">{{ $page }}</a>
                                                    </li>
                                                @endif
                                            @endforeach

                                            {{-- Next Page Link --}}
                                            @if ($kategoris->hasMorePages())
                                                <li class="paginate_button page-item next">
                                                    <a href="{{ $kategoris->nextPageUrl() }}" class="page-link" aria-controls="editable" data-dt-idx="0" tabindex="0" rel="next">
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
@endsection
@push('scripts')
<script>
$(document).ready(function() {
    function applyFilters() {
        var status = $('#filterStatus').val();
        var q = $('#filterQ').val();
        var minSoal = $('#filterMinSoal').val();
        var maxSoal = $('#filterMaxSoal').val();

        var params = new URLSearchParams();
        if (status) params.append('status', status);
        if (q) params.append('q', q);
        if (minSoal) params.append('min_soal', minSoal);
        if (maxSoal) params.append('max_soal', maxSoal);

        var url = window.location.pathname;
        if (params.toString()) {
            url += '?' + params.toString();
        }
        window.location.href = url;
    }

    $('#filterStatus').change(applyFilters);
    $('#btnSearch').click(applyFilters);
    $('#filterQ').keypress(function(e) { if (e.which === 13) applyFilters(); });
    $('#filterMinSoal, #filterMaxSoal').change(applyFilters);
});
</script>
@endpush
