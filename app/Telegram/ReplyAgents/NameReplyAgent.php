<?php

namespace App\Telegram\ReplyAgents;

use App\User;
use App\UserMeta;

class NameReplyAgent extends AbstractReplyAgent
{
    protected $name = 'name';

    public function handle()
    {
        $message = $this->message;
        $chat_id = $this->chat_id;
        $user_id = $this->user_id;

        $state = config('telegram.states.surnameState');

        /** update state in User model */
        User::where('chat_id', $chat_id)->where('state', '!=', $state)->update(['state' => $state]);

        UserMeta::updateOrCreate(['user_id' => $user_id], ['name' => $message]);

        $this->replyWithMessage([
            'text' => 'Ваше ім\'я <b>' . $message . '</b>',
            'parse_mode' => 'html',
        ]);

        $this->replyWithMessage([
            'text' => 'Введіть Ваше прізвище:',
            'parse_mode' => 'html',
        ]);
    }
}
