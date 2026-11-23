<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\TestimonialRequest;
use App\Http\Resources\Api\V1\TestimonialResource;
use App\Models\Testimonial;
use App\Support\APIResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    public function index(): JsonResponse
    {
        return APIResponse::paginated(
            Testimonial::query()->latest()->paginate(15),
            TestimonialResource::class,
        );
    }

    public function store(TestimonialRequest $request): JsonResponse
    {
        $testimonial = Testimonial::query()->create($request->validated());

        return APIResponse::success(
            (new TestimonialResource($testimonial))->resolve($request),
            'Testimoni berhasil ditambahkan.',
            201,
        );
    }

    public function show(Request $request, Testimonial $testimonial): JsonResponse
    {
        return APIResponse::success((new TestimonialResource($testimonial))->resolve($request));
    }

    public function update(TestimonialRequest $request, Testimonial $testimonial): JsonResponse
    {
        $testimonial->update($request->validated());

        return APIResponse::success(
            (new TestimonialResource($testimonial->refresh()))->resolve($request),
            'Testimoni berhasil diperbarui.',
        );
    }

    public function destroy(Testimonial $testimonial): JsonResponse
    {
        $testimonial->delete();

        return APIResponse::success(message: 'Testimoni berhasil dihapus.');
    }
}
