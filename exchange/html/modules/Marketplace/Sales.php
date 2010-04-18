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

require_once("mainfile.php");
$module_name = basename(dirname(__FILE__));
require_once('modules/'.$module_name.'/includes/functions.php');
get_lang($module_name);
$pagetitle = "- "._MARKETPLACE."";
define('INDEX_FILE', true);
	
function view_sales($sid, $min, $orderby, $show) 
{
    global $prefix, $db, $cookie, $user, $module_name;
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
	echo '<center><font class="title"><b>'._TODAYS_SALES.'</b></font></center><br />';
		$sales = $db->sql_query("SELECT * FROM " . $prefix . "_marketplace_sales_log WHERE merchant_firstname = '".$firstname."' AND merchant_lastname = '".$lastname."' ORDER BY date");
		echo '<div align="center"><table width="100%"><tr><td>Date</td><td>ID</td><td>&nbsp;</td><td>Item</td><td>Merchant</td><td>Buyer</td><td>Amount</td><td>Commission</td></tr>';
		$full_count = $db->sql_query("SELECT * FROM ".$prefix."_marketplace_sales_log WHERE merchant_firstname='$firstname' AND merchant_lastname='$lastname'");
        $totalselectedsales = $db->sql_numrows($full_count);
		$x = 0;
		while($row = $db->sql_fetchrow($sales))
		{  
		   $id = intval($row['id']);
		   $item_id = intval($row['item_id']);
		   if(!is_numeric($sid))
		   {
              $sid = intval($row['id']);
		   }		   
		   $date = filter($row['date']);
		   
		   $merchant_firstname = ($row['merchant_firstname']);
		   $merchant_lastname = ($row['merchant_lastname']);
		   
		   $buyer_firstname = ($row['buyer_firstname']);
		   $buyer_lastname = ($row['buyer_lastname']);
		   
		   $amount = filter($row['amount']);
		   $commission = filter($row['commission']);
		   
		   $hits = intval($row['hits']);
		   $title = filter($row['title']);
		   
		   ereg ("([0-9]{4})-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})", $date, $datetime);
           $datetime = strftime(""._LINKSDATESTRING."", mktime($datetime[4],$datetime[5],$datetime[6],$datetime[2],$datetime[3],$datetime[1]));
           $datetime = ucfirst($datetime);
	
		echo '<tr>
              <td>'.$datetime.'</td>
              <td>'.$id.'</td>
              <td><a href="index.php?name='.$module_name.'&amp;file=Sales&amp;mode=Redeliver&amp;id='.$item_id.'&amp;firstname='.$buyer_firstname.'&amp;lastname='.$buyer_lastname.'">Redeliver</a></td>
              <td>'.$title.'</td>
              <td>'.$merchant_firstname.'&nbsp;'.$merchant_lastname.'</td>
              <td>'.$buyer_firstname.'&nbsp;'.$buyer_lastname.'</td>
              <td>'.$amount.'</td>
              <td>'.$commission.'</td>
              </tr>';
		$x++;
		}
		echo '</table></div>';
	// end while
    $orderby = convertorderbyout($orderby);
	// Calculates how many pages exist.  Which page one should be on, etc...
    $salespagesint = ($totalselectedsales / $perpage);
    $downloadpageremainder = ($totalselectedsales % $perpage);
    if ($downloadpageremainder != 0) {
        $salespages = ceil($salespagesint);
        if ($totalselectedsales < $perpage) {
    	    $downloadpageremainder = 0;
        }
    } else {
    	$salespages = $salespagesint;
    }        
    
	// Page Numbering
    if ($salespages!=1 && $salespages!=0) 
	{
	echo "<br /><br />"
    	    .""._SELECTPAGE.": ";
        $prev = $min - $perpage;
        if ($prev >= 0) 
		{
    	    echo "&nbsp;&nbsp;<b>[ <a href=\"index.php?name=$module_name&amp;file=Sales&amp;mode=view_sales&amp;sid=$sid&amp;min=$prev&amp;orderby=$orderby&amp;show=$show\">"
    		." &lt;&lt; "._PREVIOUS."</a> ]</b> ";
      	}
        $counter = 1;
        $currentpage = ($max / $perpage);
        while ($counter<=$salespages ) 
		{
    	    $cpage = $counter;
            $mintemp = ($perpage * $counter) - $perpage;
            if ($counter == $currentpage) {
		echo "<b>$counter</b>&nbsp;";
	    } 
		else 
		{
		   echo "<a href=\"index.php?name=$module_name&amp;file=Sales&amp;mode=view_sales&amp;sid=$sid&amp;min=$mintemp&amp;orderby=$orderby&amp;show=$show\">$counter</a> ";
	    }
            $counter++; 	
        }    	
        $next = $min + $perpage;
        if ($x >= $perpage) 
		{
    	    echo "&nbsp;&nbsp;<b>[ <a href=\"index.php?name=$module_name&amp;file=Sales&amp;mode=view_sales&amp;sid=$sid&amp;min=$max&amp;orderby=$orderby&amp;show=$show\">"
    		." "._NEXT." &gt;&gt;</a> ]</b> ";
        }    
	}	    				
	    Close_Table();
	    echo "</td>";	
	    categories(0);	
}

function Redeliver($id, $firstname, $lastname)
{
   global $prefix, $db, $cookie, $user, $module_name;
   $sales_log = $db->sql_query("SELECT item_id, title FROM ".$prefix."_marketplace_sales_log WHERE item_id = '".$id."' limit 0,1");
   list($item_id, $title) = $db->sql_fetchrow($sales_log);
   if($id == $item_id)
   {
    $sales = $db->sql_query("SELECT inventory FROM ".$prefix."_marketplace_items WHERE id='".$id."' limit 0,1");
	while($row = $db->sql_fetchrow($sales))
	{
       $inventory = $row['inventory'];
	   $users = $db->sql_query("SELECT avatar_key FROM ".$prefix."_users WHERE firstname='".$firstname."' AND lastname='".$lastname."' limit 0,1");
	   while($row2 = $db->sql_fetchrow($users))
	   {
	   $avatar_key = $row2['avatar_key'];
	   $server = $db->sql_fetchrow($db->sql_query("SELECT p.*, s.*
				FROM " . $prefix . "_marketplace_magicbox_products p
				LEFT JOIN " . $prefix . "_marketplace_magicbox s ON (s.server_key = p.server_key)
				WHERE p.product_name = '".$inventory."' limit 0,1"));
		$ch = curl_init($server['server_url']);
	    $magicbox = $avatar_key.",".$inventory;
		$magicbox_key = $server['server_key'];
		$transmit = CallLSLScript($server['server_url'], $magicbox);
		echo $magicbox_key;
		if(!$transmit)
		{ 
		   mail($magicbox_key."@lsl.secondlife.com", $sitename, $magicbox);
		}
			include ("header.php");
			menu(false);
			echo "<br />";
			Merchants_Menu();
			echo "<br />";
			echo "<table width=\"100%\" valign=\"top\"><tr><td valign='top'>";
			Open_Table();
			echo '<center><font class="title"><b>'._ITEM_SENT.'</b></font></center><br />';    		
		    Close_Table();
	        echo "</td>";	
	        categories(0);		
	   }
    }
   }	
}

if (!(isset($mode))) { $mode = ""; }
	
switch($mode) 
{
	
    case "view_sales":
    view_sales($sid, $min, $orderby, $show);
    break;
	case "Redeliver":
	Redeliver($id, $firstname, $lastname);
	default:
	view_sales($sid, $min, $orderby, $show);
    break;
}
?>