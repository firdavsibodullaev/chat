<?php

namespace App\Rules;

use App\Chat;
use App\ChatUser;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as BuilderAlias;

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
        return ChatUser::query()
            ->whereIn('chat_id', function (BuilderAlias $q) use ($id) {
                $q->select('chat_id')
                    ->from('chat_user')
                    ->where('user_id', '=', $id);
            })
            ->where('user_id', '=', $value)
            ->with('user')
            ->doesntExist();
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
