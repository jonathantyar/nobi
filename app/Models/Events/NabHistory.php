<?php

namespace App\Models\Events;

use Illuminate\Database\Eloquent\Model;

class NabHistory extends Model
{
    public $timestamps = false;
    protected $table = 'event_nab_histories';
    protected $fillable = [
        'investment_product_id',
        'datetime',
        'nab',
    ];
}
