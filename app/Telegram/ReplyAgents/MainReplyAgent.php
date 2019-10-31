<?php

namespace App\Telegram\ReplyAgents;

use App\Event;
use App\Order;
use App\User;
use Telegram\Bot\Keyboard\Keyboard;

class MainReplyAgent extends AbstractReplyAgent
{
    protected $name = 'start';

    public function handle()
    {
        $message = $this->message;

        $eventsCollection = Event::all();
        $events = [];

        foreach ($eventsCollection as $event) {
            $events[] = ['id' => $event->id, 'title' => $event->title];
        }

        if (strpos($message, $events[0]['title']) === 0) {

            $event_id = $events['0']['id'];

            $this->handle_event($event_id);

        } elseif (strpos($message, $events[1]['title']) === 0) {

            $event_id = $events['1']['id'];

            $this->handle_event($event_id);

        } else {
            $reply = new DefaultReplyAgent($this->telegram);
            $reply->setUpdate($this->update);
            $reply->handle();
        }
    }

    public function handle_event($event_id)
    {
        $orders = Order::where('event_id', $event_id)
            ->where('user_id', $this->user_id)
            ->whereIn('status', ['pending_payment', 'pending_payment_pre'])
            ->get();

        if (count($orders) > 0) {
            $this->replyWithMessage([
                'text' => 'У вас є незавершене замовлення. Оплатіть його вище.'
            ]);
        } else {
            $state = config('telegram.states.quantityState');

            /** update state in User model */
            User::where('chat_id', $this->chat_id)->where('state', '!=', $state)->update(['state' => $state]);

            Order::create([
                'user_id' => $this->user_id,
                'event_id' => $event_id,
                'status' => 'created'
            ]);

            $this->replyWithMessage([
                'text' => __('telegram.start2'),
                'reply_markup' => $this->prepare_qty_keyboard()
            ]);
        }
    }

    public function prepare_qty_keyboard()
    {
        $keyboard = Keyboard::make(['resize_keyboard' => true]);

        for ($i = 1; $i <= 4; $i++) {
            $button = Keyboard::button([
                'text' => $i . ' квиток',
            ]);
            $keyboard->row($button);
        }

        return $keyboard;
    }
}
