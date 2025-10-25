<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $tables = 'subscriptions';

    protected $fillable = [
        'user_id',
        'start_date',
        'end_date',
        'amount_paid',
        'payment_status',
        'payment_method',
        'payment_details',
        'transaction_id'
    ];

    protected $dates = [
        'start_date',
        'end_date',
        'payment_details' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
