<?php

namespace Database\Seeders;

use App\Models\DocumentCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DocumentCategorySeeder extends Seeder
{
    public const CATEGORIES = [
        'Commercial Invoice',
        'Proforma Invoice',
        'Packing List',
        'Bill of Lading',
        'Airway Bill',
        'Certificate of Origin',
        'Insurance Certificate',
        'Inspection Certificate',
        'Beneficiary Certificate',
        'Weight Certificate',
        'Quality Certificate',
        'Customs Documents',
        'Shipping Documents',
        'Bank Advice',
        'LC Copy',
        'Amendment Copy',
        'Payment Advice',
        'Bank Correspondence',
        'Other Supporting Documents',
    ];

    public function run(): void
    {
        foreach (self::CATEGORIES as $name) {
            DocumentCategory::updateOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name]
            );
        }
    }
}
