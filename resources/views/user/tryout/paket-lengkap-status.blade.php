@extends('layouts.app')

@section('title', 'Status Paket Lengkap')

@section('content')
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-title">
                    <h5><i class="fa fa-trophy"></i> Status Paket Lengkap</h5>
                </div>
                <div class="ibox-content">
                    @if($status['is_complete'])
                        <!-- Paket Lengkap Selesai -->
                        <div class="alert {{ $status['scoring_info']['passed'] ? 'alert-success' : 'alert-warning' }}">
                            <h4><i class="fa {{ $status['scoring_info']['passed'] ? 'fa-check-circle' : 'fa-exclamation-triangle' }}"></i> 
                                {{ $status['scoring_info']['passed'] ? 'Selamat! Paket Lengkap Anda Sudah Selesai' : 'Paket Lengkap Anda Sudah Selesai' }}
                            </h4>
                            <p>Anda telah menyelesaikan semua komponen paket lengkap dengan skor akhir:</p>
                            <h2 class="text-center {{ $status['scoring_info']['passed'] ? 'text-success' : 'text-warning' }}">{{ $status['scoring_info']['final_score'] }}</h2>
                            
                            <!-- Status Kelulusan -->
                            <div class="text-center mt-3">
                                <span class="label label-{{ $status['scoring_info']['passed'] ? 'success' : 'danger' }} label-lg">
                                    {{ $status['scoring_info']['passed'] ? 'LULUS' : 'TIDAK LULUS' }}
                                </span>
                                <p class="mt-2 text-muted">
                                    <small>Standar kelulusan: {{ $status['scoring_info']['passing_grade'] }}</small>
                                </p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="panel panel-success">
                                    <div class="panel-heading">
                                        <h4><i class="fa fa-eye"></i> Tes Kecermatan</h4>
                                    </div>
                                    <div class="panel-body text-center">
                                        <h3 class="text-success">{{ $status['kecermatan']['score'] }}</h3>
                                        <p><small>Tanggal: {{ \Carbon\Carbon::parse($status['kecermatan']['tanggal'])->format('d/m/Y H:i') }}</small></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="panel panel-success">
                                    <div class="panel-heading">
                                        <h4><i class="fa fa-brain"></i> Tryout Kecerdasan</h4>
                                    </div>
                                    <div class="panel-body text-center">
                                        <h3 class="text-success">{{ $status['kecerdasan']['score'] }}</h3>
                                        @if(isset($status['kecerdasan']['tryout_title']))
                                            <p><small>{{ $status['kecerdasan']['tryout_title'] }}</small></p>
                                        @endif
                                        <p><small>Tanggal: {{ \Carbon\Carbon::parse($status['kecerdasan']['tanggal'])->format('d/m/Y H:i') }}</small></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="panel panel-success">
                                    <div class="panel-heading">
                                        <h4><i class="fa fa-user"></i> Tryout Kepribadian</h4>
                                    </div>
                                    <div class="panel-body text-center">
                                        <h3 class="text-success">{{ $status['kepribadian']['score'] }}</h3>
                                        @if(isset($status['kepribadian']['tryout_title']))
                                            <p><small>{{ $status['kepribadian']['tryout_title'] }}</small></p>
                                        @endif
                                        <p><small>Tanggal: {{ \Carbon\Carbon::parse($status['kepribadian']['tanggal'])->format('d/m/Y H:i') }}</small></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Paket Lengkap Belum Selesai -->
                        <div class="alert alert-info">
                            <h4><i class="fa fa-info-circle"></i> Progress Paket Lengkap</h4>
                            <p>Anda perlu menyelesaikan semua komponen untuk mendapatkan skor akhir paket lengkap.</p>
                        </div>

                        <div class="progress">
                            <div class="progress-bar progress-bar-striped active" role="progressbar" 
                                 style="width: {{ $summary['progress'] }}%">
                                {{ $summary['progress'] }}%
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-4">
                                <div class="panel {{ $status['kecermatan']['completed'] ? 'panel-success' : 'panel-default' }}">
                                    <div class="panel-heading">
                                        <h4>
                                            <i class="fa {{ $status['kecermatan']['completed'] ? 'fa-check-circle' : 'fa-circle-o' }}"></i> 
                                            Tes Kecermatan
                                        </h4>
                                    </div>
                                    <div class="panel-body text-center">
                                        @if($status['kecermatan']['completed'])
                                            <h3 class="text-success">{{ $status['kecermatan']['score'] }}</h3>
                                            <p><small>Tanggal: {{ \Carbon\Carbon::parse($status['kecermatan']['tanggal'])->format('d/m/Y H:i') }}</small></p>
                                        @else
                                            <p class="text-muted">{{ $status['kecermatan']['message'] }}</p>
                                            <a href="{{ route('kecermatan.index') }}" class="btn btn-primary btn-sm">
                                                <i class="fa fa-play"></i> Mulai Tes
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="panel {{ $status['kecerdasan']['completed'] ? 'panel-success' : 'panel-default' }}">
                                    <div class="panel-heading">
                                        <h4>
                                            <i class="fa {{ $status['kecerdasan']['completed'] ? 'fa-check-circle' : 'fa-circle-o' }}"></i> 
                                            Tryout Kecerdasan
                                        </h4>
                                    </div>
                                    <div class="panel-body text-center">
                                        @if($status['kecerdasan']['completed'])
                                            <h3 class="text-success">{{ $status['kecerdasan']['score'] }}</h3>
                                            @if(isset($status['kecerdasan']['tryout_title']))
                                                <p><small>{{ $status['kecerdasan']['tryout_title'] }}</small></p>
                                            @endif
                                            <p><small>Tanggal: {{ \Carbon\Carbon::parse($status['kecerdasan']['tanggal'])->format('d/m/Y H:i') }}</small></p>
                                        @else
                                            <p class="text-muted">{{ $status['kecerdasan']['message'] }}</p>
                                            <a href="{{ route('user.tryout.index') }}" class="btn btn-primary btn-sm">
                                                <i class="fa fa-play"></i> Mulai Tryout
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="panel {{ $status['kepribadian']['completed'] ? 'panel-success' : 'panel-default' }}">
                                    <div class="panel-heading">
                                        <h4>
                                            <i class="fa {{ $status['kepribadian']['completed'] ? 'fa-check-circle' : 'fa-circle-o' }}"></i> 
                                            Tryout Kepribadian
                                        </h4>
                                    </div>
                                    <div class="panel-body text-center">
                                        @if($status['kepribadian']['completed'])
                                            <h3 class="text-success">{{ $status['kepribadian']['score'] }}</h3>
                                            @if(isset($status['kepribadian']['tryout_title']))
                                                <p><small>{{ $status['kepribadian']['tryout_title'] }}</small></p>
                                            @endif
                                            <p><small>Tanggal: {{ \Carbon\Carbon::parse($status['kepribadian']['tanggal'])->format('d/m/Y H:i') }}</small></p>
                                        @else
                                            <p class="text-muted">{{ $status['kepribadian']['message'] }}</p>
                                            <a href="{{ route('user.tryout.index') }}" class="btn btn-primary btn-sm">
                                                <i class="fa fa-play"></i> Mulai Tryout
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="panel panel-info">
                                <div class="panel-heading">
                                    <h4><i class="fa fa-info-circle"></i> Informasi Paket Lengkap</h4>
                                </div>
                                <div class="panel-body">
                                    <p><strong>Cara Kerja:</strong></p>
                                    <ul>
                                        <li>Paket lengkap terdiri dari 2 komponen utama: Tes Kecermatan (wajib) dan Tryout CBT</li>
                                        <li>Tryout CBT bisa berisi kategori Kecerdasan, Kepribadian, atau keduanya (campuran)</li>
                                        <li>Skor akhir dihitung dari rata-rata komponen yang tersedia: (Skor Kecermatan + Skor Kecerdasan + Skor Kepribadian) ÷ jumlah komponen</li>
                                        <li>Kecermatan wajib diselesaikan + minimal 1 tryout CBT untuk mendapatkan skor akhir</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 text-center">
                            <a href="{{ route('user.tryout.index') }}" class="btn btn-default">
                                <i class="fa fa-arrow-left"></i> Kembali ke Daftar Tryout
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
