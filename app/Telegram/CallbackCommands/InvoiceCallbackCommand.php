<?php

namespace App\Telegram\CallbackCommands;

use Telegram\Bot\Keyboard\Keyboard;

class InvoiceCallbackCommand extends CallbackCommand
{
    protected $name = 'invoice';

    protected $params = [];

    public function getParameters()
    {
        return [];
    }

    public function setParameters($params)
    {
        $this->params = $params;
    }

    public function handle()
    {

        if ($this->params[0] == 'go_back') {
            $this->answerCallbackQuery(['text' => __('telegram.invoice.inline_notification')]);

            $this->telegram->deleteMessage([
                'chat_id' => $this->chat_id,
                'message_id' => $this->message_id,
                //'inline_message_id' => $this->update->callbackQuery->inlineMessageId,
            ]);

            $this->telegram->sendMessage([
                'chat_id' => $this->chat_id,
                'text' => __('telegram.invoice.discard_text'),
            ]);
        }
    }

    public static function prepare_invoice_keyboard()
    {
        $keyboard = Keyboard::make()->inline();

        $button1 = Keyboard::inlineButton([
            'text' => __('telegram.invoice.pay_button'),
            'pay' => true,
        ]);

        $button2 = Keyboard::inlineButton([
            'text' => __('telegram.invoice.discard_button'),
            'callback_data' => 'invoice:go_back',
        ]);

        $keyboard->row($button1, $button2);

        return $keyboard;
    }
}
