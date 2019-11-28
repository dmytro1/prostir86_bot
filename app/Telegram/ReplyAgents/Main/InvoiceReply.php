<?php
/**
 * Created by PhpStorm.
 * User: imac
 * Date: 2019-07-19
 * Time: 23:29
 */

namespace App\Telegram\ReplyAgents\Main;

use App\Event;
use App\Order;
use App\Telegram\Commands\StartCommand;
use App\Telegram\ReplyAgents\AbstractReplyAgent;
use App\Telegram\ReplyAgents\DefaultReplyAgent;
use App\Telegram\ReplyAgents\PhoneReplyAgent;
use App\User;
use Telegram\Bot\Objects\Payments\LabeledPrice;

class InvoiceReply extends AbstractReplyAgent
{
    protected $name = 'payment';

    public function handle()
    {
        $message = $this->message;

        if (strpos($message, 'Перейти до оплати') === 0) {
            $state = config('telegram.states.invoiceState');

            /** update state in User model */
            //User::where('chat_id', $chat_id)->where('state', '!=', $state)->update(['state' => $state]);

            $updated_order = Order::where('user_id', $this->user_id)
                ->where('status', 'created')
                ->latest('updated_at')->first();

            if (!is_null($updated_order)) {
                $updated_order->update(['status' => 'pending_payment']);
            }

            $order = Order::where('user_id', $this->user_id)
                ->where('status', 'pending_payment')
                ->latest('updated_at')->first();

            $this->replyWithInvoice(self::prepareParams($order));

        } elseif (strpos($message, 'Відмінити замовлення') === 0) {
            $state = config('telegram.states.startState');

            /** update state in User model */
            User::where('chat_id', $this->chat_id)
                ->where('state', '!=', $state)
                ->update(['state' => $state]);

            Order::where('user_id', $this->user_id)
                ->whereIn('status', ['created', 'pending_payment'])
                ->latest('updated_at')->first()->delete();

            $this->replyWithMessage([
                'text' => 'Замовлення відмінено' . PHP_EOL . 'Виберіть повторно:',
                'reply_markup' => StartCommand::prepare_start_keyboard(),
            ]);

        } elseif (strpos($message, 'Підтвердити') === 0) {

            $state = config('telegram.states.startState');

            /** update state in User model */
            User::where('chat_id', $this->chat_id)->where('state', '!=', $state)->update(['state' => $state]);

            $this->replyWithMessage([
                'text' => 'Ура! Реєстрація пройшла успішно 🎉',
                'reply_markup' => StartCommand::prepare_start_keyboard(),
            ]);
        } elseif (strpos($message, 'Зареєструватися повторно') === 0) {
            $state = config('telegram.states.startState');

            /** update state in User model */
            User::where('chat_id', $this->chat_id)->where('state', '!=', $state)->update(['state' => $state]);

            $this->replyWithMessage([
                'text' => 'Спробуйте знову',
                'reply_markup' => StartCommand::prepare_start_keyboard(),
            ]);
        } else {
            $reply = new DefaultReplyAgent($this->telegram);
            $reply->setUpdate($this->update);
            $reply->reply_markup = PhoneReplyAgent::prepare_invoice_button();
            $reply->handle();
        }
    }

    public
    static function prepareParams($order)
    {
        $event_price = Event::find($order->event_id)->price;
        $event_title = Event::find($order->event_id)->title;
        $event_banner = Event::find($order->event_id)->banner_url;
        $quantity = $order->quantity;

        return [
            'title' => __('telegram.product.name'),
            'description' => 'Ви хочете оплатити ' . $quantity . ' квитки на ' . $event_title,
            'payload' => 'invoice123123',
            'provider_token' => '632593626:TEST:i56982357197', // test
//            'provider_token' => '635983722:LIVE:i37577844625', // live
            'start_parameter' => 'invoice_123123',
            'currency' => 'uah',
            'prices' => [new LabeledPrice([
                'amount' => round($event_price * $quantity * 100),
                'label' => $quantity . ' квитки на ' . $event_title
            ])],
            'photo_url' => $event_banner,
            'photo_width' => 90,
            'photo_height' => 50,
            'need_name' => true,
            'need_phone_number' => true,
            'need_email' => true,
            //'need_shipping_address' => true,
//            'is_flexible' => true,
//            'reply_markup' => InvoiceCallbackCommand::prepare_invoice_keyboard(),

        ];
    }
}
