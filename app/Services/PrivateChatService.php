<?php

namespace App\Services;

use App\Chat;
use App\ChatUser;
use App\Constants\ChatTypeConstant;
use App\User;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as BuilderAlias;
use Illuminate\Support\Collection;

/**
 * Class PrivateChatService
 * @package App\Services
 */
class PrivateChatService
{
    /**
     * @return Collection
     */
    public function fetchAll(): Collection
    {
        /** @var User $user */
        $user = auth()->user();
        $guests = Chat::query()
            ->where('chat_type', '=', ChatTypeConstant::PRIVATE_CHAT)
            ->whereHas('participants', function (Builder $q) use ($user) {
                $q->where('user_id', '=', $user->id);
            })
            ->with('participants')
            ->get()
            ->map(function (Chat $chat) use ($user) {
                return $chat->participants->filter(function (User $participant) use ($user) {
                    return $participant->id !== $user->id;
                });
            })->flatten();

        return collect([
            'host' => $user,
            'guests' => $guests
        ]);
    }

    /**
     * @param int $user
     * @return Collection
     */
    public function show(int $user): Collection
    {
        /** @var User $host */
        $host = auth()->user();

        $chat = ChatUser::query()
            ->whereIn('chat_id', function (BuilderAlias $q) use ($host) {
                $q->select('chat_id')
                    ->from('chat_user')
                    ->where('user_id', '=', $host->id);
            })
            ->where('user_id', '=', $user)
            ->with(['user', 'chat.messages'])
            ->firstOrFail();

        return collect([
            'id' => $chat->chat_id,
            'host' => $host,
            'guest' => $chat->user,
            'messages' => $chat->chat->messages
        ]);
    }

    /**
     * @param int $user
     * @return Collection
     */
    public function getGuest(int $user): Collection
    {
        /** @var User $host */
        $host = auth()->user();

        $chat = ChatUser::query()
            ->whereIn('chat_id', function (BuilderAlias $q) use ($host) {
                $q->select('chat_id')
                    ->from('chat_user')
                    ->where('user_id', '=', $host->id);
            })
            ->where('user_id', '=', $user)
            ->with('user')
            ->firstOrFail();
        return collect([
            'id' => $chat->id,
            'guest' => $chat->user,
        ]);
    }

    /**
     * @param array $payload
     * @return Model
     */
    public function create(array $payload): Model
    {
        /** @var Chat $chat */
        $chat = Chat::query()->create([
            'chat_type' => ChatTypeConstant::PRIVATE_CHAT
        ]);

        $chat->participants()->sync([
            auth()->id(), $payload['user_id']
        ]);

        return $chat->load(['participants', 'messages']);
    }

    /**
     * @param int $user
     * @return bool|null
     * @throws Exception
     */
    public function delete(int $user): ?bool
    {
        $id = auth()->id();
        $chat = ChatUser::query()
            ->whereIn('chat_id', function (BuilderAlias $q) use ($id) {
                $q->select('chat_id')
                    ->from('chat_user')
                    ->where('user_id', '=', $id);
            })
            ->where('user_id', '=', $user)
            ->with('chat')
            ->firstOrFail();

        return $chat->chat->delete();
    }

    /**
     * @param int $chat_id
     * @param User $user
     * @return bool
     */
    public function checkChatUserExistence(int $chat_id, User $user): bool
    {
        /** @var Chat $chat */
        $chat = Chat::query()->whereKey($chat_id)->first();

        if (is_null($chat)) {
            return false;
        }
        return (bool)$chat->participants->where('id', '=', $user->id)->first();
    }
}
