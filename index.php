<?php
  require 'vendor/autoload.php';
  require_once('telegramAPI.php');
  require_once('database.php');
  require_once('const.php');
  
  if ($text) {
    if ($text == startDialog) {

      if ($name != "") {
        $reply = WELCOMING . $name . "!";
      }

      else {
        $reply = WELCOMING . ", Незнакомец!";
      }

      sendNewMessage($chatId, $reply, $replyMarkup, $telegram);
      deleteInfo(COMMANDS_TABLE, $chatId);
    }

    elseif ($text == MY_LIBRARY_COMMAND) {
      $reply = "Выберите команду";
      sendNewMessage($chatId, $reply, $libraryKeyboardMarkUp, $telegram);
      deleteInfo(COMMANDS_TABLE, $chatId);
    }

    elseif ($text == "Показать библиотеку") {
      deleteInfo(COMMANDS_TABLE, $chatId);
      $bookHistory = getInfoFromTable(bookHistoryTable, $chatId);
      $bookHistory = array_slice($bookHistory, 1);
      $reply = emptyLibraryReply;
      foreach ($bookHistory as $books) {
        if ($books != emptyField) {
          $reply = $books;
          sendNewMessage($chatId, $reply, $replyMarkup, $telegram);
        }
      }
      if ($reply == emptyLibraryReply) {
        sendNewMessage($chatId, $reply, $replyMarkup, $telegram);
      }
    }

    elseif ($text == "Очистить библиотеку") {
      deleteInfo(bookHistoryTable, $chatId);
      $reply = "Библиотека очищена!";
      sendNewMessage($chatId, $reply, $replyMarkup, $telegram);
    }

    elseif ($text == "Назад") {
      $reply = "Выберите команду";
      deleteInfo(COMMANDS_TABLE, $chatId);
      sendNewMessage($chatId, $reply, $replyMarkup, $telegram);
    }

    elseif ($text == "Найти книгу") {
      updateCommand(COMMANDS_TABLE, $chatId, "search");
      $reply = "Этот бот может найти книгу по названию(для этого введите название книги)\nДля большей точности в поиске, в первую строку введите название книги, во второй автора, таким образом:\nМы\nЗамятин";
      sendNewMessage($chatId, $reply, $replyMarkup, $telegram);
    }

    elseif ($text == "Добавить книгу") {
      updateCommand(COMMANDS_TABLE, $chatId, "add");
      $reply = "Какую книгу вы хотите добавить?";
      sendNewMessage($chatId, $reply, $libraryKeyboardMarkUp, $telegram);
    }

    elseif ($text == "Удалить книгу") {
      updateCommand(COMMANDS_TABLE, $chatId, "remove");
      $reply = "Какую книгу вы хотите удалить?";
      sendNewMessage($chatId, $reply, $libraryKeyboardMarkUp, $telegram);
    }

    else {
      $commands = getInfoFromTable(COMMANDS_TABLE, $chatId)["command"];
      if ($commands == "add" or $commands == "search" or $commands == "remove") {
        if (strpos($text, lineBreak)) {
          $text = explode(lineBreak, $text);
          $reply = getResponseText($text[0], $text[1], $chatId, $commands);
        }
        else {
          $bookAuthor = emptySrting;
          $reply = getResponseText($text, $bookAuthor, $chatId, $commands);
        }
      }

      else {
        $reply = "Выберите команду";
      }
      sendNewMessage($chatId, $reply, $replyMarkup, $telegram);
    }
  }

  function getResponseText($bookName, $bookAuthor, $chatId, $commands) { 
    $bookInfo = getBookInfo($bookName, $bookAuthor, $chatId);
    
    if ($bookInfo["totalItems"] == 0) {
      return bookSearchWarning;
    }

    else {
      $bookTitle = $bookInfo["items"][0]["volumeInfo"]["title"];
      $authors = $bookInfo["items"][0]["volumeInfo"]["authors"][0];
      $bookInfo = $bookInfo["items"][0]["volumeInfo"]["infoLink"];

      if ($commands == "add") {
        $booksArray = getInfoFromTable(bookHistoryTable, $chatId);
        foreach ($booksArray as $book) {
          if ($book == $bookInfo){
            deleteInfo(COMMANDS_TABLE, $chatId);
            return "Такая книга уже есть в библиотеке";
          }
        }

        addBookToHistory($bookInfo, $chatId);
        deleteInfo(COMMANDS_TABLE, $chatId);
        return "Вы успешно добавили книгу в библиотеку!"; 
      }
      elseif ($commands == "remove") {
        $booksArray = getInfoFromTable(bookHistoryTable, $chatId);
        deleteInfo(bookHistoryTable, $chatId);
        $deleteBook = false;
        foreach ($booksArray as $book) {
          if ($book == $bookInfo)
          {
            $booksArray[key($booksArray)] = emptyField;
            $deleteBook = true;
          }
          next($booksArray);
        }
        insertToBase(bookHistoryTable, $booksArray);
        deleteInfo(COMMANDS_TABLE, $chatId);
        if ($deleteBook) {
          return "Книга успешно удалена из библиотеки!";
        }
        else {
          return "Такой книги нет в вашей библиотеке!";
        }
        
      }
      else {
        deleteInfo(COMMANDS_TABLE, $chatId);
        return "Название: " . $bookTitle ."\nАвтор: ". $authors . " \nУзнать больше информации о книге: " . $bookInfo . "";
      }
    }
  }

  function getBookInfo($bookName, $bookAuthor, $chatId) { 
    $bookName = str_replace(' ', '+', $bookName);
    if ($bookAuthor == '') {
      $bookInfo = file_get_contents('https://www.googleapis.com/books/v1/volumes?q=intitle:'. $bookName .'&maxResults=1&orderBy=relevance&key=AIzaSyALM0SWc1JdHtgpPplJ6T2k9Fwcc1dI7vk');
    }
    else {
      $bookInfo = file_get_contents('https://www.googleapis.com/books/v1/volumes?q=intitle:'. $bookName .'+inauthor:'. $bookAuthor .'&maxResults=1&orderBy=relevance&key=AIzaSyALM0SWc1JdHtgpPplJ6T2k9Fwcc1dI7vk');
    }
    return json_decode($bookInfo, true);
  }