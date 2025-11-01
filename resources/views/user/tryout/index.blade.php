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
                                                <span class="struktur-soal-label">Struktur Soal:</span>
                                                @if($tryout->blueprints && $tryout->blueprints->count() > 0)
                                                    @php
                                                        $strukturKode = $tryout->blueprints->groupBy('kategori_id')->map(function($group) {
                                                            $kategori = $group->first()->kategori;
                                                            $jumlah = $group->sum('jumlah');
                                                            return ($kategori ? $kategori->kode : 'N/A') . ' (' . $jumlah . ')';
                                                        })->implode(', ');
                                                    @endphp
                                                    @if($strukturKode)
                                                        <span class="badge badge-struktur">{{ $strukturKode }}</span>
                                                    @endif
                                                @endif
                                            </div>

                                            {{-- Info Cards --}}
                                            <div class="mb-3">
                                                <div class="info-cards-container">
                                                    <div class="info-card">
                                                        <div class="info-card-label">Total Soal</div>
                                                        <div class="info-card-value">{{ $tryout->total_soal ?? $tryout->blueprints->sum('jumlah') ?? 0 }}</div>
                                                    </div>
                                                    <div class="info-card">
                                                        <div class="info-card-label">Durasi</div>
                                                        <div class="info-card-value">{{ $tryout->durasi_menit }} menit</div>
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
                                                <span class="badge {{ $badgeClass }} badge-lg">
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

    /* Struktur Soal Styles */
    .struktur-soal-label {
        font-weight: 600;
        font-size: 0.9rem;
        color: #495057;
        margin-right: 0.5rem;
    }

    .badge-struktur {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 0.4rem 0.8rem;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 500;
        border: none;
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
        display: inline-block;
    }

    /* Info Cards Styles */
    .info-cards-container {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0.75rem;
    }

    .info-card {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border: 1px solid #dee2e6;
        border-radius: 12px;
        padding: 1rem;
        text-align: center;
        transition: all 0.3s ease;
    }

    .info-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        border-color: #007bff;
    }

    .info-card-label {
        font-size: 0.85rem;
        color: #6c757d;
        font-weight: 500;
        margin-bottom: 0.5rem;
    }

    .info-card-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: #007bff;
    }

    .badge-lg {
        font-size: 0.95rem;
        padding: 0.5rem 1rem;
        border-radius: 25px;
    }

    @media (max-width: 768px) {
        .info-cards-container {
            gap: 0.5rem;
        }

        .info-card {
            padding: 0.75rem;
        }

        .info-card-label {
            font-size: 0.8rem;
        }

        .info-card-value {
            font-size: 1.25rem;
        }
    }
</style>
@endpush
