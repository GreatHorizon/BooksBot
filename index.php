<?php
  require 'vendor/autoload.php';
  require_once('const.php');
  require_once('telegramAPI.php');
  require_once('database.php');
  require_once('googleAPI.php');
  
  if ($text) {
    if ($text == startDialog) {

      if ($name != emptySrting) {
        $reply = welcoming . $name . "!";
      }

      else {
        $reply = welcoming . ", Незнакомец!";
      }
      sendNewMessage($chatId, $reply, $replyMarkup, $telegram);
      deleteInfo("commands", $chatId);
    }

    elseif ($text == myLibrary) {
      $reply = "Выберите функцию";
      sendNewMessage($chatId, $reply, $libraryKeyboardMarkUp, $telegram);
      deleteInfo("commands", $chatId);
    }

    elseif ($text == "Показать библиотеку") {
      deleteInfo("commands", $chatId);
      showLibrary($chatId, $replyMarkup, $telegram);
    }

    elseif ($text == "Очистить библиотеку") {
      deleteInfo(bookHistoryTable, $chatId);
      $reply = "Библиотека очищена!";
      sendNewMessage($chatId, $reply, $replyMarkup, $telegram);
    }

    elseif ($text == "Назад") {
      $reply = "Choose command";
      deleteInfo("commands", $chatId);
      sendNewMessage($chatId, $reply, $replyMarkup, $telegram);
    }

    elseif ($text == "Найти книгу") {
      updateCommand("commands", $chatId, "search");
      $reply = "Этот бот может найти книгу по названию(для этого введите название книги)\nДля большей точности в поиске, в первую строку введите название книги, во второй автора, таким образом:\nМы\nЗамятин";
      sendNewMessage($chatId, $reply, $replyMarkup, $telegram);
    }

    elseif ($text == "Добавить книгу") {
      updateCommand("commands", $chatId, "add");
      $reply = "Какую книгу вы хотите добавить?";
      sendNewMessage($chatId, $reply, $libraryKeyboardMarkUp, $telegram);
    }

    elseif ($text == "Удалить книгу") {
      updateCommand("commands", $chatId, "remove");
      $reply = "Какую книгу вы хотите удалить?";
      sendNewMessage($chatId, $reply, $libraryKeyboardMarkUp, $telegram);
    }

    else {
      $commands = getInfoFromTable("commands", $chatId)["command"];
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
            deleteInfo("commands", $chatId);
            return "Такая книга уже есть в библиотеке";
          }
        }
        addBookToHistory($bookInfo, $chatId);
        deleteInfo("commands", $chatId);
        return "Вы успешно добавили книгу в библиотеку!"; 
      }

      elseif ($commands == "remove") {
        $booksArray = getInfoFromTable(bookHistoryTable, $chatId);
        deleteInfo(bookHistoryTable, $chatId);
        $deleteBook = false;
        if ($booksArray) {
          foreach ($booksArray as $book) {
            if ($book == $bookInfo)
            {
              $booksArray[key($booksArray)] = emptyField;
              $deleteBook = true;
            }
            next($booksArray);
          }
          insertToBase(bookHistoryTable, $booksArray);
          deleteInfo("commands", $chatId);
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
        deleteInfo("commands", $chatId);
        return "Название: " . $bookTitle ."\nАвтор: ". $authors . " \nУзнать больше информации о книге: " . $bookInfo . "";
      }
    }
  }

  