<?php

test('root redirects to admin panel', function () {
    $response = $this->get('/');

    $response->assertRedirect('/admin');
});
