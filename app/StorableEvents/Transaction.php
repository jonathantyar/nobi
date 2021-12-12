<?php

namespace App\StorableEvents;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class Transaction extends ShouldBeStored
{
    public $user_id;
    public $investment_product_id;
    public $type;
    public $nab;
    public $unit;
    public $amount;
    public $timestamp;

    public function __construct(Int $user_id, Int $investment_product_id, String $type, Float $nab, Float $unit,  Float $amount, Int $timestamp)
    {
        $this->user_id = $user_id;
        $this->investment_product_id = $investment_product_id;
        $this->type = $type;
        $this->nab = $nab;
        $this->unit = $unit;
        $this->amount = $amount;
        $this->timestamp = $timestamp;
    }
}
