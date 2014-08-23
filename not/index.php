<?php
header('Content-Type: text/html; charset=utf-8');
session_start();
class Notatnik{
	function __setTXT($nazwa, $zawartosc){
		$file = $nazwa.'.txt';
		//otwarcie pliku
		$fp = fopen($file, 'w');
		// zapisanie danych
		fputs($fp, $zawartosc);
		// zamknięcie pliku
		fclose($fp);	
	}
	function __getTXT($nazwa){
		$file = $nazwa.'.txt';
		if(file_exists($file)){	
			//otwarcie pliku
			$fp = fopen($file, 'r');
			// sprawdzam wielkość
            $size = filesize($file);
            if($size > 0 ){
                // czytam danye
                $dane = fread($fp, $size);
                // zamknięcie pliku
                fclose($fp);
                return $dane;
            }
            else{
                return 'pusty';
            }
		}
		else{
			return 'error';
		}
	}
}
$rec = new Notatnik();
!isset($_GET['file']) ? $_GET['file'] = '0.start' : $error = 'error' ;
isset($_POST['save']) ? $rec->__setTXT($_GET['file'], $_POST['txt']) : 'error1';
isset($_POST['add']) ? $rec->__setTXT($_SESSION['count'].'.'.$_POST['new_name'], '') : 'error2';
?>
<!DOCTYPE HTML>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <title>Notatnik</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <script type="text/javascript" src="http://code.jquery.com/jquery-1.5.2.js"></script>
    <script type="text/javascript">
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
                alert('wow');
            });
        });
    })(jQuery);
    </script>
    <style type="text/css"></style>
    <script type="text/javascript">
    $(document).ready(function()
    {
        $('#new').click(function()
        {
            $('.hidden').css({'display':'inline'});        
        });
    });
    </script>	
</head>
<body>
    <section id="site-place-holder">
        <?php 
        //echo __FILE__.'<br />';
        //echo basename(__FILE__, '.php').'<br />';
        //echo basename(__FILE__).'<br />';
        //echo basename($_SERVER['PHP_SELF']).'<br />';
        //echo dirname(__FILE__);
        ?>
        <span id="ak" class="akapit">Notatnik</span >
        <form method="POST">
            <?php
            $dir = dirname(__FILE__);
            $this2 = scandir($dir);
            //var_dump($this2);
            $tab = array();
            foreach($this2 as $file){
                $ext = new SplFileInfo($file);
                $ext = $ext->getExtension();
                if($file != '.' && $file != '..' && !is_dir($file) && $ext !='php'){
                    $name = new SplFileInfo($file);
                    //$name = $name->getFilename();
                    $name = $name->getBasename('.txt');
                    $tab[] .= $name;
                }
            }           
            $_SESSION['count'] = count($tab);
            // if(isset($_POST['add'])){//dla cyferek tylko               
                // $next=$count;
                // $rec->__setTXT($next, '');
                // header('Location: index.php?file='.$_GET['file']);
                // exit;
            // }
            //var_dump($tab);
            foreach($tab as $wyn){
                echo ' <a class="link" href="?file='.$wyn.'">'.$wyn.'</a> ';
            }
            ?>       
            <input id="new" type="button" name="new" value="Nowy" />
            <input class="hidden" type="text" name="new_name" />
            <input class="hidden" type="submit" name="add" value="Dodaj" />
        </form>
        <form method="POST">
            <textarea class="txtarea" name="txt" ><?php echo $rec->__getTXT($_GET['file']); ?></textarea><br />
            <input type="submit" name="save" value="Zapisz" />
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
?>