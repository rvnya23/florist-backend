<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\TestimonialRequest;
use App\Http\Resources\Api\V1\TestimonialResource;
use App\Models\Testimonial;
use App\Support\APIResponse;
use Illuminate\Http\JsonResponse;

class TestimonialController extends Controller
{
    public function store(TestimonialRequest $request): JsonResponse
    {
        $testimonial = Testimonial::query()->create($request->validated());

        return APIResponse::success(
            (new TestimonialResource($testimonial))->resolve($request),
            'Terima kasih atas testimoninya, akan segera kami tinjau.',
            201,
        );
    }
}
