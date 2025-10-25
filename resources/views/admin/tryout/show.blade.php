@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <!-- Header Card -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">{{ $tryout->judul }}</h4>
                        <div>
                            <a href="{{ route('admin.tryout.edit', $tryout) }}" class="btn btn-warning btn-sm">
                                <i class="fa fa-edit"></i> Edit
                            </a>
                            <a href="{{ route('admin.tryout.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fa fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h5>Informasi Umum</h5>
                                <table class="table table-borderless">
                                    <tr>
                                        <td width="150"><strong>Judul:</strong></td>
                                        <td>{{ $tryout->judul }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Deskripsi:</strong></td>
                                        <td>{{ $tryout->deskripsi ?: '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Durasi:</strong></td>
                                        <td>{{ $tryout->durasi_menit }} menit</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Akses Paket:</strong></td>
                                        <td>
                                            <span
                                                class="badge badge-pill
                                                @if ($tryout->akses_paket == 'free') badge-success
                                                @elseif($tryout->akses_paket == 'premium') badge-warning
                                                @else badge-danger @endif">
                                                {{ strtoupper($tryout->akses_paket) }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Total Soal:</strong></td>
                                        <td>{{ $tryout->total_soal }} soal</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-4">
                                <h5>Status</h5>
                                <div class="alert alert-{{ $tryout->is_active ? 'success' : 'danger' }}">
                                    <h6 class="mb-1">
                                        <i class="fa fa-{{ $tryout->is_active ? 'check-circle' : 'times-circle' }}"></i>
                                        {{ $tryout->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                    </h6>
                                    <small>
                                        {{ $tryout->is_active ? 'Tryout dapat diakses oleh user' : 'Tryout tidak dapat diakses oleh user' }}
                                    </small>
                                </div>

                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6>Informasi Waktu</h6>
                                        <small class="text-muted">
                                            <strong>Dibuat:</strong><br>
                                            {{ $tryout->created_at->format('d/m/Y H:i:s') }}<br>
                                            <strong>Terakhir Diubah:</strong><br>
                                            {{ $tryout->updated_at->format('d/m/Y H:i:s') }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Struktur Soal Card -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5><i class="fa fa-list"></i> Struktur Soal</h5>
                    </div>
                    <div class="card-body">
                        @if (count($strukturSoal) > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Kategori</th>
                                            <th>Kode</th>
                                            <th class="text-center">Jumlah Soal</th>
                                            <th class="text-center">Soal Tersedia</th>
                                            <th class="text-center">Persentase</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($strukturSoal as $item)
                                            <tr>
                                                <td>{{ $item['kategori'] ? $item['kategori']->nama : 'Kategori tidak ditemukan' }}
                                                </td>
                                                <td>
                                                    <span class="badge badge-secondary">
                                                        {{ $item['kategori'] ? $item['kategori']->kode : '-' }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge badge-primary">{{ $item['jumlah'] }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <span
                                                        class="badge badge-{{ $item['soal_tersedia'] >= $item['jumlah'] ? 'success' : 'warning' }}">
                                                        {{ $item['soal_tersedia'] }}
                                                    </span>
                                                </td>
                                                <td class="text-center">{{ $item['persentase'] }}%</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="thead-light">
                                        <tr>
                                            <th colspan="2">Total</th>
                                            <th class="text-center">
                                                <span class="badge badge-success">{{ $totalSoal }}</span>
                                            </th>
                                            <th colspan="2"></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-warning">
                                <i class="fa fa-exclamation-triangle"></i>
                                Struktur soal belum dikonfigurasi.
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Statistik Penggunaan Card -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5><i class="fa fa-chart-bar"></i> Statistik Penggunaan</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body text-center">
                                        <h3>{{ $totalPeserta }}</h3>
                                        <p class="mb-0">Total Peserta</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-info text-white">
                                    <div class="card-body text-center">
                                        <h3>{{ $totalAttempts }}</h3>
                                        <p class="mb-0">Total Percobaan</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body text-center">
                                        <h3>{{ $completedSessions }}</h3>
                                        <p class="mb-0">Selesai</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body text-center">
                                        <h3>{{ $activeSessions }}</h3>
                                        <p class="mb-0">Sedang Aktif</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if ($totalPeserta > 0)
                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <h4 class="text-primary">{{ number_format($averageScore, 2) }}</h4>
                                            <p class="mb-0">Rata-rata Skor</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <h4 class="text-success">{{ $completionRate }}%</h4>
                                            <p class="mb-0">Tingkat Penyelesaian</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Per Level Breakdown (minimal) -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h6>Ringkasan Per Level</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>Level</th>
                                                    <th class="text-center">Jumlah Soal</th>
                                                    <th class="text-center">Skor Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $levelSummary = \App\Models\UserTryoutSoal::where('tryout_id', $tryout->id)
                                                        ->select('level', \DB::raw('COUNT(*) as cnt'), \DB::raw('SUM(skor) as total_skor'))
                                                        ->groupBy('level')
                                                        ->get();
                                                @endphp
                                                @foreach ($levelSummary as $row)
                                                    <tr>
                                                        <td>{{ ucfirst($row->level ?? '-') }}</td>
                                                        <td class="text-center">{{ $row->cnt }}</td>
                                                        <td class="text-center">{{ number_format($row->total_skor ?? 0, 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Peserta Terbaru Card -->
                @if ($totalPeserta > 0)
                    <div class="card mt-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5><i class="fa fa-users"></i> Peserta Terbaru</h5>
                            <a href="#" class="btn btn-sm btn-outline-primary">
                                <i class="fa fa-eye"></i> Lihat Semua
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Nama</th>
                                            <th>Email</th>
                                            <th>Status</th>
                                            <th>Mulai</th>
                                            <th>Selesai</th>
                                            <th>Skor</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($recentParticipants as $participant)
                                            <tr>
                                                <td>{{ $participant['session']->user->name }}</td>
                                                <td>{{ $participant['session']->user->email }}</td>
                                                <td>
                                                    <span
                                                        class="badge badge-{{ $participant['session']->status == 'completed' ? 'success' : ($participant['session']->status == 'active' ? 'warning' : 'secondary') }}">
                                                        {{ ucfirst($participant['session']->status) }}
                                                    </span>
                                                </td>
                                                <td>{{ $participant['session']->started_at ? $participant['session']->started_at->format('d/m/Y H:i') : '-' }}
                                                </td>
                                                <td>{{ $participant['session']->finished_at ? $participant['session']->finished_at->format('d/m/Y H:i') : '-' }}
                                                </td>
                                                <td>
                                                    @if ($participant['session']->status == 'completed')
                                                        <span
                                                            class="badge badge-primary">{{ $participant['score'] }}</span>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Action Buttons -->
                <div class="card mt-4 mb-5">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Aksi Tryout</h6>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.tryout.edit', $tryout) }}" class="btn btn-warning">
                                        <i class="fa fa-edit"></i> Edit Tryout
                                    </a>
                                    @if ($tryout->is_active)
                                        <button type="button" class="btn btn-secondary"
                                            onclick="toggleStatus({{ $tryout->id }}, false)">
                                            <i class="fa fa-pause"></i> Nonaktifkan
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-success"
                                            onclick="toggleStatus({{ $tryout->id }}, true)">
                                            <i class="fa fa-play"></i> Aktifkan
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6>Aksi Data</h6>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-info">
                                        <i class="fa fa-download"></i> Export Data
                                    </button>
                                    @if ($totalPeserta > 0)
                                        <button type="button" class="btn btn-danger"
                                            onclick="resetData({{ $tryout->id }})">
                                            <i class="fa fa-trash"></i> Reset Data
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Toggle Status -->
    <div class="modal fade" id="toggleStatusModal" tabindex="-1" role="dialog"
        aria-labelledby="toggleStatusModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="toggleStatusModalLabel">Konfirmasi Perubahan Status</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p id="toggleStatusMessage"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="confirmToggleStatus">Konfirmasi</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Reset Data -->
    <div class="modal fade" id="resetDataModal" tabindex="-1" role="dialog" aria-labelledby="resetDataModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="resetDataModalLabel">Konfirmasi Reset Data</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="fa fa-exclamation-triangle"></i>
                        <strong>Peringatan!</strong> Tindakan ini akan menghapus semua data peserta dan jawaban untuk tryout
                        ini.
                    </div>
                    <p>Apakah Anda yakin ingin mereset semua data tryout ini?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" id="confirmResetData">Ya, Reset Data</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Pastikan modal bisa ditutup dengan tombol close
            $('.modal .close').on('click', function() {
                $(this).closest('.modal').modal('hide');
            });

            // Pastikan modal bisa ditutup dengan klik di luar modal
            $('.modal').on('click', function(e) {
                if (e.target === this) {
                    $(this).modal('hide');
                }
            });

            // Pastikan modal bisa ditutup dengan tombol Batal
            $('.modal .btn-secondary').on('click', function() {
                $(this).closest('.modal').modal('hide');
            });
        });

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
                        alert('Terjadi kesalahan. Silakan coba lagi.');
                    }
                });
            });
        }

        function resetData(tryoutId) {
            // Hapus event listener sebelumnya untuk menghindari duplikasi
            $('#confirmResetData').off('click');

            $('#resetDataModal').modal('show');

            $('#confirmResetData').on('click', function() {
                const $button = $(this);
                $button.prop('disabled', true).text('Memproses...');

                $.ajax({
                    url: `/admin/tryout/${tryoutId}/reset-data`,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        $('#resetDataModal').modal('hide');
                        // Tambahkan delay kecil sebelum reload
                        setTimeout(function() {
                            location.reload();
                        }, 300);
                    },
                    error: function(xhr) {
                        $button.prop('disabled', false).text('Ya, Reset Data');
                        alert('Terjadi kesalahan. Silakan coba lagi.');
                    }
                });
            });
        }
    </script>
@endpush
