<?php
/*
	This code is released unto the public domain
	This script is originally from: http://www.googlecommunity.com/scripts/google-pagerank.php
	It is also hosted, and was modified slightly, by: http://www.searchengineengine.com
	The architecture is:
		getrank($url) function - The function called externally to get the PageRank
			strord($url) function - Function called by getrank() to convert the url string into a numeric string
			GoogleCH($numericstring) function - Function called by getrank() to get the checksum of the url
				mix($a, $b, $c) function - Called by GoogleCH() during checksum calculation
					zeroFill($a, $b) function - Called by mix() during mixing
*/

//header("Content-Type: text/plain; charset=utf-8");		//Uncommenting this makes the file downloadable rather than executable

define("GOOGLE_MAGIC", 0xE6359A60);		//Define the named constant "GOOGLE_MAGIC"

//unsigned shift right
function zeroFill($a, $b) { 
	$z = hexdec(80000000);
	if ($z & $a) {
		$a = ($a>>1);
		$a &= (~$z);
		$a |= 0x40000000;
		$a = ($a>>($b-1));
	}
	else {
		$a = ($a>>$b);
	}
	return $a;
}

function mix($a, $b, $c) {		//This function is used in the Google Checksum calculation
	$a -= $b;
	$a -= $c;
	$a ^= (zeroFill($c,13));
	$b -= $c;
	$b -= $a;
	$b ^= ($a<<8);
	$c -= $a;
	$c -= $b;
	$c ^= (zeroFill($b,13));
	$a -= $b;
	$a -= $c;
	$a ^= (zeroFill($c,12));
	$b -= $c;
	$b -= $a;
	$b ^= ($a<<16);
	$c -= $a;
	$c -= $b;
	$c ^= (zeroFill($b,5));
	$a -= $b;
	$a -= $c;
	$a ^= (zeroFill($c,3));
	$b -= $c;
	$b -= $a;
	$b ^= ($a<<10);
	$c -= $a;
	$c -= $b;
	$c ^= (zeroFill($b,15));
	return array($a,$b,$c);
}

function GoogleCH($url, $length=null, $init=GOOGLE_MAGIC) {		//Calculate the Google Checksum for a given URL
	if(is_null($length)) {
		$length = sizeof($url);
    }
	$a = $b = 0x9E3779B9;
	$c = $init;
	$k = 0;
	$len = $length;
	while($len >= 12) {
		$a += ($url[$k+0] +($url[$k+1]<<8) +($url[$k+2]<<16) +($url[$k+3]<<24));
		$b += ($url[$k+4] +($url[$k+5]<<8) +($url[$k+6]<<16) +($url[$k+7]<<24));
		$c += ($url[$k+8] +($url[$k+9]<<8) +($url[$k+10]<<16)+($url[$k+11]<<24));
		$mix = mix($a,$b,$c);
		$a = $mix[0];
		$b = $mix[1];
		$c = $mix[2];
		$k += 12;
		$len -= 12;
	}
	$c += $length;
	switch($len)			/* all the case statements fall through */
    {
		case 11: $c+=($url[$k+10]<<24);
		case 10: $c+=($url[$k+9]<<16);
		case 9 : $c+=($url[$k+8]<<8);
			/* the first byte of c is reserved for the length */
		case 8 : $b+=($url[$k+7]<<24);
		case 7 : $b+=($url[$k+6]<<16);
		case 6 : $b+=($url[$k+5]<<8);
		case 5 : $b+=($url[$k+4]);
		case 4 : $a+=($url[$k+3]<<24);
		case 3 : $a+=($url[$k+2]<<16);
		case 2 : $a+=($url[$k+1]<<8);
		case 1 : $a+=($url[$k+0]);
			/* case 0: nothing left to add */
	}
	$mix = mix($a,$b,$c);
	return $mix[2];
}

function strord($string) {		//converts a string into an array of integers containing the numeric value of the char
	for($i=0;$i<strlen($string);$i++) {
		$result[$i] = ord($string{$i});
	}
	return $result;
}

function getrank($url, $prefix="info:", $datacenter="www.google.com") {				//This is the function used to get the PageRank value.
		//If $prefix is "info:", then the Toolbar pagerank will be returned.
		//$datacenter sets the datacenter to get the results from. e.g., "www.google.com", "216.239.53.99", "66.102.11.99".
	$url = $prefix.$url;
	$ch = GoogleCH(strord($url));		//Get the Google checksum for $url using the GoogleCH function.
	$file = "http://$datacenter/search?client=navclient-auto&ch=6$ch&features=Rank&q=$url";
		//To get the Crawl Date instead of the PageRank, change "&features=Rank" to "&features=Crawldate"
		//To get detailed XML results, remove "&features=Rank"
	$oldlevel = error_reporting(0);		//Suppress error reporting temporarily.
	$data = file($file);
	error_reporting($oldlevel);			//Restart error reporting.
	if(!$data || preg_match("/(.*)\.(.*)/i", $url)==0) return "N/A";			//If the Google data is unavailable, or the URL is invalid, return "N/A".
		//The preg_match check is a very basic url validator that only checks if the URL has a period in it.
	$rankarray = explode (":", $data[2]);		//There are two line breaks before the PageRank data on the Google page.
	$rank = trim($rankarray[2]);		//Trim whitespace and line breaks.
	if($rank=="") return "N/A";			//Return N/A if no rank.
	return $rank;
}

function getrealrank($url, $datacenter="www.google.com"){
	$ch = GoogleCH(strord($url));
	$array = xmltoarray("http://$datacenter/search?client=navclient-auto&ch=6$ch&q=$url");
	$infoarray = $array['GSP'][0]['RES'][0]['R'];
	$nonwwwurl = str_replace("www.", "", $url);
	$urlpermutationsarray = array($url, "http://".$url, "http://www.".$url, $url."/", "http://".$url."/", "http://www.".$url."/", $nonwwwurl, "http://".$nonwwwurl, "http://www.".$nonwwwurl, $nonwwwurl."/", "http://".$nonwwwurl."/", "http://www.".$nonwwwurl."/"); 
	for($i=0; $i<count($infoarray); $i++){
		$urlU = $infoarray[$i]['U'];		//U is the URL. UE is the 'clean' URL.
		$urlrank = $infoarray[$i]['RK'];
		foreach($urlpermutationsarray as $permutation){
			if(strtolower($permutation)==strtolower($urlU)) return $urlrank;		//Case insensitive match.
		}
	}
	return "Unknown";		//If no matches found.
}

/*	//Example of how to use the getrank function.
$url = "www.google.com";
echo "PageRank of ".$url." is:<br />".getrank($url);
*/

?>