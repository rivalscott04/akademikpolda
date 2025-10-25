<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriSoal extends Model
{
    protected $table = 'kategori_soal';
    
    protected $fillable = [
        'nama',
        'kode',
        'deskripsi',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function soals()
    {
        return $this->hasMany(Soal::class, 'kategori_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByStatus($query, $status)
    {
        if (is_bool($status)) {
            return $query->where('is_active', $status);
        }
        if ($status === 'aktif') {
            return $query->where('is_active', true);
        }
        if ($status === 'nonaktif') {
            return $query->where('is_active', false);
        }
        return $query;
    }

    public function scopeSearch($query, $term)
    {
        $trimmed = trim((string) $term);
        if ($trimmed === '') {
            return $query;
        }
        return $query->where(function ($q) use ($trimmed) {
            $q->where('nama', 'like', '%' . str_replace('%', '\\%', $trimmed) . '%')
              ->orWhere('kode', 'like', '%' . str_replace('%', '\\%', strtoupper($trimmed)) . '%');
        });
    }

} 