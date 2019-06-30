<?php
  require 'vendor/autoload.php'; //Подключаем библиотеку;



  use Telegram\Bot\Api; 

  $telegram = new Api('680225339:AAFoHWnPG5KVG_9lD8IrbbBhqDmhYxtKyKE'); //Устанавливаем токен, полученный у BotFather
  $result = $telegram -> getWebhookUpdates(); //Передаем в переменную $result полную информацию о сообщении пользовате
  $text = $result["message"]["text"]; //Текст сообщения
  $chat_id = $result["message"]["chat"]["id"]; //Уникальный идентификатор пользователя
  $name = $result["message"]["from"]["username"]; //Юзернейм пользователя
  $keyboard = [["My library"], ["Search book by name"], ["Say Hello"]]; //Клавиатура

  if ($text) {
    
    if ($text == "Say Hello" or $text == "/start") {

      if ($name != "") {
        $reply = "Hello, ". $name . "!";
      }
    
      else {
        $reply = "Hello, stranger!";
      }

      $reply_markup = $telegram->replyKeyboardMarkup([ 'keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false ]);
      $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply, 'reply_markup' => $reply_markup ]);
    }
    elseif ($text == "Search book by name") {
      $reply = "Write name of book";
      $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply, 'reply_markup' => $reply_markup ]);
      $writeBookName = true;
    }
    elseif ($text == "My library") {
      // TODO: implement method
    }
    else {
      $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => searchBook($text)]);
    }
  }

  function searchBook($bookName) { 
    //Получаем массив с информацией о книге
    $bookName = str_replace(' ', '+', $bookName);
    $bookInfo = file_get_contents('https://www.googleapis.com/books/v1/volumes?q=intitle:'.$bookName.'&maxResults=1&key=AIzaSyALM0SWc1JdHtgpPplJ6T2k9Fwcc1dI7vk');
    $bookInfo = json_decode($bookInfo, true);

    if ($bookInfo["totalItems"] == 0) {
      return "Write correct name of book";
    }

    else {
      //Получаем определенную информацию из массива
      $bookTitle = $bookInfo["items"][0]["volumeInfo"]["title"];
      $authors = $bookInfo["items"][0]["volumeInfo"]["authors"][0];
      $bookInfo = $bookInfo["items"][0]["volumeInfo"]["infoLink"];

      return "Name of the book: " . $bookTitle ."\nAuthor: ". $authors . " \nMore information about this book: " . $bookInfo . "";
    }
  }
  
  $db = new MysqliDb ('eu-cdbr-west-02.cleardb.net', 'b5c433cc63ee73', '290309dc', 'heroku_2cd2894cd704696');
  
  $data = [
    "username" => "admiфывыв",
    "email" => "John",
    "password" => 'Doe'
  ];

  $id = $db->insert ('user', $data);
  if ($id)
  {
    echo 'user was created. Id=' . $id;
  }

?>