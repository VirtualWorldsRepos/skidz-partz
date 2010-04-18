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

if (!defined('ADMIN_FILE')) {
	die ("Access Denied");
}

global $prefix, $db, $admin_file;
$aid = substr($aid, 0,31);
$aid = substr($aid, 0,31);
$row = $db->sql_fetchrow($db->sql_query("SELECT title, admins FROM ".$prefix."_modules WHERE title='Terminal_Locations'"));
$row2 = $db->sql_fetchrow($db->sql_query("SELECT name, radminsuper FROM ".$prefix."_authors WHERE aid='$aid' AND aid2='$aid2'"));
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

	function Terminals() 
	{
		global $prefix, $db, $module_name, $admin_file;
		include ("header.php");
		GraphicAdmin();
		Open_Table();
		$ThemeSel = get_theme();
		if (file_exists('themes/'.$ThemeSel.'/images/terminal-logo.gif')) 
		{
			echo '<center><a href="index.php?name='.$module_name.'"><img src="themes/'.$ThemeSel.'/images/terminal-logo.gif" border="0" alt="'._TERMINAL_LOCATIONS.'"></a><br /><br />';
		} 
		else 
		{
			echo '<center><a href="index.php?name='.$module_name.'"><img src="modules/'.$module_name.'/images/terminal-logo.gif" border="0" alt="'._TERMINAL_LOCATIONS.'"></a><br /><br />';
		}
		$result = $db->sql_query("SELECT * from " . $prefix . "_terminals");
		$numrows = $db->sql_numrows($result);
		echo '<font class="content">' . _THEREARE . '&nbsp;<b>'.$numrows.'</b> ' . _TERMINALSINDB . '</font></center>';
		Close_Table();
		echo "<br />";
		Open_Table();
		echo '<form method="post" action="'.$admin_file.'.php">
		<font class="content"><b>' . _ADDTERMINAL . '</b><br><br>
		<table widht="100%" bordr="0">
		<tr><td>' . _LOCATION . ':<input type="text" name="location" size="30" maxlength="100"></td></tr>
		<tr><td>' . _SLURL . ':<input type="text" name="slurl" size="30" maxlength="100"></td></tr>
		' . _UUID . ':<input type="text" name="uuid" value="'.$uuid.'" size="51" maxlength="50"></td></tr>
		<tr><td>' . _TYPE . ':<select name="type">
		<option name="type" value="0">'._ATM_TYPE_0.'</option>
		<option name="type" value="1">'._ATM_TYPE_1.'</option>
		<option name="type" value="2">'._ATM_TYPE_2.'</option>
		<option name="type" value="3">'._ATM_TYPE_3.'</option>
		</select>
		<tr><td>&nbsp;</td><td><input type="hidden" name="op" value="terminal_save">
		<input type="submit" value="' . _ADD . '">
		</td></tr></table>
		</form>';
		Close_Table();
		echo "<br />";
		// Modify Terminal
		$result10 = $db->sql_query("SELECT * from " . $prefix . "_terminals");
		$numrows = $db->sql_numrows($result10);
		if ($numrows>0) 
		{
			Open_Table();
			echo '<form method="post" action="'.$admin_file.'.php"><font class="content"><b>' . _MODTERMINALS . '</b></font><br /><br />';
			$result11 = $db->sql_query("SELECT id, uuid, location, slurl, type from " . $prefix . "_terminals order by location");
			echo  _TERMINALS . ': <select name="terminal">';
			while($row11 = $db->sql_fetchrow($result11)) 
			{
				$terminal_id = intval($row11['id']);
				$terminal_location = filter($row11['location'], "nohtml");
				echo '<option value="'.$terminal_id.'">'.$terminal_location.'</option>';
			}
			echo "</select>"
			."<input type=\"hidden\" name=\"op\" value=\"terminals_mod\">" //@todo rename me when category is case + function renamed
			."&nbsp;<input type=\"submit\" value=\"" . _MODIFY . "\">"
			."</form>";
			Close_Table();
			echo "<br>";
		} else {
		}
		include ("footer.php");
	}

	function terminals_mod($terminal) 
	{
		global $prefix, $db, $admin_file;
		include ("header.php");
		GraphicAdmin();
		Open_Table();
		echo "<center><font class=\"title\"><b>" . _WEBDOWNLOADSADMIN . "</b></font></center>";
		Close_Table();
		echo "<br>";
		$terminal = explode("-", $terminal);
		if (empty($terminal[1])) {
			$terminal[1] = 0;
		}
		$terminal[0] = intval($terminal[0]);
		$terminal[1] = intval($terminal[1]);
		Open_Table();		
		echo "<center><font class=\"content\"><b>" . _MODTERMINAL . "</b></font></center><br><br>";
		if ($terminal[1] == 0) 
		{
			$row = $db->sql_fetchrow($db->sql_query("SELECT uuid, location, slurl, type from " . $prefix . "_terminals where id='".$terminal[0]."'"));
			$uuid = filter($row['uuid'], "nohtml");
			$location = filter($row['location'], "nohtml");
			$slurl = filter($row['slurl']);
			$type = intval($row['type']);
			if ($type == 0) { $sel1 = "selected"; }
		    if ($type == 1) { $sel2 = "selected"; }
		    if ($type == 2) { $sel3 = "selected"; }
		    if ($type == 3) { $sel4 = "selected"; }
			echo '<form action="'.$admin_file.'.php" method="get">
			' . _LOCATION . ':&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="location" value="'.$location.'" size="51" maxlength="50"><br />
			' . _SLURL . ':&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="slurl" value="'.$slurl.'" size="51" maxlength="50"><br />
			' . _UUID . ': <input type="text" name="uuid" value="'.$uuid.'" size="51" maxlength="50"><br />
			' . _TYPE . ': &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<select name="type">
			<option name="type" value="0" '.$sel1.'>'._ATM_TYPE_0.'</option>
			<option name="type" value="1" '.$sel2.'>'._ATM_TYPE_1.'</option>
			<option name="type" value="2" '.$sel3.'>'._ATM_TYPE_2.'</option>
			<option name="type" value="3" '.$sel4.'>'._ATM_TYPE_3.'</option>
			</select>
			<input type="hidden" name="id" value="'.$terminal[0].'">
			<input type="hidden" name="op" value="terminals_save">
			<table border="0"><tr><td>
			<input type="submit" value="' . _SAVECHANGES . '"></form></td><td>';
			echo '<form action="'.$admin_file.'.php" method="get">
			<input type="hidden" name="op" value="terminals_delete">
			<input type="hidden" name="id" value="'.$terminal[0].'">
			<input type="submit" value="' . _DELETE . '"></form></td></tr></table>'; //terminals_delete
		}
		Close_Table();
		include("footer.php");
	}

	function terminals_save($id, $location, $slurl, $uuid, $type)	
	{
		global $prefix, $db, $admin_file;
		$id = intval($id);
		$db->sql_query("UPDATE " . $prefix . "_terminals SET location='".$location."', slurl='".$slurl."', uuid='".$uuid."', type='".$type."' WHERE id='".$id."'");
		Header("Location: ".$admin_file.".php?op=Terminals");
	}

	function terminals_delete($id, $ok = 0) 
	{
		global $prefix, $db, $admin_file;
		$id = intval($id);
		if($ok == 1)
		{
		   $db->sql_query("DELETE FROM " . $prefix . "_terminals where id='".$id."'");
		   Header("Location: ".$admin_file.".php?op=Terminals");
		}
		include("header.php");
		GraphicAdmin();
		Open_Table();
		echo "<br /><center><font class=\"option\">" . _DELETETERMINALWARNING . "</b></font></center>";	
		echo "<center>[ <a href=\"".$admin_file.".php?op=terminals_delete&amp;id=$id&amp;ok=1\">" . _YES . "</a> | <a href=\"".$admin_file.".php?op=Terminals\">" . _NO . "</a> ]<br /><br /></center>";
		Close_Table();
		include("footer.php");
	}
	
	function terminal_save($location, $slurl, $uuid, $type) {
		global $prefix, $db, $admin_file;
		$result = $db->sql_query("SELECT id FROM " . $prefix . "_terminals WHERE location='".$location."' AND slurl='".$slurl."'");
		$numrows = $db->sql_numrows($result);
		if ($numrows > 0) 
		{
			include("header.php");
			GraphicAdmin();
			Open_Table();
			echo '<br /><center><font class="content"><b>' . _ERRORTHETERMINAL . ' '.$title.' ' . _TERMINALALEXIST . '</b><br /><br />' . _GOBACK . '<br /><br />';
			Close_Table();
			include("footer.php");
		} 
		else 
		{
			$location = filter($location, "nohtml", 1);
			$slurl = filter($slurl, "nohtml", 1);
			$uuid = filter($uuid, "nohtml", 1);
			$type = intval($type);
			$db->sql_query("insert into " . $prefix . "_terminals values (NULL, '".$uuid."', '".$location."', '".$slurl."', '".$type."')");
			Header("Location: ".$admin_file.".php?op=Terminals");
		}
	}

if (!isset($submitter)) { $submitter = ''; }

	switch ($op) 
	{
		case "Terminals":
		Terminals();
		break;
		
		case "terminals_mod":
		terminals_mod($terminal);
		break;

		case "terminals_save":
		//terminals_save($cid, $sid, $sub, $title, $cdescription);
		terminals_save($id, $location, $slurl, $uuid, $type);
		break;
		
		case "terminals_delete":
		terminals_delete($id, $ok);
		break;		

		case "terminal_save":
		terminal_save($location, $slurl, $uuid, $type);
		break;
	}

} 
else 
{
	include('header.php');
	GraphicAdmin();
	Open_Table();
	echo '<center><b>'._ERROR.'</b><br><br>You do not have administration permission for module '.$module_name.'</center>';
	Close_Table();
	include('footer.php');
}

?>