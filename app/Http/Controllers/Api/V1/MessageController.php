<?php

namespace App\Http\Controllers\Api\V1;

use App\Events\MessagesReadEvent;
use App\Events\NewMessageEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\SendMessageRequest;
use App\Http\Resources\MessageResource;
use App\Models\Message;
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
        $this->messageService->markAllAsReadFromUser($user);
        $messages = $this->messageService->getAllForUser($user);

        return MessageResource::collection($messages);
    }

    public function send(SendMessageRequest $request)
    {
        $message = $this->messageService->create($request->validated());

        NewMessageEvent::dispatch($message);

        return new MessageResource($message);
    }

    public function markAsRead(Message $message)
    {
        $updatedMessage = $this->messageService->markAsRead($message);

        MessagesReadEvent::dispatch(collect([$updatedMessage]));

        return new MessageResource($updatedMessage);
    }
}
