@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Edit Paket: {{ $package->name }}</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.packages.update', $package) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Nama Paket <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                               id="name" name="name" value="{{ old('name', $package->name) }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="access_tier_id">Tipe Akses <span class="text-danger">*</span></label>
                                        <select class="form-control @error('access_tier_id') is-invalid @enderror" id="access_tier_id" name="access_tier_id" required>
                                            <option value="">-- Pilih Tipe Akses --</option>
                                            @foreach($accessTiers as $tier)
                                                <option value="{{ $tier->id }}" {{ old('access_tier_id', $package->access_tier_id) == $tier->id ? 'selected' : '' }}>{{ $tier->name }} ({{ $tier->key }})</option>
                                            @endforeach
                                        </select>
                                        @error('access_tier_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="label">Label (opsional)</label>
                                        <input type="text" class="form-control @error('label') is-invalid @enderror" 
                                               id="label" name="label" value="{{ old('label', $package->label) }}" 
                                               placeholder="Contoh: PALING LARIS, DIREKOMENDASIKAN">
                                        @error('label')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="description">Deskripsi (opsional)</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="3">{{ old('description', $package->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="price">Harga <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control @error('price') is-invalid @enderror" 
                                               id="price" name="price" value="{{ old('price', $package->price) }}" 
                                               min="0" step="0.01" required>
                                        @error('price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="old_price">Harga Lama (opsional)</label>
                                        <input type="number" class="form-control @error('old_price') is-invalid @enderror" 
                                               id="old_price" name="old_price" value="{{ old('old_price', $package->old_price) }}" 
                                               min="0" step="0.01">
                                        @error('old_price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="sort_order">Urutan Tampil</label>
                                        <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                                               id="sort_order" name="sort_order" value="{{ old('sort_order', $package->sort_order) }}" 
                                               min="0">
                                        @error('sort_order')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Fitur Paket <span class="text-danger">*</span></label>
                                <div id="features-container">
                                    @php
                                        $features = old('features', $package->features);
                                    @endphp
                                    @foreach($features as $index => $feature)
                                        <div class="input-group mb-2 feature-item">
                                            <input type="text" class="form-control" name="features[]" 
                                                   value="{{ $feature }}" required>
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-danger remove-feature">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <button type="button" class="btn btn-success btn-sm" id="add-feature">
                                    <i class="fa fa-plus"></i> Tambah Fitur
                                </button>
                                @error('features')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" 
                                           value="1" {{ old('is_active', $package->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Aktifkan paket
                                    </label>
                                </div>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save"></i> Update Paket
                                </button>
                                <a href="{{ route('admin.packages.index') }}" class="btn btn-secondary">
                                    <i class="fa fa-arrow-left"></i> Kembali
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add feature
            document.getElementById('add-feature').addEventListener('click', function() {
                const container = document.getElementById('features-container');
                const newFeature = document.createElement('div');
                newFeature.className = 'input-group mb-2 feature-item';
                newFeature.innerHTML = `
                    <input type="text" class="form-control" name="features[]" 
                           placeholder="Masukkan fitur paket" required>
                    <div class="input-group-append">
                        <button type="button" class="btn btn-danger remove-feature">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>
                `;
                container.appendChild(newFeature);
            });

            // Remove feature
            document.addEventListener('click', function(e) {
                if (e.target.closest('.remove-feature')) {
                    const featureItem = e.target.closest('.feature-item');
                    const container = document.getElementById('features-container');
                    
                    // Don't remove if it's the last feature
                    if (container.children.length > 1) {
                        featureItem.remove();
                    }
                }
            });
        });
    </script>
@endsection
