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

        if (strlen($message) > 1) {
            $state = config('telegram.states.surnameState');

            /** update state in User model */
            User::where('chat_id', $chat_id)->where('state', '!=', $state)->update(['state' => $state]);

            UserMeta::updateOrCreate(['user_id' => $user_id], ['name' => $message]);

            $this->replyWithMessage([
                'text' => 'Ваше ім\'я <b>' . $message . '</b>',
                'parse_mode' => 'html',
            ]);

            $this->replyWithMessage([
                'text' => '📃 Відправте Ваше прізвище:',
                'parse_mode' => 'html',
            ]);
        } else {
            $this->replyWithMessage([
                'text' => 'Введіть ім\'я коректно',
                'parse_mode' => 'html',
            ]);
        }

    }
}
