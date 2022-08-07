<?php

namespace App\Services;

use App\Chat;
use App\User;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Class ChatService
 * @package App\Services
 */
class ChatService
{
    /**
     * @return Collection
     */
    public function fetchAll(): Collection
    {
        /** @var User $user */
        $user = auth()->user();
        $guests = Chat::query()
            ->where('participant_1_id', '=', $user->id)
            ->orWhere('participant_2_id', '=', $user->id)
            ->with(['host', 'guest'])
            ->get()
            ->map(function (Chat $chat) use ($user) {
                $guest = $chat->participant_1_id === $user->id
                    ? $chat->guest
                    : $chat->host;
                return $guest;
            });

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

        $chat = Chat::query()
            ->where(function (Builder $q) use ($user, $host) {
                $q->where('participant_1_id', '=', $host->id)
                    ->where('participant_2_id', '=', $user);
            })->orWhere(function (Builder $q) use ($user, $host) {
                $q->where('participant_2_id', '=', $host->id)
                    ->where('participant_1_id', '=', $user);
            })->with(['messages', 'host', 'guest'])
            ->firstOrFail();

        $guest = $chat->participant_1_id === $user
            ? $chat->host
            : $chat->guest;

        return collect([
            'id' => $chat->id,
            'host' => $host,
            'guest' => $guest,
            'messages' => $chat->messages
        ]);
    }

    /**
     * @param $user
     * @return Collection
     */
    public function getGuest($user): Collection
    {
        /** @var User $host */
        $host = auth()->user();

        $chat = Chat::query()
            ->where(function (Builder $q) use ($user, $host) {
                $q->where('participant_1_id', '=', $host->id)
                    ->where('participant_2_id', '=', $user);
            })->orWhere(function (Builder $q) use ($user, $host) {
                $q->where('participant_2_id', '=', $host->id)
                    ->where('participant_1_id', '=', $user);
            })->with(['host', 'guest'])
            ->firstOrFail();

        $guest = $chat->participant_1_id === $user
            ? $chat->host
            : $chat->guest;

        return collect([
            'id' => $chat->id,
            'guest' => $guest,
        ]);
    }

    /**
     * @param array $payload
     * @return Model
     */
    public function create(array $payload): Model
    {
        return Chat::query()->create([
            'participant_1_id' => auth()->id(),
            'participant_2_id' => $payload['user_id']
        ])->load(['messages', 'guest']);
    }

    /**
     * @param int $user
     * @return bool|null
     * @throws Exception
     */
    public function delete(int $user): ?bool
    {
        $id = auth()->id();
        $chat = Chat::query()
            ->where(function (Builder $q) use ($user, $id) {
                $q->where('participant_1_id', '=', $id)
                    ->where('participant_2_id', '=', $user);
            })->orWhere(function (Builder $q) use ($user, $id) {
                $q->where('participant_2_id', '=', $id)
                    ->where('participant_1_id', '=', $user);
            })->with(['messages', 'host', 'guest'])
            ->firstOrFail();

        return $chat->delete();
    }

    /**
     * @param int $chat_id
     * @param User $user
     * @return bool
     */
    public function checkChatUserExistance(int $chat_id, User $user): bool
    {
        $chat = Chat::query()->whereKey($chat_id)->first();

        if (is_null($chat)) {
            return false;
        }
        return $chat->participant_1_id === $user->id || $chat->participant_2_id === $user->id;
    }
}
