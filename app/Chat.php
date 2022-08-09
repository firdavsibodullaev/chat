<?php

namespace App;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property-read User $host
 * @property-read User $guest
 * @property-read Collection<Message> $messages
 */
class Chat extends Model
{
    protected $fillable = [
        'chat_type'
    ];

    /**
     * @return HasOne
     */
    public function host(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'participant_1_id');
    }

    /**
     * @return HasOne
     */
    public function guest(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'participant_2_id');
    }

    /**
     * @return HasMany
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'chat_id', 'id');
    }
}
