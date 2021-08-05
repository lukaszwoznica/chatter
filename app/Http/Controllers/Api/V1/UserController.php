<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContactResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index(Request $request)
    {
        $users = $this->userService->getAll($request->query('search'));

        return UserResource::collection($users);
    }

    public function show(User $user)
    {
        return new UserResource($user);
    }

    public function contacts(User $user)
    {
        $this->authorize('viewContactsList', $user);

        $contacts = $this->userService->getUserContacts($user);

        return ContactResource::collection($contacts);
    }

    public function authUser()
    {
        return new UserResource(auth()->user());
    }
}
