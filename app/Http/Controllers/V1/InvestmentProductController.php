<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Calculate;
use ResponseWrapper;

use App\Models\InvestmentProduct;
use App\Models\User;

use App\StorableEvents\Transaction;
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
            'current_balance' => 'required|numeric|min:1'
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

    /**
     * Top Up
     *
     * This endpoint allows you to topup to the investment product.
     *
     * @bodyParam user_id numeric required user id.
     * @bodyParam amount_rupiah numeric required the amount user want to top up.
     *
     * @response scenario="Success"{
     *  "nilai_unit_hasil_topup": 1.2452,
     *  "nilai_unit_total": 1.2452,
     *  "saldo_rupiah_total": 129455.25,
     * }
     */
    public function topup(InvestmentProduct $investmentProduct, Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount_rupiah' => 'required|numeric|min:1',
        ]);

        $nilaiUnitTopup = Calculate::roundDown($request->amount_rupiah / $investmentProduct->nab, 4);

        event(new Transaction(
            $request->user_id,
            $investmentProduct->id,
            'topup',
            $investmentProduct->nab,
            $nilaiUnitTopup,
            $request->amount_rupiah,
            now()->timestamp
        ));

        $user = User::find($request->user_id);
        $investment = $user->investmentOnProduct($investmentProduct->id);

        return response()->json([
            'nilai_unit_hasil_topup' => $nilaiUnitTopup,
            'nilai_unit_total' => $investment->pivot->unit,
            'saldo_rupiah_total' => Calculate::roundDown($investmentProduct->nab * $investment->pivot->unit, 2)
        ]);
    }

    /**
     * Top Up
     *
     * This endpoint allows you to topup to the investment product.
     *
     * @bodyParam user_id numeric required user id.
     * @bodyParam amount_rupiah numeric required the amount user want to top up.
     *
     * @response scenario="Success"{
     *  "nilai_unit_hasil_topup": 1.2452,
     *  "nilai_unit_total": 1.2452,
     *  "saldo_rupiah_total": 129455.25,
     * }
     */
    public function withdraw(InvestmentProduct $investmentProduct, Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount_rupiah' => 'required|numeric|min:1',
        ]);

        $user = User::find($request->user_id);
        $investment = $user->investmentOnProduct($investmentProduct->id);
        $nilaiUnitTopup = Calculate::roundDown($request->amount_rupiah / $investmentProduct->nab, 4);

        if (!$investment || $investment && $investment->pivot->unit < $nilaiUnitTopup) {
            return ResponseWrapper::success('Nilai investasi tidak cukup untuk melakukan penarikan', [], false);
        }

        event(new Transaction(
            $request->user_id,
            $investmentProduct->id,
            'withdraw',
            $investmentProduct->nab,
            $nilaiUnitTopup,
            $request->amount_rupiah,
            now()->timestamp
        ));

        $investment = $user->investmentOnProduct($investmentProduct->id);

        return ResponseWrapper::success('Penarikan berhasil dilakukan', [
            'nilai_unit_setelah_withdraw' => $nilaiUnitTopup,
            'nilai_unit_total' => $investment->pivot->unit,
            'saldo_rupiah_total' => Calculate::roundDown($investmentProduct->nab * $investment->pivot->unit, 2)
        ]);
    }
}
