<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessTier extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'name',
        'description',
    ];

    public function packages()
    {
        return $this->hasMany(Package::class);
    }
}


