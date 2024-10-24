<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Approver;
use App\Models\ApprovalStage;
use App\Models\Expense;
use App\Models\Status;

class ApprovalTest extends TestCase
{
    use RefreshDatabase;

    public function testApprovalWorkflow()
    {
        // 1. Buat 3 approver
        $approvers = [
            ['name' => 'Ana'],
            ['name' => 'Ani'],
            ['name' => 'Ina'],
        ];

        foreach ($approvers as $approver) {
            $this->postJson('/approvers', $approver);
        }

        // 2. Buat 3 tahap approval
        $approvalStages = [
            ['approver_id' => 1],
            ['approver_id' => 2],
            ['approver_id' => 3],
        ];

        foreach ($approvalStages as $stage) {
            $this->postJson('/approval-stages', $stage);
        }

        // 3. Buat 4 pengeluaran
        $expenses = [
            ['amount' => 1000], // Pengeluaran pertama
            ['amount' => 2000], // Pengeluaran kedua
            ['amount' => 3000], // Pengeluaran ketiga
            ['amount' => 4000], // Pengeluaran keempat
        ];

        $expenseIds = [];
        foreach ($expenses as $expense) {
            $response = $this->postJson('api/expenses', $expense);
            $expenseIds[] = $response->json('id');
        }

        // 4. Setujui pengeluaran pertama oleh semua approver
        foreach ($approvalStages as $index => $stage) {
            $this->patchJson("api/expenses/{$expenseIds[0]}/approve", ['approver_id' => $stage['approver_id']]);
        }

        // Pastikan status pengeluaran pertama disetujui
        $response = $this->getJson(route('expenses.show', ['id' => $expenseIds[0]]));
        $response->assertStatus(200)
                 ->assertJson([
                     'status' => 'disetujui', // Status baru
                 ]);

        // 5. Setujui pengeluaran kedua oleh dua approver
        $this->patchJson("api/expenses/{$expenseIds[1]}/approve", ['approver_id' => $approvalStages[0]['approver_id']]);
        $this->patchJson("api/expenses/{$expenseIds[1]}/approve", ['approver_id' => $approvalStages[1]['approver_id']]);

        // Pastikan status pengeluaran kedua menunggu persetujuan
        $response = $this->getJson(route('expenses.show', ['id' => $expenseIds[1]]));
        $response->assertStatus(200)
                 ->assertJson([
                     'status' => 'menunggu persetujuan', // Status baru
                 ]);

        // 6. Setujui pengeluaran ketiga oleh satu approver
        $this->patchJson("api/expenses/{$expenseIds[2]}/approve", ['approver_id' => $approvalStages[0]['approver_id']]);

        // Pastikan status pengeluaran ketiga menunggu persetujuan
        $response = $this->getJson(route('expenses.show', ['id' => $expenseIds[2]]));
        $response->assertStatus(200)
                 ->assertJson([
                     'status' => 'menunggu persetujuan', // Status baru
                 ]);

        // 7. Pastikan pengeluaran keempat belum disetujui
        $response = $this->getJson(route('expenses.show', ['id' => $expenseIds[3]]));
        $response->assertStatus(200)
                 ->assertJson([
                     'status' => 'menunggu persetujuan', // Status baru
                 ]);
    }
}
