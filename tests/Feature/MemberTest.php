<?php

use App\Models\InvestmentProduct;
use App\Models\User;

use function Pest\Laravel\get;

/**
 * Test if investment product code does not exists.
 *
 * @return void
 */
test('Get List of Investment product code does not exists', function () {
    $investment = InvestmentProduct::factory()->definition();
    $response = get(route('investment.member', ['investment_product' => $investment['code']]), []);
    $response->assertStatus(404);
});

/**
 * Test if investment product code does not exists.
 *
 * @return void
 */
test('Get List investment product with parameters', function () {
    $users = User::factory(25)->create();
    $investment = InvestmentProduct::factory()->hasAttached(
        $users,
        ['unit' => 5250.0125]
    )->create();

    $response = get(route('investment.member', ['investment_product' => $investment->code]), []);
    $response->assertStatus(200);
    expect($response->json()['data']['members'])->toHaveCount(20);

    $response = get(route('investment.member', ['investment_product' => $investment->code, 'page' => 2]), []);
    $response->assertStatus(200);
    expect($response->json()['data']['members'])->toHaveCount(5);

    $response = get(route('investment.member', ['investment_product' => $investment->code, 'limit' => 10]), []);
    $response->assertStatus(200);
    expect($response->json()['data']['members'])->toHaveCount(10);

    $response = get(route('investment.member', ['investment_product' => $investment->code, 'userid' => $users->first()->id]), []);
    $response->assertStatus(200);
    expect($response->json()['data']['members'])->toHaveCount(1);
});
