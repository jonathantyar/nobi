<?php

use App\Models\InvestmentProduct;

use function Pest\Laravel\get;

/**
 * Test if investment product code does not exists.
 *
 * @return void
 */
test('Get List of Investment product code does not exists', function () {
    $investment = InvestmentProduct::factory()->definition();
    $response = get(route('investment.list.nab', ['investment_product' => $investment['code']]), []);
    $response->assertStatus(404);
});

/**
 * Test if investment product code does not exists.
 *
 * @return void
 */
test('Get List investment product', function () {
    $investment = InvestmentProduct::factory()->create();
    $response = get(route('investment.list.nab', ['investment_product' => $investment->code]), []);
    $response->assertStatus(200);
});
