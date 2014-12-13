<?php
error_reporting(E_STRICT | E_ALL);
ini_set("display_errors",1);
header('Content-Type: text/html; charset=utf-8');
session_start();
class Notatnik
{
    private $user;
    private $encryption_key = 'mysecretkey'; // for text coding/decoding
    private $crypt; // for text coding/decoding
    private $key = 'My strong random secret key';
    public function __setUser($user)
    {
        $this->user = $user;
    }
    public function __setCrypt()
    {
        $file = 'users/'.$this->user.'.txt';
		if (file_exists($file)) {
			$fp = fopen($file, 'r');
            $size = filesize($file);
            if ($size > 0 ) {
                $dane = fread($fp, $size);
                fclose($fp);
                $user = explode(':&|&:', $dane);
                if (isset($user[2])){ //zabezpieczenie dla tych co nie maja ustawione jeszcze nic
                    $user[2] == 1 ? $this->crypt = 1 : $this->crypt = 0;
                    return $this->crypt;
                } else {
                    return $this->crypt = 0;
                }
            }
        }
    }
	public function __setTXT($nazwa, $zawartosc)
    {
		$file = 'data/'.$this->user.'/'.$nazwa.'.txt';
		$fp = fopen($file, 'w');
        $this->crypt == 1 ? $zawartosc = $this->encrypt($zawartosc) : $sec = 'off'; // for text coding/decoding
		fputs($fp, $zawartosc);
		fclose($fp);
        chmod($file, 0777);//for Linux access
	}
    public function __getTXT($nazwa)
    {
		$file = 'data/'.$this->user.'/'.$nazwa.'.txt';
		if (file_exists($file)) {
			$fp = fopen($file, 'r');
            $size = filesize($file);
            if ($size > 0 ) {
                $dpass = fgets($fp);
                $this->crypt == 1 ? $dpass = $this->decrypt($dpass) : $sec = 'off'; // for text coding/decoding
                fclose($fp);
                $security = explode (':',$dpass);
                $security = preg_replace('~[\r\n]+~', '', $security);// delete enter from end line
                if ($security[0] == 'pass') {
                    if ($security[1] == @$_SESSION[$nazwa]) {
                        $fp = fopen($file, 'r');
                        $dane = fread($fp, $size);
                        $this->crypt == 1 ? $dane = $this->decrypt($dane) : $sec = 'off'; // for text coding/decoding
                        return $dane;
                        fclose($fp);
                    } else {
                        return 'Enter password';
                    }
                } else {
                    $fp = fopen($file, 'r');
                    $dane = fread($fp, $size);
                    $this->crypt == 1 ? $dane = $this->decrypt($dane) : $sec = 'off'; // for text coding/decoding
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
    public function checkSecurity($nazwa)
    {
        $file = 'data/'.$this->user.'/'.$nazwa.'.txt';
        if (file_exists($file)) {
            $fp = fopen($file, 'r');
            $size = filesize($file);
            $chsec = fgets($fp);
            $this->crypt == 1 ? $chsec = $this->decrypt($chsec) : $sec = 'off'; // for text coding/decoding
            fclose($fp);
            $security = explode (':',$chsec);
            if ($security[0] == 'pass') {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    public function __getNameTab()
    {
        // directory files data base
        $dir = dirname(__FILE__).'/data/'.$this->user.'/';
        $arr = scandir($dir);
        $sort_n = array();
        foreach ($arr as $file) {
            $src = new SplFileInfo($file);
            // get only extension of file
            $ext = $src->getExtension();
            if ($file != '.' && $file != '..' && !is_dir($file) && $ext !='php') {
                // get only file name
                $name = $src->getBasename('.txt');
                // add file name to array
                $sort_n[] .= $name;
            }
        }
        $_SESSION['count'] = count($sort_n);
        // sort numerically
        usort($sort_n, 'strnatcasecmp');
        // sort alphabetically 
        $wyn = array();
        foreach ($sort_n as $un) {
            $un = explode('.', $un);
            @$wyn[] .= @$un[1].'.'.@$un[0];            
        }
        usort($wyn, 'strnatcasecmp');
        $sort_a=array();
        foreach ($wyn as $un) {
            $un = explode('.', $un);
            $sort_a[] .= $un[1].'.'.$un[0];            
        }        
        return array($sort_n,$sort_a);
    }
    public function __getCurentName()
    {
        $curent = explode('.', $_GET['file']);
        unset($curent[0]);
        $curent = implode('.', $curent);
        return $curent;
    }
    public function showName()
    {   
        $tab = $this->__getNameTab();
        $i = 0;
        $sort = (@$_COOKIE['sort']=='1') ? $tab[1] : $tab[0] ;
        echo '<div id="sort">';
        foreach ($sort as $wyn) {
                $clear_int=explode('.', $wyn);
                unset($clear_int[0]);
                $clear_int=implode('.', $clear_int);
                echo '<a id="filename-'.$wyn.'.txt" class="link-'.$i.' links" href="?file='.$wyn.'">('.$clear_int.')</a>';
                $i++;
        }
        echo '</div>';
    }
    public function __getInt()
    {
        $int = explode('.',$_GET['file']);
        return $int[0].'.';
    }
    public function changeName()
    {   
        $new = $this->__getInt().(str_replace('.', ',', $_POST['rename']));
        rename(dirname(__FILE__).'/data/'.$this->user.'/'.$_GET['file'].'.txt', dirname(__FILE__).'/data/'.$this->user.'/'.$new.'.txt');
        //header('location: ?file='.$new);
        //header('Location'.$_SERVER['PHP_SELF'].'?file='.$new);
        //$_GET['file'] = $new;        
        //header('Refresh:0; url='.$_SERVER['REQUEST_URI'].'?file='.$new);
        //header('Refresh:0; url='.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'?file='.$new);
        //
        //ob_start();
        header('Refresh:0; url='.$_SERVER['PHP_SELF'].'?file='.$new);
        //ob_end_flush();
        //echo("<script>location.href = 'index.php?file=".$new."';</script>");
    }
    public function deleteName()
    {
        unlink('data/'.$this->user.'/'.$_GET['file'].'.txt');
        $dir = dirname(__FILE__).'/data/'.$this->user.'/';
        $arr = scandir($dir);
        $sort_n = array();
        foreach ($arr as $file) {
            $src = new SplFileInfo($file);
            // get only extension of file
            $ext = $src->getExtension();
            if ($file != '.' && $file != '..' && !is_dir($file) && $ext !='php') {
                // get only file name
                $name = $src->getBasename('.txt');
                // add file name to array
                $sort_n[] .= $name;
            }
        }        
        usort($sort_n, 'strnatcasecmp');
        $i = 0;
        $new_int = array();
        foreach ($sort_n as $pices) {
            $name = explode('.', $pices);
            $add_int = $i.'.'.$name[1];
            $new_int[] .= $add_int;
            rename('data/'.$this->user.'/'.$pices.'.txt', 'data/'.$this->user.'/'.$i.'.'.$name[1].'.txt');
            $i++;
        }   
        header('Refresh:0; url='.$_SERVER['PHP_SELF']);
    }
    public function createDir()
    {
        if (! is_dir('data/'.$this->user)) {
            @mkdir('data/'.$this->user, 0777, true);
            chmod('data/'.$this->user, 0777);
            $this->__setTXT('0.start','');
        } 
        
    }
    //*************************************************************//
    public function createNew($login, $password, $re_password, $email)
    {
        if (! is_dir('users')) {
            @mkdir('users');
            chmod('users', 0777);
        }
        $file = 'users/'.$login.'.txt';
		if (! file_exists($file)) {	
            // Checking data is data correct
            if ($password === $re_password) {
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    // start creating
                    $fp = fopen($file, 'w');
                    // encrypt or on
                    isset($_POST['encrypt']) ? $encrypt = 1 : $encrypt = 0;
                    $zawartosc = md5($password).':&|&:'.$email.':&|&:'.$encrypt;
                    // save data
                    fputs($fp, $zawartosc);
                    // close file
                    fclose($fp);
                    // set permission
                    chmod($file, 0777);
                    setcookie('auth',$login,time()+3600*12);// first login remember half day
                    header('location: ');
                } else {
                    return 'Błedna forma adresu email.';
                }
            } else {
                return 'Hasła nie są zgodne.';
            }
        } else {
            // user exist
            return $login.' login zajęty.';
        }
    }
    public function loginUser($login, $password)
    {
        $file = 'users/'.$login.'.txt';
		if (file_exists($file)) {
			$fp = fopen($file, 'r');
            $size = filesize($file);
            if ($size > 0 ) {
                $dane = fread($fp, $size);
                fclose($fp);
                $user = explode(':&|&:', $dane);
                if ($user[0] === md5($password) && ! isset($_POST['remember_me'])) {
                    setcookie('auth',$login,time()+3600*12);// remember half day
                    header('location: ?file='.$_GET['file']);
                } elseif ($user[0] === md5($password) && isset($_POST['remember_me'])) {
                    setcookie('auth',$login,time()+3600*24*365);// remember one year
                    header('location: ?file='.$_GET['file']);
                } else {
                    return 'Błędne hasło';
                }
            } else {
                return 'Dane nie istnieją';
            }
        } else {
            return 'Błędny bądź nie istniejący login.';
        }
    }
    public function logoutUser()
    {   
        setcookie ('auth', '', time() - 3600);
        header('location: index.php');
    }
    public function encrypt($string)  // for text coding/decoding
    {
        /**
        * Returns an encrypted & utf8-encoded
        */
        $iv = md5(md5($this->key));
        $output = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($this->key), $string, MCRYPT_MODE_CBC, $iv);
        $output = base64_encode($output);
        return $output;
    }

    
    public function decrypt($string)  // for text coding/decoding
    {
        /**
        * Returns decrypted original string
        **/
        $iv = md5(md5($this->key));
        $output = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($this->key), base64_decode($string), MCRYPT_MODE_CBC, $iv);
        //$output = rtrim($output, "");
        $output = trim($output, "\0\4");
        return $output;
    }
}
$rec = new Notatnik;
$rec->__setUser(@$_COOKIE['auth']);
$rec->__setCrypt();
$rec->createDir();

! isset($_GET['file']) ? $_GET['file'] = '0.start' : $error = 'Utworz nowy plik' ;
(isset($_POST['save']) && (trim(@$_POST['txt']) != 'Enter password') && isset($_POST['txt'])) ? $rec->__setTXT($_GET['file'], $_POST['txt']) : 'error1';
isset($_POST['add']) && ! empty($_POST['new_name']) ? $rec->__setTXT($_SESSION['count'].'.'.(str_replace('.', ',', $_POST['new_name'])), '') : 'error2';
isset($_POST['confirm']) && !empty($_POST['rename']) ? $rec->changeName() : 'error2';
$rec->__getNameTab();// calls to the session count is start save for css color !important
isset($_POST['del_confirm']) ? $rec->deleteName() : 'error5';
isset($_POST['setting']) ? header('location: setting.php') : 'error7';
/***************************************************************************************/
$obj_user = clone $rec;
if (isset($_POST['save_user']) && ! empty($_POST['login'])) {
   echo $obj_user->createNew($_POST['login'], $_POST['password'], $_POST['re_password'], $_POST['email']);
}
if (isset($_POST['enter'])) {
    echo $obj_user->loginUser($_POST['login'], $_POST['password']);
}
if (isset($_POST['logout'])) {
    $obj_user->logoutUser();
}
/**************************************************************************************/
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
    <!--
    <script type="text/javascript" src="http://code.jquery.com/jquery-1.8.2.js"></script>
    <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js"></script>
    -->
    <script type="text/javascript" src="js/jquery-1.8.2.min.js"></script>
    <script type="text/javascript" src="js/jquery-ui.min.js"></script>
    <script type="text/javascript">
    <?php if (isset($_COOKIE['auth'])) { ?>
    
    $(document).ready(function(){

        var can = 1;
        var txt2 = $.trim($(".txtarea").val());
        var txt3 = "Enter password";
        if (txt2 != txt3) {
            can = 0;
        } else {
            $("textarea").attr("disabled", true);
        }
        var oldVal = "";
        $(".txtarea").on("change keyup paste", function() {
            if (txt2 != txt3) {
                can = 0;
            } else {
                $("textarea").attr("disabled", true);
            }
        });
        if (can == 0) {
            <?php if (isset($_COOKIE['savemod0'])) { ?>
            /**
            * Save Form alt+s
            **/
            $(window).keypress(function(event) 
            {
                if (!(event.which == 115 && event.ctrlKey) && !(event.which == 19)) return true;
                $('form input[name=save]').click();
                event.preventDefault();
                return false;
                alert('save');
            });
            <?php } ?>
            <?php if (isset($_COOKIE['savemod1'])) { ?>
            /**
            * Save when link clicked
            **/
            $('.links').click(function()
            {
                var txt = $(".txtarea").val();
                var get = <?php echo json_encode($_GET['file']); ?>;
                $.ajax({ 
                    async: false,
                    type: 'POST', 
                    url: 'save.php',
                    data: {text : txt, file : get},
                    success: function(){
                        // do something
                    }
                });
            });
            <?php } ?>
            <?php if (isset($_COOKIE['savemod2'])) { ?>
            /**
            * Save when protect clicked
            **/
            $('.security').click(function()
            {
                var txt = $(".txtarea").val();
                var get = <?php echo json_encode($_GET['file']); ?>;
                $.ajax({ 
                    async: false,
                    type: 'POST', 
                    url: 'save.php',
                    data: {text : txt, file : get},
                    success: function(){
                        // do something
                    }
                });
            });
            <?php } ?>
            <?php if (isset($_COOKIE['savemod3'])) { ?>
            /**
            * Save when logout
            **/
            $('input[name=logout]').click(function()
            {
                var txt = $(".txtarea").val();
                var get = <?php echo json_encode($_GET['file']); ?>;
                $.ajax({ 
                    async: false,
                    type: 'POST', 
                    url: 'save.php',
                    data: {text : txt, file : get},
                    success: function(){
                        // do something
                    }
                });
            });
            <?php } ?>
        }
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
    <script type="text/javascript">   
        $(document).ready(function () {
            /**
            * Allow sort item jQuery UI
            **/
            $('#sort').sortable({
                axis: 'xy',
                stop: function (event, ui) {
                    var data = $(this).sortable('serialize');
                    $.ajax({
                        async: false,
                        type: 'POST',
                        url: 'rename.php',
                        data: {data:data},
                        success: function(){
                            // do something
                        }
                    });
            }
            });
            /**
            *   Block special chars in text input
            **/
            $('[name="login"],[name="rename"],[name="new_name"],[name="file_protect_password"]').bind('keypress', function (event) {//,[name="password"],[name="re_password"]
                var regex = new RegExp("^[a-zA-Z0-9]+$");
                var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
                if (!regex.test(key) && event.which != 8 && event.keyCode != 9 && event.keyCode != 116) {//8 backspace//116 F5//9 tab
                   console.log('zablokowane');
                   console.log(event.which);
                   event.preventDefault();
                   return false;
                }
            });
            $('[name="email"]').bind('keypress', function (event) {
                var regex = new RegExp("^[a-zA-Z0-9]+$");
                var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
                var allow = $.inArray( event.which, [ 8, 45, 46, 64, 95 ]);
                console.log('Allow: '+allow);
                if (!regex.test(key) && allow == -1 && event.keyCode != 9 && event.keyCode != 116) {//8 backspace//116 F5//9tab
                   console.log('zablokowane');
                   console.log(event.which);
                   event.preventDefault();
                   return false;
                }
            });
            // var wasPressed = false;
            // document.onkeydown = f1;
            // function f1(e){
            // e = e || window.event;
            // if( wasPressed ) return;
                // if (e.keyCode == 116) {
                     // alert("f5 pressed");
                    // wasPressed = true;
                // }else {
                    // alert("Window closed");
                // }
            // }
        });
        $(function(){
            // $( '#area' ).focus();
            // document.getElementById('area').setSelectionRange(3,11);
            // //document.getElementById('area').setSelectionRange(3,3);
            // $( '#area' ).click(function(){
                // var one = $('#area')[0].selectionStart;
                // var two = $('#area')[0].selectionEnd;
                // //console.log(one);
                // //console.log(two);
            // });
            function insertAtCursor(myField, myValue) {
                //IE support
                if (document.selection) {
                    myField.focus();
                    sel = document.selection.createRange();
                    sel.text = myValue;
                }
                //MOZILLA/NETSCAPE support
                else if (myField.selectionStart || myField.selectionStart == '0') {
                    var startPos = myField.selectionStart;
                    var endPos = myField.selectionEnd;
                    myField.value = myField.value.substring(0, startPos)
                    + myValue
                    + myField.value.substring(endPos, myField.value.length);
                    // position cursor at end
                    myField.selectionStart = startPos + myValue.length;
                    myField.selectionEnd = endPos + myValue.length;
                    //myField.focus();
                } else {
                    myField.value += myValue;
                }
            }
            // calling the function
            $(document).on('keydown', '#area', function(e) { 
                var keyCode = e.keyCode || e.which; 
                if (keyCode == 9) { 
                e.preventDefault(); 
                    // call custom function here
                    var tabs = '    ';
                    //console.log(tabs.length);
                    //var one = $('#asd')[0].selectionStart + tabs.length;
                    //var two = $('#asd')[0].selectionEnd + tabs.length;
                    insertAtCursor(document.getElementById('area'), tabs);
                    //document.getElementById('asd').setSelectionRange(one,two);
                } 
            });
        });
    </script>    
    <script type="text/javascript"></script>
    <style type="text/css">
    <?php
    for ($x=0; $x<$_SESSION['count']; $x++) {
        $r = rand(70,255);
        $g = rand(70,255);
        $b = rand(70,255);
        ?>
        .link-<?php echo $x; ?>{
            color: rgb(<?php echo $r; ?>,<?php echo $g; ?>,<?php echo $b; ?>);
        }
        .link-<?php echo $x; ?>:hover{
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
        <p class="neon">Notatnik</p>
        <form method="POST">
            <?php if (isset($_COOKIE['auth'])) { 
                $rec->showName();
            } elseif (isset($_POST['create_user'])) { ?>
                <input type="text" name="login" placeholder="login" />
                <input type="text" name="password" placeholder="hasło" />
                <input type="text" name="re_password" placeholder="powtórz hasło" />
                <input type="text" name="email" value="email" />
                <input type="checkbox" name="encrypt" />Szyfruj pliki&nbsp;
                <input type="submit" name="save_user" value="Dodaj" />
                <input type="submit" name="cancel" value="Anuluj" />
            <?php } else { ?>
                <input type="text" name="login" /><input type="password" name="password" /><input type="submit" name="enter" value="Zaloguj" /><input type="submit" name="create_user" value="Stwórz Nowego" /><input type="checkbox" name="remember_me" />Zapamiętaj mnie
            <?php } ?>          
        </form>
        <form method="POST">
            <textarea id="area" class="txtarea" name="txt" ><?php echo isset($_COOKIE['auth']) ? $rec->__getTXT($_GET['file']) : 'Enter Password'; ?></textarea><br />           
            <?php if(isset($_COOKIE['auth'])) { ?>
                <input type="submit" name="save" value="Zapisz" /><!--Dopisany do JS-->
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
                <?php if ($rec->checkSecurity($_GET['file']) == true) { ?>
                    <?php if (! isset($_SESSION[$_GET['file']])) { ?>  
                        <input type="password" name="file_protect_password" />
                        <input class="" type="submit" name="file_protect_enter" value="Odblokuj" />
                    <?php } else { ?>
                        <input class="security"  type="submit" name="file_protect" value="Zablokuj" />
                    <?php } ?>
                <?php } ?>
                <input class="right" type="submit" name="logout" value="Wyloguj" />
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
    //for($x=0; $x<$_SESSION['count']; $x++){
    //echo rand(0,255);
    //echo '<br />';
    //}
    //echo $_SESSION['count'];
    //unset($_SESSION['count'])
?>