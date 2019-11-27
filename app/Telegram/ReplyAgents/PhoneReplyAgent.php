<?php

namespace App\Telegram\ReplyAgents;

use App\User;
use App\UserMeta;
use Telegram\Bot\Keyboard\Keyboard;

class PhoneReplyAgent extends AbstractReplyAgent
{
    protected $name = 'phone';
    protected $phone_number_string = '';

    public function handle()
    {
        $phone_string = $this->message;
        $phone_number = $this->phone_number;
        $chat_id = $this->chat_id;
        $user_id = $this->user_id;

        $state = config('telegram.states.paymentState');

        if ($phone_number || $this->validate_phone_number($phone_string)) {
            /** update state in User model */
            User::where('chat_id', $chat_id)->where('state', '!=', $state)->update(['state' => $state]);

            $phone_number = $phone_number ?? $this->phone_number_string;

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

    public function validate_phone_number($phone)
    {
        // Allow +, - and . in phone number
        $filtered_phone_number = filter_var($phone, FILTER_SANITIZE_NUMBER_INT);
        // Remove "-" from number
        $phone_to_check = str_replace("-", "", $filtered_phone_number);
        // Check the lenght of number
        // This can be customized if you want phone number from a specific country
        if (strlen($phone_to_check) < 10 || strlen($phone_to_check) > 14) {
            return false;
        } else {
            $this->phone_number_string = $phone_to_check;
            return true;
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
