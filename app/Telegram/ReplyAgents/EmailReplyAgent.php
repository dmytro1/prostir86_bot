<?php

namespace App\Telegram\ReplyAgents;

use App\User;
use App\UserMeta;
use Telegram\Bot\Keyboard\Keyboard;

class EmailReplyAgent extends AbstractReplyAgent
{
    protected $name = 'email';

    public function handle()
    {
        $message = $this->message;
        $chat_id = $this->chat_id;
        $user_id = $this->user_id;

        if (filter_var($message, FILTER_VALIDATE_EMAIL)) {
            $state = config('telegram.states.phoneState');

            /** update state in User model */
            User::where('chat_id', $chat_id)->where('state', '!=', $state)->update(['state' => $state]);

            UserMeta::updateOrCreate(['user_id' => $user_id], ['email' => $message]);

            $this->replyWithMessage([
                'text' => 'Ваш e-mail <b>' . $message . '</b>',
                'parse_mode' => 'html',
            ]);

            $this->replyWithMessage([
                'text' => '📱 Відправте Ваш телефон з допомогою клавіши 👇🏻',
                'reply_markup' => $this->prepare_phone_keyboard(),
            ]);
        } else {
            $this->replyWithMessage([
                'text' => 'Введіть коректний email',
                'parse_mode' => 'html',
            ]);
        }

    }

    public function prepare_phone_keyboard()
    {
        $keyboard = Keyboard::make(['resize_keyboard' => true]);

        $button1 = Keyboard::button([
            'text' => 'Ваш телефон',
            'request_contact' => true,
        ]);

        $keyboard->row($button1);

        return $keyboard;
    }
}
