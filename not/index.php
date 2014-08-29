<?php
header('Content-Type: text/html; charset=utf-8');
session_start();
class Notatnik{
	function __setTXT($nazwa, $zawartosc)
    {
		$file = 'data/'.$nazwa.'.txt';
		//open file
		$fp = fopen($file, 'w');
		//save data
		fputs($fp, $zawartosc);
		//close file
		fclose($fp);
	}
	function __getTXT($nazwa)
    {
		$file = 'data/'.$nazwa.'.txt';
		if(file_exists($file))
        {	
			//open file
			$fp = fopen($file, 'r');
			//check size
            $size = filesize($file);
            if($size > 0 )
            {
                //read file
                $dane = fread($fp, $size);
                //close file
                fclose($fp);
                return $dane;
            }
            else
            {
                return 'pusty';
            }
		}
		else{
			return 'error';
		}
	}
    function __getNameTab()
    {
        // directory files data base
        $dir = dirname(__FILE__).'/data/';
        $arr = scandir($dir);
        $dst = array();
        foreach($arr as $file){
            $src = new SplFileInfo($file);
            //get only extension of file
            $ext = $src->getExtension();
            if($file != '.' && $file != '..' && !is_dir($file) && $ext !='php')
            {
                //get only file name
                $name = $src->getBasename('.txt');
                //add file name to array
                $dst[] .= $name;
            }
        }
        $_SESSION['count'] = count($dst);
        //natural sort arra like 9,10
        //sort($dst, SORT_NATURAL);
        //same as up but neww
        natsort($dst);
        return $dst;
    }
    function __getCurentName()
    {
        $curent = explode('.', $_GET['file']);
        unset($curent[0]);
        $curent = implode('.', $curent);
        return $curent;
    }
    function showName()
    {       
        $i = 0;
        foreach($this->__getNameTab() as $wyn){
                $clear_int=explode('.', $wyn);
                unset($clear_int[0]);
                $clear_int=implode('.', $clear_int);
                echo '<a id="link-'.$i++.'" class="link" href="?file='.$wyn.'">('.$clear_int.')</a>';
        }
    }
    function __getInt()
    {
        //$int = filter_var($_GET['file'], FILTER_SANITIZE_NUMBER_INT);
        $int = explode('.',$_GET['file']);
        return $int[0].'.';
    }
    function changeName()
    {   
        rename(dirname(__FILE__).'/data/'.$_GET['file'].'.txt', dirname(__FILE__).'/data/'.$this->__getInt().$_POST['rename'].'.txt');
        header('location: ?file='.$this->__getInt().$_POST['rename']);
    }
    function userIn()
    {   
        $_POST['password'] == 'piotrek' ? setcookie('auth','yes',time()+3600*12) : 'password error';
        header('location: ?file='.$_GET['file']);
        
    }
    function userOut()
    {   
        setcookie ('auth', '', time() - 3600);
        header('location:');
    }
}
$rec = new Notatnik();
!isset($_GET['file']) ? $_GET['file'] = '0.start' : $error = 'error' ;
isset($_POST['save']) ? $rec->__setTXT($_GET['file'], $_POST['txt']) : 'error1';
isset($_POST['add']) && !empty($_POST['new_name']) ? $rec->__setTXT($_SESSION['count'].'.'.$_POST['new_name'], '') : 'error2';
isset($_POST['confirm']) && !empty($_POST['rename']) ? $rec->changeName() : 'error2';
isset($_POST['login_user']) && !empty($_POST['password']) ? $rec->userIn() : 'error3';
isset($_POST['logout_user']) ? $rec->userOut() : 'error4';
?>
<!DOCTYPE HTML>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <title>Notatnik</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/style.php">
    <script type="text/javascript" src="http://code.jquery.com/jquery-1.5.2.js"></script>
    <script type="text/javascript">
    <?php if(isset($_COOKIE['auth'])) { ?>
    (function($){
        $(document).ready(function()
        {
            // Save Form alt+s
            $(window).keypress(function(event)
            {
                if (!(event.which == 115 && event.ctrlKey) && !(event.which == 19)) return true;
                $("form input[name=save]").click();
                event.preventDefault();
                return false;
                alert('save');
            });
        });
    })(jQuery);
    <?php } ?>
    $(document).ready(function()
    {
        $('#new').click(function()
        {
            $('.hidden').css({'display':'inline'});        
        });
    });
    $(document).ready(function()
    {
        $('#rename').click(function()
        {
            $('.hidden_sec').css({'display':'inline'});        
        });
    });
    </script>          
    <script type="text/javascript"></script>
    <style type="text/css">
    <?php
    for($x=0; $x<$_SESSION['count']; $x++){
        $r = rand(70,255);
        $g = rand(70,255);
        $b = rand(70,255);
        ?>
        #link-<?php echo $x; ?>{
            color: rgb(<?php echo $r; ?>,<?php echo $g; ?>,<?php echo $b; ?>);
        }
        #link-<?php echo $x; ?>:hover{
            color: rgb(<?php echo floor($r / 0.7); ?>,<?php echo floor($g / 0.7); ?>,<?php echo floor($b / 0.7); ?>);
        }
    <?php
    }
    ?>
    </style>
    <link rel="icon" type="image/png" href="favicon.png"/>
</head>
<body>
    <section id="site-place-holder">
        <span class="header">Notatnik</span >
        <form method="POST">
            <?php echo isset($_COOKIE['auth']) ? $rec->showName() : '<input type="password" name="password" /><input type="submit" name="login_user" value="Zaloguj" />' ; ?>
            <input id="new" type="button" name="new" value="Nowy" />
            <input class="hidden" type="text" name="new_name" />
            <input class="hidden" type="submit" name="add" value="Dodaj" />
            <input id="rename" type="button" name="change" value="Zmień" />           
            <span id='int' class="hidden_sec"><?php echo $rec->__getInt(); ?></span>
            <input class="hidden_sec" type="text" name="rename" value="<?php echo $rec->__getCurentName(); ?>" />
            <input class="hidden_sec" type="submit" name="confirm" value="Ok" />
        </form>
        <form method="POST">
            <textarea class="txtarea" name="txt" ><?php echo isset($_COOKIE['auth']) ? $rec->__getTXT($_GET['file']) : 'Enter Password'; ?></textarea><br />
            <input type="submit" name="save" value="Zapisz" /><!--DOpisany do JS-->            
            <input type="submit" name="logout_user" value="Wyloguj" />
        </form>
    </section>
    <footer>
    </footer>
</body>
</html>
<?php
    //var_dump ($_POST);
    //var_dump ($_GET);
    //var_dump ($_SESSION);
    //var_dump ($_COOKIE);
    // for($x=0; $x<$_SESSION['count']; $x++){
    // echo rand(0,255);
    // echo '<br />';
    // }
?>