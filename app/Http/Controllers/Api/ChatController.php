<?php

namespace App\Http\Controllers\Api;

use App\Chat;
use App\Events\UserDeleted;
use App\Http\Controllers\Controller;
use App\Http\Resources\ChatGroupResource;
use App\Http\Resources\ChatResource;
use App\Http\Resources\MessageResource;
use App\Rules\CheckChatExistance;
use App\Services\PrivateChatService;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class ChatController extends Controller
{
    /**
     * @var PrivateChatService
     */
    private $chatService;

    public function __construct(PrivateChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    /**
     * @return ChatGroupResource
     */
    public function index(): ChatGroupResource
    {
        $chats = $this->chatService->fetchAll();
        return ChatGroupResource::make($chats);
    }

    /**
     * @param int $user
     * @return ChatResource
     */
    public function show(int $user): ChatResource
    {
        $chat = $this->chatService->show($user);

        return ChatResource::make($chat);
    }

    /**
     * @param Request $request
     * @return ChatResource
     */
    public function store(Request $request): ChatResource
    {
        /** @var User $user */
        $user = auth()->user();

        $validated = $request->validate([
            'user_id' => [
                'required',
                'integer',
                'exists:users,id',
                new CheckChatExistance(),
                Rule::notIn([$user->id])
            ]
        ]);

        $chat = $this->chatService->create($validated);

        return ChatResource::make([
            'host' => $user,
            'guest' => $chat->guest,
            'messages' => $chat->messages
        ]);
    }

    /**
     * @param int $user
     * @return Response
     * @throws \Exception
     */
    public function destroy(int $user): Response
    {
        $this->chatService->delete($user);
        event(new UserDeleted($user, auth()->id()));

        return response('', 204);
    }
}
