<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Order;
use App\Models\Product;
use App\Models\Testimonial;
use App\Models\TransaksiOrder;
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
            'orders' => Order::query()->count(),
            'pending_orders' => Order::query()->where('status', Order::STATUS_PENDING_PAYMENT)->count(),
            'pending_payments' => TransaksiOrder::query()->where('status', TransaksiOrder::STATUS_PENDING)->count(),
            'confirmed_orders' => Order::query()->where('status', Order::STATUS_CONFIRMED)->count(),
        ]);
    }
}
