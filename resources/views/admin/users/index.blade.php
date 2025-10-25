@extends('layouts.app')

@section('content')

    @push('styles')
        <style>
            .avatar {
                width: 40px;
                height: 40px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: bold;
                color: white;
            }

            /* Status select styling - Clean Inspinia Theme */
            .status-wrapper {
                display: inline-block;
                min-width: 100px;
            }

            .status-select {
                appearance: none;
                -webkit-appearance: none;
                -moz-appearance: none;
                background: #fff;
                border: 1px solid #e1e5e9;
                border-radius: 4px;
                padding: 6px 25px 6px 10px;
                font-size: 12px;
                font-weight: 500;
                color: #2c3e50;
                cursor: pointer;
                transition: all 0.2s ease;
                min-width: 100px;
                text-align: center;
                background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6,9 12,15 18,9'%3e%3c/polyline%3e%3c/svg%3e");
                background-repeat: no-repeat;
                background-position: right 8px center;
                background-size: 12px;
            }

            .status-select:hover {
                border-color: #1ab394;
                box-shadow: 0 2px 4px rgba(26, 179, 148, 0.1);
            }

            .status-select:focus {
                outline: none;
                border-color: #1ab394;
                box-shadow: 0 0 0 2px rgba(26, 179, 148, 0.1);
            }

            .status-select.active {
                background-color: #1ab394;
                color: #FFFFFF;
                border-color: #1ab394;
                background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23FFFFFF' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6,9 12,15 18,9'%3e%3c/polyline%3e%3c/svg%3e");
            }

            .status-select.active:hover {
                background-color: #18a085;
                border-color: #18a085;
            }

            .status-select.inactive {
                background-color: #ED5565;
                color: #FFFFFF;
                border-color: #ED5565;
                background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23FFFFFF' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6,9 12,15 18,9'%3e%3c/polyline%3e%3c/svg%3e");
            }

            .status-select.inactive:hover {
                background-color: #e74c3c;
                border-color: #e74c3c;
            }

            /* Pill dropdown badge styles to mimic example */
            .package-wrapper, .status-wrapper { display: inline-block; min-width: 120px; }
            .pill-dropdown { position: relative; display: inline-block; }
            .pill-btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 6px;
                padding: 6px 12px;
                font-size: 12px;
                font-weight: 600;
                border-radius: 999px;
                border: 1px solid transparent;
                color: #fff;
                cursor: pointer;
                min-width: 120px;
            }
            .pill-caret { margin-left: 6px; font-size: 11px; opacity: .9; }
            .pill-primary { background-color: #1ab394; border-color: #1ab394; }
            .pill-info { background-color: #1c84c6; border-color: #1c84c6; }
            .pill-warning { background-color: #f8ac59; border-color: #f8ac59; }
            .pill-danger { background-color: #ED5565; border-color: #ED5565; }
            .pill-default { background-color: #D1DADE; border-color: #D1DADE; color: #5E5E5E; }
            .pill-menu { position: absolute; top: 100%; left: 0; z-index: 1000; display: none; min-width: 140px; padding: 6px 0; margin-top: 4px; background: #fff; border: 1px solid #e7eaec; border-radius: 6px; box-shadow: 0 6px 18px rgba(0,0,0,.08); }
            .pill-menu.show { display: block; }
            .pill-item { display: block; width: 100%; text-align: left; padding: 8px 12px; font-size: 12px; color: #676a6c; background: #fff; border: 0; cursor: pointer; }
            .pill-item:hover { background: #f5f5f5; }

            .action-icon {
                color: #667085;
                margin: 0 4px;
                cursor: pointer;
            }

            .fa-trash {
                color: #f13535;
                font-size: 20px;
            }

            .fa-edit {
                color: #007BFF;
                font-size: 18px;
            }

            .fa-info-circle {
                color: #000;
                font-size: 18px;
            }

            .username {
                font-weight: 500;
                color: #101828;
            }

            .handle {
                color: #667085;
                font-size: 14px;
            }




            /* Search Box Styling */
            .input-group-addon {
                background-color: #f3f3f4;
                border-color: #e7eaec;
                color: #676a6c;
            }

            #searchInput {
                border-color: #e7eaec;
                transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
            }

            #searchInput:focus {
                border-color: #1ab394;
                box-shadow: 0 0 0 0.2rem rgba(26, 179, 148, 0.25);
            }

            #clearSearch {
                border-color: #e7eaec;
                color: #676a6c;
                transition: all 0.15s ease-in-out;
                border-left: none;
                border-radius: 0 3px 3px 0;
            }

            #clearSearch:hover {
                background-color: #f3f3f4;
                border-color: #d2d2d2;
                color: #333;
            }

            /* Ensure input border radius matches */
            #searchInput {
                border-radius: 3px 0 0 3px;
            }

            /* Page Length Control Styling */
            #pageLength {
                border-color: #e7eaec;
                border-radius: 3px;
                font-size: 12px;
                padding: 4px 8px;
                height: 30px;
                transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
            }

            #pageLength:focus {
                border-color: #1ab394;
                box-shadow: 0 0 0 0.2rem rgba(26, 179, 148, 0.25);
            }

            /* Align form group elements */
            .form-group label {
                display: block;
                margin-bottom: 5px;
            }

            /* Ensure all elements are aligned properly */
            .align-items-center {
                align-items: center !important;
            }

            .row.align-items-center {
                min-height: 40px;
            }

            /* Make search results vertically centered */
            #searchResults {
                line-height: 34px;
                display: inline-block;
            }

            /* Responsive styling for search controls */
            @media screen and (max-width: 767px) {
                .row.align-items-center {
                    flex-direction: column;
                    align-items: stretch !important;
                }

                .row.align-items-center>div {
                    margin-bottom: 10px;
                }

                .row.align-items-center>div:last-child {
                    margin-bottom: 0;
                }

                #searchInput {
                    border-radius: 3px;
                }

                #clearSearch {
                    border-radius: 0 3px 3px 0;
                }

                .d-flex.align-items-center.justify-content-center {
                    justify-content: flex-start !important;
                }

                #searchResults {
                    text-align: center;
                    line-height: normal;
                    margin-top: 5px;
                }
            }

            /* DataTable Custom Styling */
            .dataTables_wrapper .dataTables_length select {
                border: 1px solid #e7eaec;
                border-radius: 3px;
                padding: 4px 8px;
            }

            /* Responsive DataTable Styling */
            .dataTables_wrapper .dataTables_processing {
                background: rgba(255, 255, 255, 0.9);
                border: 1px solid #e7eaec;
                border-radius: 3px;
                color: #676a6c;
            }

            /* Responsive breakpoints */
            @media screen and (max-width: 767px) {

                .dataTables_wrapper .dataTables_info,
                .dataTables_wrapper .dataTables_paginate {
                    text-align: center;
                    float: none;
                    margin-top: 10px;
                }

                .dataTables_wrapper .dataTables_paginate .paginate_button {
                    margin: 0 2px;
                }

                /* Hide less important columns on mobile */
                .dataTables-example th:nth-child(3),
                .dataTables-example td:nth-child(3) {
                    display: none;
                }
            }

            @media screen and (max-width: 480px) {

                /* Hide more columns on very small screens */
                .dataTables-example th:nth-child(4),
                .dataTables-example td:nth-child(4) {
                    display: none;
                }

                .dataTables-example th:nth-child(5),
                .dataTables-example td:nth-child(5) {
                    display: none;
                }
            }

            .dataTables_wrapper .dataTables_filter input {
                border: 1px solid #e7eaec;
                border-radius: 3px;
                padding: 6px 12px;
                margin-left: 8px;
            }

            .dataTables_wrapper .dataTables_filter input:focus {
                border-color: #1ab394;
                box-shadow: 0 0 0 0.2rem rgba(26, 179, 148, 0.25);
            }

            .dataTables_wrapper .dataTables_paginate .paginate_button {
                border: 1px solid #e7eaec;
                background: #fff;
                color: #676a6c !important;
                border-radius: 3px;
                margin: 0 2px;
            }

            .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
                background: #f3f3f4 !important;
                border-color: #d2d2d2;
                color: #333 !important;
            }

            .dataTables_wrapper .dataTables_paginate .paginate_button.current {
                background: #1ab394 !important;
                border-color: #1ab394;
                color: #fff !important;
            }

            .dataTables_wrapper .dataTables_info {
                color: #676a6c;
                padding-top: 8px;
            }

            /* Responsive table wrapper */
            .table-responsive {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            /* Ensure table doesn't break on mobile */
            .table-responsive table {
                min-width: 600px;
            }

            @media screen and (max-width: 767px) {
                .table-responsive table {
                    min-width: 100%;
                }
            }
        </style>
    @endpush

@section('content')
    <div class="container">
        <div class="wrapper wrapper-content animated fadeInRight">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox">
                        <div class="ibox-title">
                            <h5>Data user</h5>
                        </div>
                        <div class="ibox-content">
                            <!-- Search Box and Controls -->
                            <div class="row mb-3 align-items-center">
                                <div class="col-md-5">
                                    <form method="GET" action="{{ route('admin.users.index') }}" id="searchForm">
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="fa fa-search"></i>
                                            </span>
                                            <input type="text" id="searchInput" name="search" class="form-control"
                                                placeholder="Cari berdasarkan nama atau email..." 
                                                value="{{ request('search') }}">
                                            <span class="input-group-btn">
                                                <button type="button" id="clearSearch" class="btn btn-default"
                                                    style="display: none;">
                                                    <i class="fa fa-times"></i>
                                                </button>
                                            </span>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-md-4 text-center">
                                    <div class="d-flex align-items-center justify-content-center">
                                        <span style="font-size: 12px; color: #676a6c; margin-right: 8px;">Tampilkan</span>
                                        <select id="pageLength" class="form-control input-sm"
                                            style="width: 60px; display: inline-block; margin: 0 5px;">
                                            <option value="10">10</option>
                                            <option value="25" selected>25</option>
                                            <option value="50">50</option>
                                            <option value="100">100</option>
                                        </select>
                                        <span style="font-size: 12px; color: #676a6c; margin-left: 5px;">data</span>
                                    </div>
                                </div>
                                <div class="col-md-3 text-right">
                                    @if(request('search'))
                                        <span class="text-muted" style="font-size: 12px;">
                                            <i class="fa fa-info-circle"></i> 
                                            {{ $users->total() }} dari {{ $users->total() }} data ditemukan untuk "{{ request('search') }}"
                                            <a href="{{ route('admin.users.index') }}" class="text-danger ml-2" title="Hapus pencarian">
                                                <i class="fa fa-times"></i>
                                            </a>
                                        </span>
                                    @else
                                        <span id="searchResults" class="text-muted" style="font-size: 12px;"></span>
                                    @endif
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered table-hover dataTables-example">
                                    <thead>
                                        <tr>
                                            <th>USER</th>
                                            <th>EMAIL</th>
                                            <th class="text-center">PACKAGE</th>
                                            <th class="text-center">STATUS</th>
                                            <th class="text-center">ACTIONS</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($users as $user)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div
                                                            class="avatar bg-{{ ['primary', 'success', 'info', 'warning', 'danger'][array_rand(['primary', 'success', 'info', 'warning', 'danger'])] }} mr-3">
                                                            {{ strtoupper(substr($user->name, 0, 2)) }}
                                                        </div>
                                                        <div>
                                                            <div class="username">{{ $user->name }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>{{ $user->email }}</td>
                                                <td class="text-center">
                                                    <div class="package-wrapper">
                                                        @php
                                                            $currentPackage = $user->package ?? '';
                                                            $pkgLabel = $currentPackage ? ucfirst($currentPackage) : 'No Package';
                                                            $pkgClass = match($currentPackage){
                                                                'lengkap' => 'pill-primary',
                                                                'kecerdasan','akademik' => 'pill-info',
                                                                'akademik' => 'pill-warning',
                                                                'free' => 'pill-default',
                                                                default => 'pill-default',
                                                            };
                                                        @endphp
                                                        <div class="pill-dropdown" data-dropdown="pkg-{{ $user->id }}">
                                                            <button type="button" class="pill-btn {{ $pkgClass }}">
                                                                {{ $pkgLabel }} <span class="pill-caret">▾</span>
                                                            </button>
                                                            <div class="pill-menu" id="pkg-{{ $user->id }}">
                                                                <form method="POST" action="{{ route('admin.users.updatePackage', $user->id) }}">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <input type="hidden" name="package" value="">
                                                                    <button type="button" class="pill-item" data-value="lengkap">Lengkap</button>
                                                                    <button type="button" class="pill-item" data-value="akademik">Kecermatan</button>
                                                                    <button type="button" class="pill-item" data-value="kecerdasan">Kecerdasan</button>
                                                                    <button type="button" class="pill-item" data-value="akademik">Kepribadian</button>
                                                                    <button type="button" class="pill-item" data-value="free">Free</button>
                                                                    <button type="button" class="pill-item" data-value="">No Package</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <div class="status-wrapper">
                                                        @php
                                                            $isActive = (bool) $user->is_active;
                                                            $statusLabel = $isActive ? 'Active' : 'Inactive';
                                                            $statusClass = $isActive ? 'pill-primary' : 'pill-danger';
                                                        @endphp
                                                        <div class="pill-dropdown" data-dropdown="sts-{{ $user->id }}">
                                                            <button type="button" class="pill-btn {{ $statusClass }}">
                                                                {{ $statusLabel }} <span class="pill-caret">▾</span>
                                                            </button>
                                                            <div class="pill-menu" id="sts-{{ $user->id }}">
                                                                <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <input type="hidden" name="is_active" value="">
                                                                    <button type="button" class="pill-item" data-value="1">Active</button>
                                                                    <button type="button" class="pill-item" data-value="0">Inactive</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center align-items-center">
                                                        <a href="{{ route('user.profile', $user->id) }}"
                                                            class="action-icon">
                                                            <i class="fa fa-info-circle"></i>
                                                        </a>
                                                        @canBeImpersonated($user)
                                                        <a href="#" class="action-icon impersonate-btn"
                                                            onclick="confirmImpersonate({{ $user->id }}, '{{ $user->name }}', '{{ $user->email }}')"
                                                            title="Login sebagai user ini">
                                                            <i class="fa fa-user-secret" style="color: #28a745;"></i>
                                                        </a>
                                                        @endCanBeImpersonated
                                                        <form action="{{ route('admin.users.delete', $user->id) }}"
                                                            method="POST"
                                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?');"
                                                            class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger"
                                                                style="border: none; background: none; padding: 0;">
                                                                <i class="fa fa-trash action-icon"
                                                                    style="color: red; cursor: pointer;"></i>
                                                            </button>
                                                        </form>
                                                        <a href="#" class="action-icon">
                                                            <i class="fa fa-edit"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('datatables-scripts')
    <script src="{{ asset('js/plugins/dataTables/datatables.min.js') }}"></script>
@endpush

@push('scripts')
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $.fn.dataTable.Buttons.defaults.dom.button.className = 'btn btn-white btn-sm';

        $(document).ready(function() {
            // Wait a bit to ensure DOM is fully ready
            setTimeout(function() {
                window.userTable = $('.dataTables-example').DataTable({
                    pageLength: 25,
                    responsive: true,
                    dom: 'rt<"bottom"ip><"clear">',
                    searching: true, // Enable searching functionality
                    buttons: [],
                    initComplete: function() {
                        // DataTable initialized successfully
                    },
                    language: {
                        lengthMenu: "Tampilkan _MENU_ data per halaman",
                        search: "Cari:",
                        searchPlaceholder: "Cari nama atau email...",
                        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                        infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                        infoFiltered: "(difilter dari _MAX_ total data)",
                        paginate: {
                            first: "Pertama",
                            last: "Terakhir",
                            next: "Selanjutnya",
                            previous: "Sebelumnya"
                        },
                        emptyTable: "Tidak ada data yang tersedia",
                        zeroRecords: "Tidak ada data yang cocok dengan pencarian"
                    },
                    order: [
                        [0, 'asc']
                    ], // Sort by name column ascending
                    columnDefs: [{
                            targets: [0, 1], // Name and Email columns
                            searchable: true
                        },
                        {
                            targets: [2, 3, 4], // Plan, Status, Actions columns
                            searchable: false
                        }
                    ]
                });

                // Custom search functionality - using event delegation
                var searchTimeout;
                $(document).on('keyup input paste', '#searchInput', function() {
                    var searchValue = this.value;
                    clearTimeout(searchTimeout);
                    
                    // Auto-submit form after 500ms delay
                    searchTimeout = setTimeout(function() {
                        $('#searchForm').submit();
                    }, 500);
                    
                    toggleClearButton(searchValue);
                });

                // Clear search button - using event delegation
                $(document).on('click', '#clearSearch', function() {
                    $('#searchInput').val('');
                    $('#searchForm').submit();
                });

                // Function to toggle clear button visibility
                function toggleClearButton(searchValue) {
                    if (searchValue.length > 0) {
                        $('#clearSearch').show();
                    } else {
                        $('#clearSearch').hide();
                    }
                }

                // Function to update search results count
                function updateSearchResults() {
                    var searchValue = $('#searchInput').val();
                    var totalRecords = window.userTable ? window.userTable.page.info().recordsTotal : 0;
                    var filteredRecords = window.userTable ? window.userTable.page.info().recordsDisplay : 0;

                    if (searchValue) {
                        $('#searchResults').html(
                            '<i class="fa fa-info-circle"></i> ' +
                            filteredRecords + ' dari ' + totalRecords + ' data ditemukan'
                        );
                    } else {
                        $('#searchResults').html('');
                    }
                }

                // Initial call to set up search results
                updateSearchResults();

                // Initialize clear button state
                toggleClearButton($('#searchInput').val());

                // Page length control - using event delegation
                $(document).on('change', '#pageLength', function() {
                    if (window.userTable) {
                        window.userTable.page.len($(this).val()).draw();
                    }
                });

                // Pill dropdown interactions (open/close and submit)
                document.addEventListener('click', function(e) {
                    // Toggle menus
                    if (e.target.closest('.pill-dropdown')) {
                        const dd = e.target.closest('.pill-dropdown');
                        const menu = dd.querySelector('.pill-menu');
                        // Close others
                        document.querySelectorAll('.pill-menu.show').forEach(m => { if (m !== menu) m.classList.remove('show'); });
                        menu.classList.toggle('show');
                        return;
                    }
                    // Clicked outside -> close all
                    document.querySelectorAll('.pill-menu.show').forEach(m => m.classList.remove('show'));
                });

                // Submit on item click
                document.addEventListener('click', function(e) {
                    const item = e.target.closest('.pill-item');
                    if (!item) return;
                    e.preventDefault();
                    const form = item.closest('form');
                    
                    // Check if this is a package form or status form
                    const isPackageForm = form.action.includes('/package') || form.action.includes('updatePackage');
                    const isStatusForm = form.action.includes('update') && !form.action.includes('/package') && !form.action.includes('updatePackage');
                    
                    let hidden, dataValue;
                    
                    if (isPackageForm) {
                        hidden = form.querySelector('input[name="package"]');
                        dataValue = item.getAttribute('data-value');
                    } else if (isStatusForm) {
                        hidden = form.querySelector('input[name="is_active"]');
                        dataValue = item.getAttribute('data-value');
                    } else {
                        return;
                    }
                    
                    if (hidden) {
                        hidden.value = dataValue;
                    }
                    
                    // Show loading state
                    const submitBtn = form.querySelector('button[type="submit"]') || item;
                    const originalText = submitBtn.textContent;
                    submitBtn.textContent = 'Updating...';
                    submitBtn.disabled = true;
                    
                    // Refresh CSRF token before submitting
                    refreshCsrfToken().then(newToken => {
                        if (newToken) {
                            const csrfInput = form.querySelector('input[name="_token"]');
                            if (csrfInput) {
                                csrfInput.value = newToken;
                            }
                        }
                        form.submit();
                    }).catch(error => {
                        // Still submit the form even if token refresh fails
                        form.submit();
                    });
                });

            }, 100); // 100ms delay to ensure DOM is ready
        });

        // Global error handler for 419 errors
        document.addEventListener('DOMContentLoaded', function() {
            // Intercept fetch requests to handle 419 errors globally
            const originalFetch = window.fetch;
            window.fetch = function(...args) {
                return originalFetch.apply(this, args).then(response => {
                    if (response.status === 419) {
                        Swal.fire({
                            title: 'Session Expired',
                            text: 'Your session has expired. Please refresh the page and try again.',
                            icon: 'warning',
                            confirmButtonText: 'Refresh Page'
                        }).then(() => {
                            window.location.reload();
                        });
                    }
                    return response;
                });
            };

            // Periodically refresh CSRF token to prevent expiration
            setInterval(function() {
                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                if (csrfToken) {
                    fetch('{{ route("admin.users.index") }}', {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const newToken = doc.querySelector('meta[name="csrf-token"]');
                        if (newToken && newToken.getAttribute('content') !== csrfToken.getAttribute('content')) {
                            csrfToken.setAttribute('content', newToken.getAttribute('content'));
                        }
                    })
                    .catch(error => {
                        // Silent error handling
                    });
                }
            }, 300000); // Refresh every 5 minutes
        });

        // Function to refresh CSRF token
        function refreshCsrfToken() {
            return fetch('{{ route("admin.users.index") }}', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (response.status === 419) {
                    // CSRF token mismatch - redirect to login
                    window.location.href = '{{ route("login") }}';
                    return null;
                }
                return response.text();
            })
            .then(html => {
                if (!html) return null;
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newToken = doc.querySelector('meta[name="csrf-token"]');
                if (newToken) {
                    document.querySelector('meta[name="csrf-token"]').setAttribute('content', newToken.getAttribute('content'));
                    return newToken.getAttribute('content');
                }
                return null;
            })
            .catch(error => {
                return null;
            });
        }

        // Impersonate confirmation function
        function confirmImpersonate(userId, userName, userEmail) {
            Swal.fire({
                title: '<i class="fa fa-user-secret" style="color: #28a745;"></i> Konfirmasi Impersonate',
                html: `
                    <div class="text-left">
                        <p><strong>Anda akan login sebagai user berikut:</strong></p>
                        <div class="user-info p-3 mb-3" style="background: #f8f9fa; border-radius: 8px;">
                            <p><strong>Nama:</strong> ${userName}</p>
                            <p><strong>Email:</strong> ${userEmail}</p>
                        </div>
                        <p class="text-warning"><i class="fa fa-exclamation-triangle"></i>
                        <strong>Peringatan:</strong> Semua aktivitas akan tercatat sebagai user ini.</p>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fa fa-sign-in"></i> Ya, Login Sebagai User Ini',
                cancelButtonText: '<i class="fa fa-times"></i> Batal',
                reverseButtons: true,
                customClass: {
                    popup: 'swal-wide'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: 'Memproses...',
                        html: 'Sedang login sebagai user lain...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Redirect to impersonate route
                    window.location.href = "{{ route('admin.impersonate', ':userId') }}".replace(':userId',
                    userId);
                }
            });
        }
    </script>

    <style>
        .swal-wide {
            width: 500px !important;
        }

        .user-info {
            border-left: 4px solid #28a745;
        }

        .impersonate-btn:hover {
            transform: scale(1.1);
            transition: transform 0.2s ease;
        }

        .swal2-popup {
            font-size: 14px;
        }

        .swal2-confirm {
            margin-right: 10px;
        }
    </style>
@endpush
