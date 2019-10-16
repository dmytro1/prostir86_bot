<?php

namespace App\Http\Controllers;

use App\Telegram\Core\CallbackCommandBus;
use App\Telegram\Core\ReplyAgentsSupervisor;
use Telegram\Bot\Api;

/**
 * Class BotController
 */
class BotController extends Controller
{
    /** @var Api */
    protected $telegram;

    /**
     * BotController constructor.
     *
     * @param Api $telegram
     */
    public function __construct(Api $telegram)
    {
        $this->telegram = $telegram;
    }

    /**
     * Get updates from Telegram.
     */
    public function getUpdates()
    {
        $updates = $this->telegram->getUpdates()->getResult();

        // Do something with the updates
    }

    /**
     * Set a webhook.
     */
    public function setWebhook()
    {
        // Edit this with your webhook URL.
        // You can also use: route('bot-webhook')
        $url = "https://domain.com/bot/webhook";
        $response = $this->telegram->setWebhook()
            ->url($url)
            ->getResult();

        return $response->getDecodedBody();
    }

    /**
     * Remove webhook.
     *
     * @return array
     */
    public function removeWebhook()
    {
        $response = $this->telegram->removeWebhook();

        return $response->getDecodedBody();
    }

    public function webhookHandler(Api $telegram)
    {
        $update = $telegram->commandsHandler(true);

        if ($callbackQuery = $update->callbackQuery) {

            /** @var CallbackCommandBus $bus */
            $bus = app(CallbackCommandBus::class);
            $data = $callbackQuery->data;

            $bus->handle($data, $update);
        }

        if ($update->message) {
            $text = $update->message->text;
            $location = $update->message->location;
            if ((strlen($text) > 0 && substr($text, 0, 1) != '/') || (isset($location) && $location->latitude && $location->longitude)) {
                /** @var ReplyAgentsSupervisor $supervisor */
                $supervisor = app(ReplyAgentsSupervisor::class);
                $supervisor->handle($update);
            }
        }
    }
}
