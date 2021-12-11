<?php

use App\Models\InvestmentProduct;
use function Pest\Laravel\post;

/**
 * Test user add request incomplete.
 *
 * @return void
 */
test('Request given is incomplete', function () {
    $user = User::factory()->definition();

    $response = post('api/v1/user/add', []);
    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['username', 'name']);

    $response = $this->post('api/v1/user/add', [
        'username' => $user['username']
    ]);
    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['name']);
});
