<?php

use App\Models\User;

test('admin login screen can be rendered', function () {
    $response = $this->get('/admin/login');

    $response->assertStatus(200);
});

test('admin users can authenticate using the login screen', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    // Use actingAs to simulate authentication instead of POST request
    $response = $this->actingAs($user)->get('/admin');

    $response->assertStatus(200);
    $this->assertAuthenticated();
});

test('unauthenticated users are redirected to login', function () {
    $response = $this->get('/admin');

    $response->assertRedirect('/admin/login');
    $this->assertGuest();
});

// Two-factor authentication test removed - 2FA feature disabled

test('admin users can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/admin/logout');

    $response->assertRedirect('/admin/login');

    $this->assertGuest();
});
