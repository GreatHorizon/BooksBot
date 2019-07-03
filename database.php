<?php
  const firstBook = 'first_book_slot';
  const secondBook = 'second_book_slot';
  const thirdBook = 'third_book_slot';
  const fourthBook = 'fourth_book_slot';
  const fifthBook = 'fifth_book_slot';
  const userId = 'user_id';
  const emptyField = 'empty';
  const bookHistoryTable = 'book_history';
  const dataBaseLogin = 'b5c433cc63ee73';
  const dataBaseName = 'heroku_2cd2894cd704696';
  const dataBasePassword = '290309dc';
  const dataBaseHost = 'eu-cdbr-west-02.cleardb.net';
  
  function addBookToHistory($bookTitle, $chatId) {
    $bookHistoryArray = getInfoFromTable(bookHistoryTable, $chatId);
    if ($bookHistoryArray) {
      $updatedUserInfo = changeUserHistory($chatId);
      deleteUserInfo(bookHistoryTable, $chatId);
      insertToBase($updatedUserInfo);
    }

    else {
      $newUser = addUserInfo($chatId);
      insertToBase(bookHistoryTable, $newUser);
    }
  }

  function getInfoFromTable($table, $chatId)
  {
    $db = getBd();
    $db->where(userId, $chatId);
    $bookHistoryArray = $db->getOne($table);
  }

  function deleteUserInfo($table, $chatId) {
    $db->where(userId, $chatId);
    $db->delete($table);
    return $userInfo;
  }

  function addUserInfo($chatId) { 
    $newUser = [
      userId => $chatId,
      firstBook => emptyField,
      secondBook => emptyField,
      thirdBook => emptyField,
      fourthBook => emptyField,
      fifthBook => $bookTitle
    ];
    return $newUser;
    insertToBase($newUser);
  }

  function changeUserHistory($chatId) {
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
    return $db = new MysqliDb (dataBaseHost, dataBaseLogin, dataBasePassword, dataBaseName);
  }