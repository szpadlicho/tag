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
        $sort_n = array();
        foreach($arr as $file){
            $src = new SplFileInfo($file);
            //get only extension of file
            $ext = $src->getExtension();
            if($file != '.' && $file != '..' && !is_dir($file) && $ext !='php')
            {
                //get only file name
                $name = $src->getBasename('.txt');
                //add file name to array
                $sort_n[] .= $name;
            }
        }
        $_SESSION['count'] = count($sort_n);
        //sortowanie numerycznie
        usort($sort_n, 'strnatcasecmp');
        //sortowanie alfabetycznie  
        $wyn = array();
        foreach($sort_n as $un){
            $un = explode('.', $un);
            $wyn[] .= $un[1].'.'.$un[0];            
        }
        usort($wyn, 'strnatcasecmp');
        $sort_a=array();
        foreach($wyn as $un){
            $un = explode('.', $un);
            $sort_a[] .= $un[1].'.'.$un[0];            
        }        
        return array($sort_n,$sort_a);
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
        $tab = $this->__getNameTab();
        $i = 0;
        $sort = (@$_COOKIE['sort']=='1') ? $tab[1] : $tab[0] ;
        foreach($sort as $wyn){
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
        rename(dirname(__FILE__).'/data/'.$_GET['file'].'.txt', dirname(__FILE__).'/data/'.$this->__getInt().(str_replace('.', ',', $_POST['rename'])).'.txt');
        header('location: ?file='.$this->__getInt().(str_replace('.', ',', $_POST['rename'])));
    }
    function userIn()
    {   
        $_POST['password'] == 'piotrek' ? setcookie('auth','yes',time()+3600*12) : 'password error';
        header('location: ?file='.$_GET['file']);
        
    }
    function userOut()
    {   
        setcookie ('auth', '', time() - 3600);
        $this->__setTXT($_GET['file'], $_POST['txt']);
        header('location:');
    }
    function deleteName()
    {
        unlink('data/'.$_GET['file'].'.txt');
        $dir = dirname(__FILE__).'/data/';
        $arr = scandir($dir);
        $sort_n = array();
        foreach($arr as $file){
            $src = new SplFileInfo($file);
            //get only extension of file
            $ext = $src->getExtension();
            if($file != '.' && $file != '..' && !is_dir($file) && $ext !='php')
            {
                //get only file name
                $name = $src->getBasename('.txt');
                //add file name to array
                $sort_n[] .= $name;
            }
        }        
        usort($sort_n, 'strnatcasecmp');
        $i = 0;
        $new_int = array();
        foreach($sort_n as $pices){
            $name = explode('.', $pices);
            $add_int = $i.'.'.$name[1];
            $new_int[] .= $add_int;
            rename('data/'.$pices.'.txt', 'data/'.$i.'.'.$name[1].'.txt');
            $i++;
        }
        //return $new_int;
        header('location: ?file=0.start');
    }
    function __setSortMod($mod)
    {
        setcookie ('sort', $mod, time() + 3600*24*30);
        header('location:');
    }
}
$rec = new Notatnik();
!isset($_GET['file']) ? $_GET['file'] = '0.start' : $error = 'error' ;
isset($_POST['save']) ? $rec->__setTXT($_GET['file'], $_POST['txt']) : 'error1';
isset($_POST['add']) && !empty($_POST['new_name']) ? $rec->__setTXT($_SESSION['count'].'.'.(str_replace('.', ',', $_POST['new_name'])), '') : 'error2';
isset($_POST['confirm']) && !empty($_POST['rename']) ? $rec->changeName() : 'error2';
isset($_POST['login_user']) && !empty($_POST['password']) ? $rec->userIn() : 'error3';
isset($_POST['logout_user']) ? $rec->userOut() : 'error4';
$sort = $rec->__getNameTab();
//var_dump($sort);
isset($_POST['del_confirm']) ? $rec->deleteName() : 'error5';
isset($_POST['sorting']) ? $rec->__setSortMod($_POST['sorting']) : 'error6';
isset($_POST['setting']) ? header('location: setting.php') : 'error7';
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
                $('form input[name=save]').click();
                event.preventDefault();
                return false;
                alert('save');
            });
        });
    })(jQuery);
    $(document).ready(function()
    {
        // Save when link clicked
        //$('.link').click(function()
        //{     
            //$("form input[name=save]").click();
            //event.preventDefault();
            //return false;            
            //alert('save');            
        //});
        $('.link').click(function()
        {
            // Save when link clicked
            var txt = jQuery(".txtarea").val();
            //alert(txt);
            var get = <?php echo json_encode($_GET['file']); ?>;
            //alert(get);
            $.ajax({ 
              async: false,
              type: 'POST', 
              url: 'save.php',
              data: {text : txt, file : get}
            });
        });
    });
    $(document).ready(function()
    {
        // Save when sorting change
        $('input[name=sorting]').click(function()
        {
            $("form input[name=anuluj]").click();//anuluj tylko po to by odświeżyć strone     
        });
    });
    <?php } ?>
    $(document).ready(function()
    {
        $('#new').click(function()
        {
            $('#new').css({'display':'none'});
            $('#rename').css({'display':'none'});
            $('#del').css({'display':'none'});
            $('.hidden').css({'display':'inline'});        
        });
    });
    $(document).ready(function()
    {
        $('#rename').click(function()
        {
            $('#new').css({'display':'none'});
            $('#rename').css({'display':'none'});
            $('#del').css({'display':'none'});
            $('.hidden_sec').css({'display':'inline'});     
        });
    });
    $(document).ready(function()
    {
        $('#del').click(function()
        {
            $('#new').css({'display':'none'});
            $('#rename').css({'display':'none'});
            $('#del').css({'display':'none'});
            $('.del_confirm').css({'display':'inline'});       
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
            <?php if(isset($_COOKIE['auth'])) 
            { 
                $rec->showName();
            } else { ?>
                <input type="password" name="password" /><input type="submit" name="login_user" value="Zaloguj" />
            <?php } ?>          
        </form>
        <form method="POST">
            <textarea class="txtarea" name="txt" ><?php echo isset($_COOKIE['auth']) ? $rec->__getTXT($_GET['file']) : 'Enter Password'; ?></textarea><br />           
            <?php if(isset($_COOKIE['auth'])) { ?>
                <input type="submit" name="save" value="Zapisz" /><!--DOpisany do JS-->
                <span class="bottom">
                    <input id="new" type="button" name="new" value="Nowy" />
                    <input class="hidden" type="text" name="new_name" />
                    <input class="hidden" type="submit" name="add" value="Dodaj" />
                    <input class="hidden" type="submit" name="anuluj" value="Anuluj" />
                    <input id="rename" type="button" name="change" value="Zmień" />           
                    <span id='int' class="hidden_sec"><?php echo $rec->__getInt(); ?></span>
                    <input class="hidden_sec" type="text" name="rename" value="<?php echo $rec->__getCurentName(); ?>" />
                    <input class="hidden_sec" type="submit" name="confirm" value="Ok" />
                    <input class="hidden_sec" type="submit" name="anuluj" value="Anuluj" />
                    <input id="del" type="button" name="del" value="Usuń" />
                    <span class="del_confirm">Na pewno ?</span>
                    <input class="del_confirm" type="submit" name="del_confirm" value="Tak" />
                    <input class="del_confirm" type="submit" name="anuluj" value="Nie" />
                </span>
                <span class="bottom">
                    Sortowanie :
                    <label><input class="radio" type="radio" <?php echo (@$_COOKIE['sort']=='0') ? 'checked="checked"' : '';  ?> name="sorting" value="0" /><label>Kolejność tworzenia</label></label>
                    <label><input class="radio" type="radio" <?php echo (@$_COOKIE['sort']=='1') ? 'checked="checked"' : '';  ?> name="sorting" value="1" /><label>Alfabetycznie</label></label>
                </span>
                <input class="right" type="submit" name="logout_user" value="Wyloguj" />
                <input id="setting" class="right" type="submit" name="setting" value="Ustawienia" />
            <?php } ?>
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