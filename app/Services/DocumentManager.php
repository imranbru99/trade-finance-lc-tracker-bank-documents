<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Document;
use App\Models\DocumentVersion;
use App\Models\LcDocumentRequirement;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentManager
{
    /**
     * Standard trade finance document types
     */
    public const DEFAULT_DOCUMENT_TYPES = [
        'commercial_invoice' => 'Commercial Invoice',
        'packing_list' => 'Detailed Packing List',
        'bill_of_lading' => 'Clean on Board Ocean Bill of Lading',
        'airway_bill' => 'Airway Bill',
        'certificate_of_origin' => 'Certificate of Origin',
        'insurance_certificate' => 'Marine Insurance Certificate / Policy',
        'inspection_certificate' => 'Pre-shipment Inspection Certificate',
        'beneficiary_certificate' => 'Beneficiary Certificate',
        'quality_certificate' => 'Quality & Weight Certificate',
        'bank_advice' => 'Bank LC Advice / Copy',
        'amendment_copy' => 'LC Amendment Copy',
        'payment_advice' => 'Settlement & Payment Advice',
    ];

    /**
     * Auto-generate document checklist requirements for an LC
     */
    public function generateChecklistForLc($lc, array $selectedTypes = []): void
    {
        $typesToCreate = empty($selectedTypes)
            ? ['commercial_invoice', 'packing_list', 'bill_of_lading', 'certificate_of_origin', 'insurance_certificate']
            : $selectedTypes;

        foreach ($typesToCreate as $typeKey) {
            $name = self::DEFAULT_DOCUMENT_TYPES[$typeKey] ?? Str::headline($typeKey);

            LcDocumentRequirement::firstOrCreate(
                [
                    'organization_id' => $lc->organization_id,
                    'trackable_type' => get_class($lc),
                    'trackable_id' => $lc->id,
                    'document_type' => $typeKey,
                ],
                [
                    'document_name' => $name,
                    'copies_original' => 1,
                    'copies_duplicate' => 2,
                    'is_mandatory' => true,
                    'status' => 'Pending',
                ]
            );
        }
    }

    /**
     * Store a new document privately
     */
    public function storeDocument(UploadedFile $file, array $attributes, ?User $user = null): Document
    {
        $orgId = $attributes['organization_id'] ?? (auth()->check() ? auth()->user()->current_organization_id : 1);
        $folder = "organizations/{$orgId}/documents/" . date('Y/m');
        
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs($folder, $filename, 'local');

        $document = Document::create(array_merge($attributes, [
            'original_filename' => $file->getClientOriginalName(),
            'stored_path' => $path,
            'disk' => 'local',
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'version' => 1,
            'is_latest' => true,
            'verification_status' => 'Pending',
            'uploaded_by_user_id' => $user?->id ?? auth()->id(),
        ]));

        // Create initial version record
        DocumentVersion::create([
            'document_id' => $document->id,
            'version_number' => 1,
            'original_filename' => $document->original_filename,
            'stored_path' => $document->stored_path,
            'file_size' => $document->file_size,
            'change_notes' => 'Initial upload',
            'uploaded_by_user_id' => $document->uploaded_by_user_id,
        ]);

        // Audit Log
        AuditLog::create([
            'organization_id' => $orgId,
            'user_id' => $user?->id ?? auth()->id(),
            'event' => 'document.uploaded',
            'auditable_type' => Document::class,
            'auditable_id' => $document->id,
            'new_values' => ['title' => $document->title, 'filename' => $document->original_filename],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return $document;
    }

    /**
     * Replace or upload new version of document
     */
    public function uploadNewVersion(Document $document, UploadedFile $file, string $changeNotes = '', ?User $user = null): DocumentVersion
    {
        $newVersionNumber = $document->version + 1;
        $orgId = $document->organization_id;
        $folder = "organizations/{$orgId}/documents/" . date('Y/m');

        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs($folder, $filename, 'local');

        $version = DocumentVersion::create([
            'document_id' => $document->id,
            'version_number' => $newVersionNumber,
            'original_filename' => $file->getClientOriginalName(),
            'stored_path' => $path,
            'file_size' => $file->getSize(),
            'change_notes' => $changeNotes,
            'uploaded_by_user_id' => $user?->id ?? auth()->id(),
        ]);

        $document->update([
            'version' => $newVersionNumber,
            'stored_path' => $path,
            'original_filename' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'verification_status' => 'Pending',
        ]);

        return $version;
    }

    /**
     * Verify document compliance status
     */
    public function verifyDocument(Document $document, string $status, User $verifier, ?string $notes = null): void
    {
        $oldStatus = $document->verification_status;
        $document->update([
            'verification_status' => $status,
            'verified_by_user_id' => $verifier->id,
            'verified_at' => now(),
            'notes' => $notes ?: $document->notes,
        ]);

        AuditLog::create([
            'organization_id' => $document->organization_id,
            'user_id' => $verifier->id,
            'event' => 'document.verified',
            'auditable_type' => Document::class,
            'auditable_id' => $document->id,
            'old_values' => ['status' => $oldStatus],
            'new_values' => ['status' => $status, 'notes' => $notes],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
