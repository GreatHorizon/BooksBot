<?php
  require_once('const.php');
  
  function addBookToHistory(string $bookTitle, ?int $chatId): void {
    $bookHistoryArray = getInfoFromTable(BOOK_LIBRARY_TABLE, $chatId);
    if ($bookHistoryArray) {
      $updatedUserInfo = changeUserHistory($chatId, $bookTitle);
      insertToBase(BOOK_LIBRARY_TABLE, $updatedUserInfo);
    }

    else {
      $newUser = addUserInfo($chatId, $bookTitle);
      insertToBase(BOOK_LIBRARY_TABLE, $newUser);
    }
  }

  function getInfoFromTable(string $table, ?int $chatId) {
    $db = getBd();
    $db->where(USER_ID, $chatId);
    $bookHistoryArray = $db->getOne($table);
    return $bookHistoryArray;
  }

  function deleteInfo(string $table, ?int $chatId): void {
    $db = getBd();
    $db->where(USER_ID, $chatId);
    $db->delete($table);
  }

  function addUserInfo(?int $chatId, ?string $bookTitle) : array { 
    $newUser = [
      USER_ID => $chatId,
      FIRST_BOOK => EMPTY_FIELD,
      SECOND_BOOK => EMPTY_FIELD,
      THIRD_BOOK => EMPTY_FIELD,
      FOURTH_BOOK => EMPTY_FIELD,
      FIFTH_BOOK => $bookTitle
    ];
    return $newUser;
    
  }

  function changeUserHistory(?int $chatId, ?string $bookTitle): array {
    $bookHistoryArray = getInfoFromTable(BOOK_LIBRARY_TABLE, $chatId);
    deleteInfo(BOOK_LIBRARY_TABLE, $chatId);
    $userHistory = [
      USER_ID => $chatId,
      FIRST_BOOK => $bookHistoryArray[SECOND_BOOK],
      SECOND_BOOK => $bookHistoryArray[THIRD_BOOK],
      THIRD_BOOK => $bookHistoryArray[FOURTH_BOOK],
      FOURTH_BOOK => $bookHistoryArray[FIFTH_BOOK],
      FIFTH_BOOK => $bookTitle,
    ];
    return $userHistory;
  }

  function insertToBase(string $table, array $addingPart): void {
    $db = getBd();
    $db->insert($table, $addingPart);
  }

  function getBd(): MysqliDb {
    return new MysqliDb (DB_HOST, DB_LOGIN, DB_PASSWORD, DB_NAME);
  }

  function addCommand(?int $chatId, string $command): void {
    $command = [
      USER_ID => $chatId,
      COMMAND => $command,
    ];
    insertToBase(COMMANDS_TABLE, $command);
  }

  function updateCommand(string $table, ?int $chatId, string $command): void {
    deleteInfo(COMMANDS_TABLE, $chatId);
    addCommand($chatId, $command);
  }
  
  function showLibrary(?int $chatId, string $replyMarkup, Api $telegram): void {
    $bookHistory = getInfoFromTable(BOOK_LIBRARY_TABLE, $chatId);
    $reply = EMPTY_LIBRARY_REPLY;
    if ($bookHistory) {
      $bookHistory = array_slice($bookHistory, 1);
      foreach ($bookHistory as $books) {
        if ($books != EMPTY_FIELD) {
          $reply = $books;
          sendNewMessage($chatId, $reply, $replyMarkup, $telegram);
        }
      }
    }
    if ($reply == EMPTY_LIBRARY_REPLY) {
      sendNewMessage($chatId, $reply, $replyMarkup, $telegram);
    }
  }