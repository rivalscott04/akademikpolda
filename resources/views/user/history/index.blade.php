@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="wrapper wrapper-content animated fadeInRight">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox">
                        <div class="ibox-title">
                            <h5><i class="fa fa-history"></i> Riwayat Tes</h5>
                            <div class="ibox-tools">
                                <span class="badge badge-primary">{{ $allHistory->count() }} Tes</span>
                            </div>
                        </div>
                        <div class="ibox-content">
                            @if ($allHistory->count() > 0)
                                <div class="row">
                                    @foreach ($allHistory as $history)
                                        <div class="col-lg-6 col-md-6 mb-4">
                                            <a class="history-card-link"
                                                href="{{ $history['type'] === 'tryout' ? route('user.tryout.finish', ['tryout' => $history['tryout_id'] ?? null]) : route('kecermatan.detail', ['id' => $history['id']]) }}">
                                                <div class="history-card {{ $history['status'] }}">
                                                    <div class="card-header">
                                                        <div class="card-title">
                                                            <i
                                                                class="fa {{ $history['type'] == 'tryout' ? 'fa-graduation-cap' : 'fa-eye' }}"></i>
                                                            {{ $history['title'] }}
                                                            @if ($history['type'] === 'kecermatan')
                                                                <span class="type-badge kecermatan">
                                                                    <i class="fa fa-eye"></i>
                                                                    {{ ucfirst($history['type']) }}
                                                                </span>
                                                            @elseif($history['type'] === 'tryout')
                                                                <span class="type-badge tryout">
                                                                    <i class="fa fa-graduation-cap"></i>
                                                                    {{ ucfirst($history['type']) }}
                                                                </span>
                                                            @else
                                                                <span class="type-badge lain">
                                                                    <i class="fa fa-question"></i>
                                                                    {{ ucfirst($history['type']) }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                        <div class="card-date">
                                                            {{ \Carbon\Carbon::parse($history['date'])->format('d M Y, H:i') }}
                                                        </div>
                                                    </div>

                                                    <div class="card-body">
                                                        <div class="score-section">
                                                            <div class="score-circle">
                                                                @if (
                                                                    !empty($history['is_tkp']) &&
                                                                        $history['is_tkp'] &&
                                                                        in_array($history['jenis_paket'] ?? '', ['kepribadian', 'lengkap']))
                                                                    <div class="score-percentage">
                                                                        {{ number_format($history['tkp_final'] ?? 0, 2) }}%
                                                                    </div>
                                                                    <div class="score-label">Skor TKP</div>
                                                                @else
                                                                    <div class="score-percentage">{{ number_format($history['percentage'] ?? 0, 2) }}%
                                                                    </div>
                                                                    <div class="score-label">Skor</div>
                                                                @endif
                                                            </div>

                                                            <div class="score-details">
                                                                @if (
                                                                    !empty($history['is_tkp']) &&
                                                                        $history['is_tkp'] &&
                                                                        in_array($history['jenis_paket'] ?? '', ['kepribadian', 'lengkap']))
                                                                    <div class="score-item total">
                                                                        <i class="fa fa-list"></i>
                                                                        <span>{{ $history['tkp_n'] }} Soal</span>
                                                                    </div>
                                                                    <div class="score-item correct">
                                                                        <i class="fa fa-star"></i>
                                                                        <span>{{ $history['tkp_t'] }} Poin</span>
                                                                    </div>
                                                                @else
                                                                    <div class="score-item correct">
                                                                        <i class="fa fa-check-circle"></i>
                                                                        <span>{{ $history['correct_answers'] }}
                                                                            Benar</span>
                                                                    </div>
                                                                    <div class="score-item wrong">
                                                                        <i class="fa fa-times-circle"></i>
                                                                        <span>{{ $history['wrong_answers'] }} Salah</span>
                                                                    </div>
                                                                    <div class="score-item total">
                                                                        <i class="fa fa-list"></i>
                                                                        <span>{{ $history['total_questions'] }} Soal</span>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        @if (empty($history['is_tkp']) ||
                                                                !$history['is_tkp'] ||
                                                                !in_array($history['jenis_paket'] ?? '', ['kepribadian', 'lengkap']))
                                                            <div class="progress-section">
                                                                <div class="progress">
                                                                    <div class="progress-bar {{ $history['status'] }}"
                                                                        style="width: {{ $history['percentage'] }}%"></div>
                                                                </div>
                                                            </div>
                                                        @endif

                                                        <div class="card-footer">
                                                            <div class="duration">
                                                                <i class="fa fa-clock-o"></i>
                                                                {{ $history['duration'] }} menit
                                                            </div>
                                                            <div class="status-badge {{ $history['status'] }}">
                                                                @switch($history['status'])
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
                                                                @endswitch
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="empty-state">
                                    <div class="empty-icon">
                                        <i class="fa fa-history fa-3x"></i>
                                    </div>
                                    <h4>Belum Ada Riwayat Tes</h4>
                                    <p>Mulai mengerjakan tes untuk melihat riwayat hasil di sini.</p>
                                    <a href="{{ route('show.test') }}" class="btn btn-primary">
                                        <i class="fa fa-play"></i> Mulai Tes
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .history-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            overflow: hidden;
            border-left: 4px solid #ddd;
        }

        .history-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        }

        .history-card.excellent {
            border-left-color: #28a745;
        }

        .history-card.good {
            border-left-color: #17a2b8;
        }

        .history-card.fair {
            border-left-color: #007bff;
        }

        .history-card.poor {
            border-left-color: #dc3545;
        }

        .card-header {
            padding: 20px 20px 10px;
            border-bottom: 1px solid #eee;
        }

        .card-title {
            font-size: 16px;
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .card-title i {
            margin-right: 8px;
            color: #666;
        }

        .type-badge {
            display: inline-block;
            margin-left: 8px;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 600;
            vertical-align: middle;
        }

        .type-badge.tryout {
            background-color: #007bff;
            color: white;
        }

        .type-badge.kecermatan {
            background-color: #28a745;
            color: white;
        }

        .type-badge.lain {
            background-color: #6c757d;
            color: white;
        }

        .card-date {
            font-size: 12px;
            color: #999;
        }

        .card-body {
            padding: 20px;
        }

        .score-section {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .score-circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
            position: relative;
        }

        .history-card.excellent .score-circle {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
        }

        .history-card.good .score-circle {
            background: linear-gradient(135deg, #17a2b8, #6f42c1);
            color: white;
        }

        .history-card.fair .score-circle {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
        }

        .history-card.poor .score-circle {
            background: linear-gradient(135deg, #dc3545, #e83e8c);
            color: white;
        }

        .score-percentage {
            font-size: 20px;
            font-weight: bold;
            line-height: 1;
        }

        .score-label {
            font-size: 10px;
            opacity: 0.9;
        }

        .score-details {
            flex: 1;
        }

        .score-item {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .score-item i {
            width: 16px;
            margin-right: 8px;
        }

        .score-item.correct {
            color: #28a745;
        }

        .score-item.wrong {
            color: #dc3545;
        }

        .score-item.total {
            color: #6c757d;
        }

        .progress-section {
            margin-bottom: 15px;
        }

        .progress {
            height: 8px;
            border-radius: 4px;
            background-color: #e9ecef;
            overflow: hidden;
        }

        .progress-bar {
            height: 100%;
            border-radius: 4px;
            transition: width 0.6s ease;
        }

        .progress-bar.excellent {
            background: linear-gradient(90deg, #28a745, #20c997);
        }

        .progress-bar.good {
            background: linear-gradient(90deg, #17a2b8, #6f42c1);
        }

        .progress-bar.fair {
            background: linear-gradient(90deg, #007bff, #0056b3);
        }

        .progress-bar.poor {
            background: linear-gradient(90deg, #dc3545, #e83e8c);
        }

        .card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }

        .duration {
            font-size: 12px;
            color: #6c757d;
        }

        .duration i {
            margin-right: 5px;
        }

        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-badge.excellent {
            background: #d4edda;
            color: #155724;
        }

        .status-badge.good {
            background: #d1ecf1;
            color: #0c5460;
        }

        .status-badge.fair {
            background: #cce5ff;
            color: #004085;
        }

        .status-badge.poor {
            background: #f8d7da;
            color: #721c24;
        }

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

        @media (max-width: 768px) {
            .score-section {
                flex-direction: column;
                text-align: center;
            }

            .score-circle {
                margin-right: 0;
                margin-bottom: 15px;
            }

            .card-footer {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
@endpush
