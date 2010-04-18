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

//if (!(isset($op))) { $op = ""; }

function item_id($id)
{
	global $user, $cookie, $db, $prefix, $admin_file, $datetime, $module_name;
	include("header.php");
	echo "<table width=\"100%\" valign=\"top\"><tr><td valign='top'>";
	Open_Table();
	$ThemeSel = get_theme();
	$result = $db->sql_query("SELECT id, category, subcategory, inventory, slurl, title, description, permissions, prims, lindens, dollars, quantity, adult, sales, messages, enhancements, image, firstname, lastname from " . $prefix . "_marketplace_items where id='".$id."'");
	while($row = $db->sql_fetchrow($result)) 
    {
	   $item_id = intval($row['id']);
	   $title = filter($row['title'], "nohtml");
	   $lindens = intval($row['lindens']);
	   echo '<center><font class="title"><b>'._GIFT_PRODUCT.'</b></font></center><br />
	         <table width="100%" border="0" cellspacing="3">
	         <tr><td nowrap><form enctype="multipart/form-data" method="post" action="index.php?name='.$module_name.'&amp;file=gift_option">
	         <tr><td nowrap>Item : '.$title.'</td><td>
             <tr><td nowrap>Price : L$'.$lindens.'</td><td>
	         <tr><td nowrap>Send To :<input type="text" name="username" size="40" maxlength="63"></td><td>
	         <tr><td>&nbsp;</td><td>
			 <tr><td><input type="hidden" name="lindens" value="'.$lindens.'"></td><td>
	         <tr><td><input type="hidden" name="item_id" value="'.$item_id.'"></td><td>
	         <tr><td><input type="hidden" name="op" value="send_gift"></td><td>
             <tr><td><input type="submit" value="'._CONFIRM.'">
             </form></td></tr></table>';
	}
	Close_Table();
	echo "</td>";	
	categories(0);
}

function send_gift($username, $lindens, $item_id)
{
	global $prefix, $db, $user, $admin, $cookie, $sitename, $module_name;
	//require_once('modules/'.$module_name.'/includes/class.http.php');
	$user2 = base64_decode($user);
	$user2 = addslashes($user2);
	$cookie = explode(":", $user2);
	cookiedecode($user);
	if (is_user($user)) 
	{
	   $result = $db->sql_query("SELECT * FROM ".$prefix."_users WHERE firstname='".$cookie[1]."' AND lastname='".$cookie[2]."'");
	   
	   $marketplace = $db->sql_fetchrow($db->sql_query("SELECT * FROM ".$prefix."_marketplace_items WHERE id='".$item_id."'"));
	   $inventory = filter($marketplace['inventory'], "nohtml");
	   $title = filter($marketplace['title'], "nohtml");
	   $merchant_firstname = filter($marketplace['firstname'], "nohtml");
	   $merchant_lastname = filter($marketplace['lastname'], "nohtml");
	   $username = htmlspecialchars($username, ENT_QUOTES);
	   $result2 = $db->sql_query("SELECT * FROM ".$prefix."_name2key WHERE name='".$username."'");
	   $name2key = $db->sql_fetchrow($result2);
	   //echo var_dump($name2key);
	   $friend_key = filter($name2key['key'], "nohtml");
	   $server = $db->sql_fetchrow($db->sql_query("SELECT p.*, s.*
				FROM " . $prefix . "_marketplace_magicbox_products p
				LEFT JOIN " . $prefix . "_marketplace_magicbox s ON (s.server_key = p.server_key)
				WHERE p.product_name LIKE '%".$inventory."%' AND p.firstname = '".$merchant_firstname."' AND p.lastname = '".$merchant_lastname."'"));				
		
	   $current = date('Y-m-d G:i:s');
	   
	   if ($db->sql_numrows($result) == 1 && $db->sql_numrows($result2) == 1)
	   {
		    $row = $db->sql_fetchrow($result);
		    if ($lindens <= $row['currency'])
		    {
		       $total = ($lindens) - ($lindens * $percentage);
		       $comission = ($lindens * $percentage); 
			   $lindens_total = '$' .$total;
			   $comission_total = '$' .$comission; 
			   if($merchant_firstname != $cookie[1] && $merchant_lastname != $cookie[2] || is_admin($admin))
		       {  
			      $db->sql_query("UPDATE ".$prefix."_users SET currency = currency - '".$lindens."' WHERE firstname = '".$cookie[1]."' AND lastname = '".$cookie[2]."'");
			      $db->sql_query("UPDATE ".$prefix."_marketplace_items SET sold = sold + '1' WHERE id = '".$item_id."'");
			      $db->sql_query("INSERT INTO ".$prefix."_marketplace_sales_log (id, item_id, title, merchant_firstname, merchant_lastname, buyer_firstname, buyer_lastname, amount, commission, date) VALUES (NULL, '".$item_id."', '".$title."', '".$merchant_firstname."', '".$merchant_lastname."', '".$cookie[1]."', '".$cookie[2]."', '".$lindens_total."', '".$comission_total."', '".$current."')");
			   }
			$magicbox = $friend_key.",".$inventory;
			$magicbox_key = $server['server_key'];
			$transmit = CallLSLScript($server['server_url'], $magicbox);
			if(!$transmit)
			{ 
			   mail($magicbox_key."@lsl.secondlife.com", $sitename, $magicbox);
			}
			//echo $magicbox;
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
	   else
	   {
            include("header.php");
            menu(false);
			echo "<br />";
	        Merchants_Menu();
            echo "<br />";            
			Open_Table();
            echo '<center><font class="title"><b>'._NO_USER.'</b></font></center><br />';
			Close_Table();
			include("footer.php");
	   }	   
	}	
}
switch($op) 
{

   case "item_id":
   item_id($id);
   break;
   
   case "send_gift":
   send_gift($username, $lindens, $item_id);
   break;
}
?>