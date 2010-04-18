<?php

######################################################################
# Skidz Partz - Exchange
# ============================================
#
# Copyright (c) 2010 by Dazzle Development Team
# http://www.dazzlecms.com
#
# This program is free software. You can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License.
######################################################################

function truncate($string, $max = 20, $replacement = '...')
{
    if (strlen($string) <= $max)
    {
        return $string;
    }
    $leave = $max - strlen ($replacement);
    return substr_replace($string, $replacement, $leave);
}
	
function menu($page) 
{
    global $prefix, $module_name, $query;
    Open_Table();
    $ThemeSel = get_theme();
    if (file_exists("themes/$ThemeSel/images/wishlist.gif")) {
	echo "<br><center><a href=\"index.php?name=$module_name\"><img src=\"themes/$ThemeSel/images/wishlist.gif\" border=\"0\" alt=\"\"></a><br><br>";
    } else {
	echo "<br><center><a href=\"index.php?name=$module_name\"><img src=\"modules/$module_name/images/wishlist.gif\" border=\"0\" alt=\"\"></a><br><br>";
    }
    echo "<form action=\"index.php?name=$module_name&amp;mode=search&amp;query=$query\" method=\"post\">"
	."<font class=\"content\"><input type=\"text\" size=\"25\" name=\"query\"> <input type=\"submit\" value=\""._SEARCH."\"></font>"
	."</form>";
    Close_Table();
}

function SearchForm() {
    global $module_name;
    echo "<form action=\"index.php?name=$module_name\" method=\"post\">"
	."<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">"
	."<tr><td><font class=\"content\"><input type=\"hidden\" name=\"mode\" value=\"search\"><input type=\"text\" size=\"25\" name=\"query\"> <input type=\"submit\" value=\""._SEARCH."\"></td></tr>"
	."</table>"
	."</form>";
}
	
function get_rating($score, $votes)
{
	global $module_name;
	$rating = array('score'=>0, 'rating'=>0, 'desc'=>_MARKETPLACE_NRATED);
	if ($votes > 0) {
		$rating['score'] = round($score / $votes, 2);
		if ($rating['score'] < 0.5) {
			$rating['rating'] = 0;
			$rating['desc'] = _MARKETPLACE_RUBBISH;
		} elseif ($rating['score'] >= 0.5 && $rating['score'] < 1) {
			$rating['rating'] = 1;
			$rating['desc'] = _MARKETPLACE_RUBBISH;
		} elseif ($rating['score'] >= 1 && $rating['score'] < 1.5) {
			$rating['rating'] = 2;
			$rating['desc'] = _MARKETPLACE_BELOWAVG;
		} elseif ($rating['score'] >= 1.5 && $rating['score'] < 2) {
			$rating['rating'] = 3;
			$rating['desc'] = _MARKETPLACE_BELOWAVG;
		} elseif ($rating['score'] >= 2 && $rating['score'] < 2.5) {
			$rating['rating'] = 4;
			$rating['desc'] = _MARKETPLACE_AVG;
		} elseif ($rating['score'] >= 2.5 && $rating['score'] < 3) {
			$rating['rating'] = 5;
			$rating['desc'] = _MARKETPLACE_AVG;
		} elseif ($rating['score'] >= 3 && $rating['score'] < 3.5) {
			$rating['rating'] = 6;
			$rating['desc'] = _MARKETPLACE_GOOD;
		} elseif ($rating['score'] >= 3.5 && $rating['score'] < 4) {
			$rating['rating'] = 7;
			$rating['desc'] = _MARKETPLACE_GOOD;
		} elseif ($rating['score'] >= 3 && $rating['score'] < 4.5) {
			$rating['rating'] = 8;
			$rating['desc'] = _MARKETPLACE_VGOOD;
		} elseif ($rating['score'] >= 4.5 && $rating['score'] < 5) {
			$rating['rating'] = 9;
			$rating['desc'] = _MARKETPLACE_VGOOD;
		} else {
			$rating['rating'] = 10;
			$rating['desc'] = _MARKETPLACE_EXCELLENT;
		}
	}
	$rating['image'] = '<img src="modules/'.$module_name.'/images/stars/'.$rating['rating'].'.png" alt="'.$rating['desc'].'" title="'.$rating['desc'].'" />';
	return $rating;
}

function convertorderbyin($orderby) {
	if ($orderby != "titleA" AND $orderby != "dateA" AND $orderby != "hitsA" AND $orderby != "ratingA" AND $orderby != "titleD" AND $orderby != "dateD" AND $orderby != "hitsD" AND $orderby != "ratingD") {
	    Header("Location: index.php");
	    die();
	}
    if ($orderby == "titleA")	$orderby = "title ASC";
    if ($orderby == "dateA")	$orderby = "date ASC";
    if ($orderby == "hitsA")	$orderby = "hits ASC";
    if ($orderby == "ratingA")	$orderby = "rating ASC";
    if ($orderby == "titleD")	$orderby = "title DESC"; 
    if ($orderby == "dateD")	$orderby = "date DESC";
    if ($orderby == "hitsD")	$orderby = "hits DESC";
    if ($orderby == "ratingD")	$orderby = "rating DESC";
    return $orderby;
}

function convertorderbyout($orderby) {
	if ($orderby != "title ASC" AND $orderby != "date ASC" AND $orderby != "hits ASC" AND $orderby != "rating ASC" AND $orderby != "title DESC" AND $orderby != "date DESC" AND $orderby != "hits DESC" AND $orderby != "rating DESC") {
	    Header("Location: index.php");
	    die();
	}
    if ($orderby == "title ASC")		$orderby = "titleA";
    if ($orderby == "date ASC")			$orderby = "dateA";
    if ($orderby == "hits ASC")			$orderby = "hitsA";
    if ($orderby == "rating ASC")	$orderby = "ratingA";
    if ($orderby == "title DESC")		$orderby = "titleD";
    if ($orderby == "date DESC")		$orderby = "dateD";
    if ($orderby == "hits DESC")		$orderby = "hitsD";
    if ($orderby == "rating DESC")	$orderby = "ratingD";
    return $orderby;
}

//
function popgraphic($hits) {
    global $module_name;
    include("modules/$module_name/config.php");
    if ($hits>=$popular) {
	echo "&nbsp;<img src=\"modules/$module_name/images/popular.gif\" alt=\""._POPULAR."\">";
    }
}

function convertorderbytrans($orderby) {
	if ($orderby != "hits ASC" AND $orderby != "hits DESC" AND $orderby != "title ASC" AND $orderby != "title DESC" AND $orderby != "date ASC" AND $orderby != "date DESC" AND $orderby != "downloadratingsummary ASC" AND $orderby != "downloadratingsummary DESC") {
	    Header("Location: index.php");
	    die();
	}
    if ($orderby == "hits ASC")			$orderbyTrans = ""._POPULARITY1."";
    if ($orderby == "hits DESC")		$orderbyTrans = ""._POPULARITY2."";
    if ($orderby == "title ASC")		$orderbyTrans = ""._TITLEAZ."";
    if ($orderby == "title DESC")		$orderbyTrans = ""._TITLEZA."";
    if ($orderby == "date ASC")			$orderbyTrans = ""._DDATE1."";
    if ($orderby == "date DESC")		$orderbyTrans = ""._DDATE2."";
    if ($orderby == "downloadratingsummary ASC")	$orderbyTrans = ""._RATING1."";
    if ($orderby == "downloadratingsummary DESC")	$orderbyTrans = ""._RATING2."";
    return $orderbyTrans;
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