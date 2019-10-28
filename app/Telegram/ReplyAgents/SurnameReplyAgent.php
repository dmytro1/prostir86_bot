<?php

namespace App\Telegram\ReplyAgents;

use App\User;
use App\UserMeta;

class SurnameReplyAgent extends AbstractReplyAgent
{
    protected $name = 'surname';

    public function handle()
    {
        $message = $this->message;
        $chat_id = $this->chat_id;
        $user_id = $this->user_id;

        $state = config('telegram.states.emailState');

        /** update state in User model */
        User::where('chat_id', $chat_id)->where('state', '!=', $state)->update(['state' => $state]);

        UserMeta::updateOrCreate(['user_id' => $user_id], ['surname' => $message]);

        $this->replyWithMessage([
            'text' => 'Ваше прізвище <b>' . $message . '</b>',
            'parse_mode' => 'html',
        ]);

        $this->replyWithMessage([
            'text' => 'Введіть Ваш e-mail:',
            'parse_mode' => 'html',
        ]);
    }
}
