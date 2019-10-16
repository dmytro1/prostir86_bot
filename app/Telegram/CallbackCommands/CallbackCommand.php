<?php

namespace App\Telegram\CallbackCommands;

use Telegram\Bot\Answers\Answerable;
use Telegram\Bot\Api;
use Telegram\Bot\Objects\Update;

abstract class CallbackCommand
{
    use Answerable;

    protected $name;

    protected $chat_id;

    protected $message_id;

    protected $inline_message_id;

    protected $callbackQuery;

    /** @var Update */
    protected $update;

    /** @var Api */
    protected $telegram;

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
        $this->callbackQuery = $update->callbackQuery;
        $this->chat_id = $update->callbackQuery->from->id;
        $this->message_id = $update->callbackQuery->message->messageId;
        $this->inline_message_id = $update->callbackQuery->inlineMessageId;

        //$this->update->callbackQuery->message;
        //$this->update->callbackQuery->message->chat;
        //$this->update->callbackQuery->message->chat->id;
        //$this->update->callbackQuery->id;

        return $this;
    }

    public function getCallbackData()
    {
        $data = $this->getName() . ':' . implode(",", $this->getParameters());

        if (strlen($data) > 64) {
            throw new \InvalidArgumentException("Callback data is larger than 64 bytes");
        }

        return $data;
    }

    public function __call($method, $arguments)
    {
        $action = substr($method, 0, 9);
        if ($action !== 'replyWith') {
            throw new \BadMethodCallException("Method [$method] does not exist.");
        }

        $reply_name = studly_case(substr($method, 9));
        $methodName = 'send' . $reply_name;

        if (! method_exists($this->telegram, $methodName)) {
            throw new \BadMethodCallException("Method [$method] does not exist.");
        }

        if (null === $chat = $this->callbackQuery->message->chat) {
            throw new \BadMethodCallException("No chat available for reply with [$method].");
        }

        $chat_id = $chat->id;

        $params = array_merge(compact('chat_id'), $arguments[0]);

        return call_user_func_array([$this->telegram, $methodName], [$params]);
    }

    public function editMessageText($params)
    {
        if (null === $this->callbackQuery->message) {
            throw new \BadMethodCallException("No callbackQuery available for editMessageText");
        }

        if (! empty($this->inline_message_id)) {
            $additionalParams = [
                'inline_message_id' => $this->inline_message_id,
            ];
        } else {
            $additionalParams = [
                'chat_id' => $this->callbackQuery->message->chat->id,
                'message_id' => $this->message_id,
            ];
        }

        $params = array_merge($additionalParams, $params);

        $this->telegram->editMessageText($params);
    }

    public function editMessageReplyMarkup($params)
    {
        if (null === $this->callbackQuery->message) {
            throw new \BadMethodCallException("No callbackQuery available for editMessageText");
        }

        if (! empty($this->inline_message_id)) {
            $additionalParams = [
                'inline_message_id' => $this->inline_message_id,
            ];
        } else {
            $additionalParams = [
                'chat_id' => $this->callbackQuery->message->chat->id,
                'message_id' => $this->message_id,
            ];
        }

        $params = array_merge($additionalParams, $params);

        $this->telegram->editMessageReplyMarkup($params);
    }

    public function answerCallbackQuery($params = [])
    {
        $params = array_merge([
            'callback_query_id' => $this->callbackQuery->id,
        ], $params);

        $this->telegram->answerCallbackQuery($params);
    }

    abstract public function getParameters();

    abstract public function setParameters($params);

    abstract public function handle();
}
