<?php

namespace App\StorableEvents;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class UpdateTotalBalance extends ShouldBeStored
{
    public $investment_product_id;
    public $current_balance;
    public $timestamp;

    public function __construct(Int $investment_product_id, Int $current_balance, Int $timestamp)
    {
        $this->investment_product_id = $investment_product_id;
        $this->current_balance = $current_balance;
        $this->timestamp = $timestamp;
    }
}
