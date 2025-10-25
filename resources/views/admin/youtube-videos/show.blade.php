@extends('layouts.app')

@section('title', 'Detail YouTube Video')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Detail YouTube Video</h5>
                <div class="ibox-tools">
                    <a href="{{ route('admin.youtube-videos.index') }}" class="btn btn-default btn-sm">
                        <i class="fa fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-md-8">
                        <table class="table table-bordered">
                            <tr>
                                <th width="150">ID</th>
                                <td>{{ $youtubeVideo->id }}</td>
                            </tr>
                            <tr>
                                <th>Title</th>
                                <td>{{ $youtubeVideo->title ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>YouTube URL</th>
                                <td>
                                    <a href="{{ $youtubeVideo->youtube_url }}" target="_blank" class="text-primary">
                                        {{ $youtubeVideo->youtube_url }}
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <th>YouTube ID</th>
                                <td><code>{{ $youtubeVideo->youtube_id }}</code></td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>
                                    @if($youtubeVideo->is_active)
                                        <span class="label label-primary">Active</span>
                                    @else
                                        <span class="label label-default">Inactive</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Description</th>
                                <td>{{ $youtubeVideo->description ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Created At</th>
                                <td>{{ $youtubeVideo->created_at->format('d/m/Y H:i:s') }}</td>
                            </tr>
                            <tr>
                                <th>Updated At</th>
                                <td>{{ $youtubeVideo->updated_at->format('d/m/Y H:i:s') }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-4">
                        @if($youtubeVideo->youtube_id)
                        <h4>Preview Video</h4>
                        <div class="embed-responsive embed-responsive-16by9">
                            <iframe class="embed-responsive-item" 
                                    src="{{ $youtubeVideo->embed_url }}" 
                                    frameborder="0" 
                                    allowfullscreen>
                            </iframe>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="row" style="margin-top: 20px;">
                    <div class="col-md-12">
                        <a href="{{ route('admin.youtube-videos.edit', $youtubeVideo) }}" class="btn btn-warning">
                            <i class="fa fa-edit"></i> Edit
                        </a>
                        <button type="button" class="btn btn-danger" onclick="showDeleteModal({{ $youtubeVideo->id }}, '{{ $youtubeVideo->title ?? 'Video YouTube' }}')">
                            <i class="fa fa-trash"></i> Hapus
                        </button>
                    </div>
                </div>
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
