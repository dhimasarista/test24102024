<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateExpenseRequest;
use App\Models\Expense;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

class ExpenseController extends Controller
{
    /**
     * @OA\Post(
     *     path="/expenses",
     *     summary="Tambah pengeluaran",
     *     tags={"Expenses"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="amount", type="integer", example=1000)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Pengeluaran ditambahkan",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="amount", type="integer", example=1000),
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation Error"),
     * )
     */
    public function store(CreateExpenseRequest $request): JsonResponse
    {
        $expense = Expense::create($request->validated());
        return response()->json($expense, 201);
    }

    /**
     * @OA\Patch(
     *     path="/expenses/{id}/approve",
     *     summary="Setujui pengeluaran",
     *     tags={"Expenses"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="approver_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Pengeluaran disetujui",
     *     ),
     *     @OA\Response(response=422, description="Validation Error"),
     * )
     */
    public function approve($id, Request $request): JsonResponse
    {
        // Logic untuk menyetujui pengeluaran
    }

    /**
     * @OA\Get(
     *     path="api/expenses/{id}",
     *     summary="Ambil pengeluaran",
     *     tags={"Expenses"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detail pengeluaran",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="amount", type="integer", example=1000),
     *             @OA\Property(property="status", type="string", example="menunggu persetujuan"),
     *             @OA\Property(property="approval", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="approver", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Ana"),
     *                     @OA\Property(property="status", type="integer", example=1)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="Pengeluaran tidak ditemukan"),
     * )
     */
    public function show($id): JsonResponse
    {
        $expense = Expense::findOrFail($id);
        return response()->json($expense);
    }
}
