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

function add_item() 
{
    global $prefix, $db, $cookie, $user, $module_name;
    include("header.php");
    menu(false);
    echo "<br />";
	Merchants_Menu();
	echo "<br />";
	echo "<table width=\"100%\" valign=\"top\"><tr><td valign='top'>";
	Open_Table();
	$ThemeSel = get_theme();
    echo '<center><font class="title"><b>'._ADDAPRODUCT.'</b></font></center><br />';
    if (is_user($user))
	{
	    $user2 = base64_decode($user);
	    $user2 = addslashes($user2);
	    $cookie = explode(":", $user2);
	    cookiedecode($user);
		
    	$message = '<b>'._INSTRUCTIONS.':</b><br />
	    	<strong><big>&middot;</big></strong> '._DSUBMITONCE.'<br />
	    	<strong><big>&middot;</big></strong> '._USERANDIP.'<br />';
		$result = $db->sql_query("SELECT inventory from " . $prefix . "_marketplace_items WHERE firstname = '".$cookie[1]."' AND lastname = '".$cookie[2]."'");
		list($inventory) = $db->sql_fetchrow($result);
		   info_box("caution", $message);
	       echo '<br /><br /><table width="100%" border="0" cellspacing="3">
    		     <tr><td nowrap><form enctype="multipart/form-data" method="post" action="index.php?name='.$module_name.'&amp;file=Add"> 
    		     <tr><td nowrap><img src="themes/'.$ThemeSel.'/images/icon_help.gif" width="18" height="18" onmouseover="return overlib(\''._POPUP_CATEGORY.'\');" onmouseout="return nd();">&nbsp;<b>'._CATEGORY.':</b></td><td><select name="cat">';
    	   $sql = "SELECT cid, title, parentid FROM ".$prefix."_marketplace_categories ORDER BY parentid,title";
		   $result2 = $db->sql_query($sql);
    	   while ($row = $db->sql_fetchrow($result2)) 
		   {
		      $cid2 = intval($row['cid']);
			  $ctitle2 = filter($row['title'], "nohtml");
			  $parentid2 = intval($row['parentid']);
    	      if ($parentid2!=0) $ctitle2 = getparent($parentid2, $ctitle2);
    	      echo '<option value="'.$cid2.'">'.$ctitle2.'</option>';
    	   }
    	   echo '</select></td></tr>			
		         <tr><td nowrap><img src="themes/'.$ThemeSel.'/images/icon_help.gif" width="18" height="18" onmouseover="return overlib(\''._POPUP_PRODUCT.'\');" onmouseout="return nd();">&nbsp;<b>'._PRODUCT.':</b></td><td><select name="inventory">';
    	   $sql = "SELECT id, product_name, product_type FROM ".$prefix."_marketplace_magicbox_products WHERE firstname='".$cookie[1]."' AND lastname='".$cookie[2]."' ORDER BY product_name";
		   $result3 = $db->sql_query($sql);
    	   while ($row = $db->sql_fetchrow($result3)) 
		   {
		      $item_name = filter($row['product_name'], "nohtml");
			  $item_type = intval($row['product_type']);
    	      if($item_name != $inventory)
			  {
			     echo '<option value="'.$item_name.'" style="background-image: url(themes/'.$ThemeSel.'/images/inventory/'.$item_type.'.png);background-repeat: no-repeat; padding-left: 20px; height: 16px;">'.$item_name.'</option>';
    	      }
		   }
    	   echo '</select></td></tr>
		         <tr><td nowrap><img src="themes/'.$ThemeSel.'/images/icon_help.gif" width="18" height="18" onmouseover="return overlib(\''._POPUP_SLURL.'\');" onmouseout="return nd();">&nbsp;<b>'._SLURL.':</b></td><td><input type="text" name="slurl" size="40" maxlength="255">&nbsp;<a href="http://slurl.com/build.php">'._SLURL_CREATE.'</a>&nbsp;<a href="http://slurl.com/about.php">'._SLURL_ABOUT.'</a></td></tr>
		         <tr><td nowrap><img src="themes/'.$ThemeSel.'/images/icon_help.gif" width="18" height="18" onmouseover="return overlib(\''._POPUP_TITLE.'\');" onmouseout="return nd();">&nbsp;<b>'._DOWNLOADNAME.':</b></td><td><input type="text" name="title" size="40" maxlength="100"></td></tr>	
		         <tr><td nowrap><img src="themes/'.$ThemeSel.'/images/icon_help.gif" width="18" height="18" onmouseover="return overlib(\''._POPUP_DESCRIPTION.'\');" onmouseout="return nd();">&nbsp;<b>'._DESCRIPTION.':</b></td><td><textarea name="description" cols="60" rows="10"></textarea></td></tr>
		         <tr><td nowrap><img src="themes/'.$ThemeSel.'/images/icon_help.gif" width="18" height="18" onmouseover="return overlib(\''._POPUP_PERMISSIONS.'\');" onmouseout="return nd();">&nbsp;<b>'._PERMISSIONS.':</b></td><td><select name="permissions">
		         <option value="None">'._NONE.'</option>
		         <option value="Copy">'._COPY.'</option>
		         <option value="Copy, Modify">'._COPY_MODIFY.'</option>
		         <option value="Copy, Transfer">'._COPY_TRANSFER.'</option>
		         <option value="Copy, Modify, Transfer">'._COPY_MODIFY_TRANSFER.'</option>
		         <option value="Modify">'._MODIFY.'</option>
		         <option value="Modify, Transfer">'._MODIFY_TRANSFER.'</option>
		         <option value="Transfer">'._TRANSFER.'</option>
	             </select>
		         <tr><td nowrap><img src="themes/'.$ThemeSel.'/images/icon_help.gif" width="18" height="18" onmouseover="return overlib(\''._POPUP_PRIMS.'\');" onmouseout="return nd();">&nbsp;<b>'._PRIMS.':</b></td><td><input type="text" name="prims" size="30" maxlength="60"></td></tr>
		         <tr><td nowrap><img src="themes/'.$ThemeSel.'/images/icon_help.gif" width="18" height="18" onmouseover="return overlib(\''._POPUP_LINDEN.'\');" onmouseout="return nd();">&nbsp;<b>'._PRICE_LINDEN.':</b></td><td><input type="text" name="lindens" size="30" maxlength="60"></td></tr>
		         <tr><td nowrap><img src="themes/'.$ThemeSel.'/images/icon_help.gif" width="18" height="18" onmouseover="return overlib(\''._POPUP_USD.'\');" onmouseout="return nd();">&nbsp;<b>'._PRICE_USD.':</b></td><td><input type="text" name="dollars" size="30" maxlength="60"></td></tr>
		         <tr><td nowrap><img src="themes/'.$ThemeSel.'/images/icon_help.gif" width="18" height="18" onmouseover="return overlib(\''._POPUP_QUANTITY.'\');" onmouseout="return nd();">&nbsp;<b>'._QUANTITY.':</b></td><td><input type="text" name="quantity" size="30" maxlength="60" value="-1"></td></tr>
	             <tr><td nowrap><img src="themes/'.$ThemeSel.'/images/icon_help.gif" width="18" height="18" onmouseover="return overlib(\''._POPUP_ADULT.'\');" onmouseout="return nd();">&nbsp;<b>'._ADULT.':</b></td><td><input type="checkbox" name="adult"></td></tr>
	             <tr><td nowrap><img src="themes/'.$ThemeSel.'/images/icon_help.gif" width="18" height="18" onmouseover="return overlib(\''._POPUP_NOTIFICATIONS_SALES.'\');" onmouseout="return nd();">&nbsp;<b>'._NOTIFICATIONS_SALES.':</b></td><td><input type="checkbox" name="sales" checked></td></tr>
	             <tr><td nowrap><img src="themes/'.$ThemeSel.'/images/icon_help.gif" width="18" height="18" onmouseover="return overlib(\''._POPUP_NOTIFICATIONS_POST.'\');" onmouseout="return nd();">&nbsp;<b>'._NOTIFICATIONS_POST.':</b></td><td><input type="checkbox" name="messages" checked></td></tr>
	
                 <tr><td nowrap><img src="themes/'.$ThemeSel.'/images/icon_help.gif" width="18" height="18" onmouseover="return overlib(\''._POPUP_ENHANCEMENTS.'\');" onmouseout="return nd();">&nbsp;<b>'._ENHANCEMENTS.':</b></td><td>	
	             <input type="radio" name="enhancements" id="enhancement_5" value="6">
		         <label for="enhancement_5">'._ENHANCEMENT_5.'</label><br/>
		         <input type="radio" name="enhancements" id="enhancement_4" value="5">
		         <label for="enhancement_4">'._ENHANCEMENT_4.'</label><br/>
		         <input type="radio" name="enhancements" id="enhancement_3" value="4">
		         <label for="enhancement_3">'._ENHANCEMENT_3.'</label><br/>
		         <input type="radio" name="enhancements" id="enhancement_2" value="3">
		         <label for="enhancement_2">'._ENHANCEMENT_2.'</label><br/>
		         <input type="radio" name="enhancements" id="enhancement_1" value="2">
		         <label for="enhancement_1">'._ENHANCEMENT_1.'</label><br/>
		         <input type="radio" name="enhancements" id="enhancement_0" value="1">
		         <label for="enhancement_0">'._ENHANCEMENT_0.'</label><br/>
		         <input type="radio" name="enhancements" id="enhancement_none" value="0" checked>
		         <label for="enhancement_none">'._ENHANCEMENT_NONE.'</label>
		         <tr><td nowrap><img src="themes/'.$ThemeSel.'/images/icon_help.gif" width="18" height="18" onmouseover="return overlib(\''._POPUP_UPLOAD.'\');" onmouseout="return nd();">&nbsp;<b>'._UPLOAD.':</b></td><td><input name="image" type="file" /></td></tr>
                 <tr><td>&nbsp;</td><td>
		         <input type="hidden" name="MAX_FILE_SIZE" value="100000" />
		         <input type="hidden" name="firstname" value="'.$cookie[1].'">
		         <input type="hidden" name="lastname" value="'.$cookie[2].'">
		         <input type="hidden" name="op" value="Add">
    	         <input type="submit" value="'._ADDTHISFILE.'">
    	         </form></td></tr></table>';
	}	
	else 
	{
    	echo '<center>'._INVENTORYNOTUSER1.'<br />
	    	 '._INVENTORYNOTUSER2.'<br /><br />
    	     '._INVENTORYNOTUSER3.'<br />
    	     '._INVENTORYNOTUSER4.'<br />
    	     '._INVENTORYNOTUSER5.'<br />
    	     '._INVENTORYNOTUSER6.'<br />
    	     '._INVENTORYNOTUSER7.'<br /><br />
    	     '._INVENTORYNOTUSER8;
    }
	Close_Table();
	echo "</td>";	
	categories(0);
}

function Add($cat, $inventory, $slurl, $title, $description, $permissions, $prims, $lindens, $dollars, $quantity, $adult, $sales, $messages, $enhancements, $image, $firstname, $lastname) 
{
    global $prefix, $db, $user, $admin, $cookie, $module_name;
	include('modules/'.$module_name.'/config.php');
	if(is_user($user)) 
	{
		$firstname = substr(htmlspecialchars(str_replace("\'", "'", trim($cookie[1]))), 0, 31);
    	$firstname = rtrim($cookie[1], "\\");	
    	$firstname = str_replace("'", "\'", $cookie[1]);
		
		$lastname = substr(htmlspecialchars(str_replace("\'", "'", trim($cookie[2]))), 0, 31);
    	$lastname = rtrim($cookie[2], "\\");	
    	$lastname = str_replace("'", "\'", $cookie[2]);
		$users = $db->sql_query("SELECT user_id, currency FROM ".$prefix."_users WHERE firstname = '".$firstname."' AND lastname = '".$lastname."' ORDER BY user_id DESC LIMIT 0,1");
		while(list($user_id, $balance) = $db->sql_fetchrow($users))
	    {
		   $current = time();
		   $now = date('Y-m-d G:i:s');
		   $target_path = 'modules/'.$module_name.'/images/'.$user_id.'/'.$db->sql_autoid('id', $prefix.'_marketplace_items');
		   $user_file = $_FILES['image']['name'];
		   $file_type = $_FILES['image']['type'];
		   $tmp_name = $_FILES['image']['tmp_name'];
		   if ($file_type == "image/jpeg") $GLOBALS['user_ext'] = '.jpeg';
		   else if ($file_type == "image/pjpeg") $GLOBALS['user_ext'] = '.jpeg';
           else if ($file_type == "image/gif") $GLOBALS['user_ext'] = '.gif';
           else if ($file_type == "image/png") $GLOBALS['user_ext'] = '.png';
		   $file_name = $target_path.'/'.sha1($user_file).$GLOBALS['user_ext'];
		   $thumbnail = $target_path.'/thumbnail/'.sha1($user_file).$GLOBALS['user_ext'];
		   
		   // Check if inventory exist
		   if (empty($inventory)) 
		   {
		      include("header.php");
		      menu(false);
              echo "<br />";
	          Merchants_Menu();
	          echo "<br />";
	          echo "<table width=\"100%\" valign=\"top\"><tr><td valign='top'>";
	          Open_Table();
		      echo '<center><b>'._INVENTORYNOTITLE.'</b><br /><br />'._GOBACK;
		      Close_Table();
	          echo "</td>";	
	          categories(0);
		   }
		
		   // Check if title title exist
		   if (empty($title)) 
		   {
		      include("header.php");
		      menu(false);
              echo "<br />";
	          Merchants_Menu();
	          echo "<br />";
	          echo "<table width=\"100%\" valign=\"top\"><tr><td valign='top'>";
	          Open_Table();
		      echo '<center><b>'._PRODUCTNOTITLE.'</b><br /><br />'._GOBACK;
		      Close_Table();
	          echo "</td>";	
	          categories(0);
		   }
		   
	       // Check if description was supplied
		   if (empty($description)) 
		   {
		      include("header.php");
		      menu(false);
              echo "<br />";
	          Merchants_Menu();
	          echo "<br />";
	          echo "<table width=\"100%\" valign=\"top\"><tr><td valign='top'>";
	          Open_Table();
		      echo '<center><b>'._DESCRIPTIONNOTITLE.'</b><br /><br />'._GOBACK;
		      Close_Table();
	          echo "</td>";	
	          categories(0);
		   }
	       
		   // Check if permissions was supplied
		   if (empty($permissions)) 
		   {
		      include("header.php");
		      menu(false);
              echo "<br />";
	          Merchants_Menu();
	          echo "<br />";
	          echo "<table width=\"100%\" valign=\"top\"><tr><td valign='top'>";
	          Open_Table();
		      echo '<center><b>'._PERMISSIONSNOTITLE.'</b><br /><br />'._GOBACK;
		      Close_Table();
	          echo "</td>";	
	          categories(0);
		   }
		   
		   // Check if lindens was supplied
		   if (empty($lindens)) 
		   {
		      $lindens = '0';
		   }
		
		   // Check if dollars was supplied
		   if (empty($dollars)) 
		   {
		      $dollars = '0';
		   }
		   // Check if quantity was supplied
		   if (empty($quantity)) 
		   {
		      include("header.php");
		      menu(false);
              echo "<br />";
	          Merchants_Menu();
	          echo "<br />";
	          echo "<table width=\"100%\" valign=\"top\"><tr><td valign='top'>";
	          Open_Table();
		      echo '<center><b>'._QUANTITYNOTITLE.'</b><br /><br />'._GOBACK;
		      Close_Table();
	          echo "</td>";	
	          categories(0);
		   }
		   
		   // Check if adult was supplied
		   if (empty($adult)) 
		   {
		      $adult = 'off';
		   }
		   
		   // Check if sales was supplied
		   if (empty($sales)) 
		   {
		      $sales = 'off';
		   }
		   // Check if firstname was supplied
		   if (empty($messages)) 
		   {
		      $messages = 'off';
		   }
		   // Check if enhancements was supplied
		   if (empty($enhancements)) 
		   {
		      $enhancements = false;
		   }
		   if (!is_admin($admin) && $enhancements == 6 && $enhancements_price6 <= $balance)
		   {
		      $result = $db->sql_query("UPDATE ".$prefix."_users SET currency = currency - '".$enhancements_price6."' WHERE user_id = '".$user_id."'");
		   }
		   else if (!is_admin($admin) && $enhancements == 5 && $enhancements_price5 <= $balance)
		   {
		      $result = $db->sql_query("UPDATE ".$prefix."_users SET currency = currency - '".$enhancements_price5."' WHERE user_id = '".$user_id."'");
		   }
		   else if (!is_admin($admin) && $enhancements == 4 && $enhancements_price4 <= $balance)
		   {
		      $result = $db->sql_query("UPDATE ".$prefix."_users SET currency = currency - '".$enhancements_price4."' WHERE user_id = '".$user_id."'");
		   }
		   else if (!is_admin($admin) && $enhancements == 3 && $enhancements_price3 <= $balance)
		   {
		      $result = $db->sql_query("UPDATE ".$prefix."_users SET currency = currency - '".$enhancements_price3."' WHERE user_id = '".$user_id."'");
		   }
		   else if (!is_admin($admin) && $enhancements == 2 && $enhancements_price2 <= $balance)
		   {
		      $result = $db->sql_query("UPDATE ".$prefix."_users SET currency = currency - '".$enhancements_price2."' WHERE user_id = '".$user_id."'");
		   }
		   else if (!is_admin($admin) && $enhancements == 1 && $enhancements_price1 <= $balance)
		   {
		      $result = $db->sql_query("UPDATE ".$prefix."_users SET currency = currency - '".$enhancements_price1."' WHERE user_id = '".$user_id."'");
		   }		   
		   else if (empty($enhancements) || $enhancements == false)
		   {
		      $enhancements = false;
		   }	   
		   if (empty($enhancements)) 
		   {
		      $enhancements = false;
		   }
		   	// Check if image was supplied
		   if (empty($image)) 
		   {
	          include("header.php");
	          menu(false);
              echo "<br />";
	          Merchants_Menu();
	          echo "<br />";
	          echo "<table width=\"100%\" valign=\"top\"><tr><td valign='top'>";
	          Open_Table();
	          echo '<center><b>'._IMAGENOTITLE.'</b><br /><br />'._GOBACK;
		      Close_Table();
	          echo "</td>";	
	          categories(0);
		   }
		   $inventory = filter($inventory, "nohtml", 1);
		   $slurl = filter($slurl, "nohtml", 1);
		   $title = filter($title, "nohtml", 1);
		   $description = filter($description, "nohtml", 1);
		   $permissions = filter($permissions, "nohtml", 1);
		   $prims = intval($prims);
		   $lindens = intval($lindens);
		   $dollars = intval($dollars);
		   $quantity = intval($quantity);
		   $adult = filter($adult, "nohtml", 1);
		   $sales = filter($sales, "nohtml", 1);
		   $messages = filter($messages, "nohtml", 1);
		   $enhancements = intval($enhancements);
		   $firstname = filter($firstname, "nohtml", 1);
		   $lastname = filter($lastname, "nohtml", 1);
		   $cat = explode("-", $cat);
		   if (empty($cat[1])) 
		   {
		      $cat[1] = 0;
		   }
		   $slurl = filter($slurl, "nohtml", 1);
		   $cat[0] = intval($cat[0]);
		   $cat[1] = intval($cat[1]);
		   $db->sql_query("INSERT INTO ".$prefix."_marketplace_items (id, category, subcategory, inventory, slurl, title, description, permissions, prims, lindens, dollars, quantity, adult, sales, messages, enhancements, time, image, thumbnail, firstname, lastname, date) VALUES (NULL , '".$cat[0]."', '".$cat[1]."', '".add_slashes($inventory)."', '".add_slashes($slurl)."', '".add_slashes($title)."', '".add_slashes($description)."', '".add_slashes($permissions)."', '".$prims."', '".$lindens."', '".$dollars."', '".add_slashes($quantity)."', '".add_slashes($adult)."', '".add_slashes($sales)."', '".add_slashes($messages)."', '".$enhancements."', ".$current.", '".add_slashes($file_name)."', '".add_slashes($thumbnail)."', '".$firstname."', '".$lastname."', '".$now."')");
		   if(!file_exists($target_path) && !is_writable($target_path)) 
		   { 
		      mkdir($target_path, 0777, true);
		      mkdir($target_path.'/thumbnail', 0777, true); 
		   }
		   
		   if(move_uploaded_file($tmp_name, $file_name) && $file_name != $image_original && $thumbnail != $thumbnail_original)
	       {
	   	      echo $thumbnail;
			  if($file_type != "image/png" && $file_type != "image/gif" && $file_type != "image/pjpeg" && $file_type !="image/jpeg") 
	   	      {
		         include("header.php");
		         menu(false);
                 echo "<br />";
	             Merchants_Menu();
	             echo "<br />";
	             echo "<table width=\"100%\" valign=\"top\"><tr><td valign='top'>";
	             Open_Table(); 		   
		         echo "This file type is not allowed";
		         Close_Table();
	             echo "</td>";	
	             categories(0);	   
		         unlink($tmp_name);		
	   	      }		   
		      if ($file_type == "image/jpeg") echo resampimagejpg(80, 80, $file_name, $thumbnail);
			  else if ($file_type == "image/pjpeg") echo resampimagejpg(80, 80, $file_name, $thumbnail);
		      else if ($file_type == "image/gif") echo resampimagegif(80, 80, $file_name, $thumbnail);
		      else if ($file_type == "image/png") echo resampimagepng(80, 80, $file_name, $thumbnail);
	       }
		   //Header("Location: index.php?name=".$module_name);
	    } // end while
	} // end user
} // end function
	
switch($op) 
{
	case "add_item":
	add_item();
	break;
	
	case "Add":
    Add($cat, $inventory, $slurl, $title, $description, $permissions, $prims, $lindens, $dollars, $quantity, $adult, $sales, $messages, $enhancements, $image, $firstname, $lastname);
    break;	
	
	default:
	add_item();
    break;
}
?>