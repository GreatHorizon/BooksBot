<?php
  include('vendor/autoload.php'); //Подключаем библиотеку;
  use Telegram\Bot\Api; 

  echo "Hello, world";

  $telegram = new Api('680225339:AAFoHWnPG5KVG_9lD8IrbbBhqDmhYxtKyKE'); //Устанавливаем токен, полученный у BotFather
  $result = $telegram -> getWebhookUpdates(); //Передаем в переменную $result полную информацию о сообщении пользовате
  $text = $result["message"]["text"]; //Текст сообщения
  $chat_id = $result["message"]["chat"]["id"]; //Уникальный идентификатор пользователя
  $name = $result["message"]["from"]["username"]; //Юзернейм пользователя
  $keyboard = [["Моя библиотека"], ["Найти книгу"]]; //Клавиатура

  if ($text == "/start") {
    $reply = "Добро пожаловать в бота!";
    $reply_markup = $telegram->replyKeyboardMarkup([ 'keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false ]);
    echo "aaaasd";
    $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply, 'reply_markup' => $reply_markup ]);

  }

  elseif ($text == "/help") {
    $reply = "Информация с помощью.";
    $reply_markup = $telegram->replyKeyboardMarkup([ 'keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false ]);
    echo "aaa";
    $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply, 'reply_markup' => $reply_markup ]);

  }
?>