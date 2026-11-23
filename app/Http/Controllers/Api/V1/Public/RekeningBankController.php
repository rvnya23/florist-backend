<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\RekeningBankResource;
use App\Models\RekeningBank;
use App\Support\APIResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RekeningBankController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $accounts = RekeningBank::query()
            ->where('is_active', true)
            ->orderBy('nama_bank')
            ->get();

        return APIResponse::success(RekeningBankResource::collection($accounts)->resolve($request));
    }
}
