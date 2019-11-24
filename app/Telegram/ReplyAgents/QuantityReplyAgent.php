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

            $state = config('telegram.states.paymentState');

            /** update state in User model */
            User::where('chat_id', $chat_id)->where('state', '!=', $state)->update(['state' => $state]);

            $current_order = Order::where([
                'user_id' => $user_id,
                'status' => 'created'
            ])->latest('updated_at')->first();

            $event_price = Event::find($current_order->event_id)->price;
            $event_title = Event::find($current_order->event_id)->title;

            Order::find($current_order->id)
                ->update([
                    'price' => $event_price,
                    'quantity' => $ticket_qty,
                ]);

            $this->replyWithMessage([
                'text' => 'Ви вибрали:' . PHP_EOL . '<b>' . $ticket_qty . '</b> квитків на ' . $event_title,
                'parse_mode' => 'html',
            ]);

            $this->replyWithMessage([
//                'text' => 'Введіть Ваше ім\'я:',
                'text' => 'Перевірте вибрані дані. Наступним кроком введіть платіжні дані та особисту інформацію учасника',
                'parse_mode' => 'html',
                'reply_markup' => $this->prepare_invoice_button(),
            ]);
        } else {
            $this->replyWithMessage([
                'text' => 'Виберіть кількість або введіть число',
                'parse_mode' => 'html',
            ]);
        }

    }

    public function prepare_invoice_button()
    {
        $keyboard = Keyboard::make(['resize_keyboard' => true]);

        $button1 = Keyboard::button([
            'text' => 'Перейти до оплати',
        ]);

        $button2 = Keyboard::button([
            'text' => 'Відмінити замовлення',
        ]);

        $keyboard->row($button1);
        $keyboard->row($button2);

        return $keyboard;
    }
}
