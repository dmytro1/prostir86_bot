<?php
/**
 * Created by PhpStorm.
 * User: dmytrov
 * Date: 18.03.2019
 * Time: 16:18
 */

namespace App\Telegram\Core;

use App\Order;
use App\OrderDetail;
use App\Telegram\Commands\StartCommand;
use App\Telegram\ReplyAgents\LocationReplyAgent;
use App\Transaction;
use App\User;
use Telegram\Bot\Api;
use Telegram\Bot\Objects\Payments\SuccessfulPayment;
use Telegram\Bot\Objects\Update;

class PaymentsHandler
{
    /** @var Api */
    protected $telegram;

    public function __construct(Api $telegram)
    {
        $this->telegram = $telegram;
    }

    public function handlePreCheckoutQuery(Update $update)
    {

        // Update status in Orders
        $user_id = User::where('chat_id', $update->preCheckoutQuery->from->id)->value('id');
        Order::where('user_id', $user_id)
            ->where('status', 'pending_payment')
            ->latest('updated_at')->first()
            ->update(['status' => 'pending_payment_pre']);

        /* Check if everything with OK with checkout and shipping */
        $result = 'if everything is true' ? true : false;

        $this->telegram->answerPreCheckoutQuery([
            'pre_checkout_query_id' => $update->preCheckoutQuery->id,
            'ok' => $result,
            'error_message' => 'Precheckout error',
        ]);
    }

    public function handleSuccessfulPayment(Update $update, SuccessfulPayment $successfulPayment)
    {
        $chat_id = $update->message->chat->id;
        $orderInfo = $successfulPayment->orderInfo;

        // In currency
        $total_amount = $successfulPayment->totalAmount / 100;

        // Update status in Orders
        $user_id = User::where('chat_id', $chat_id)->value('id');
        $update_order = Order::where('user_id', $user_id)
            ->where('status', 'pending_payment_pre')
            ->latest('updated_at')->first();

        if (!is_null($update_order)) {
            $update_order->update(['status' => 'completed']);
        }

        // Update Transaction
        $order = Order::where('user_id', $user_id)
            ->where('status', 'completed')
            ->latest('updated_at')->first();

        Transaction::create([
            'user_id' => $user_id,
            'order_id' => $order->id,
            'status' => 'successful',
            'total_amount' => $total_amount
        ]);

        OrderDetail::create([
            'order_id' => $order->id,
            'name' => $orderInfo->name,
            'phone_number' => $orderInfo->phoneNumber,
            'email' => $orderInfo->email
        ]);

        /** update state in User model */
        $state = config('telegram.states.startState');
        User::where('chat_id', $chat_id)
            ->where('state', '!=', $state)
            ->update(['state' => $state]);

        $this->telegram->sendMessage([
            'chat_id' => $chat_id,
            'text' => LocationReplyAgent::print_response_string($successfulPayment),
            'parse_mode' => 'html',
        ]);
        $this->telegram->sendMessage([
            'chat_id' => $update->message->chat->id,
            'text' => 'Оплата пройшла успішно',
            'reply_markup' => StartCommand::prepare_start_keyboard(),
        ]);

    }
}