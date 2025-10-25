<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class UserTryoutSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tryout_id',
        'started_at',
        'finished_at',
        'shuffle_seed',
        'status',
        'tkp_final_score',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'tkp_final_score' => 'decimal:2'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tryout()
    {
        return $this->belongsTo(Tryout::class);
    }

    public function getElapsedMinutesAttribute()
    {
        if (!$this->started_at) {
            return 0;
        }

        $endTime = $this->finished_at ?: now();
        return $this->started_at->diffInMinutes($endTime);
    }

    public function getRemainingMinutesAttribute()
    {
        if (!$this->started_at || !$this->tryout) {
            return 0;
        }

        $elapsedMinutes = $this->started_at->diffInMinutes(now());
        return max(0, $this->tryout->durasi_menit - $elapsedMinutes);
    }

    public function getRemainingSecondsAttribute()
    {
        return $this->remaining_minutes * 60;
    }

    public function isTimeUp()
    {
        return $this->remaining_minutes <= 0;
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}
