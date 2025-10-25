<nav class="navbar-default navbar-static-side" role="navigation">
    <div class="sidebar-collapse">
        <style>
            .fa.arrow {
                float: right !important;
                margin-top: 2px !important;
                line-height: 1 !important;
            }
            .nav > li > a {
                display: flex !important;
                align-items: center !important;
                justify-content: space-between !important;
            }
            .nav > li > a .nav-label {
                flex: 1 !important;
            }
        </style>
        <ul class="nav metismenu" id="side-menu">
            <li class="nav-header">
                <div class="dropdown profile-element">
                    @auth
                    <div
                        class="avatar bg-{{ ['primary', 'success', 'info', 'warning', 'danger'][array_rand(['primary', 'success', 'info', 'warning', 'danger'])] }} mb-1">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                        <span class="text-medium mt-3 block">
                            {{ Auth::user()->name }}
                        </span>
                    </a>
                    @endauth
                </div>

                <div class="logo-element">
                    <img src="{{ asset('img/logo.png') }}" alt="Logo" class="logo">
                </div>
            </li>

            @auth
            @if (Auth::user()->role == 'admin')
                <li>
                    <a href="{{ route('admin.dashboard') }}">
                        <i class="fa fa-dashboard"></i>
                        <span class="nav-label">Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.users.index') }}">
                        <i class="fa fa-user"></i>
                        <span class="nav-label">User</span>
                    </a>
                </li>

                <!-- Master Soal AKADEMIK -->
                <li>
                    <a href="#"><i class="fa fa-graduation-cap"></i> <span class="nav-label">Master Soal AKADEMIK</span> <span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level collapse">
                        <li>
                            <a href="{{ route('admin.kategori.index', ['type' => 'akademik']) }}">
                                <i class="fa fa-tags"></i>
                                <span class="nav-label">Kategori Soal</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.soal.index', ['type' => 'akademik']) }}">
                                <i class="fa fa-question-circle"></i>
                                <span class="nav-label">Soal</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Manajemen Tes -->
                <li>
                    <a href="#"><i class="fa fa-graduation-cap"></i> <span class="nav-label">Manajemen Tes</span> <span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level collapse">
                        <li>
                            <a href="{{ route('admin.tryout.index') }}">
                                <i class="fa fa-graduation-cap"></i>
                                <span class="nav-label">Tryout</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.riwayat-tes') }}">
                                <i class="fa fa-history"></i>
                                <span class="nav-label">Riwayat Tes</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Laporan -->
                <li>
                    <a href="{{ route('admin.laporan.kemampuan.index') }}">
                        <i class="fa fa-chart-line"></i>
                        <span class="nav-label">Laporan Kemampuan</span>
                    </a>
                </li>

                <!-- Manajemen Paket -->
                <li>
                    <a href="{{ route('admin.packages.index') }}">
                        <i class="fa fa-shopping-cart"></i>
                        <span class="nav-label">Kelola Paket</span>
                    </a>
                </li>

                <!-- Pengaturan -->
                <li>
                    <a href="#"><i class="fa fa-cogs"></i> <span class="nav-label">Pengaturan</span> <span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level collapse">
                        <li>
                            <a href="{{ route('admin.package-mapping.index') }}">
                                <i class="fa fa-gem"></i>
                                <span class="nav-label">Pengaturan Paket</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.scoring-settings.index') }}">
                                <i class="fa fa-sliders"></i>
                                <span class="nav-label">Pengaturan Simulasi Nilai</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.youtube-videos.index') }}">
                                <i class="fa fa-youtube-play"></i>
                                <span class="nav-label">Link YouTube Video</span>
                            </a>
                        </li>
                    </ul>
                </li>
            @endif

            @if (Auth::user()->role == 'user')
                <li>
                    <a href="{{ route('user.profile', ['userId' => Auth::user()->id]) }}">
                        <i class="fa fa-user-circle"></i>
                        <span class="nav-label">Profile</span>
                    </a>
                </li>

                {{-- Menu Paket - Selalu tampil untuk semua user --}}
                <li>
                    <a href="{{ route('subscription.packages') }}">
                        <i class="fa fa-gem"></i>
                        <span class="nav-label">Paket</span>
                        {{-- Badge untuk menunjukkan paket saat ini --}}
                        <span class="badge badge-primary pull-right" style="font-size: 10px;">
                            {{ Auth::user()->getPackageDisplayName() }}
                        </span>
                    </a>
                </li>

                <!-- Manajemen Tes -->
                <li>
                    <a href="#"><i class="fa fa-graduation-cap"></i> <span class="nav-label">Manajemen Tes</span> <span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level collapse">
                        <li>
                            <a href="{{ route('show.test') }}">
                                <i class="fa fa-check-square-o"></i>
                                <span class="nav-label">Tes</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('simulasi.nilai') }}">
                                <i class="fa fa-balance-scale"></i>
                                <span class="nav-label">Simulasi Nilai</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('user.history.index') }}">
                                <i class="fa fa-history"></i>
                                <span class="nav-label">Riwayat Tes</span>
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- Tampilkan pesan jika tidak ada subscription aktif --}}
                @if (!Auth::user()->hasActiveSubscription())
                    <li class="text-warning" style="padding: 8px 20px; border-left: 3px solid #f8ac59;">
                        <small>
                            <i class="fa fa-exclamation-triangle"></i>
                            <strong>Langganan Diperlukan</strong><br>
                            <span style="color: #676a6c;">Pilih paket untuk akses fitur</span>
                        </small>
                    </li>
                @endif
            @endif
            @endauth


        </ul>
    </div>
</nav>
