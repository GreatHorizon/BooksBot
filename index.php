<?php
  require 'vendor/autoload.php';
  require_once('const.php');
  require_once('telegramAPI.php');
  require_once('database.php');
  require_once('googleAPI.php');
  
  if ($text) {
    if ($text == startDialog) {

      if ($name != EMPTY_STRING) {
        $reply = welcoming . $name . "!";
      }

      else {
        $reply = welcoming . ", Незнакомец!";
      }
      sendNewMessage($chatId, $reply, $replyMarkup, $telegram);
      deleteInfo(COMMANDS_TABLE, $chatId);
    }

    elseif ($text == myLibrary) {
      $reply = "Выберите функцию";
      sendNewMessage($chatId, $reply, $libraryKeyboardMarkUp, $telegram);
      deleteInfo(COMMANDS_TABLE, $chatId);
    }

    elseif ($text == "Показать библиотеку") {
      deleteInfo(COMMANDS_TABLE, $chatId);
      showLibrary($chatId, $replyMarkup, $telegram);
    }

    elseif ($text == "Очистить библиотеку") {
      deleteInfo(BOOK_LIBRARY_TABLE, $chatId);
      $reply = "Библиотека очищена!";
      sendNewMessage($chatId, $reply, $replyMarkup, $telegram);
    }

    elseif ($text == "Назад") {
      $reply = "Choose command";
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
        if (strpos($text, LINE_BREAK)) {
          $text = explode(LINE_BREAK, $text);
          $reply = getResponseText($text[0], $text[1], $chatId, $commands);
        }
        else {
          $bookAuthor = EMPTY_STRING;
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
        $booksArray = getInfoFromTable(BOOK_LIBRARY_TABLE, $chatId);
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
        $booksArray = getInfoFromTable(BOOK_LIBRARY_TABLE, $chatId);
        deleteInfo(BOOK_LIBRARY_TABLE, $chatId);
        $deleteBook = false;
        if ($booksArray) {
          foreach ($booksArray as $book) {
            if ($book == $bookInfo)
            {
              $booksArray[key($booksArray)] = EMPTY_FIELD;
              $deleteBook = true;
            }
            next($booksArray);
          }
          insertToBase(BOOK_LIBRARY_TABLE, $booksArray);
          deleteInfo(COMMANDS_TABLE, $chatId);
          if ($deleteBook) {
            return "Книга успешно удалена из библиотеки!";
          }
          else {
            return "Такой книги нет в вашей библиотеке!";
          }
        }
        else {
          return "Ваша библиотека пуста!";
        }
      }
      else {
        deleteInfo(COMMANDS_TABLE, $chatId);
        return "Название: " . $bookTitle ."\nАвтор: ". $authors . " \nУзнать больше информации о книге: " . $bookInfo . "";
      }
    }
  }

  