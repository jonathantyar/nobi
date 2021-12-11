<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\InvestmentProduct;

use App\StorableEvents\UpdateTotalBalance;

class InvestmentProductController extends Controller
{
    public function updateTotalBalance(InvestmentProduct $investmentProduct, Request $request)
    {
        $request->validate([
            'current_balance' => 'required|numeric|min:0'
        ]);

        event(new UpdateTotalBalance($investmentProduct->id, $request->current_balance, now()->timestamp));

        return response()->json([
            'nab' => $investmentProduct->nab
        ]);
    }
}
