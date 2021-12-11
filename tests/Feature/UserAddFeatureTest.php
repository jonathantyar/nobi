<?php

namespace Tests\Feature;

use App\Models\User;
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

/**
 * Test user username already taken.
 *
 * @return void
 */
test('Username already taken handler', function () {
    $user = User::factory()->create();

    $response = $this->post('api/v1/user/add', [
        'username' => $user->username,
        'name' => $user->name
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['username']);
});

/**
 * Test user username already taken.
 *
 * @return void
 */
test('User created', function () {
    $user = User::factory()->definition();

    $response = post('api/v1/user/add', [
        'username' => $user['username'],
        'name' => $user['name']
    ]);

    $response->assertStatus(200);
    expect($response->json())->toHaveKey('id_user');
});
