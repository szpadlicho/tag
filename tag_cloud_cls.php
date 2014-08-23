<?php
class TagCloudCls{
	private $url;
	private $decline_words;
	//private $strona;
	//private $text;
	//private $words;
	private $stopwords;
	private $word_count;
	private $unique_words;
	//private $filtered_words;
	//private $frequency_list;
	//private $filter;
	//private $lol;
	public function setUrl($url)
	{
		$this->url=$url;
		//return $this->otworz_adres($this->url);
	}
	public function getUrl()
	{
		return $this->url;
		//return $this->otworz_adres($this->url);
	}	
	public function setDeclineWords($decline_words)
	{
		$this->decline_words=$decline_words;
	}
	public function getWordCount()
	{
		return $this->word_count;
	}
	public function getUniqueWords()
	{
		return $this->unique_words;
	}	
	public function otworz_adres($post=false, $blad=3)
	{/*cURL pozyskuje strone*/
		$header[]='Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5';
		$header[]='Accept-Language: pl,en-us;q=0.7,en;q=0.3';
		$header[]='Accept-Charset: ISO-8859-2,utf-8;q=0.7,*;q=0.7';
		$header[]='Keep-Alive: 300';
		$header[]='Connection: keep-alive';
		 
		$ch=curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->url);
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
				return $this->otworz_adres($post, --$blad);
			}
		}
		else
			return false;
		
		curl_close($ch);
		return $this->gzdecode2($zwroc);/*otwiera i zarazem zwraca poniższa funkcje*/
	}	 
	public function gzdecode2($tresc)
	{//rozpakowywyje strone jesli jest spakowana
		if(strlen($tresc)<18 OR strcmp(substr($tresc,0,2),"\x1f\x8b")){
			return $tresc;
			return gzinflate(substr($tresc, 10));
		}
	}
	public function html2txt ($document)
	{/*usuwam znaczniki html js i css*/
		$search = array('@<script[^>]*?>.*?</script>@si',  // Strip out javascript
					   '@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags*/
					   '@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
					   '@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments including CDATA
		);
		$text = preg_replace($search, ' ', $document);
		//return $this->set_array($this->decline_words,$text);
		return $text;
	}
	public function set_array($text)
	{/* Get the posted text */
		$this->stopwords = $stopwords = explode(",", $this->decline_words);
		$words = str_word_count($text, 1, 'ąęłóżźśćńöĄĘŁÓŻŹŚĆŃ1234567890'); /* Generate list of words */
		$this->word_count = $word_count = count($words); /* Word count */
		$this->unique_words = $unique_words = count(array_unique($words) ); /* Unique word count */
		return $this->words = $words;
		//$this->filter_stopwords($this->words, $this->stopwords);
	}
	public function filter_stopwords($words) 
	{//usówanie niechcianych słow z teksu
		foreach ($words as $pos => $word) {
			if (!in_array(strtolower($word), $this->stopwords, TRUE)) {
				$filtered_words[$pos] = $word;
			}
		}
		return $this->filtered_words=@$filtered_words;
		//return $this->word_freq($this->words);
	}
	public function word_freq($words) 
	{//czestotliweosc wystapienia słowa w tekscie
		$frequency_list = array();//deklaruje nowa tablice
		foreach ($words as $pos => $word) {
			$word = strtolower($word);//zmieniam na male literki
			if (array_key_exists($word, $frequency_list)) {//porównuje czy dane słowo jest juz w nowej tablicy jako klucz
				++$frequency_list[$word];//jesli jest zwiekszam wartosc o jeden
			}
			else {
				$frequency_list[$word] = 1;//jeli nie ma jeszcze ustawiam wartosc na jeden
			}
		}
		return $this->frequency_list=$frequency_list;//tablica ze słowami jako klucz i iloscia powtórzeń jak zawartosc
		//return $this->freq_filter($this->words, $this->filter) ;
	}
	public function freq_filter($words, $filter) 
	{//filtrowanie słow z mała ilościa powtórzen
		return array_filter($words, function($v) use($filter) { if ($v >= $filter) return $v; } );
		//return $this->word_cloud($words, $this->word_count);
	}
	public function word_cloud($words) 
	{ /* This word cloud generation algorithm was taken from the Wikipedia page on "word cloud"
       with some minor modifications to the implementation */  
		$cloud = "";   
		$tags = 0;//bedzie zliczac ilos tagów    
		$fmax = 100; /* Maximum font size */
		$fmin = 10; /* Minimum font size */
		$tmin = @min($words); /* najmniejsza wartosc wystepujaca w tablicy*/
		$tmax = @max($words); /* najwieksza wartosc wystepujaca w tablicy* */

		foreach ($words as $word => $frequency) {//parametry do wyswietlenia 
			if ($frequency > $tmin) {// 4 > 3
				$font_size = floor(  ( $fmax * ($frequency - $tmin) ) / ( $tmax - $tmin )  );//96*(4-3)/(10-3) = 96*1/7 = 96/7 = 13,71428571428571 = floor 13
                
                /*obliczam stosunek wielkości liczb do słowa */
                $font_size_sec = floor( $fmax * ($frequency / $tmax) / 2 );
                /*obliczam margines */
                $marg =  round( 1-($font_size_sec/70) , 3) ;
                
				/* Define a color index based on the frequency of the word */
				//$r = 152; $g = 82; $b = floor( 255 * ($frequency / $tmax) );// 255* (4 / 10) =255* 0,4 = 102 = floor 102
                $r = 152; 
                $g = 76;
                $b = floor( 255 * ($frequency / $tmax) );// 255* (4 / 10) =255* 0,4 = 102 = floor 102
                
                //if($font_size <= 50 && $font_size >= 30){ $r = floor( 255 * ($frequency / $tmax) * 1.5 ); } else { $r = 152; }
                //if($font_size <= 60 && $font_size >= 20){ $g = floor( 255 * ($frequency / $tmax) / 1.1 ); } else { $g = 128; }
				$color = "rgb($r,$g,$b)";
				//$color = '#' . sprintf('%02s', dechex($r)) . sprintf('%02s', dechex($g)) . sprintf('%02s', dechex($b));
                
				/*obliczam procentowa zawartosc słowa w tekscie*/
				$proc=round(($frequency/$this->word_count)*100, 2);

                
                
			}
			else {
				$font_size = 0;
			}       
			if ($font_size >= $fmin) {//wyswietlanie
				$cloud .= " <span id=\"word\" style=\"font-size: {$font_size}px; color: {$color}; margin-left: {$marg}em;\" title=\"{$word} / freq={$frequency} / p={$proc}% / f1={$font_size}px / f2={$font_size_sec}px / marg={$marg}em / r={$r} / g={$g} / b={$b}\">$word<span id=\"numbers\" style=\"font-size:{$font_size_sec}px;\">(<span id=\"freq\">$frequency</span>-<span id=\"proc\">$proc%</span>)</span></span>";/*{$font_size}/{$font_size_sec}*/
				$tags++;
			}       
		}    
		return array($cloud, $tags); 
		//$cloud .= "</div>";       
	}
	public function word_cloud_v2($words) 
	{ /* This word cloud generation algorithm was taken from the Wikipedia page on "word cloud"
       with some minor modifications to the implementation */  
		$cloud = "";   
		$tags = 0;//bedzie zliczac ilos tagów    
		$fmax = 100; /* Maximum font size */
		$fmin = 20; /* Minimum font size */
		$tmin = @min($words); /* najmniejsza wartosc wystepujaca w tablicy*/
		$tmax = @max($words); /* najwieksza wartosc wystepujaca w tablicy* */

		foreach ($words as $word => $frequency) {//parametry do wyswietlenia 
			if ($frequency > $tmin) {// 4 > 3
				$font_size = floor(  ( $fmax * ($frequency - $tmin) ) / ( $tmax - $tmin )  );//96*(4-3)/(10-3) = 96*1/7 = 96/7 = 13,71428571428571 = floor 13
                
                /*obliczam stosunek wielkości liczb do słowa */
                $font_size > 10 ? $wsp = 2 : $wsp = 1;
                $font_size_sec = floor( $fmax * ($frequency / $tmax) / $wsp );
                /*obliczam margines */
                $marg =  round( 1-($font_size_sec/70) , 3) ;
                
				/* Define a color index based on the frequency of the word */
				//$r = 152; $g = 82; $b = floor( 255 * ($frequency / $tmax) );// 255* (4 / 10) =255* 0,4 = 102 = floor 102
                $r = 152; 
                $g = 76;
                $b = floor( 255 * ($frequency / $tmax) );// 255* (4 / 10) =255* 0,4 = 102 = floor 102
                
                //if($font_size <= 50 && $font_size >= 30){ $r = floor( 255 * ($frequency / $tmax) * 1.5 ); } else { $r = 152; }
                //if($font_size <= 60 && $font_size >= 20){ $g = floor( 255 * ($frequency / $tmax) / 1.1 ); } else { $g = 128; }
				$color = "rgb($r,$g,$b)";
				//$color = '#' . sprintf('%02s', dechex($r)) . sprintf('%02s', dechex($g)) . sprintf('%02s', dechex($b));
                
				/*obliczam procentowa zawartosc słowa w tekscie*/
				$proc=round(($frequency/$this->word_count)*100, 2);

                
                
			}
			else {
				$font_size = 20;
			}       
			if ($font_size >= $fmin) {//wyswietlanie
				$cloud .= " <span id=\"word\" style=\"font-size: {$font_size}px; color: {$color}; margin-left: {$marg}em;\" title=\"{$word} / freq={$frequency} / p={$proc}% / f1={$font_size}px / f2={$font_size_sec}px / marg={$marg}em / r={$r} / g={$g} / b={$b}\">$word<span id=\"numbers\" style=\"font-size:{$font_size_sec}px;\">(<span id=\"freq\">$frequency</span>-<span id=\"proc\">$proc%</span>)</span></span>";/*{$font_size}/{$font_size_sec}*/
				$tags++;
			}       
		}    
		return array($cloud, $tags); 
		//$cloud .= "</div>";       
	}
	function setIgnoreWords($zawartosc){
		$file="ignore.txt";
		//otwarcie pliku
		$fp = fopen($file, "w");
		// zapisanie danych
		fputs($fp, $zawartosc);
		// zamknięcie pliku
		fclose($fp);	
	}
	function getIgnoreWords(){
		$file="ignore.txt";
		if(file_exists($file)){	
			//otwarcie pliku
			$fp = fopen($file, "r");
			// czytam danye
			$dane = fread($fp, filesize($file));
			// zamknięcie pliku
			fclose($fp);
			return $dane;
		}
		else{
			return "0,1";
		}
	}
	function _setFrequencyNumber($zawartosc){
		$file="nr.txt";
		//otwarcie pliku
		$fp = fopen($file, "w");
		// zapisanie danych
		fputs($fp, $zawartosc);
		// zamknięcie pliku
		fclose($fp);	
	}
	function _getFrequencyNumber(){
		$file="nr.txt";
		if(file_exists($file)){		
			//otwarcie pliku
			$fp = fopen($file, "r");
			// czytam danye
			$dane = fread($fp, filesize($file));
			// zamknięcie pliku
			fclose($fp);
			return $dane;
		}
		else{
			return 1;
		}
	}
	function _setModNumber($zawartosc){
		$file="mod.txt";
		//otwarcie pliku
		$fp = fopen($file, "w");
		// zapisanie danych
		fputs($fp, $zawartosc);
		// zamknięcie pliku
		fclose($fp);	
	}
	function _getModNumber(){
		$file="mod.txt";
		if(file_exists($file)){	
			//otwarcie pliku
			$fp = fopen($file, "r");
			// czytam danye
			$dane = fread($fp, filesize($file));
			// zamknięcie pliku
			fclose($fp);
			return $dane;
		}
		else{
			return 1;
		}
	}
	function _setSort($zawartosc){
		$file="sort.txt";
		//otwarcie pliku
		$fp = fopen($file, "w");
		// zapisanie danych
		fputs($fp, $zawartosc);
		// zamknięcie pliku
		fclose($fp);	
	}
	function _getSort(){
		$file="sort.txt";
		if(file_exists($file)){	
			//otwarcie pliku
			$fp = fopen($file, "r");
			// czytam danye
			$dane = fread($fp, filesize($file));
			// zamknięcie pliku
			fclose($fp);
			return $dane;
		}
		else{
			return 'no';
		}
	}
	function _setClearNumber($zawartosc){
		$file="clear_nr.txt";
		//otwarcie pliku
		$fp = fopen($file, "w");
		// zapisanie danych
		fputs($fp, $zawartosc);
		// zamknięcie pliku
		fclose($fp);	
	}
	function _getClearNumber(){
		$file="clear_nr.txt";
		if(file_exists($file)){	
			//otwarcie pliku
			$fp = fopen($file, "r");
			// czytam danye
			$dane = fread($fp, filesize($file));
			// zamknięcie pliku
			fclose($fp);
			return $dane;
		}
		else{
			return 'no';
		}
	}
    function _setShowMod($zawartosc){
		$file="show_mod.txt";
		//otwarcie pliku
		$fp = fopen($file, "w");
		// zapisanie danych
		fputs($fp, $zawartosc);
		// zamknięcie pliku
		fclose($fp);	
	}
	function _getShowMod(){
		$file="show_mod.txt";
		if(file_exists($file)){	
			//otwarcie pliku
			$fp = fopen($file, "r");
			// czytam danye
			$dane = fread($fp, filesize($file));
			// zamknięcie pliku
			fclose($fp);
			return $dane;
		}
		else{
			return 'no';
		}
	}
	// function getIgnoreWords2(){
		// $file="dane.txt";
		// $fp = fopen($file, "r");
		// if(file_exists($file)){
			// //set_time_limit(0);
			// while(!feof($fp)){
				// $dane = (@fread($fp, 1024*8));
				// //ob_flush();
				// //flush();
			// }
		// }
	// }
}
?>