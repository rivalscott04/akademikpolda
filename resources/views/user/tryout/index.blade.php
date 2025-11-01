@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4>Daftar Tryout Tersedia</h4>
                                <p class="text-muted mb-0">
                                    Paket Anda:
                                    <span
                                        class="badge badge-{{ auth()->user()->paket_akses === 'lengkap' ? 'danger' : 'primary' }}">
                                        {{ strtoupper(auth()->user()->paket_akses) }}
                                    </span>
                                    <small class="text-muted">({{ auth()->user()->getAvailableTryoutsCount() }} tryout
                                        tersedia)</small>
                                </p>
                            </div>
                            @if (auth()->user()->paket_akses === 'lengkap')
                                <div>
                                    <a href="{{ route('user.tryout.paket-lengkap.status') }}" class="btn btn-info btn-sm">
                                        <i class="fa fa-trophy"></i> Status Paket Lengkap
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        <div class="row">
                            @forelse($tryouts as $tryout)
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card h-100">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">{{ $tryout->judul }}</h5>
                                        </div>
                                        <div class="card-body">
                                            <p class="card-text">{{ Str::limit($tryout->deskripsi, 100) }}</p>

                                            {{-- Struktur Soal --}}
                                            <div class="mb-2">
                                                <span class="text-muted">Struktur Soal:</span>
                                                @if($tryout->blueprints && $tryout->blueprints->count() > 0)
                                                    @php
                                                        $strukturKode = $tryout->blueprints->groupBy('kategori_id')->map(function($group) {
                                                            $kategori = $group->first()->kategori;
                                                            $jumlah = $group->sum('jumlah');
                                                            return ($kategori ? $kategori->kode : 'N/A') . ' (' . $jumlah . ')';
                                                        })->implode(', ');
                                                    @endphp
                                                    @if($strukturKode)
                                                        <span class="badge badge-info">{{ $strukturKode }}</span>
                                                    @endif
                                                @endif
                                            </div>

                                            {{-- Info Cards --}}
                                            <div class="row mb-3">
                                                <div class="col-6">
                                                    <div class="widget-text-box" style="padding: 10px 15px; margin: 0;">
                                                        <div class="row">
                                                            <div class="col-12 text-center">
                                                                <span class="text-muted" style="font-size: 11px;">Total Soal</span>
                                                                <h3 class="font-bold m-t-xs m-b-none" style="font-size: 20px;">{{ $tryout->total_soal ?? $tryout->blueprints->sum('jumlah') ?? 0 }}</h3>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="widget-text-box" style="padding: 10px 15px; margin: 0;">
                                                        <div class="row">
                                                            <div class="col-12 text-center">
                                                                <span class="text-muted" style="font-size: 11px;">Durasi</span>
                                                                <h3 class="font-bold m-t-xs m-b-none" style="font-size: 20px;">{{ $tryout->durasi_menit }} menit</h3>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Dynamic Paket Badge --}}
                                            @php
                                                $badgeClass = 'badge-primary';
                                                if ($tryout->jenis_paket === 'lengkap') {
                                                    $badgeClass = 'badge-danger';
                                                } elseif (in_array($tryout->jenis_paket, ['bahasa_inggris', 'pu'])) {
                                                    $badgeClass = 'badge-info';
                                                } elseif (in_array($tryout->jenis_paket, ['twk', 'numerik'])) {
                                                    $badgeClass = 'badge-warning';
                                                } elseif ($tryout->jenis_paket === 'free') {
                                                    $badgeClass = 'badge-success';
                                                }
                                            @endphp
                                            <div class="text-center mb-3">
                                                <span class="badge {{ $badgeClass }}">
                                                    {{ strtoupper(str_replace('_', ' ', $tryout->jenis_paket)) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="card-footer">
                                            <!-- Single button for all packages -->
                                            <a href="{{ route('user.tryout.start', $tryout) }}?type={{ $tryout->jenis_paket }}"
                                                class="btn btn-primary btn-block">
                                                <i class="fa fa-play"></i> Mulai Tryout
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <div class="text-center py-5">
                                        <i class="fa fa-book fa-3x text-muted mb-3"></i>
                                        <h5>Tidak ada tryout tersedia</h5>
                                        <p class="text-muted">
                                            @php $t = request('type'); @endphp
                                            @if ($t === 'bahasa_inggris')
                                                Tidak ada tryout bahasa Inggris yang tersedia untuk paket Anda saat ini.
                                            @elseif($t === 'pengetahuan_umum')
                                                Tidak ada tryout pengetahuan umum yang tersedia untuk paket Anda saat ini.
                                            @elseif($t === 'twk')
                                                Tidak ada tryout TWK yang tersedia untuk paket Anda saat ini.
                                            @elseif($t === 'numerik')
                                                Tidak ada tryout penalaran numerik yang tersedia untuk paket Anda saat ini.
                                            @else
                                                Saat ini belum ada tryout yang tersedia untuk paket Anda.
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                        
                        {{-- Pagination --}}
                        @if($tryouts->hasPages())
                            <div class="d-flex justify-content-center mt-4">
                                {{ $tryouts->appends(request()->query())->links('pagination::bootstrap-4') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .pagination {
        margin: 20px 0;
        justify-content: center;
    }

    .pagination .page-link {
        padding: 0.5rem 0.75rem;
        font-size: 0.9rem;
        border: 1px solid #dee2e6;
        color: #007bff;
        background-color: #fff;
        margin: 0 2px;
        border-radius: 4px;
    }

    .pagination .page-link:hover {
        color: #0056b3;
        background-color: #e9ecef;
        border-color: #dee2e6;
        text-decoration: none;
    }

    .pagination .page-item.active .page-link {
        color: #fff;
        background-color: #007bff;
        border-color: #007bff;
    }

    .pagination .page-item.disabled .page-link {
        color: #6c757d;
        background-color: #fff;
        border-color: #dee2e6;
    }

    .pagination .page-link i {
        font-size: 0.8rem;
    }
</style>
@endpush
