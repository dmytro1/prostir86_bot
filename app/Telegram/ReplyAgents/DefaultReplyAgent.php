<?php

namespace App\Telegram\ReplyAgents;

use App\User;

class DefaultReplyAgent extends AbstractReplyAgent
{
    protected $name = 'default';

    public function handle()
    {
        $chat_id = $this->chat_id;
        $message = $this->message;

        $locale = app()->getLocale();
//        $state = User::where(['chat_id' => $chat_id])->value('state');
        $state = 'start';

        $command = 'Command: <b>"' . $message . '"</b> not found';
        $default = 'This is default message';
        $locale_msg = 'Locale: <b>' . $locale . '</b>';
        $state_msg = 'State: <b>' . $state . '</b>';

        $this->replyWithMessage([
            'text' => sprintf('%s' . PHP_EOL . '%s' . PHP_EOL . '%s' . PHP_EOL . '%s', $command, $default, $locale_msg, $state_msg),
            'parse_mode' => 'html',
        ]);
    }
}