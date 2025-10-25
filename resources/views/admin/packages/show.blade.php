@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4>Detail Paket: {{ $package->name }}</h4>
                        <div>
                            <a href="{{ route('admin.packages.edit', $package) }}" class="btn btn-warning">
                                <i class="fa fa-edit"></i> Edit
                            </a>
                            <a href="{{ route('admin.packages.index') }}" class="btn btn-secondary">
                                <i class="fa fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <table class="table table-borderless">
                                    <tr>
                                        <td width="200"><strong>Nama Paket:</strong></td>
                                        <td>{{ $package->name }}</td>
                                    </tr>
                                    @if($package->description)
                                    <tr>
                                        <td><strong>Deskripsi:</strong></td>
                                        <td>{{ $package->description }}</td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <td><strong>Harga:</strong></td>
                                        <td>
                                            @if($package->old_price)
                                                <small class="text-muted text-decoration-line-through">Rp {{ number_format($package->old_price, 0, ',', '.') }}</small><br>
                                            @endif
                                            <strong class="text-primary">Rp {{ number_format($package->price, 0, ',', '.') }}</strong>
                                        </td>
                                    </tr>
                                    @if($package->label)
                                    <tr>
                                        <td><strong>Label:</strong></td>
                                        <td><span class="badge badge-info">{{ $package->label }}</span></td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <td><strong>Urutan Tampil:</strong></td>
                                        <td><span class="badge badge-secondary">{{ $package->sort_order }}</span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Status:</strong></td>
                                        <td>
                                            @if ($package->is_active)
                                                <span class="badge badge-success">Aktif</span>
                                            @else
                                                <span class="badge badge-danger">Nonaktif</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Dibuat:</strong></td>
                                        <td>{{ $package->created_at->format('d M Y H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Diupdate:</strong></td>
                                        <td>{{ $package->updated_at->format('d M Y H:i') }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5>Preview Paket</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="pricing-card" style="border: 2px solid #20c997; border-radius: 12px; padding: 20px; position: relative;">
                                            @if($package->label)
                                                <div class="package-label" style="background: white; color: #20c997; font-weight: bold; padding: 5px 20px; border: 2px solid #20c997; border-radius: 20px; position: absolute; top: -15px; left: 50%; transform: translateX(-50%); white-space: nowrap;">
                                                    {{ $package->label }}
                                                </div>
                                            @endif
                                            
                                            <h2 class="package-name" style="font-weight: bold; font-size: 1.5rem; text-align: center; margin-bottom: 1rem;">
                                                {{ $package->name }}
                                            </h2>
                                            
                                            @if($package->old_price)
                                                <div class="old-price" style="color: #6c757d; text-decoration: line-through; font-size: 1rem; text-align: center;">
                                                    Rp {{ number_format($package->old_price, 0, ',', '.') }}
                                                </div>
                                            @endif
                                            
                                            <div class="current-price" style="font-size: 2rem; font-weight: bold; text-align: center; margin-bottom: 1rem;">
                                                Rp {{ number_format($package->price, 0, ',', '.') }},-
                                            </div>
                                            
                                            <button class="register-button" style="background: #20c997; color: white; border: none; width: 100%; padding: 10px; border-radius: 6px; font-weight: bold; margin-bottom: 1rem;">
                                                DAFTAR SEKARANG
                                            </button>
                                            
                                            <p class="section-title" style="color: #444; margin-bottom: 1rem;">Akses yang didapat:</p>
                                            
                                            <ul class="feature-list" style="list-style: none; padding-left: 0;">
                                                @foreach($package->features as $feature)
                                                    <li style="margin-bottom: 1rem; display: flex; align-items: start; gap: 10px;">
                                                        <span class="feature-check" style="color: #20c997; font-weight: bold; flex-shrink: 0;">✓</span>
                                                        <span>{{ $feature }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
