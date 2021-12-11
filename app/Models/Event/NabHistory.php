<?php

namespace App\Models\Event;

use Illuminate\Database\Eloquent\Model;

class NabHistory extends Model
{
    protected $table = 'event_nab_histories';
    protected $fillable = ['investment_products_id', 'datetime', 'nab'];
}