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

//complete_review
function complete_review($error)
{
    global $module_name;
    include('modules/'.$module_name.'/config.php');
    if ($error == 'none') echo '<center><font class="content"><b>'._COMPLETEVOTE1.'</b></font></center>';
    if ($error == 'anonflood') echo '<center><font class="option"><b>'._COMPLETEVOTE2.'</b></font></center><br />';
    if ($error == 'regflood') echo '<center><font class="option"><b>'._COMPLETEVOTE3.'</b></font></center><br />';
    if ($error == 'postervote') echo '<center><font class="option"><b>'._COMPLETEVOTE4.'</b></font></center><br />';
    if ($error == 'nullerror') echo '<center><font class="option"><b>'._COMPLETEVOTE5.'</b></font></center><br />';
    if ($error == 'outsideflood') echo '<center><font class="option"><b>'._COMPLETEVOTE6.'</b></font></center><br />';
}

function getparent($parentid,$title) {
    global $prefix, $db;
    $parentid = intval($parentid);
    $sql = "SELECT cid, title, parentid FROM ".$prefix."_marketplace_categories WHERE cid='$parentid'";
    $result = $db->sql_query($sql);
    $row = $db->sql_fetchrow($result);
  	$cid = intval($row['cid']);
  	$ptitle = filter($row['title'], "nohtml");
  	$pparentid = intval($row['parentid']);
    if ($ptitle=="$title") $title = $title; 
    	elseif (!empty($ptitle)) $title = $ptitle."/".$title;
    if ($pparentid!=0) 
	{
		$title = getparent($pparentid, $ptitle);
    }
    return $title;
}

function getparentlink($parentid,$title) {
    global $prefix, $db, $module_name;
    $parentid = intval($parentid);
    $sql = "SELECT cid, title, parentid FROM ".$prefix."_marketplace_categories WHERE cid='$parentid'";
    $result = $db->sql_query($sql);
    $row = $db->sql_fetchrow($result);
    $cid = intval($row['cid']);
  	$ptitle = filter($row['title'], "nohtml");
  	$pparentid = intval($row['parentid']);
    if (!empty($ptitle)) $title="<a href=index.php?name=$module_name&amp;mode=viewdownload&amp;cid=$cid>$ptitle</a>/".$title;
    if ($pparentid!=0) {
    	$title=getparentlink($pparentid,$ptitle);
    }
    return $title;
}

function item_parent_link($parentid,$title) {
    global $prefix, $db, $module_name;
    $parentid = intval($parentid);
    $sql = "SELECT cid, title, parentid FROM ".$prefix."_marketplace_categories WHERE cid='$parentid'";
    $result = $db->sql_query($sql);
    $row = $db->sql_fetchrow($result);
    $cid = intval($row['cid']);
  	$ptitle = filter($row['title'], "nohtml");
  	$pparentid = intval($row['parentid']);
    if (!empty($ptitle)) $title="<a href=index.php?name=$module_name&amp;mode=viewdownload&amp;cid=$cid>$ptitle</a>";
    if ($pparentid!=0) {
    	$title=item_parent_link($pparentid,$ptitle);
    }
    return $title;
}

function item_categories_link($parentid,$title) {
    global $prefix, $db, $module_name;
    $parentid = intval($parentid);
    $sql = "SELECT cid, title, parentid FROM ".$prefix."_marketplace_categories WHERE cid='$parentid'";
    $result = $db->sql_query($sql);
    $row = $db->sql_fetchrow($result);
    $cid = intval($row['cid']);
  	$ptitle = filter($row['title'], "nohtml");
  	$pparentid = intval($row['parentid']);
    if (!empty($ptitle)) $title="<a href=index.php?name=$module_name&amp;mode=viewdownload&amp;cid=$cid>$ptitle</a>";
    if ($pparentid!=0) {
    	$title=item_parent_link($pparentid,$ptitle);
    }
    return $title;
}

function resampimagejpg( $forcedwidth, $forcedheight, $sourcefile, $destfile )
{
    $fw = $forcedwidth;
    $fh = $forcedheight;
    $is = getimagesize( $sourcefile );
    if( $is[0] >= $is[1] )
    {
        $orientation = 0;
    }
    else
    {
        $orientation = 1;
        $fw = $forcedheight;
        $fh = $forcedwidth;
    }
    if ( $is[0] > $fw || $is[1] > $fh )
    {
        if( ( $is[0] - $fw ) >= ( $is[1] - $fh ) )
        {
            $iw = $fw;
            $ih = ( $fw / $is[0] ) * $is[1];
        }
        else
        {
            $ih = $fh;
            $iw = ( $ih / $is[1] ) * $is[0];
        }
        $t = 1;
    }
    else
    {
        $iw = $is[0];
        $ih = $is[1];
        $t = 2;
    }
    if ( $t == 1 )
    {
        $img_src = imagecreatefromjpeg( $sourcefile );
        $img_dst = imagecreatetruecolor( $iw, $ih );
        imagecopyresampled( $img_dst, $img_src, 0, 0, 0, 0, $iw, $ih, $is[0], $is[1] );
        if( !imagejpeg( $img_dst, $destfile, 90 ) )
        {
            //exit( );
			echo "error";
        }
		echo $destfile;
    }
    else if ( $t == 2 )
    {
        copy( $sourcefile, $destfile );
    }
}

##
function resampimagepng( $forcedwidth, $forcedheight, $sourcefile, $destfile )
{
    $fw = $forcedwidth;
    $fh = $forcedheight;
    $is = getimagesize( $sourcefile );
    if( $is[0] >= $is[1] )
    {
        $orientation = 0;
    }
    else
    {
        $orientation = 1;
        $fw = $forcedheight;
        $fh = $forcedwidth;
    }
    if ( $is[0] > $fw || $is[1] > $fh )
    {
        if( ( $is[0] - $fw ) >= ( $is[1] - $fh ) )
        {
            $iw = $fw;
            $ih = ( $fw / $is[0] ) * $is[1];
        }
        else
        {
            $ih = $fh;
            $iw = ( $ih / $is[1] ) * $is[0];
        }
        $t = 1;
    }
    else
    {
        $iw = $is[0];
        $ih = $is[1];
        $t = 2;
    }
    if ( $t == 1 )
    {
        $img_src = imagecreatefrompng( $sourcefile );
        $img_dst = imagecreatetruecolor( $iw, $ih );
        imagecopyresampled( $img_dst, $img_src, 0, 0, 0, 0, $iw, $ih, $is[0], $is[1] );
        if( !imagepng( $img_dst, $destfile, 90 ) )
        {
            exit( );
        }
    }
    else if ( $t == 2 )
    {
        copy( $sourcefile, $destfile );
    }
}

function resampimagegif( $forcedwidth, $forcedheight, $sourcefile, $destfile )
{
    $fw = $forcedwidth;
    $fh = $forcedheight;
    $is = getimagesize( $sourcefile );
    if( $is[0] >= $is[1] )
    {
        $orientation = 0;
    }
    else
    {
        $orientation = 1;
        $fw = $forcedheight;
        $fh = $forcedwidth;
    }
    if ( $is[0] > $fw || $is[1] > $fh )
    {
        if( ( $is[0] - $fw ) >= ( $is[1] - $fh ) )
        {
            $iw = $fw;
            $ih = ( $fw / $is[0] ) * $is[1];
        }
        else
        {
            $ih = $fh;
            $iw = ( $ih / $is[1] ) * $is[0];
        }
        $t = 1;
    }
    else
    {
        $iw = $is[0];
        $ih = $is[1];
        $t = 2;
    }
    if ( $t == 1 )
    {
        $img_src = imagecreatefromgif( $sourcefile );
        $img_dst = imagecreatetruecolor( $iw, $ih );
        imagecopyresampled( $img_dst, $img_src, 0, 0, 0, 0, $iw, $ih, $is[0], $is[1] );
        if( !imagegif( $img_dst, $destfile, 90 ) )
        {
            exit( );
        }
    }
    else if ( $t == 2 )
    {
        copy( $sourcefile, $destfile );
    }
}
	
function menu($page) 
{
    global $prefix, $module_name, $query;
    Open_Table();
    $ThemeSel = get_theme();
    if (file_exists("themes/$ThemeSel/images/marketplace.gif")) {
	echo "<br><center><a href=\"index.php?name=$module_name\"><img src=\"themes/$ThemeSel/images/marketplace.gif\" border=\"0\" alt=\"\"></a><br><br>";
    } else {
	echo "<br><center><a href=\"index.php?name=$module_name\"><img src=\"modules/$module_name/images/marketplace.gif\" border=\"0\" alt=\"\"></a><br><br>";
    }
    echo "<form action=\"index.php?name=$module_name&amp;mode=search&amp;query=$query\" method=\"post\">"
	."<font class=\"content\"><input type=\"text\" size=\"25\" name=\"query\"> <input type=\"submit\" value=\""._SEARCH."\"></font>"
	."</form>";
	echo '<center>'._PAGE_VIEW.':&nbsp;[&nbsp;';
	if ($page == "Popular") 
	{
		echo _PAGE_POPULAR.'&nbsp;|&nbsp;';
	} 
	else 
	{
		echo '<a href="index.php?name='.$module_name.'&amp;mode=Popular">'._PAGE_POPULAR.'</a>&nbsp;|&nbsp;';
	}
	if ($page == "Featured") 
	{
		echo _PAGE_FEATURED.'&nbsp;|&nbsp;';
	} 
	else 
	{
		echo '<a href="index.php?name='.$module_name.'&amp;mode=Featured">'._PAGE_FEATURED.'</a>&nbsp;|&nbsp;';
	}
	if ($page == "New") {
		echo _PAGE_NEW.'&nbsp;|&nbsp;';
	} 
	else 
	{
		echo '<a href="index.php?name='.$module_name.'&amp;mode=New">'._PAGE_NEW.'</a>&nbsp;|&nbsp;';
	}
	if ($page == "Free") {
		echo _PAGE_FREE.'&nbsp;|&nbsp;';	
	} 
	else 
	{
		echo '<a href="index.php?name='.$module_name.'&amp;mode=Free">'._PAGE_FREE.'</a> | ';
	}
	if ($page == "Merchants") 
	{
		echo _PAGE_MERCHANTS.'&nbsp;]</center>';
	} 
	else 
	{
		echo '<a href="index.php?name='.$module_name.'&amp;mode=Merchants">'._PAGE_MERCHANTS.'</a>&nbsp;]</center>';
	}
    Close_Table();
}

function Merchants_Menu()
{
    global $user, $cookie, $module_name;
	$user2 = base64_decode($user);
	$user2 = addslashes($user2);
	$cookie = explode(":", $user2);
	cookiedecode($user);
	$firstname = $cookie[1];
	$lastname = $cookie[2];	
	if($user)
	{
	//Get Started
	define('_GET_STARTED', 'Get Started');
	define('_VIEW_ITEMS', 'View My Items');
	define('_VIEW_INVENTORY', 'My Inventory');
	define('_VIEW_SALES', 'Sales');
	define('_VIEW_TRAFFIC', 'My Traffic');
	define('_VIEW_HELP', 'Help');
	//@todo pages: Started,
	//echo "&nbsp;[&nbsp;".GET_STARTED."&nbsp;&nbsp;|&nbsp;View My Items&nbsp;&nbsp;|&nbsp;Edit My Items&nbsp;&nbsp;|&nbsp;My Inventory&nbsp;&nbsp;&nbsp;|&nbsp;Sales&nbsp;&nbsp;|&nbsp;My Traffic&nbsp;&nbsp;|&nbsp;Help&nbsp;]&nbsp;";
	Open_Table();
	   echo '<center>&nbsp;[&nbsp;<a href="index.php?name='.$module_name.'&amp;file=Started">'._GET_STARTED.'</a>&nbsp;|&nbsp;<a href="index.php?name='.$module_name.'&amp;file=Add">'._ADD.'</a>&nbsp;|&nbsp;<a href="index.php?name='.$module_name.'&amp;file=Edit">'._VIEW_ITEMS.'</a>&nbsp;|&nbsp;<a href="index.php?name='.$module_name.'&amp;file=Inventory">'._VIEW_INVENTORY.'</a>&nbsp;|&nbsp;<a href="index.php?name='.$module_name.'&amp;file=Sales">'._VIEW_SALES.'</a>&nbsp;|&nbsp;<a href="index.php?name='.$module_name.'&amp;file=Traffic">'._VIEW_TRAFFIC.'</a>&nbsp;|&nbsp;<a href="index.php?name='.$module_name.'&amp;file=Help">'._VIEW_HELP.'</a>&nbsp;]&nbsp;</center>';
	Close_Table();
	}
}

	function categories($subcategories) { //categories(0)
		echo "<td width=\"8\">&nbsp;</td><td valign=\"top\" width=\"140\">";
		block($subcategories);
		echo "</td></tr></table>";
		include("footer.php");
	}

	function block($subcategories) 
	{
		global $admin_file, $db, $prefix, $module_name;
		if($subcategories != 0)
		{
		   $sql = "SELECT cid, title FROM ".$prefix."_marketplace_categories WHERE parentid='".$subcategories."' ORDER BY title";
		   $result = $db->sql_query($sql);
		   $admintitle  = "Subcategories";
		   $adminblock .= "<table cellpadding=\"2\" cellspacing=\"0\" border=\"0\" width=\"100%\">";
		   while ($row = $db->sql_fetchrow($result)) 
		   {		
		      $cid = intval($row['cid']);
		      $title = stripslashes(check_html($row['title'], 'nohtml'));
		      $adminblock .= "<tr><td class=\"alignCenter\">";
		      $adminblock .= "<a href=\"index.php?name=".$module_name."&amp;mode=view_category&amp;cid=".$cid."\">";
		      $adminblock .= "".$title."</a> ";
		      $adminblock .= "</td></tr>";
		   }
		}
		else
		{
		   $sql = 'SELECT cid, title FROM '.$prefix.'_marketplace_categories WHERE parentid=0 ORDER BY title';
		   $result = $db->sql_query($sql);
		   $admintitle  = "Categories";
		   $adminblock .= "<table cellpadding=\"2\" cellspacing=\"0\" border=\"0\" width=\"100%\">";
		   while ($row2 = $db->sql_fetchrow($result)) 
		   {		
		      $cid = intval($row2['cid']);
		      $title = stripslashes(check_html($row2['title'], 'nohtml'));
		      $adminblock .= "<tr><td class=\"alignCenter\">";
		      $adminblock .= "<a href=\"index.php?name=".$module_name."&amp;mode=view_category&amp;cid=".$cid."\">";
		      $adminblock .= "".$title."</a> ";
		      $adminblock .= "</td></tr>";
		   }		
		}
		$adminblock .= "<tr><td>";
		$adminblock .= "</td></tr>";		
		$adminblock .= "<tr><td class=\"redBold\">";
		$adminblock .= "<a href=\"modules.php?name=".$module_name."&amp;file=mature_popup\">";
		$adminblock .= "Enable Mature Content</a>";
		$adminblock .= "</td></tr>";		
		$adminblock .= "</table>";
		themesidebox($admintitle, $adminblock);
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