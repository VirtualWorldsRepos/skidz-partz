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

if (!defined('ADMIN_FILE')) die ("Access Denied");

global $prefix, $db, $admin_file;
$aid = substr($aid, 0,31);
$aid2 = substr($aid2, 0,31);
$row = $db->sql_fetchrow($db->sql_query("SELECT title, admins FROM ".$prefix."_modules WHERE title='Marketplace'"));
$row2 = $db->sql_fetchrow($db->sql_query("SELECT name, radminsuper FROM ".$prefix."_authors WHERE aid='".$aid."' AND aid2='".$aid2."'"));
$admins = explode(",", $row['admins']);
$auth_user = 0;
for ($i=0; $i < sizeof($admins); $i++) 
{
	if ($row2['name'] == "$admins[$i]" AND !empty($row['admins'])) 
	{
		$auth_user = 1;
	}
}

if ($row2['radminsuper'] == 1 || $auth_user == 1) 
{		
	function marketplace_menu($page) 
	{
		global $admin_file;
		Open_Table();
		echo "<center><font class='title'><b>" . _MARKETPLACE_CONFIG . "</b></font></center>";
		Close_Table();
		echo "<br />";
		Open_Table();
		echo "<center><b>"._SITECONFIGURE."</b><br /><br />"
			."[ ";
		if ($page == "marketplace_category") 
		{
			echo ""._ADDCATEGORY." | ";
		} 
		else 
		{
			echo "<a href=\"".$admin_file.".php?op=marketplace_category\">"._ADDCATEGORY."</a> | ";
		}
		if ($page == "marketplace_modcategory") 
		{
			echo ""._MODCATEGORY." | ";
		} 
		else 
		{
			echo "<a href=\"".$admin_file.".php?op=marketplace_modcategory\">"._MODCATEGORY."</a> | ";
		}
		if ($page == "modify_items") 
		{
			echo ""._MOD_ITEM." | ";	
		} 
		else 
		{
			echo "<a href=\"".$admin_file.".php?op=modify_items\">"._MOD_ITEM."</a> | ";
		}
		if ($page == "transfer_categories") 
		{
			echo ""._EZTRANSFERDOWNLOADS." | ";
		} 
		else 
		{
			echo "<a href=\"".$admin_file.".php?op=transfer_categories\">"._EZTRANSFERDOWNLOADS."</a> | ";
		}
		if ($page == "informative") 
		{
			echo ""._MESSAGES." ]</center>";
		} 
		else 
		{
			echo "<a href=\"".$admin_file.".php?op=informative\">"._MESSAGES."</a> ]</center>";
		}		
		Close_Table();
		echo "<br />";		
	}
	
    function settings()
	{
		global $prefix, $db, $admin_file;
		include ("header.php");
		GraphicAdmin();
		Open_Table();
		echo "<center><font class='title'><b>" . _MARKETPLACE_CONFIG . "</b></font></center>";
		Close_Table();
		echo "<br>";
		Open_Table();
		echo "<center><b>"._SITECONFIGURE."</b><br><br>";
		echo "[ <a href=\"".$admin_file.".php?op=marketplace_category\">"._ADDCATEGORY."</a> | ";
		echo "<a href=\"".$admin_file.".php?op=marketplace_modcategory\">"._MODCATEGORY."</a> | ";
		echo "<a href=\"".$admin_file.".php?op=modify_items\">"._MOD_ITEM."</a> | ";
		echo "<a href=\"".$admin_file.".php?op=comments\">"._EZTRANSFERDOWNLOADS."</a> | ";
		echo "<a href=\"".$admin_file.".php?op=informative\">"._MESSAGES."</a> ]</center>";
		Close_Table();
        include("footer.php");		
	}
	
	function informative()
	{
		global $prefix, $db, $admin_file;
		include ("header.php");
		GraphicAdmin();
		marketplace_menu("informative");
		Open_Table();
		$row = $db->sql_fetchrow($db->sql_query("SELECT message1, message2 from ".$prefix."_marketplace_messages"));
		$foot1 = filter($row['message1'], "", 0, 'preview');
		$foot2 = filter($row['message2'], "", 0, 'preview');
		echo "<center><font class='option'><b>"._MESSAGESCONFIG."</b></font></center>"
			."<form action='".$admin_file.".php' method='post'>"
			."<table border=\"0\" align=\"center\" cellpadding=\"3\"><tr><td>"
			."" . _MESSAGESLINE1 . ":</td><td><textarea name='message1' cols='70' rows='15'>" . stripslashes($foot1) . "</textarea>"
			."</td></tr><tr><td>"
			."" . _MESSAGESLINE2 . ":</td><td><textarea name='message2' cols='70' rows='15'>" . stripslashes($foot2) . "</textarea>"
			."</td></tr><tr><td>&nbsp;</td><td halign=\"left\"><input type='hidden' name='op' value='save_informative'>"
			."<br><br><input type='submit' value='" . _SAVECHANGES . "'></form></td></tr></table>";		
		Close_Table();
		include("footer.php");	
	}
	
	function save_informative($message1, $message2) 
	{
		global $prefix, $db, $admin_file;
		$message1 = filter($message1, "", 1);
		$message2 = filter($message2, "", 1);
		$db->sql_query("UPDATE ".$prefix."_marketplace_messages SET message1='$message1', message2='$message2'");
		Header("Location: ".$admin_file.".php?op=informative");
		die();
	}
	
	function marketplace_category()
	{
		global $prefix, $db, $module_name, $admin_file;
		include ("header.php");
		include('modules/'.$module_name.'/includes/functions.php');
		GraphicAdmin();
		marketplace_menu('marketplace_category');
		Open_Table();
		echo '<form method="post" action="'.$admin_file.'.php">
		<font class="content"><b>' . _ADDMAINCATEGORY . '</b><br /><br />
		<table widht="100%" bordr="0">
		<tr><td>' . _NAME . ':</td><td><input type="text" name="title" size="30" maxlength="100"></td></tr>
		<tr><td>&nbsp;</td><td><input type="hidden" name="op" value="category_save">
		<input type="submit" value="' . _ADD . '">
		</td></tr></table>
		</form>';
		Close_Table();
		$result6 = $db->sql_query("SELECT * from " . $prefix . "_marketplace_categories");
		$numrows = $db->sql_numrows($result6);
		if ($numrows>0) 
		{
			Open_Table();
			echo '<form method="post" action="'.$admin_file.'.php">
			<font class="content"><b>' . _ADDSUBCATEGORY . '</b></font><br /><br />
			<table widht="100%" bordr="0">
			<tr><td>' . _NAME . ':</td><td><input type="text" name="title" size="30" maxlength="100">&nbsp;' . _IN . '&nbsp;';
			$result7 = $db->sql_query("SELECT cid, title, parentid from " . $prefix . "_marketplace_categories order by parentid,title");
			echo '<select name="cid">';
			while($row7 = $db->sql_fetchrow($result7)) {
				$cid2 = intval($row7['cid']);
				$ctitle2 = filter($row7['title'], "nohtml");
				$parentid2 = intval($row7['parentid']);
				if ($parentid2!=0) $ctitle2 = getparent($parentid2,$ctitle2);
				echo '<option value="'.$cid2.'">'.$ctitle2.'</option>';
			}
			echo '</select></td></tr>
			<tr><td>&nbsp;</td><td><input type="hidden" name="op" value="subcategory_save">
			<input type="submit" value="' . _ADD . '"></td></tr></table>
			</form>';
			Close_Table();
			echo "<br />";
		}
		include ("footer.php");
	}
	
	function category_save($title) 
	{
		global $prefix, $db, $admin_file;
		$result = $db->sql_query("SELECT cid from " . $prefix . "_marketplace_categories where title='".$title."'");
		$numrows = $db->sql_numrows($result);
		if ($numrows>0) {
			include("header.php");
			GraphicAdmin();
			marketplace_menu('marketplace_category');
			Open_Table();
			echo '<br /><center><font class="content">
			<b>' . _ERRORTHECATEGORY . ' '.$title.' ' . _ALREADYEXIST . '</b><br /><br />
			' . _GOBACK . '<br /><br />';
			Close_Table();
			include("footer.php");
		} 
		else 
		{
			$title = filter($title, "nohtml", 1);
			$db->sql_query("INSERT INTO " . $prefix . "_marketplace_categories values (NULL, '".$title."', '0')");
			Header('Location: '.$admin_file.'.php?op=marketplace_category');
		}
	}
	
	function subcategory_save($cid, $title) 
	{
		global $prefix, $db, $admin_file;
		$cid = intval($cid);
		$result = $db->sql_query("SELECT cid from " . $prefix . "_marketplace_categories where title='".$title."' AND cid='".$cid."'");
		$numrows = $db->sql_numrows($result);
		if ($numrows > 0) 
		{
			include("header.php");
			GraphicAdmin();
			marketplace_menu('marketplace_category');
			Open_Table();
			echo '<br /><center>';
			echo '<font class="content">
			<b>' . _ERRORTHESUBCATEGORY . ' '. $title .' ' . _ALREADYEXIST . '</b><br /><br />
			' . _GOBACK . '<br /><br />';
			include("footer.php");
		} else {
			$title = filter($title, "nohtml", 1);
			$db->sql_query("INSERT INTO " . $prefix . "_marketplace_categories values (NULL, '$title', '$cid')");
			Header("Location: ".$admin_file.".php?op=marketplace_category");
		}
	}
	
	function marketplace_modcategory()
	{
		global $prefix, $db, $module_name, $admin_file;
		include ("header.php");
		include('modules/'.$module_name.'/includes/functions.php');
		GraphicAdmin();
		marketplace_menu('marketplace_modcategory');
		$result10 = $db->sql_query("SELECT * from " . $prefix . "_marketplace_categories");
		$numrows = $db->sql_numrows($result10);
		if ($numrows>0) 
		{
			Open_Table();
			echo '<form method="post" action="'.$admin_file.'.php"><font class="content"><b>' . _MODCATEGORY . '</b></font><br /><br />';
			$result11 = $db->sql_query("SELECT cid, title, parentid from " . $prefix . "_marketplace_categories order by title");
			echo  _CATEGORY . ': <select name="cat">';
			while($row11 = $db->sql_fetchrow($result11)) {
				$cid2 = intval($row11['cid']);
				$ctitle2 = filter($row11['title'], "nohtml");
				$parentid2 = intval($row11['parentid']);
				if ($parentid2!=0) $ctitle2 = getparent($parentid2,$ctitle2);
				echo '<option value="'.$cid2.'">'.$ctitle2.'</option>';
			}
			echo '</select>
			<input type="hidden" name="op" value="modify_category">
			&nbsp;<input type="submit" value="' . _MODIFY . '">
			</form>';
			Close_Table();
			echo "<br>";
		}
		include("footer.php");		

	}
	
	function modify_category($cat) 
	{
		global $prefix, $db, $admin_file;
		include ("header.php");
		GraphicAdmin();
		marketplace_menu('marketplace_modcategory');
		$cat = explode("-", $cat);
		$cat[0] = intval($cat[0]);
		$cat[1] = intval($cat[1]);
		Open_Table();
		echo "<center><font class=\"content\"><b>" . _MODCATEGORY . "</b></font></center><br><br>";
		if ($cat[1] == 0) 
		{
			$row = $db->sql_fetchrow($db->sql_query("SELECT title from " . $prefix . "_marketplace_categories where cid='".$cat[0]."'"));
			$title = filter($row['title'], "nohtml");
			echo '<form action="'.$admin_file.'.php" method="get">
			' . _NAME . ': <input type="text" name="title" value="'.$title.'" size="51" maxlength="50"><br />
			<input type="hidden" name="cid" value="'.$cat[0].'">
			<input type="hidden" name="op" value="update_category">
			<table border="0"><tr><td>
			<input type="submit" value="' . _SAVECHANGES . '"></form></td><td>
			<form action="'.$admin_file.'.php" method="get">
			<input type="hidden" name="cid" value="'.$cat[0].'">
			<input type="hidden" name="op" value="delete_category">
			<input type="submit" value="' . _DELETE . '"></form></td></tr></table>';
		}
		Close_Table();
		include("footer.php");
	}
	
	function update_category($cid, $title) 
	{
		global $prefix, $db, $admin_file;
		$cid = intval($cid);
		$db->sql_query("UPDATE " . $prefix . "_marketplace_categories SET title='".$title."' WHERE cid='".$cid."'");
		Header("Location: ".$admin_file.".php?op=marketplace_modcategory");
	}
	
	function delete_category($cid, $ok=0)
	{
		global $prefix, $db, $admin_file;
		$cid = intval($cid);
		if($ok == 1) 
		{
				$db->sql_query("DELETE FROM " . $prefix . "_marketplace_items where category='".$cid."'");
				$result2 = $db->sql_query("SELECT cid from " . $prefix . "_marketplace_categories where parentid='".$cid."'");
				while ($row2 = $db->sql_fetchrow($result2)) {
					$cid2 = intval($row2['cid']);
					$db->sql_query("DELETE FROM " . $prefix . "_marketplace_items where category='".$cid2."'");
				}
				$db->sql_query("DELETE FROM " . $prefix . "_marketplace_categories where parentid='".$cid."'");
				$db->sql_query("DELETE FROM " . $prefix . "_marketplace_categories where cid='".$cid."'");
			Header("Location: ".$admin_file.".php?op=marketplace_modcategory");
		} 
		else 
		{
			$result = $db->sql_query("SELECT * from " . $prefix . "_marketplace_categories where parentid='".$cid."'");
			$nbsubcat = $db->sql_numrows($result);
			$result3 = $db->sql_query("SELECT cid from " . $prefix . "_marketplace_categories where parentid='".$cid."'");
			while ($row3 = $db->sql_fetchrow($result3)) 
			{
				$cid2 = intval($row3['cid']);
				$result4 = $db->sql_query("SELECT * from " . $prefix . "_marketplace_items where category='".$cid2."'");
				$nblink = $db->sql_numrows($result4);
			}
			include("header.php");
			GraphicAdmin();
			Open_Table();
			echo '<br /><center><font class="option">';
			echo '<b>' . _EZTHEREIS . ' '.$nbsubcat.' ' . _EZSUBCAT . ' ' . _EZATTACHEDTOCAT . '</b><br />';
			echo '<b>' . _EZTHEREIS . ' '.$nblink.' ' . _ITEMS. ' ' . _EZATTACHEDTOCAT . '</b><br />';
			echo '<b>' . _DELEZDOWNLOADSCATWARNING . '</b><br /><br />';
		}
		echo '[ <a href="'.$admin_file.'.php?op=delete_category&amp;cid='.$cid.'&amp;ok=1">' . _YES . '</a> | <a href="'.$admin_file.'.php?op=Marketplace">' . _NO . '</a> ]<br /><br />';
		Close_Table();
		include("footer.php");
	}
	
	function transfer_categories()
	{
		global $db, $prefix, $module_name, $admin_file;
		$result13 = $db->sql_query("SELECT * from " . $prefix . "_marketplace_items");
		$numrows = $db->sql_numrows($result13);
		if ($numrows > 0) 
		{
			include("header.php");
			include('modules/'.$module_name.'/includes/functions.php');
			GraphicAdmin();
			marketplace_menu("transfer_categories");
			Open_Table();
			echo '<form method="post" action="'.$admin_file.'.php">
			<font class="option"><b>' . _EZTRANSFERDOWNLOADS . '</b></font><br /><br />
			<table widht="100%" bordr="0">
			<tr><td>' . ucfirst(_IN) . '&nbsp;' . _CATEGORY . ':</td><td>
			<select name="cidfrom">';
			$result14 = $db->sql_query("SELECT cid, title, parentid FROM " . $prefix . "_marketplace_categories ORDER BY parentid, title");
			while($row14 = $db->sql_fetchrow($result14)) 
			{
				$cid2 = intval($row14['cid']);
				$ctitle2 = filter($row14['title'], "nohtml");
				$parentid2 = intval($row14['parentid']);
				if ($parentid2!=0) $ctitle2 = getparent($parentid2,$ctitle2);
				echo '<option value="'.$cid2.'">'.$ctitle2.'</option>';
			}
			echo '</select></td></tr></tr><td>' . ucfirst(_TO) . '&nbsp;' . _CATEGORY . ':</td><td>';
			$result15 = $db->sql_query("SELECT cid, title, parentid FROM " . $prefix . "_marketplace_categories ORDER BY parentid, title");
			echo '<select name="cidto">';
			while($row15 = $db->sql_fetchrow($result15)) 
			{
				$cid2 = intval($row15['cid']);
				$ctitle2 = filter($row15['title'], "nohtml");
				$parentid2 = intval($row15['parentid']);
				if ($parentid2!=0) $ctitle2 = getparent($parentid2,$ctitle2);
				echo '<option value="'.$cid2.'">'.$ctitle2.'</option>';
			}
			echo '</select></td></tr>
			<tr><td>&nbsp;</td><td><input type="hidden" name="op" value="transfer_save">
			<input type="submit" value="' . _EZTRANSFER . '"></td></tr></table>
			</form>';
			Close_Table();
			include("footer.php");
		}
        else
        {
			include("header.php");
		    GraphicAdmin();
		    marketplace_menu("transfer_categories");
		    Open_Table();		
		    echo '<center><font class="title"><b>'._NOCATEGORYEXIST.'</b></font></center>';
		    Close_Table();
		    include("footer.php");
        }		
	}
	
	function transfer_save($cidfrom, $cidto) 
	{
		global $prefix, $db, $admin_file;
		$cidfrom = intval($cidfrom);
		$db->sql_query("update " . $prefix . "_marketplace_items SET category='".$cidto."' WHERE category='".$cidfrom."'");
		Header("Location: ".$admin_file.".php?op=transfer_categories");
	}
	
	function modify_items()
	{		
		global $prefix, $db, $module_name, $admin_file;
		$result = $db->sql_query("SELECT * from " . $prefix . "_marketplace_items");
		$numrows = $db->sql_numrows($result);
		if ($numrows>0) 
		{
		    include ("header.php");
		    GraphicAdmin();
		    marketplace_menu('marketplace_modcategory');			
			Open_Table();
			echo '<form method="post" action="'.$admin_file.'.php"><font class="content"><b>' . _MODIFY_ITEM . '</b></font><br /><br />';
			$result2 = $db->sql_query("SELECT id, title from " . $prefix . "_marketplace_items order by title");
			echo  _SELECT_ITEM . ': <select name="lid">';
			while($row = $db->sql_fetchrow($result2)) 
			{
				$id = intval($row['id']);
				$title = filter($row['title'], "nohtml");
				echo '<option value="'.$id.'">'.$title.'</option>';
			}
			echo '</select>
			<input type="hidden" name="op" value="marketplace_item">
			&nbsp;<input type="submit" value="' . _MODIFY . '">
			</form>';
			Close_Table();
			include("footer.php");
		}
		else
		{
		    include ("header.php");
		    GraphicAdmin();
		    marketplace_menu('marketplace_modcategory');			
			Open_Table();
            echo '<center><font class="title"><b>'._NOITEMEXIST.'</b></font></center>';			
			Close_Table();
			include("footer.php");		
		}		
	}

	function marketplace_item($lid)
	{
       global $prefix, $db, $admin_file, $module_name;
       include("header.php");
	   include('modules/'.$module_name.'/includes/functions.php');
	   GraphicAdmin();
	   marketplace_menu('modify_items');
       echo "<br />";
       Open_Table();
	   $lid = intval($lid);
	   $ThemeSel = get_theme();
       echo '<center><font class="title"><b>'._EDITAPRODUCT.'</b></font></center><br />';
       $result = $db->sql_query("SELECT id, category, subcategory, inventory, slurl, title, description, editor, permissions, prims, lindens, dollars, quantity, adult, sales, messages, enhancements, image, firstname, lastname from " . $prefix . "_marketplace_items where id='$lid'");
	   while($row = $db->sql_fetchrow($result)) 
       {	
          $id = intval($row['id']);
		  $category = intval($row['category']);
		  $subcategory = intval($row['subcategory']);
		  $inventory = filter($row['inventory'], "nohtml");
		  $slurl = filter($row['slurl'], "nohtml");
		  $title = filter($row['title'], "nohtml");
		  $description = filter($row['description'], "nohtml");
		  $editor = filter($row['editor'], "nohtml");
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
    	        <tr><td nowrap><form enctype="multipart/form-data" method="post" action="'.$admin_file.'.php">';
		  $result2 = $db->sql_query("SELECT cid, title, parentid from " . $prefix . "_marketplace_categories order by title");
		  echo '<input type="hidden" name="lid" value="'.$lid.'">
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
			 if($item_name == $inventory)
			 {
			    echo '<option value="'.$item_name.'" style="background-image: url(themes/'.$ThemeSel.'/images/inventory/'.$item_type.'.png);background-repeat: no-repeat; padding-left: 20px; height: 16px;" '.$sel.'>'.$item_name.'</option>';
	  	     }
		  }
    	  echo '</select></td></tr>
		        <tr><td nowrap><img src="themes/'.$ThemeSel.'/images/icon_help.gif" width="18" height="18" onmouseover="return overlib(\''._POPUP_SLURL.'\');" onmouseout="return nd();">&nbsp;<b>'._SLURL.':</b></td><td><input type="text" name="slurl" value="'.$slurl.'" size="40" maxlength="255">&nbsp;<a href="http://slurl.com/build.php">'._SLURL_CREATE.'</a>&nbsp;<a href="http://slurl.com/about.php">'._SLURL_ABOUT.'</a></td></tr>
		        <tr><td nowrap><img src="themes/'.$ThemeSel.'/images/icon_help.gif" width="18" height="18" onmouseover="return overlib(\''._POPUP_TITLE.'\');" onmouseout="return nd();">&nbsp;<b>'._DOWNLOADNAME.':</b></td><td><input type="text" name="title" value="'.$title.'"  size="40" maxlength="100"></td></tr>	
		        <tr><td nowrap><img src="themes/'.$ThemeSel.'/images/icon_help.gif" width="18" height="18" onmouseover="return overlib(\''._POPUP_DESCRIPTION.'\');" onmouseout="return nd();">&nbsp;<b>'._DESCRIPTION.':</b></td><td><textarea name="description" cols="60" rows="10">'.$description.'</textarea></td></tr>
		        <tr><td nowrap><img src="themes/'.$ThemeSel.'/images/icon_help.gif" width="18" height="18" onmouseover="return overlib(\''._POPUP_EDITOR_NOTE.'\');" onmouseout="return nd();">&nbsp;<b>'._EDITOR_NOTE.':</b></td><td><textarea name="editor" cols="60" rows="10">'.$editor.'</textarea></td></tr>
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
		        <input type="hidden" name="op" value="save_item">
    	        <input type="submit" value="'._ADDTHISFILE.'">
    	        </form></td></tr></table>';
		
		}
		Close_Table();
		echo '<br />';
		include ("footer.php");
	}
	
	function save_item($lid, $cat, $inventory, $slurl, $title, $description, $editor, $permissions, $prims, $lindens, $dollars, $quantity, $adult, $sales, $messages, $enhancements, $image, $firstname, $lastname) 
	{
        global $prefix, $db, $user, $admin, $module_name;
		include('modules/'.$module_name.'/config.php');
		include('modules/'.$module_name.'/includes/functions.php');
		$users = $db->sql_query("SELECT user_id, currency FROM ".$prefix."_users WHERE firstname = '".$firstname."' AND lastname = '".$lastname."' ORDER BY user_id DESC LIMIT 0,1");
		while(list($user_id, $balance) = $db->sql_fetchrow($users))
	    {
		   //$current = date('Y-m-d G:i:s');
		   $current = time();
		   $target_path = 'modules/'.$module_name.'/images/'.$user_id.'/'.$db->previous_id($prefix.'_marketplace_items', 'id', $inventory);
		   $user_file = $_FILES['image']['name'];
		   $file_type = $_FILES['image']['type'];
		   $tmp_name = $_FILES['image']['tmp_name'];
		   //$file_name = $target_path.'/'.$_FILES['image']['name'];
           if ($file_type == "image/jpeg") $user_ext = '.jpeg';
           else if ($file_type == "image/gif") $user_ext = '.gif';
           else if ($file_type == "image/png") $user_ext = '.png';
		   $file_name = $target_path.'/'.sha1($user_file).$user_ext;
		   $thumbnail = $target_path.'/thumbnail/'.sha1($user_file).$user_ext;
		   if (empty($inventory)) 
		   {
		      include("header.php");
			  GraphicAdmin();
	          marketplace_menu('modify_items');
		      echo "<br />";
		      Open_Table();
		      echo '<center><b>'._INVENTORYNOTITLE.'</b><br /><br />'._GOBACK;
		      Close_Table();
		      include("footer.php");
		   }
		   if (empty($title)) 
		   {
		      include("header.php");
			  GraphicAdmin();
	          marketplace_menu('modify_items');			  
		      echo "<br />";
		      Open_Table();
		      echo '<center><b>'._PRODUCTNOTITLE.'</b><br /><br />'._GOBACK;
		      Close_Table();
		      include("footer.php");
		   }
		   if (empty($description)) 
		   {
		      include("header.php");
			  GraphicAdmin();
	          marketplace_menu('modify_items');			  
		      echo "<br />";
		      Open_Table();
		      echo '<center><b>'._DESCRIPTIONNOTITLE.'</b><br /><br />'._GOBACK;
		      Close_Table();
		      include("footer.php");
		   }
		   if (empty($permissions)) 
		   {
		      include("header.php");
			  GraphicAdmin();
	          marketplace_menu('modify_items');			  
		      echo "<br />";
		      Open_Table();
		      echo '<center><b>'._PERMISSIONSNOTITLE.'</b><br /><br />'._GOBACK;
		      Close_Table();
		      include("footer.php");
		   }
		   if (empty($lindens)) 
		   {
		      $lindens = '0';
		   }
		   if (empty($dollars)) 
		   {
		      $dollars = '0';
		   }
		   if (empty($quantity)) 
		   {
		      include("header.php");
			  GraphicAdmin();
	          marketplace_menu('modify_items');
		      echo "<br />";
		      Open_Table();
		      echo '<center><b>'._QUANTITYNOTITLE.'</b><br /><br />'._GOBACK;
		      Close_Table();
		      include("footer.php");
		   }
		   if (empty($adult)) 
		   {
		      $adult = 'off';
		   }
		   if (empty($sales)) 
		   {
		      $sales = 'off';
		   }
		   if (empty($messages)) 
		   {
		      $messages = 'off';
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
		   $editor = filter($editor, "nohtml", 1);
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
		   $sql_submit .= "UPDATE ".$prefix."_marketplace_items SET category = '".$cat[0]."', subcategory = '".$cat[1]."', inventory = '".add_slashes($inventory)."', slurl = '".add_slashes($slurl)."', title = '".add_slashes($title)."', description = '".add_slashes($description)."', editor = '".add_slashes($editor)."', permissions = '".add_slashes($permissions)."', prims = '".intval($prims)."', lindens = '".intval($lindens)."', dollars = '".intval($dollars)."', quantity = '".intval($quantity)."', adult = '".$adult."', sales = '".$sales."', messages = '".$messages."', enhancements = '".intval($enhancements)."', time = '".$current."'";
		   $current_id = $db->previous_id($prefix.'_marketplace_items', 'id', $inventory);
		   $sql_prepare = "SELECT image, thumbnail FROM ".$prefix."_marketplace_items WHERE id='$current_id' AND firstname='$firstname' AND lastname='$lastname'";
		   $result = $db->sql_query($sql_prepare);
		   list($image_original, $thumbnail_original) = $db->sql_fetchrow($result);
	
		   $sql_submit .= " WHERE firstname = '".$firstname."' AND lastname = '".$lastname."' AND id = '".$lid."'";
		   
	       if(move_uploaded_file($tmp_name, $file_name) && $file_name != $image_original && $thumbnail != $thumbnail_original)
	       {
			  if($file_name != $image_original)
			  {
		         $sql_submit .= ", image = '".add_slashes($file_name)."'";
			  }
			  if($thumbnail != $thumbnail_original)
			  {
		         $sql_submit .= ", thumbnail = '".add_slashes($thumbnail)."'";	
			  }
			  if(!file_exists($target_path) && !is_writable($target_path)) 
			  { 
		         @mkdir($target_path, 0777);
		         @mkdir($target_path.'/thumbnail', 0777); 
			  }	   	      
			  if($file_type != "image/png" && $file_type != "image/gif" && $file_type != "image/pjpeg" && $file_type !="image/jpeg") 
	   	      {
		          include("header.php");
			      GraphicAdmin();
	              marketplace_menu('modify_items');
		          echo "<br />";
		          Open_Table(); 		   
		          echo "This file type is not allowed";
		          Close_Table();
		          include("footer.php");		   
		          unlink($tmp_name);		
	   	      }		   
		      if ($file_type == "image/jpeg") resampimagejpg(80, 80, $file_name, $thumbnail);
		      else if ($file_type == "image/gif") resampimagegif(80, 80, $file_name, $thumbnail);
		      else if ($file_type == "image/png") resampimagepng(80, 80, $file_name, $thumbnail);
	      }
		  include("header.php");
		  GraphicAdmin();
	      marketplace_menu('modify_items');
		  $result2 = $db->sql_query($sql_submit);
		  echo '<br />';
		  Open_Table();
		  echo '<center><b>'._ITEM_UPDATED.'</b><br />';
		  Close_Table();
		  include("footer.php");
	    }		
	}

	switch ($op) 
	{		
		case "Marketplace":
		settings();
		break;
		
		case "settings":
		settings();
		break;
		
		case "informative":
		informative();
		break;
		
		case "save_informative":
		save_informative($message1, $message2);
		break;
		
		case "marketplace_category":
		marketplace_category();
		break;
		
		case "marketplace_modcategory":
		marketplace_modcategory();
		break;
		
		case "modify_category":
		modify_category($cat);
		break;
		
		case "category_save":
		category_save($title);
		break;

		case "subcategory_save":
		subcategory_save($cid, $title);
		break;
		
		case "delete_category":
		delete_category($cid, $ok);
		break;

		case "update_category":
		update_category($cid, $title);
		break;

        case "transfer_categories":
        transfer_categories();
        break;

		case "transfer_save":
		transfer_save($cidfrom, $cidto);
		break;
		
        case "modify_items":
        modify_items();
        break;

		case "marketplace_item":
		marketplace_item($lid);
		break;
		
		case "save_item":
		save_item($lid, $cat, $inventory, $slurl, $title, $description, $editor, $permissions, $prims, $lindens, $dollars, $quantity, $adult, $sales, $messages, $enhancements, $image, $firstname, $lastname);
		break;
		
	}

} else {
	include("header.php");
	GraphicAdmin();
	Open_Table();
	echo "<center><b>"._ERROR."</b><br><br>You do not have administration permission for module \"$module_name\"</center>";
	Close_Table();
	include("footer.php");
}

?>