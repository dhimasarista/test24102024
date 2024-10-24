<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

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
            $response = $this->postJson('/expense', $expense);
            $expenseIds[] = $response->json('id');
        }

        // 4. Setujui pengeluaran pertama oleh semua approver
        foreach ($approvalStages as $index => $stage) {
            $this->patchJson("/expense/{$expenseIds[0]}/approve", ['approver_id' => $stage['approver_id']]);
        }

        // Pastikan status pengeluaran pertama disetujui
        $response = $this->getJson("/expense/{$expenseIds[0]}");
        $response->assertStatus(200)
                 ->assertJson([
                     'status' => 'approved',
                 ]);

        // 5. Setujui pengeluaran kedua oleh dua approver
        $this->patchJson("/expense/{$expenseIds[1]}/approve", ['approver_id' => $approvalStages[0]['approver_id']]);
        $this->patchJson("/expense/{$expenseIds[1]}/approve", ['approver_id' => $approvalStages[1]['approver_id']]);

        // Pastikan status pengeluaran kedua menunggu persetujuan
        $response = $this->getJson("/expense/{$expenseIds[1]}");
        $response->assertStatus(200)
                 ->assertJson([
                     'status' => 'pending',
                 ]);

        // 6. Setujui pengeluaran ketiga oleh satu approver
        $this->patchJson("/expense/{$expenseIds[2]}/approve", ['approver_id' => $approvalStages[0]['approver_id']]);

        // Pastikan status pengeluaran ketiga menunggu persetujuan
        $response = $this->getJson("/expense/{$expenseIds[2]}");
        $response->assertStatus(200)
                 ->assertJson([
                     'status' => 'pending',
                 ]);

        // 7. Pastikan pengeluaran keempat belum disetujui
        $response = $this->getJson("/expense/{$expenseIds[3]}");
        $response->assertStatus(200)
                 ->assertJson([
                     'status' => 'pending',
                 ]);
    }
}
