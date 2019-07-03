<?php
  require 'vendor/autoload.php'; //Подключаем библиотеку;


  use Telegram\Bot\Api; 

  $telegram = new Api('680225339:AAFoHWnPG5KVG_9lD8IrbbBhqDmhYxtKyKE'); //Устанавливаем токен, полученный у BotFather
  $result = $telegram -> getWebhookUpdates(); //Передаем в переменную $result полную информацию о сообщении пользовате
  $text = $result["message"]["text"]; //Текст сообщения
  $chat_id = $result["message"]["chat"]["id"]; //Уникальный идентификатор пользователя
  $name = $result["message"]["from"]["username"]; //Юзернейм пользователя
  $keyboard = [["Say Hello"], ["Show history"]]; //Клавиатура

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
    }

    elseif ($text == "Show history")
    {
      $db = new MysqliDb ('eu-cdbr-west-02.cleardb.net', 'b5c433cc63ee73', '290309dc', 'heroku_2cd2894cd704696');
      $db->where ("user_id", $chat_id);
      $bookHistory = $db->getOne ("book_history");
      $bookHistory = array_slice($bookHistory, 1);
      foreach ($bookHistory as $books) {
        $reply .= $books . "\n";
      }

      $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply, 'reply_markup' => $reply_markup ]);
    }

    else {
      if (strpos($text, "\n")) {
        $text = explode("\n", $text);
        $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => workWithBook($text[0], $text[1], $chat_id)]);
      }

      else {
        $bookAuthor = '';
        $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => workWithBook($text, $bookAuthor, $chat_id)]);
      }
    }
  }

  function workWithBook($bookName, $bookAuthor, $chat_id) { 
    //Получаем массив с информацией о книге
    $bookName = str_replace(' ', '+', $bookName);
    if ($bookAuthor = '') {
      $bookInfo = file_get_contents('https://www.googleapis.com/books/v1/volumes?q=intitle:'. $bookName .'&maxResults=1&orderBy=relevance&key=AIzaSyALM0SWc1JdHtgpPplJ6T2k9Fwcc1dI7vk');
    }

    else
    {
      $bookInfo = file_get_contents('https://www.googleapis.com/books/v1/volumes?q=intitle:'. $bookName .'+inauthor:'. $bookAuthor .'&maxResults=1&orderBy=relevance&key=AIzaSyALM0SWc1JdHtgpPplJ6T2k9Fwcc1dI7vk');
    }

    $bookInfo = json_decode($bookInfo, true);
    
    if ($bookInfo["totalItems"] == 0) {
      return "Write correct name of book";
    }

    else {
      //Получаем определенную информацию из массива
      $bookTitle = $bookInfo["items"][0]["volumeInfo"]["title"];
      $authors = $bookInfo["items"][0]["volumeInfo"]["authors"][0];
      $bookInfo = $bookInfo["items"][0]["volumeInfo"]["infoLink"];
      addBookToHistory($bookTitle, $chat_id);
      return "Name of the book: " . $bookTitle ."\nAuthor: ". $authors . " \nMore information about this book: " . $bookInfo . "";
    }
  }
      function addBookToHistory($bookTitle, $chat_id) {
      $db = new MysqliDb ('eu-cdbr-west-02.cleardb.net', 'b5c433cc63ee73', '290309dc', 'heroku_2cd2894cd704696');
      $db->where("user_id", $chat_id);
      $$bookHistoryArray = $db->getOne('book_history');
      
      if ($record) {
        $userHistory = [
        'user_id' => $chat_id,
        'first_book_slot' => $bookHistoryArray['second_book_slot'],
        'second_book_slot' => $bookHistoryArray['third_book_slot'],
        'third_book_slot' => $bookHistoryArray['fourth_book_slot'],
        'fourth_book_slot' => $bookHistoryArray['fifth_book_slot'],
        'fifth_book_slot' => $bookTitle
        ];

        $db->where("user_id", $chat_id);
        $db->delete('book_history');
        $db->insert('book_history', $userHistory);
      }

      else {
        $newUser = [
          'user_id' => $chat_id,
          'first_book_slot' => 'empty',
          'second_book_slot' => 'empty',
          'third_book_slot' => 'empty',
          'fourth_book_slot' => 'empty',
          'fifth_book_slot' => $bookTitle
        ];
    
        $db->insert('book_history', $newUser);
      }
    }
    $string = "Мы
    Замятин";
    if (strpos($string, "\n")){
    $string = explode("\n", $string);
    var_dump($string);
    }
