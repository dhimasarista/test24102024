<?php
/**
 * @OA\Info(
 *     title="Assesment Backend PT. Loker Asli Indonesia",
 *     version="1.0.0",
 *     description="-"
 * )
 */
use App\Http\Controllers\ApprovalStageController;
use App\Http\Controllers\ApproverController;
use App\Http\Controllers\ExpenseController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::get('/test', function(){
    return response()->json([
        "message" => "Hello, PHP"
    ], 200);
});


// Rute untuk approver
Route::post('/approvers', [ApproverController::class, 'store'])
    ->name('approvers.store'); // Tambah approver

// Rute untuk approval stages
Route::post('/approval-stages', [ApprovalStageController::class, 'store'])
    ->name('approval-stages.store'); // Tambah tahap approval

Route::put('/approval-stages/{id}', [ApprovalStageController::class, 'update'])
    ->name('approval-stages.update'); // Ubah tahap approval

// Rute untuk expenses
Route::post('/expenses', [ExpenseController::class, 'store'])
    ->name('expenses.store'); // Tambah pengeluaran

Route::patch('/expenses/{id}/approve', [ExpenseController::class, 'approve'])
    ->name('expenses.approve'); // Setujui pengeluaran

Route::get('/expenses/{id}', [ExpenseController::class, 'show'])
    ->name('expenses.show'); // Ambil pengeluaran