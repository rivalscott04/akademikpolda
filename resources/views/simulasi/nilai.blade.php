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

                        <div class="form-group"><label class="col-sm-5 control-label">Bahasa Inggris</label>
                            <div class="col-sm-7"><input type="number" min="0" max="100" class="form-control" name="bahasa_inggris" value="{{ old('bahasa_inggris') }}" placeholder="Masukkan skor bahasa Inggris (0-100)" required></div>
                        </div>

                        <div class="form-group"><label class="col-sm-5 control-label">Pengetahuan Umum</label>
                            <div class="col-sm-7"><input type="number" min="0" max="100" class="form-control" name="pengetahuan_umum" value="{{ old('pengetahuan_umum') }}" placeholder="Masukkan skor pengetahuan umum (0-100)" required></div>
                        </div>

                        <div class="form-group"><label class="col-sm-5 control-label">Tes Wawasan Kebangsaan</label>
                            <div class="col-sm-7"><input type="number" min="0" max="100" class="form-control" name="twk" value="{{ old('twk') }}" placeholder="Masukkan skor TWK (0-100)" required></div>
                        </div>

                        <div class="form-group"><label class="col-sm-5 control-label">Penalaran Numerik</label>
                            <div class="col-sm-7"><input type="number" min="0" max="100" class="form-control" name="numerik" value="{{ old('numerik') }}" placeholder="Masukkan skor penalaran numerik (0-100)" required></div>
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
                <p class="weights-display">Bobot saat ini: Bahasa Inggris {{ $setting->weight_bahasa_inggris }}%, Pengetahuan Umum {{ $setting->weight_pu }}%, TWK {{ $setting->weight_twk }}%, Penalaran Numerik {{ $setting->weight_numerik }}%.</p>
                <p>Nilai minimal kelulusan: <strong class="passing-grade">{{ $setting->passing_grade }}</strong></p>
                @php $hasResult = isset($result); $score = $hasResult ? $result['score'] : 0; @endphp
                <h3 class="m-t-none">Nilai Akhir: <strong class="score-display">{{ $score }}</strong>
                    {!! $hasResult ? ($result['passed'] ? '<span class="label label-success m-l-sm">LULUS</span>' : '<span class="label label-danger m-l-sm">TIDAK LULUS</span>') : '<span class="label label-default m-l-sm">Belum dihitung</span>' !!}
                </h3>
                <p class="text-muted formula-display">Rumus: ({{ $setting->weight_bahasa_inggris }}% × Bahasa Inggris) + ({{ $setting->weight_pu }}% × Pengetahuan Umum) + ({{ $setting->weight_twk }}% × TWK) + ({{ $setting->weight_numerik }}% × Penalaran Numerik)</p>
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


