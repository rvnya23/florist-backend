<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\RekeningBankRequest;
use App\Http\Resources\Api\V1\RekeningBankResource;
use App\Models\RekeningBank;
use App\Support\APIResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RekeningBankController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $accounts = RekeningBank::query()->latest()->get();

        return APIResponse::success(RekeningBankResource::collection($accounts)->resolve($request));
    }

    public function store(RekeningBankRequest $request): JsonResponse
    {
        $account = RekeningBank::query()->create($request->validated());

        return APIResponse::success(
            (new RekeningBankResource($account))->resolve($request),
            'Rekening bank berhasil ditambahkan.',
            201,
        );
    }

    public function update(RekeningBankRequest $request, RekeningBank $bankAccount): JsonResponse
    {
        $bankAccount->update($request->validated());

        return APIResponse::success(
            (new RekeningBankResource($bankAccount->refresh()))->resolve($request),
            'Rekening bank berhasil diperbarui.',
        );
    }

    public function updateStatus(Request $request, RekeningBank $bankAccount): JsonResponse
    {
        $data = $request->validate(['is_active' => ['required', 'boolean']]);
        $bankAccount->update($data);

        return APIResponse::success(
            (new RekeningBankResource($bankAccount->refresh()))->resolve($request),
            'Status rekening bank berhasil diperbarui.',
        );
    }
}
