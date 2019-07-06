<?php

use Telegram\Bot\Api;

const apiToken = "680225339:AAFoHWnPG5KVG_9lD8IrbbBhqDmhYxtKyKE";
$keyboard = [["Hello"], ["My library"], ["Help"]];
$libraryKeyboard = [["Show library"], ["Add book"], ["Remove book"]];

$telegram = new Api(apiToken);

function getTelegramData($telegram) {
    return $telegram -> getWebhookUpdates(); 
}

function getReplyMarkup($keyboard, $telegram) {
    return $telegram->replyKeyboardMarkup([ 'keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false ]);
}

function getText($result) {
    return $result["message"]["text"]; //Текст сообщения
}

function getChatId($result) {
    return $result["message"]["chat"]["id"]; //Уникальный идентификатор пользователя
}

function getUserName($result) {
    return $result["message"]["from"]["username"]; //Юзернейм пользователя
}

