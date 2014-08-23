<!DOCTYPE HTML>
<?php
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
isset($_POST['add']) ? $rec->__setTXT('txt1', $_POST['txt1']) : 'error1';//1
isset($_POST['add']) ? $rec->__setTXT('txt2', $_POST['txt2']) : 'error2';//2
isset($_POST['add']) ? $rec->__setTXT('txt3', $_POST['txt3']) : 'error3';//3
?>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <title>Notatnik</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="not.css">
    <script type="text/javascript" src="http://code.jquery.com/jquery-1.5.2.js"></script>
    <script type="text/javascript">       
		//długość okna przeglądarki
		window.onload = setSize; 
		window.onresize = setSize;
		function setSize(){
			var fmw = document.documentElement.clientWidth;
			var fmh = document.documentElement.clientHeight;
			var fns = parseInt($('body').css('font-size'));//parseInt(sdfsdf) parseInt will automatically ignore the units.
			//var fmw = window.innerWidth;
			//var fmh = window.innerHeight;
			//var fmw = document.body.clientWidth;
			//var fmh = document.body.clientHeight;
			document.getElementById('count_1').innerHTML='W = '+fmw+'px & H = '+fmh+'px F = '+fns+'px';
			//document.getElementById('count_r').innerHTML='W = '+fmw+'px & H = '+fmh+'px F = '+fns+'px Em = '+fem;		
		};
		//pozycja scrollbars
		$(window).scroll(function() {
			var fmx = document.documentElement.scrollTop;
			var fmy = document.documentElement.scrollLeft;
			//var fmx = window.pageYOffset;
			//var fmy = window.pageXOffset;
			//var fmx = document.body.scrollTop;//nie dziala
			//var fmy = document.body.scrollLeft;//nie dziala
			//var fmx = $(this).scrollTop();
			//var fmy = $(this).scrollLeft();
			document.getElementById('count_2').innerHTML='X = '+fmx+'<br />Y = '+fmy;
			$('#count_2').css('top',0+fmx+'px');
		});        
	</script>
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
            <textarea class="txtarea" name="txt1" ><?php echo $rec->__getTXT('txt1'); ?></textarea><br />
            <input type="submit" name="add" value="Zapisz" />
        <!--</form>-->
        <span id="ak2"  class="akapit">Notatnik nr: 2</span >
        <!--<form method="POST">-->
            <textarea class="txtarea" name="txt2" ><?php echo $rec->__getTXT('txt2'); ?></textarea><br />
            <input type="submit" name="add" value="Zapisz" />
        <!--</form>-->
        <span id="ak3"  class="akapit">Notatnik nr: 3</span >
        <!--<form method="POST">-->
            <textarea class="txtarea" name="txt3" ><?php echo $rec->__getTXT('txt3'); ?></textarea><br />
            <input type="submit" name="add" value="Zapisz" />
        </form>
    </section>
    <footer>
        <div id="count_1"></div>
        <div id="count_2"></div>
    </footer>
</body>
</html>
<?php
    var_dump ($_POST);
?>