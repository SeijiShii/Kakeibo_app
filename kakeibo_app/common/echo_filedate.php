<?php
  // スクリプトの更新日付を返す
  function echo_filedate($filename) {
    if (file_exists($filename)) {
      $dateTime = (filemtime($filename));
      // $dateTime->setTimeZone(new DateTimeZone('Asia/Tokyo'));
      $dateTime = date('YmdHis', filemtime($filename));
      echo $dateTime;
    } else {
      echo 'file not found';
    }
  }
?>
