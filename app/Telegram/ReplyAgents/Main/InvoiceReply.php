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
use App\Telegram\ReplyAgents\AbstractReplyAgent;
use App\Telegram\ReplyAgents\DefaultReplyAgent;
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
                ->latest()->first();

            if (!is_null($updated_order)) {
                $updated_order->update(['status' => 'pending_payment']);
            }

            $order = Order::where('user_id', $this->user_id)
                ->where('status', 'pending_payment')
                ->latest()->first();

            $this->replyWithInvoice(self::prepareParams($order));

        } else {
            $reply = new DefaultReplyAgent($this->telegram);
            $reply->setUpdate($this->update);
            $reply->handle();
        }
    }

    public static function prepareParams($order)
    {
        $event_price = Event::find($order->event_id)->price;
        $event_title = Event::find($order->event_id)->title;
        $event_banner = Event::find($order->event_id)->banner_url;
        $quantity = $order->quantity;

        return [
            'title' => __('telegram.product.name'),
            'description' => 'Ви хочете оплатити ' . $quantity . ' квитки на ' . $event_title,
            'payload' => 'invoice123123',
            'provider_token' => '632593626:TEST:i56982357197',
            'start_parameter' => 'invoice_123123',
            'currency' => 'usd',
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
