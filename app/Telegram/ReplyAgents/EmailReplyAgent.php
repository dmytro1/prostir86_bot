<?php

namespace App\Telegram\ReplyAgents;

use App\User;
use App\UserMeta;

class EmailReplyAgent extends AbstractReplyAgent
{
    protected $name = 'email';

    public function handle()
    {
        $message = $this->message;
        $chat_id = $this->chat_id;
        $user_id = User::where('chat_id', $chat_id)->value('id');

        $state = config('telegram.states.paymentState');

        /** update state in User model */
//        User::where('chat_id', $chat_id)->where('state', '!=', $state)->update(['state' => $state]);

        UserMeta::updateOrCreate(['user_id' => $user_id], ['email' => $message]);

        $this->replyWithMessage([
            'text' => 'Ваш e-mail <b>' . $message . '</b>',
            'parse_mode' => 'html',
        ]);

        $this->replyWithMessage([
            'text' => 'Перевірте дані перед оплатою.',
            'parse_mode' => 'html',
        ]);

        $user_meta = UserMeta::where('user_id', $user_id)->first();

        $reply = 'Ім\'я: ' . $user_meta->name . PHP_EOL;
        $reply .= 'Прізвище: ' . $user_meta->surname . PHP_EOL;
        $reply .= 'E-mail: ' . $user_meta->email . PHP_EOL;

        $this->replyWithMessage([
            'text' => $reply,
            'parse_mode' => 'html',
        ]);

    }
}
