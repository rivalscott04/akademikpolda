@extends('layouts.app')

@section('content')
<div class="container">
    <div class="wrapper wrapper-content animated fadeInRight">
        <!-- Statistics Overview Cards -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6">
                <div class="ibox">
                    <div class="ibox-content">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fa fa-users text-primary"></i>
                            </div>
                            <div class="stat-content">
                                <h3 class="stat-number">{{ $totalUsers }}</h3>
                                <p class="stat-label">Total User</p>
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
                                <i class="fa fa-clipboard-list text-success"></i>
                            </div>
                            <div class="stat-content">
                                <h3 class="stat-number">{{ $totalTests }}</h3>
                                <p class="stat-label">Total Tes</p>
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
                                <i class="fa fa-calendar-day text-info"></i>
                            </div>
                            <div class="stat-content">
                                <h3 class="stat-number">{{ $todayTests }}</h3>
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
                                <i class="fa fa-calendar-week text-warning"></i>
                            </div>
                            <div class="stat-content">
                                <h3 class="stat-number">{{ $thisWeekTests }}</h3>
                                <p class="stat-label">Minggu Ini</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="ibox">
            <div class="ibox-title">
                <h5><i class="fa fa-users"></i> Daftar User - Riwayat Tes</h5>
                <div class="ibox-tools">
                    <span class="badge badge-primary" id="total-users">{{ $totalUsers }} User</span>
                </div>
            </div>
            <div class="ibox-content">
                <!-- DataTable -->
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" id="users-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama User</th>
                                <th>Email</th>
                                <th>Package</th>
                                <th>Total Tes</th>
                                <th>Terakhir Tes</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $index => $user)
                                <tr>
                                    <td>{{ $users->firstItem() + $index }}</td>
                                    <td>
                                        <div class="user-info-cell">
                                            <div class="user-avatar-small">
                                                {{ strtoupper(substr($user->name, 0, 2)) }}
                                            </div>
                                            <div class="user-details-cell">
                                                <strong>{{ $user->name }}</strong>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <span class="badge badge-{{ $user->package == 'lengkap' ? 'success' : ($user->package == 'akademik' ? 'info' : ($user->package == 'kecerdasan' ? 'primary' : ($user->package == 'akademik' ? 'warning' : 'secondary'))) }}">
                                            {{ $user->getPackageDisplayName() }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-info">{{ $user->total_tests }}</span>
                                    </td>
                                    <td>
                                        @if($user->total_tests > 0)
                                            <small class="text-muted">
                                                {{ \Carbon\Carbon::parse($user->hasilTes->first()->tanggal_tes ?? now())->diffForHumans() }}
                                            </small>
                                        @else
                                            <small class="text-muted">Belum ada tes</small>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.riwayat-tes-user', $user->id) }}" class="btn btn-primary btn-sm">
                                            <i class="fa fa-history"></i> Riwayat Tes
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $users->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
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
    background: rgba(0,0,0,0.05);
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

/* DataTable Styling */
#users-table {
    font-size: 14px;
}

#users-table th {
    background-color: #f8f9fa;
    font-weight: 600;
    color: #333;
    border-bottom: 2px solid #dee2e6;
    padding: 12px 8px;
}

#users-table td {
    padding: 12px 8px;
    vertical-align: middle;
    border-bottom: 1px solid #f0f0f0;
}

#users-table tbody tr:hover {
    background-color: #f8f9fa;
}

/* User Info Cell */
.user-info-cell {
    display: flex;
    align-items: center;
}

.user-avatar-small {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 10px;
    color: white;
    font-weight: bold;
    font-size: 12px;
}

.user-details-cell {
    flex: 1;
}

/* Badge Styling */
.badge {
    font-size: 11px;
    padding: 4px 8px;
    border-radius: 12px;
}

.badge-success {
    background-color: #28a745;
    color: white;
}

.badge-info {
    background-color: #17a2b8;
    color: white;
}

.badge-primary {
    background-color: #007bff;
    color: white;
}

.badge-warning {
    background-color: #ffc107;
    color: #212529;
}

.badge-secondary {
    background-color: #6c757d;
    color: white;
}

/* Button Styling */
.btn-sm {
    padding: 4px 8px;
    font-size: 12px;
}

/* Pagination Style - Same as User Index and Soal Index */
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

/* Responsive Design */
@media (max-width: 768px) {
    .table-responsive {
        font-size: 12px;
    }

    #users-table th,
    #users-table td {
        padding: 8px 4px;
    }

    .user-avatar-small {
        width: 30px;
        height: 30px;
        font-size: 10px;
        margin-right: 8px;
    }

    .btn-sm {
        padding: 2px 6px;
        font-size: 10px;
    }

    .badge {
        font-size: 9px;
        padding: 2px 6px;
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

    .table-responsive {
        font-size: 11px;
    }

    #users-table th,
    #users-table td {
        padding: 6px 2px;
    }

    .user-info-cell {
        flex-direction: column;
        text-align: center;
    }

    .user-avatar-small {
        margin-right: 0;
        margin-bottom: 5px;
    }
}
</style>
@endpush