<?php
/**
 * Created by PhpStorm.
 * User: imac
 * Date: 2019-07-20
 * Time: 14:38
 */

namespace App\Telegram\ReplyAgents;


use App\Telegram\Commands\StartCommand;
use App\User;

trait BackTrait
{
    public function back_to_start()
    {
        $state = config('telegram.states.startState');

        User::where('chat_id', $this->chat_id)->where('state', '!=', $state)->update(['state' => $state]);

        $this->replyWithMessage([
            'text' => __('telegram.start', ['firstName' => $this->first_name]),
            'reply_markup' => StartCommand::prepare_start_keyboard(),
        ]);

        return false;
    }
}
