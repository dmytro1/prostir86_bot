<?php

namespace App\Telegram\ReplyAgents;

use App\Event;
use App\Order;
use App\User;

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

            $state = config('telegram.states.nameState');

            /** update state in User model */
            User::where('chat_id', $this->chat_id)->where('state', '!=', $state)->update(['state' => $state]);

            Order::create([
                'user_id' => $this->user_id,
                'event_id' => $event_id,
                'status' => 'created'
            ]);

            $this->replyWithMessage([
                'text' => __('telegram.start2'),
            ]);

        } elseif (strpos($message, $events[0]['title']) === 0) {

            $this->replyWithMessage([
                'text' => 'Event 2',
            ]);

        } else {
            $reply = new DefaultReplyAgent($this->telegram);
            $reply->setUpdate($this->update);
            $reply->handle();
        }
    }
}
