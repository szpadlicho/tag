<?php
error_reporting(E_STRICT | E_ALL);
ini_set("display_errors",1);
header('Content-Type: text/html; charset=utf-8');
session_start();
class Notatnik
{
    private $user;
    public function __setUser($user)
    {
        $this->user = $user;
    }
	public function __setTXT($nazwa, $zawartosc)
    {
		$file = 'data/'.$this->user.'/'.$nazwa.'.txt';
		//open file
		$fp = fopen($file, 'w');
		//save data
		fputs($fp, $zawartosc);
		//close file
		fclose($fp);
        chmod($file, 0777);//dla servera linux dostep
	}
	public function __getTXT22($nazwa)
    {
		$file = 'data/'.$this->user.'/'.$nazwa.'.txt';
		if (file_exists($file)) {	
			//open file
			$fp = fopen($file, 'r');
			//check size
            $size = filesize($file);
            if ($size > 0 ) {
                //read file
                $dane = fread($fp, $size);
                //close file
                fclose($fp);
                return $dane;
            } else {
                return 'pusty';
            }
		} else {
			return 'error';
		}
	}
    public function createDir()
    {
        if (! is_dir('data/'.$this->user)) {
        
            @mkdir('data/'.$this->user, 0777, true);
            chmod('data/'.$this->user, 0777);
            $this->__setTXT('0.start','');
        } 
        
    }
    public function __getTXT($nazwa)
    {
		$file = 'data/'.$this->user.'/'.$nazwa.'.txt';
		if (file_exists($file)) {	
			//open file
			$fp = fopen($file, 'r');
			//check size
            $size = filesize($file);
            if ($size > 0 ) {
                $dpass = fgets($fp);
                fclose($fp);
                $security = explode (':',$dpass);
                $security = preg_replace('~[\r\n]+~', '', $security);//delete enter from end line
                if ($security[0] == 'pass') {
                    if ($security[1] == @$_SESSION[$nazwa]) {
                        $fp = fopen($file, 'r');
                        $dane = fread($fp, $size);
                        return $dane;
                        fclose($fp);
                    } else {
                        return 'Enter password';
                    }
                } else {
                    $fp = fopen($file, 'r');
                    $dane = fread($fp, $size);
                    return $dane;
                    fclose($fp);
                }             
            } else {
                return 'pusty';
            }
		} else {
			return 'error';
		}
	}
}
$rec = new Notatnik;
$rec->__setUser('ja');
$_GET['file']='0.start';
$rec->createDir();
isset($_POST['save']) ? $rec->__setTXT($_GET['file'], $_POST['txt']) : 'error1';
if (isset($_POST['file_protect_enter'])){
    $_SESSION[$_GET['file']]=$_POST['file_protect_password'];
}
if (isset($_POST['file_protect'])){
    unset($_SESSION[$_GET['file']]);
}
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
    <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js"></script>
    <script type="text/javascript"></script>
</head>
<body>
    <section id="site-place-holder">
        <form method="POST">
            <textarea class="txtarea" name="txt" ><?php echo $rec->__getTXT($_GET['file']); ?></textarea><br />           
            <input type="submit" name="save" value="Zapisz" /><!--DOpisany do JS-->
            <?php if ($rec->__getTXT($_GET['file']) == 'Enter password') { ?>  
                <input type="text" name="file_protect_password" />
                <input type="submit" name="file_protect_enter" value="Odblokuj" />
                
            <?php } ?>
            <input type="submit" name="file_protect" value="Zablokuj" />
        </form>
    </section>
</body>
</html>
<?php var_dump($_SESSION); ?>
<?php 
/*
$file = 'data/ja/0.start.txt';
if (file_exists($file)) {	
    //open file
    $fp = fopen($file, 'r');
    //check size
    $size = filesize($file);
    if ($size > 0 ) {
        $dpass = fgets($fp);
        fclose($fp);
        $security = explode (':',$dpass);
        echo $security[1];
        echo $_SESSION['0.start'];
        
        var_dump($security);
    }
}
*/
?>