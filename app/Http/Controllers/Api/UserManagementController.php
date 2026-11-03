<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreUserRequest;
use App\Http\Requests\Api\UpdateManagedUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class UserManagementController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(): AnonymousResourceCollection
    {
        $this->authorize('viewAny', User::class);

        $users = User::query()
            ->with('roles')
            ->latest()
            ->get();

        return UserResource::collection($users);
    }

    /**
     * Store a newly created user.
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $this->authorize('create', User::class);

        $user = User::create($request->safe()->only(['name', 'email', 'password']));
        $user->syncRoles([$request->string('role')->toString()]);

        return response()->json([
            'user' => (new UserResource($user->load('roles')))->resolve(),
        ], 201);
    }

    /**
     * Display the specified user.
     */
    public function show(User $user): UserResource
    {
        $this->authorize('view', $user);

        return new UserResource($user->load('roles'));
    }

    /**
     * Update the specified user.
     */
    public function update(UpdateManagedUserRequest $request, User $user): JsonResponse
    {
        $this->authorize('update', $user);

        $user->fill($request->safe()->only(['name', 'email']));

        if ($request->filled('password')) {
            $user->password = $request->string('password')->toString();
        }

        $user->save();

        if ($request->user()->hasRole('admin') && $request->filled('role')) {
            $user->syncRoles([$request->string('role')->toString()]);
        }

        return response()->json([
            'user' => (new UserResource($user->fresh()->load('roles')))->resolve(),
        ]);
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user): Response
    {
        $this->authorize('delete', $user);

        $user->delete();

        return response()->noContent();
    }
}
