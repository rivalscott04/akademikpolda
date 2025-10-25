<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TryoutBlueprint extends Model
{
    protected $table = 'tryout_blueprints';

    protected $fillable = [
        'tryout_id',
        'kategori_id',
        'level',
        'jumlah'
    ];

    public function tryout()
    {
        return $this->belongsTo(Tryout::class);
    }

    public function kategori()
    {
        return $this->belongsTo(KategoriSoal::class, 'kategori_id');
    }
}


