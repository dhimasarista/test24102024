<?php

namespace App\Services;

use App\Models\ApprovalStage;

class ApprovalStageService
{
    public function createApprovalStage(array $data)
    {
        return ApprovalStage::create($data);
    }

    public function updateApprovalStage($id, array $data)
    {
        $approvalStage = ApprovalStage::findOrFail($id);
        $approvalStage->update($data);
        return $approvalStage;
    }
}
