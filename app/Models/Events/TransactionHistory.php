<?php

namespace App\Models\Events;

use Illuminate\Database\Eloquent\Model;

class TransactionHistory extends Model
{
    public $timestamps = false;
    protected $table = 'event_transaction_histories';
    protected $fillable = [
        'user_id',
        'investment_product_id',
        'type',
        'nab',
        'unit',
        'total_unit',
        'amount',
        'balance',
        'datetime',
    ];
}
