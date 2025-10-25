@extends('layouts.app')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
	<div class="col-lg-10">
		<h2>Simulasi Nilai</h2>
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="{{ route('user.dashboard') }}">Dashboard</a></li>
			<li class="breadcrumb-item active"><strong>Simulasi Nilai</strong></li>
		</ol>
	</div>
</div>

<div class="wrapper wrapper-content">
	<div class="row">
		<div class="col-lg-6">
			<div class="ibox">
				<div class="ibox-title"><h5>Input Skor</h5></div>
				<div class="ibox-content">
					<form method="POST" action="{{ route('simulasi.nilai.calculate') }}" class="form-horizontal">
						@csrf

                        <div class="form-group"><label class="col-sm-5 control-label">Kecermatan</label>
                            <div class="col-sm-7"><input type="number" min="0" max="100" class="form-control" name="kecermatan" value="{{ old('kecermatan') }}" placeholder="Masukkan skor kecermatan (0-100)" required></div>
                        </div>

                        <div class="form-group"><label class="col-sm-5 control-label">Kecerdasan</label>
                            <div class="col-sm-7"><input type="number" min="0" max="100" class="form-control" name="kecerdasan" value="{{ old('kecerdasan') }}" placeholder="Masukkan skor kecerdasan (0-100)" required></div>
                        </div>

                        <div class="form-group"><label class="col-sm-5 control-label">Kepribadian</label>
                            <div class="col-sm-7"><input type="number" min="0" max="100" class="form-control" name="kepribadian" value="{{ old('kepribadian') }}" placeholder="Masukkan skor kepribadian (0-100)" required></div>
                        </div>

                    <div class="form-group">
                        <div class="col-sm-7 col-sm-offset-5">
                            <button class="btn btn-primary" type="submit" id="btnHitung">
                                <i class="fa fa-calculator"></i> Hitung
                            </button>
                            <button class="btn btn-default" type="submit" formaction="{{ route('simulasi.nilai.reset') }}" formmethod="POST">
                                @csrf
                                <i class="fa fa-undo"></i> Reset
                            </button>
                        </div>
                    </div>
					</form>
				</div>
			</div>
		</div>

		<div class="col-lg-6">
			<div class="ibox">
				<div class="ibox-title"><h5>Hasil</h5></div>
				<div class="ibox-content">
                <p class="weights-display">Bobot saat ini: Kecermatan {{ $setting->weight_kecermatan }}%, Kecerdasan {{ $setting->weight_kecerdasan }}%, Kepribadian {{ $setting->weight_kepribadian }}%.</p>
                <p>Nilai minimal kelulusan: <strong class="passing-grade">{{ $setting->passing_grade }}</strong></p>
                @php $hasResult = isset($result); $score = $hasResult ? $result['score'] : 0; @endphp
                <h3 class="m-t-none">Nilai Akhir: <strong class="score-display">{{ $score }}</strong>
                    {!! $hasResult ? ($result['passed'] ? '<span class="label label-success m-l-sm">LULUS</span>' : '<span class="label label-danger m-l-sm">TIDAK LULUS</span>') : '<span class="label label-default m-l-sm">Belum dihitung</span>' !!}
                </h3>
                <p class="text-muted formula-display">Rumus: ({{ $setting->weight_kecermatan }}% × Kecermatan) + ({{ $setting->weight_kecerdasan }}% × Kecerdasan) + ({{ $setting->weight_kepribadian }}% × Kepribadian)</p>
				</div>
			</div>
		</div>
	</div>
</div>
@push('scripts')
<!-- Load simulasi nilai calculator script -->
<script src="{{ asset('js/simulasi-nilai.js') }}"></script>
@endpush
@endsection


