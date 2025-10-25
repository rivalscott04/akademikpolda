@extends('layouts.app')

@section('title', 'Tambah YouTube Video')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Tambah YouTube Video</h5>
                <div class="ibox-tools">
                    <a href="{{ route('admin.youtube-videos.index') }}" class="btn btn-default btn-sm">
                        <i class="fa fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
            <div class="ibox-content">
                <form action="{{ route('admin.youtube-videos.store') }}" method="POST" class="form-horizontal">
                    @csrf
                    
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Title</label>
                        <div class="col-sm-10">
                            <input type="text" name="title" class="form-control" value="{{ old('title') }}" placeholder="Judul video (opsional)">
                            @error('title')
                                <span class="help-block text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">YouTube URL <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <input type="url" name="youtube_url" class="form-control" value="{{ old('youtube_url') }}" placeholder="https://www.youtube.com/watch?v=..." required>
                            @error('youtube_url')
                                <span class="help-block text-danger">{{ $message }}</span>
                            @enderror
                            <span class="help-block">Masukkan URL lengkap video YouTube</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Description</label>
                        <div class="col-sm-10">
                            <textarea name="description" class="form-control" rows="3" placeholder="Deskripsi video (opsional)">{{ old('description') }}</textarea>
                            @error('description')
                                <span class="help-block text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Status</label>
                        <div class="col-sm-10">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="is_active" value="1" {{ old('is_active') ? 'checked' : '' }}>
                                    Aktif (video akan ditampilkan di halaman utama)
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-4 col-sm-offset-2">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                            <a href="{{ route('admin.youtube-videos.index') }}" class="btn btn-default">Batal</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
