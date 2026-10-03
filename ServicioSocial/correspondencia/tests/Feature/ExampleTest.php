<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_the_application_health_check_returns_ok(): void
    {
        $response = $this->get('/up');
        $response->assertStatus(200);
    }
}
