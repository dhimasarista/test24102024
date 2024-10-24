<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\ApproveExpenseRequest;
use App\Services\ExpenseService;
use Illuminate\Http\JsonResponse;

class ExpenseController extends Controller
{
    protected $expenseService;

    public function __construct(ExpenseService $expenseService)
    {
        $this->expenseService = $expenseService;
    }

    /**
     * @OA\Post(
     *     path="/expenses",
     *     tags={"Expenses"},
     *     summary="Tambah pengeluaran",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StoreExpenseRequest")
     *     ),
     *     @OA\Response(response=201, description="Pengeluaran berhasil ditambahkan"),
     *     @OA\Response(response=422, description="Validasi gagal")
     * )
     */
    public function store(StoreExpenseRequest $request): JsonResponse
    {
        $expense = $this->expenseService->createExpense($request->validated());
        return response()->json($expense, 201);
    }

    /**
     * @OA\Patch(
     *     path="/expenses/{id}/approve",
     *     tags={"Expenses"},
     *     summary="Setujui pengeluaran",
     *     @OA\Parameter(name="id", in="path", required=true, description="ID pengeluaran"),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/ApproveExpenseRequest")
     *     ),
     *     @OA\Response(response=200, description="Pengeluaran berhasil disetujui"),
     *     @OA\Response(response=422, description="Validasi gagal")
     * )
     */
    public function approveExpense($id, ApproveExpenseRequest $request): JsonResponse
    {
        $expense = $this->expenseService->approveExpense($id, $request->validated());
        return response()->json($expense, 200);
    }

    /**
     * @OA\Get(
     *     path="/expenses/{id}",
     *     tags={"Expenses"},
     *     summary="Ambil pengeluaran",
     *     @OA\Parameter(name="id", in="path", required=true, description="ID pengeluaran"),
     *     @OA\Response(response=200, description="Pengeluaran ditemukan"),
     *     @OA\Response(response=404, description="Pengeluaran tidak ditemukan")
     * )
     */
    public function show($id): JsonResponse
    {
        $expense = $this->expenseService->getExpense($id);
        return response()->json($expense, 200);
    }
}
