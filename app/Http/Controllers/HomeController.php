<?php

namespace App\Http\Controllers;

use App\Chat;
use App\Events\ChatList;
use App\Events\SendMessage;
use App\Events\UserDeleted;
use App\Http\Resources\MessageResource;
use App\Message;
use App\Rules\CheckChatExistance;
use App\Services\ChatService;
use App\Services\MessageService;
use App\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Throwable;

class HomeController extends Controller
{
    /**
     * @var ChatService
     */
    private $chatService;

    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    /**
     * Show the application dashboard.
     *
     * @return array|string
     * @throws Throwable
     */
    public function index()
    {
        return view('chat.index')->render();
    }

    /**
     * @param int $user
     * @return array|string
     * @throws Throwable
     */
    public function show(int $user)
    {

        return view('chat.show', [
            'chat' => $this->chatService->getGuest($user)
        ])->render();
    }

    /**
     * @return array|string
     * @throws Throwable
     */
    public function create()
    {
        $ids = $this->chatService->fetchAll()->flatten()->pluck('id');
        return view('chat.create', [
            'users' => User::query()->whereNotIn('id', $ids)->get()
        ])->render();
    }


    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
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

        /** @var Chat $chat */
        $chat = $this->chatService->create($validated);
        event(new ChatList($user, $chat));

        return redirect()->route('chat.show', $validated['user_id']);
    }

    /**
     * @param Request $request
     * @param User $user
     * @return MessageResource
     */
    public function send(Request $request, User $user): MessageResource
    {
        $validated = $request->validate([
            'text' => 'required|string'
        ]);

        /** @var Message $message */
        $message = (new MessageService())->create($user, $validated);
        event(new SendMessage($message));

        return MessageResource::make($message);
    }

    /**
     * @throws \Exception
     */
    public function destroy(int $user): RedirectResponse
    {
        $this->chatService->delete($user);


        return redirect()->route('index');
    }
}
