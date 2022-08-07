<?php

namespace App\Rules;

use App\Chat;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\Eloquent\Builder;

class CheckChatExistance implements Rule
{
    /**
     * @var mixed
     */
    private $value;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        $id = auth()->id();
        $this->value = $value;
        return Chat::query()->where(function (Builder $q) use ($value, $id) {
            $q->where('participant_1_id', '=', $id)
                ->where('participant_2_id', '=', $value);
        })->orWhere(function (Builder $q) use ($value, $id) {
            $q->where('participant_2_id', '=', $id)
                ->where('participant_1_id', '=', $value);
        })->doesntExist();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return "Чат с этим пользователем имеется, " . route('api.chat.show', $this->value);
    }
}
