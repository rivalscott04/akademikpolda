@extends('layouts.app')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
	<div class="col-lg-10">
		<h2>Pengaturan Simulasi Nilai</h2>
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
			<li class="breadcrumb-item active"><strong>Pengaturan Simulasi Nilai</strong></li>
		</ol>
	</div>
</div>

<div class="wrapper wrapper-content">
	<div class="row">
		<div class="col-lg-6">
			<div class="ibox">
				<div class="ibox-title"><h5>Bobot & Passing Grade</h5></div>
				<div class="ibox-content">
					@if ($errors->any())
						<div class="alert alert-danger">
							<ul class="mb-0">
								@foreach ($errors->all() as $error)
									<li>{{ $error }}</li>
								@endforeach
							</ul>
						</div>
					@endif
					@if (session('success'))
						<div class="alert alert-success">{{ session('success') }}</div>
					@endif

					<form method="POST" action="{{ route('admin.scoring-settings.update') }}" class="form-horizontal">
						@csrf
						@method('PUT')

						<div class="form-group"><label class="col-sm-5 control-label">Bobot Kecermatan (%)</label>
							<div class="col-sm-7"><input type="number" min="0" max="100" class="form-control" name="weight_kecermatan" value="{{ old('weight_kecermatan', $setting->weight_kecermatan) }}" required></div>
						</div>

						<div class="form-group"><label class="col-sm-5 control-label">Bobot Kecerdasan (%)</label>
							<div class="col-sm-7"><input type="number" min="0" max="100" class="form-control" name="weight_kecerdasan" value="{{ old('weight_kecerdasan', $setting->weight_kecerdasan) }}" required></div>
						</div>

						<div class="form-group"><label class="col-sm-5 control-label">Bobot Kepribadian (%)</label>
							<div class="col-sm-7"><input type="number" min="0" max="100" class="form-control" name="weight_kepribadian" value="{{ old('weight_kepribadian', $setting->weight_kepribadian) }}" required></div>
						</div>

						<div class="form-group"><label class="col-sm-5 control-label">Nilai Minimal Kelulusan</label>
							<div class="col-sm-7"><input type="number" min="0" max="100" class="form-control" name="passing_grade" value="{{ old('passing_grade', $setting->passing_grade) }}" required></div>
						</div>

						<div class="form-group">
                        <div class="col-sm-7 col-sm-offset-5">
                            <button class="btn btn-primary" type="submit" id="btnSimpan"><i class="fa fa-save"></i> Simpan</button>
                        </div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
@push('scripts')
<script>
    (function(){
        var form = document.querySelector('form');
        if (!form) return;
        var btn = document.getElementById('btnSimpan');
        form.addEventListener('submit', function(){
            if(btn){
                btn.disabled = true;
                btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Menyimpan...';
            }
        });
    })();
</script>
@endpush
@endsection


