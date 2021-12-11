<?php

namespace App\Projectors;

use Spatie\EventSourcing\EventHandlers\Projectors\Projector;
use Carbon\Carbon;
use Calculate;

use App\StorableEvents\UpdateTotalBalance;

use App\Models\InvestmentProduct;
use App\Models\Events\Transaction;
use App\Models\Events\NabHistory;

class UpdateTotalBalanceProjector extends Projector
{
    public function onUpdateTotalBalance(UpdateTotalBalance $event)
    {
        $nab = 1;

        $transactions = Transaction::where('investment_product_id', $event->investment_product_id)->get('unit');
        if ($transactions->count()) {
            $nab = Calculate::roundDown($event->balance / $transactions->sum('unit'), 4);
        }

        InvestmentProduct::find($event->investment_product_id)->update([
            'nab' => $nab
        ]);

        NabHistory::create([
            'investment_product_id' => $event->investment_product_id,
            'datetime' => Carbon::createFromTimestamp($event->timestamp),
            'nab' => $nab
        ]);
    }
}
