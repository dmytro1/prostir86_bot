<?php

namespace App\Telegram\ReplyAgents;

use App\User;
use App\UserMeta;
use Telegram\Bot\Keyboard\Keyboard;

class PhoneReplyAgent extends AbstractReplyAgent
{
    protected $name = 'phone';

    public function handle()
    {
        $phone_number = $this->phone_number;
        $chat_id = $this->chat_id;
        $user_id = $this->user_id;

        $state = config('telegram.states.paymentState');

        if ($phone_number) {
            /** update state in User model */
            User::where('chat_id', $chat_id)->where('state', '!=', $state)->update(['state' => $state]);

            User::updateOrInsert(['id' => $user_id], ['phone_number' => $phone_number]);

            $this->replyWithMessage([
                'text' => 'Ваш телефон <b>' . $phone_number . '</b>',
                'parse_mode' => 'html',
            ]);

            $this->replyWithMessage([
                'text' => 'Перевірте Ваші дані перед підтвердженням',
                'parse_mode' => 'html',
            ]);

            $user_meta = UserMeta::where('user_id', $user_id)->first();

            $reply = '📃 Ім\'я: ' . $user_meta->name . PHP_EOL;
            $reply .= '📃 Прізвище: ' . $user_meta->surname . PHP_EOL;
            $reply .= '📧 E-mail: ' . $user_meta->email . PHP_EOL;
            $reply .= '📱 Телефон: ' . $phone_number . PHP_EOL;

            $this->replyWithMessage([
                'text' => $reply,
                'parse_mode' => 'html',
                'reply_markup' => $this->prepare_invoice_button(),
            ]);
        } else {
            $this->replyWithMessage([
                'text' => 'Ви не відправили номер',
                'parse_mode' => 'html',
            ]);
        }

    }

    public function prepare_invoice_button()
    {
        $keyboard = Keyboard::make(['resize_keyboard' => true]);

        $button1 = Keyboard::button([
            'text' => 'Підтвердити ☑️',
        ]);

        $button2 = Keyboard::button([
            'text' => 'Зареєструватися повторно 🔁',
        ]);

        $keyboard->row($button1);
        $keyboard->row($button2);

        return $keyboard;
    }
}
