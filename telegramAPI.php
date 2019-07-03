<?php

use Telegram\Bot\Api;

function getTelegramData(): object {
    $telegram = new Api('680225339:AAFoHWnPG5KVG_9lD8IrbbBhqDmhYxtKyKE'); //Устанавливаем токен, полученный у BotFather
    return $telegram -> getWebhookUpdates(); 
}

function getReplyMarkup() {
    return getTelegramData()->replyKeyboardMarkup([ 'keyboard' => getKeyboard(getTelegramData()), 'resize_keyboard' => true, 'one_time_keyboard' => false ]);
}

function getText(aray $result): string {
    return $result["message"]["text"]; //Текст сообщения
}

function getChatId(aray $result): string {
    return $chatId = $result["message"]["chat"]["id"]; //Уникальный идентификатор пользователя
}

function getUserName(aray $result): string {
    return $name = $result["message"]["from"]["username"]; //Юзернейм пользователя
}

function getKeyboard(): string {
    return $keyboard = [["Hello"], ["Show history"], ["Help"]]; //Клавиатура
}
