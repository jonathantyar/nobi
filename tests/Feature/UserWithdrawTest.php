<?php

use App\Models\InvestmentProduct;
use App\Models\User;

use function Pest\Laravel\post;

/**
 * Test if investment product code does not exists.
 *
 * @return void
 */
test('Investment product code does not exists', function () {
    $investment = InvestmentProduct::factory()->definition();
    $response = post(route('investment.withdraw', ['investment_product' => $investment['code']]), []);
    $response->assertStatus(404);
});

/**
 * Test request are incomplete.
 *
 * @return void
 */
test('Request given is incomplete or not valid', function () {
    $investment = InvestmentProduct::factory()->create();
    $response = post(route('investment.withdraw', ['investment_product' => $investment->code]), []);
    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['user_id', 'amount_rupiah']);

    $response = post(route('investment.withdraw', ['investment_product' => $investment->code]), [
        'user_id' => 'string',
        'amount_rupiah' => 15000.05
    ]);
    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['user_id']);
});

/**
 * Test users withdraw below the unit he had.
 *
 * @return void
 */
test('User withdraw below the unit he had', function () {
    $user = User::factory()->create();
    $investment = InvestmentProduct::factory()->create();

    $response = post(route('investment.topup', ['investment_product' => $investment->code]), [
        'user_id' => $user->id,
        'amount_rupiah' => 15000.05
    ]);
    $response->assertStatus(200);
    expect($response->json())->toMatchArray([
        'data' => [
            'nilai_unit_hasil_topup' => 15000.05,
            'nilai_unit_total' => 15000.05,
            'saldo_rupiah_total' => 15000.05
        ]
    ]);

    /**
     * 20000.25 / 15000.05 = 1.9047
     */
    $response = post(route('investment.update.balance', ['investment_product' => $investment->code]), [
        'current_balance' => 20000.25
    ]);
    $response->assertStatus(200);
    expect($response->json())->toMatchArray([
        'data' => [
            'nab' => 1.3333
        ]
    ]);

    $response = post(route('investment.withdraw', ['investment_product' => $investment->code]), [
        'user_id' => $user->id,
        'amount_rupiah' => 15000.05
    ]);
    $response->assertStatus(200);
    expect($response->json())->toMatchArray([
        'data' => [
            'nilai_unit_setelah_withdraw' => 11250.3187,
            'nilai_unit_total' => 3749.7313,
            'saldo_rupiah_total' => 4999.51
        ]
    ]);
});

/**
 * Test users cannot withdraw since try withdraw more than the unit he had.
 *
 * @return void
 */
test('User cannot withdraw more than the unit he had', function () {
    $user = User::factory()->create();
    $investment = InvestmentProduct::factory()->create();

    $response = post(route('investment.topup', ['investment_product' => $investment->code]), [
        'user_id' => $user->id,
        'amount_rupiah' => 15000.05
    ]);
    $response->assertStatus(200);
    expect($response->json())->toMatchArray([
        'data' => [
            'nilai_unit_hasil_topup' => 15000.05,
            'nilai_unit_total' => 15000.05,
            'saldo_rupiah_total' => 15000.05
        ]
    ]);

    /**
     * 20000.25 / 15000.05 = 1.9047
     */
    $response = post(route('investment.update.balance', ['investment_product' => $investment->code]), [
        'current_balance' => 20000.25
    ]);
    $response->assertStatus(200);
    expect($response->json())->toMatchArray([
        'data' => [
            'nab' => 1.3333
        ]
    ]);

    $response = post(route('investment.withdraw', ['investment_product' => $investment->code]), [
        'user_id' => $user->id,
        'amount_rupiah' => 50000.15
    ]);
    $response->assertStatus(200);
    expect($response->json())->toMatchArray([
        'success' => false
    ]);

    $investmentUserNotHad = InvestmentProduct::factory()->create();

    $response = post(route('investment.withdraw', ['investment_product' => $investmentUserNotHad->code]), [
        'user_id' => $user->id,
        'amount_rupiah' => 50000.15
    ]);
    $response->assertStatus(200);
    expect($response->json())->toMatchArray([
        'success' => false
    ]);
});
