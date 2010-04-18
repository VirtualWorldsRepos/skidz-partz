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

define('INDEX_FILE', true);
$module_name = basename(dirname(__FILE__));
require_once("mainfile.php");
require_once('modules/'.$module_name.'/includes/functions.php');
get_lang($module_name);
$pagetitle = "- "._MARKETPLACE."";
define('INDEX_FILE', true);

function view_traffic($sid, $min, $orderby, $show) 
{
    global $prefix, $db, $cookie, $user, $module_name;
	if(is_user($user)) 
	{
	   $firstname = $cookie[1];
	   $lastname = $cookie[2];
	   include ("header.php");
       include('modules/'.$module_name.'/config.php');
	   menu(false);
	   if (!isset($min)) $min = 0;
       if (!isset($max)) $max = $min + $perpage;
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
		$show = $perpage;
       }	
       echo "<br />";
	   Merchants_Menu();
	   echo "<br />";
	   echo "<table width=\"100%\" valign=\"top\"><tr><td valign='top'>";
       if(!is_numeric($min))
       {
       $min = 0;
       }	
	   Open_Table();
	   echo '<center><font class="title"><b>'._TODAYS_TRAFFIC.'</b></font></center><br />';
       $traffic = $db->sql_query("SELECT * FROM " . $prefix . "_marketplace_traffic WHERE firstname = '".$firstname."' AND lastname = '".$lastname."' ORDER BY date limit $min,$perpage");
       echo '<div align="center"><table width="100%" border="0"><tr><td>Date</td><td>Views</td><td>Sales</td><td>Product</td></tr>';
       $full_count = $db->sql_query("SELECT * FROM ".$prefix."_marketplace_traffic WHERE firstname='$firstname' AND lastname='$lastname'");
       $totalselectedtraffic = $db->sql_numrows($full_count);
       $x = 0;
       while($row = $db->sql_fetchrow($traffic))
       {  
	      if(!is_numeric($sid))
	      {
              $sid = intval($row['id']);
	      }		   
	      $date = filter($row['date']);
	      $hits = intval($row['hits']);
	      $sales = intval($row['sales']);
	      $product = filter($row['title']);
		   
	      ereg ("([0-9]{4})-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})", $date, $datetime);
	      $datetime = strftime(""._LINKSDATESTRING."", mktime($datetime[4],$datetime[5],$datetime[6],$datetime[2],$datetime[3],$datetime[1]));
	      $datetime = ucfirst($datetime);
	
	      echo '<tr><td>'.$datetime.'</td><td>'.$hits.'</td><td>'.$sales.'</td><td>'.$product.'</td></tr>';
	      $x++;
       }
	   echo '</table></div>';
	   // end while
	   $orderby = convertorderbyout($orderby);
	   // Calculates how many pages exist.  Which page one should be on, etc...
	   $trafficpagesint = ($totalselectedtraffic / $perpage);
	   $downloadpageremainder = ($totalselectedtraffic % $perpage);
	   if ($downloadpageremainder != 0) 
	   {
	      $trafficpages = ceil($trafficpagesint);
	      if ($totalselectedtraffic < $perpage) 
	      {
	         $downloadpageremainder = 0;
	      }
	   } 
	   else 
	   {
	      $trafficpages = $trafficpagesint;
	   }        
    
	   // Page Numbering
	   if ($trafficpages!=1 && $trafficpages!=0) 
	   {
	      echo "<br /><br />"._SELECTPAGE.": ";
          $prev = $min - $perpage;
          if ($prev >= 0) 
	      {
    	     echo "&nbsp;&nbsp;<b>[ <a href=\"index.php?name=$module_name&amp;file=Traffic&amp;mode=view_traffic&amp;sid=$sid&amp;min=$prev&amp;orderby=$orderby&amp;show=$show\">"
    		     ." &lt;&lt; "._PREVIOUS."</a> ]</b> ";
	      }
	      $counter = 1;
	      $currentpage = ($max / $perpage);
	   while ($counter<=$trafficpages ) 
	   {
	      $cpage = $counter;
	      $mintemp = ($perpage * $counter) - $perpage;
	   if ($counter == $currentpage) 
	   {
	      echo "<b>$counter</b>&nbsp;";
	   } 
	   else 
	   {
	      echo "<a href=\"index.php?name=$module_name&amp;file=Traffic&amp;mode=view_traffic&amp;sid=$sid&amp;min=$mintemp&amp;orderby=$orderby&amp;show=$show\">$counter</a> ";
	   }
	      $counter++; 	
	   }    	
	      $next = $min + $perpage;
	   if ($x >= $perpage) 
	   {
	      echo "&nbsp;&nbsp;<b>[ <a href=\"index.php?name=$module_name&amp;file=Traffic&amp;mode=view_traffic&amp;sid=$sid&amp;min=$max&amp;orderby=$orderby&amp;show=$show\">"._NEXT." &gt;&gt;</a> ]</b> ";
       }    
	   }    				
	    Close_Table();
	    echo "</td>";	
	    categories(0);
	}	
}

if (!(isset($mode))) { $mode = ""; }
	
switch($mode) 
{
	
    case "view_traffic":
    view_traffic($sid, $min, $orderby, $show);
    break;
	
	default:
	view_traffic($sid, $min, $orderby, $show);
    break;
}
?>