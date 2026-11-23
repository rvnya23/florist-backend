<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\AboutUsSectionRequest;
use App\Http\Resources\Api\V1\AboutUsSectionResource;
use App\Models\AboutUsSection;
use App\Support\APIResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AboutUsSectionController extends Controller
{
    public function index(): JsonResponse
    {
        return APIResponse::paginated(
            AboutUsSection::query()->orderBy('sort_order')->paginate(15),
            AboutUsSectionResource::class,
        );
    }

    public function store(AboutUsSectionRequest $request): JsonResponse
    {
        $section = AboutUsSection::query()->create($request->validated());

        return APIResponse::success(
            (new AboutUsSectionResource($section))->resolve($request),
            'Section berhasil ditambahkan.',
            201,
        );
    }

    public function show(Request $request, AboutUsSection $aboutUsSection): JsonResponse
    {
        return APIResponse::success((new AboutUsSectionResource($aboutUsSection))->resolve($request));
    }

    public function update(AboutUsSectionRequest $request, AboutUsSection $aboutUsSection): JsonResponse
    {
        $aboutUsSection->update($request->validated());

        return APIResponse::success(
            (new AboutUsSectionResource($aboutUsSection->refresh()))->resolve($request),
            'Section berhasil diperbarui.',
        );
    }

    public function destroy(AboutUsSection $aboutUsSection): JsonResponse
    {
        $aboutUsSection->delete();

        return APIResponse::success(message: 'Section berhasil dihapus.');
    }
}
