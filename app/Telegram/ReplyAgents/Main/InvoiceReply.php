<?php
/**
 * Created by PhpStorm.
 * User: imac
 * Date: 2019-07-19
 * Time: 23:29
 */

namespace App\Telegram\ReplyAgents\Main;

use App\Telegram\CallbackCommands\InvoiceCallbackCommand;
use App\Telegram\ReplyAgents\AbstractReplyAgent;
use Telegram\Bot\Objects\Payments\LabeledPrice;

class InvoiceReply extends AbstractReplyAgent
{
    public function handle()
    {
        $state = config('telegram.states.invoiceState');

        /** update state in User model */
        //User::where('chat_id', $chat_id)->where('state', '!=', $state)->update(['state' => $state]);

        $this->replyWithInvoice(self::prepareParams());
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
            'prices' => [new LabeledPrice(['amount' => 10, 'label' => __('telegram.product.label')])],
            'photo_url' => 'https://www.leehealthwellbeing.com.au/wp-content/uploads/2016/02/graphic_product_tangible.png',
            'photo_width' => 90,
            'photo_height' => 50,
            //'need_name' => true,
            //'need_phone_number' => true,
            //'need_email' => true,
            //'need_shipping_address' => true,
            'is_flexible' => true,
            'reply_markup' => InvoiceCallbackCommand::prepare_invoice_keyboard(),

        ];
    }
}
