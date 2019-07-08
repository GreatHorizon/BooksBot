<?php

  use Telegram\Bot\Api;
  require_once('const.php');

  
  $keyboard = [["Моя библиотека"], ["Найти книгу"]];
  $libraryKeyboard = [["Показать библиотеку"], ["Добавить книгу"], ["Удалить книгу"],["Очистить библиотеку"], ["Назад"]];

  $telegram = new Api(API_TOKEN);
  $chatId = getChatId(getTelegramData($telegram));
  $name = getUserName(getTelegramData($telegram));
  $text = getText(getTelegramData($telegram));
  $replyMarkup = getReplyMarkup($keyboard, $telegram);
  $libraryKeyboardMarkUp = getReplyMarkup($libraryKeyboard, $telegram);
  
  function getTelegramData(Api $telegram) {
      return $telegram -> getWebhookUpdates();
  }
  
  function getReplyMarkup(Array $keyboard, Api $telegram): string{
      return $telegram->replyKeyboardMarkup([ 'keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false ]);
  }
  
  function getText(object $result): ?string {
      return $result["message"]["text"];
  }
  
  function getChatId(object $result): ?int {
      return $result["message"]["chat"]["id"]; 
  }
  
  function getUserName(object $result): ?string {
      return $result["message"]["from"]["username"]; 
  }
  
  function sendNewMessage(int $chatId, string $reply, ?string $replyMarkup, Api $telegram) {
      $telegram->sendMessage(['chat_id' => $chatId, 'text' => $reply, 'reply_markup' => $replyMarkup]);
    }
