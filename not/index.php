<?php
header('Content-Type: text/html; charset=utf-8');
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
isset($_POST['add']) ? $rec->__setTXT('txt0', $_POST['txt0']) : 'error1';//0
?>
<!DOCTYPE HTML>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <title>Notatnik</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <script type="text/javascript" src="http://code.jquery.com/jquery-1.5.2.js"></script>
    <script type="text/javascript">
    (function($){
        $(document).ready(function()
        {
            // Save Form alt+s
            $(window).keypress(function(event)
            {
                if (!(event.which == 115 && event.ctrlKey) && !(event.which == 19)) return true;
                $("form input[name=add]").click();
                event.preventDefault();
                return false;
                alert('wow');
            });
        });
    })(jQuery);
    </script>
    <style type="text/css"></style>
    <script type="text/javascript"></script>	
</head>
<body>
    <section id="site-place-holder">
        <span id="ak1" class="akapit">Notatnik nr: 1</span >
        <form method="POST">
            <textarea class="txtarea" name="txt0" ><?php echo $rec->__getTXT('txt0'); ?></textarea><br />
            <input type="submit" name="add" value="Zapisz" />
        </form>
    </section>
    <footer>
    </footer>
</body>
</html>
<?php
    var_dump ($_POST);
?>