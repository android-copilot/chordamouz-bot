<?php

// دریافت توکن از متغیر محیطی
$token = getenv('BOT_TOKEN');

// بررسی اینکه توکن تعریف شده یا نه
if (!$token) {
    error_log("توکن یافت نشد. لطفاً BOT_TOKEN را در محیط تعریف کن.");
    exit;
}

// دریافت داده‌های ارسالی از تلگرام
$update = json_decode(file_get_contents('php://input'), true);

// بررسی اینکه پیام دریافتی معتبره
if (isset($update['message'])) {
    $chat_id = $update['message']['chat']['id'];
    $text = $update['message']['text'];

    // اگر /start فرستاده شد، کیبورد سفارشی نشون بده
    if ($text === "/start") {
        $reply = "خوش اومدی به ChordAmouz! یکی از گزینه‌ها رو انتخاب کن:";

        $keyboard = [
            'keyboard' => [
                [['text' => 'آکوردها']],
                [['text' => 'آموزش'], ['text' => 'درباره ما']]
            ],
            'resize_keyboard' => true
        ];

        $data = [
            'chat_id' => $chat_id,
            'text' => $reply,
            'reply_markup' => json_encode($keyboard)
        ];
    } else {
        // پاسخ پیش‌فرض به پیام‌های دیگه
        $reply = "پیامت رو دریافت کردم: $text";
        $data = [
            'chat_id' => $chat_id,
            'text' => $reply
        ];
    }

    // ارسال پاسخ به کاربر
    $url = "https://api.telegram.org/bot$token/sendMessage";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_exec($ch);
    curl_close($ch);
}

?>
