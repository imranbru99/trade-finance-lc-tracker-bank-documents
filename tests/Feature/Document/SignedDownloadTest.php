<?php

namespace Tests\Feature\Document;

use App\Models\Document;
use App\Models\Organization;
use Tests\TestCase;

class SignedDownloadTest extends TestCase
{
    public function test_unsigned_download_url_is_forbidden(): void
    {
        $organization = Organization::factory()->create();

        $document = Document::withoutGlobalScopes()->create([
            'organization_id' => $organization->id,
            'title' => 'Test BL',
            'original_filename' => 'bl.pdf',
            'stored_path' => 'private/test/bl.pdf',
            'disk' => 'local',
        ]);

        $this->get(route('documents.download.signed', ['document' => $document->id]))
            ->assertForbidden();
    }

    public function test_signed_download_url_is_not_rejected_for_invalid_signature_when_file_missing(): void
    {
        $organization = Organization::factory()->create();

        $document = Document::withoutGlobalScopes()->create([
            'organization_id' => $organization->id,
            'title' => 'Test Invoice',
            'original_filename' => 'invoice.pdf',
            'stored_path' => 'private/missing/invoice.pdf',
            'disk' => 'local',
        ]);

        $signedUrl = $document->getSignedDownloadUrl();

        $this->get($signedUrl)
            ->assertNotFound();
    }
}
