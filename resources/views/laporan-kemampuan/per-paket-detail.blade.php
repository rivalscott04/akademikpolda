@extends('layouts.app')

@section('title', 'Laporan Per Paket - Detail')

@section('content')
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-title">
                    <h5><i class="fa fa-layer-group"></i> Laporan Per Paket - {{ $packageName }}</h5>
                    <div class="ibox-tools">
                        <a href="{{ route('admin.laporan.kemampuan.per-paket') }}" class="btn btn-default btn-sm">
                            <i class="fa fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="text-center mb-4">
                                <h3>Pilih Siswa untuk Laporan {{ $packageName }}</h3>
                                <p class="text-muted">Pilih siswa untuk melihat analisis kemampuan pada paket {{ $packageName }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="kategori-select">Pilih Kategori:</label>
                                <select id="kategori-select" class="form-control" name="kategori_id">
                                    <option value="">Pilih Kategori...</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="siswa-select">Pilih Siswa:</label>
                                <select id="siswa-select" class="form-control" name="user_id">
                                    <option value="">Pilih Siswa...</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="text-center">
                                <button id="generate-laporan" class="btn btn-info btn-lg" disabled>
                                    <i class="fa fa-chart-pie"></i> Generate Laporan
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="{{ asset('css/laporan-kemampuan.css') }}" rel="stylesheet">
@endpush

@push('scripts')
<script src="{{ asset('js/laporan-kemampuan.js') }}"></script>
<script>
$(document).ready(function() {
    const packageName = '{{ $packageName }}';
    
    // Load kategori berdasarkan paket
    $.get('{{ route("admin.laporan.kemampuan.kategori-by-paket") }}', {package_type: packageName})
        .done(function(data) {
            const select = $('#kategori-select');
            data.forEach(function(kategori) {
                select.append(`<option value="${kategori.id}">${kategori.nama}</option>`);
            });
        });
    
    // Load siswa berdasarkan paket (semua siswa yang pernah tes di paket ini)
    $.get('{{ route("admin.laporan.kemampuan.siswa-by-paket") }}', {
        package_type: packageName
    }).done(function(data) {
        const siswaSelect = $('#siswa-select');
        data.forEach(function(siswa) {
            siswaSelect.append(`<option value="${siswa.id}">${siswa.name}</option>`);
        });
    });
    
    // Load siswa berdasarkan paket dan kategori (untuk filtering)
    $('#kategori-select').change(function() {
        const kategoriId = $(this).val();
        const siswaSelect = $('#siswa-select');
        
        // Clear existing options
        siswaSelect.empty().append('<option value="">Pilih Siswa...</option>');
        $('#generate-laporan').prop('disabled', true);
        
        if (kategoriId) {
            // Load siswa yang pernah tes dengan kategori tertentu
            $.get('{{ route("admin.laporan.kemampuan.siswa-by-paket") }}', {
                package_type: packageName,
                kategori_id: kategoriId
            }).done(function(data) {
                data.forEach(function(siswa) {
                    siswaSelect.append(`<option value="${siswa.id}">${siswa.name}</option>`);
                });
            });
        } else {
            // Load semua siswa yang pernah tes di paket ini
            $.get('{{ route("admin.laporan.kemampuan.siswa-by-paket") }}', {
                package_type: packageName
            }).done(function(data) {
                data.forEach(function(siswa) {
                    siswaSelect.append(`<option value="${siswa.id}">${siswa.name}</option>`);
                });
            });
        }
    });
    
    // Enable generate button when both selections are made
    $('#siswa-select').change(function() {
        const kategoriId = $('#kategori-select').val();
        const siswaId = $(this).val();
        $('#generate-laporan').prop('disabled', !(kategoriId && siswaId));
    });
    
    // Generate laporan
    $('#generate-laporan').click(function() {
        const form = $('<form>', {
            method: 'POST',
            action: '{{ route("admin.laporan.kemampuan.generate-per-paket") }}'
        });
        
        form.append($('<input>', {
            type: 'hidden',
            name: '_token',
            value: '{{ csrf_token() }}'
        }));
        
        form.append($('<input>', {
            type: 'hidden',
            name: 'user_id',
            value: $('#siswa-select').val()
        }));
        
        form.append($('<input>', {
            type: 'hidden',
            name: 'package_type',
            value: packageName
        }));
        
        form.append($('<input>', {
            type: 'hidden',
            name: 'kategori_id',
            value: $('#kategori-select').val()
        }));
        
        $('body').append(form);
        form.submit();
    });
});
</script>
@endpush
