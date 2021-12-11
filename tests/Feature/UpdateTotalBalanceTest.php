<?php

use App\Models\InvestmentProduct;
use function Pest\Laravel\post;

/**
 * Test if investment product code does not exists.
 *
 * @return void
 */
test('Investment product code does not exists', function () {
    $investment = InvestmentProduct::factory()->definition();
    $response = post(route('investment.update.balance', ['product.code' => $investment['code']]), []);
    $response->assertStatus(404);
});

/**
 * Test request are incomplete.
 *
 * @return void
 */
test('Request given is incomplete and not valid', function () {
    $investment = InvestmentProduct::factory()->create();
    $response = post(route('investment.update.balance', ['product.code' => $investment->code]), []);
    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['current_balance']);

    $response = post(route('investment.update.balance', ['product.code' => $investment->code]), [
        'current_balance' => 'string'
    ]);
    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['current_balance']);
});
