<?php
//var_dump(@$_POST);
//var_dump(@$_GET);
// function __setTXT($nazwa, $zawartosc)
// {
    // $file = $nazwa.'.txt';
    // //open file
    // $fp = fopen($file, 'w');
    // //save data
    // fputs($fp, $zawartosc);
    // //close file
    // fclose($fp);
    // chmod($file, 0777);//dla servera linux dostep
// }
if (isset($_POST)) { 
    //setcookie ('asd', 'fgfh', time() - 3600);
    $get='';
    foreach ($_POST as $lol) {
        //__setTXT('qwe12', $lol);
        $get .= $lol;
    }
    //__setTXT('qwe12', $get);
    $foo=explode('filename[]=', $get);
    $foo=implode('',$foo);
    $foo=explode('&', $foo);
    var_dump($foo);
    foreach ($foo as $key => $value) {
        $new=explode('.',$value);
        rename('data/'.$value, 'data/'.$key.'.'.$new[1].'.txt');
    }
}
?>