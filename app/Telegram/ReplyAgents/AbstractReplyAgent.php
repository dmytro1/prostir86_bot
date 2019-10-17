<?php

namespace App\Telegram\ReplyAgents;

use App\User;
use Telegram\Bot\Answers\Answerable;
use Telegram\Bot\Api;
use Telegram\Bot\Objects\Update;

/**
 * Class Answerable.
 *
 * @method mixed replyWithInvoice($use_sendInvoice_parameters)       Reply Chat with a message. You can use all the sendMessage() parameters except chat_id.
 */
abstract class AbstractReplyAgent
{
    use Answerable;

    use BackTrait;

    protected $name;

    protected $message;

    protected $chat_id;

    protected $first_name;

    protected $location;

    protected $phone_number;

    protected $user_id;

    public function __construct(Api $telegram)
    {
        $this->telegram = $telegram;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setUpdate(Update $update)
    {
        $this->update = $update;

        $this->message = $update->message->text;
        $this->chat_id = $update->message->from->id;
        $this->first_name = $update->message->from->firstName;
        $this->location = $update->message->location;
        $this->phone_number = $update->message->contact->phoneNumber ?? null;

//        $this->user_id = User::where('chat_id', $this->chat_id)->value('id');
    }

    abstract public function handle();
}
