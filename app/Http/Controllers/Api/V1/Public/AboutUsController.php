<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\AboutUsSectionResource;
use App\Models\AboutUsSection;
use App\Support\APIResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AboutUsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $sections = AboutUsSection::query()->orderBy('sort_order')->get();

        return APIResponse::success(AboutUsSectionResource::collection($sections)->resolve($request));
    }
}
