@extends('layouts.app')

@section('title', 'Laporan Paket Lengkap')

@section('content')
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-title">
                    <h5><i class="fa fa-graduation-cap"></i> Laporan Paket Lengkap</h5>
                    <div class="ibox-tools">
                        <a href="{{ route('admin.laporan.kemampuan.index') }}" class="btn btn-default btn-sm">
                            <i class="fa fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="text-center mb-4">
                                <h3>Pilih Siswa untuk Laporan Paket Lengkap</h3>
                                <p class="text-muted">Pilih siswa untuk melihat analisis kemampuan secara menyeluruh</p>
                            </div>
                        </div>
                    </div>
                    
                    @if($users->count() > 0)
                        <div class="row">
                            @foreach($users as $user)
                                <div class="col-lg-4 col-md-6 mb-4">
                                    <div class="siswa-card">
                                        <div class="card-body">
                                            <div class="siswa-avatar">
                                                <i class="fa fa-user fa-3x text-primary"></i>
                                            </div>
                                            <h5 class="siswa-name">{{ $user->name }}</h5>
                                            <p class="siswa-email text-muted">{{ $user->email }}</p>
                                            
                                            <div class="siswa-stats">
                                                <div class="stat-item">
                                                    <span class="stat-label">Total Tes:</span>
                                                    <span class="stat-value">{{ $user->hasilTes->count() }}</span>
                                                </div>
                                                <div class="stat-item">
                                                    <span class="stat-label">Tes Terakhir:</span>
                                                    <span class="stat-value">{{ $user->hasilTes->first() ? $user->hasilTes->first()->created_at->format('d M Y') : '-' }}</span>
                                                </div>
                                            </div>
                                            
                                            <form action="{{ route('admin.laporan.kemampuan.generate-paket-lengkap') }}" method="POST" class="mt-3">
                                                @csrf
                                                <input type="hidden" name="user_id" value="{{ $user->id }}">
                                                <button type="submit" class="btn btn-primary btn-block">
                                                    <i class="fa fa-chart-line"></i> Lihat Laporan
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center">
                            <div class="empty-state">
                                <i class="fa fa-users fa-4x text-muted"></i>
                                <h4 class="mt-3">Belum Ada Data Siswa</h4>
                                <p class="text-muted">Belum ada siswa yang memiliki data tes untuk dianalisis.</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="{{ asset('css/laporan-kemampuan.css') }}" rel="stylesheet">
<style>
.siswa-card {
    background: #fff;
    border: 1px solid #e7eaec;
    border-radius: 8px;
    margin-bottom: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.siswa-card:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    transform: translateY(-2px);
}

.siswa-card .card-body {
    padding: 25px;
    text-align: center;
}

.siswa-avatar {
    margin-bottom: 15px;
}

.siswa-name {
    font-weight: 600;
    margin-bottom: 5px;
    color: #2c3e50;
}

.siswa-email {
    font-size: 14px;
    margin-bottom: 20px;
}

.siswa-stats {
    background: #f8f9fa;
    border-radius: 6px;
    padding: 15px;
    margin-bottom: 20px;
}

.stat-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
}

.stat-item:last-child {
    margin-bottom: 0;
}

.stat-label {
    font-size: 14px;
    color: #6c757d;
}

.stat-value {
    font-weight: 600;
    color: #2c3e50;
}

.empty-state {
    padding: 60px 20px;
}

.empty-state i {
    margin-bottom: 20px;
}

.btn-block {
    width: 100%;
}
</style>
@endpush

@push('scripts')
<script src="{{ asset('js/laporan-kemampuan.js') }}"></script>
@endpush
