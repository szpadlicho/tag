<?php
class Setting
{
    function __setSortMod($mod)
    {
        setcookie ('sort', $mod, time() + 3600*24*30);
        header('location:');
    }
}
$rec = new Setting();
isset($_POST['sorting']) ? $rec->__setSortMod($_POST['sorting']) : 'error1';
isset($_POST['notepad']) ? header('location: index.php') : 'error2';
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
    $(document).ready(function()
    {
        // post when click
        $('input[name=sorting]').click(function()
        {
            $("form input[name=anuluj]").click();      
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
            <input class="hidden_sec" type="submit" name="anuluj" value="Anuluj" />
        </span>
        <br />
        <br />
        <br />
        <span class="bottom">
            <input id="" class="" type="submit" name="notepad" value="Notatnik" />
        </span>
        </form>
    </section>
    <footer>
    </footer>
</body>
</html>