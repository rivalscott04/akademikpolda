@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4><i class="fa fa-cogs"></i> Pengaturan Paket</h4>
                    <div>
                        <button type="button" class="btn btn-warning" onclick="resetMappings()">
                            <i class="fa fa-refresh"></i> Reset ke Default
                        </button>
                        <button type="button" class="btn btn-success" onclick="saveMappings()">
                            <i class="fa fa-save"></i> Simpan Pengaturan
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    

                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i>
                        <strong>Petunjuk:</strong> Centang kategori soal yang akan muncul untuk setiap jenis paket. 
                        Kategori yang dicentang akan tersedia saat admin membuat tryout dengan paket tersebut.
                    </div>

                    <form id="mappingForm" action="{{ route('admin.package-mapping.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            @foreach([
                                'bahasa_inggris' => 'Paket Bahasa Inggris', 
                                'pu' => 'Paket Pengetahuan Umum', 
                                'twk' => 'Paket TWK', 
                                'numerik' => 'Paket Numerik', 
                                'lengkap' => 'Paket Lengkap'
                            ] as $packageType => $packageName)
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-header bg-{{ $packageType === 'lengkap' ? 'danger' : ($packageType === 'bahasa_inggris' ? 'primary' : ($packageType === 'pu' ? 'success' : ($packageType === 'twk' ? 'info' : 'warning'))) }} text-white">
                                        <h5 class="mb-0">
                                            <i class="fa fa-{{ $packageType === 'lengkap' ? 'star' : ($packageType === 'bahasa_inggris' ? 'language' : ($packageType === 'pu' ? 'book' : ($packageType === 'twk' ? 'flag' : 'calculator'))) }}"></i>
                                            {{ $packageName }}
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">                                            <label class="font-weight-bold">Pilih Kategori Soal:</label>
                                            @foreach($kategoris as $kategori)
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" 
                                                           class="custom-control-input package-checkbox" 
                                                           id="{{ $packageType }}_{{ $kategori->id }}"
                                                           name="mappings[{{ $packageType }}][]" 
                                                           value="{{ $kategori->id }}"
                                                           {{ in_array($kategori->kode, $mappings[$packageType] ?? []) ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="{{ $packageType }}_{{ $kategori->id }}">
                                                        <strong>{{ $kategori->kode }}</strong> - {{ $kategori->nama }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Reset -->
<div class="modal fade" id="resetModal" tabindex="-1" role="dialog" aria-labelledby="resetModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title" id="resetModalLabel">
                    <i class="fa fa-exclamation-triangle"></i> Konfirmasi Reset
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="fa fa-refresh fa-3x text-warning"></i>
                </div>
                <p class="text-center">
                    Apakah Anda yakin ingin mereset pengaturan paket ke default?
                </p>
                <div class="alert alert-warning">
                    <i class="fa fa-warning"></i>
                    <strong>Peringatan:</strong> Semua pengaturan paket yang sudah disesuaikan akan dikembalikan ke pengaturan awal.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fa fa-times"></i> Batal
                </button>
                <form action="{{ route('admin.package-mapping.reset') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-warning">
                        <i class="fa fa-refresh"></i> Ya, Reset ke Default
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function saveMappings() {
    // Validate at least one category is selected for each package
    let hasError = false;
    const packageTypes = ['bahasa_inggris', 'pu', 'twk', 'numerik', 'lengkap'];
    
    packageTypes.forEach(packageType => {
        const checkedBoxes = $(`input[name="mappings[${packageType}][]"]:checked`);
        if (checkedBoxes.length === 0) {
            hasError = true;
            $(`.card:has(input[name="mappings[${packageType}][]"])`).addClass('border-danger');
        } else {
            $(`.card:has(input[name="mappings[${packageType}][]"])`).removeClass('border-danger');
        }
    });
    
    if (hasError) {
        alert('Setiap paket harus memiliki minimal satu kategori yang dipilih!');
        return;
    }
    
    $('#mappingForm').submit();
}

function resetMappings() {
    $('#resetModal').modal('show');
}

// Ensure reset modal can be closed via X and Cancel
$('#resetModal').on('shown.bs.modal', function() {
    $(this).attr('data-backdrop', true).attr('data-keyboard', true);
});
$(document).on('click', '#resetModal .close, #resetModal .btn-secondary', function(e) {
    e.preventDefault();
    $('#resetModal').modal('hide');
});

// Remove border-danger class when checkbox is checked
$('.package-checkbox').on('change', function() {
    const packageType = $(this).attr('name').match(/\[(.*?)\]/)[1];
    const checkedBoxes = $(`input[name="mappings[${packageType}][]"]:checked`);
    
    if (checkedBoxes.length > 0) {
        $(`.card:has(input[name="mappings[${packageType}][]"])`).removeClass('border-danger');
    }
});
</script>
@endpush
