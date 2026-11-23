<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\PublicMemberRequest;
use App\Http\Resources\Api\V1\MemberResource;
use App\Models\Member;
use App\Models\User;
use App\Support\APIResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MemberController extends Controller
{
    public function store(PublicMemberRequest $request): JsonResponse
    {
        $data = $request->validated();

        [$member, $user] = DB::transaction(function () use ($data): array {
            $user = User::query()->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'role' => 'customer',
            ]);

            $member = Member::query()->create([
                'user_id' => $user->id,
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'address' => $data['address'],
            ]);

            return [$member, $user];
        });

        if ($data['auto_login'] ?? false) {
            Auth::login($user);
            $request->session()->regenerate();
        }

        return APIResponse::success(
            (new MemberResource($member))->resolve($request),
            "Selamat, {$member->name}! Anda telah menjadi member.",
            201,
        );
    }
}
