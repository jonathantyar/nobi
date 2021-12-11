<?php

namespace App\Projectors;

use Spatie\EventSourcing\EventHandlers\Projectors\Projector;
use Carbon\Carbon;

use App\StorableEvents\UpdateTotalBalance;

use App\Models\InvestmentProduct;
use App\Models\Events\NabHistory;

class UpdateTotalBalanceProjector extends Projector
{
    public function onUpdateTotalBalance(UpdateTotalBalance $event)
    {
        $nab = InvestmentProduct::find($event->investment_product_id)->currentNab($event->current_balance);

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
