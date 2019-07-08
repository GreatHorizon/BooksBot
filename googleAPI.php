<?php
  require_once('const.php');

  function getBookInfo(string $bookName, string $bookAuthor, ?int $chatId): array { 
      $bookName = str_replace(' ', '+', $bookName);
      if ($bookAuthor == EMPTY_STRING) {
        $bookInfo = file_get_contents('https://www.googleapis.com/books/v1/volumes?q=intitle:'. $bookName .'&maxResults=1&orderBy=relevance&key=AIzaSyALM0SWc1JdHtgpPplJ6T2k9Fwcc1dI7vk');
      }
      else {
        $bookInfo = file_get_contents('https://www.googleapis.com/books/v1/volumes?q=intitle:'. $bookName .'+inauthor:'. $bookAuthor .'&maxResults=1&orderBy=relevance&key=AIzaSyALM0SWc1JdHtgpPplJ6T2k9Fwcc1dI7vk');
      }
      return json_decode($bookInfo, true);
    }