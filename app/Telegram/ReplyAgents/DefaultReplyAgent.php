<?php

namespace App\Telegram\ReplyAgents;

use App\User;

class DefaultReplyAgent extends AbstractReplyAgent
{
    protected $name = 'default';
    public $reply_markup = '';

    public function handle()
    {
        $chat_id = $this->chat_id;
        $message = $this->message;

        $locale = app()->getLocale();
        $state = User::where(['chat_id' => $chat_id])->value('state');

        $command = 'Команда: <b>"' . $message . '"</b> не знайдена 😔' . PHP_EOL . 'Використовуйте кнопки нижче 👇🏻';
//        $default = 'This is default message';
//        $locale_msg = 'Locale: <b>' . $locale . '</bfgh>';
//        $state_msg = 'State: <b>' . $state . '</b>';

        $this->replyWithMessage([
            'text' => $command,
            'parse_mode' => 'html',
            'reply_markup' => $this->reply_markup,
        ]);
    }
}