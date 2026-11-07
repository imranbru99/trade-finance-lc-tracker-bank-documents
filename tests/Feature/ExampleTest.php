<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_home_page_returns_success(): void
    {
        $this->get('/')->assertOk();
    }
}
