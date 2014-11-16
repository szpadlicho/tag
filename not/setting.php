<?php
class Setting
{
    function __setSortMod($mod)
    {
        setcookie ('sort', $mod, time() + 3600*24*30*12);
        header('location:');
    }
    function __setSaveMod($name)
    {
        setcookie ($name, $name, time() + 3600*24*30*12);
        header('location:');
    }
    function __unsetSaveMod($name)
    {
        setcookie ($name, $name, time() - 3600*24*30*12);
        header('location:');
    }
}
$rec = new Setting();
isset($_POST['sorting']) ? $rec->__setSortMod($_POST['sorting']) : 'error1';
isset($_POST['notepad']) ? header('location: index.php') : 'error2';
// save setting
if (isset($_POST['save'])){
    isset($_POST['saving0']) ? $rec->__setSaveMod('savemod0') : $rec->__unsetSaveMod('savemod0');
    isset($_POST['saving1']) ? $rec->__setSaveMod('savemod1') : $rec->__unsetSaveMod('savemod1');
    isset($_POST['saving2']) ? $rec->__setSaveMod('savemod2') : $rec->__unsetSaveMod('savemod2');
    isset($_POST['saving3']) ? $rec->__setSaveMod('savemod3') : $rec->__unsetSaveMod('savemod3');
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
    <script type="text/javascript" src="js/jquery-1.8.2.min.js"></script>
    <script type="text/javascript">
    $(document).ready(function()
    {
        // post when click
        $('input[name=sorting]').click(function()
        {
            $("form input[name=set]").click();      
        });
        $('input[type=checkbox]').click(function()
        {
            $("form input[name=save]").click();      
        });
    });    
    </script>          
    <script type="text/javascript"></script>
    <style type="text/css"></style>
    <link rel="icon" type="image/png" href="favicon.png"/>
</head>
<body>
    <section id="site-place-holder">
        <form method="POST">
        <span class="bottom">
            Sortowanie :
            <label><input class="radio" type="radio" <?php echo (@$_COOKIE['sort']=='0') ? 'checked="checked"' : '';  ?> name="sorting" value="0" /><label>Kolejność tworzenia</label></label>
            <label><input class="radio" type="radio" <?php echo (@$_COOKIE['sort']=='1') ? 'checked="checked"' : '';  ?> name="sorting" value="1" /><label>Alfabetycznie</label></label>
            <input class="hidden_sec" type="submit" name="set" value="Ustaw" />
        </span>
        </form>
        <br />
        <form method="POST">
        <span class="bottom">
            Zapisywanie :
            <label><input class="radio" type="checkbox" <?php echo (isset($_COOKIE['savemod0'])) ? 'checked="checked"' : '';  ?> name="saving0" value="savemod0" /><label>Ctrl+S</label></label>
            <label><input class="radio" type="checkbox" <?php echo (isset($_COOKIE['savemod1'])) ? 'checked="checked"' : '';  ?> name="saving1" value="savemod1" /><label>Link click</label></label>
            <label><input class="radio" type="checkbox" <?php echo (isset($_COOKIE['savemod2'])) ? 'checked="checked"' : '';  ?> name="saving2" value="savemod2" /><label>Zablokuj click</label></label>
            <label><input class="radio" type="checkbox" <?php echo (isset($_COOKIE['savemod3'])) ? 'checked="checked"' : '';  ?> name="saving3" value="savemod3" /><label>Wyloguj click</label></label>
            <input class="hidden_sec" type="submit" name="save" value="Zapisz" />
        </span>
        </form>
        <br />
        <br />
        <br />
        <form method="POST">
        <span class="bottom">
            <input id="" class="" type="submit" name="notepad" value="Notatnik" />
        </span>
        </form>
    </section>
    <footer>
        <?php
            // echo $string = 'piotre';
            // echo '<br />';
            // echo $decode = base64_decode($string);
            // echo '<br />';
            // echo $encode = base64_encode($decode);
        ?>
    </footer>
</body>
</html>
<?php
    //var_dump ($_POST);
    //var_dump ($_GET);
    //var_dump ($_SESSION);
    //var_dump ($_COOKIE);
?>