<?php

namespace App\Services;

use App\Models\Expense;
use App\Models\Approval;
use App\Models\Status;
use Illuminate\Validation\ValidationException;

class ExpenseService
{
    public function createExpense(array $data)
    {
        $waitingStatus = Status::where('name', 'menunggu persetujuan')->firstOrFail();
        return Expense::create([
            'amount' => $data['amount'],
            'status_id' => $waitingStatus->id,
        ]);
    }

    public function approveExpense($id, array $data)
    {
        $expense = Expense::findOrFail($id);
        $approverId = $data['approver_id'];

        // Cek tahapan approval yang sesuai
        $currentStage = $expense->approvals()->count();
        $nextStage = ApprovalStage::orderBy('id')->skip($currentStage)->firstOrFail();

        if ($nextStage->approver_id != $approverId) {
            throw ValidationException::withMessages([
                'approver_id' => ['Tidak sesuai tahap approval'],
            ]);
        }

        // Buat approval baru
        Approval::create([
            'expense_id' => $id,
            'approver_id' => $approverId,
            'status_id' => Status::where('name', 'disetujui')->first()->id,
        ]);

        // Jika semua tahap disetujui, ubah status expense menjadi disetujui
        if ($expense->approvals()->count() == ApprovalStage::count()) {
            $expense->update(['status_id' => Status::where('name', 'disetujui')->first()->id]);
        }

        return $expense;
    }

    public function getExpense($id)
    {
        return Expense::with(['status', 'approvals.approver'])->findOrFail($id);
    }
}
