<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);


$token = getenv('BOT_TOKEN');


$content = file_get_contents("php://input");
file_put_contents("log.txt", "RAW INPUT:\n" . $content . "\n\n", FILE_APPEND);

if (!$content) {
    file_put_contents("log.txt", "No input received\n\n", FILE_APPEND);
    exit("No input received");
}

$update = json_decode($content, true);

if (!isset($update["message"])) {
    file_put_contents("log.txt", "No message in input\n\n", FILE_APPEND);
    exit("No message in input");
}

$chat_id = $update["message"]["chat"]["id"];
$text = $update["message"]["text"] ?? "";

file_put_contents("log.txt", "Chat ID: $chat_id\nText: $text\n", FILE_APPEND);


if ($text === "/start") {
    $keyboard = [
        'keyboard' => [
            [['text' => 'آکوردها']],
            [['text' => 'آموزش'], ['text' => 'درباره ما']]
        ],
        'resize_keyboard' => true
    ];

    $reply = "خوش اومدی به ChordAmouz! یکی از گزینه‌ها رو انتخاب کن:";

    $data = [
        'chat_id' => $chat_id,
        'text' => $reply,
        'reply_markup' => json_encode($keyboard)
    ];
} else {
    $reply = "Check menu: " . $text;
}

file_put_contents("log.txt", "Reply: $reply\n", FILE_APPEND);

sendMessage($chat_id, $reply);

function sendMessage($chat_id, $text) {
    global $token;
    $url = "https://api.telegram.org/bot$token/sendMessage";

    $data = [
        'chat_id' => $chat_id,
        'text' => $text
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    file_put_contents("log.txt", "HTTP CODE: $httpCode\nResult: $result\nCURL ERROR: $curlError\n---\n", FILE_APPEND);
}
?>
