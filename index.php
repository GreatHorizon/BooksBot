<?php
  require 'vendor/autoload.php';
  require_once('database.php');
  require_once('telegramAPI.php');

  const lineBreak = "\n";
  const emptySrting = "";
  const blank = " ";
  const plus = "+";
  const welcoming = "Hello, ";
  const hello = "Hello";
  const startDialog = "/start";
  const help = "Help";
  const botOpportunities = "This bot can find books by title.\n If you want to find more accurately, you should enter title and author in the way:\nМы\nЗамятин";
  const myLibrary = "My library";
  const bookSearchWarning = "Write correct title";
  const emptyHistoryReply = "Your library is empty now!";

  $chatId = getChatId(getTelegramData($telegram));
  $name = getUserName(getTelegramData($telegram));
  $text = getText(getTelegramData($telegram));
  $replyMarkup = getReplyMarkup($keyboard, $telegram);
  $libraryKeyboardMarkUp = getReplyMarkup($libraryKeyboard, $telegram);

  if ($text) {
    if ($text == hello or $text == startDialog) {

      if ($name != emptySrting) {
        $reply = welcoming . $name . "!";
      }

      else {
        $reply = welcoming . ", stranger!";
      }
      sendNewMessage($chatId, $reply, $replyMarkup, $telegram);
    }

    elseif ($text == help) {
      $reply = botOpportunities;
      sendNewMessage($chatId, $reply, $replyMarkup, $telegram);
    }

    elseif ($text == myLibrary)
    {
      $reply = "Choose function";
      sendNewMessage($chatId, $reply, $libraryKeyboardMarkUp, $telegram);
    }
    elseif ($text == "Show library") {
      $db = getBd();
      $db->where (userId, $chatId);
      $bookHistory = $db->getOne (bookHistoryTable);
      if (!$bookHistory) {
        $reply = emptyHistoryReply;
        sendNewMessage($chatId, $reply, $replyMarkup, $telegram);
      }
      else {
        $bookHistory = array_slice($bookHistory, 1);
        $reply = '';
        foreach ($bookHistory as $books) {
          if ($books != emptyField) {
            $bookPosition = 1;
            $reply = $i . $books;
            sendNewMessage($chatId, $reply, $replyMarkup, $telegram);
            $i++;

          }
        }
      }
    }
    elseif ($text == "Back") {
      $reply = "Choose command";
      sendNewMessage($chatId, $reply, $replyMarkup, $telegram);
    }

    elseif ($text == "Search")
    {
      addCommand($chatId, "search");
      $reply = "Enter book title";
      sendNewMessage($chatId, $reply, $replyMarkup, $telegram);
    }

    elseif ($text == "Add book") {
      $userCommands = getInfoFromTable("commands", $chatId);
      if ($userCommands) {
        deleteUserInfo("commands", $chatId);
      }
      addCommand($chatId, "add");
      $reply = "Enter book title";
      sendNewMessage($chatId, $reply, $replyMarkup, $telegram);
    }
    elseif ($text == "Remove book") {

    }
    else {
      $commands = getInfoFromTable("commands", $chatId)["command"];
      if ($commands == "add" or $commands == "search") {
        if (strpos($text, lineBreak)) {
          $text = explode(lineBreak, $text);
          $reply = getResponseText($text[0], $text[1], $chatId, $commands);
        }
        else {
          $bookAuthor = emptySrting;
          $reply = getResponseText($text, $bookAuthor, $chatId, $commands);
        }
        if ($commands == "search") {
          sendNewMessage($chatId, $reply, $replyMarkup, $telegram);
        }
        elseif ($commands == "add") {
          $reply = "You have just added book to your library!";
          sendNewMessage($chatId, $reply, $replyMarkup, $telegram);
        }
      }
      else {
        $reply = "You should choose command";
        sendNewMessage($chatId, $reply, $replyMarkup, $telegram);
      }
    }
  }

  function getResponseText($bookName, $bookAuthor, $chatId, $commands) { 
    $bookInfo = getBookInfo($bookName, $bookAuthor, $chatId);
    deleteUserInfo("commands", $chatId);
    if ($bookInfo["totalItems"] == 0) {
      return bookSearchWarning;
    }
    else {
      $bookTitle = $bookInfo["items"][0]["volumeInfo"]["title"];
      $authors = $bookInfo["items"][0]["volumeInfo"]["authors"][0];
      $bookInfo = $bookInfo["items"][0]["volumeInfo"]["infoLink"];
      if ($commands == "add") {
        addBookToHistory($bookInfo, $chatId);
      }
      return "Name of the book: " . $bookTitle ."\nAuthor: ". $authors . " \nMore information about this book: " . $bookInfo . "";
    }
  }

  function getBookInfo($bookName, $bookAuthor, $chatId) { 
    $bookName = str_replace(' ', '+', $bookName);
    if ($bookAuthor == emptySrting) {
      $bookInfo = file_get_contents('https://www.googleapis.com/books/v1/volumes?q=intitle:'. $bookName .'&maxResults=1&orderBy=relevance&key=AIzaSyALM0SWc1JdHtgpPplJ6T2k9Fwcc1dI7vk');
    }
    else
    {
      $bookInfo = file_get_contents('https://www.googleapis.com/books/v1/volumes?q=intitle:'. $bookName .'+inauthor:'. $bookAuthor .'&maxResults=1&orderBy=relevance&key=AIzaSyALM0SWc1JdHtgpPplJ6T2k9Fwcc1dI7vk');
    }
    return json_decode($bookInfo, true);
  }  
  function addCommand($chatId, $command) {
    $command = [
      "user_id" => $chatId,
      "command" => $command,
    ];
    insertToBase("commands", $command);
  }