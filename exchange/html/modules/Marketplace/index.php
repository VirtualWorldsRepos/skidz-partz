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

function Popular ($ratenum, $ratetype)
{
	global $db, $prefix, $admin_file, $module_name;
	include ("header.php");
    include('modules/'.$module_name.'/config.php');
	menu('Popular');
    echo "<br />";
	Merchants_Menu();
	echo "<br />";	
	echo "<table width=\"100%\" valign=\"top\"><tr><td valign='top'>";	
	Open_Table();
   	if (!empty($ratenum) && !empty($ratetype)) 
	{
    	$ratenum = intval($ratenum); 
    	$ratetype = htmlentities($ratetype); 
    	$mostpopular = $ratenum;
    	if ($ratetype == "percent") $mostpopularpercentrigger = 1;
    }
    if ($mostpopularpercentrigger == 1) 
	{
    	$toppercent = $mostpopular;
    	$result = $db->sql_query("SELECT * FROM ".$prefix."_marketplace_items");
		$totalmostpopular = $db->sql_numrows($result);
    	$mostpopular = $mostpopular / 100;
    	$mostpopular = $totalmostpopular * $mostpopular;
    	$mostpopular = round($mostpopular);
    }	
	echo '<center><font class="title"><b>'._TODAYS_POPULAR.'</b></font></center><br />';
    if ($mostpopularpercentrigger == 1) 
	{
	   echo '<center><font class="option"><b>'._TODAYS_POPULAR.' '.$toppercent.'% ('._OFALL.' '.$totalmostpopular.' '._DOWNLOADS.')</b></font></center>';
    } 
	else 
	{
	   echo '<center><font class="option"><b>'._TODAYS_POPULAR.' '.$mostpopular.'</b></font></center>';
    }
    echo '<center>'._SHOWTOP.': [ <a href="index.php?name='.$module_name.'&amp;mode=Popular&amp;ratenum=10&amp;ratetype=num">10</a> -
	<a href="index.php?name='.$module_name.'&amp;mode=Popular&amp;ratenum=25&amp;ratetype=num">25</a> -
    <a href="index.php?name='.$module_name.'&amp;mode=Popular&amp;ratenum=50&amp;ratetype=num">50</a> |
    <a href="index.php?name='.$module_name.'&amp;mode=Popular&amp;ratenum=1&amp;ratetype=percent">1%</a> -
    <a href="index.php?name='.$module_name.'&amp;mode=Popular&amp;ratenum=5&amp;ratetype=percent">5%</a> -
    <a href="index.php?name='.$module_name.'&amp;mode=Popular&amp;ratenum=10&amp;ratetype=percent">10%</a> ]</center><br /><br />';
	$popular = $db->sql_query("SELECT id, title, description, enhancements, time, lindens, image, thumbnail, rating, votes FROM ".$prefix."_marketplace_items order by hits DESC limit 0, ".$mostpopular." ");	
	while(list($id, $title, $description, $enhancements, $time, $lindens, $image, $thumbnail, $rating, $votes) = $db->sql_fetchrow($popular)) 
	{
       echo "<table class=\"forumline\" cellspacing=\"1\" width=\"100%\">"
       ."<tr>";
	   $rating_info = get_rating($rating, $votes);
       $thirty_days = time()-2592000;
	   $seven_days = time()-604800;
	   $now = time();
	   if($enhancements == 6 && $time < $thirty_days)
	   {
	      $db->sql_query("UPDATE ".$prefix."_marketplace_items SET enhancements = '0', time = '".$now."' WHERE id = '".$id."'");
	   }
	   if($enhancements == 5 && $time < $thirty_days)
	   {
	      $db->sql_query("UPDATE ".$prefix."_marketplace_items SET enhancements = '0', time = '".$now."' WHERE id = '".$id."'");
	   }
	   if($enhancements == 4 && $time < $seven_days)
	   {
	      $db->sql_query("UPDATE ".$prefix."_marketplace_items SET enhancements = '0', time = '".$now."' WHERE id = '".$id."'");
	   }
	   if($enhancements == 3 && $time < $thirty_days)
	   {
	      $db->sql_query("UPDATE ".$prefix."_marketplace_items SET enhancements = '0', time = '".$now."' WHERE id = '".$id."'");
	   }
	   if($enhancements == 2 && $time < $thirty_days)
	   {
	      $db->sql_query("UPDATE ".$prefix."_marketplace_items SET enhancements = '0', time = '".$now."' WHERE id = '".$id."'");
	   }
	   if($enhancements == 1 && $time < $thirty_days)
	   {
	      $db->sql_query("UPDATE ".$prefix."_marketplace_items SET enhancements = '0', time = '".$now."' WHERE id = '".$id."'");
	   }	   
	   if($enhancements != 0)
	   {
	      
		  echo "<td class=\"cat\" colspan=\"3\">&nbsp;<img src=\"modules/".$module_name."/images/new_featureditem.gif\" alt=\"$title\" title=\"$title\" />&nbsp;<a class=\"featured_title\" href=\"index.php?name=".$module_name."&amp;mode=view_details&amp;lid=".$id."\">".$title."</a>&nbsp;<font class=\"featured_title\">".$rating_info['image']."&nbsp;</font></td>";
       }
	   else
	   {
	      echo "<td class=\"cat\" colspan=\"3\">&nbsp;".$rating_info['image']."&nbsp;<a class=\"cattitle\" href=\"index.php?name=".$module_name."&amp;mode=view_details&amp;lid=".$id."\">".$title."</a>&nbsp;</td>";
	   }
	   echo "</tr><tr>"
       ."<td class=\"row1\" rowspan=\"2\" align=\"center\" style=\"width: 100px;\">";
	   if(file_exists($thumbnail) && file_exists($image))
	   {
	      echo "<a href=\"index.php?name=".$module_name."&amp;mode=view_details&amp;lid=".$id."\"><img src=\"".$thumbnail."\" alt=\"\" /></a>";
	   }
	   else
	   {
	      echo "<img src=\"modules/".$module_name."/images/missing.png\" alt=\"{dl_item.L_NOSCREENS}\" title=\"{dl_item.L_NOSCREENS}\" />";
       }
       echo "</td>"
       ."<td class=\"row1\" valign=\"top\" style=\"height: 10px;\">"
       ."</td>"
       ."<td class=\"row1\" rowspan=\"2\" align=\"center\" style=\"width: 75px;\">L$".$lindens."</a></td>"
       ."</tr><tr>"
       ."<td class=\"row1\" style=\"vertical-align: top;\"><span class=\"genmed\">".truncate($description, 200, '...')."</span></td>"
       ."</tr><tr>"
       ."<td align=\"center\" class=\"row2\"></td>"
       ."<td class=\"row2\" colspan=\"2\">"
	   ."</td></tr></table><br />";
	}
	Close_Table();
	echo "</td>";	
	categories(0);
}

function Featured($sid, $min, $orderby, $show)
{
	global $db, $prefix, $admin_file, $module_name;
	include ("header.php");
    include('modules/'.$module_name.'/config.php');	
	menu('Featured');
    echo "<br />";
	Merchants_Menu();
	echo "<br />";
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
	echo "<table width=\"100%\" valign=\"top\"><tr><td valign='top'>";
    if(!is_numeric($min))
	{
        $min=0;
    }	
	Open_Table();
	echo '<center><font class="title"><b>'._TODAYS_FEATURED.'</b></font></center><br />';
	$popular = $db->sql_query("SELECT id, title, description, enhancements, time, lindens, image, thumbnail, rating, votes FROM ".$prefix."_marketplace_items WHERE enhancements != '0' ORDER BY $orderby limit $min,$perpage");
    $fullcountresult=$db->sql_query("SELECT * FROM ".$prefix."_marketplace_items");
    $totalselectedfeatured = $db->sql_numrows($fullcountresult);
	$x=0;	
	while(list($id, $title, $description, $enhancements, $time, $lindens, $image, $thumbnail, $rating, $votes) = $db->sql_fetchrow($popular)) 
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
	   if($enhancements == 6 && $time < $thirty_days)
	   {
	      $db->sql_query("UPDATE ".$prefix."_marketplace_items SET enhancements = '0', time = '".$now."' WHERE id = '".$id."'");
	   }
	   if($enhancements == 5 && $time < $thirty_days)
	   {
	      $db->sql_query("UPDATE ".$prefix."_marketplace_items SET enhancements = '0', time = '".$now."' WHERE id = '".$id."'");
	   }
	   if($enhancements == 4 && $time < $seven_days)
	   {
	      $db->sql_query("UPDATE ".$prefix."_marketplace_items SET enhancements = '0', time = '".$now."' WHERE id = '".$id."'");
	   }
	   if($enhancements == 3 && $time < $thirty_days)
	   {
	      $db->sql_query("UPDATE ".$prefix."_marketplace_items SET enhancements = '0', time = '".$now."' WHERE id = '".$id."'");
	   }
	   if($enhancements == 2 && $time < $thirty_days)
	   {
	      $db->sql_query("UPDATE ".$prefix."_marketplace_items SET enhancements = '0', time = '".$now."' WHERE id = '".$id."'");
	   }
	   if($enhancements == 1 && $time < $thirty_days)
	   {
	      $db->sql_query("UPDATE ".$prefix."_marketplace_items SET enhancements = '0', time = '".$now."' WHERE id = '".$id."'");
	   }	   
       if($enhancements != 0)
	   {
	      
		  echo "<td class=\"cat\" colspan=\"3\">&nbsp;<img src=\"modules/".$module_name."/images/new_featureditem.gif\" alt=\"$title\" title=\"$title\" />&nbsp;<a class=\"featured_title\" href=\"index.php?name=".$module_name."&amp;mode=view_details&amp;lid=".$id."\">".$title."</a>&nbsp;<font class=\"featured_title\">".$rating_info['image']."&nbsp;</font></td>";
       }
	   else
	   {
	      echo "<td class=\"cat\" colspan=\"3\">&nbsp;".$rating_info['image']."&nbsp;<a class=\"cattitle\" href=\"index.php?name=".$module_name."&amp;mode=view_details&amp;lid=".$id."\">".$title."</a>&nbsp;</td>";
	   }
	   echo "</tr><tr>"
       ."<td class=\"row1\" rowspan=\"2\" align=\"center\" style=\"width: 100px;\">"
       ."<!-- IF dl_item.IMG_SCREEN -->";
	   if(file_exists($thumbnail) && file_exists($image))
	   {
	      echo "<a href=\"index.php?name=".$module_name."&amp;mode=view_details&amp;lid=".$id."\"><img src=\"".$thumbnail."\" alt=\"\" /></a>";
	   }
	   else
	   {
	      echo "<img src=\"modules/".$module_name."/images/missing.png\" alt=\"{dl_item.L_NOSCREENS}\" title=\"{dl_item.L_NOSCREENS}\" />";
       }
       echo "</td>"
       ."<td class=\"row1\" valign=\"top\" style=\"height: 10px;\">"
       ."</td>"
       ."<td class=\"row1\" rowspan=\"2\" align=\"center\" style=\"width: 75px;\">L$".$lindens."</a></td>"
       ."</tr><tr>"
       ."<td class=\"row1\" style=\"vertical-align: top;\"><span class=\"genmed\">".truncate($description, 200, '...')."</span></td>"
       ."</tr><tr>"
       ."<td align=\"center\" class=\"row2\"></td>"
       ."<td class=\"row2\" colspan=\"2\">"
	   ."</td></tr></table><br />";
	   $x++;
	}	   
	// end while
    $orderby = convertorderbyout($orderby);
	// Calculates how many pages exist.  Which page one should be on, etc...
    //downloadpages = featuredpages
	$featuredpagesint = ($totalselectedfeatured / $perpage);
    $featuredpageremainder = ($totalselectedfeatured % $perpage);
    if ($featuredpageremainder != 0) {
        $featuredpages = ceil($featuredpagesint);
        if ($totalselectedfeatured < $perpage) {
    	    $featuredpageremainder = 0;
        }
    } else {
    	$featuredpages = $featuredpagesint;
    }        
    
	// Page Numbering
    if ($featuredpages!=1 && $featuredpages!=0) 
	{
	echo "<br><br>"
    	    .""._SELECTPAGE.": ";
        $prev=$min-$perpage;
        if ($prev>=0) {
    	    echo "&nbsp;&nbsp;<b>[ <a href=\"index.php?name=$module_name&amp;mode=Featured&amp;sid=$sid&amp;min=$prev&amp;orderby=$orderby&amp;show=$show\">"
    		." &lt;&lt; "._PREVIOUS."</a> ]</b> ";
      	}
        $counter = 1;
        $currentpage = ($max / $perpage);
        while ($counter<=$featuredpages ) {
    	    $cpage = $counter;
            $mintemp = ($perpage * $counter) - $perpage;
            if ($counter == $currentpage) {
		echo "<b>$counter</b>&nbsp;";
	    } else {
		echo "<a href=\"index.php?name=$module_name&amp;mode=Featured&amp;sid=$sid&amp;min=$mintemp&amp;orderby=$orderby&amp;show=$show\">$counter</a> ";
	    }
            $counter++; 	
        }    	
        $next = $min + $perpage;
        if ($x >= $perpage) 
		{
    	    echo "&nbsp;&nbsp;<b>[ <a href=\"index.php?name=$module_name&amp;mode=Featured&amp;sid=$sid&amp;min=$max&amp;orderby=$orderby&amp;show=$show\">"
    		." "._NEXT." &gt;&gt;</a> ]</b> ";
        }    
	}
	Close_Table();
	echo "</td>";	
	categories(0);
}

function new_item($sid, $min, $orderby, $show)
{
	global $db, $prefix, $admin_file, $module_name;
	include ("header.php");
    include('modules/'.$module_name.'/config.php');	
	menu('New');
    echo "<br />";
	Merchants_Menu();
	echo "<br />";
	if (!isset($min)) $min=0;
    if (!isset($max)) $max=$min+$perpage;
    if(!empty($orderby)) 
	{
		$orderby = convertorderbyin($orderby);
    } 
	else 
	{
		$orderby = "title ASC";
    }
    if (!empty($show)) 
	{
		$perpage = $show;
    } 
	else 
	{
		$show=$perpage;
    }	
	echo "<table width=\"100%\" valign=\"top\"><tr><td valign='top'>";
    if(!is_numeric($min))
	{
        $min=0;
    }	
	Open_Table();
	echo '<center><font class="title"><b>'._TODAYS_NEW.'</b></font></center><br />';
	$popular = $db->sql_query("SELECT id, title, description, enhancements, time, lindens, image, thumbnail, rating, votes FROM ".$prefix."_marketplace_items ORDER BY $orderby limit $min,$perpage ");
    $fullcountresult=$db->sql_query("SELECT * FROM ".$prefix."_marketplace_items WHERE firstname='$firstname' AND lastname='$lastname'");
	$totalselectednew = $db->sql_numrows($fullcountresult);
	$x=0;	
	while(list($id, $title, $description, $enhancements, $time, $lindens, $image, $thumbnail, $rating, $votes) = $db->sql_fetchrow($popular)) 
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
	   if($enhancements == 6 && $time < $thirty_days)
	   {
	      $db->sql_query("UPDATE ".$prefix."_marketplace_items SET enhancements = '0', time = '".$now."' WHERE id = '".$id."'");
	   }
	   if($enhancements == 5 && $time < $thirty_days)
	   {
	      $db->sql_query("UPDATE ".$prefix."_marketplace_items SET enhancements = '0', time = '".$now."' WHERE id = '".$id."'");
	   }
	   if($enhancements == 4 && $time < $seven_days)
	   {
	      $db->sql_query("UPDATE ".$prefix."_marketplace_items SET enhancements = '0', time = '".$now."' WHERE id = '".$id."'");
	   }
	   if($enhancements == 3 && $time < $thirty_days)
	   {
	      $db->sql_query("UPDATE ".$prefix."_marketplace_items SET enhancements = '0', time = '".$now."' WHERE id = '".$id."'");
	   }
	   if($enhancements == 2 && $time < $thirty_days)
	   {
	      $db->sql_query("UPDATE ".$prefix."_marketplace_items SET enhancements = '0', time = '".$now."' WHERE id = '".$id."'");
	   }
	   if($enhancements == 1 && $time < $thirty_days)
	   {
	      $db->sql_query("UPDATE ".$prefix."_marketplace_items SET enhancements = '0', time = '".$now."' WHERE id = '".$id."'");
	   }	   
       if($enhancements != 0)
	   {
	      
		  echo "<td class=\"cat\" colspan=\"3\">&nbsp;<img src=\"modules/".$module_name."/images/new_featureditem.gif\" alt=\"$title\" title=\"$title\" />&nbsp;<a class=\"featured_title\" href=\"index.php?name=".$module_name."&amp;mode=view_details&amp;lid=".$id."\">".$title."</a>&nbsp;<font class=\"featured_title\">".$rating_info['image']."&nbsp;</font></td>";
       }
	   else
	   {
	      echo "<td class=\"cat\" colspan=\"3\">&nbsp;".$rating_info['image']."&nbsp;<a class=\"cattitle\" href=\"index.php?name=".$module_name."&amp;mode=view_details&amp;lid=".$id."\">".$title."</a>&nbsp;</td>";
	   }
	   echo "</tr><tr>"
       ."<td class=\"row1\" rowspan=\"2\" align=\"center\" style=\"width: 100px;\">"
       ."<!-- IF dl_item.IMG_SCREEN -->";
	   if(file_exists($thumbnail) && file_exists($image))
	   {
	      echo "<a href=\"index.php?name=".$module_name."&amp;mode=view_details&amp;lid=".$id."\"><img src=\"".$thumbnail."\" alt=\"\" /></a>";
	   }
	   else
	   {
	      echo "<img src=\"modules/".$module_name."/images/missing.png\" alt=\"{dl_item.L_NOSCREENS}\" title=\"{dl_item.L_NOSCREENS}\" />";
       }
       echo "</td>"
       ."<td class=\"row1\" valign=\"top\" style=\"height: 10px;\">"
       ."</td>"
       ."<td class=\"row1\" rowspan=\"2\" align=\"center\" style=\"width: 75px;\">L$".$lindens."</a></td>"
       ."</tr><tr>"
       ."<td class=\"row1\" style=\"vertical-align: top;\"><span class=\"genmed\">".truncate($description, 200, '...')."</span></td>"
       ."</tr><tr>"
       ."<td align=\"center\" class=\"row2\"></td>"
       ."<td class=\"row2\" colspan=\"2\">"
	   ."</td></tr></table><br />";
	   $x++;
	}
	// end while
    $orderby = convertorderbyout($orderby);
	// Calculates how many pages exist.  Which page one should be on, etc...
    //newpages
	$newpagesint = ($totalselectednew / $perpage);
    $newpageremainder = ($totalselectednew % $perpage);
    if ($newpageremainder != 0) {
        $newpages = ceil($newpagesint);
        if ($totalselectednew < $perpage) {
    	    $newpageremainder = 0;
        }
    } else {
    	$newpages = $newpagesint;
    }        
    
	// Page Numbering
    if ($newpages!=1 && $newpages!=0) 
	{
	echo "<br><br>"
    	    .""._SELECTPAGE.": ";
        $prev=$min-$perpage;
        if ($prev>=0) {
    	    echo "&nbsp;&nbsp;<b>[ <a href=\"index.php?name=$module_name&amp;mode=New&amp;sid=$sid&amp;min=$prev&amp;orderby=$orderby&amp;show=$show\">"
    		." &lt;&lt; "._PREVIOUS."</a> ]</b> ";
      	}
        $counter = 1;
        $currentpage = ($max / $perpage);
        while ($counter<=$newpages ) {
    	    $cpage = $counter;
            $mintemp = ($perpage * $counter) - $perpage;
            if ($counter == $currentpage) {
		echo "<b>$counter</b>&nbsp;";
	    } else {
		echo "<a href=\"index.php?name=$module_name&amp;mode=New&amp;sid=$sid&amp;min=$mintemp&amp;orderby=$orderby&amp;show=$show\">$counter</a> ";
	    }
            $counter++; 	
        }    	
        $next = $min + $perpage;
        if ($x >= $perpage) 
		{
    	    echo "&nbsp;&nbsp;<b>[ <a href=\"index.php?name=$module_name&amp;mode=New&amp;sid=$sid&amp;min=$max&amp;orderby=$orderby&amp;show=$show\">"
    		." "._NEXT." &gt;&gt;</a> ]</b> ";
        }    
	}	
	Close_Table();
	echo "</td>";	
	categories(0);
}

function Free($sid, $min, $orderby, $show)
{
	global $db, $prefix, $admin_file, $module_name;
	include ("header.php");
    include('modules/'.$module_name.'/config.php');	
	menu('Free');
    echo "<br />";
	Merchants_Menu();
	echo "<br />";
	if (!isset($min)) $min=0;
    if (!isset($max)) $max=$min+$perpage;
    if(!empty($orderby)) 
	{
		$orderby = convertorderbyin($orderby);
    } 
	else 
	{
		$orderby = "title ASC";
    }
    if (!empty($show)) 
	{
		$perpage = $show;
    } 
	else 
	{
		$show=$perpage;
    }	
	echo "<table width=\"100%\" valign=\"top\"><tr><td valign='top'>";
    if(!is_numeric($min))
	{
        $min = 0;
    }	
	Open_Table();
	echo '<center><font class="title"><b>'._TODAYS_FREE.'</b></font></center><br />';
	$popular = $db->sql_query("SELECT id, title, description, enhancements, time, lindens, image, thumbnail, rating, votes FROM ".$prefix."_marketplace_items WHERE lindens = '0' AND dollars = '0' ORDER BY $orderby limit $min,$perpage ");
    $fullcountresult=$db->sql_query("SELECT * FROM ".$prefix."_marketplace_items WHERE firstname='$firstname' AND lastname='$lastname'");
    //totalselectedfree
	$totalselectedfree = $db->sql_numrows($fullcountresult);
	$x=0;	
	while(list($id, $title, $description, $enhancements, $time, $lindens, $image, $thumbnail, $rating, $votes) = $db->sql_fetchrow($popular)) 
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
	   if($enhancements == 6 && $time < $thirty_days)
	   {
	      $db->sql_query("UPDATE ".$prefix."_marketplace_items SET enhancements = '0', time = '".$now."' WHERE id = '".$id."'");
	   }
	   if($enhancements == 5 && $time < $thirty_days)
	   {
	      $db->sql_query("UPDATE ".$prefix."_marketplace_items SET enhancements = '0', time = '".$now."' WHERE id = '".$id."'");
	   }
	   if($enhancements == 4 && $time < $seven_days)
	   {
	      $db->sql_query("UPDATE ".$prefix."_marketplace_items SET enhancements = '0', time = '".$now."' WHERE id = '".$id."'");
	   }
	   if($enhancements == 3 && $time < $thirty_days)
	   {
	      $db->sql_query("UPDATE ".$prefix."_marketplace_items SET enhancements = '0', time = '".$now."' WHERE id = '".$id."'");
	   }
	   if($enhancements == 2 && $time < $thirty_days)
	   {
	      $db->sql_query("UPDATE ".$prefix."_marketplace_items SET enhancements = '0', time = '".$now."' WHERE id = '".$id."'");
	   }
	   if($enhancements == 1 && $time < $thirty_days)
	   {
	      $db->sql_query("UPDATE ".$prefix."_marketplace_items SET enhancements = '0', time = '".$now."' WHERE id = '".$id."'");
	   }	   
       if($enhancements != 0)
	   {
	      
		  echo "<td class=\"cat\" colspan=\"3\">&nbsp;<img src=\"modules/".$module_name."/images/new_featureditem.gif\" alt=\"$title\" title=\"$title\" />&nbsp;<a class=\"featured_title\" href=\"index.php?name=".$module_name."&amp;mode=view_details&amp;lid=".$id."\">".$title."</a>&nbsp;<font class=\"featured_title\">".$rating_info['image']."&nbsp;</font></td>";
       }
	   else
	   {
	      echo "<td class=\"cat\" colspan=\"3\">&nbsp;".$rating_info['image']."&nbsp;<a class=\"cattitle\" href=\"index.php?name=".$module_name."&amp;mode=view_details&amp;lid=".$id."\">".$title."</a>&nbsp;</td>";
	   }
	   echo "</tr><tr>"
       ."<td class=\"row1\" rowspan=\"2\" align=\"center\" style=\"width: 100px;\">"
       ."<!-- IF dl_item.IMG_SCREEN -->";
	   if(file_exists($thumbnail) && file_exists($image))
	   {
	      echo "<a href=\"index.php?name=".$module_name."&amp;mode=view_details&amp;lid=".$id."\"><img src=\"".$thumbnail."\" alt=\"\" /></a>";
	   }
	   else
	   {
	      echo "<img src=\"modules/".$module_name."/images/missing.png\" alt=\"{dl_item.L_NOSCREENS}\" title=\"{dl_item.L_NOSCREENS}\" />";
       }
       echo "</td>"
       ."<td class=\"row1\" valign=\"top\" style=\"height: 10px;\">"
       ."</td>"
       ."<td class=\"row1\" rowspan=\"2\" align=\"center\" style=\"width: 75px;\">L$".$lindens."</a></td>"
       ."</tr><tr>"
       ."<td class=\"row1\" style=\"vertical-align: top;\"><span class=\"genmed\">".truncate($description, 200, '...')."</span></td>"
       ."</tr><tr>"
       ."<td align=\"center\" class=\"row2\"></td>"
       ."<td class=\"row2\" colspan=\"2\">"
	   ."</td></tr></table><br />";
	   $x++;
	}
	// end while
    $orderby = convertorderbyout($orderby);
	// freepageremainder freepages
	// Calculates how many pages exist.  Which page one should be on, etc...
    $freepagesint = ($totalselectedfree / $perpage);
    $freepageremainder = ($totalselectedfree % $perpage);
    if ($freepageremainder != 0) {
        $freepages = ceil($freepagesint);
        if ($totalselectedfree < $perpage) {
    	    $freepageremainder = 0;
        }
    } else {
    	$freepages = $freepagesint;
    }        
    
	// Page Numbering
    if ($freepages!=1 && $freepages!=0) 
	{
	echo "<br><br>"
    	    .""._SELECTPAGE.": ";
        $prev=$min-$perpage;
        if ($prev>=0) {
    	    echo "&nbsp;&nbsp;<b>[ <a href=\"index.php?name=$module_name&amp;mode=Free&amp;sid=$sid&amp;min=$prev&amp;orderby=$orderby&amp;show=$show\">"
    		." &lt;&lt; "._PREVIOUS."</a> ]</b> ";
      	}
        $counter = 1;
        $currentpage = ($max / $perpage);
        while ($counter<=$freepages ) {
    	    $cpage = $counter;
            $mintemp = ($perpage * $counter) - $perpage;
            if ($counter == $currentpage) {
		echo "<b>$counter</b>&nbsp;";
	    } else {
		echo "<a href=\"index.php?name=$module_name&amp;mode=Free&amp;sid=$sid&amp;min=$mintemp&amp;orderby=$orderby&amp;show=$show\">$counter</a> ";
	    }
            $counter++; 	
        }    	
        $next = $min + $perpage;
        if ($x >= $perpage) 
		{
    	    echo "&nbsp;&nbsp;<b>[ <a href=\"index.php?name=$module_name&amp;mode=Free&amp;sid=$sid&amp;min=$max&amp;orderby=$orderby&amp;show=$show\">"
    		." "._NEXT." &gt;&gt;</a> ]</b> ";
        }    
	}	
	Close_Table();
	echo "</td>";	
	categories(0);
}

function alpha() 
{
	global $module_name;
	include ("header.php");	
	menu('Merchants');
    echo "<br />";
	Merchants_Menu();	
	echo "<br />";
	echo "<table width=\"100%\" valign=\"top\"><tr><td valign='top'>";
	Open_Table();
	echo '<center><font class="title"><b>'._PAGE_MERCHANTS.'</b></font></center><br />';	
	$alphabet = array ("A","B","C","D","E","F","G","H","I","J","K","L","M",
	"N","O","P","Q","R","S","T","U","V","W","X","Y","Z","1","2","3","4","5","6","7","8","9","0");
	$num = count($alphabet) - 1;
	echo "<center>[ ";
	$counter = 0;
	while (list(, $ltr) = each($alphabet)) 
	{
		echo '<a href="index.php?name='.$module_name.'&amp;mode='.$ltr.'">'.$ltr.'</a>';
		if ( $counter == round($num/2) ) 
		{
			echo ' ]<br />[ ';
		} 
		elseif ( $counter != $num ) 
		{
			echo '&nbsp;|&nbsp;';
		}
		$counter++;
	}
	echo " ]</center><br />";
	Close_Table();
	echo '</td>';	
	categories(0);
}

function show_merchants($letter, $field, $order) 
{
	global $bgcolor4, $sitename, $prefix, $multilingual, $currentlang, $db, $module_name;
	include ("header.php");	
	menu('Merchants');
    echo "<br />";
	Merchants_Menu();	
	echo "<br />";
	echo "<table width=\"100%\" valign=\"top\"><tr><td valign='top'>";
	$letter = substr($letter, 0,1);
	Open_Table();
	echo '<center><b>'.$sitename.'&nbsp;'._MERCHANTS.'</b><br />';
	echo '<i>'._MERCHANTS_LETTER.'&nbsp;'.$letter.'</i><br /><br />';
	switch ($field) 
	{

		case "merchant":
		$result = $db->sql_query("SELECT id, title, firstname, lastname, hits, rating, votes FROM ".$prefix."_marketplace_items WHERE UPPER(firstname) LIKE '$letter%' ORDER by firstname $order");
		break;

		case "score":
		$result = $db->sql_query("SELECT id, title, firstname, lastname, hits, rating, votes FROM ".$prefix."_marketplace_items WHERE UPPER(firstname) LIKE '$letter%' ORDER by votes $order");
		break;

		case "hits":
		$result = $db->sql_query("SELECT id, title, firstname, lastname, hits, rating, votes FROM ".$prefix."_marketplace_items WHERE UPPER(firstname) LIKE '$letter%' ORDER by hits $order");
		break;
		
		default:
		$result = $db->sql_query("SELECT id, title, firstname, lastname, hits, rating, votes FROM ".$prefix."_marketplace_items WHERE UPPER(firstname) LIKE '$letter%' ORDER by firstname $order");
		break;

	}
	$numresults = $db->sql_numrows($result);
	if ($numresults == 0) 
	{
		echo "<i><b>"._NOMERCHANTS." \"$letter\"</b></i><br><br>";
	} 
	elseif ($numresults > 0) 
	{
		echo "<TABLE BORDER=\"0\" width=\"100%\" CELLPADDING=\"2\" CELLSPACING=\"4\">
		<tr>
		<td width=\"50%\" bgcolor=\"$bgcolor4\">
		<P ALIGN=\"LEFT\"><a href=\"index.php?name=$module_name&amp;mode=$letter&amp;field=title&amp;order=ASC\"><img src=\"images/up.gif\" border=\"0\" width=\"15\" height=\"9\" Alt=\""._SORTASC."\"></a><B> "._PRODUCT_TITLE." </B><a href=\"index.php?name=$module_name&amp;mode=$letter&amp;field=title&amp;order=DESC\"><img src=\"images/down.gif\" border=\"0\" width=\"15\" height=\"9\" Alt=\""._SORTDESC."\"></a>
		</td>
		<td width=\"18%\" bgcolor=\"$bgcolor4\">
		<P ALIGN=\"CENTER\"><a href=\"index.php?name=$module_name&amp;mode=$letter&amp;field=merchant&amp;order=ASC\"><img src=\"images/up.gif\" border=\"0\" width=\"15\" height=\"9\" Alt=\""._SORTASC."\"></a><B> "._MERCHANTS." </B><a href=\"index.php?name=$module_name&amp;mode=$letter&amp;field=merchant&amp;order=desc\"><img src=\"images/down.gif\" border=\"0\" width=\"15\" height=\"9\" Alt=\""._SORTDESC."\"></a>
		</td>
		<td width=\"18%\" bgcolor=\"$bgcolor4\">
		<P ALIGN=\"CENTER\"><a href=\"index.php?name=$module_name&amp;mode=$letter&amp;field=score&amp;order=ASC\"><img src=\"images/up.gif\" border=\"0\" width=\"15\" height=\"9\" Alt=\""._SORTASC."\"></a><B> "._SCORE." </B><a href=\"index.php?name=$module_name&amp;mode=$letter&amp;field=score&amp;order=DESC\"><img src=\"images/down.gif\" border=\"0\" width=\"15\" height=\"9\" Alt=\""._SORTDESC."\"></a>
		</td>
		<td width=\"14%\" bgcolor=\"$bgcolor4\">
		<P ALIGN=\"CENTER\"><a href=\"index.php?name=$module_name&amp;mode=$letter&amp;field=hits&amp;order=ASC\"><img src=\"images/up.gif\" border=\"0\" width=\"15\" height=\"9\" Alt=\""._SORTASC."\"></a><B> "._HITS." </B><a href=\"index.php?name=$module_name&amp;mode=$letter&amp;field=hits&amp;order=DESC\"><img src=\"images/down.gif\" border=\"0\" width=\"15\" height=\"9\" Alt=\""._SORTDESC."\"></a>
		</td>
		</tr>";
		while($myrow = $db->sql_fetchrow($result)) 
		{
			$firstname = filter($myrow["firstname"], "nohtml");
			$lastname = filter($myrow["lastname"], "nohtml");
			$id = intval($myrow['id']);
			$title = filter($myrow['title'], "nohtml");
			$hits = intval($myrow['hits']);
			$rating_info = get_rating($myrow["rating"], $myrow["votes"]);
			echo '<tr>
		    <td width="50%" bgcolor="'.$bgcolor4.'"><a href="index.php?name='.$module_name.'&amp;mode=item&amp;id='.$id.'">'.$title.'</a></td>
		    <td width="18%" bgcolor="'.$bgcolor4.'">';
			if (!empty($firstname) && !empty($lastname))
			echo '<center>'.$firstname.'&nbsp;'.$lastname.'</center>';
			echo '</td><td width="18%" bgcolor="'.$bgcolor4.'"><center>'.$rating_info['image'];
			echo '</center></td><td width="14%" bgcolor="'.$bgcolor4.'"><center>'.$hits.'</center></td></tr>';
		}
		echo '</TABLE>';
		echo '<br />'.$numresults.'&nbsp;'._TOTAL_PRODUCTS.'<br /><br />';
	}
	Close_Table();
	echo '</td>';	
	categories(0);
}

function view_category($cid, $min, $orderby, $show) 
{
	global $db, $prefix, $admin_file, $module_name;
	include ("header.php");
    include('modules/'.$module_name.'/config.php');	
	menu('Popular');
    echo "<br />";
	Merchants_Menu();
	echo "<br />";
	if (!isset($min)) $min=0;
    if (!isset($max)) $max=$min+$perpage;
    if(!empty($orderby)) 
	{
		$orderby = convertorderbyin($orderby);
    } 
	else 
	{
		$orderby = "title ASC";
    }
    if (!empty($show)) 
	{
		$perpage = $show;
    } 
	else 
	{
		$show=$perpage;
    }	
	echo "<table width=\"100%\" valign=\"top\"><tr><td valign='top'>";
    if(!is_numeric($min))
	{
        $min=0;
    }	
	Open_Table();
    $result = $db->sql_query("SELECT cid, title FROM ".$prefix."_marketplace_categories WHERE cid='".$subcategories."'");
	list($ctitle) = $db->sql_fetchrow($result);
	$ctitle = filter($ctitle, "nohtml");
	$ctitle = getparent($cid,$ctitle);
	echo '<center><font class="title"><b>'._CATEGORY.' - '.$ctitle.'  </b></font></center><br />';
	$popular = $db->sql_query("SELECT id, title, description, enhancements, time, lindens, image, thumbnail, rating, votes FROM ".$prefix."_marketplace_items WHERE category = '".$cid."' ORDER BY $orderby limit $min,$perpage "); // order by title DESC 
    $fullcountresult=$db->sql_query("SELECT * FROM ".$prefix."_marketplace_items");
	$totalselectedcategory = $db->sql_numrows($fullcountresult);
	$x=0;	
	while(list($id, $title, $description, $enhancements, $time, $lindens, $image, $thumbnail, $rating, $votes) = $db->sql_fetchrow($popular)) 
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
	   if($enhancements == 6 && $time < $thirty_days)
	   {
	      $db->sql_query("UPDATE ".$prefix."_marketplace_items SET enhancements = '0', time = '".$now."' WHERE id = '".$id."'");
	   }
	   if($enhancements == 5 && $time < $thirty_days)
	   {
	      $db->sql_query("UPDATE ".$prefix."_marketplace_items SET enhancements = '0', time = '".$now."' WHERE id = '".$id."'");
	   }
	   if($enhancements == 4 && $time < $seven_days)
	   {
	      $db->sql_query("UPDATE ".$prefix."_marketplace_items SET enhancements = '0', time = '".$now."' WHERE id = '".$id."'");
	   }
	   if($enhancements == 3 && $time < $thirty_days)
	   {
	      $db->sql_query("UPDATE ".$prefix."_marketplace_items SET enhancements = '0', time = '".$now."' WHERE id = '".$id."'");
	   }
	   if($enhancements == 2 && $time < $thirty_days)
	   {
	      $db->sql_query("UPDATE ".$prefix."_marketplace_items SET enhancements = '0', time = '".$now."' WHERE id = '".$id."'");
	   }
	   if($enhancements == 1 && $time < $thirty_days)
	   {
	      $db->sql_query("UPDATE ".$prefix."_marketplace_items SET enhancements = '0', time = '".$now."' WHERE id = '".$id."'");
	   }	   
       if($enhancements != 0)
	   {
	      
		  echo "<td class=\"cat\" colspan=\"3\">&nbsp;<img src=\"modules/".$module_name."/images/new_featureditem.gif\" alt=\"$title\" title=\"$title\" />&nbsp;<a class=\"featured_title\" href=\"index.php?name=".$module_name."&amp;mode=view_details&amp;lid=".$id."\">".$title."</a>&nbsp;<font class=\"featured_title\">".$rating_info['image']."&nbsp;</font></td>";
       }
	   else
	   {
	      echo "<td class=\"cat\" colspan=\"3\">&nbsp;".$rating_info['image']."&nbsp;<a class=\"cattitle\" href=\"index.php?name=".$module_name."&amp;mode=view_details&amp;lid=".$id."\">".$title."</a>&nbsp;</td>";
	   }
	   echo "</tr><tr>"
       ."<td class=\"row1\" rowspan=\"2\" align=\"center\" style=\"width: 100px;\">";
	   if(file_exists($thumbnail) && file_exists($image))
	   {
	      echo "<a href=\"index.php?name=".$module_name."&amp;mode=view_details&amp;lid=".$id."\"><img src=\"".$thumbnail."\" alt=\"\" /></a>";
	   }
	   else
	   {
	      echo "<img src=\"modules/".$module_name."/images/missing.png\" alt=\"{dl_item.L_NOSCREENS}\" title=\"{dl_item.L_NOSCREENS}\" />";
       }
       echo "</td>"
       ."<td class=\"row1\" valign=\"top\" style=\"height: 10px;\">"
       ."</td>"
       ."<td class=\"row1\" rowspan=\"2\" align=\"center\" style=\"width: 75px;\">L$".$lindens."</a></td>"
       ."</tr><tr>"
       ."<td class=\"row1\" style=\"vertical-align: top;\"><span class=\"genmed\">".truncate($description, 200, '...')."</span></td>"
       ."</tr><tr>"
       ."<td align=\"center\" class=\"row2\"></td>"
       ."<td class=\"row2\" colspan=\"2\">"
	   ."</td></tr></table><br />";
	   $x++;
	}
	// end while
    $orderby = convertorderbyout($orderby);
	// categorypages  categorypages
    $categorypagesint = ($totalselectedcategory / $perpage);
    $categoryremainder = ($totalselectedcategory % $perpage);
    if ($categoryremainder  != 0) {
        $categorypages = ceil($categorypagesint);
        if ($totalselectedcategory < $perpage) {
    	    $categoryremainder = 0;
        }
    } else {
    	$categorypages = $categorypagesint;
    }        
    
	// Page Numbering
    if ($categorypages!=1 && $categorypages!=0) 
	{
	echo "<br><br>"
    	    .""._SELECTPAGE.": ";
        $prev=$min-$perpage;
        if ($prev>=0) {
    	    echo "&nbsp;&nbsp;<b>[ <a href=\"index.php?name=$module_name&amp;mode=view_category&amp;sid=$sid&amp;min=$prev&amp;orderby=$orderby&amp;show=$show\">"
    		." &lt;&lt; "._PREVIOUS."</a> ]</b> ";
      	}
        $counter = 1;
        $currentpage = ($max / $perpage);
        while ($counter<=$categorypages ) {
    	    $cpage = $counter;
            $mintemp = ($perpage * $counter) - $perpage;
            if ($counter == $currentpage) {
		echo "<b>$counter</b>&nbsp;";
	    } else {
		echo "<a href=\"index.php?name=$module_name&amp;mode=view_category&amp;sid=$sid&amp;min=$mintemp&amp;orderby=$orderby&amp;show=$show\">$counter</a> ";
	    }
            $counter++; 	
        }    	
        $next = $min + $perpage;
        if ($x >= $perpage) 
		{
    	    echo "&nbsp;&nbsp;<b>[ <a href=\"index.php?name=$module_name&amp;mode=view_category&amp;sid=$sid&amp;min=$max&amp;orderby=$orderby&amp;show=$show\">"
    		." "._NEXT." &gt;&gt;</a> ]</b> ";
        }    
	}	
	Close_Table();
	echo "</td>";	
	categories($cid);
}

function update_hits($lid) 
{
    global $prefix, $db;
    $lid = intval($lid);
    $db->sql_query("UPDATE ".$prefix."_marketplace_items SET hits=hits+1 WHERE id='$lid'");
}

// totalselectedfree
// OR description LIKE '%$query2%' ORDER BY $orderby LIMIT $min,$downloadsresults
//WHERE title LIKE '%$query1%'  OR description LIKE '%$query2%' ORDER BY $orderby LIMIT $min,$downloadsresults 
function search($query, $min, $orderby, $show)
//function Free($sid, $min, $orderby, $show)
{
	global $db, $prefix, $admin_file, $module_name;
	include ("header.php");
    include('modules/'.$module_name.'/config.php');	
	menu('Free');
    echo "<br />";
	Merchants_Menu();
	echo "<br />";
	if (!isset($min)) $min=0;
    if (!isset($max)) $max=$min+$perpage;
    if(!empty($orderby)) 
	{
		$orderby = convertorderbyin($orderby);
    } 
	else 
	{
		$orderby = "title ASC";
    }
    if (!empty($show)) 
	{
		$perpage = $show;
    } 
	else 
	{
		$show=$perpage;
    }	
	echo "<table width=\"100%\" valign=\"top\"><tr><td valign='top'>";
    $query1 = filter($query, "nohtml", 1);
    $query1 = addslashes($query1);
	$query2 = filter($query, "", 1);    
	if(!is_numeric($min))
	{
        $min = 0;
    }	
	Open_Table();
	echo '<center><font class="title"><b>'._SEARCH_RESULTS.'</b></font></center><br />';
	$search = $db->sql_query("SELECT id, title, description, enhancements, time, lindens, image, thumbnail, rating, votes FROM ".$prefix."_marketplace_items WHERE title LIKE '%$query1%'  OR description LIKE '%$query2%' ORDER BY $orderby limit $min,$perpage ");
    $fullcountresult=$db->sql_query("SELECT * FROM ".$prefix."_marketplace_items");
    $totalselectedsearch = $db->sql_numrows($fullcountresult);
	$x=0;	
	while(list($id, $title, $description, $enhancements, $time, $lindens, $image, $thumbnail, $rating, $votes) = $db->sql_fetchrow($search)) 
	{       
	   echo "<table class=\"forumline\" cellspacing=\"1\" width=\"100%\">"
       ."<tr>";	   
	   $rating_info = get_rating($rating, $votes);
       $thirty_days = time()-2592000;
	   $seven_days = time()-604800;
	   $now = time();
	   if($enhancements == 6 && $time < $thirty_days)
	   {
	      $db->sql_query("UPDATE ".$prefix."_marketplace_items SET enhancements = '0', time = '".$now."' WHERE id = '".$id."'");
	   }
	   if($enhancements == 5 && $time < $thirty_days)
	   {
	      $db->sql_query("UPDATE ".$prefix."_marketplace_items SET enhancements = '0', time = '".$now."' WHERE id = '".$id."'");
	   }
	   if($enhancements == 4 && $time < $seven_days)
	   {
	      $db->sql_query("UPDATE ".$prefix."_marketplace_items SET enhancements = '0', time = '".$now."' WHERE id = '".$id."'");
	   }
	   if($enhancements == 3 && $time < $thirty_days)
	   {
	      $db->sql_query("UPDATE ".$prefix."_marketplace_items SET enhancements = '0', time = '".$now."' WHERE id = '".$id."'");
	   }
	   if($enhancements == 2 && $time < $thirty_days)
	   {
	      $db->sql_query("UPDATE ".$prefix."_marketplace_items SET enhancements = '0', time = '".$now."' WHERE id = '".$id."'");
	   }
	   if($enhancements == 1 && $time < $thirty_days)
	   {
	      $db->sql_query("UPDATE ".$prefix."_marketplace_items SET enhancements = '0', time = '".$now."' WHERE id = '".$id."'");
	   }	   
       if($enhancements != 0)
	   {
	      
		  echo "<td class=\"cat\" colspan=\"3\">&nbsp;<img src=\"modules/".$module_name."/images/new_featureditem.gif\" alt=\"$title\" title=\"$title\" />&nbsp;<a class=\"featured_title\" href=\"index.php?name=".$module_name."&amp;mode=view_details&amp;lid=".$id."\">".$title."</a>&nbsp;<font class=\"featured_title\">".$rating_info['image']."&nbsp;</font></td>";
       }
	   else
	   {
	      echo "<td class=\"cat\" colspan=\"3\">&nbsp;".$rating_info['image']."&nbsp;<a class=\"cattitle\" href=\"index.php?name=".$module_name."&amp;mode=view_details&amp;lid=".$id."\">".$title."</a>&nbsp;</td>";
	   }
	   echo "</tr><tr>"
       ."<td class=\"row1\" rowspan=\"2\" align=\"center\" style=\"width: 100px;\">"
       ."<!-- IF dl_item.IMG_SCREEN -->";
	   if(file_exists($thumbnail) && file_exists($image))
	   {
	      echo "<a href=\"index.php?name=".$module_name."&amp;mode=view_details&amp;lid=".$id."\"><img src=\"".$thumbnail."\" alt=\"\" /></a>";
	   }
	   else
	   {
	      echo "<img src=\"modules/".$module_name."/images/missing.png\" alt=\"{dl_item.L_NOSCREENS}\" title=\"{dl_item.L_NOSCREENS}\" />";
       }
       echo "</td>"
       ."<td class=\"row1\" valign=\"top\" style=\"height: 10px;\">"
       ."</td>"
       ."<td class=\"row1\" rowspan=\"2\" align=\"center\" style=\"width: 75px;\">L$".$lindens."</a></td>"
       ."</tr><tr>"
       ."<td class=\"row1\" style=\"vertical-align: top;\"><span class=\"genmed\">".truncate($description, 200, '...')."</span></td>"
       ."</tr><tr>"
       ."<td align=\"center\" class=\"row2\"></td>"
       ."<td class=\"row2\" colspan=\"2\">"
	   ."</td></tr></table><br />";
	   $x++;
	}
	// end while
    $orderby = convertorderbyout($orderby);
	// searchpagesint searchpageremainder
	// Calculates how many pages exist.  Which page one should be on, etc...
    $searchpagesint = ($totalselectedsearch / $perpage);
    $searchpageremainder = ($totalselectedsearch % $perpage);
    if ($searchpageremainder != 0) {
        $freepages = ceil($searchpagesint);
        if ($totalselectedsearch < $perpage) {
    	    $searchpageremainder = 0;
        }
    } else {
    	$freepages = $searchpagesint;
    }        
    
	// Page Numbering
    if ($freepages!=1 && $freepages!=0) 
	{
	echo "<br><br>"
    	    .""._SELECTPAGE.": ";
        $prev=$min-$perpage;
        if ($prev>=0) {
    	    echo "&nbsp;&nbsp;<b>[ <a href=\"index.php?name=$module_name&amp;mode=Search&amp;query=$query&amp;min=$prev&amp;orderby=$orderby&amp;show=$show\">"
    		." &lt;&lt; "._PREVIOUS."</a> ]</b> ";
      	}
        $counter = 1;
        $currentpage = ($max / $perpage);
        while ($counter<=$freepages ) {
    	    $cpage = $counter;
            $mintemp = ($perpage * $counter) - $perpage;
            if ($counter == $currentpage) {
		echo "<b>$counter</b>&nbsp;";
	    } else {
		echo "<a href=\"index.php?name=$module_name&amp;mode=Search&amp;query=$query&amp;min=$mintemp&amp;orderby=$orderby&amp;show=$show\">$counter</a> ";
	    }
            $counter++; 	
        }    	
        $next = $min + $perpage;
        if ($x >= $perpage) 
		{
    	    echo "&nbsp;&nbsp;<b>[ <a href=\"index.php?name=$module_name&amp;mode=Search&amp;query=$query&amp;min=$max&amp;orderby=$orderby&amp;show=$show\">"
    		." "._NEXT." &gt;&gt;</a> ]</b> ";
        }    
	}	
	Close_Table();
	echo "</td>";	
	categories(0);
}

function view_details($lid)
{
	global $user, $cookie, $db, $prefix, $admin_file, $datetime, $module_name;
	include ("header.php");
    include('modules/'.$module_name.'/config.php');	
	menu('Popular');
    echo "<br />";
	Merchants_Menu();
	echo "<br />";
	echo "<table width=\"100%\" valign=\"top\"><tr><td valign='top'>";
	Open_Table();
    update_hits($lid);
	$view = $db->sql_query("SELECT id, category, title, description, editor, enhancements, lindens, dollars, image, thumbnail, firstname, lastname, updated, date, sold, hits, rating, votes FROM ".$prefix."_marketplace_items WHERE id = '".$lid."'");
	while(list($id, $category, $title, $description, $editor, $enhancements, $lindens, $dollars, $image, $thumbnail, $firstname, $lastname, $updated, $date, $sold, $hits, $rating, $votes) = $db->sql_fetchrow($view)) 
	{
	   $ttitle = filter($title, "nohtml");
	   $categories = $db->sql_query("SELECT cid, title FROM ".$prefix."_marketplace_categories WHERE cid='".$category."'");
	   list($ctitle) = $db->sql_fetchrow($categories);
	   $ctitle = filter($ctitle, "nohtml");
	   $ctitle = item_categories_link($category, $ctitle);
       $day = time()-86400;
       $now = time();	   
	   if($updated < $day)
	   {
		  $db->sql_query("INSERT INTO ".$prefix."_marketplace_traffic (id, title, firstname, lastname, sales, hits, date) VALUES (NULL, '".$title."', '".$cookie[1]."', '".$cookie[2]."', '".$sold."', '".$hits."', '".date('Y-m-d G:i:s')."')");
		  $db->sql_query("UPDATE ".$prefix."_marketplace_items SET updated = '".$now."' WHERE id = '".$id."'");
	   }
	   echo "<div class=\"table1\">"
           ."<center><font class=\"title\"><b>".$title."</b></font></center><br /><br />"
           ."<table border=\"0\" width=\"100%\">"
           ."<tr>"
           ."<td valign=\"top\" style=\"width:80%;\">"
           ."<table border=\"0\" cellpadding=\"3\" cellspacing=\"1\" width=\"57%\" class=\"forumline\">"
           ."<tr>"
           ."<td class=\"row1\" style=\"width:30%;\"><span class=\"genmed\">"._CATEGORY."</span></td>"
           ."<td class=\"row2\">".$ctitle."</td>"
           ."</tr><tr>"
           ."<td class=\"row1\"><span class=\"genmed\">"._MERCHANT."</span></td>"
           ."<td class=\"row2\"><div style=\"float:left;\"><img src=\"modules/$module_name/images/icon_person.gif\" alt=\"$firstname $lastname\" title=\"$firstname $lastname\" />$firstname $lastname</div>"
           ."</td>"
           ."</tr>"
           ."<tr>"
           ."<td class=\"row1\"><span class=\"genmed\">"._ITEM_PRODUCT."</span></td>"
           ."<td class=\"row2\">".$title."</td>"
           ."</tr>";
           ereg ("([0-9]{4})-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})", $date, $datetime);
           $datetime = strftime(""._LINKSDATESTRING."", mktime($datetime[4],$datetime[5],$datetime[6],$datetime[2],$datetime[3],$datetime[1]));
           $datetime = ucfirst($datetime);
           echo "<tr>"
           ."<td class=\"row1\"><span class=\"genmed\">"._ITEM_PUBLISHED."</span></td>"
           ."<td class=\"row2\"><div style=\"float:left;\">".$datetime."</div></td>"
           ."</tr>"
           ."</table><br />"
           ."<div class=\"genmed\" style=\"width:90%;\">".$description."</div>";
           if($editor != "")
		   {
              echo "<br /><br /><span class=\"genmed\"><em>"._EDITOR_NOTE.":</em></span><br />"
              ."<div class=\"genmed\" style=\"width:90%;\">".$editor."</div>";
		   }
           $lid = intval(trim($lid));
           $comments = $db->sql_query("SELECT firstname, lastname, rating, comments, hostname, timestamp FROM ".$prefix."_marketplace_votedata WHERE item_id = '".$lid."' AND comments != '' ORDER BY timestamp DESC");
           $totalcomments = $db->sql_numrows($comments);
		   $total_votes = $db->sql_numrows($db->sql_query("SELECT rating FROM ".$prefix."_marketplace_votedata WHERE item_id = '".$lid."'"));
           $ttitle = htmlentities($ttitle);
           $transfertitle = ereg_replace ("_", " ", $ttitle);
           $displaytitle = stripslashes($transfertitle);
		   $x = 0;
           while(list($rating_firstname, $rating_lastname, $rating_score, $rating_comments, $rating_hostname, $rating_timestamp) = $db->sql_fetchrow($comments))
           {
		      echo "<br /><br />"
              ."<div style=\"width:90%;\">"
              ."<table border=\"0\" width=\"100%\" class=\"forumline\">"
              ."<tr><td class=\"spaceRow\"><img src=\"images/spacer.gif\" alt=\"\" height=\"1\" /></td></tr>";
              $rating_info = get_rating($rating_score, $total_votes);
              ereg ("([0-9]{4})-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})", $rating_timestamp, $datetime);
              $datetime = strftime(""._LINKSDATESTRING."", mktime($datetime[4],$datetime[5],$datetime[6],$datetime[2],$datetime[3],$datetime[1]));
              $datetime = ucfirst($datetime);			  
              echo "<tr>"
              ."<td class=\"row1\" colspan=\"2\">"
		      ."<div style=\"float:left;\">"
		      ."<a name=\"r{dl_review.ID}\" href=\"{dl_review.U_ID}\">".$rating_info['image']."</a>"
		      ."&nbsp;"._ITEM_BY."&nbsp;<a href=\"index.php?name=Your_Account&ampop=userinfo&amp;firstname=".$rating_firstname."&amp;lastname=".$rating_lastname."\">".$rating_firstname."&nbsp;".$rating_lastname."</a>"
		      ."&nbsp;"._ITEM_ON.":&nbsp;$datetime</div>"
              ."<br />"
              ."<hr style=\"width:99%;\" />"
              ."<span class=\"genmed\">".$rating_comments."</span>"
              ."</td>"
              ."</tr>"
              ."</table>"
              ."</div>";
           }
           echo "<br /><br /><center><font class=\"title\"><b><a href=\"index.php?name=".$module_name."&amp;mode=review_item&amp;lid=".$lid."\">"._WRITE_REVIEW."</a></b></font></center><br />";
		   echo "</td><td valign=\"top\" style=\"width:20%;\">"
           ."<a href=\"".$image."\"><img src=\"".$thumbnail."\" alt=\"".$title."\" title=\"".$title."\" /></a><br /><br />"
           ."<table border=\"0\" cellpadding=\"3\" cellspacing=\"1\" width=\"100%\" class=\"forumline\">"
           ."<tr>";
		   $rating_info = get_rating($rating, $votes);
           echo "<td class=\"row1\" style=\"width:50%;\">"._ITEM_RATING."</td>"
           ."<td align=\"center\" class=\"row2\">".$rating_info['image']."</td>"
           ."</tr><tr>"
           ."<td class=\"row1\" style=\"width:50%;\">"._ITEM_PAGEVIEWS."</td>"
           ."<td align=\"center\" class=\"row2\">".$hits."</td>"
           ."</tr>"
           ."</table><br />";
		   if($user)
		   {
              echo "<form action=\"index.php?name=".$module_name."\" method=\"post\">";
           }
		   else
		   {
		      echo "<form action=\"index.php?name=Your_Account\" method=\"post\">";
		   }
		   echo "<table border=\"0\" cellpadding=\"3\" cellspacing=\"1\" width=\"100%\" class=\"forumline\">"
               ."<tr>"
               ."<td class=\"row1\" style=\"width:50%;\"><input type=\"radio\" name=\"payment_method\" value=\"0\" checked></td>"
               ."<td align=\"center\" class=\"row2\">L$".$lindens."</td>"
               ."</tr><tr>"
               ."<td class=\"row1\" style=\"width:50%;\"><input type=\"radio\" name=\"payment_method\" value=\"1\"></td>"
               ."<td align=\"center\" class=\"row2\">$".$dollars."</td>"
               ."</tr>"
               ."</table><br />"
               ."<div align=\"center\">";
		   if($user)
		   {
		   echo "<input type=\"hidden\" name=\"item_id\" value=\"".$lid."\">"
		       ."<input type=\"hidden\" name=\"lindens\" value=\"".$lindens."\">"
		       ."<input type=\"hidden\" name=\"dollars\" value=\"".$dollars."\">"
		       ."<input type=\"hidden\" name=\"mode\" value=\"item_process\">";
		   }
		   echo "<hr />";
		   echo "<a href=\"index.php?name=".$module_name."&amp;file=add_wishlist&amp;op=item_id&amp;id=".$id."\">Add to Wishlist</a>";
		   echo "<hr />";
		   //echo $lid;
		   if(!$user)
		   {
		   echo "<input type=\"image\" src=\"modules/".$module_name."/images/login_purchase_1_en_US.gif\" value=\"" . _SAVECHANGES . "\"></form>";
		   }
		   if($user && $firstname != $cookie[1] && $lastname != $cookie[2])
		   {
              echo "<input type=\"image\" src=\"modules/".$module_name."/images/buy_now_1_en_US.gif\" value=\"" . _SAVECHANGES . "\"></form>";
		   }
		   else if($user && $firstname == $cookie[1] && $lastname == $cookie[2])
		   {
		      echo "<input type=\"image\" src=\"modules/".$module_name."/images/test_delivery_1_en_US.gif\" value=\"" . _SAVECHANGES . "\"></form>";
		   }
		   // Gifting Option //&file=gift_popup&ItemID=2127268
		   echo "<br /><a href=\"index.php?name=".$module_name."&amp;file=gift_option&amp;op=item_id&amp;id=".$id."\"><img src=\"modules/".$module_name."/images/purchase_gift_1_en_US.gif\" alt=\"$title\" title=\"$title\" /></a>";
		   echo "</div></td></tr></table></div>";
	}
	Close_Table();
	echo "</td>";	
	echo "</td></tr></table>";
	include("footer.php");
}

function item_process($item_id, $payment_method, $lindens, $dollars) 
{
	global $prefix, $db, $user, $admin, $cookie, $sitename, $site_url, $adminmail, $module_name;
	include('modules/'.$module_name.'/config.php');
	$user2 = base64_decode($user);
	$user2 = addslashes($user2);
	$cookie = explode(":", $user2);
	cookiedecode($user);
	
	if (is_user($user)) 
	{
	   $result = $db->sql_query("SELECT * FROM ".$prefix."_users WHERE firstname='".$cookie[1]."' AND lastname='".$cookie[2]."'");
	   $marketplace = $db->sql_fetchrow($db->sql_query("SELECT * FROM ".$prefix."_marketplace_items WHERE id='".$item_id."'")); //limit 0, ".$limit." 
	   $inventory = filter($marketplace['inventory'], "nohtml");
	   $title = filter($marketplace['title'], "nohtml");
	   $sales = filter($marketplace['sales'], "nohtml");
	   $merchant_firstname = filter($marketplace['firstname'], "nohtml");
	   $merchant_lastname = filter($marketplace['lastname'], "nohtml");
	   
	   $result2 = $db->sql_query("SELECT * FROM ".$prefix."_users WHERE firstname='".$merchant_firstname."' AND lastname='".$merchant_lastname."'");
	   
	   $sales_id = $db->sql_numrows($db->sql_query("SELECT * FROM ".$prefix."_sales_log WHERE merchant_firstname='".$merchant_firstname."' AND merchant_lastname='".$merchant_lastname."'"));
	   
	   $server = $db->sql_fetchrow($db->sql_query("SELECT p.*, s.*
				FROM " . $prefix . "_marketplace_magicbox_products p
				LEFT JOIN " . $prefix . "_marketplace_magicbox s ON (s.server_key = p.server_key)
				WHERE p.product_name LIKE '%".$inventory."%' AND p.firstname = '".$merchant_firstname."' AND p.lastname = '".$merchant_lastname."'"));
		
		$current = date('Y-m-d G:i:s');
		
	   if($payment_method != 0)
	   {
		  if ($db->sql_numrows($result) == 1)
	      {
		     $row = $db->sql_fetchrow($result);
			 $row2 = $db->sql_fetchrow($result2);
		     if ($dollars <= $row['dollar'] || is_admin($admin))
		     {
		        $total = ($dollars) - ($dollars * $percentage);
		        $comission = ($dollars * $percentage); 
				$dollars_total = '$' .$total;
				$comission_total = '$' .$comission; 
				if($merchant_firstname != $cookie[1] && $merchant_lastname != $cookie[2] || is_admin($admin))
		        {  
				   if(!is_admin($admin) || $merchant_firstname != $cookie[1] && $merchant_lastname != $cookie[2])
				   {				   
				      $db->sql_query("UPDATE ".$prefix."_users SET dollar = dollar - '".$dollars."' WHERE firstname = '".$cookie[1]."' AND lastname = '".$cookie[2]."'");
				      $db->sql_query("UPDATE ".$prefix."_marketplace_items SET sold = sold + '1' WHERE id = '".$item_id."'");
				      $db->sql_query("INSERT INTO ".$prefix."_marketplace_sales_log (id, item_id, title, merchant_firstname, merchant_lastname, buyer_firstname, buyer_lastname, amount, commission, date) VALUES (NULL, '".$item_id."', '".$title."', '".$merchant_firstname."', '".$merchant_lastname."', '".$cookie[1]."', '".$cookie[2]."', '".$dollars_total."', '".$comission_total."', '".$current."')");
				   }
                   if($sales)
				   {
					  $message = _SALE_TO.'&nbsp;'. $merchant_firstname .'&nbsp;'.$merchant_lastname.',<br /> <br />'._CONGRATULATIONS.'<br /> <br />'._REGARDS.',<br /> <br />'.$sitename.'<br /> <br />'._SALE_DETAILS.':<br />'._SALE_ID.': '.$sales_id.'<br />Buyer: '.$cookie[1].'&nbsp;'.$cookie[2].'<br />Delivered To: '.$cookie[1].'&nbsp;'.$cookie[2].'<br />'._ITEM_NAME.': '.$title.'<br />'._PURCHASE_PRICE.': $'.$dollars.'<br />'._PROCEEDS.': '.$dollars_total.'<br /> <br /> <br /> <br />'._REGARDS.',<br />'._MESSAGE_SUPPORT.'<br /> <br />'._MESSAGE_SENTBY.'<br />'._FOR_HELP.' <a href="'.$site_url .'" target="_blank">'.$site_url .'</a>';
					  $subject = _SALES_SUBJECT . ' - ' . $title;
			
					  // Content Types
					  $headers  = 'MIME-Version: 1.0' . "\r\n";
					  $headers .= 'Content-type: text/html; charset='._CHARSET.'' . "\r\n";
            
					  // Additional headers
					  $headers .= 'To: '.$merchant_firstname.' '.$merchant_lastname.' <'.$row2['user_email'].'>' . "\r\n";
					  $headers .= 'From: '.$sitename.' <'.$adminmail.'>' . "\r\n";				      
					  mail($row2['user_email'], $subject, $message, $headers);
				   } 				   
				}
				
				$magicbox = $row['avatar_key'].",".$inventory;
				$magicbox_key = $server['server_key'];
			    $transmit = CallLSLScript($server['server_url'], $magicbox);
			    if(!$transmit)
			    { 
			       mail($magicbox_key."@lsl.secondlife.com", $sitename, $magicbox);
			    }
                include("header.php");
                menu(false);
				echo "<br />";
	            Merchants_Menu();
                echo "<br />";
                Open_Table();
                echo '<center><font class="title"><b>'._ITEM_THANKYOU.'</b></font></center><br />				
			          <br /><center><font class="content">'._ITEM_COMPLETED.'</font></center><br />
				      <br /><center><font class="content">'.$title.'&nbsp;'._ITEM_QUEUED.'</font></center><br />';
			    Close_Table();
			    include("footer.php");				
			 } 
		     else 
		     {
		        echo _ERROR1;
		     }
	      }	
	   }
	   else
	   {
	      if ($db->sql_numrows($result) == 1)
	      {
		     $row = $db->sql_fetchrow($result);
			 $row2 = $db->sql_fetchrow($result2);
		     if ($lindens <= $row['currency'] || is_admin($admin))
		     {
		        $total = ($lindens) - ($lindens * $percentage);
		        $comission = ($lindens * $percentage);
				$currency_total = 'L$' .$total;
				$comission_total = 'L$' .$comission;
				if($merchant_firstname != $cookie[1] && $merchant_lastname != $cookie[2])
				{
				   if(!is_admin($admin) || $merchant_firstname != $cookie[1] && $merchant_lastname != $cookie[2])
				   {
				      $db->sql_query("UPDATE ".$prefix."_users SET currency = currency - '".$lindens."' WHERE firstname = '".$cookie[1]."' AND lastname = '".$cookie[2]."'");
                      $db->sql_query("UPDATE ".$prefix."_marketplace_items SET sold = sold + '1' WHERE id = '".$item_id."'");
				      $db->sql_query("INSERT INTO ".$prefix."_marketplace_sales_log (id, item_id, title, merchant_firstname, merchant_lastname, buyer_firstname, buyer_lastname, amount, commission, date) VALUES (NULL, '".$item_id."', '".$title."', '".$merchant_firstname."', '".$merchant_lastname."', '".$cookie[1]."', '".$cookie[2]."', '".$currency_total."', '".$comission_total."', '".$current."')");
				   }				
				}
                if($sales)
				{
					$message = _SALE_TO.'&nbsp;'. $merchant_firstname .'&nbsp;'.$merchant_lastname.',<br /> <br />'._CONGRATULATIONS.'<br /> <br />'._REGARDS.',<br /> <br />'.$sitename.'<br /> <br />'._SALE_DETAILS.':<br />'._SALE_ID.': '.$sales_id.'<br />Buyer: '.$cookie[1].'&nbsp;'.$cookie[2].'<br />Delivered To: '.$cookie[1].'&nbsp;'.$cookie[2].'<br />'._ITEM_NAME.': '.$title.'<br />'._PURCHASE_PRICE.': $L'.$lindens.'<br />'._PROCEEDS.': '.$currency_total.'<br /> <br /> <br /> <br />'._REGARDS.',<br />'._MESSAGE_SUPPORT.'<br /> <br />'._MESSAGE_SENTBY.'<br />'._FOR_HELP.' <a href="'.$site_url .'" target="_blank">'.$site_url .'</a>';
					$subject = _SALES_SUBJECT . ' - ' . $title;
			
					// Content Types
					$headers  = 'MIME-Version: 1.0' . "\r\n";
					$headers .= 'Content-type: text/html; charset='._CHARSET.'' . "\r\n";
            
					// Additional headers
					$headers .= 'To: '.$merchant_firstname.' '.$merchant_lastname.' <'.$row2['user_email'].'>' . "\r\n";
					$headers .= 'From: '.$sitename.' <'.$adminmail.'>' . "\r\n";				      
					mail($row2['user_email'], $subject, $message, $headers);
				}				
			    $magicbox = $row['avatar_key'].",".$inventory;
				$magicbox_key = $server['server_key'];
			    $transmit = CallLSLScript($server['server_url'], $magicbox);
			    if(!$transmit)
			    { 
			       mail($magicbox_key."@lsl.secondlife.com", $sitename, $magicbox);
			    }
                include("header.php");
                menu(false);
                echo "<br />";
				echo "<br />";
	            Merchants_Menu();
                Open_Table();
                echo '<center><font class="title"><b>'._ITEM_THANKYOU.'</b></font></center><br />				
			          <br /><center><font class="content">'._ITEM_COMPLETED.'</font></center><br />
				      <br /><center><font class="content">'.$title.'&nbsp;'._ITEM_QUEUED.'</font></center><br />';
			    Close_Table();
			    include("footer.php");				
			 } 
		     else 
		     {
		        echo _ERROR2;
		     }
	      }
	   }		
	}
}

function add_review($lid, $firstname, $lastname, $rating, $host_name, $comments) 
{
    global $prefix, $db, $cookie, $user, $module_name;
    $passtest = 'yes';
    include('header.php');	
    include('modules/'.$module_name.'/config.php');
	menu(false);
    echo "<br />";
	echo "<br />";
	echo "<table width=\"100%\" valign=\"top\"><tr><td valign='top'>";
	Open_Table();	
    $lid = intval($lid);
    if(is_user($user)) 
	{
		$user2 = base64_decode($user);
		$user2 = addslashes($user2);
	   	$cookie = explode(":", $user2);
		cookiedecode($user);
		$firstname = $cookie[1];
		$lastname = $cookie[2];
    }
    $results3 = $db->sql_query("SELECT title, post, firstname, lastname  FROM ".$prefix."_marketplace_items WHERE id='".$lid."'");
    while(list($title, $post, $merchant_firstname, $merchant_lastname) = $db->sql_fetchrow($results3)) 
	$ttitle = filter($title, "nohtml");
    $title = filter($title, "nohtml");
	$merchant_firstname = filter($marketplace['firstname'], "nohtml");
	$merchant_lastname = filter($marketplace['lastname'], "nohtml");
    
	$merchant_users = $db->sql_query("SELECT * FROM ".$prefix."_users WHERE firstname='".$merchant_firstname."' AND lastname='".$merchant_lastname."'");
	$merchant = $db->sql_fetchrow($merchant_users);
	/* Make sure only 1 anonymous from an IP in a single day. */
    $ip = $_SERVER['REMOTE_HOST'];
    if (empty($ip)) 
	{
       $ip = $_SERVER['REMOTE_ADDR'];
    }
    
	/* Check if Rating is Null */
    if ($rating == "--") 
	{
	   $error = 'nullerror';
       complete_review($error);
	   $passtest = 'no';
    }
	
	$result = $db->sql_query("SELECT firstname, lastname FROM ".$prefix."_marketplace_items WHERE id='".$lid."'");
    while(list($merchant_firstname, $merchant_lastname) = $db->sql_fetchrow($result)) 
	{
    	if ($merchant_firstname == $firstname && $merchant_lastname == $lastname)
		{
    		$error = 'postervote';
    	    complete_review($error);
		    $passtest = 'no';
    	}
   	}
	
    /* Check if REG user is trying to vote twice. */
    if ($firstname != $sl_firstname && $lastname != $sl_firstname) 
	{
    	$result = $db->sql_query("SELECT firstname, lastname FROM ".$prefix."_marketplace_votedata WHERE id='".$lid."'");
    	while(list($merchant_firstname, $merchant_lastname) = $db->sql_fetchrow($result)) 
		{
    	    if ($merchant_firstname == $firstname && $merchant_lastname == $lastname) 
			{
    	        $error = 'regflood';
                complete_review($error);
		        $passtest = 'no';
			}
        }
    }
    /* Check if ANONYMOUS user is trying to vote more than once per day. */
    if ($firstname == $sl_firstname && $lastname == $sl_lastname)
	{
    	$yesterdaytimestamp = (time()-(86400 * $anonwaitdays));
    	$ytsDB = Date("Y-m-d H:i:s", $yesterdaytimestamp);
    	$result=$db->sql_query("SELECT * FROM ".$prefix."_marketplace_votedata WHERE item_id = '".$lid."' AND firstname = '".$sl_firstname."' AND lastname = '".$sl_lastname."' AND hostname = '".$ip."' AND TO_DAYS(NOW()) - TO_DAYS(timestamp) < '".$anonwaitdays."'");
        $anonvotecount = $db->sql_numrows($result); 
    	if ($anonvotecount >= 1) 
		{
    	    $error = 'anonflood';
            complete_review($error);
    	    $passtest = 'no';
    	}
    }
	
    /* Passed Tests */
    if ($passtest == "yes") 
	{
    	$comments = filter($comments);
	    if (!empty($comments)) 
	    {
	       update_points(19);
	    }
	    update_points(18);
    	
	    /* All is well.  Add to Line Item Rate to DB. */
	    $lid = intval($lid);
	    $rating = intval($rating);
	    $comments = filter($comments, "", 1);
	    if ($rating > 10 || $rating < 1) 
	    { 
           header('Location: index.php?name='.$module_name.'&amp;mode=review_item&amp;lid='.$lid); 
    	   die(); 
	    }
	
	    $db->sql_query("INSERT into ".$prefix."_marketplace_votedata values (NULL,'".$lid."', '".$firstname."', '".$lastname."', '".$rating."', '".$ip."', '".$comments."', now())");	
	    
		if($post)
		{
			$message = _POST_TO.'&nbsp;'. $merchant_firstname .'&nbsp;'.$merchant_lastname.',<br /> <br />'._REVIEWED.'<br /> <br />'._REGARDS.',<br /> <br />'.$sitename.'<br /> <br />'._POST_DETAILS.':<br />'._COMMENTS_ID.': '.$comments.'&nbsp;<a href="'.$site_url .'/index.php?name='.$module_name.'&amp;mode=view_details&amp;lid='.$lid.'" target="_blank">'.POST_VIEW_ITEM.'</a>By: '.$cookie[1].'&nbsp;'.$cookie[2].'<br /> <br /> <br /><br />'._REGARDS.',<br />'._MESSAGE_SUPPORT.'<br /> <br />'._MESSAGE_SENTBY.'<br />'._FOR_HELP.' <a href="'.$site_url .'" target="_blank">'.$site_url .'</a>';
			$subject = _POST_SUBJECT . ' - ' . $title;
			
			// Content Types
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset='._CHARSET.'' . "\r\n";
            
			// Additional headers
			$headers .= 'To: '.$merchant_firstname.' '.$merchant_lastname.' <'.$merchant['user_email'].'>' . "\r\n";
			$headers .= 'From: '.$sitename.' <'.$adminmail.'>' . "\r\n";				      
			mail($merchant['user_email'], $subject, $message, $headers);		
		}
		/* All is well.  Calculate Score & Add to Summary (for quick retrieval & sorting) to DB. */
	    /* NOTE: If weight is modified, ALL downloads need to be refreshed with new weight. */
	    /*	 Running a SQL statement with your modded calc for ALL downloads will accomplish this. */
	    $voteresult = $db->sql_query("SELECT rating, firstname, lastname, comments FROM ".$prefix."_marketplace_votedata WHERE item_id = '".$lid."'");
	    $totalvotesDB = $db->sql_numrows($voteresult);
	    include ("modules/$module_name/voteinclude.php");
        $finalrating = intval($finalrating);
        $totalvotesDB = intval($totalvotesDB);
        $truecomments = intval($truecomments);
        $lid = intval($lid);
        $db->sql_query("UPDATE ".$prefix."_marketplace_items SET rating='".$finalrating."', votes='".$totalvotesDB."', comments='".$truecomments."' WHERE id = '".$lid."'");
        $error = "none";
        complete_review($error);
    }
    complete_review_footer($lid, $firstname, $lastname);
	echo "</td>";	
	categories(0);	
}

function complete_review_footer($lid, $firstname, $lastname) 
{
    global $prefix, $db, $module_name, $sitename;
    include('modules/'.$module_name.'/config.php');
    $lid = intval($lid);
    $row = $db->sql_query("SELECT title FROM ".$prefix."_marketplace_items WHERE id='".$lid."'");
    $ttitle = filter($row[title], "nohtml");
    echo '<font class="content">'._THANKSTOTAKETIME.'&nbsp;'.$sitename.'.<br />'._LETSDECIDE.'</font><br><br /><br />';
	Close_Table();
	echo "</td>";	
	categories(0);
}

function review_item($lid, $user) 
{
    global $prefix, $cookie, $datetime, $module_name, $user_prefix;
    include("header.php");
	menu(false);
	echo "<table width=\"100%\" valign=\"top\"><tr><td valign='top'>";
    $row = $db->sql_fetchrow($db->sql_query("SELECT title FROM ".$prefix."_marketplace_items WHERE id='".$lid."'"));
    $displaytitle = filter($row['title'], "nohtml");
    Open_Table();
    if (isset($_SERVER['REMOTE_HOST'])) 
	{ 
	   $ip = $_SERVER['REMOTE_HOST']; 
	}
    if (empty($ip)) 
	{
       $ip = $_SERVER['REMOTE_ADDR'];
    }
    echo '<b>'.$displaytitle.'</b>
	<ul><font class="content">
	<li>'._RATENOTE1.'
	<li>'._RATENOTE2.'
	<li>'._RATENOTE3.'
	<li>'._DRATENOTE4.'
	<li>'._RATENOTE5;
    if(is_user($user)) 
	{
    	$user2 = base64_decode($user);
    	$user2 = addslashes($user2);
   		$cookie = explode(":", $user2);
		echo '<li>'._YOUAREREGGED.'<li>'._FEELFREE2ADD;
		cookiedecode($user);
		$firstname = $cookie[1];
		$lastname = $cookie[2];
    echo '</ul>
    	<form method="post" action="index.php?name='.$module_name.'">
        <table border="0" cellpadding="1" cellspacing="0" width="100%">
        <tr><td width="25" nowrap></td>
        <tr><td width="25" nowrap></td><td width="550">
        <input type="hidden" name="lid" value="'.$lid.'\">
        <input type="hidden" name="firstname" value="'.$firstname.'">
		<input type="hidden" name="lastname" value="'.$firstname.'">
        <input type="hidden" name="hostname" value="'.$ip.'">
        <font class="content">'._RATETHISSITE.'
        <select name="rating">
        <option>--</option>
        <option>10</option>
        <option>9</option>
	    <option>8</option>
        <option>7</option>
        <option>6</option>
        <option>5</option>
        <option>4</option>
        <option>3</option>
        <option>2</option>
        <option>1</option>
        </select></font>
		<font class="content"><input type="submit" value="'._RATETHISSITE.'"></font>
        <br /><br />';
    $karma = $db->sql_fetchrow($db->sql_query("SELECT karma FROM ".$user_prefix."_users WHERE user_id='".$cookie[0]."'"));
    if(is_user($user) && $karma['karma'] != 3 && $karma['karma'] != 4) 
	{
		echo '<b>'._SCOMMENTS.':</b><br /><textarea wrap="virtual" cols="70" rows="15" name="comments"></textarea><br /><br /><br /></font></td>';
    } 
	else 
	{
		echo '<input type="hidden" name="comments" value="">';
    }
    echo '</tr></table></form>';
	}
    Close_Table();
	echo "</td>";	
	categories(0);
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
	
    case "review_item":
    review_item($lid, $user);
    break;
	
	case "add_review":
	add_review($lid, $firstname, $lastname, $rating, $host_name, $comments);
    break;
	
    case "view_category":
    view_category($cid, $min, $orderby, $show);
    break;
	
    case "view_details":
    view_details($lid);
    break;
	
	case "item_process":
	item_process($item_id, $payment_method, $lindens, $dollars);
	break;
	
	case "Popular":
	Popular($ratenum, $ratetype);
    break;
	
	case "Featured":
	Featured($sid, $min, $orderby, $show);
	break;
	
	case "New":
	new_item($sid, $min, $orderby, $show);
	break;
	
	case "Free":
	Free($sid, $min, $orderby, $show);
	break;
	
	case "Merchants":
	alpha();
	break;
	
    default:
	Popular($ratenum, $ratetype);
    break;

}

?>