<?php
$nazwa = $_POST['file'];
$zawartosc = $_POST['text'];
$user = $_COOKIE['auth'];
$file = 'data/'.$user.'/'.$nazwa.'.txt';
if (file_exists($file)) {//dodane bo sie tworzył nowy przy kliknieci na link (podów heade nie działa na szpadlic);
    //open file
    $fp = fopen($file, 'w');
    //save data
    fputs($fp, $zawartosc);
    //close file
    fclose($fp);
}