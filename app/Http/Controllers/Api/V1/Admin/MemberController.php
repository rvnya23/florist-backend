<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\AdminMemberRequest;
use App\Http\Resources\Api\V1\MemberResource;
use App\Models\Member;
use App\Support\APIResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function index(): JsonResponse
    {
        return APIResponse::paginated(
            Member::query()->latest()->paginate(15),
            MemberResource::class,
        );
    }

    public function store(AdminMemberRequest $request): JsonResponse
    {
        $member = Member::query()->create($request->validated());

        return APIResponse::success(
            (new MemberResource($member))->resolve($request),
            'Member berhasil ditambahkan.',
            201,
        );
    }

    public function show(Request $request, Member $member): JsonResponse
    {
        return APIResponse::success((new MemberResource($member))->resolve($request));
    }

    public function update(AdminMemberRequest $request, Member $member): JsonResponse
    {
        $member->update($request->validated());

        return APIResponse::success(
            (new MemberResource($member->refresh()))->resolve($request),
            'Member berhasil diperbarui.',
        );
    }

    public function destroy(Member $member): JsonResponse
    {
        if ($member->orders()->exists()) {
            return APIResponse::failed('Member yang memiliki riwayat order tidak dapat dihapus.', status: 422);
        }

        $member->delete();

        return APIResponse::success(message: 'Member berhasil dihapus.');
    }
}
