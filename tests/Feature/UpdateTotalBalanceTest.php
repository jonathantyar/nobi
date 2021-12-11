<?php

use App\Models\User;
use App\Models\InvestmentProduct;

use function Pest\Laravel\post;
use function Pest\Laravel\get;

/**
 * Test if investment product code does not exists.
 *
 * @return void
 */
test('Investment product code does not exists', function () {
    $investment = InvestmentProduct::factory()->definition();
    $response = post(route('investment.update.balance', ['investment_product' => $investment['code']]), []);
    $response->assertStatus(404);
});

/**
 * Test request are incomplete.
 *
 * @return void
 */
test('Request given is incomplete or not valid', function () {
    $investment = InvestmentProduct::factory()->create();
    $response = post(route('investment.update.balance', ['investment_product' => $investment->code]), []);
    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['current_balance']);

    $response = post(route('investment.update.balance', ['investment_product' => $investment->code]), [
        'current_balance' => 'string'
    ]);
    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['current_balance']);
});

/**
 * Test update total balance when unity zero.
 *
 * @return void
 */
test('Update total balance when unit zero', function () {
    $investment = InvestmentProduct::factory()->create();

    $response = post(route('investment.update.balance', ['investment_product' => $investment->code]), [
        'current_balance' => 100000.05
    ]);
    $response->assertStatus(200);
    expect($response->json())->toMatchArray([
        'nab' => 1
    ]);
});

/**
 * Test update total balance when there already units in product investment.
 *
 * @return void
 */
test('Update total balance when unit not zero', function () {
    $user = User::factory()->create();
    $investment = InvestmentProduct::factory()->hasAttached(
        $user,
        ['unit' => 1500.0125]
    )->create();

    /**
     * 10000 / 1500.0125 = 6.6666
     */
    $response = post(route('investment.update.balance', ['investment_product' => $investment->code]), [
        'current_balance' => 10000.05
    ]);
    $response->assertStatus(200);
    expect($response->json())->toMatchArray([
        'nab' => 6.6666
    ]);
});

/**
 * Test round down nab on calculate.
 *
 * @return void
 */
test('Update total balance when unit not zero check round down', function () {
    $user = User::factory()->create();
    $investment = InvestmentProduct::factory()->hasAttached(
        $user,
        ['unit' => 5250.0125]
    )->create();

    /**
     * 10000.05 / 5250.0125 = 1.9047
     */
    $response = post(route('investment.update.balance', ['investment_product' => $investment->code]), [
        'current_balance' => 10000.05
    ]);
    $response->assertStatus(200);
    expect($response->json())->toMatchArray([
        'nab' => 1.9047
    ]);
});

/**
 * Test get the lastest nab from list.
 *
 * @return void
 */
test('Update total balance and list get the lastest', function () {
    $user = User::factory()->create();
    $investment = InvestmentProduct::factory()->hasAttached(
        $user,
        ['unit' => 5250.0125]
    )->create();

    /**
     * 12000.05 / 5250.0125 = 2.2857
     */
    $response = post(route('investment.update.balance', ['investment_product' => $investment->code]), [
        'current_balance' => 12000.05
    ]);
    $response->assertStatus(200);
    expect($response->json())->toMatchArray([
        'nab' => 2.2857
    ]);

    /**
     * 10000.05 / 5250.0125 = 1.9047
     */
    $response = post(route('investment.update.balance', ['investment_product' => $investment->code]), [
        'current_balance' => 10000.05
    ]);
    $response->assertStatus(200);
    expect($response->json())->toMatchArray([
        'nab' => 1.9047
    ]);


    $response = get(route('investment.list.nab', ['investment_product' => $investment->code]), []);
    $response->assertStatus(200);
    expect($response->json()[0])->toMatchArray([
        'nab' => 1.9047
    ]);
});
