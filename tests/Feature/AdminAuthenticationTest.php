<?php

use App\Models\User;

test('admin panel redirects unauthenticated users to login', function () {
    $response = $this->get('/admin');

    $response->assertRedirect('/admin/login');
});

test('admin panel allows authenticated users', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $response = $this->actingAs($user)->get('/admin');

    $response->assertStatus(200);
});

test('admin login page is accessible', function () {
    $response = $this->get('/admin/login');

    $response->assertStatus(200);
});

test('admin registration page is accessible', function () {
    $response = $this->get('/admin/register');

    $response->assertStatus(200);
});

test('admin password reset page is accessible', function () {
    $response = $this->get('/admin/password-reset/request');

    $response->assertStatus(200);
});
