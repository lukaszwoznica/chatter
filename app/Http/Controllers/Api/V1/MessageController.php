<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\SendMessageRequest;
use App\Http\Resources\MessageResource;
use App\Models\User;
use App\Services\MessageService;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    private MessageService $messageService;

    public function __construct(MessageService $messageService)
    {
        $this->messageService = $messageService;
    }

    public function conversation(User $user)
    {
        $this->messageService->markAsReadFromUser($user);
        $messages = $this->messageService->getAllForUser($user);

        return MessageResource::collection($messages);
    }

    public function send(SendMessageRequest $request)
    {
        $message = $this->messageService->create($request->validated());

        return new MessageResource($message);
    }
}
