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
	menu(false);
	echo "<table width=\"100%\" valign=\"top\"><tr><td valign='top'>";
	Open_Table();
	$ThemeSel = get_theme();
	$result = $db->sql_query("SELECT id, category, subcategory, inventory, slurl, title, description, permissions, prims, lindens, quantity, adult, sales, messages, enhancements, image, thumbnail, firstname, lastname from " . $prefix . "_marketplace_items where id='".$id."'");
	while($row = $db->sql_fetchrow($result)) 
    {
	   $item_id = intval($row['id']);
	   $title = filter($row['title'], "nohtml");
	   $lindens = intval($row['lindens']);
	   $image = filter($row['image'], "nohtml");
	   $thumbnail = filter($row['thumbnail'], "nohtml");
	   echo '<center><font class="title"><b>'._ADD_WISHLIST.'</b></font></center><br />
	   <table width="100%" border="0">
       <tr>
	   <td><a href="'.$image.'"><img src="'.$thumbnail.'" alt="'.$title.'" title="'.$title.'" /></a>
	   </td>
	   <td>
	         <table width="100%" border="0" cellspacing="3">
	         <tr><td nowrap><form enctype="multipart/form-data" method="post" action="index.php?name='.$module_name.'&amp;file=add_wishlist">
	         <tr><td nowrap><b>Title</b><br />
			 <input type="text" name="title" value="'.$title.'" size="40" maxlength="255"></td><td>
             <tr><td nowrap><b>Price*</b><br />
			 <input type="text" name="amount" value="'.$lindens.'" size="10" maxlength="255" disabled="disabled" /></td><td>
			 <tr><td nowrap><b>Priority</b><br /></td><td>
			 <tr><td nowrap><select name="priority">
	         <option value="5" id="veryhigh">I neeeed it!</option>
	         <option value="4" id="high">I really really want it</option>
	         <option selected="selected" value="3" id="normal">I want it</option>
	         <option value="2" id="low">I wouldn\'t mind having it</option>
	         <option value="1" id="verylow">Thinking about it</option>
             </select></td><td>
			 <tr><td nowrap><b>Desired Quantity</b><br /></td><td>
			 <tr><td nowrap><input type="text" name="quantity" value="1" size="10" maxlength="4" /></td><td>
	         <tr><td nowrap><b>Tags</b> (Apparel, Vacation Spot, Gadgets, etc.)<br /></td><td>
			 <tr><td nowrap><input type="text" name="tags" size="40" maxlength="100"></td><td>
	         <tr><td nowrap><b>Notes</b><br /></td><td>
			 <tr><td nowrap><textarea name="notes" cols="60" rows="10">'.$notes.'</textarea></td><td>
			 <tr><td nowrap><b>Visible to</b><br /></td><td>
			 <tr><td nowrap><select name="access">
             <option value="1">Everybody (public)</option>
             <option value="0">Myself only (private)</option>
             </select></td><td>
			 <tr><td>&nbsp;</td><td>
	         <tr><td><input type="hidden" name="item_id" value="'.$item_id.'"></td><td>
	         <tr><td><input type="hidden" name="op" value="add_wishlist"></td><td>
             <tr><td><input type="submit" value="'._SUBMIT.'">
             </form></td></tr></table></td></tr></table>';
	}
	Close_Table();
	echo "</td>";	
	categories(0);
}

function add_wishlist($item_id, $title, $amount, $priority, $quantity, $tags, $notes, $access)
{
	global $prefix, $db, $user, $admin, $cookie, $sitename, $module_name;
	$user2 = base64_decode($user);
	$user2 = addslashes($user2);
	$cookie = explode(":", $user2);
	cookiedecode($user);
	if (is_user($user)) 
	{
	   $result = $db->sql_query("SELECT * FROM ".$prefix."_marketplace_items WHERE id='".$item_id."'");
	   while($row = $db->sql_fetchrow($result)) 
       {
	      $item_id = intval($item_id);
		  $title = filter($title, "nohtml", 1); 
	 	  $amount = intval($row['lindens']);
	 	  $priority = intval($priority);
	 	  $quantity = intval($quantity);
	 	  $tags = filter($tags, "nohtml", 1);
	 	  $notes = filter($notes, "nohtml", 1);
	 	  $access = intval($access);
		  $db->sql_query("INSERT INTO ".$prefix."_marketplace_wishlist (id, item_id, title, amount, priority, quantity, tags, notes, access, firstname, lastname) VALUES (NULL, '".$item_id."', '".$title."', '".$amount."', '".$priority."', '".$quantity."', '".$tags."', '".$notes."', '".$access."', '".$cookie[1]."', '".$cookie[2]."')");
       	  Header("Location: index.php?name=".$module_name);
		  die();
	   }		
	}	
}
switch($op) 
{

   case "item_id":
   item_id($id);
   break;
   
   case "add_wishlist":
   add_wishlist($item_id, $title, $lindens, $priority, $quantity, $tags, $notes, $access);
   break;
}
?>