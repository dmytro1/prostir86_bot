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

        if (strlen($message) > 2) {

            $state = config('telegram.states.emailState');

            /** update state in User model */
            User::where('chat_id', $chat_id)->where('state', '!=', $state)->update(['state' => $state]);

            UserMeta::updateOrCreate(['user_id' => $user_id], ['surname' => $message]);

            $this->replyWithMessage([
                'text' => 'Ваше прізвище <b>' . $message . '</b>',
                'parse_mode' => 'html',
            ]);

            $this->replyWithMessage([
                'text' => '📧 Відправте Ваш e-mail:',
                'parse_mode' => 'html',
            ]);
        } else {
            $this->replyWithMessage([
                'text' => 'Введіть прізвище коректно',
                'parse_mode' => 'html',
            ]);
        }
    }
}
