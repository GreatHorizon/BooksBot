<?php

  use Telegram\Bot\Api;
  $telegram = new Api(apiToken);
  const apiToken = "680225339:AAFoHWnPG5KVG_9lD8IrbbBhqDmhYxtKyKE";
 
  
  
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
