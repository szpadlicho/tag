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
    function showName()
    {
        $i = 0;
        foreach($this->__getNameTab() as $wyn){
                echo '<a id="link-'.$i++.'" class="link" href="?file='.$wyn.'">'.$wyn.'</a>';
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
    <link rel="stylesheet" href="css/style.php">
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
    $(document).ready(function()
    {
        $('#new').click(function()
        {
            $('.hidden').css({'display':'inline'});        
        });
    });
    </script>          
    <script type="text/javascript"></script>
    <style type="text/css">
    <?php
    for($x=0; $x<$_SESSION['count']; $x++){
        $r = rand(0,255);
        $g = rand(0,255);
        $b = rand(0,255);
        ?>
        #link-<?php echo $x; ?>{
            color: rgb(<?php echo $r; ?>,<?php echo $g; ?>,<?php echo $b; ?>);
        }
    <?php
    }
    ?>
    </style>
</head>
<body>
    <section id="site-place-holder">
        <span class="header">Notatnik</span >
        <form method="POST">
            <?php echo $rec->showName(); ?>       
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
    for($x=0; $x<$_SESSION['count']; $x++){
    echo rand(0,255);
    echo '<br />';
    }
?>