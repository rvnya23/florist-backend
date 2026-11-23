<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\AdminProductRequest;
use App\Http\Resources\Api\V1\ProductResource;
use App\Models\Product;
use App\Support\APIResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(): JsonResponse
    {
        return APIResponse::paginated(
            Product::query()->latest()->paginate(15),
            ProductResource::class,
        );
    }

    public function store(AdminProductRequest $request): JsonResponse
    {
        $data = $request->safe()->except('image');
        $data['image'] = $request->file('image')->store('products', 'public');
        $product = Product::query()->create($data);

        return APIResponse::success(
            (new ProductResource($product))->resolve($request),
            'Produk berhasil ditambahkan.',
            201,
        );
    }

    public function show(Request $request, Product $product): JsonResponse
    {
        return APIResponse::success((new ProductResource($product))->resolve($request));
    }

    public function update(AdminProductRequest $request, Product $product): JsonResponse
    {
        $data = $request->safe()->except('image');
        $oldImage = $product->image;

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        if (isset($data['image'])) {
            Storage::disk('public')->delete($oldImage);
        }

        return APIResponse::success(
            (new ProductResource($product->refresh()))->resolve($request),
            'Produk berhasil diperbarui.',
        );
    }

    public function destroy(Product $product): JsonResponse
    {
        $image = $product->image;
        $product->delete();
        Storage::disk('public')->delete($image);

        return APIResponse::success(message: 'Produk berhasil dihapus.');
    }
}
