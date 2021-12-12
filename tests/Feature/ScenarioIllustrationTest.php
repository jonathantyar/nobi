<?php

use App\Models\User;
use App\Models\InvestmentProduct;

use function Pest\Laravel\post;
use function Pest\Laravel\get;

test('Scenario test from example docs', function () {
    // Tanggal 1 angka NAB adalah 1.40000
    $investment = InvestmentProduct::factory()->create([
        'nab' => 1.4000
    ]);

    /** @var arman user yang penyetoran balance sebesar 50000 */
    $arman = User::factory()->create();

    $response = post(route('investment.topup', ['investment_product' => $investment->code]), [
        'user_id' => $arman->id,
        'amount_rupiah' => 5000
    ]);
    $response->assertStatus(200);
    expect($response->json())->toMatchArray([
        'data' => [
            'nilai_unit_hasil_topup' => 3571.4285,
            'nilai_unit_total' => 3571.4285,
            'saldo_rupiah_total' => 5000 - 0.01 // Round down?
        ]
    ]);

    // Tanggal 2 angka NAB adalah 2.2250
    InvestmentProduct::find($investment->id)->update(['nab' => 2.2250]);

    /** @var budi user yang penyetoran balance sebesar 15000 */
    $budi = User::factory()->create();

    $response = post(route('investment.topup', ['investment_product' => $investment->code]), [
        'user_id' => $budi->id,
        'amount_rupiah' => 15000
    ]);
    $response->assertStatus(200);
    expect($response->json())->toMatchArray([
        'data' => [
            'nilai_unit_hasil_topup' => 6741.573,
            'nilai_unit_total' => 6741.573,
            'saldo_rupiah_total' => 15000 - 0.01 // Round down?
        ]
    ]);

    // Tanggal 3 angka NAB adalah 2.4000
    InvestmentProduct::find($investment->id)->update(['nab' => 2.4000]);

    $response = post(route('investment.withdraw', ['investment_product' => $investment->code]), [
        'user_id' => $arman->id,
        'amount_rupiah' => 6000
    ]);
    $response->assertStatus(200);
    expect($response->json())->toMatchArray([
        'data' => [
            'nilai_unit_setelah_withdraw' => 2500,
            'nilai_unit_total' => 1071.4285,
            'saldo_rupiah_total' => 2571.42
        ]
    ]);
});
