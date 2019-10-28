<?php

namespace App\Telegram\ReplyAgents;

use App\Event;
use App\Order;
use App\User;
use Telegram\Bot\Keyboard\Keyboard;

class QuantityReplyAgent extends AbstractReplyAgent
{
    protected $name = 'quantity';

    public function handle()
    {
        $message = $this->message;
        $chat_id = $this->chat_id;
        $user_id = $this->user_id;

        $ticket_qty = substr($message, 0, 1);

        if (is_numeric($ticket_qty) ?? $ticket_qty < 10) {

            $state = config('telegram.states.nameState');

            /** update state in User model */
            User::where('chat_id', $chat_id)->where('state', '!=', $state)->update(['state' => $state]);

            $current_order = Order::where([
                'user_id' => $user_id,
                'status' => 'created'
            ])->latest()->first();

            $event_price = Event::find($current_order->event_id)->price;

            Order::find($current_order->id)
                ->update([
                    'price' => $event_price,
                    'quantity' => $ticket_qty,
                ]);

            $this->replyWithMessage([
                'text' => 'Ви вибрали: <b>' . $ticket_qty . '</b> квитків',
                'reply_markup' => Keyboard::remove(['remove_keyboard' => true]),
                'parse_mode' => 'html',
            ]);

            $this->replyWithMessage([
                'text' => 'Введіть Ваше ім\'я:',
                'parse_mode' => 'html',
            ]);
        } else {
            $this->replyWithMessage([
                'text' => 'Виберіть кількість або введіть число',
                'parse_mode' => 'html',
            ]);
        }

    }
}
