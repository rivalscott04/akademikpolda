<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Soal extends Model
{
    protected $table = 'soals';

    protected $fillable = [
        'pertanyaan',
        'tipe',
        'level',
        'kategori_id',
        'pembahasan',
        'pembahasan_type',
        'pembahasan_image',
        'jawaban_benar',
        'gambar',
        'is_active',
        'is_used',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_used' => 'boolean',
    ];

    public function kategori()
    {
        return $this->belongsTo(KategoriSoal::class, 'kategori_id');
    }

    public function opsi()
    {
        return $this->hasMany(OpsiSoal::class, 'soal_id');
    }

    public function userTryoutSoal()
    {
        return $this->hasMany(UserTryoutSoal::class, 'soal_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByKategori($query, $kategoriId)
    {
        return $query->where('kategori_id', $kategoriId);
    }

    public function scopeByTipe($query, $tipe)
    {
        return $query->where('tipe', $tipe);
    }

    public function scopeByLevel($query, $level)
    {
        return $query->where('level', $level);
    }

    public function scopeByKategoriLevel($query, $kategoriId, $level)
    {
        return $query->where('kategori_id', $kategoriId)->where('level', $level);
    }

    // Accessor for image URL
    public function getGambarUrlAttribute()
    {
        if ($this->gambar) {
            return asset('storage/' . $this->gambar);
        }
        return null;
    }

    // Accessor for pembahasan image URL
    public function getPembahasanImageUrlAttribute()
    {
        if ($this->pembahasan_image) {
            return asset('storage/' . $this->pembahasan_image);
        }
        return null;
    }
}
