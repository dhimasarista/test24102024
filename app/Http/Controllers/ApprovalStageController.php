<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateApprovalStageRequest;
use App\Http\Requests\StoreApprovalRequest;
use App\Models\ApprovalStage;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

class ApprovalStageController extends Controller
{
    /**
     * @OA\Post(
     *     path="api/approval-stages",
     *     summary="Tambah tahap approval",
     *     tags={"Approval Stages"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="approver_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Approval stage ditambahkan",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="approver_id", type="integer", example=1),
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation Error"),
     * )
     */
    public function store(StoreApprovalRequest $request): JsonResponse
    {
        $approvalStage = ApprovalStage::create($request->validated());
        return response()->json($approvalStage, 201);
    }
}
