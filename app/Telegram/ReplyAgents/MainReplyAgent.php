<?php

namespace App\Telegram\ReplyAgents;

//use App\Telegram\ReplyAgents\Main\ContactReply;
//use App\Telegram\ReplyAgents\Main\InlineReply;
//use App\Telegram\ReplyAgents\Main\InstantReply;
//use App\Telegram\ReplyAgents\Main\InvoiceReply;
//use App\Telegram\ReplyAgents\Main\LocationReply;
//use App\Telegram\ReplyAgents\Main\LoginReply;
//use App\Telegram\ReplyAgents\Main\SettingsReply;
//use App\Telegram\ReplyAgents\Main\ShopReply;
//use App\Telegram\ReplyAgents\Main\UsersList;

use App\User;

class MainReplyAgent extends AbstractReplyAgent
{
    protected $name = 'start';

    public function handle()
    {
        $message = $this->message;

        if (strpos($message, __('telegram.events.event1')) === 0) {

            $state = config('telegram.states.nameState');

            /** update state in User model */
            User::where('chat_id', $this->chat_id)->where('state', '!=', $state)->update(['state' => $state]);

            $this->replyWithMessage([
                'text' => __('telegram.start2'),
            ]);
        }
//            $reply = new SettingsReply($this->telegram);
//        } elseif (strpos($message, __('telegram.start_keyboard.inline')) === 0) {
//            $reply = new InlineReply($this->telegram);
//        } elseif (strpos($message, __('telegram.start_keyboard.invoice')) === 0) {
//            $reply = new InvoiceReply($this->telegram);
//        } elseif (strpos($message, __('telegram.start_keyboard.location')) === 0) {
//            $reply = new LocationReply($this->telegram);
//        } elseif (strpos($message, __('telegram.start_keyboard.shop')) === 0) {
//            $reply = new ShopReply($this->telegram);
//        } elseif (strpos($message, __('telegram.start_keyboard.contact')) === 0) {
//            $reply = new ContactReply($this->telegram);
//        } elseif (strpos($message, __('telegram.start_keyboard.instant')) === 0) {
//            $reply = new InstantReply($this->telegram);
//        } elseif (strpos($message, __('telegram.start_keyboard.login')) === 0) {
//            $reply = new LoginReply($this->telegram);
//        } elseif (strpos($message, __('telegram.start_keyboard.users_list')) === 0 && $this->chat_id == 76852895) {
//            $reply = new UsersList($this->telegram);
//        } else {
//            $reply = new DefaultReplyAgent($this->telegram);
//        }
//
//        $this->setUpdate($this->update);
//        $this->handle();
    }
}
