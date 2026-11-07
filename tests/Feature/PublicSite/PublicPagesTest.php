<?php

namespace Tests\Feature\PublicSite;

use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    public function test_marketing_pages_are_reachable(): void
    {
        $paths = [
            '/',
            '/pricing',
            '/features',
            '/demo',
            '/security',
            '/faq',
            '/contact',
            '/features/import-lc',
        ];

        foreach ($paths as $path) {
            $this->get($path)->assertOk();
        }
    }

    public function test_contact_form_submission_redirects_with_success(): void
    {
        $response = $this->post('/contact', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'subject' => 'Demo inquiry',
            'message' => 'Interested in a product demo for our trading desk.',
        ]);

        $response->assertRedirect(route('public.contact'));
        $response->assertSessionHas('contact_success', true);
    }
}
