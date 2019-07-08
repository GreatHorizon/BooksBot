<?php

  use Telegram\Bot\Api;
  require_once('const.php');

  $telegram = new Api(apiToken);
  $keyboard = [["Моя библиотека"], ["Найти книгу"]];
  $libraryKeyboard = [["Показать библиотеку"], ["Добавить книгу"], ["Удалить книгу"],["Очистить библиотеку"], ["Назад"]];
  $chatId = getChatId(getTelegramData($telegram));
  $name = getUserName(getTelegramData($telegram));
  $text = getText(getTelegramData($telegram));
  $replyMarkup = getReplyMarkup($keyboard, $telegram);
  $libraryKeyboardMarkUp = getReplyMarkup($libraryKeyboard, $telegram);
  
 
  
  
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
  
  function sendNewMessage($chatId, $reply, $replyMarkup, $telegram) {
      $telegram->sendMessage(['chat_id' => $chatId, 'text' => $reply, 'reply_markup' => $replyMarkup]);
    }
