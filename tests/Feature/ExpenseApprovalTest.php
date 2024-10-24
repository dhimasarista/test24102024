<?php
namespace Tests\Feature;

use App\Models\Approver;
use App\Models\Expense;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpenseApprovalTest extends TestCase
{
    use RefreshDatabase;

    public function test_expenses_approval_flow()
    {
        // Create approvers
        $approver1 = Approver::create(['name' => 'Ana']);
        $approver2 = Approver::create(['name' => 'Ani']);
        $approver3 = Approver::create(['name' => 'Ina']);

        // Create expenses
        $expense1 = Expense::create(['amount' => 100, 'status' => 'pending']);
        $expense2 = Expense::create(['amount' => 200, 'status' => 'pending']);
        $expense3 = Expense::create(['amount' => 300, 'status' => 'pending']);
        $expense4 = Expense::create(['amount' => 400, 'status' => 'pending']);

        // Simulate approvals
        $this->patchJson("/api/expenses/{$expense1->id}/approve", ['approver_id' => $approver1->id]);
        $this->patchJson("/api/expenses/{$expense1->id}/approve", ['approver_id' => $approver2->id]);
        $this->patchJson("/api/expenses/{$expense1->id}/approve", ['approver_id' => $approver3->id]);

        // Check that the first expense is approved
        $response = $this->getJson("/api/expenses/{$expense1->id}");
        $response->assertStatus(200)
                 ->assertJson(['status' => 'disetujui']);

        // Approve the second expense by two approvers
        $this->patchJson("/api/expenses/{$expense2->id}/approve", ['approver_id' => $approver1->id]);
        $this->patchJson("/api/expenses/{$expense2->id}/approve", ['approver_id' => $approver2->id]);

        // Check that the second expense is still pending
        $response = $this->getJson("/api/expenses/{$expense2->id}");
        $response->assertStatus(200)
                 ->assertJson(['status' => 'menunggu persetujuan']);

        // Approve the third expense by one approver
        $this->patchJson("/api/expenses/{$expense3->id}/approve", ['approver_id' => $approver1->id]);

        // Check that the third expense is still pending
        $response = $this->getJson("/api/expenses/{$expense3->id}");
        $response->assertStatus(200)
                 ->assertJson(['status' => 'menunggu persetujuan']);

        // Check that the fourth expense is still pending (not approved by anyone)
        $response = $this->getJson("/api/expenses/{$expense4->id}");
        $response->assertStatus(200)
                 ->assertJson(['status' => 'menunggu persetujuan']);
    }
}
