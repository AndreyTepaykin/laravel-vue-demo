<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UpdateUserRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Return the authenticated user.
     */
    public function show(Request $request): UserResource
    {
        return new UserResource($request->user()->load('roles'));
    }

    /**
     * Update the authenticated user's profile.
     */
    public function update(UpdateUserRequest $request): JsonResponse
    {
        $user = $request->user();
        $user->fill($request->safe()->only(['name', 'email']));

        if ($request->filled('password')) {
            $user->password = $request->string('password')->toString();
        }

        $user->save();

        return response()->json([
            'user' => (new UserResource($user->fresh()->load('roles')))->resolve(),
        ]);
    }
}
