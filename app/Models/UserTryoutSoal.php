<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserTryoutSoal extends Model
{
    protected $table = 'user_tryout_soal';

    protected $fillable = [
        'user_id',
        'tryout_id',
        'user_tryout_session_id',
        'soal_id',
        'level',
        'urutan',
        'jawaban_user',
        'skor',
        'waktu_jawab',
        'shuffle_seed',
        'sudah_dijawab',
        'is_marked'
    ];

    protected $casts = [
        'jawaban_user' => 'array',
        'skor' => 'decimal:2',
        'sudah_dijawab' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tryout()
    {
        return $this->belongsTo(Tryout::class);
    }

    public function session()
    {
        return $this->belongsTo(UserTryoutSession::class, 'user_tryout_session_id');
    }

    public function soal()
    {
        return $this->belongsTo(Soal::class, 'soal_id');
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByTryout($query, $tryoutId)
    {
        return $query->where('tryout_id', $tryoutId);
    }

    public function scopeSudahDijawab($query)
    {
        return $query->where('sudah_dijawab', true);
    }

    public function scopeBelumDijawab($query)
    {
        return $query->where('sudah_dijawab', false);
    }
}
