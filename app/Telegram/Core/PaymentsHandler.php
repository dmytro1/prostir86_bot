<?php
/**
 * Created by PhpStorm.
 * User: dmytrov
 * Date: 18.03.2019
 * Time: 16:18
 */

namespace App\Telegram\Core;

use App\Telegram\Commands\StartCommand;
use App\Telegram\ReplyAgents\LocationReplyAgent;
use App\User;
use Telegram\Bot\Api;
use Telegram\Bot\Objects\Payments\SuccessfulPayment;
use Telegram\Bot\Objects\Update;
use Telegram\Bot\Objects\Payments\LabeledPrice;
use Telegram\Bot\Objects\Payments\ShippingOption;

class PaymentsHandler
{
    /** @var Api */
    protected $telegram;

    public function __construct(Api $telegram)
    {
        $this->telegram = $telegram;
    }

    public function handleShippingQuery(Update $update)
    {
        $this->telegram->answerShippingQuery([
            'shipping_query_id' => $update->shippingQuery->id,
            'ok' => true,
            'shipping_options' => $this->prepare_shipping_options(),
            'error_message' => 'Shipin error',
        ]);
    }

    public function handlePreCheckoutQuery(Update $update)
    {
        $this->telegram->answerPreCheckoutQuery([
            'pre_checkout_query_id' => $update->preCheckoutQuery->id,
            'ok' => true,
            'error_message' => 'Precheckout error',
        ]);
    }

    public function handleSuccessfulPayment(Update $update, SuccessfulPayment $successfulPayment)
    {
        $this->telegram->sendMessage([
            'chat_id' => $update->message->chat->id,
            'text' => LocationReplyAgent::print_response_string($successfulPayment),
            'parse_mode' => 'html',
        ]);
        $this->telegram->sendMessage([
            'chat_id' => $update->message->chat->id,
            'text' => 'Оплата пройшла успішно',
            'reply_markup' => StartCommand::prepare_start_keyboard(),
        ]);

        $state = config('telegram.states.startState');

        /** update state in User model */
        User::where('chat_id', $update->message->chat->id)->where('state', '!=', $state)->update(['state' => $state]);

    }

    public function prepare_shipping_options()
    {
        $shipping_options = [
            new ShippingOption([
                'id' => 'nova_poshta',
                'title' => 'Nova Poshta',
                'prices' => [
                    new LabeledPrice(['amount' => 15, 'label' => 'Delivery (NP)']),
                    new LabeledPrice(['amount' => 2, 'label' => 'Registration (NP)']),
                ],
            ]),
            new ShippingOption([
                'id' => 'dhl',
                'title' => 'DHL Express',
                'prices' => [
                    new LabeledPrice(['amount' => 20, 'label' => 'Delivery (DHL)']),
                    new LabeledPrice(['amount' => 0, 'label' => 'Registration (DHL)']),
                ],
            ]),
        ];

        return json_encode($shipping_options);
    }
}