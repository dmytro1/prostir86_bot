<?php

namespace App\Telegram\ReplyAgents;

use Telegram\Bot\Keyboard\Keyboard;

class LocationReplyAgent extends AbstractReplyAgent
{
    protected $name = 'location';

    public function handle()
    {
        $message = $this->message;
        $location = $this->location;

        if ($location) {

            $reply = __('telegram.location.reply');
            $reply .= PHP_EOL . self::print_response_string($location) . PHP_EOL . PHP_EOL;
            $reply .= __('telegram.location.reply_2');

            $this->replyWithMessage([
                'text' => $reply,
                'parse_mode' => 'html',
            ]);

            $this->replyWithLocation([
                'latitude' => 40.707961,
                'longitude' => -74.009047,
            ]);
        } elseif (strpos($message, __('telegram.back')) === 0) {
            $this->back_to_start();
        } else {
            $default = new DefaultReplyAgent($this->telegram);
            $default->setUpdate($this->update);
            $default->handle();
        }
    }

    public static function prepare_location_keyboard()
    {
        $keyboard = Keyboard::make(['resize_keyboard' => true]);

        $button1 = Keyboard::button([
            'text' => __('telegram.location.button'),
            'request_location' => true,
        ]);
        $button2 = Keyboard::button([
            'text' => __('telegram.back'),
        ]);

        $keyboard->row($button1, $button2);

        return $keyboard;
    }

    /**
     * Store every object/array key=>value in output
     *
     * @var string
     */
    private static $output = '';

    /**
     * Store iteration number
     *
     * @var int
     */
    private static $iteration = 0;

    /**
     * Returns the response from Telegram API in HTML
     *
     * @param  $object_array
     *
     * @return string
     */
    public static function print_response_string($object_array)
    {
        foreach ($object_array as $key => $value) {
            if (gettype($value) == 'array' || gettype($value) == 'object') {
                self::$output .= self::add_space(self::$iteration) . "<strong>" . $key . " => " . self::what_data_type(gettype($value)) . "</strong>\r\n";
                self::$iteration++;
                self::print_response_string($value);
                self::$iteration--;
            } else {
                self::$output .= self::add_space(self::$iteration) . "<strong>" . $key . "</strong> => " . $value . "\r\n";
            }
        }

        return self::$output;
    }

    /**
     * Add white space before string
     *
     * @param $iteration
     *
     * @return string
     */
    private static function add_space($iteration)
    {
        $space = "";
        for ($i = 0; $i < $iteration; $i++) {
            $space .= "\t\t\t\t\t";
        }

        return $space;
    }

    /**
     * returns object or array brackets string
     *
     * @param $data_type
     *
     * @return string
     */
    private static function what_data_type($data_type)
    {
        return $data_type == 'array' ? '[]' : '{}';
    }
}
