<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContactResource;
use App\Models\User;
use App\Services\UserService;


class ContactController extends Controller
{
    public function __invoke(User $user, UserService $userService)
    {
        $this->authorize('viewContactsList', $user);

        $contacts = $userService->getUserContacts($user);

        return ContactResource::collection($contacts);
    }
}
