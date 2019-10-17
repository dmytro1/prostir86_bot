<?php

namespace App\Telegram\Commands;

use App;
use App\User;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Keyboard\Keyboard;

/**
 * Class StartCommand
 */
class StartCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = 'start';

    protected $first_name;

    protected $last_name;

    protected $username;

    protected $chat_id;

    /**
     * {@inheritdoc}
     */
    public function handle()
    {

        $update = $this->update;

        $this->first_name = $update->message->from->firstName ?? '';
        $this->last_name = $update->message->from->lastName ?? '';
        $this->username = $update->message->from->username ?? '';
        $this->chat_id = $update->message->from->id;

        $data = [
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'username' => $this->username,
            'chat_id' => $this->chat_id,
            //'user_locale' => $update->message->languageCode,
            'state' => config('telegram.states.startState', 'start'),
        ];

        $new_user = User::updateOrCreate(['chat_id' => $this->chat_id], $data);

        $response = json_encode($new_user);

        $text = __('telegram.start', ['firstName' => $this->first_name]);

        $is_admin = $this->chat_id == 76852895 ? true : false;

        $this->replyWithMessage([
            'text' => $text,
            'reply_markup' => $this->prepare_start_keyboard($is_admin),
        ]);
    }

    /**
     * @param $super_admin boolean Is user with admin rights
     *
     * @return object
     */
    public static function prepare_start_keyboard($super_admin = false)
    {
        $keyboard = Keyboard::make(['resize_keyboard' => true]);

        $event1 = Keyboard::button([
            'text' => __('telegram.events.event1'),
        ]);

        $event2 = Keyboard::button([
            'text' => __('telegram.events.event2'),
        ]);

        $keyboard->row($event1);
        $keyboard->row($event2);

        return $keyboard;
    }
}
