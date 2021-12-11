<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\InvestmentProduct;

use App\StorableEvents\UpdateTotalBalance;

class InvestmentProductController extends Controller
{
    /**
     * Update Total Balance
     *
     * This endpoint allows you to update total balance of an investment product.
     *
     * @bodyParam current_balance numeric required The current balance of the product.
     *
     * @response scenario="Success"{
     *  "nab": 1.2452,
     * }
     */
    public function updateTotalBalance(InvestmentProduct $investmentProduct, Request $request)
    {
        $request->validate([
            'current_balance' => 'required|numeric|min:0'
        ]);

        event(new UpdateTotalBalance($investmentProduct->id, $request->current_balance, now()->timestamp));

        $investmentProduct->refresh();

        return response()->json([
            'nab' => $investmentProduct->nab
        ]);
    }

    /**
     * List of NAB
     *
     * This endpoint allows you to update total balance of an investment product.
     *
     * @response scenario="Success"{
     *  "nab": 1.2452,
     * }
     */
    public function listNAB(InvestmentProduct $investmentProduct)
    {
        return response()->json($investmentProduct->histories);
    }
}
