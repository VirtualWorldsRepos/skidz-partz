<?php

function makePass() 
{
	$cons = "bcdfghjklmnpqrstvwxyz";
	$vocs = "aeiou";
	for ($x=0; $x < 6; $x++) 
	{
		mt_srand ((double) microtime() * 1000000);
		$con[$x] = substr($cons, mt_rand(0, strlen($cons)-1), 1);
		$voc[$x] = substr($vocs, mt_rand(0, strlen($vocs)-1), 1);
	}
	mt_srand((double)microtime()*1000000);
	$num1 = mt_rand(0, 9);
	$num2 = mt_rand(0, 9);
	$makepass = $con[0] . $voc[0] .$con[2] . $num1 . $num2 . $con[3] . $voc[3] . $con[4];
	return($makepass);
}

function clean_text($text)
{
   return ( get_magic_quotes_gpc() ? stripslashes($text) : $text );
}

function FixQuotes ($what = "") {
	while (stristr($what, "\\\\'")) {
		$what = str_replace("\\\\'","'",$what);
	}
	return $what;
}

/*********************************************************/
/* text filter                                           */
/*********************************************************/

function check_words($Message) {
	global $CensorMode, $CensorReplace, $EditedMessage;
	include(dirname(__FILE__) . '/../config.php');
	$EditedMessage = $Message;
	if ($CensorMode != 0) {
		if (is_array($CensorList)) {
			$Replace = $CensorReplace;
			if ($CensorMode == 1) {
				for ($i = 0; $i < count($CensorList); $i++) {
					$EditedMessage = eregi_replace("$CensorList[$i]([^a-zA-Z0-9])","$Replace\\1",$EditedMessage);
				}
			} elseif ($CensorMode == 2) {
				for ($i = 0; $i < count($CensorList); $i++) {
					$EditedMessage = eregi_replace("(^|[^[:alnum:]])$CensorList[$i]","\\1$Replace",$EditedMessage);
				}
			} elseif ($CensorMode == 3) {
				for ($i = 0; $i < count($CensorList); $i++) {
					$EditedMessage = eregi_replace("$CensorList[$i]","$Replace",$EditedMessage);
				}
			}
		}
	}
	return ($EditedMessage);
}

function delQuotes($string){
	/* no recursive function to add quote to an HTML tag if needed */
	/* and delete duplicate spaces between attribs. */
	$tmp="";    # string buffer
	$result=""; # result string
	$i=0;
	$attrib=-1; # Are us in an HTML attrib ?   -1: no attrib   0: name of the attrib   1: value of the atrib
	$quote=0;   # Is a string quote delimited opened ? 0=no, 1=yes
	$len = strlen($string);
	while ($i<$len) {
		switch($string[$i]) { # What car is it in the buffer ?
		case "\"": #"       # a quote.
		if ($quote==0) {
			$quote=1;
		} else {
			$quote=0;
			if (($attrib>0) && ($tmp != "")) { $result .= "=\"$tmp\""; }
			$tmp="";
			$attrib=-1;
		}
		break;
		case "=":           # an equal - attrib delimiter
		if ($quote==0) {  # Is it found in a string ?
		$attrib=1;
		if ($tmp!="") $result.=" $tmp";
		$tmp="";
		} else $tmp .= '=';
		break;
		case " ":           # a blank ?
		if ($attrib>0) {  # add it to the string, if one opened.
		$tmp .= $string[$i];
		}
		break;
		default:            # Other
		if ($attrib<0)    # If we weren't in an attrib, set attrib to 0
		$attrib=0;
		$tmp .= $string[$i];
		break;
		}
		$i++;
	}
	if (($quote!=0) && ($tmp != "")) {
		if ($attrib==1) $result .= "=";
		/* If it is the value of an atrib, add the '=' */
		$result .= "\"$tmp\"";  /* Add quote if needed (the reason of the function ;-) */
	}
	return $result;
}

function check_html ($str, $strip="") {
	/* The core of this code has been lifted from phpslash */
	/* which is licenced under the GPL. */
	include(dirname(__FILE__) . '/../config.php');
	if ($strip == "nohtml")
	$AllowableHTML = array('');
	$str = stripslashes($str);
	$str = eregi_replace("<[[:space:]]*([^>]*)[[:space:]]*>",'<\\1>', $str);
	// Delete all spaces from html tags .
	$str = eregi_replace("<a[^>]*href[[:space:]]*=[[:space:]]*\"?[[:space:]]*([^\" >]*)[[:space:]]*\"?[^>]*>",'<a href="\\1">', $str);
	// Delete all attribs from Anchor, except an href, double quoted.
	$str = eregi_replace("<[[:space:]]* img[[:space:]]*([^>]*)[[:space:]]*>", '', $str);
	// Delete all img tags
	$str = eregi_replace("<a[^>]*href[[:space:]]*=[[:space:]]*\"?javascript[[:punct:]]*\"?[^>]*>", '', $str);
	// Delete javascript code from a href tags -- Zhen-Xjell @ http://nukecops.com
	$tmp = "";
	while (ereg("<(/?[[:alpha:]]*)[[:space:]]*([^>]*)>",$str,$reg)) 
	{
		$i = strpos($str,$reg[0]);
		$l = strlen($reg[0]);
		if ($reg[1][0] == "/") $tag = strtolower(substr($reg[1],1));
		else $tag = strtolower($reg[1]);
		if ($a = $AllowableHTML[$tag])
		if ($reg[1][0] == "/") $tag = "</$tag>";
		elseif (($a == 1) || ($reg[2] == "")) $tag = "<$tag>";
		else {
			# Place here the double quote fix function.
			$attrb_list = delQuotes($reg[2]);
			// A VER
			//$attrb_list = ereg_replace("&","&amp;",$attrb_list);
			$tag = "<$tag" . $attrb_list . ">";
		} # Attribs in tag allowed
		else $tag = "";
		$tmp .= substr($str,0,$i) . $tag;
		$str = substr($str,$i+$l);
	}
	$str = $tmp . $str;
	return $str;
	exit;
	/* Squash PHP tags unconditionally */
	$str = ereg_replace("<\?","",$str);
	return $str;
}

function filter_text($Message, $strip="") {
	global $EditedMessage;
	check_words($Message);
	$EditedMessage=check_html($EditedMessage, $strip);
	return ($EditedMessage);
}

function filter($what, $strip="", $save="", $type="") {
	if ($strip == "nohtml") 
	{
		$what = check_html($what, $strip);
		$what = htmlentities(trim($what), ENT_QUOTES);
		// If the variable $what doesn't comes from a preview screen should be converted
		if ($type != "preview" && $save != 1) 
		{
			$what = html_entity_decode($what, ENT_QUOTES);
		}
	}
	if ($save == 1) 
	{
		$what = check_words($what);
		$what = check_html($what, $strip);
		$what = addslashes($what);
	} 
	else 
	{
		$what = stripslashes(FixQuotes($what));
		$what = check_words($what);
		$what = check_html($what, $strip);
	}
	return($what);
}

function CallLSLScript($URL, $Data, $Timeout = 10)
{
 //Parse the URL into Server, Path and Port
 $Host = str_ireplace("http://", "", $URL);
 $Path = explode("/", $Host, 2);
 $Host = $Path[0];
 $Path = $Path[1];
 $PrtSplit = explode(":", $Host);
 $Host = $PrtSplit[0];
 $Port = $PrtSplit[1];
 
 //Open Connection
 $Socket = @fsockopen($Host, $Port, $Dummy1, $Dummy2, $Timeout);
 if ($Socket)
 {
  //Send Header and Data
  @fputs($Socket, "POST /$Path HTTP/1.1\r\n");
  @fputs($Socket, "Host: $Host\r\n");
  @fputs($Socket, "Content-type: application/x-www-form-urlencoded\r\n");
  @fputs($Socket, "User-Agent: Opera/9.01 (Windows NT 5.1; U; en)\r\n");
  @fputs($Socket, "Accept-Language: de-DE,de;q=0.9,en;q=0.8\r\n");
  @fputs($Socket, "Content-length: ".strlen($Data)."\r\n");
  @fputs($Socket, "Connection: close\r\n\r\n");
  @fputs($Socket, $Data);
 
  //Receive Data
  while(!@feof($Socket))
   {$res .= @fgets($Socket, 4096);}
  fclose($Socket);
 }
 
 //ParseData and return it
 $res = explode("\r\n\r\n", $res);
 return $res[1];
}
?>