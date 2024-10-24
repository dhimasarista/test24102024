<?php
// tests/Feature/ExpenseApprovalTest.php
namespace Tests\Feature;

use App\Models\Approver;
use App\Models\Expense;
use App\Models\Status;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpenseApprovalTest extends TestCase
{
    // use RefreshDatabase;

    public function test_expense_approval_flow()
    {
        // 1. Buat 3 approver menggunakan endpoint
        $approvers = ['Ana', 'Ani', 'Ina'];
        $approverIds = [];
        foreach ($approvers as $approverName) {
            $response = $this->postJson('/api/approvers', ['name' => $approverName]);
            $response->assertStatus(201);
            $approverIds[] = $response->json('id');
        }

        // 2. Buat status 'menunggu persetujuan' dan 'disetujui'
        $waitingStatus = Status::create(['name' => 'menunggu persetujuan']);
        $approvedStatus = Status::create(['name' => 'disetujui']);

        // 3. Buat 3 tahap approval menggunakan endpoint
        foreach ($approverIds as $approverId) {
            $response = $this->postJson('/api/approval-stages', [
                'approver_id' => $approverId,
            ]);
            $response->assertStatus(201);
        }

        // 4. Buat 4 pengeluaran menggunakan endpoint
        $expenses = [];
        for ($i = 1; $i <= 4; $i++) {
            $response = $this->postJson('/api/expenses', [
                'amount' => 100 * $i,
            ]);
            $response->assertStatus(201);
            $expenses[] = $response->json('id');
        }
        

        // 5. Proses approval untuk setiap pengeluaran

        // 5.1 Pengeluaran Pertama - Disetujui Semua
        foreach ($approverIds as $approverId) {
            $this->patchJson("/api/expenses/{$expenses[0]}/approve", [
                'approver_id' => $approverId,
            ])->assertStatus(200);
        }
        // Pastikan status pengeluarannya disetujui
        $response = $this->getJson("/api/expenses/{$expenses[0]}");
        $response->assertStatus(200)
            ->assertJson([
                'status' => ['name' => 'disetujui']
            ]);

        // 5.2 Pengeluaran Kedua - Disetujui 2 approver
        foreach (array_slice($approverIds, 0, 2) as $approverId) {
            $this->patchJson("/api/expenses/{$expenses[1]}/approve", [
                'approver_id' => $approverId,
            ])->assertStatus(200);
        }
        // Pastikan status pengeluarannya masih menunggu persetujuan
        $response = $this->getJson("/api/expenses/{$expenses[1]}");
        $response->assertStatus(200)
            ->assertJson([
                'status' => ['name' => 'menunggu persetujuan']
            ]);

        // 5.3 Pengeluaran Ketiga - Disetujui 1 approver
        $this->patchJson("/api/expenses/{$expenses[2]}/approve", [
            'approver_id' => $approverIds[0],
        ])->assertStatus(200);
        // Pastikan status pengeluarannya masih menunggu persetujuan
        $response = $this->getJson("/api/expenses/{$expenses[2]}");
        $response->assertStatus(200)
            ->assertJson([
                'status' => ['name' => 'menunggu persetujuan']
            ]);

        // 5.4 Pengeluaran Keempat - Belum disetujui
        // Pastikan status pengeluarannya masih menunggu persetujuan
        $response = $this->getJson("/api/expenses/{$expenses[3]}");
        $response->assertStatus(200)
            ->assertJson([
                'status' => ['name' => 'menunggu persetujuan']
            ]);
    }
}
