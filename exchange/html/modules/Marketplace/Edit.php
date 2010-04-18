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

function views_items($sid, $min, $orderby, $show) 
{
    global $prefix, $db, $cookie, $user, $module_name;
    include("header.php");
	include("modules/".$module_name."/config.php");
	menu(false);
	echo '<br />';
	Merchants_Menu();
	echo '<br />';
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
    echo "<br />";
    Open_Table();
	$ThemeSel = get_theme();
    echo '<center><font class="title"><b>'._EDITPRODUCTS.'</b></font></center><br />';
    if (is_user($user))
	{
    	$firstname = substr(htmlspecialchars(str_replace("\'", "'", trim($cookie[1]))), 0, 31);
    	$firstname = rtrim($cookie[1], "\\");	
    	$firstname = str_replace("'", "\'", $cookie[1]);
    	
		$lastname = substr(htmlspecialchars(str_replace("\'", "'", trim($cookie[2]))), 0, 31);
    	$lastname = rtrim($cookie[2], "\\");	
    	$lastname = str_replace("'", "\'", $cookie[2]);	
	    echo "<table class=\"forumline\" cellspacing=\"1\" width=\"100%\">";
        if(!is_numeric($min))
		{
           $min=0;
        }	    
		$result2 = $db->sql_query("SELECT id, slurl, title, description, permissions, enhancements, lindens, dollars, prims, image, thumbnail, rating, votes, comments FROM ".$prefix."_marketplace_items WHERE firstname='".$firstname."' AND lastname='".$lastname."' order by title limit $min,$perpage");
        $fullcountresult=$db->sql_query("SELECT * FROM ".$prefix."_marketplace_items WHERE firstname='$firstname' AND lastname='$lastname'");
        $totalselecteddownloads = $db->sql_numrows($fullcountresult);
		$x=0;
		while(list($id, $slurl, $title, $description, $permissions, $enhancements, $lindens, $dollars, $prims, $image, $thumbnail, $rating, $votes, $comments) = $db->sql_fetchrow($result2)) 
		{
		   if(!is_numeric($sid))
		   {
              $sid = intval($id);
		   }
		   $rating_info = get_rating($rating, $votes);
		echo "<tr><td class=\"cat\" colspan=\"3\"><font class=\"option\"><b>".$title."</b></font></a></td>"
            ."</tr><tr>"
            ."<td class=\"row1\" rowspan=\"2\" align=\"center\" style=\"width: 100px;\">"
            ."<a href=\"index.php?name=".$module_name."&amp;mode=view_details&amp;lid=".$id."\"><img src=\"".$thumbnail."\" alt=\"".$title."\" />$title</a>"
            ."</td>"
            ."<td class=\"row1\" valign=\"mode\" style=\"height: 20px;\">"
		    ."<table align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">"
            ."<tr>"
            ."<td class=\"gensmall\" width=\"20%\"></td>"
            ."<td class=\"gensmall\" width=\"20%\"></td>"
            ."<td class=\"gensmall\" width=\"60%\"></td>"
            ."</tr>"
            ."</table>"
            ."</td>"
            ."<td class=\"row1\" rowspan=\"2\" align=\"center\" style=\"width: 75px;\"><a href=\"index.php?name=".$module_name."&amp;file=Edit&amp;mode=Edit&amp;item_id=".$id."\">"._EDIT_DETAILS."</a></td>"
            ."</tr><tr>"
            ."<td class=\"row1\" style=\"vertical-align: top;\"><span class=\"genmed\">".truncate($description, 200, '...')."</span></td>"
            ."</tr><tr>"
            ."<td align=\"center\" class=\"row2\">Price:&nbsp;L$".$lindens."</td>"
            ."<td class=\"row2\" colspan=\"2\">"
            ."<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">"
            ."<tr>"
            ."<td class=\"gensmall\" width=\"15%\"><em>Price:&nbsp;$".$dollars."</em></td>";
           if($enhancements != 1)
		   {
		      echo "<td class=\"gensmall\" width=\"15%\"><span style=\"color: #009933;\"><em>"._FEATURED."</em></span></td>";
           }
		   if($active == TRUE)
		   {
		   echo "<td class=\"gensmall\" width=\"15%\"><span style=\"color: #2f3c75;\"><em>"._ACTIVATED."</em></span></td>";
           }
		   else
		   {
		   echo "<td class=\"gensmall\" width=\"15%\"><span style=\"color: #2f3c75;\"><em>"._DEACTIVATED."</em></span></td>";
		   }
		   echo "<td width=\"20%\">&nbsp;&nbsp;Prims: $prims</td>"
		   ."<td class=\"gensmall\" width=\"20%\">"
		   ."".$rating_info['image']."</a>"
		   ."</td>"
           ."<td width=\"30%\">"
           .""._REVIEWS.":&nbsp;".$comments.""
           ."</td></tr>"
           ."</table>"
           ."</td></tr>";
		   $x++;
		
		} 
		echo "</table><br />";
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
    	    echo "&nbsp;&nbsp;<b>[ <a href=\"index.php?name=$module_name&amp;file=Edit&amp;mode=items_page&amp;sid=$sid&amp;min=$prev&amp;orderby=$orderby&amp;show=$show\">"
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
		echo "<a href=\"index.php?name=$module_name&amp;file=Edit&amp;mode=items_page&amp;sid=$sid&amp;min=$mintemp&amp;orderby=$orderby&amp;show=$show\">$counter</a> ";
	    }
            $counter++; 	
        }    	
        $next = $min + $perpage;
        if ($x >= $perpage) 
		{
    	    echo "&nbsp;&nbsp;<b>[ <a href=\"index.php?name=$module_name&amp;file=Edit&amp;mode=items_page&amp;sid=$sid&amp;min=$max&amp;orderby=$orderby&amp;show=$show\">"
    		." "._NEXT." &gt;&gt;</a> ]</b> ";
        }    
	}
    }
    Close_Table();
    include("footer.php");	
}

function Edit($item_id)
{
    global $prefix, $db, $cookie, $user, $module_name;
    include("header.php");
    menu(false);
	echo '<br />';
	Merchants_Menu();
	echo '<br />';
    Open_Table();
	$item_id = intval($item_id);
	$ThemeSel = get_theme();
    echo '<center><font class="title"><b>'._EDITAPRODUCT.'</b></font></center><br />';
    $result = $db->sql_query("SELECT id, category, subcategory, inventory, slurl, title, description, permissions, prims, lindens, dollars, quantity, adult, sales, messages, enhancements, image, firstname, lastname from " . $prefix . "_marketplace_items where id='$item_id'");
	while($row = $db->sql_fetchrow($result)) 
    {	
    	$id = intval($row['id']);
		$category = intval($row['category']);
		$subcategory = intval($row['subcategory']);
		$inventory = filter($row['inventory'], "nohtml");
		$slurl = filter($row['slurl'], "nohtml");
		$title = filter($row['title'], "nohtml");
		$description = filter($row['description'], "nohtml");
		$permissions = filter($row['permissions'], "nohtml");
		$prims = intval($row['prims']);
		$lindens = intval($row['lindens']);
		$dollars = intval($row['dollars']);
		$quantity = intval($row['quantity']);
		$adult = filter($row['adult'], "nohtml");
		$sales = filter($row['sales'], "nohtml");
		$messages = filter($row['messages'], "nohtml");
		$enhancements = intval($row['enhancements']);
		$image = filter($row['image'], "nohtml");
		$firstname = filter($row['firstname'], "nohtml");
		$lastname = filter($row['lastname'], "nohtml");
		   
		$message = '<b>'._INSTRUCTIONS.':</b><br /><strong><big>&middot;</big></strong> '._DSUBMITONCE.'<br /><strong><big>&middot;</big></strong> '._USERANDIP.'<br />';
		info_box("caution", $message);
		echo '<br /><br /><table width="100%" border="0" cellspacing="3">
    	<tr><td nowrap><form enctype="multipart/form-data" method="post" action="index.php?name='.$module_name.'&amp;file=Edit&amp;mode=Save">';
		$result2 = $db->sql_query("SELECT cid, title, parentid from " . $prefix . "_marketplace_categories order by title");
		echo '<input type="hidden" name="item_id" value="'.$item_id.'">
		<tr><td nowrap><img src="themes/'.$ThemeSel.'/images/icon_help.gif" width="18" height="18" onmouseover="return overlib(\''._POPUP_CATEGORY.'\');" onmouseout="return nd();">&nbsp;<b>'._CATEGORY.':</b></td><td><select name="cat">';
		while($row2 = $db->sql_fetchrow($result2)) 
		{
			$cid2 = intval($row2['cid']);
			$ctitle2 = filter($row2['title'], "nohtml");
			$parentid2 = intval($row2['parentid']);
			if ($cid2 == $category) 
			{
				$sel = "selected";
			} 
			else 
			{
				$sel = "";
			}
			if ($parentid2!=0) $ctitle2 = getparent($parentid2, $ctitle2);
			echo '<option value="'.$cid2.'" '.$sel.'>'.$ctitle2.'</option>';
		}
    	echo '</select></td></tr>			
		<tr><td nowrap><img src="themes/'.$ThemeSel.'/images/icon_help.gif" width="18" height="18" onmouseover="return overlib(\''._POPUP_PRODUCT.'\');" onmouseout="return nd();">&nbsp;<b>'._PRODUCT.':</b></td><td><select name="inventory">';
    	$sql = "SELECT id, product_name, product_type FROM ".$prefix."_marketplace_magicbox_products WHERE firstname='".$firstname."' AND lastname='".$lastname."' ORDER BY product_name";
		$result = $db->sql_query($sql);
    	while ($row3 = $db->sql_fetchrow($result)) 
		{
			$item_name = filter($row3['product_name'], "nohtml");
			$item_type = intval($row3['product_type']);
			if ($item_name == $inventory)
			{
				$sel = "selected";
			} 
			else 
			{
				$sel = "";
			}
			   echo '<option value="'.$item_name.'" style="background-image: url(themes/'.$ThemeSel.'/images/inventory/'.$item_type.'.png);background-repeat: no-repeat; padding-left: 20px; height: 16px;" '.$sel.'>'.$item_name.'</option>';
		}
    	echo '</select></td></tr>
		<tr><td nowrap><img src="themes/'.$ThemeSel.'/images/icon_help.gif" width="18" height="18" onmouseover="return overlib(\''._POPUP_SLURL.'\');" onmouseout="return nd();">&nbsp;<b>'._SLURL.':</b></td><td><input type="text" name="slurl" value="'.$slurl.'" size="40" maxlength="255">&nbsp;<a href="http://slurl.com/build.php">'._SLURL_CREATE.'</a>&nbsp;<a href="http://slurl.com/about.php">'._SLURL_ABOUT.'</a></td></tr>
		<tr><td nowrap><img src="themes/'.$ThemeSel.'/images/icon_help.gif" width="18" height="18" onmouseover="return overlib(\''._POPUP_TITLE.'\');" onmouseout="return nd();">&nbsp;<b>'._DOWNLOADNAME.':</b></td><td><input type="text" name="title" value="'.$title.'"  size="40" maxlength="100"></td></tr>	
		<tr><td nowrap><img src="themes/'.$ThemeSel.'/images/icon_help.gif" width="18" height="18" onmouseover="return overlib(\''._POPUP_DESCRIPTION.'\');" onmouseout="return nd();">&nbsp;<b>'._DESCRIPTION.':</b></td><td><textarea name="description" cols="60" rows="10">'.$description.'</textarea></td></tr>
		<tr><td nowrap><img src="themes/'.$ThemeSel.'/images/icon_help.gif" width="18" height="18" onmouseover="return overlib(\''._POPUP_PERMISSIONS.'\');" onmouseout="return nd();">&nbsp;<b>'._PERMISSIONS.':</b></td><td><select name="permissions">';
		if ($permissions == "None") { $sel1 = "selected"; }
		if ($permissions == "Copy") { $sel2 = "selected"; }
		if ($permissions == "Copy, Modify") { $sel3 = "selected"; }
		if ($permissions == "Copy, Transfer") { $sel4 = "selected"; }
		if ($permissions == "Copy, Modify, Transfer") { $sel5 = "selected"; }
		if ($permissions == "Modify") { $sel6 = "selected"; }
		if ($permissions == "Modify, Transfer") { $sel7 = "selected"; }
		if ($permissions == "Transfer") { $sel8 = "selected"; }		
		echo '<option value="None" '.$sel1.'>'._NONE.'</option>
		<option value="Copy" '.$sel2.'>'._COPY.'</option>
		<option value="Copy, Modify" '.$sel3.'>'._COPY_MODIFY.'</option>
		<option value="Copy, Transfer" '.$sel4.'>'._COPY_TRANSFER.'</option>
		<option value="Copy, Modify, Transfer" '.$sel5.'>'._COPY_MODIFY_TRANSFER.'</option>
		<option value="Modify" '.$sel6.'>'._MODIFY.'</option>
		<option value="Modify, Transfer" '.$sel7.'>'._MODIFY_TRANSFER.'</option>
		<option value="Transfer" '.$sel8.'>'._TRANSFER.'</option>
	    </select>';
		if ($adult == "on") { $selected1 = "checked"; }
		if ($adult == "off") { $selected1 = ""; }
		if ($sales == "on") { $selected2 = "checked"; }
		if ($sales == "off") { $selected2 = ""; }
		if ($messages == "on") { $selected3 = "checked"; }
		if ($messages == "off") { $selected3 = ""; }
		echo '<tr><td nowrap><img src="themes/'.$ThemeSel.'/images/icon_help.gif" width="18" height="18" onmouseover="return overlib(\''._POPUP_PRIMS.'\');" onmouseout="return nd();">&nbsp;<b>'._PRIMS.':</b></td><td><input type="text" name="prims" size="30" maxlength="60" value="'.$prims.'"></td></tr>
		<tr><td nowrap><img src="themes/'.$ThemeSel.'/images/icon_help.gif" width="18" height="18" onmouseover="return overlib(\''._POPUP_LINDEN.'\');" onmouseout="return nd();">&nbsp;<b>'._PRICE_LINDEN.':</b></td><td><input type="text" name="lindens" size="30" maxlength="60" value="'.$lindens.'"></td></tr>
		<tr><td nowrap><img src="themes/'.$ThemeSel.'/images/icon_help.gif" width="18" height="18" onmouseover="return overlib(\''._POPUP_USD.'\');" onmouseout="return nd();">&nbsp;<b>'._PRICE_USD.':</b></td><td><input type="text" name="dollars" size="30" maxlength="60" value="'.$dollars.'"></td></tr>
		<tr><td nowrap><img src="themes/'.$ThemeSel.'/images/icon_help.gif" width="18" height="18" onmouseover="return overlib(\''._POPUP_QUANTITY.'\');" onmouseout="return nd();">&nbsp;<b>'._QUANTITY.':</b></td><td><input type="text" name="quantity" size="30" maxlength="60" value="'.$quantity.'"></td></tr>
	    <tr><td nowrap><img src="themes/'.$ThemeSel.'/images/icon_help.gif" width="18" height="18" onmouseover="return overlib(\''._POPUP_ADULT.'\');" onmouseout="return nd();">&nbsp;<b>'._ADULT.':</b></td><td><input type="checkbox" name="adult" '.$selected1.'></td></tr>
	    <tr><td nowrap><img src="themes/'.$ThemeSel.'/images/icon_help.gif" width="18" height="18" onmouseover="return overlib(\''._POPUP_NOTIFICATIONS_SALES.'\');" onmouseout="return nd();">&nbsp;<b>'._NOTIFICATIONS_SALES.':</b></td><td><input type="checkbox" name="sales" '.$selected2.'></td></tr>
	    <tr><td nowrap><img src="themes/'.$ThemeSel.'/images/icon_help.gif" width="18" height="18" onmouseover="return overlib(\''._POPUP_NOTIFICATIONS_POST.'\');" onmouseout="return nd();">&nbsp;<b>'._NOTIFICATIONS_POST.':</b></td><td><input type="checkbox" name="messages" '.$selected3.'></td></tr>';
		if ($enhancements == 6) { $selection6 = "checked"; }
		if ($enhancements == 5) { $selection5 = "checked"; }
		if ($enhancements == 4) { $selection4 = "checked"; }
		if ($enhancements == 3) { $selection3 = "checked"; }
		if ($enhancements == 2) { $selection2 = "checked"; }
		if ($enhancements == 1) { $selection1 = "checked"; }
		if ($enhancements == 0) { $selection0 = "checked"; }
        echo '<tr><td nowrap><img src="themes/'.$ThemeSel.'/images/icon_help.gif" width="18" height="18" onmouseover="return overlib(\''._POPUP_ENHANCEMENTS.'\');" onmouseout="return nd();">&nbsp;<b>'._ENHANCEMENTS.':</b></td><td>	
	    <input type="radio" name="enhancements" id="enhancement_5" value="6" '.$selection6.'>
		<label for="enhancement_5">'._ENHANCEMENT_5.'</label><br/>
		<input type="radio" name="enhancements" id="enhancement_4" value="5" '.$selection5.'>
		<label for="enhancement_4">'._ENHANCEMENT_4.'</label><br/>
		<input type="radio" name="enhancements" id="enhancement_3" value="4" '.$selection4.'>
		<label for="enhancement_3">'._ENHANCEMENT_3.'</label><br/>
		<input type="radio" name="enhancements" id="enhancement_2" value="3" '.$selection3.'>
		<label for="enhancement_2">'._ENHANCEMENT_2.'</label><br/>
		<input type="radio" name="enhancements" id="enhancement_1" value="2" '.$selection2.'>
		<label for="enhancement_1">'._ENHANCEMENT_1.'</label><br/>
		<input type="radio" name="enhancements" id="enhancement_0" value="1" '.$selection1.'>
		<label for="enhancement_0">'._ENHANCEMENT_0.'</label><br/>
		<input type="radio" name="enhancements" id="enhancement_none" value="0" '.$selection0.'>
		<label for="enhancement_none">'._ENHANCEMENT_NONE.'</label>		
		<tr><td nowrap><img src="themes/'.$ThemeSel.'/images/icon_help.gif" width="18" height="18" onmouseover="return overlib(\''._POPUP_UPLOAD.'\');" onmouseout="return nd();">&nbsp;<b>'._UPLOAD.':</b></td><td><input name="image" type="file" /></td></tr>
        <tr><td>&nbsp;</td><td>
		<input type="hidden" name="MAX_FILE_SIZE" value="100000" />
		<input type="hidden" name="firstname" value="'.$firstname.'">
		<input type="hidden" name="lastname" value="'.$lastname.'">
		<input type="hidden" name="mode" value="Save">
    	<input type="submit" value="'._UPDATETHISFILE.'">
    	</form>
		<form enctype="multipart/form-data" method="post" action="index.php?name='.$module_name.'&amp;file=Edit">
		<input type="hidden" name="id" value="'.$item_id.'">
		<input type="hidden" name="mode" value="Delete">
		<input type="submit" value="'._DELETE.'">
		</form>
		</td></tr></table>';
		
		} // end while	
		Close_Table();
		echo '<br />';
		include ("footer.php");
}

function Delete($id, $ok = 0)
{
	global $prefix, $db, $module_name;
	$id = intval($id);
	if($ok == 1) 
	{
		$db->sql_query("DELETE FROM " . $prefix . "_marketplace_items WHERE id='".$id."'");
		Header("Location: index.php?name=".$module_name);
	}
	else
	{
		include("header.php");
		menu(false);
		Open_Table();
		echo '<br /><center><font class="option">';
		echo '<b>' . _DELEZITEMWARNING . '</b><br /><br />';
		echo '[ <a href="index.php?name='.$module_name.'&amp;file=Edit&amp;mode=Delete&amp;id='.$id.'&amp;ok=1">' . _YES . '</a> | <a href="index.php?name='.$module_name.'">' . _NO . '</a> ]<br /><br />';
		Close_Table();
		include("footer.php");
	}
}

function Save($item_id, $cat, $inventory, $slurl, $title, $description, $permissions, $prims, $lindens, $dollars, $quantity, $adult, $sales, $messages, $enhancements, $image, $firstname, $lastname) 
{
    global $prefix, $db, $user, $admin, $module_name;
	include('modules/'.$module_name.'/config.php');
	if(is_user($user)) 
	{
	    $user2 = base64_decode($user);
	    $user2 = addslashes($user2);
	    $cookie = explode(":", $user2);
	    cookiedecode($user);
		$users = $db->sql_query("SELECT user_id, currency FROM ".$prefix."_users WHERE firstname = '".$cookie[1]."' AND lastname = '".$cookie[2]."' ORDER BY user_id DESC LIMIT 0,1");
		while(list($user_id, $balance) = $db->sql_fetchrow($users))
	    {
		   $current = time();
		   $target_path = 'modules/'.$module_name.'/images/'.$user_id.'/'.$db->previous_id($prefix.'_marketplace_items', 'id', $inventory);
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
		      Open_Table();
		      echo '<center><b>'._INVENTORYNOTITLE.'</b><br /><br />'._GOBACK;
		      Close_Table();
		      include("footer.php");
		   }
		
		   // Check if title title exist
		   if (empty($title)) 
		   {
		      include("header.php");
		      menu(false);
		      echo "<br />";
		      Open_Table();
		      echo '<center><b>'._PRODUCTNOTITLE.'</b><br /><br />'._GOBACK;
		      Close_Table();
		      include("footer.php");
		   }
		   
	       // Check if description was supplied
		   if (empty($description)) 
		   {
		      include("header.php");
		      menu(false);
		      echo "<br />";
		      Open_Table();
		      echo '<center><b>'._DESCRIPTIONNOTITLE.'</b><br /><br />'._GOBACK;
		      Close_Table();
		      include("footer.php");
		   }
	       
		   // Check if permissions was supplied
		   if (empty($permissions)) 
		   {
		      include("header.php");
		      menu(false);
		      echo "<br />";
		      Open_Table();
		      echo '<center><b>'._PERMISSIONSNOTITLE.'</b><br /><br />'._GOBACK;
		      Close_Table();
		      include("footer.php");
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
		      Open_Table();
		      echo '<center><b>'._QUANTITYNOTITLE.'</b><br /><br />'._GOBACK;
		      Close_Table();
		      include("footer.php");
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
		   $sql_submit .= "UPDATE ".$prefix."_marketplace_items SET category = '".$cat[0]."', subcategory = '".$cat[1]."', inventory = '".add_slashes($inventory)."', slurl = '".add_slashes($slurl)."', title = '".add_slashes($title)."', description = '".add_slashes($description)."', permissions = '".add_slashes($permissions)."', prims = '".$prims."', lindens = '".$lindens."', dollars = '".$dollars."', quantity = '".$quantity."', adult = '".$adult."', messages = '".$messages."', enhancements = '".$enhancements."', time = '".$current."'";
		   $current_id = $db->previous_id($prefix.'_marketplace_items', 'id', $inventory);
		   $sql_prepare = "SELECT image, thumbnail FROM ".$prefix."_marketplace_items WHERE id='$current_id' AND firstname='$firstname' AND lastname='$lastname'";
		   $result = $db->sql_query($sql_prepare);
		   list($image_original, $thumbnail_original) = $db->sql_fetchrow($result);
		   if(!file_exists($target_path) && !is_writable($target_path)) 
		   { 
		      @mkdir($target_path, 0777, true);
		      @mkdir($target_path.'/thumbnail', 0777, true); 
		   }	       
		   if(move_uploaded_file($tmp_name, $file_name) && $file_name != $image_original && $thumbnail != $thumbnail_original)
	       {
		      echo $thumbnail;
			  if($file_name != $image_original)
		      {
		         $sql_submit .= ", image = '".add_slashes($file_name)."'";	
		      }			  
		      if($thumbnail != $thumbnail_original)
		      {
		         $sql_submit .= ", thumbnail = '".add_slashes($thumbnail)."'";	
		      }
			  if($file_type != "image/png" && $file_type != "image/gif" && $file_type != "image/pjpeg" && $file_type !="image/jpeg") 
	   	      {
		         include("header.php");
		         menu(false);
		         echo "<br />";
		         Open_Table(); 		   
		         echo "This file type is not allowed";
		         Close_Table();
		         include("footer.php");		   
		         unlink($tmp_name);		
	   	      }			  
		      if ($file_type == "image/jpeg") echo resampimagejpg(80, 80, $file_name, $thumbnail);
			  else if ($file_type == "image/pjpeg") echo resampimagejpg(80, 80, $file_name, $thumbnail);
		      else if ($file_type == "image/gif") resampimagegif(80, 80, $file_name, $thumbnail);
		      else if ($file_type == "image/png") resampimagepng(80, 80, $file_name, $thumbnail);
	       }
		   $sql_submit .= " WHERE firstname = '".$firstname."' AND lastname = '".$lastname."' AND id = '".$item_id."'";
		   include("header.php");
		   menu(false);
		   echo "<br />";
	       Merchants_Menu();
	       echo "<br />";
	       echo "<table width=\"100%\" valign=\"top\"><tr><td valign='top'>";
		   $result2 = $db->sql_query($sql_submit);
		   echo "<br />";
		   Open_Table();
		   echo '<center><b>'._UPDATERECEIVED.'</b><br />';
	       Close_Table();
	       echo "</td>";	
	       categories(0);
	    } // end while
	} // end user
}

if (!(isset($mode))) { $mode = ""; }
if (!(isset($min))) { $min = 0; }
if (!(isset($orderby))) { $orderby = ""; }
if (!(isset($show))) { $show = ""; }
	
switch($mode) 
{
	
	case "Save":
    Save($item_id, $cat, $inventory, $slurl, $title, $description, $permissions, $prims, $lindens, $dollars, $quantity, $adult, $sales, $messages, $enhancements, $image, $firstname, $lastname);
    break;
    
	case "Delete":
	Delete($id, $ok);
	break;
	
	case "Edit":
	Edit($item_id);
    break;
	
    case "items_page":
    views_items($sid, $min, $orderby, $show);
    break;
	
	default:
	views_items($sid, $min, $orderby, $show);
    break;
}
?>