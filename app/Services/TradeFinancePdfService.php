<?php

namespace App\Services;

use App\Models\Discrepancy;
use App\Models\ImportLc;
use App\Models\Organization;
use App\Models\PaymentSchedule;
use ImranDev\UnicodePdf\Facades\UnicodePdf;
use Symfony\Component\HttpFoundation\Response;

class TradeFinancePdfService
{
    /**
     * Generate an official LC Summary PDF in Unicode
     */
    public function generateLcSummaryPdf(ImportLc $lc, string $locale = 'en'): Response
    {
        $view = 'pdf.lc_summary';
        $data = [
            'lc' => $lc,
            'organization' => $lc->organization,
            'locale' => $locale,
            'generated_at' => now()->toFormattedDateString(),
        ];

        return UnicodePdf::loadView($view, $data)
            ->a4()
            ->title("LC Summary - {$lc->lc_number}")
            ->download("LC-Summary-{$lc->lc_number}.pdf");
    }

    /**
     * Generate Bank Document Checklist PDF
     */
    public function generateDocumentChecklistPdf(ImportLc $lc): Response
    {
        $view = 'pdf.document_checklist';
        $data = [
            'lc' => $lc,
            'requirements' => $lc->documentRequirements,
            'documents' => $lc->documents,
            'organization' => $lc->organization,
            'generated_at' => now()->toFormattedDateString(),
        ];

        return UnicodePdf::loadView($view, $data)
            ->a4()
            ->title("Checklist - {$lc->lc_number}")
            ->download("Document-Checklist-{$lc->lc_number}.pdf");
    }

    /**
     * Generate Discrepancy Notice PDF
     */
    public function generateDiscrepancyReportPdf(Discrepancy $discrepancy): Response
    {
        $view = 'pdf.discrepancy_report';
        $data = [
            'discrepancy' => $discrepancy,
            'organization' => $discrepancy->organization,
            'generated_at' => now()->toFormattedDateString(),
        ];

        return UnicodePdf::loadView($view, $data)
            ->a4()
            ->title("Discrepancy - {$discrepancy->discrepancy_reference}")
            ->download("Discrepancy-Notice-{$discrepancy->discrepancy_reference}.pdf");
    }

    /**
     * Generate Payment Maturity Schedule Report
     */
    public function generatePaymentSchedulePdf(PaymentSchedule $schedule): Response
    {
        $view = 'pdf.payment_schedule';
        $data = [
            'schedule' => $schedule,
            'organization' => $schedule->organization,
            'payments' => $schedule->payments,
            'generated_at' => now()->toFormattedDateString(),
        ];

        return UnicodePdf::loadView($view, $data)
            ->a4()
            ->title("Payment Schedule - {$schedule->schedule_number}")
            ->download("Payment-Schedule-{$schedule->schedule_number}.pdf");
    }

    /**
     * Generate Import LC Register PDF for an organization
     */
    public function generateImportLcRegisterPdf(Organization $organization, string $locale = 'en'): Response
    {
        $closedStatuses = ['Settled', 'Expired', 'Cancelled', 'Closed'];

        $importLcs = ImportLc::query()
            ->where('organization_id', $organization->id)
            ->whereNotIn('status', $closedStatuses)
            ->with(['issuingBank', 'counterparty'])
            ->orderBy('lc_number')
            ->get();

        $view = 'pdf.import_lc_register';
        $data = [
            'organization' => $organization,
            'importLcs' => $importLcs,
            'locale' => $locale,
            'generated_at' => now()->toFormattedDateString(),
        ];

        $slug = $organization->slug ?? 'org';

        return UnicodePdf::loadView($view, $data)
            ->a4()
            ->title("Import LC Register - {$organization->name}")
            ->download("Import-LC-Register-{$slug}.pdf");
    }
}
