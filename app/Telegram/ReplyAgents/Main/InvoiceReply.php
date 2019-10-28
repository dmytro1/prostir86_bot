<?php
/**
 * Created by PhpStorm.
 * User: imac
 * Date: 2019-07-19
 * Time: 23:29
 */

namespace App\Telegram\ReplyAgents\Main;

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

            Order::where('user_id', $this->user_id)
                ->where('status', 'created')
                ->update(['status' => 'pending_payment']);

            $this->replyWithInvoice(self::prepareParams());

        } else {
            $reply = new DefaultReplyAgent($this->telegram);
            $reply->setUpdate($this->update);
            $reply->handle();
        }
    }

    public static function prepareParams()
    {
        return [
            'title' => __('telegram.product.name'),
            'description' => __('telegram.product.description'),
            'payload' => 'invoice123123',
            'provider_token' => '632593626:TEST:i56982357197',
            'start_parameter' => 'invoice_123123',
            'currency' => 'usd',
            'prices' => [new LabeledPrice(['amount' => 12099, 'label' => __('telegram.product.label')])],
            'photo_url' => 'https://prostir86.com/wp-content/uploads/2019/10/72471166_3571177489562661_3507589143695720448_n.jpg',
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
