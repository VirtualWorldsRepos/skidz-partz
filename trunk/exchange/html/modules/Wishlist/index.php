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

if (!defined('MODULE_FILE')) {
    die ("You can't access this file directly...");
}

if (isset($min)) {
    $min = intval($min);
}

if (isset($show)) {
    $show = intval($show);
}

define('INDEX_FILE', true);
require_once("mainfile.php");
$module_name = basename(dirname(__FILE__));
get_lang($module_name);
$pagetitle = "- "._MARKETPLACE."";
require_once("modules/$module_name/config.php");
require_once('modules/'.$module_name.'/includes/functions.php');
define('INDEX_FILE', true);

function latest_wishes($sid, $min, $orderby, $show)
{
	global $db, $prefix, $admin_file, $module_name;
	include ("header.php");
    include('modules/'.$module_name.'/config.php');	
	menu('New');
    echo "<br />";
	//echo "<table width=\"100%\" valign=\"top\"><tr><td valign='top'>";
	if (!isset($min)) $min=0;
    if (!isset($max)) $max=$min+$perpage;
    if(!empty($orderby)) 
	{
		$orderby = convertorderbyin($orderby);
    } else {
		$orderby = "title ASC";
    }
    if (!empty($show)) {
		$perpage = $show;
    } else {
		$show=$perpage;
    }	
	Open_Table();
	echo '<center><font class="title"><b>'._TODAYS_WISHES.'</b></font></center><br />';
	//id 	item_id 	title 	amount 	priority 	quantity 	tags 	notes 	access 	firstname 	lastname
    if(!is_numeric($min))
	{
        $min=0;
    }	
	$wishlist = $db->sql_query("SELECT id, item_id, title, amount, priority, quantity, tags, notes, access, firstname, lastname FROM ".$prefix."_marketplace_wishlist order by title limit $min,$perpage"); // order by date DESC 
	$fullcountresult=$db->sql_query("SELECT * FROM ".$prefix."_marketplace_wishlist");
    $totalselecteddownloads = $db->sql_numrows($fullcountresult);
	$x=0;
	while(list($id, $item_id, $title, $amount, $priority, $quantity, $tags, $notes, $access, $firstname, $lastname) = $db->sql_fetchrow($wishlist)) 
	{
	   $products = $db->sql_query("SELECT id, title, lindens, dollars, image, thumbnail, firstname, lastname, date, hits, rating, votes, comments FROM ".$prefix."_marketplace_items WHERE id = '".$item_id."'"); // order by date DESC
	   while(list($id2, $title2, $lindens2, $dollars2, $image2, $thumbnail2, $firstname2, $lastname2, $date2, $hits2, $rating2, $votes2, $comments2) = $db->sql_fetchrow($products)) 
	   {
	   if(!is_numeric($sid))
	   {
          $sid = intval($id);
	   }	   
	   echo "<table class=\"forumline\" cellspacing=\"1\" width=\"100%\">"
       ."<tr>";
	   $rating_info = get_rating($rating, $votes);
       $thirty_days = time()-2592000;
	   $seven_days = time()-604800;
	   $now = time();
	   echo "<td class=\"cat\" colspan=\"3\">&nbsp;".$rating_info['image']."&nbsp;<a class=\"cattitle\" href=\"index.php?name=".$module_name."&amp;mode=view_details&amp;lid=".$id."\">".$title."</a>&nbsp;</td>"
	       ."</tr><tr>"
           ."<td class=\"row1\" rowspan=\"2\" align=\"center\" style=\"width: 100px;\">"
           ."<!-- IF dl_item.IMG_SCREEN -->";
	   if(file_exists($thumbnail2) && file_exists($image2))
	   {
	      echo "<a href=\"index.php?name=".$module_name."&amp;mode=view_details&amp;lid=".$id."\"><img src=\"".$thumbnail2."\" alt=\"\" /></a>";
	   }
	   else
	   {
	      echo "<img src=\"modules/".$module_name."/images/missing.png\" alt=\"{dl_item.L_NOSCREENS}\" title=\"{dl_item.L_NOSCREENS}\" />";
       }
       echo "</td>"
       ."<td class=\"row1\" valign=\"top\" style=\"height: 10px;\">"
       ."Wished by :$firstname $lastname</td>" //<img src=\"modules/".$module_name."/images/gift.png\" alt=\"{dl_item.L_NOSCREENS}\" title=\"{dl_item.L_NOSCREENS}\" />
       ."<td class=\"row1\" rowspan=\"2\" align=\"center\" style=\"width: 75px;\"><a href=\"index.php?name=Marketplace&file=gift_option&op=item_id&amp;id=".$item_id."\"><img src=\"modules/".$module_name."/images/gift.png\" alt=\""._GIFT."\" title=\""._GIFT."\"/></a></td>"
       ."</tr><tr>"
       ."<td class=\"row1\" style=\"vertical-align: top;\"><span class=\"genmed\">$notes</span></td>"
       ."</tr><tr>"
       ."<td align=\"center\" class=\"row2\">$".$amount."</td>"
       ."<td class=\"row2\" colspan=\"2\">";
	   foreach (array($tags) as $tag)
	   {
	      echo $tag;
		}
	   echo"</td></tr></table><br />";
	   	   } // end while1
	$x++;
	} // end while2
	// end while
    $orderby = convertorderbyout($orderby);
	// Calculates how many pages exist.  Which page one should be on, etc...
    $downloadpagesint = ($totalselecteddownloads / $perpage);
    $downloadpageremainder = ($totalselecteddownloads % $perpage);
    if ($downloadpageremainder != 0) {
        $downloadpages = ceil($downloadpagesint);
        if ($totalselecteddownloads < $perpage) {
    	    $downloadpageremainder = 0;
        }
    } else {
    	$downloadpages = $downloadpagesint;
    }        
    
	// Page Numbering
    if ($downloadpages!=1 && $downloadpages!=0) 
	{
	echo "<br><br>"
    	    .""._SELECTPAGE.": ";
        $prev=$min-$perpage;
        if ($prev>=0) {
    	    echo "&nbsp;&nbsp;<b>[ <a href=\"index.php?name=$module_name&amp;mode=latest_wishes&amp;sid=$sid&amp;min=$prev&amp;orderby=$orderby&amp;show=$show\">"
    		." &lt;&lt; "._PREVIOUS."</a> ]</b> ";
      	}
        $counter = 1;
        $currentpage = ($max / $perpage);
        while ($counter<=$downloadpages ) {
    	    $cpage = $counter;
            $mintemp = ($perpage * $counter) - $perpage;
            if ($counter == $currentpage) {
		echo "<b>$counter</b>&nbsp;";
	    } else {
		echo "<a href=\"index.php?name=$module_name&amp;mode=latest_wishes&amp;sid=$sid&amp;min=$mintemp&amp;orderby=$orderby&amp;show=$show\">$counter</a> ";
	    }
            $counter++; 	
        }    	
        $next = $min + $perpage;
        if ($x >= $perpage) 
		{
    	    echo "&nbsp;&nbsp;<b>[ <a href=\"index.php?name=$module_name&amp;mode=latest_wishes&amp;sid=$sid&amp;min=$max&amp;orderby=$orderby&amp;show=$show\">"
    		." "._NEXT." &gt;&gt;</a> ]</b> ";
        }    
	}	
	Close_Table();
	include("footer.php");
}

function search($query, $min, $orderby, $show)
{
	global $db, $prefix, $admin_file, $module_name;
	include ("header.php");
    include('modules/'.$module_name.'/config.php');	
	menu('New');
    echo "<br />";
	//echo "<table width=\"100%\" valign=\"top\"><tr><td valign='top'>";
	if (!isset($min)) $min=0;
    if (!isset($max)) $max=$min+$perpage;
    if(!empty($orderby)) 
	{
		$orderby = convertorderbyin($orderby);
    } else {
		$orderby = "title ASC";
    }
    if (!empty($show)) {
		$perpage = $show;
    } else {
		$show=$perpage;
    }
    $query1 = filter($query, "nohtml", 1);
    $query1 = addslashes($query1);	
	Open_Table();
	echo '<center><font class="title"><b>'._SEARCHRESULTS4.'</b>&nbsp;'.$query1.'</font></center><br />';
	//id 	item_id 	title 	amount 	priority 	quantity 	tags 	notes 	access 	firstname 	lastname
    if(!is_numeric($min))
	{
        $min=0;
    }	
	$wishlist = $db->sql_query("SELECT id, item_id, title, amount, priority, quantity, tags, notes, access, firstname, lastname FROM ".$prefix."_marketplace_wishlist WHERE title LIKE '%$query1%' ORDER BY $orderby LIMIT $min,$perpage"); // WHERE title LIKE '%$query1%' ORDER BY $orderby LIMIT $min,$perpage 
	$fullcountresult=$db->sql_query("SELECT * FROM ".$prefix."_marketplace_wishlist WHERE title LIKE '%$query1%'");
    $totalselecteddownloads = $db->sql_numrows($fullcountresult);
	$x=0;
	if (!empty($query1) && $totalselecteddownloads>0) 
	{
	//if($nrows>0)
	//{
	while(list($id, $item_id, $title, $amount, $priority, $quantity, $tags, $notes, $access, $firstname, $lastname) = $db->sql_fetchrow($wishlist)) 
	{
	   $products = $db->sql_query("SELECT id, title, lindens, dollars, image, thumbnail, firstname, lastname, date, hits, rating, votes, comments FROM ".$prefix."_marketplace_items WHERE id = '".$item_id."'"); // order by date DESC
	   while(list($id2, $title2, $lindens2, $dollars2, $image2, $thumbnail2, $firstname2, $lastname2, $date2, $hits2, $rating2, $votes2, $comments2) = $db->sql_fetchrow($products)) 
	   {
	   if(!is_numeric($sid))
	   {
          $sid = intval($id);
	   }	   
	   echo "<table class=\"forumline\" cellspacing=\"1\" width=\"100%\">"
       ."<tr>";
	   $rating_info = get_rating($rating, $votes);
       $thirty_days = time()-2592000;
	   $seven_days = time()-604800;
	   $now = time();
	   echo "<td class=\"cat\" colspan=\"3\">&nbsp;".$rating_info['image']."&nbsp;<a class=\"cattitle\" href=\"index.php?name=".$module_name."&amp;mode=view_details&amp;lid=".$id."\">".$title."</a>&nbsp;</td>"
	       ."</tr><tr>"
           ."<td class=\"row1\" rowspan=\"2\" align=\"center\" style=\"width: 100px;\">";
	   if(file_exists($thumbnail2) && file_exists($image2))
	   {
	      echo "<a href=\"index.php?name=".$module_name."&amp;mode=view_details&amp;lid=".$id."\"><img src=\"".$thumbnail2."\" alt=\"\" /></a>";
	   }
	   else
	   {
	      echo "<img src=\"modules/".$module_name."/images/missing.png\" alt=\"{dl_item.L_NOSCREENS}\" title=\"{dl_item.L_NOSCREENS}\" />";
       }
       echo "</td>"
       ."<td class=\"row1\" valign=\"top\" style=\"height: 10px;\">"
       ."Wished by :$firstname $lastname</td>" //<img src=\"modules/".$module_name."/images/gift.png\" alt=\"{dl_item.L_NOSCREENS}\" title=\"{dl_item.L_NOSCREENS}\" />
       ."<td class=\"row1\" rowspan=\"2\" align=\"center\" style=\"width: 75px;\"><a href=\"index.php?name=Marketplace&file=gift_option&op=item_id&amp;id=".$item_id."\"><img src=\"modules/".$module_name."/images/gift.png\" alt=\""._GIFT."\" title=\""._GIFT."\"/></a></td>"
       ."</tr><tr>"
	   
       ."<td class=\"row1\" style=\"vertical-align: top;\"><span class=\"genmed\">$notes</span></td>"
       ."</tr><tr>"
       ."<td align=\"center\" class=\"row2\">$".$amount."</td>"
	   ."<td align=\"center\" class=\"row2\">$tags</td>"
       ."<td class=\"row2\" colspan=\"2\">";
	   //foreach (array($tags) as $tag)
	   //{
	      //echo $tag;
		//}
		if($priority == 5) echo "Very High";
		if($priority == 4) echo "High";
		if($priority == 3) echo "Normal";
		if($priority == 2) echo "Low";
		if($priority == 1) echo "Very Low";
	   echo"</td></tr></table><br />";
	   	   } // end while1
	$x++;
	
	} // end while2
	// end while
	}
	else
	{
      echo "<center><font class=\"option\"><b>"._NOMATCHES."</b></font></center><br><br>";      
	  echo "<br><br><center><font class=\"content\">"
	      .""._TRY2SEARCH." \"$the_query\" "._INOTHERSENGINES."<br>"
	      ."<a target=\"_blank\" href=\"http://www.altavista.com/cgi-bin/query?pg=q&amp;sc=on&amp;hl=on&amp;act=2006&amp;par=0&amp;q=$query1&amp;kl=XX&amp;stype=stext\">Alta Vista</a> - "
	      ."<a target=\"_blank\" href=\"http://search.yahoo.com/bin/search?p=$query1\">Yahoo</a> - "
	      ."<a target=\"_blank\" href=\"http://www.google.com/search?q=$query1\">Google</a>"
	      ."</font>";
	}
    $orderby = convertorderbyout($orderby);
	// Calculates how many pages exist.  Which page one should be on, etc...
    $downloadpagesint = ($totalselecteddownloads / $perpage);
    $downloadpageremainder = ($totalselecteddownloads % $perpage);
    if ($downloadpageremainder != 0) {
        $downloadpages = ceil($downloadpagesint);
        if ($totalselecteddownloads < $perpage) {
    	    $downloadpageremainder = 0;
        }
    } else {
    	$downloadpages = $downloadpagesint;
    }        
    
	// Page Numbering
    if ($downloadpages!=1 && $downloadpages!=0) 
	{
	echo "<br><br>"
    	    .""._SELECTPAGE.": ";
        $prev=$min-$perpage;
        if ($prev>=0) {
    	    echo "&nbsp;&nbsp;<b>[ <a href=\"index.php?name=$module_name&amp;mode=latest_wishes&amp;sid=$sid&amp;min=$prev&amp;orderby=$orderby&amp;show=$show\">"
    		." &lt;&lt; "._PREVIOUS."</a> ]</b> ";
      	}
        $counter = 1;
        $currentpage = ($max / $perpage);
        while ($counter<=$downloadpages ) {
    	    $cpage = $counter;
            $mintemp = ($perpage * $counter) - $perpage;
            if ($counter == $currentpage) {
		echo "<b>$counter</b>&nbsp;";
	    } else {
		echo "<a href=\"index.php?name=$module_name&amp;mode=latest_wishes&amp;sid=$sid&amp;min=$mintemp&amp;orderby=$orderby&amp;show=$show\">$counter</a> ";
	    }
            $counter++; 	
        }    	
        $next = $min + $perpage;
        if ($x >= $perpage) 
		{
    	    echo "&nbsp;&nbsp;<b>[ <a href=\"index.php?name=$module_name&amp;mode=latest_wishes&amp;sid=$sid&amp;min=$max&amp;orderby=$orderby&amp;show=$show\">"
    		." "._NEXT." &gt;&gt;</a> ]</b> ";
        }    
	}	
	Close_Table();
	include("footer.php");
}

if (isset($lid) && isset ($firstname) && isset ($lastname) && isset ($rating)) {
    $ret = add_review($lid, $firstname, $lastname, $rating, $host_name, $comments);
}

if (!(isset($mode))) { $mode = ""; }
if (!(isset($min))) { $min = 0; }
if (!(isset($orderby))) { $orderby = ""; }
if (!(isset($show))) { $show = ""; }
if (!(isset($ratenum))) { $ratenum = ""; }
if (!(isset($ratetype))) { $ratetype = ""; }
if (strlen($mode) == 1 && ctype_alnum($mode)) show_merchants($mode, $field, $order);

switch($mode) {	

    case "search":
    search($query, $min, $orderby, $show);
    break;
	
	case "latest_wishes":
	latest_wishes($sid, $min, $orderby, $show);
    break;	
	
    default:
	latest_wishes($sid, $min, $orderby, $show);
    break;

}

?>