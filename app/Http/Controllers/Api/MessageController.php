<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MessageResource;
use App\Services\MessageService;
use App\User;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    /**
     * @var MessageService
     */
    private $messageService;

    public function __construct(MessageService $messageService)
    {
        $this->messageService = $messageService;
    }

    /**
     * @param User $user
     * @param Request $request
     * @return MessageResource
     */
    public function store(User $user, Request $request): MessageResource
    {
        $validated = $request->validate([
            'text' => 'required|string'
        ]);

        $message = $this->messageService->create($user, $validated);

        return MessageResource::make($message);
    }
}
