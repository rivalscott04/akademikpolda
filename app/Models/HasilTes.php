<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HasilTes extends Model
{
    protected $fillable = [
        'user_id',
        'jenis_tes',
        'skor_benar',
        'skor_salah',
        'waktu_total',
        'average_time',
        'detail_jawaban',
        'tanggal_tes',
        'tkp_final_score',
        'panker',
        'tianker',
        'janker',
        'hanker',
        'skor_akhir',
        'kategori_skor',
    ];

    protected $casts = [
        'tkp_final_score' => 'decimal:2',
        'skor_akhir' => 'decimal:2'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
