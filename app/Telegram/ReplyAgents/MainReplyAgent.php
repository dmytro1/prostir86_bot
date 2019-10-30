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

        } elseif (strpos($message, $events[1]['title']) === 0) {

            $event_id = $events['1']['id'];

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

        } else {
            $reply = new DefaultReplyAgent($this->telegram);
            $reply->setUpdate($this->update);
            $reply->handle();
        }
    }

    public function prepare_qty_keyboard()
    {
        $keyboard = Keyboard::make(['resize_keyboard' => true]);

        $button1 = Keyboard::button([
            'text' => '1 квиток',
        ]);

        $button2 = Keyboard::button([
            'text' => '2 квитки',
        ]);

        $button3 = Keyboard::button([
            'text' => '3 квитки',
        ]);

        $button4 = Keyboard::button([
            'text' => '4 квитки',
        ]);

        $keyboard->row($button1);
        $keyboard->row($button2);
        $keyboard->row($button3);
        $keyboard->row($button4);

        return $keyboard;
    }
}
