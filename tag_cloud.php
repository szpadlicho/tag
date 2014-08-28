<?php
header('Content-Type: text/html; charset=utf-8');
session_start();
// ini_set('xdebug.var_display_max_depth', -1);
// ini_set('xdebug.var_display_max_children', -1);
// ini_set('xdebug.var_display_max_data', -1);
include ('tag_cloud_cls.php');
$cls=new TagCloudCls();
if(!empty($_POST['url']) && isset($_POST['gen_url'])){
	$cls->setUrl(@$_POST['url']);
	$_SESSION['url']=@$_POST['url'];/*zapamitywanie*/
	unset($_SESSION['text']);
}
else if(!empty($_SESSION['url']) && !isset($_POST['gen_url']) && !isset($_POST['gen_tex'])){
	$cls->setUrl(@$_SESSION['url']);
}
else if(!empty($_POST['text']) && isset($_POST['gen_tex'])){
	$text=@$_POST['text'];
	$_SESSION['text']=@$_POST['text'];
	unset($_SESSION['url']);
}
else{
	$text = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas vel sollicitudin mauris, imperdiet tempor libero. Aliquam vel sodales neque. Aliquam nulla enim, auctor sit amet orci nec, malesuada auctor justo. Sed eu ligula tellus. Maecenas lacinia a lacus vel lobortis. Integer et leo sit amet purus malesuada pellentesque. Ut non libero at magna malesuada tincidunt et ac metus. Fusce eu gravida turpis.

	Fusce vulputate, urna tristique sollicitudin ultrices, nisi eros consectetur tellus, eget tempor elit purus non risus. Nullam porttitor tempus ipsum, non lacinia quam pharetra non. Mauris volutpat arcu dolor, nec posuere ligula ornare eget. Phasellus blandit tortor quam, in consectetur libero malesuada in. Curabitur sed lacus malesuada nulla volutpat fermentum nec suscipit turpis. Nunc ornare metus vel facilisis condimentum. Curabitur placerat ipsum sit amet turpis pellentesque aliquet. Cras augue dui, ornare vitae auctor eget, mattis nec lorem. Mauris ornare euismod arcu, et egestas mi sodales ac. Nunc vestibulum sapien ut turpis pulvinar, ac euismod mi facilisis. Morbi volutpat orci nisl, in iaculis justo lobortis vel. Praesent iaculis arcu urna, in facilisis tellus pharetra id. Etiam at lorem ullamcorper, condimentum ante non, mollis elit.

	Integer feugiat rutrum congue. Vivamus rhoncus elit massa, sit amet dictum lorem lacinia quis. Sed eu tempus sem, ut commodo lorem. Phasellus laoreet iaculis odio. Phasellus velit nulla, mollis vel ultricies eget, semper in dui. Aenean blandit euismod dignissim. Vestibulum vitae dui sed sem aliquam consectetur. Aliquam luctus tempus urna, in pharetra sem ultrices in. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Curabitur porttitor erat sed risus consequat, ornare vulputate ipsum condimentum. Sed sed libero ac nisl dictum vehicula.

	Ut velit eros, elementum nec massa vitae, viverra pellentesque sem. Etiam feugiat lorem dui, ac porttitor justo placerat nec. Proin non arcu vitae ante gravida ultrices. Mauris pulvinar massa vel nulla congue, ut lacinia odio rutrum. Donec dui enim, luctus in sollicitudin sit amet, hendrerit vitae est. Aenean facilisis, massa ut volutpat hendrerit, magna ligula laoreet est, sit amet bibendum nisi orci non libero. Pellentesque et nibh tincidunt, viverra justo iaculis, venenatis nibh. Donec eu nunc vel odio lacinia interdum. Vivamus orci enim, tincidunt ornare nisi ac, euismod tincidunt dolor.

	Sed scelerisque felis nibh, a lobortis quam dapibus faucibus. Vestibulum sed quam in urna pellentesque iaculis. Suspendisse varius nisi libero, sed posuere justo gravida ac. Aenean varius lobortis nisl sit amet tempor. Aenean pulvinar accumsan leo ut egestas. Donec tincidunt faucibus neque, ac sollicitudin eros euismod eu. Nulla vel ligula a quam aliquet sollicitudin. ";
}
if(isset($_SESSION['url'])){ 
	$_POST['url']=$_SESSION['url'];/*zapamitywanie*/
}
if(isset($_SESSION['text'])){ 
	$text=$_POST['text']=$_SESSION['text'];/*zapamitywanie*/
}
if(isset($_POST['ignore_list']) && !empty($_POST['ignore_list']) && isset($_POST['ignore_button'])){
	$cls->setIgnoreWords($_POST['ignore_list']);
}
if(isset($_POST['filtr_nr']) && !empty($_POST['filtr_nr']) && isset($_POST['filtr_button'])){
	$cls->_setFrequencyNumber($_POST['filtr_nr']);
}
if(isset($_POST['mod_nr']) && !empty($_POST['mod_nr']) && isset($_POST['mod_button'])){
	$cls->_setModNumber($_POST['mod_nr']);
}
if(isset($_POST['sort_nr']) && !empty($_POST['sort_nr']) && isset($_POST['sort_button'])){
	$cls->_setSort($_POST['sort_nr']);
}
if(isset($_POST['clear_nr']) && !empty($_POST['clear_nr']) && isset($_POST['clear_button'])){
	$cls->_setClearNumber($_POST['clear_nr']);
}
if(isset($_POST['show_nr']) && !empty($_POST['show_nr']) && isset($_POST['show_button'])){
	$cls->_setShowMod($_POST['show_nr']);
}

$cls->setDeclineWords($cls->getIgnoreWords());

if(!isset($_SESSION['text']) && isset($_SESSION['url']) && $cls->_getModNumber()==1){// dodac mod na 2 metody
	$document=$cls->otworz_adres();
	// Dump contents (without tags) from HTML
	$text=$cls->html2txt($document);// tryb 1	
}
if(!isset($_SESSION['text']) && isset($_SESSION['url']) && $cls->_getModNumber()!=1){// || isset($_POST['filtr_button']) || isset($_POST['ignore_button']) 
	$document=$cls->otworz_adres();
	include ('simple_html_dom.php');
    // Add http:// if no exist 
    parse_url($cls->getUrl(), PHP_URL_SCHEME)==''?$cls->setUrl('http://'.@$_SESSION['url']):$cls->setUrl(@$_SESSION['url']);
	// Dump contents (without tags) from HTML
	$text = file_get_html($cls->getUrl())->plaintext;//tryb 2
}
if(!empty($document) || isset($_POST['text'])){
	$text_arr=$cls->set_array($text);
    //var_dump($text_arr);
	$word_clear=$cls->filter_stopwords($text_arr);
    //var_dump($word_clear);
	$word_frek=$cls->word_freq($word_clear);
    //var_dump($word_frek);
	$word_filter=$cls->freq_filter($word_frek, $cls->_getFrequencyNumber());//$cls->_getFrequencyNumber() mozna wcisnac odrazu w klase
    //var_dump($word_filter);
    if($cls->_getSort()=='yes'){//do sortowania 
        arsort($word_filter);//sortuje pod wzgledem wartosci od największej
        reset($word_filter);//wracam do pierwszej pozycji w tablicy (ważne)!!
    }
	if($cls->_getClearNumber()=='no'){//do usuwania liczb
		foreach($word_filter as $key => $value){
			if(is_int($key)){//sprawdzam czy klucz to liczba
				unset($word_filter[$key]);//usuwam klucze liczbowe
			}
		}
	}
    //var_dump($word_filter);    
    if($cls->_getShowMod()=='1'){
        $des=$cls->word_cloud_v2($word_filter);
    }
    else{
        $des=$cls->word_cloud($word_filter);
    }
    //var_dump($des);
}
//$text=preg_replace('/\s+/', ' ', trim($text));//usuwam zbedne spacje z textu i entery
$text=preg_replace('/(\s)\s*/', '\\1', trim($text));//usuwam zbedne spacje z textu ale zostawiam po jednym enterze
	
//var_dump($word_filter);
?>
<!DOCTYPE HTML>
<html lang="pl">
<head>
<meta charset="UTF-8">
    <title>Tag Cloud by Dutedraku</title>
    <?php include ("meta5.html"); ?>
	<link title="deafult" rel="stylesheet" type="text/css" href="tag_cloud.css"/>
</head>
<body>
	<div id="place-holder">
		<div>
			<form method="post">
				URL<input id="url_inp" type="text" name="url" value="<?php if(isset($_POST['url'])){echo @$_SESSION['url'] ;} ?>"/><input class="sub" type="submit" name="gen_url" value="Generuj Chmurę" />
			</form>
		</div>
		<div id="inf_div">
			<span id="all" ><b>Ilość zaindexowanych słów:</b><span id="one" > <?php echo $cls->getWordCount(); ?></span></span>
			<span id="uni" ><b>Ilość unikatowych słów:</b><span id="two" > <?php echo $cls->getUniqueWords(); ?></span></span>
			<span id="tag" ><b>Liczba zaindexowanych tagów:</b><span id="three" > <?php echo @$des[1]; ?></span></span>
		</div>		
		<div id="words_div" >
			<?php echo @$des[0]; ?><!--word-->
		</div>
		<form method="post">
			<div id="form_div">		
				<textarea id="text_inp" name="text"><?php if(!empty($text)){echo $text;} ?></textarea>
				<br />
				<input class="sub" type="submit" name="gen_tex" value="Generuj Chmurę" />		
			</div>
		</form>
		<div id="ignore_div">
			<form method="post">
                Filtr powtórzeń:
                <input id="filtr_inp" type="text" name="filtr_nr" value="<?php echo $cls->_getFrequencyNumber(); ?>" />
                <input class="sub" type="submit" name="filtr_button" value="Ustaw" />
                Tryb Filtra: 
				<!--<input id="mod_inp" type="text" name="mod_nrr" value="<?php //echo $cls->_getModNumber(); ?>" />-->              
                <input class="mod_radio" type="radio" name="mod_nr" value="1" title="własny" <?php if($cls->_getModNumber()==1){ echo 'checked="checked"'; }?> />1
                <input class="mod_radio" type="radio" name="mod_nr" value="2" title="simple_html_dom.php" <?php if($cls->_getModNumber()==2){ echo 'checked="checked"'; }?> />2
                <input class="sub" type="submit" name="mod_button" value="Ustaw" /><br />
                Show Mod:
                <input class="show_radio" type="radio" name="show_nr" value="1" title="własny" <?php if($cls->_getShowMod()=='1'){ echo 'checked="checked"'; }?> />1
                <input class="show_radio" type="radio" name="show_nr" value="2" title="orginał" <?php if($cls->_getShowMod()=='2'){ echo 'checked="checked"'; }?> />2
                <input class="sub" type="submit" name="show_button" value="Ustaw" /><br /> 
                Sortuj:
                <!--<input id="sort_inp" type="text" name="sort_nrr" value="<?php //echo $cls->_getSort(); ?>" />-->
                <input class="sort_radio" type="radio" name="sort_nr" value="yes" <?php if($cls->_getSort()=='yes'){ echo 'checked="checked"'; }?> />Tak
                <input class="sort_radio" type="radio" name="sort_nr" value="no" <?php if($cls->_getSort()=='no'){ echo 'checked="checked"'; }?> />Nie
                <input class="sub" type="submit" name="sort_button" value="Ustaw" /><br />
				Liczby:
                <!--<input id="sort_inp" type="text" name="sort_nrr" value="<?php //echo $cls->_getSort(); ?>" />-->
                <input class="clear_radio" type="radio" name="clear_nr" value="yes" <?php if($cls->_getClearNumber()=='yes'){ echo 'checked="checked"'; }?> />Tak
                <input class="clear_radio" type="radio" name="clear_nr" value="no" <?php if($cls->_getClearNumber()=='no'){ echo 'checked="checked"'; }?> />Nie
                <input class="sub" type="submit" name="clear_button" value="Ustaw" /><br />               
                <!--*-->
				<textarea id="text_inp_ign" name="ignore_list"><?php echo $cls->getIgnoreWords(); ?></textarea>
				<br />
				<input class="sub" type="submit" name="ignore_button" value="Dopisz do ignorowanych" />
				<input type="hidden" name="sniffer1" value="<?php if(isset($_SESSION['url'])){ echo $_SESSION['url'];}/*zapamitywanie*/ ?>" />
			</form>
		</div>
		<br />
		<?php //if(!empty($text)){echo $text;} ?>
		<?php //var_dump($_POST); ?>
		<?php //var_dump($_SESSION); ?>
		<?php //echo $cls->otworz_adres(); ?>
	</div>
</body>
</html>