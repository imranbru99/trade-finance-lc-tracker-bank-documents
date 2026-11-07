<?php

namespace App\Services;

use App\Models\ApprovalHistory;
use App\Models\ApprovalRequest;
use App\Models\ApprovalWorkflow;
use App\Models\AuditLog;
use App\Models\ImportLc;
use App\Models\User;
use Exception;

class ApprovalEngine
{
    /**
     * Submit an LC or financial item for approval
     */
    public function submitForApproval($approvable, User $requestedBy, ?string $notes = null): ApprovalRequest
    {
        $orgId = $approvable->organization_id;
        $module = ($approvable instanceof ImportLc) ? 'import_lc' : 'general';

        // Check if a workflow exists
        $workflow = ApprovalWorkflow::where('organization_id', $orgId)
            ->where('module', $module)
            ->where('is_active', true)
            ->first();

        $totalSteps = $workflow && !empty($workflow->steps) ? count($workflow->steps) : 1;

        $request = ApprovalRequest::create([
            'organization_id' => $orgId,
            'workflow_id' => $workflow?->id,
            'approvable_type' => get_class($approvable),
            'approvable_id' => $approvable->id,
            'requested_by_user_id' => $requestedBy->id,
            'current_step' => 1,
            'total_steps' => $totalSteps,
            'status' => 'Pending',
            'decision_notes' => $notes,
        ]);

        if (method_exists($approvable, 'update')) {
            $approvable->update(['status' => 'Pending Approval']);
        }

        AuditLog::create([
            'organization_id' => $orgId,
            'user_id' => $requestedBy->id,
            'event' => 'approval.submitted',
            'auditable_type' => get_class($approvable),
            'auditable_id' => $approvable->id,
            'new_values' => ['request_id' => $request->id, 'step' => 1],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return $request;
    }

    /**
     * Approve the current step or complete final approval
     */
    public function approve(ApprovalRequest $request, User $user, ?string $comments = null): void
    {
        if ($request->status !== 'Pending') {
            throw new Exception("Approval request #{$request->id} is already {$request->status}.");
        }

        ApprovalHistory::create([
            'approval_request_id' => $request->id,
            'user_id' => $user->id,
            'step_number' => $request->current_step,
            'action' => 'Approved',
            'comments' => $comments,
            'action_at' => now(),
        ]);

        if ($request->current_step >= $request->total_steps) {
            // Final approval
            $request->update([
                'status' => 'Approved',
                'completed_at' => now(),
            ]);

            $approvable = $request->approvable;
            if ($approvable && method_exists($approvable, 'update')) {
                $approvable->update(['status' => 'Active']);
            }
        } else {
            // Move to next step
            $request->increment('current_step');
        }

        AuditLog::create([
            'organization_id' => $request->organization_id,
            'user_id' => $user->id,
            'event' => 'approval.step_approved',
            'auditable_type' => ApprovalRequest::class,
            'auditable_id' => $request->id,
            'new_values' => ['step' => $request->current_step, 'comments' => $comments],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Reject an approval request
     */
    public function reject(ApprovalRequest $request, User $user, ?string $comments = null): void
    {
        if ($request->status !== 'Pending') {
            throw new Exception("Approval request #{$request->id} is already {$request->status}.");
        }

        ApprovalHistory::create([
            'approval_request_id' => $request->id,
            'user_id' => $user->id,
            'step_number' => $request->current_step,
            'action' => 'Rejected',
            'comments' => $comments,
            'action_at' => now(),
        ]);

        $request->update([
            'status' => 'Rejected',
            'decision_notes' => $comments,
            'completed_at' => now(),
        ]);

        $approvable = $request->approvable;
        if ($approvable && method_exists($approvable, 'update')) {
            $approvable->update(['status' => 'Draft']);
        }

        AuditLog::create([
            'organization_id' => $request->organization_id,
            'user_id' => $user->id,
            'event' => 'approval.rejected',
            'auditable_type' => ApprovalRequest::class,
            'auditable_id' => $request->id,
            'new_values' => ['comments' => $comments],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
