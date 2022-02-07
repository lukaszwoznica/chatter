<?php

namespace App\Http\Controllers\Api\V1;

use App\Events\NewMessageEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\SendMessageRequest;
use App\Http\Resources\MessageResource;
use App\Models\Message;
use App\Models\User;
use App\Services\MessageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    private MessageService $messageService;

    public function __construct(MessageService $messageService)
    {
        $this->messageService = $messageService;
    }

    public function conversation(Request $request, User $user)
    {
        $this->messageService->markAllReceivedMessagesFromUserAsRead(Auth::user(), $user);
        $messages = $this->messageService->getAllBetweenUsers(Auth::user(), $user, $request->query('per_page') ?? 15);

        return MessageResource::collection($messages);
    }

    public function send(SendMessageRequest $request)
    {
        $message = Auth::user()->messagesSent()->create($request->validated());

        NewMessageEvent::dispatch($message);

        return new MessageResource($message);
    }

    public function markAsRead(Message $message)
    {
        $this->authorize('markAsRead', $message);

        $updatedMessage = $this->messageService->markAsRead($message);

        return new MessageResource($updatedMessage);
    }
}
