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
    public function handle($arguments)
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

//        $new_user = User::updateOrCreate(['chat_id' => $this->chat_id], $data);

//        $response = json_encode($new_user);

//        $text = __('telegram.start', ['firstName' => $this->first_name]);

//        $is_admin = $this->chat_id == 76852895 ? true : false;

        $this->replyWithMessage([
            'text' => 'test test',
//            'text' => $text,
//            'reply_markup' => $this->prepare_start_keyboard($is_admin),
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

        $shop = Keyboard::button([
            'text' => __('telegram.start_keyboard.shop'),
        ]);
        $invoice = Keyboard::button([
            'text' => __('telegram.start_keyboard.invoice'),
        ]);
        $inline = Keyboard::button([
            'text' => __('telegram.start_keyboard.inline'),
        ]);
        $location = Keyboard::button([
            'text' => __('telegram.start_keyboard.location'),
        ]);
        $instant = Keyboard::button([
            'text' => __('telegram.start_keyboard.instant'),
        ]);
        $login = Keyboard::button([
            'text' => __('telegram.start_keyboard.login'),
        ]);
        $settings = Keyboard::button([
            'text' => __('telegram.start_keyboard.settings'),
        ]);
        $contact = Keyboard::button([
            'text' => __('telegram.start_keyboard.contact'),
        ]);
        $users_list = Keyboard::button([
            'text' => __('telegram.start_keyboard.users_list'),
        ]);

        $keyboard->row($shop, $invoice);
        $keyboard->row($inline, $location);
        $keyboard->row($instant, $login);
        $keyboard->row($settings, $contact);
        if ($super_admin) {
            $keyboard->row($users_list);
        }

        return $keyboard;
    }
}
