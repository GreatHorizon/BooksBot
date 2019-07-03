<?php

use Telegram\Bot\Api;

function getTelegramData(): object {
    $telegram = new Api('680225339:AAFoHWnPG5KVG_9lD8IrbbBhqDmhYxtKyKE'); //Устанавливаем токен, полученный у BotFather
    return $telegram -> getWebhookUpdates(); 
}

function getReplyMarkup() {
    return getTelegramData()->replyKeyboardMarkup([ 'keyboard' => getKeyboard(getTelegramData()), 'resize_keyboard' => true, 'one_time_keyboard' => false ]);
}

function getText(object $result): string {
    return $result["message"]["text"]; //Текст сообщения
}

function getChatId(object $result): string {
    return $result["message"]["chat"]["id"]; //Уникальный идентификатор пользователя
}

function getUserName(object $result): string {
    return $result["message"]["from"]["username"]; //Юзернейм пользователя
}

function getKeyboard() {
    return $keyboard = [["Hello"], ["Show history"], ["Help"]]; //Клавиатура
}
