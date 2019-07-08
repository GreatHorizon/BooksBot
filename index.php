<?php
  require 'vendor/autoload.php';
  require_once('const.php');
  require_once('telegramAPI.php');
  require_once('database.php');
  require_once('googleAPI.php');
  
  if ($text) {
    if ($text == START_DIALOG) {

      if ($name != EMPTY_STRING) {
        $reply = WELCOMING . $name . "!";
      }

      else {
        $reply = WELCOMING . ", Незнакомец!";
      }
      sendNewMessage($chatId, $reply, $replyMarkup, $telegram);
      deleteInfo(COMMANDS_TABLE, $chatId);
    }

    elseif ($text == MY_LIBRARY) {
      $reply = CHOOSE_FUNCTION_REPLY;
      sendNewMessage($chatId, $reply, $libraryKeyboardMarkUp, $telegram);
      deleteInfo(COMMANDS_TABLE, $chatId);
    }

    elseif ($text == SHOW_LIBRARY_COMMAND) {
      deleteInfo(COMMANDS_TABLE, $chatId);
      showLibrary($chatId, $replyMarkup, $telegram);
    }

    elseif ($text == CLEANING_LIBRARY_COMMAND) {
      deleteInfo(BOOK_LIBRARY_TABLE, $chatId);
      $reply = LIBRARY_WAS_CLINED_REPLY;
      sendNewMessage($chatId, $reply, $replyMarkup, $telegram);
    }

    elseif ($text == MOVE_BACK_COMMAND) {
      $reply = CHOOSE_FUNCTION_REPLY;
      deleteInfo(COMMANDS_TABLE, $chatId);
      sendNewMessage($chatId, $reply, $replyMarkup, $telegram);
    }

    elseif ($text == SEARCH_BOOK_COMMAND) {
      updateCommand(COMMANDS_TABLE, $chatId, SEARCH_STATEMENT);
      $reply = SEARCH_BOOK_COMMAND_REPLY;
      sendNewMessage($chatId, $reply, $replyMarkup, $telegram);
    }

    elseif ($text == ADD_BOOK_COMMAND) {
      updateCommand(COMMANDS_TABLE, $chatId, ADD_STATEMENT);
      $reply = ADD_BOOK_REPLY;
      sendNewMessage($chatId, $reply, $libraryKeyboardMarkUp, $telegram);
    }

    elseif ($text == REMOVE_BOOK_COMMAND) {
      updateCommand(COMMANDS_TABLE, $chatId, REMOVE_STATEMENT);
      $reply = REMOVE_BOOK_REPLY;
      sendNewMessage($chatId, $reply, $libraryKeyboardMarkUp, $telegram);
    }

    else {
      $commands = getInfoFromTable(COMMANDS_TABLE, $chatId)["command"];
      if ($commands == ADD_STATEMENT or $commands == SEARCH_STATEMENT or $commands == REMOVE_STATEMENT) {
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
        $reply = CHOOSE_FUNCTION_REPLY;
      }
      sendNewMessage($chatId, $reply, $replyMarkup, $telegram);
    }
  }

  function getResponseText($bookName, $bookAuthor, $chatId, $commands) { 
    $bookInfo = getBookInfo($bookName, $bookAuthor, $chatId);
    
    if ($bookInfo["totalItems"] == 0) {
      return BOOK_SEARCH_WARNING;
    }

    else {
      $bookTitle = $bookInfo["items"][0]["volumeInfo"]["title"];
      $authors = $bookInfo["items"][0]["volumeInfo"]["authors"][0];
      $bookInfo = $bookInfo["items"][0]["volumeInfo"]["infoLink"];

      if ($commands == ADD_STATEMENT) {
        $booksArray = getInfoFromTable(BOOK_LIBRARY_TABLE, $chatId);
        foreach ($booksArray as $book) {
          if ($book == $bookInfo){
            deleteInfo(COMMANDS_TABLE, $chatId);
            return BOOK_IN_LIBRARY_REPLY;
          }
        }
        addBookToHistory($bookInfo, $chatId);
        deleteInfo(COMMANDS_TABLE, $chatId);
        return SUCESSFUL_BOOK_ADDING_REPLY; 
      }

      elseif ($commands == REMOVE_STATEMENT) {
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
            return BOOK_REMOVED_REPLY;
          }
          else {
            return NOT_FOUND_LIBRALY_REPLY;
          }
        }
        else {
          return EMPTY_LIBRARY_REPLY;
        }
      }
      else {
        deleteInfo(COMMANDS_TABLE, $chatId);
        return "Название: " . $bookTitle ."\nАвтор: ". $authors . " \nУзнать больше информации о книге: " . $bookInfo . "";
      }
    }
  }

  