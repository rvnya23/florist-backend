<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\PublicProductIndexRequest;
use App\Http\Resources\Api\V1\ProductResource;
use App\Models\Product;
use App\Support\APIResponse;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function index(PublicProductIndexRequest $request): JsonResponse
    {
        $products = Product::query()
            ->latest()
            ->when($request->integer('limit'), fn ($query, int $limit) => $query->limit($limit))
            ->get();

        return APIResponse::success(ProductResource::collection($products)->resolve($request));
    }
}
