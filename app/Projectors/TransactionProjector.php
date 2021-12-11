<?php

namespace App\Projectors;

use Spatie\EventSourcing\EventHandlers\Projectors\Projector;
use Carbon\Carbon;

use App\StorableEvents\Transaction;

use App\Models\User;
use App\Models\Events\TransactionHistory;

class TransactionProjector extends Projector
{
    public function onTransactionStart(Transaction $event)
    {
        $user = User::find($event->user_id);

        $invest = $user->investmentOnProduct($event->investment_product_id);
        if ($invest) {
            switch ($event->type) {
                case 'debit':
                    $event->total_unit = $invest->pivot->unit + $event->unit;
                    break;

                case 'credit':
                    $event->total_unit = $invest->pivot->unit - $event->unit;
                    break;
            }

            $user->investments()->sync([$event->investment_product_id => ['unit' => $event->total_unit]]);
        } else {
            $user->investments()->attach([$event->investment_product_id => ['unit' => $event->unit]]);
            $event->total_unit = $event->unit;
        }

        $event->balance = $event->nab * $event->total_unit;

        TransactionHistory::create([
            'user_id' => $event->user_id,
            'investment_product_id' => $event->investment_product_id,
            'type' => $event->type,
            'nab' => $event->nab,
            'unit' => $event->unit,
            'total_unit' => $event->total_unit,
            'amount' => $event->amount,
            'balance' => $event->balance,
            'datetime' => Carbon::createFromTimestamp($event->timestamp),
        ]);
    }
}
