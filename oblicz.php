<?php
// $file='Meble24h.html';
// //echo $dane = fread(fopen($file, "r"), filesize($file));
// $fh=fopen($file, 'r');
// $fs=filesize($file);
// $text=fread($fh,$fs);
/*--------------------------*/
function otworz_adres($adres, $post=false, $blad=3)
{
	$header[]='Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5';
	$header[]='Accept-Language: pl,en-us;q=0.7,en;q=0.3';
	$header[]='Accept-Charset: ISO-8859-2,utf-8;q=0.7,*;q=0.7';
	$header[]='Keep-Alive: 300';
	$header[]='Connection: keep-alive';
	 
	$ch=curl_init();
	curl_setopt($ch, CURLOPT_URL, $adres);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.0; pl; rv:1.8.1.11) Gecko/20071127 Firefox/2.0.0.11');
	curl_setopt($ch, CURLOPT_TIMEOUT, 60);
	curl_setopt($ch, CURLOPT_AUTOREFERER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');
	curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookies.txt');
	if($post!==false)
	{
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
	}
	$zwroc=curl_exec($ch);
	 
	if($blad>0)
	{
		$naglowek=substr(curl_getinfo($ch, CURLINFO_HTTP_CODE), 0, 1);
		if($zwroc=='' OR curl_error($ch)!='' OR $naglowek=='4' OR $naglowek=='5')
		{
			curl_close($ch);
			sleep(1);
			return otworz_adres($adres, $post, --$blad);
		}
	}
	else
		return false;
	 
	curl_close($ch);
	return gzdecode2($zwroc);
}
	 
function gzdecode2($tresc){
	if(strlen($tresc)<18 OR strcmp(substr($tresc,0,2),"\x1f\x8b")){
		return $tresc;
		return gzinflate(substr($tresc, 10));
	}
}
$tresc=otworz_adres(@$_POST['url']);
$tags = @get_meta_tags(@$_POST['url']);
$text=$tresc;
$tag_arr=explode(',', $tags['keywords']);
foreach($tag_arr as $tag_arr_sp){
	$tag_arr_nospace[]=trim($tag_arr_sp);
}

function html2txt ($document) {
	$search = array('@<script[^>]*?>.*?</script>@si',  // Strip out javascript
				   '@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags*/
				   '@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
				   '@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments including CDATA
	);
	$text = preg_replace($search, ' ', $document);
	return $text;
}
$textRedy=html2txt($text);
$tagi=$tag_arr_nospace;
$countText=str_word_count($textRedy, 0, 'ąęłóżźśćńöĄĘŁÓŻŹŚĆŃ1234567890');
?> 
<html>
<body>
<meta charset="UTF-8">
<form action="spr.php" method="post">
URL: <input type="text" name="url" />
<input type="submit" />
</form>
<?php
if ($textRedy!=null){
	echo "<br />----------------------------------------<br />";
	echo "ilość słów = ".$countText;
	echo "<br />";
	foreach ($tagi as $tag) {
		$ile=@substr_count($textRedy, $tag); // 2
		$proc=($ile/$countText)*100;//obliczam procent
		$proc2=round($proc, 2);//zaokrąglam do 2 miejsc po przecinku
		echo "<br />";
		echo $tag.' = '.$ile.' - '.@$proc2.'%';
		echo "<br />";
	}
	echo "<br />----------------------------------------<br />";
	echo $textRedy;
}
?>
</body>
</html>




