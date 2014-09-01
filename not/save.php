<?php
$nazwa = $_POST['file'];
$zawartosc = $_POST['text'];
$file = 'data/'.$nazwa.'.txt';
//open file
$fp = fopen($file, 'w');
//save data
fputs($fp, $zawartosc);
//close file
fclose($fp);