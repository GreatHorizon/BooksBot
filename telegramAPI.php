<?php

use Telegram\Bot\Api;

const apiToken = "680225339:AAFoHWnPG5KVG_9lD8IrbbBhqDmhYxtKyKE";
$keyboard = [["Hello"], ["Show history"], ["Help"]];

function getTelegramData() {
    $telegram = new Api(apiToken); //Устанавливаем токен, полученный у BotFather
    return $telegram -> getWebhookUpdates(); 
}

function getReplyMarkup($keyboard) {
    return getTelegramData()->replyKeyboardMarkup([ 'keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false ]);
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

