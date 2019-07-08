<?php
  require_once('const.php');
  
  function addBookToHistory($bookTitle, $chatId) {
    $bookHistoryArray = getInfoFromTable(bookHistoryTable, $chatId);
    if ($bookHistoryArray) {
      $updatedUserInfo = changeUserHistory($chatId, $bookTitle);
      insertToBase(bookHistoryTable, $updatedUserInfo);
    }

    else {
      $newUser = addUserInfo($chatId, $bookTitle);
      insertToBase(bookHistoryTable, $newUser);
    }
  }

  function getInfoFromTable($table, $chatId)
  {
    $db = getBd();
    $db->where(userId, $chatId);
    $bookHistoryArray = $db->getOne($table);
    return $bookHistoryArray;
  }

  function deleteInfo($table, $chatId) {
    $db = getBd();
    $db->where(userId, $chatId);
    $db->delete($table);
  }

  function addUserInfo($chatId, $bookTitle) { 
    $newUser = [
      userId => $chatId,
      firstBook => emptyField,
      secondBook => emptyField,
      thirdBook => emptyField,
      fourthBook => emptyField,
      fifthBook => $bookTitle
    ];
    return $newUser;
    
  }

  function changeUserHistory($chatId, $bookTitle) {
    $bookHistoryArray = getInfoFromTable(bookHistoryTable, $chatId);
    deleteInfo(bookHistoryTable, $chatId);
    $userHistory = [
      userId => $chatId,
      firstBook => $bookHistoryArray[secondBook],
      secondBook => $bookHistoryArray[thirdBook],
      thirdBook => $bookHistoryArray[fourthBook],
      fourthBook => $bookHistoryArray[fifthBook],
      fifthBook => $bookTitle,
    ];
    return $userHistory;
  }

  function insertToBase($table, $addingPart) {
    $db = getBd();
    $db->insert($table, $addingPart);
  }

  function getBd() {
    return new MysqliDb (dataBaseHost, dataBaseLogin, dataBasePassword, dataBaseName);
  }
  function addCommand($chatId, $command) {
    $command = [
      "user_id" => $chatId,
      "command" => $command,
    ];
    insertToBase("commands", $command);
  }

  function updateCommand($table, $chatId, $command) {
    deleteInfo("commands", $chatId);
    addCommand($chatId, $command);
  }
  
  function showLibrary($chatId, $replyMarkup, $telegram) {
    $bookHistory = getInfoFromTable(bookHistoryTable, $chatId);
    $reply = emptyLibraryReply;
    if ($bookHistory) {
      $bookHistory = array_slice($bookHistory, 1);
      foreach ($bookHistory as $books) {
        if ($books != emptyField) {
          $reply = $books;
          sendNewMessage($chatId, $reply, $replyMarkup, $telegram);
        }
      }
    }
    if ($reply == emptyLibraryReply) {
      sendNewMessage($chatId, $reply, $replyMarkup, $telegram);
    }
  }