<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Product;
use App\Models\Testimonial;
use App\Support\APIResponse;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function __invoke(): JsonResponse
    {
        return APIResponse::success([
            'products' => Product::query()->count(),
            'members' => Member::query()->count(),
            'testimonials' => Testimonial::query()->count(),
        ]);
    }
}
