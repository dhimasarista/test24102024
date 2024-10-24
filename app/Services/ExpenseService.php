<?php
namespace App\Services;

use App\Models\ApprovalStage;
use App\Models\Approver;
use App\Models\Expense;
use App\Models\Approval;
use App\Models\Status;

class ExpenseService
{
    public function createExpense($data)
    {
        // Buat pengeluaran
        $expense = Expense::create([
            'amount' => $data['amount'],
            'status_id' => 1, // Status default: menunggu persetujuan
        ]);

        // Logika untuk approval stages
        foreach ($data['approver_ids'] as $approverId) {
            Approval::create([
                'expense_id' => $expense->id,
                'approver_id' => $approverId,
                'status_id' => 1, // Status default: menunggu persetujuan
            ]);
        }

        return $expense;
    }

    public function approveExpense($expenseId, $approverId)
    {
        $expense = Expense::findOrFail($expenseId);
        $approver = Approver::findOrFail($approverId);

        // Cek tahapan approval yang sesuai
        $currentStage = $expense->approvals()->count();
        $nextStage = ApprovalStage::orderBy('id')->skip($currentStage)->firstOrFail();

        if ($nextStage->approver_id != $approverId) {
            throw new \Exception('Tidak sesuai tahap approval');
        }

        // Buat approval baru
        Approval::create([
            'expense_id' => $expenseId,
            'approver_id' => $approverId,
            'status_id' => Status::where('name', 'disetujui')->first()->id,
        ]);

        // Jika semua tahap disetujui, ubah status expense menjadi disetujui
        if ($expense->approvals()->count() == ApprovalStage::count()) {
            $expense->update(['status_id' => Status::where('name', 'disetujui')->first()->id]);
        }
    }

}
