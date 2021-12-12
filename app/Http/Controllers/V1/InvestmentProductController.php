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

use App\Http\Resources\MemberResource;

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

        return ResponseWrapper::success('Successfully updated the total balance', [
            'nab' => $investmentProduct->nab
        ]);
    }

    /**
     * List of NAB
     *
     * This endpoint allows you to list all the recent histories of NAB.
     *
     * @response scenario="Success"{
     * [
     *  "nab": 2.4243,
     *  "datetime": "2013-02-01 00:00:00"
     * ]
     * }
     */
    public function listNAB(InvestmentProduct $investmentProduct)
    {
        return ResponseWrapper::success('Successfully get list of NAB histories', $investmentProduct->histories);
    }

    /**
     * List of Member
     *
     * This endpoint allows you to list all member on these product investment.
     *
     * @response scenario="Success"{
     * [
     *  "nab": 2.4243,
     *  "members": [
     *      "userid": 2924,
     *      "total_unit_per_uid": 124234.3242,
     *      "total_amountrupiah_per_uid": 25000.25
     * ]
     * ]
     * }
     */
    public function member(InvestmentProduct $investmentProduct, Request $request)
    {
        $userId = $request->input('userid', false);
        $limit  = $request->input('limit', 20);

        $members = $investmentProduct->users()->orderBy('users.id');
        $members = $userId ? $members->where('users.id', $userId) : $members;
        $members = $members->paginate($limit);

        $request->nab = $investmentProduct->nab;

        return ResponseWrapper::success('List investment member', [
            'nab'     => $request->nab,
            'members' => MemberResource::collection($members->items())
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

        return ResponseWrapper::success('Penambahan berhasil dilakukan', [
            'nilai_unit_hasil_topup' => $nilaiUnitTopup,
            'nilai_unit_total' => Calculate::roundDown($investment->pivot->unit, 4),
            'saldo_rupiah_total' => Calculate::roundDown($investmentProduct->nab * $investment->pivot->unit, 2)
        ]);
    }

    /**
     * Withdraw
     *
     * This endpoint allows you to withdraw to the investment product.
     *
     * @bodyParam user_id numeric required user id.
     * @bodyParam amount_rupiah numeric required the amount user want to top up.
     *
     * @response scenario="Success"{
     *  "nilai_unit_setelah_withdraw": 1.2452,
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
            'nilai_unit_total' => Calculate::roundDown($investment->pivot->unit, 4),
            'saldo_rupiah_total' => Calculate::roundDown($investmentProduct->nab * $investment->pivot->unit, 2)
        ]);
    }
}
