<?php

return [

    'start' => 'Привіт, :firstName! Ласкаво просимо до Prostir86 bot! Виберіть івент на який можна зареєструватися.',
    'start2' => 'Введіть своє ім\'я і відправте нам:' ,

    'events' => [
      'event1' => 'Fashion week 2019',
      'event2' => 'Design week 2020',
    ],

    'start_keyboard' => [
        'shop' => 'Store',
        'invoice' => 'Invoice',
        'inline' => 'Inline',
        'location' => 'Location',
        'settings' => 'Settings',
        'instant' => 'Instant View',
        'login' => 'Login widget',
        'contact' => 'Contact',
        'users_list' => 'Users list',
    ],

    'back' => 'Back',

    'settings_keyboard' => [
        'change' => 'Change language',
        'change_message' => 'Choose language:',
        'address' => 'Address',
        'write_addr' => 'Write your address:',
        'addr_message' => 'Your address updated: ',
        'wrong_addr' => 'Please write correct address',
    ],

    'settings_msg' => 'You are in settings',

    'languages' => [
        'en' => 'English',
        'uk' => 'Українська',
    ],

    'languages_msg' => 'English is set',

    'inline_msg' => 'You are in inline mode',

    'inline_keyboard' => [
        'inline_notification' => ' :operator 1; current value: ',
        'inline_alert' => 'Negative not allowed',
    ],

    'invoice' => [
        'pay_button' => 'Pay !!!',
        'discard_button' => 'Discard',
        'discard_text' => 'Current invoice was discarded',
        'inline_notification' => 'Discard with invoice ...',
    ],

    'product' => [
        'name' => 'Sand payment',
        'description' => 'Here you could test payments for goods and services from users.',
        'label' => 'Some product/service',
    ],

    'location' => [
        'message' => 'You could share current location',
        'button' => 'Share location',
        'reply' => 'Your location is:',
        'reply_2' => 'I could send location to you also:',
    ],

    'contact' => [
        'message' => 'Do you want your own bot?' . PHP_EOL . 'Write here: @dmytrov1',
    ],

    'shop' => [
        'cart' => 'Cart',
        'empty' => 'Empty cart',
        'empty_message' => 'Your cart is empty',
        'add_btn' => '🛍 Add to cart',
        'add_message' => "Product (:product_id) added to cart",
        'description' => 'Your want to order: ' .
            PHP_EOL . PHP_EOL . ':text' . PHP_EOL .
            'Total order: ' . ':sum$' . PHP_EOL . PHP_EOL .
            'Please check your order before pay.' . PHP_EOL .
            'Click "Pay!" to continue payment',
        'checkout_btn' => '🛒 In cart :quantity item(s); sum: :sum$',
    ],

    'instant' => '<a href="https://telegra.ph/Example-of-Instant-views-in-Telegram-06-10">Example</a> of Instant View',

    'login_message_text' => 'This is a demonstration of Login with @the_introduction_bot. It works from channel as well:' .
        PHP_EOL . 'https://t.me/the_introduction_channel',

];
