@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4>Kelola Paket</h4>
                        <a href="{{ route('admin.packages.create') }}" class="btn btn-primary">
                            <i class="fa fa-plus"></i> Tambah Paket
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

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Paket</th>
                                        <th>Harga</th>
                                        <th>Label</th>
                                        <th>Urutan</th>
                                        <th>Status</th>
                                        <th>Jumlah Fitur</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($packages as $package)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <strong>{{ $package->name }}</strong>
                                                @if($package->description)
                                                    <br><small class="text-muted">{{ Str::limit($package->description, 50) }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($package->old_price)
                                                    <small class="text-muted text-decoration-line-through">Rp {{ number_format($package->old_price, 0, ',', '.') }}</small><br>
                                                @endif
                                                <strong>Rp {{ number_format($package->price, 0, ',', '.') }}</strong>
                                            </td>
                                            <td>
                                                @if($package->label)
                                                    <span class="badge badge-info">{{ $package->label }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-secondary">{{ $package->sort_order }}</span>
                                            </td>
                                            <td>
                                                @if ($package->is_active)
                                                    <span class="badge badge-success">Aktif</span>
                                                @else
                                                    <span class="badge badge-danger">Nonaktif</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-primary">{{ count($package->features) }} fitur</span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.packages.show', $package) }}"
                                                        class="btn btn-sm btn-info" title="Lihat Detail">
                                                        <i class="fa fa-eye"></i>
                                                    </a>

                                                    <a href="{{ route('admin.packages.edit', $package) }}"
                                                        class="btn btn-sm btn-warning" title="Edit">
                                                        <i class="fa fa-edit"></i>
                                                    </a>

                                                    <form action="{{ route('admin.packages.toggle-status', $package) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-{{ $package->is_active ? 'secondary' : 'success' }}" 
                                                                title="{{ $package->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                                            <i class="fa fa-toggle-{{ $package->is_active ? 'on' : 'off' }}"></i>
                                                        </button>
                                                    </form>

                                                    <form action="{{ route('admin.packages.destroy', $package) }}"
                                                        method="POST" class="d-inline"
                                                        onsubmit="return confirm('Yakin ingin menghapus package ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">Tidak ada data paket</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
