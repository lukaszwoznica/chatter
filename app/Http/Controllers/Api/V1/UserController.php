<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::paginate(50);

        return UserResource::collection($users);
    }

    public function show(User $user)
    {
        return new UserResource($user);
    }
}
