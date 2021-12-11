<?php

namespace App\Models\Events;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    public $timestamps = false;
    protected $table = 'event_transactions';
    protected $fillable = [
        'user_id',
        'investment_product_id',
        'type',
        'nab',
        'unit',
        'total_unit',
        'amount',
        'datetime',
    ];
}
