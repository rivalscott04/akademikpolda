<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OpsiSoal extends Model
{
    protected $table = 'opsi_soal';
    
    protected $fillable = [
        'soal_id',
        'opsi',
        'teks',
        'bobot'
    ];

    protected $casts = [
        'bobot' => 'decimal:2'
    ];

    public function soal()
    {
        return $this->belongsTo(Soal::class, 'soal_id');
    }
} 