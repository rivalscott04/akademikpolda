@extends('layouts.app')

@section('title', 'YouTube Videos')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>YouTube Videos</h5>
                <div class="ibox-tools">
                    <a href="{{ route('admin.youtube-videos.create') }}" class="btn btn-primary btn-sm">
                        <i class="fa fa-plus"></i> Tambah Video
                    </a>
                </div>
            </div>
            <div class="ibox-content">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissable">
                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                        {{ session('success') }}
                    </div>
                @endif

                @if($videos->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>YouTube URL</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($videos as $video)
                                <tr>
                                    <td>{{ $video->id }}</td>
                                    <td>{{ $video->title ?? '-' }}</td>
                                    <td>
                                        <a href="{{ $video->youtube_url }}" target="_blank" class="text-primary">
                                            {{ Str::limit($video->youtube_url, 50) }}
                                        </a>
                                    </td>
                                    <td>
                                        @if($video->is_active)
                                            <span class="label label-primary">Active</span>
                                        @else
                                            <span class="label label-default">Inactive</span>
                                        @endif
                                    </td>
                                    <td>{{ $video->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <a href="{{ route('admin.youtube-videos.show', $video) }}" class="btn btn-info btn-xs">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.youtube-videos.edit', $video) }}" class="btn btn-warning btn-xs">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-danger btn-xs" onclick="showDeleteModal({{ $video->id }}, '{{ $video->title ?? 'Video YouTube' }}')">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center">
                        <p>Belum ada video YouTube yang ditambahkan.</p>
                        <a href="{{ route('admin.youtube-videos.create') }}" class="btn btn-primary">
                            <i class="fa fa-plus"></i> Tambah Video Pertama
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal Hapus YouTube Video -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="fa fa-exclamation-triangle"></i> Konfirmasi Hapus Video
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="fa fa-youtube-play fa-3x text-danger"></i>
                </div>
                <p class="text-center">
                    Apakah Anda yakin ingin menghapus video berikut?
                </p>
                <div class="alert alert-warning">
                    <strong>Video:</strong>
                    <div id="videoPreview" class="mt-2 p-2 bg-light rounded"></div>
                </div>
                <div class="alert alert-danger">
                    <i class="fa fa-warning"></i>
                    <strong>Peringatan:</strong> Tindakan ini tidak dapat dibatalkan. Video akan dihapus permanen.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fa fa-times"></i> Batal
                </button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fa fa-trash"></i> Ya, Hapus Video
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Function to show delete modal
        window.showDeleteModal = function(videoId, videoTitle) {
            $('#deleteForm').attr('action', `/admin/youtube-videos/${videoId}`);
            $('#videoPreview').text(videoTitle);
            $('#deleteModal').modal('show');
        };

        // Handle modal close events
        $('#deleteModal').on('hidden.bs.modal', function () {
            // Reset form action when modal is closed
            $('#deleteForm').attr('action', '');
        });

        // Handle close button clicks
        $('#deleteModal .close, #deleteModal [data-dismiss="modal"]').on('click', function() {
            $('#deleteModal').modal('hide');
        });
    });
</script>
@endpush
