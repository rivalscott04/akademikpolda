@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="wrapper wrapper-content animated fadeInRight">
            <!-- Back Button -->
            <div class="mb-3">
                <a href="{{ route('admin.riwayat-tes') }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> Kembali ke Daftar User
                </a>
            </div>

            <!-- User Info Header -->
            <div class="ibox">
                <div class="ibox-title">
                    <h5><i class="fa fa-user"></i> Riwayat Tes - {{ $user->name }}</h5>
                    <div class="ibox-tools">
                        <span class="badge badge-info">{{ $user->getPackageDisplayName() }}</span>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="user-profile">
                                <div class="user-avatar-large">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                                <div class="user-details">
                                    <h4 class="user-name">{{ $user->name }}</h4>
                                    <p class="user-email">{{ $user->email }}</p>
                                    <span
                                        class="user-package badge badge-{{ $user->package == 'lengkap' ? 'success' : ($user->package == 'akademik' ? 'info' : ($user->package == 'kecerdasan' ? 'primary' : ($user->package == 'akademik' ? 'warning' : 'secondary'))) }}">
                                        Package: {{ $user->getPackageDisplayName() }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="user-stats">
                                <div class="stat-item">
                                    <div class="stat-number">{{ $userStats['total_tests'] }}</div>
                                    <div class="stat-label">Total Tes</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-number">{{ $userStats['today_tests'] }}</div>
                                    <div class="stat-label">Hari Ini</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-number">{{ $userStats['this_week_tests'] }}</div>
                                    <div class="stat-label">Minggu Ini</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-number">{{ $userStats['this_month_tests'] }}</div>
                                    <div class="stat-label">Bulan Ini</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Overview Cards -->
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6">
                    <div class="ibox">
                        <div class="ibox-content">
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fa fa-clipboard-list text-primary"></i>
                                </div>
                                <div class="stat-content">
                                    <h3 class="stat-number">{{ $hasilTes->total() }}</h3>
                                    <p class="stat-label">Total Hasil Tes</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="ibox">
                        <div class="ibox-content">
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fa fa-calendar-day text-success"></i>
                                </div>
                                <div class="stat-content">
                                    <h3 class="stat-number">{{ $userStats['today_tests'] }}</h3>
                                    <p class="stat-label">Hari Ini</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="ibox">
                        <div class="ibox-content">
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fa fa-calendar-week text-info"></i>
                                </div>
                                <div class="stat-content">
                                    <h3 class="stat-number">{{ $userStats['this_week_tests'] }}</h3>
                                    <p class="stat-label">Minggu Ini</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="ibox">
                        <div class="ibox-content">
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fa fa-calendar-alt text-warning"></i>
                                </div>
                                <div class="stat-content">
                                    <h3 class="stat-number">{{ $userStats['this_month_tests'] }}</h3>
                                    <p class="stat-label">Bulan Ini</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="ibox">
                <div class="ibox-title">
                    <h5><i class="fa fa-history"></i> Riwayat Tes Detail</h5>
                    <div class="ibox-tools">
                        <span class="badge badge-primary">{{ $hasilTes->total() }} Hasil Tes</span>
                    </div>
                </div>
                <div class="ibox-content">
                    <!-- Filter Section -->
                    <div class="filter-section mb-4">
                        <form method="GET" action="{{ route('admin.riwayat-tes-user', $user->id) }}" class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Jenis Tes</label>
                                <select name="jenis_tes" class="form-control">
                                    <option value="">Semua Jenis</option>
                                    <option value="bahasa_inggris"
                                        {{ request('jenis_tes') == 'bahasa_inggris' ? 'selected' : '' }}>Bahasa Inggris</option>
                                    <option value="pu"
                                        {{ request('jenis_tes') == 'pu' ? 'selected' : '' }}>Pengetahuan Umum</option>
                                    <option value="twk"
                                        {{ request('jenis_tes') == 'twk' ? 'selected' : '' }}>TWK</option>
                                    <option value="numerik"
                                        {{ request('jenis_tes') == 'numerik' ? 'selected' : '' }}>Numerik</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Tanggal Mulai</label>
                                <input type="date" name="date_from" class="form-control"
                                    value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Tanggal Akhir</label>
                                <input type="date" name="date_to" class="form-control"
                                    value="{{ request('date_to') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-search"></i> Filter
                                    </button>
                                    <a href="{{ route('admin.riwayat-tes-user', $user->id) }}" class="btn btn-secondary">
                                        <i class="fa fa-refresh"></i> Reset
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Results Section -->
                    @if ($hasilTes->count() > 0)
                        <div class="row">
                            @foreach ($hasilTes as $tes)
                                <div class="col-lg-4 col-md-6 mb-3">
                                    <div class="compact-test-card {{ $tes->kategori_skor ?? 'fair' }}">
                                        <div class="card-header-compact">
                                            <div class="test-type">
                                                <i
                                                    class="fa {{ $tes->jenis_tes == 'bahasa_inggris' ? 'fa-language' : ($tes->jenis_tes == 'pu' ? 'fa-book' : ($tes->jenis_tes == 'twk' ? 'fa-flag' : ($tes->jenis_tes == 'numerik' ? 'fa-calculator' : 'fa-graduation-cap'))) }}"></i>
                                                <span class="type-text">{{ ucfirst(str_replace('_', ' ', $tes->jenis_tes ?? 'Tes')) }}</span>
                                            </div>
                                            <div class="test-date">
                                                {{ \Carbon\Carbon::parse($tes->tanggal_tes)->format('d/m H:i') }}
                                            </div>
                                        </div>

                                        <div class="card-body-compact">
                                            <div class="score-compact">
                                                <div class="score-main">
                                                    @if ($tes->skor_akhir)
                                                        <span
                                                            class="score-number">{{ number_format($tes->skor_akhir, 0) }}</span>
                                                        <span class="score-label">Skor</span>
                                                    @else
                                                        @php
                                                            $total = $tes->skor_benar + $tes->skor_salah;
                                                            $percentage =
                                                                $total > 0
                                                                    ? round(($tes->skor_benar / $total) * 100)
                                                                    : 0;
                                                        @endphp
                                                        <span class="score-number">{{ $percentage }}%</span>
                                                        <span class="score-label">Skor</span>
                                                    @endif
                                                </div>
                                                <div class="score-details-compact">
                                                    <span class="score-item correct">{{ $tes->skor_benar }}✓</span>
                                                    <span class="score-item wrong">{{ $tes->skor_salah }}✗</span>
                                                    @if ($tes->waktu_total)
                                                        <span
                                                            class="score-item duration">{{ round($tes->waktu_total / 60) }}m</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="card-footer-compact">
                                                <div class="status-badge-compact {{ $tes->kategori_skor ?? 'fair' }}">
                                                    @switch($tes->kategori_skor)
                                                        @case('excellent')
                                                            <i class="fa fa-star"></i> Excellent
                                                        @break

                                                        @case('good')
                                                            <i class="fa fa-thumbs-up"></i> Good
                                                        @break

                                                        @case('fair')
                                                            <i class="fa fa-meh-o"></i> Fair
                                                        @break

                                                        @case('poor')
                                                            <i class="fa fa-frown-o"></i> Poor
                                                        @break

                                                        @default
                                                            <i class="fa fa-question"></i> Belum Dinilai
                                                    @endswitch
                                                </div>
                                                <a href="#" class="btn-detail" onclick="showTestDetail({{ $tes->id }}, '{{ $tes->jenis_tes }}')">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            {{ $hasilTes->appends(request()->query())->links('pagination::bootstrap-4') }}
                        </div>
                    @else
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="fa fa-clipboard-list fa-3x"></i>
                            </div>
                            <h4>Tidak Ada Data Tes</h4>
                            <p>Tidak ada hasil tes yang sesuai dengan filter yang dipilih.</p>
                            <a href="{{ route('admin.riwayat-tes-user', $user->id) }}" class="btn btn-primary">
                                <i class="fa fa-refresh"></i> Lihat Semua
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* User Profile Header */
        .user-profile {
            display: flex;
            align-items: center;
        }

        .user-avatar-large {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
            color: white;
            font-weight: bold;
            font-size: 24px;
        }

        .user-details h4 {
            margin: 0 0 5px 0;
            color: #333;
        }

        .user-details p {
            margin: 0 0 10px 0;
            color: #666;
        }

        .user-stats {
            display: flex;
            justify-content: space-around;
            text-align: center;
        }

        .user-stats .stat-item {
            flex: 1;
        }

        .user-stats .stat-number {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }

        .user-stats .stat-label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
        }

        /* Statistics Cards */
        .stat-card {
            display: flex;
            align-items: center;
            padding: 10px 0;
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            background: rgba(0, 0, 0, 0.05);
        }

        .stat-icon i {
            font-size: 24px;
        }

        .stat-content {
            flex: 1;
        }

        .stat-number {
            font-size: 28px;
            font-weight: bold;
            margin: 0;
            color: #333;
        }

        .stat-label {
            margin: 0;
            color: #666;
            font-size: 14px;
        }

        /* Filter Section */
        .filter-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }

        .filter-section .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        /* Compact Test Cards */
        .compact-test-card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            transition: all 0.2s ease;
            overflow: hidden;
            border-left: 3px solid #ddd;
            height: 100%;
        }

        .compact-test-card:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }

        .compact-test-card.excellent {
            border-left-color: #28a745;
        }

        .compact-test-card.good {
            border-left-color: #17a2b8;
        }

        .compact-test-card.fair {
            border-left-color: #ffc107;
        }

        .compact-test-card.poor {
            border-left-color: #dc3545;
        }

        .card-header-compact {
            padding: 12px 15px 8px;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .test-type {
            display: flex;
            align-items: center;
            font-size: 13px;
            font-weight: 600;
            color: #333;
        }

        .test-type i {
            margin-right: 6px;
            font-size: 12px;
            color: #666;
        }

        .test-date {
            font-size: 11px;
            color: #999;
            font-weight: 500;
        }

        .card-body-compact {
            padding: 12px 15px;
        }

        .score-compact {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .score-main {
            text-align: center;
        }

        .score-number {
            display: block;
            font-size: 18px;
            font-weight: bold;
            color: #333;
            line-height: 1;
        }

        .score-label {
            font-size: 9px;
            color: #666;
            text-transform: uppercase;
        }

        .score-details-compact {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .score-item {
            font-size: 11px;
            font-weight: 500;
            padding: 1px 4px;
            border-radius: 3px;
            text-align: center;
            min-width: 35px;
        }

        .score-item.correct {
            color: #28a745;
            background: rgba(40, 167, 69, 0.1);
        }

        .score-item.wrong {
            color: #dc3545;
            background: rgba(220, 53, 69, 0.1);
        }

        .score-item.duration {
            color: #17a2b8;
            background: rgba(23, 162, 184, 0.1);
        }

        .card-footer-compact {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 8px;
            border-top: 1px solid #f0f0f0;
        }

        .status-badge-compact {
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 9px;
            font-weight: 600;
            text-transform: uppercase;
            display: flex;
            align-items: center;
            gap: 3px;
        }

        .status-badge-compact.excellent {
            background: #d4edda;
            color: #155724;
        }

        .status-badge-compact.good {
            background: #d1ecf1;
            color: #0c5460;
        }

        .status-badge-compact.fair {
            background: #fff3cd;
            color: #856404;
        }

        .status-badge-compact.poor {
            background: #f8d7da;
            color: #721c24;
        }

        .btn-detail {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: #007bff;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            font-size: 10px;
            transition: all 0.2s ease;
        }

        .btn-detail:hover {
            background: #0056b3;
            color: white;
            text-decoration: none;
            transform: scale(1.1);
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }

        .empty-icon {
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .empty-state h4 {
            margin-bottom: 10px;
            color: #495057;
        }

        .empty-state p {
            margin-bottom: 30px;
            font-size: 16px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .user-profile {
                flex-direction: column;
                text-align: center;
            }

            .user-avatar-large {
                margin-right: 0;
                margin-bottom: 15px;
            }

            .user-stats {
                flex-wrap: wrap;
                gap: 15px;
            }

            .score-compact {
                flex-direction: column;
                gap: 8px;
            }

            .score-details-compact {
                flex-direction: row;
                justify-content: center;
                gap: 8px;
            }

            .filter-section .row {
                margin: 0;
            }

            .filter-section .col-md-3 {
                margin-bottom: 15px;
            }
        }

        @media (max-width: 576px) {
            .stat-card {
                flex-direction: column;
                text-align: center;
            }

            .stat-icon {
                margin-right: 0;
                margin-bottom: 10px;
            }

            .compact-test-card {
                margin-bottom: 12px;
            }

            .card-header-compact {
                padding: 10px 12px 6px;
            }

            .card-body-compact {
                padding: 10px 12px;
            }

            .score-number {
                font-size: 16px;
            }
        }
        
        /* Modal Fix Styles */
        #testDetailModal {
            z-index: 1050;
        }
        
        #testDetailModal.show {
            display: block !important;
        }
        
        .modal-backdrop {
            z-index: 1040;
        }
        
        .modal-open {
            overflow: hidden;
        }
        
        /* Ensure modal is properly centered */
        .modal-dialog {
            margin: 1.75rem auto;
        }
        
        /* Fix for close button */
        .modal-header .close {
            padding: 1rem 1rem;
            margin: -1rem -1rem -1rem auto;
            font-size: 1.5rem;
            font-weight: 700;
            line-height: 1;
            color: #000;
            text-shadow: 0 1px 0 #fff;
            opacity: .5;
            cursor: pointer;
        }
        
        .modal-header .close:hover {
            opacity: .75;
        }
        
        /* Enhanced Modal Styles */
        .test-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding: 15px;
            background: #1ab394;
            border-radius: 8px;
            color: white;
        }
        
        .test-id-badge {
            font-weight: 600;
            font-size: 14px;
        }
        
        .test-status {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            background: rgba(255,255,255,0.2);
        }
        
        .test-status.completed {
            background: #1ab394;
        }
        
        .test-status.incomplete {
            background: #f8ac59;
        }
        
        /* Question Breakdown */
        .question-breakdown {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        .breakdown-item {
            display: flex;
            align-items: center;
            padding: 10px;
            border-radius: 8px;
            background: #f8f9fa;
            border-left: 4px solid #ddd;
        }
        
        .breakdown-item.total {
            grid-column: 1 / -1;
            background: #1ab394;
            color: white;
            border-left-color: #1ab394;
        }
        
        .breakdown-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            font-size: 16px;
        }
        
        .breakdown-icon.correct {
            background: #1ab394;
            color: white;
        }
        
        .breakdown-icon.wrong {
            background: #ed5565;
            color: white;
        }
        
        .breakdown-icon.unanswered {
            background: #f8ac59;
            color: white;
        }
        
        .breakdown-icon.total {
            background: rgba(255,255,255,0.2);
            color: white;
        }
        
        .breakdown-content {
            flex: 1;
        }
        
        .breakdown-number {
            display: block;
            font-size: 18px;
            font-weight: bold;
            line-height: 1;
        }
        
        .breakdown-label {
            font-size: 12px;
            opacity: 0.8;
        }
        
        /* Enhanced Score Display */
        .score-circle.excellent {
            background: #1ab394;
        }
        
        .score-circle.good {
            background: #23c6c8;
        }
        
        .score-circle.fair {
            background: #f8ac59;
        }
        
        .score-circle.poor {
            background: #ed5565;
        }
        
        .score-details {
            flex: 1;
        }
        
        .score-item.time {
            color: #23c6c8;
        }
        
        /* Performance Chart */
        .chart-container {
            text-align: center;
        }
        
        .progress-ring {
            position: relative;
            display: inline-block;
            margin-bottom: 15px;
        }
        
        .chart-center {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
        
        .chart-score {
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }
        
        .chart-legend {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        
        .legend-item {
            display: flex;
            align-items: center;
            font-size: 12px;
        }
        
        .legend-color {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 8px;
        }
        
        .legend-color.correct {
            background: #1ab394;
        }
        
        .legend-color.wrong {
            background: #ed5565;
        }
        
        .legend-color.unanswered {
            background: #f8ac59;
        }
        
        /* Benchmarking */
        .benchmark-info {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        .benchmark-item {
            text-align: center;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #1ab394;
        }
        
        .benchmark-label {
            font-size: 12px;
            color: #666;
            margin-bottom: 5px;
        }
        
        .benchmark-value {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .benchmark-value.percentile-excellent {
            color: #1ab394;
        }
        
        .benchmark-value.percentile-good {
            color: #23c6c8;
        }
        
        .benchmark-value.percentile-fair {
            color: #f8ac59;
        }
        
        .benchmark-value.percentile-poor {
            color: #ed5565;
        }
        
        .benchmark-desc {
            font-size: 11px;
            color: #666;
        }
        
        /* Recommendations */
        .recommendations {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        }
        
        .recommendations-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        
        .recommendation-item {
            display: flex;
            align-items: center;
            padding: 10px 15px;
            background: white;
            border-radius: 6px;
            border-left: 4px solid #1ab394;
            font-size: 14px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .recommendation-item.excellent {
            border-left-color: #1ab394;
            background: #f0f9f7;
        }
        
        .recommendation-item.good {
            border-left-color: #23c6c8;
            background: #f0f9f9;
        }
        
        .recommendation-item.poor {
            border-left-color: #ed5565;
            background: #fef7f7;
        }
        
        .recommendation-item i {
            margin-right: 10px;
            width: 16px;
        }
        
        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
        }
        
        .action-buttons .btn {
            min-width: 120px;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .test-header {
                flex-direction: column;
                gap: 10px;
                text-align: center;
            }
            
            .question-breakdown {
                grid-template-columns: 1fr;
            }
            
            .breakdown-item.total {
                grid-column: 1;
            }
            
            .score-display {
                flex-direction: column;
                gap: 15px;
            }
            
            .benchmark-info {
                gap: 10px;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .action-buttons .btn {
                width: 100%;
            }
        }
    </style>
@endpush

@push('scripts')
<script>
// Admin Riwayat Tes Filter - Custom JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Initialize filter functionality
    initAdminTestFilter();
    
    // Initialize test detail modal
    initTestDetailModal();
});

function initAdminTestFilter() {
    const filterForm = document.querySelector('.filter-section form');
    const filterButton = filterForm.querySelector('button[type="submit"]');
    const resetButton = filterForm.querySelector('a[href*="riwayat-tes-user"]');
    
    // Add loading state to filter button
    filterButton.addEventListener('click', function(e) {
        const originalText = this.innerHTML;
        this.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Memproses...';
        this.disabled = true;
        
        // Re-enable after 2 seconds (fallback)
        setTimeout(() => {
            this.innerHTML = originalText;
            this.disabled = false;
        }, 2000);
    });
    
    // Auto-submit on date change
    const dateInputs = filterForm.querySelectorAll('input[type="date"]');
    dateInputs.forEach(input => {
        input.addEventListener('change', function() {
            // Auto-submit form when date changes
            setTimeout(() => {
                filterForm.submit();
            }, 500);
        });
    });
    
    // Auto-submit on jenis tes change
    const jenisTesSelect = filterForm.querySelector('select[name="jenis_tes"]');
    if (jenisTesSelect) {
        jenisTesSelect.addEventListener('change', function() {
            setTimeout(() => {
                filterForm.submit();
            }, 300);
        });
    }
}

function initTestDetailModal() {
    // Create modal if it doesn't exist
    if (!document.getElementById('testDetailModal')) {
        const modalHTML = `
            <div class="modal fade" id="testDetailModal" tabindex="-1" role="dialog" aria-labelledby="testDetailModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="testDetailModalLabel">
                                <i class="fa fa-eye"></i> Detail Hasil Tes
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="closeTestModal()">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body" id="testDetailContent">
                            <div class="text-center">
                                <i class="fa fa-spinner fa-spin fa-2x"></i>
                                <p>Memuat detail tes...</p>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="closeTestModal()">
                                <i class="fa fa-times"></i> Tutup
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', modalHTML);
        
        // Add event listeners for modal close
        const modal = document.getElementById('testDetailModal');
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeTestModal();
            }
        });
        
        // Add ESC key listener
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && modal.classList.contains('show')) {
                closeTestModal();
            }
        });
    }
}

function showTestDetail(testId, testType) {
    const modal = document.getElementById('testDetailModal');
    const content = document.getElementById('testDetailContent');
    
    // Reset content
    content.innerHTML = `
        <div class="text-center">
            <i class="fa fa-spinner fa-spin fa-2x"></i>
            <p>Memuat detail tes...</p>
        </div>
    `;
    
    // Show modal using multiple methods to ensure compatibility
    modal.style.display = 'block';
    modal.classList.add('show');
    modal.setAttribute('aria-hidden', 'false');
    document.body.classList.add('modal-open');
    
    // Add backdrop
    const backdrop = document.createElement('div');
    backdrop.className = 'modal-backdrop fade show';
    backdrop.id = 'testModalBackdrop';
    document.body.appendChild(backdrop);
    
    // Fetch test details
    fetch(`/admin/test-detail/${testId}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            content.innerHTML = generateTestDetailHTML(data.test, testType);
        } else {
            content.innerHTML = `
                <div class="alert alert-warning">
                    <i class="fa fa-exclamation-triangle"></i>
                    ${data.message || 'Gagal memuat detail tes'}
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        content.innerHTML = `
            <div class="alert alert-danger">
                <i class="fa fa-exclamation-circle"></i>
                Terjadi kesalahan saat memuat detail tes. Silakan coba lagi.
            </div>
        `;
    });
}

function generateTestDetailHTML(test, testType) {
    const testDate = new Date(test.tanggal_tes).toLocaleDateString('id-ID', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
    
    const testStartTime = new Date(test.tanggal_tes).toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    });
    
    const testEndTime = test.waktu_total ? 
        new Date(new Date(test.tanggal_tes).getTime() + (test.waktu_total * 1000)).toLocaleTimeString('id-ID', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        }) : 'Tidak tersedia';
    
    const scorePercentage = test.skor_benar + test.skor_salah > 0 
        ? Math.round((test.skor_benar / (test.skor_benar + test.skor_salah)) * 100)
        : 0;
    
    const totalQuestions = test.skor_benar + test.skor_salah;
    const unansweredQuestions = (test.total_soal || totalQuestions) - totalQuestions;
    
    // Calculate percentile (mock calculation - in real app, this would come from database)
    const percentile = calculatePercentile(scorePercentage);
    
    // Generate performance chart data
    const chartData = generatePerformanceChart(scorePercentage, test.skor_benar, test.skor_salah, unansweredQuestions);
    
    // Generate recommendations
    const recommendations = generateRecommendations(scorePercentage, test.kategori_skor, testType);
    
    return `
        <div class="test-detail-content">
            <!-- Header with Test ID and Status -->
            <div class="test-header">
                <div class="test-id-badge">
                    <i class="fa fa-hashtag"></i> Test ID: ${test.id}
                </div>
                <div class="test-status ${test.status || 'completed'}">
                    <i class="fa fa-${getStatusIcon(test.status)}"></i> ${getStatusText(test.status)}
                </div>
            </div>

            <div class="row">
                <!-- Left Column -->
                <div class="col-md-6">
                    <!-- Test Information -->
                    <div class="detail-section">
                        <h6><i class="fa fa-info-circle"></i> Informasi Tes</h6>
                        <table class="table table-sm">
                            <tr>
                                <td><strong>Jenis Tes:</strong></td>
                                <td>
                                    <span class="badge badge-primary">
                                        ${testType.charAt(0).toUpperCase() + testType.slice(1)}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Tanggal Tes:</strong></td>
                                <td>${testDate}</td>
                            </tr>
                            <tr>
                                <td><strong>Waktu Mulai:</strong></td>
                                <td>${testStartTime}</td>
                            </tr>
                            <tr>
                                <td><strong>Waktu Selesai:</strong></td>
                                <td>${testEndTime}</td>
                            </tr>
                            <tr>
                                <td><strong>Durasi Total:</strong></td>
                                <td>${test.waktu_total ? Math.round(test.waktu_total / 60) + ' menit' : 'Tidak tersedia'}</td>
                            </tr>
                            <tr>
                                <td><strong>Attempt:</strong></td>
                                <td>${test.attempt_number || 1}</td>
                            </tr>
                        </table>
                    </div>

                    <!-- Question Breakdown -->
                    <div class="detail-section">
                        <h6><i class="fa fa-list-ol"></i> Breakdown Soal</h6>
                        <div class="question-breakdown">
                            <div class="breakdown-item">
                                <div class="breakdown-icon correct">
                                    <i class="fa fa-check"></i>
                                </div>
                                <div class="breakdown-content">
                                    <span class="breakdown-number">${test.skor_benar}</span>
                                    <span class="breakdown-label">Benar</span>
                                </div>
                            </div>
                            <div class="breakdown-item">
                                <div class="breakdown-icon wrong">
                                    <i class="fa fa-times"></i>
                                </div>
                                <div class="breakdown-content">
                                    <span class="breakdown-number">${test.skor_salah}</span>
                                    <span class="breakdown-label">Salah</span>
                                </div>
                            </div>
                            <div class="breakdown-item">
                                <div class="breakdown-icon unanswered">
                                    <i class="fa fa-question"></i>
                                </div>
                                <div class="breakdown-content">
                                    <span class="breakdown-number">${unansweredQuestions}</span>
                                    <span class="breakdown-label">Tidak Dijawab</span>
                                </div>
                            </div>
                            <div class="breakdown-item total">
                                <div class="breakdown-icon total">
                                    <i class="fa fa-list"></i>
                                </div>
                                <div class="breakdown-content">
                                    <span class="breakdown-number">${test.total_soal || totalQuestions}</span>
                                    <span class="breakdown-label">Total Soal</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="col-md-6">
                    <!-- Performance Score -->
                    <div class="detail-section">
                        <h6><i class="fa fa-chart-bar"></i> Hasil Tes</h6>
                        <div class="score-display">
                            <div class="score-circle ${getScoreClass(scorePercentage)}">
                                <span class="score-number">${scorePercentage}%</span>
                                <span class="score-label">Skor</span>
                            </div>
                            <div class="score-details">
                                <div class="score-item correct">
                                    <i class="fa fa-check"></i>
                                    <span>${test.skor_benar} Benar</span>
                                </div>
                                <div class="score-item wrong">
                                    <i class="fa fa-times"></i>
                                    <span>${test.skor_salah} Salah</span>
                                </div>
                                <div class="score-item time">
                                    <i class="fa fa-clock-o"></i>
                                    <span>${test.waktu_total ? Math.round(test.waktu_total / 60) + 'm' : 'N/A'}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Performance Chart -->
                    <div class="detail-section">
                        <h6><i class="fa fa-pie-chart"></i> Visualisasi Performa</h6>
                        <div class="performance-chart">
                            ${chartData}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Performance Analysis Row -->
            <div class="row">
                <div class="col-md-6">
                    <!-- Benchmarking -->
                    <div class="detail-section">
                        <h6><i class="fa fa-trophy"></i> Benchmarking</h6>
                        <div class="benchmark-info">
                            <div class="benchmark-item">
                                <div class="benchmark-label">Percentile</div>
                                <div class="benchmark-value percentile-${getPercentileClass(percentile)}">
                                    ${percentile}%
                                </div>
                                <div class="benchmark-desc">Lebih baik dari ${percentile}% user</div>
                            </div>
                            <div class="benchmark-item">
                                <div class="benchmark-label">Rata-rata User</div>
                                <div class="benchmark-value">75%</div>
                                <div class="benchmark-desc">${scorePercentage >= 75 ? 'Di atas rata-rata' : 'Di bawah rata-rata'}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <!-- Final Score & Category -->
                    <div class="detail-section">
                        <h6><i class="fa fa-star"></i> Hasil Akhir</h6>
                        ${test.skor_akhir ? `
                            <div class="final-score">
                                <span class="final-score-number">${test.skor_akhir}</span>
                                <span class="final-score-label">Skor Akhir</span>
                            </div>
                        ` : ''}
                        ${test.kategori_skor ? `
                            <div class="category-badge ${test.kategori_skor}">
                                ${getCategoryDisplay(test.kategori_skor)}
                            </div>
                        ` : ''}
                    </div>
                </div>
            </div>

            <!-- Recommendations -->
            <div class="detail-section recommendations">
                <h6><i class="fa fa-lightbulb-o"></i> Rekomendasi</h6>
                <div class="recommendations-list">
                    ${recommendations}
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="action-buttons">
                <button class="btn btn-primary" onclick="downloadTestPDF(${test.id})">
                    <i class="fa fa-download"></i> Download PDF
                </button>
                <button class="btn btn-info" onclick="printTestResult()">
                    <i class="fa fa-print"></i> Print
                </button>
                <button class="btn btn-success" onclick="shareTestResult(${test.id})">
                    <i class="fa fa-share"></i> Share
                </button>
            </div>
        </div>
        
        <style>
            .test-detail-content .detail-section {
                margin-bottom: 20px;
                padding: 15px;
                background: #f8f9fa;
                border-radius: 8px;
                border-left: 4px solid #007bff;
            }
            
            .test-detail-content .detail-section h6 {
                margin-bottom: 15px;
                color: #333;
                font-weight: 600;
            }
            
            .score-display {
                display: flex;
                align-items: center;
                gap: 20px;
            }
            
            .score-circle {
                width: 80px;
                height: 80px;
                border-radius: 50%;
                background: linear-gradient(135deg, #007bff, #0056b3);
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                color: white;
                text-align: center;
            }
            
            .score-number {
                font-size: 20px;
                font-weight: bold;
                line-height: 1;
            }
            
            .score-label {
                font-size: 10px;
                opacity: 0.9;
            }
            
            .score-breakdown {
                flex: 1;
            }
            
            .score-item {
                display: flex;
                align-items: center;
                margin-bottom: 8px;
                font-size: 14px;
            }
            
            .score-item.correct {
                color: #28a745;
            }
            
            .score-item.wrong {
                color: #dc3545;
            }
            
            .score-item i {
                margin-right: 8px;
                width: 16px;
            }
            
            .final-score {
                text-align: center;
                padding: 20px;
                background: #1ab394;
                border-radius: 8px;
                color: white;
            }
            
            .final-score-number {
                display: block;
                font-size: 32px;
                font-weight: bold;
                line-height: 1;
            }
            
            .final-score-label {
                font-size: 14px;
                opacity: 0.9;
            }
            
            .category-badge {
                display: inline-block;
                padding: 8px 16px;
                border-radius: 20px;
                font-weight: 600;
                text-transform: uppercase;
                font-size: 12px;
            }
            
            .category-badge.excellent {
                background: #1ab394;
                color: white;
            }
            
            .category-badge.good {
                background: #23c6c8;
                color: white;
            }
            
            .category-badge.fair {
                background: #f8ac59;
                color: white;
            }
            
            .category-badge.poor {
                background: #ed5565;
                color: white;
            }
        </style>
    `;
}

function getCategoryDisplay(category) {
    const categories = {
        'excellent': '<i class="fa fa-star"></i> Excellent',
        'good': '<i class="fa fa-thumbs-up"></i> Good',
        'fair': '<i class="fa fa-meh-o"></i> Fair',
        'poor': '<i class="fa fa-frown-o"></i> Poor'
    };
    return categories[category] || '<i class="fa fa-question"></i> Belum Dinilai';
}

// Helper functions for enhanced modal
function calculatePercentile(score) {
    // Mock calculation - in real app, this would query database
    if (score >= 90) return 95;
    if (score >= 80) return 85;
    if (score >= 70) return 70;
    if (score >= 60) return 50;
    if (score >= 50) return 30;
    return 15;
}

function getScoreClass(score) {
    if (score >= 80) return 'excellent';
    if (score >= 60) return 'good';
    if (score >= 40) return 'fair';
    return 'poor';
}

function getPercentileClass(percentile) {
    if (percentile >= 80) return 'excellent';
    if (percentile >= 60) return 'good';
    if (percentile >= 40) return 'fair';
    return 'poor';
}

function getStatusIcon(status) {
    const icons = {
        'completed': 'check-circle',
        'incomplete': 'clock-o',
        'cancelled': 'times-circle',
        'pending': 'hourglass-half'
    };
    return icons[status] || 'check-circle';
}

function getStatusText(status) {
    const texts = {
        'completed': 'Selesai',
        'incomplete': 'Belum Selesai',
        'cancelled': 'Dibatalkan',
        'pending': 'Menunggu'
    };
    return texts[status] || 'Selesai';
}

function generatePerformanceChart(score, correct, wrong, unanswered) {
    const total = correct + wrong + unanswered;
    const correctPercent = total > 0 ? Math.round((correct / total) * 100) : 0;
    const wrongPercent = total > 0 ? Math.round((wrong / total) * 100) : 0;
    const unansweredPercent = total > 0 ? Math.round((unanswered / total) * 100) : 0;
    
    // Calculate circumference for the circle (2 * π * radius)
    const circumference = 2 * Math.PI * 50; // radius = 50
    
    // Calculate stroke-dasharray based on percentage
    const correctDashArray = (correctPercent / 100) * circumference;
    const wrongDashArray = (wrongPercent / 100) * circumference;
    const unansweredDashArray = (unansweredPercent / 100) * circumference;
    
    // Calculate stroke-dashoffset for positioning
    const wrongDashOffset = -correctDashArray;
    const unansweredDashOffset = -(correctDashArray + wrongDashArray);
    
    return `
        <div class="chart-container">
            <div class="progress-ring">
                <svg width="120" height="120">
                    <circle cx="60" cy="60" r="50" fill="none" stroke="#e9ecef" stroke-width="8"/>
                    ${correctPercent > 0 ? `
                    <circle cx="60" cy="60" r="50" fill="none" stroke="#1ab394" stroke-width="8" 
                            stroke-dasharray="${correctDashArray} ${circumference}" stroke-dashoffset="0" 
                            transform="rotate(-90 60 60)" class="progress-correct"/>
                    ` : ''}
                    ${wrongPercent > 0 ? `
                    <circle cx="60" cy="60" r="50" fill="none" stroke="#ed5565" stroke-width="8" 
                            stroke-dasharray="${wrongDashArray} ${circumference}" stroke-dashoffset="${wrongDashOffset}" 
                            transform="rotate(-90 60 60)" class="progress-wrong"/>
                    ` : ''}
                    ${unansweredPercent > 0 ? `
                    <circle cx="60" cy="60" r="50" fill="none" stroke="#f8ac59" stroke-width="8" 
                            stroke-dasharray="${unansweredDashArray} ${circumference}" stroke-dashoffset="${unansweredDashOffset}" 
                            transform="rotate(-90 60 60)" class="progress-unanswered"/>
                    ` : ''}
                </svg>
                <div class="chart-center">
                    <span class="chart-score">${score}%</span>
                </div>
            </div>
            <div class="chart-legend">
                ${correctPercent > 0 ? `
                <div class="legend-item">
                    <span class="legend-color correct"></span>
                    <span>Benar (${correctPercent}%)</span>
                </div>
                ` : ''}
                ${wrongPercent > 0 ? `
                <div class="legend-item">
                    <span class="legend-color wrong"></span>
                    <span>Salah (${wrongPercent}%)</span>
                </div>
                ` : ''}
                ${unansweredPercent > 0 ? `
                <div class="legend-item">
                    <span class="legend-color unanswered"></span>
                    <span>Tidak Dijawab (${unansweredPercent}%)</span>
                </div>
                ` : ''}
            </div>
        </div>
    `;
}

function generateRecommendations(score, category, testType) {
    let recommendations = [];
    
    if (score >= 80) {
        recommendations.push('<div class="recommendation-item excellent"><i class="fa fa-star"></i> Performa sangat baik! Pertahankan konsistensi.</div>');
    } else if (score >= 60) {
        recommendations.push('<div class="recommendation-item good"><i class="fa fa-thumbs-up"></i> Hasil cukup baik. Fokus pada area yang masih lemah.</div>');
    } else {
        recommendations.push('<div class="recommendation-item poor"><i class="fa fa-exclamation-triangle"></i> Perlu peningkatan. Disarankan untuk latihan lebih intensif.</div>');
    }
    
    // Test type specific recommendations
    if (testType === 'bahasa_inggris') {
        recommendations.push('<div class="recommendation-item"><i class="fa fa-language"></i> Tingkatkan kemampuan bahasa Inggris dengan latihan rutin.</div>');
    } else if (testType === 'pu') {
        recommendations.push('<div class="recommendation-item"><i class="fa fa-book"></i> Perbanyak membaca untuk meningkatkan pengetahuan umum.</div>');
    } else if (testType === 'twk') {
        recommendations.push('<div class="recommendation-item"><i class="fa fa-flag"></i> Pelajari lebih dalam tentang wawasan kebangsaan dan Pancasila.</div>');
    } else if (testType === 'numerik') {
        recommendations.push('<div class="recommendation-item"><i class="fa fa-calculator"></i> Latih kemampuan numerik dan matematika dasar.</div>');
    }
    
    // General recommendations
    recommendations.push('<div class="recommendation-item"><i class="fa fa-clock-o"></i> Manajemen waktu yang lebih baik dapat meningkatkan hasil.</div>');
    recommendations.push('<div class="recommendation-item"><i class="fa fa-book"></i> Lakukan latihan rutin untuk meningkatkan kemampuan.</div>');
    
    return recommendations.join('');
}

// Action functions
function downloadTestPDF(testId) {
    // Open PDF in new tab
    window.open(`/admin/test-pdf/${testId}`, '_blank');
}

function printTestResult() {
    window.print();
}

function shareTestResult(testId) {
    if (navigator.share) {
        navigator.share({
            title: 'Hasil Tes',
            text: 'Lihat hasil tes saya',
            url: window.location.href
        });
    } else {
        // Fallback - copy to clipboard
        navigator.clipboard.writeText(window.location.href).then(() => {
            alert('Link hasil tes telah disalin ke clipboard');
        });
    }
}

function closeTestModal() {
    const modal = document.getElementById('testDetailModal');
    const backdrop = document.getElementById('testModalBackdrop');
    
    if (modal) {
        // Hide modal
        modal.style.display = 'none';
        modal.classList.remove('show');
        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('modal-open');
    }
    
    if (backdrop) {
        backdrop.remove();
    }
}

// Export functions for global access
window.showTestDetail = showTestDetail;
window.initAdminTestFilter = initAdminTestFilter;
window.closeTestModal = closeTestModal;
</script>
@endpush
