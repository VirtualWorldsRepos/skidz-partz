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
$aid2 = substr($aid2, 0,31);
$row = $db->sql_fetchrow($db->sql_query("SELECT title, admins FROM ".$prefix."_modules WHERE title='News'"));
$row2 = $db->sql_fetchrow($db->sql_query("SELECT name, radminsuper FROM ".$prefix."_authors WHERE aid='".$aid."' AND aid2='".$aid2."'"));
$admins = explode(",", $row['admins']);
$auth_user = 0;
for ($i=0; $i < sizeof($admins); $i++) 
{
	if ($row2['name'] == $admins[$i] && !empty($row['admins'])) 
	{
		$auth_user = 1;
	}
}

if ($row2['radminsuper'] == 1) 
{
	$radminsuper = 1;
}

if ($row2['radminsuper'] == 1 || $auth_user == 1) 
{

function set_home($ihome, $acomm) 
{
	echo '<br /><b />'._PUBLISHINHOME.'</b>&nbsp;&nbsp;';
	if (($ihome == 0) || (empty($ihome))) 
	{
		$sel1 = 'checked';
		$sel2 = '';
	}
	if ($ihome == 1) 
	{
		$sel1 = '';
		$sel2 = 'checked';
	}
	
	echo '<input type="radio" name="ihome" value="0" '.$sel1.'>'._YES.'&nbsp;
	<input type="radio" name="ihome" value="1" '.$sel2.'>'._NO.'
	&nbsp;&nbsp;<font class="content">[ '._ONLYIFCATSELECTED.' ]</font><br />
	<br /><b>'._ACTIVATECOMMENTS.'</b>&nbsp;&nbsp;';
		
	if (($acomm == 0) || (empty($acomm)))
	{
		$sel1 = 'checked';
		$sel2 = '';
	}
	if ($acomm == 1) 
	{
		$sel1 = '';
		$sel2 = 'checked';
	}
	echo '<input type="radio" name="acomm" value="0" '.$sel1.'>'._YES.'&nbsp;
		  <input type="radio" name="acomm" value="1" '.$sel2.'>'._NO.'</font><br /><br />';

}

	function deleteStory($qid) 
	{
		global $prefix, $db, $admin_file;
		$qid = intval($qid);
		$result = $db->sql_query("DELETE FROM ".$prefix."_queue WHERE qid='".$qid."'");
		if (!$result) 
		{
			return;
		}
		Header("Location: ".$admin_file.".php?op=submissions");
	}

	function SelectCategory($cat) 
	{
		global $prefix, $db, $admin_file;
		$selcat = $db->sql_query("SELECT catid, title from ".$prefix."_stories_cat ORDER BY title");
		$a = 1;
		echo '<b>'._CATEGORY.'</b><select name="catid">';
		if ($cat == 0) 
		{
			$sel = 'selected';
		} 
		else 
		{
			$sel = '';
		}
		echo '<option name="catid" value="0" '.$sel.'>'._ARTICLES.'</option>';
		while(list($catid, $title) = $db->sql_fetchrow($selcat))
		{
			$catid = intval($catid);
			$title = filter($title, "nohtml");
			if ($catid == $cat)
			{
				$sel = 'selected';
			}
			else
			{
				$sel = '';
			}
			echo '<option name="catid" value="'.$catid.'" '.$sel.'>'.$title.'</option>';
			$a++;
		}
		echo '</select>&nbsp;&nbsp;&nbsp;<a href="'.$admin_file.'.php?op=AddCategory"><img src="images/add.gif" alt="'._ADD.'" title="'._ADD.'" border="0" width="17" height="17"></a>&nbsp;&nbsp;&nbsp;<a href="'.$admin_file.'.php?op=EditCategory"><img src="images/edit.gif" alt="'._EDIT.'" title="'._EDIT.'" border="0" width="17" height="17"></a>&nbsp;&nbsp;&nbsp;<a href="'.$admin_file.'.php?op=DelCategory"><img src="images/delete.gif" alt="'._DELETE.'" title="'._DELETE.'" border="0" width="17" height="17"></a>';
	}

	function putpoll($pollTitle, $optionText) 
	{
		Open_Table();
		echo '<center><font class="title"><b>'._ATTACHAPOLL.'</b></font><br />
		<font class="tiny">'._LEAVEBLANKTONOTATTACH.'</font><br />
		<br /><br>'._POLLTITLE.':&nbsp;<input type="text" name="pollTitle" size="50" maxlength="100" value="'.$pollTitle.'"><br /><br />
		<font class="content">'._POLLEACHFIELD.'<br />
		<table border="0">';
		for($i = 1; $i <= 12; $i++)	
		{
			echo '<tr><td>'._OPTION.'&nbsp;'.$i.':</td><td><input type="text" name="optionText['.$i.']" size="50" maxlength="50" value="'.$optionText[$i].'"></td></tr>';
		}
		echo '</table>';
		Close_Table();
	}

	function AddCategory () 
	{
		global $admin_file;
		include ('header.php');
		GraphicAdmin();
		Open_Table();
		echo '<center><font class="title"><b>'._CATEGORIESADMIN.'</b></font></center>';
		Close_Table();
		echo '<br />';
		Open_Table();
		echo '<center><font class="option"><b>'._CATEGORYADD.'</b></font><br /><br /><br />
		<form action="'.$admin_file.'.php" method="post">
		<b>'._CATNAME.':</b>
		<input type="text" name="cat_title" size="22" maxlength="20">
		<input type="hidden" name="op" value="SaveCategory">
		<input type="submit" value="'._SAVE.'">
		</form></center>';
		Close_Table();
		include('footer.php');
	}

	function EditCategory($catid) 
	{
		global $prefix, $db, $admin_file;
		$catid = intval($catid);
		$result = $db->sql_query("SELECT title FROM ".$prefix."_stories_cat WHERE catid='".$catid."'");
		list($title) = $db->sql_fetchrow($result);
		$title = filter($title, "nohtml");
		include ("header.php");
		GraphicAdmin();
		Open_Table();
		echo '<center><font class="title"><b>'._CATEGORIESADMIN.'</b></font></center>';
		Close_Table();
		echo '<br />';
		Open_Table();
		echo '<center><font class="option"><b>'._EDITCATEGORY.'</b></font><br />';
		if (!$catid) 
		{
			$selcat = $db->sql_query("SELECT catid, title from ".$prefix."_stories_cat");
			echo '<form action="'.$admin_file.'.php" method="post">
			      <b>'._ASELECTCATEGORY.'</b>
			      <select name="catid">
			      <option name="catid" value="0" '.$sel.'>Articles</option>';
			while(list($catid, $title) = $db->sql_fetchrow($selcat)) 
			{
				$catid = intval($catid);
				$title = filter($title, "nohtml");
				echo '<option name="catid" value="'.$catid.'" '.$sel.'>'.$title.'</option>';
			}
			echo '</select>
			      <input type="hidden" name="op" value="EditCategory">
			      <input type="submit" value="'._EDIT.'"><br /><br />'._NOARTCATEDIT;
		} 
		else 
		{
			echo '<form action="'.$admin_file.'.php" method="post">
			      <b>'._CATEGORYNAME.':</b>
			      <input type="text" name="title" size="22" maxlength="20" value="'.$title.'">
			      <input type="hidden" name="catid" value="'.$catid.'">
			      <input type="hidden" name="op" value="SaveEditCategory">
			      <input type="submit" value="'._SAVECHANGES.'"><br /><br />'._NOARTCATEDIT.'</form>';
		}
		echo '</center>';
		Close_Table();
		include("footer.php");
	}

	function DelCategory($cat) 
	{
		global $prefix, $db, $admin_file;
		$cat = intval($cat);
		$result = $db->sql_query("SELECT title FROM ".$prefix."_stories_cat WHERE catid='".$cat."'");
		list($title) = $db->sql_fetchrow($result);
		$title = filter($title, "nohtml");
		include ("header.php");
		GraphicAdmin();
		Open_Table();
		echo '<center><font class="title"><b>'._CATEGORIESADMIN.'</b></font></center>';
		Close_Table();
		echo "<br />";
		Open_Table();
		echo '<center><font class="option"><b>'._DELETECATEGORY.'</b></font><br />';
		if (!$cat) 
		{
			$selcat = $db->sql_query("SELECT catid, title from ".$prefix."_stories_cat");
			echo '<form action="'.$admin_file.'.php" method="post">
			<b>'._SELECTCATDEL.':&nbsp;</b>
			<select name="cat">';
			while(list($catid, $title) = $db->sql_fetchrow($selcat)) 
			{
				$catid = intval($catid);
				$title = filter($title, "nohtml");
				echo '<option name="cat" value="'.$catid.'">'.$title.'</option>';
			}
			echo '</select>
			      <input type="hidden" name="op" value="DelCategory">
			      <input type="submit" value="Delete">
			      </form>';
		} else {
			$result2 = $db->sql_query("SELECT * FROM ".$prefix."_stories WHERE catid='".$cat."'");
			$numrows = $db->sql_numrows($result2);
			if ($numrows == 0) 
			{
				$db->sql_query("DELETE FROM ".$prefix."_stories_cat WHERE catid='".$cat."'");
				echo '<br /><br />'._CATDELETED.'<br /><br />'._GOTOADMIN;
			} 
			else 
			{
				echo '<br /><br /><b>'._WARNING.':</b>&nbsp;'._THECATEGORY.'&nbsp;<b>'.$title.'</b>&nbsp;'._HAS.'&nbsp;<b>'.$numrows.'</b>&nbsp;'._STORIESINSIDE.'<br />
				'._DELCATWARNING1.'<br />
				'._DELCATWARNING2.'<br /><br />
				'._DELCATWARNING3.'<br /><br />
				<b>[&nbsp;<a href="'.$admin_file.'.php?op=YesDelCategory&amp;catid='.$cat.'">'._YESDEL.'</a>&nbsp;|&nbsp;
				<a href="'.$admin_file.'.php?op=NoMoveCategory&amp;catid='.$cat.'">'._NOMOVE.'</a>&nbsp;]</b>';
			}
		}
		echo '</center>';
		Close_Table();
		include("footer.php");
	}

	function YesDelCategory($catid) 
	{
		global $prefix, $db, $admin_file;
		$catid = intval($catid);
		$db->sql_query("DELETE FROM ".$prefix."_stories_cat WHERE catid='".$catid."'");
		$result = $db->sql_query("select sid from ".$prefix."_stories where catid='".$catid."'");
		while(list($sid) = $db->sql_fetchrow($result)) 
		{
			$sid = intval($sid);
			$db->sql_query("DELETE FROM ".$prefix."_stories WHERE catid='".$catid."'");
			$db->sql_query("DELETE FROM ".$prefix."_comments WHERE sid='".$sid."'");
		}
		Header("Location: ".$admin_file.".php");
	}

	function NoMoveCategory($catid, $newcat) 
	{
		global $prefix, $db, $admin_file;
		$catid = intval($catid);
		$newcat = filter($newcat, "nohtml", 1);
		$result = $db->sql_query("SELECT title from ".$prefix."_stories_cat WHERE catid='".$catid."'");
		list($title) = $db->sql_fetchrow($result);
		$title = filter($title, "nohtml");
		include ("header.php");
		GraphicAdmin();
		Open_Table();
		echo '<center><font class="title"><b>'._CATEGORIESADMIN.'</b></font></center>';
		Close_Table();
		echo "<br>";
		Open_Table();
		echo '<center><font class="option"><b>'._MOVESTORIES.'</b></font><br / ><br />';
		if (!$newcat) 
		{
			echo _ALLSTORIES.'&nbsp;<b>'.$title.'</b>&nbsp;'._WILLBEMOVED.'<br /><br />';
			$selcat = $db->sql_query("SELECT catid, title FROM ".$prefix."_stories_cat");
			echo '<form action="'.$admin_file.'.php" method="post">
			      <b>'._SELECTNEWCAT.':</b>&nbsp;
			      <select name="newcat">
			      <option name="newcat" value="0">'._ARTICLES.'</option>';
			while(list($newcat, $title) = $db->sql_fetchrow($selcat)) 
			{
				$title = filter($title, "nohtml");
				echo '<option name="newcat" value="'.$newcat.'">'.$title.'</option>';
			}
			echo '</select>
			<input type="hidden" name="catid" value="'.$catid.'">
			<input type="hidden" name="op" value="NoMoveCategory">
		    <input type="submit" value="'._OK.'">
			</form>';
		} 
		else 
		{
			$resultm = $db->sql_query("select sid from ".$prefix."_stories where catid='".$catid."'");
			while(list($sid) = $db->sql_fetchrow($resultm)) 
			{
				$sid = intval($sid);
				$db->sql_query("UPDATE ".$prefix."_stories SET catid='".$newcat."' WHERE sid='".$sid."'");
			}
			$db->sql_query("DELETE FROM ".$prefix."_stories_cat WHERE catid='".$catid."'");
			echo _MOVEDONE;
		}
		Close_Table();
		include("footer.php");
	}

	function SaveEditCategory($catid, $title) 
	{
		global $prefix, $db, $admin_file;
		$title = filter($title, "nohtml", 1);
		$result = $db->sql_query("SELECT catid FROM ".$prefix."_stories_cat WHERE title='".$title."'");
		$catid = intval($catid);
		$check = $db->sql_numrows($result);
		if ($check) 
		{
			$what1 = _CATEXISTS;
			$what2 = _GOBACK;
		} 
		else 
		{
			$what1 = _CATSAVED;
			$what2 = '[&nbsp;<a href="'.$admin_file.'.php">'._GOTOADMIN.'</a>&nbsp;]';
			$result = $db->sql_query("update ".$prefix."_stories_cat SET title='".$title."' WHERE catid='".$catid."'");
			if (!$result) 
			{
				return;
			}
		}
		include ('header.php');
		GraphicAdmin();
		Open_Table();
		echo '<center><font class="title"><b>'._CATEGORIESADMIN.'</b></font></center>';
		Close_Table();
		echo '<br />';
		Open_Table();
		echo '<center><font class="content"><b>'.$what1.'</b></font><br /><br />';
		echo $what2.'</center>';
		Close_Table();
		include('footer.php');
	}

	function SaveCategory($title) 
	{
		global $prefix, $db;
		$title = filter($title, "nohtml", 1);
		$result = $db->sql_query("SELECT catid FROM ".$prefix."_stories_cat WHERE title='".$title."'");
		$check = $db->sql_numrows($result);
		if ($check) 
		{
			$what1 = _CATEXISTS;
			$what2 = _GOBACK;
		} 
		else 
		{
			$what1 = _CATADDED;
			$what2 = _GOTOADMIN;
			$result = $db->sql_query("INSERT INTO ".$prefix."_stories_cat VALUES (NULL, '".$title."', '0')");
			if (!$result) 
			{
				return;
			}
		}
		include ('header.php');
		GraphicAdmin();
		Open_Table();
		echo '<center><font class="title"><b>'._CATEGORIESADMIN.'</b></font></center>';
		Close_Table();
		echo '<br />';
		Open_Table();
		echo '<center><font class="content"><b>'.$what1.'</b></font><br /><br />';
		echo $what2.'</center>';
		Close_Table();
		include('footer.php');
	}

	function autodelete($anid) 
	{
		global $prefix, $db, $admin_file;
		$anid = intval($anid);
		$db->sql_query("DELETE FROM ".$prefix."_autonews WHERE anid='".$anid."'");
		Header("Location: ".$admin_file.".php?op=adminMain");
	}

	function publish_now($anid) 
	{
		global $prefix, $db, $admin_file;
		$anid = intval($anid);
		$result2 = $db->sql_query("SELECT * FROM ".$prefix."_autonews WHERE anid='".$anid."'");
		while ($row2 = $db->sql_fetchrow($result2)) 
		{
			$title = $row2['title'];
			$hometext = filter($row2['hometext']);
			$bodytext = filter($row2['bodytext']);
			$notes = filter($row2['notes']);
			$catid2 = intval($row2['catid']);
			$aid2 = filter($row2['aid'], "nohtml");
			$aid3 = filter($row2['aid2'], "nohtml");
			$topic2 = intval($row2['topic']);
			$firstname2 = filter($row2['firstname'], 'nohtml');
			$lastname2 = filter($row2['lastname'], 'nohtml');
			$ihome2 = intval($row2['ihome']);
			$alanguage2 = $row2['alanguage'];
			$acomm2 = intval($row2['acomm']);
			$associated2 = $row2['associated'];
			// Prepare and filter variables to be saved
			$hometext = filter($hometext, '', 1);
			$bodytext = filter($bodytext, '', 1);
			$notes = filter($notes, '', 1);
			$aid2 = filter($aid2, 'nohtml', 1);
			$aid3 = filter($aid3, 'nohtml', 1);
			$firstname2 = filter($firstname2, 'nohtml', 1);
			$lastname2 = filter($lastname2, 'nohtml', 1);
			$db->sql_query("DELETE FROM ".$prefix."_autonews WHERE anid='".$anid."'");
			$db->sql_query("INSERT INTO ".$prefix."_stories VALUES (NULL, '".$catid2."', '".$aid2."', '".$aid3."', '".$title."', now(), '".$hometext."', '".$bodytext."', '0', '0', '".$topic2."', '".$firstname2."', '".$lastname2."', '".$notes."', '".$ihome2."', '".$alanguage2."', '".$acomm2."', '0', '0', '0', '0', '0', '".$associated2."')");
		}
		Header('Location: '.$admin_file.'.php?op=adminMain');
		die();
	}

	function autoEdit($anid) 
	{
		global $aid, $aid2, $bgcolor1, $bgcolor2, $prefix, $db, $multilingual, $admin_file;
		$sid = intval($sid);
		$aid = substr($aid, 0,31);
		$aid2 = substr($aid2, 0,31);
		$result = $db->sql_query("SELECT radminsuper FROM ".$prefix."_authors WHERE aid='".$aid."' AND aid2='".$aid2."'");
		list($radminsuper) = $db->sql_fetchrow($result);
		$radminsuper = intval($radminsuper);
		$result = $db->sql_query("SELECT admins FROM ".$prefix."_modules WHERE title='News'");
		$row2 = $db->sql_fetchrow($db->sql_query("SELECT name FROM ".$prefix."_authors WHERE aid='".$aid."' AND aid2='".$aid2."'"));
		while ($row = $db->sql_fetchrow($result)) 
		{
			$admins = explode(",", $row['admins']);
			$auth_user = 0;
			for ($i=0; $i < sizeof($admins); $i++) 
			{
				if ($row2['name'] == $admins[$i]) 
				{
					$auth_user = 1;
				}
			}
			if ($auth_user == 1) 
			{
				$radminarticle = 1;
			}
		}
		$result2 = $db->sql_query("SELECT aid, aid2 FROM ".$prefix."_stories WHERE sid='".$sid."'");
		list($aaid, $aaid2) = $db->sql_fetchrow($result2);
		$aaid = substr($aaid, 0,31);
		$aaid2 = substr($aaid, 0,31);
		if (($radminarticle == 1) && ($aaid == $aid) && ($aaid2 == $aid2) || ($radminsuper == 1)) 
		{
			include ("header.php");
			$result = $db->sql_query("select catid, aid, aid2, title, time, hometext, bodytext, topic, firstname, lastname, notes, ihome, alanguage, acomm from ".$prefix."_autonews WHERE anid='".$anid."'");
			list($catid, $aid, $aid2, $title, $time, $hometext, $bodytext, $topic, $firstname, $lastname, $notes, $ihome, $alanguage, $acomm) = $db->sql_fetchrow($result);
			$catid = intval($catid);
			$aid = substr($aid, 0,31);
			$aid2 = substr($aid2, 0,31);
			$firstname = substr($firstname, 0,31);
			$lastname = substr($lastname, 0,31);
			$ihome = intval($ihome);
			$acomm = intval($acomm);
			ereg ("([0-9]{4})-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})", $time, $datetime);
			GraphicAdmin();
			Open_Table();
			echo '<center><font class="title"><b>'._ARTICLEADMIN.'</b></font></center>';
			Close_Table();
			echo '<br />';
			Open_Table();
			$today = getdate();
			$tday = $today['mday'];
			if ($tday < 10)
			{
				$tday = '0'.$tday;
			}
			$tmonth = $today['month'];
			$tyear = $today['year'];
			$thour = $today['hours'];
			if ($thour < 10)
			{
				$thour = '0'.$thour;
			}
			$tmin = $today['minutes'];
			if ($tmin < 10)
			{
				$tmin = '0'.$tmin;
			}
			$tsec = $today['seconds'];
			if ($tsec < 10)
			{
				$tsec = '0'.$tsec;
			}
			$date = $tmonth.'&nbsp;'.$tday.',&nbsp;'.$tyear.'&nbsp;@&nbsp;'.$thour.':'.$tmin.':'.$tsec;
			echo '<center><font class="option"><b>'._AUTOSTORYEDIT.'</b></font></center><br /><br />
			      <form action="'.$admin_file.'.php" method="post">';
			$title = filter($title, "nohtml");
			$hometext = filter($hometext);
			$bodytext = filter($bodytext);
			$notes = filter($notes);
			$result = $db->sql_query("SELECT topicimage FROM ".$prefix."_topics WHERE topicid='".$topic."'");
			list($topicimage) = $db->sql_fetchrow($result);
			echo '<table border="0" width="75%" cellpadding="0" cellspacing="1" bgcolor="'.$bgcolor2.'" align="center"><tr><td>
			      <table border="0" width="100%" cellpadding="8" cellspacing="1" bgcolor="'.$bgcolor1.'"><tr><td>
			      <img src="images/topics/'.$topicimage.'" border="0" align="right">';
			themepreview($title, $hometext, $bodytext);
			echo '</td></tr></table></td></tr></table>
			<br /><br /><b>'._TITLE.'</b><br />
			<input type="text" name="title" size="50" value="'.$title.'"><br /><br />
			<b>'._TOPIC.'</b>&nbsp;<select name="topic">';
			$toplist = $db->sql_query("SELECT topicid, topictext FROM ".$prefix."_topics ORDER BY topictext");
			echo '<option value="">'._ALLTOPICS.'</option>';
			while(list($topicid, $topics) = $db->sql_fetchrow($toplist)) 
			{
				$topicid = intval($topicid);
				$topics = filter($topics, "nohtml");
				if ($topicid == $topic) { $sel = 'selected'; }
				echo '<option '.$sel.' value="'.$topicid.'">'.$topics.'</option>';
				$sel = '';
			}
			echo '</select><br /><br />';
			$cat = $catid;
			SelectCategory($cat);
			echo '<br />';
			set_home($ihome, $acomm);
			if ($multilingual == 1) 
			{
				echo '<br /><b>'._LANGUAGE.':&nbsp;</b><select name="alanguage">';
				$handle = opendir('language');
				while ($file = readdir($handle)) 
				{
					if (preg_match("/^lang\-(.+)\.php/", $file, $matches)) 
					{
						$langFound = $matches[1];
						$languageslist .= $langFound.'&bsp;';
					}
				}
				closedir($handle);
				$languageslist = explode(" ", $languageslist);
				sort($languageslist);
				for ($i=0; $i < sizeof($languageslist); $i++) 
				{
					if(!empty($languageslist[$i])) 
					{
						echo '<option value="'.$languageslist[$i].'&nbsp;';
						if($languageslist[$i] == $alanguage) echo 'selected';
						echo '>'.ucfirst($languageslist[$i]).'</option>';
					}
				}
				if (empty($alanguage)) 
				{
					$sellang = 'selected';
				} else {
					$sellang = '';
				}
				echo '<option value="" '.$sellang.'>'._ALL.'</option></select>';
			} 
			else 
			{
				echo '<input type="hidden" name="alanguage" value="">';
			}
			echo '<br /><br /><b>'._STORYTEXT.'</b><br />
			<textarea wrap="virtual" cols="100" rows="15" name="hometext">'.$hometext.'</textarea><br /><br />
			<b>'._EXTENDEDTEXT.'</b><br />
			<textarea wrap="virtual" cols="100" rows="15" name="bodytext">'.$bodytext.'</textarea><br />
			<font class="content">'._ARESUREURL.'</font><br /><br />';
			if ($aid != $firstname && $aid2 != $lastname) 
			{
				echo '<b>'._NOTES.'</b><br /><textarea wrap="virtual" cols="100" rows="10" name="notes">'.$notes.'</textarea><br /><br />';
			}
			echo '<br /><b>'._CHNGPROGRAMSTORY.'</b><br /><br />'._NOWIS.':&nbsp;'.$date.'<br /><br />';
			$xday = 1;
			echo _DAY.':&nbsp;<select name="day">';
			while ($xday <= 31) 
			{
				if ($xday == $datetime[3]) 
				{
					$sel = 'selected';
				} 
				else 
				{
					$sel = '';
				}
				echo '<option name="day" '.$sel.'>'.$xday.'</option>';
				$xday++;
			}
			echo '</select>';
			$xmonth = 1;
			echo _UMONTH.':&nbsp;<select name="month">';
			while ($xmonth <= 12) 
			{
				if ($xmonth == $datetime[2]) 
				{
					$sel = 'selected';
				} 
				else 
				{
					$sel = '';
				}
				echo '<option name="month" '.$sel.'>'.$xmonth.'</option>';
				$xmonth++;
			}
			echo '</select>';
			echo _YEAR.':&nbsp;<input type="text" name="year" value="'.$datetime[1].'" size="5" maxlength="4">';
			echo '<br />'._HOUR.':&nbsp;<select name="hour">';
			$xhour = 0;
			$cero = '0';
			while ($xhour <= 23) 
			{
				$dummy = $xhour;
				if ($xhour < 10) 
				{
					$xhour = $cero.''.$xhour;
				}
				if ($xhour == $datetime[4]) 
				{
					$sel = 'selected';
				} 
				else 
				{
					$sel = '';
				}
				echo '<option name="hour" '.$sel.'>'.$xhour.'</option>';
				$xhour = $dummy;
				$xhour++;
			}
			echo '</select>';
			echo ':&nbsp;<select name="min">';
			$xmin = 0;
			while ($xmin <= 59) 
			{
				if (($xmin == 0) || ($xmin == 5)) 
				{
					$xmin = '0'.$xmin;
				}
				if ($xmin == $datetime[5]) 
				{
					$sel = 'selected';
				} 
				else 
				{
					$sel = '';
				}
				echo '<option name="min" '.$sel.'>'.$xmin.'</option>';
				$xmin = $xmin + 5;
			}
			echo '</select>';
			echo ':&nbsp;00<br /><br />
                 <input type="hidden" name="anid" value="'.$anid.'">
                 <input type="hidden" name="op" value="autoSaveEdit">
                 <input type="submit" value="'._SAVECHANGES.'">
                 </form>';
			Close_Table();
			include ('footer.php');
		} 
		else 
		{
			include ('header.php');
			GraphicAdmin();
			Open_Table();
			echo '<center><font class="title"><b>'._ARTICLEADMIN.'</b></font></center>';
			Close_Table();
			echo '<br />';
			Open_Table();
			echo '<center><b>'._NOTAUTHORIZED1.'</b><br /><br />'._NOTAUTHORIZED2.'<br /><br />'._GOBACK;
			Close_Table();
			include("footer.php");
		}
	}

	function autoSaveEdit($anid, $year, $day, $month, $hour, $min, $title, $hometext, $bodytext, $topic, $notes, $catid, $ihome, $alanguage, $acomm) 
	{
		global $aid, $aid2, $prefix, $db, $admin_file;
		$aid = substr($aid, 0,31);
		$aid2 = substr($aid2, 0,31);
		$sid = intval($sid);
		$result = $db->sql_query("SELECT radminsuper from ".$prefix."_authors WHERE aid='".$aid."' AND aid2='".$aid2."'");
		list($radminsuper) = $db->sql_fetchrow($result);
		$radminsuper = intval($radminsuper);
		$result = $db->sql_query("SELECT admins FROM ".$prefix."_modules WHERE title='News'");
		$row2 = $db->sql_fetchrow($db->sql_query("SELECT name FROM ".$prefix."_authors WHERE aid='".$aid."' AND aid2='".$aid2."'"));
		while ($row = $db->sql_fetchrow($result)) 
		{
			$admins = explode(",", $row['admins']);
			$auth_user = 0;
			for ($i=0; $i < sizeof($admins); $i++) 
			{
			   if ($row2['name'] == $admins[$i]) 
			   {
					$auth_user = 1;
			   }
			}
			if ($auth_user == 1) 
			{
				$radminarticle = 1;
			}
		}
		$result2 = $db->sql_query("SELECT aid, aid2 FROM ".$prefix."_stories WHERE sid='$sid'");
		list($aaid, $aaid2) = $db->sql_fetchrow($result2);
		$aaid = substr($aaid, 0,31);
		$aaid2 = substr($aaid2, 0,31);
		if (($radminarticle == 1) AND ($aaid == $aid) AND ($aaid2 == $aid2) OR ($radminsuper == 1)) 
		{
			if ($day < 10) 
			{
				$day = '0'.$day;
			}
			if ($month < 10) 
			{
				$month = '0'.$month;
			}
			$sec = '00';
			$date = $year.'-'.$month.'-'.$day.'&nbsp;'.$hour.':'.$min.':'.$sec;
			$title = filter($title, 'nohtml', 1);
			$hometext = filter($hometext, '', 1);
			$bodytext = filter($bodytext, '', 1);
			$notes = filter($notes, '', 1);
			$result = $db->sql_query("UPDATE ".$prefix."_autonews SET catid='".$catid."', title='".$title."', time='".$date."', hometext='".$hometext."', bodytext='".$bodytext."', topic='".$topic."', notes='".$notes."', ihome='".$ihome."', alanguage='".$alanguage."', acomm='".$acomm."' WHERE anid='".$anid."'");
			if (!$result) 
			{
				exit();
			}
			Header('Location: '.$admin_file.'.php?op=adminMain');
			} else {
			include ('header.php');
			GraphicAdmin();
			Open_Table();
			echo '<center><font class="title"><b>'._ARTICLEADMIN.'</b></font></center>';
			Close_Table();
			echo '<br />';
			Open_Table();
			echo '<center><b>'._NOTAUTHORIZED1.'</b><br /><br />'._NOTAUTHORIZED2.'<br /><br />'._GOBACK;
			Close_Table();
			include("footer.php");
		}
	}

	function displayStory($qid) 
	{
		global $user, $subject, $story, $bgcolor1, $bgcolor2, $sl_firstname, $sl_lastname, $user_prefix, $prefix, $db, $multilingual, $admin_file;
		include ('header.php');
		GraphicAdmin();
		Open_Table();
		echo '<center><font class="title"><b>'._SUBMISSIONSADMIN.'</b></font></center>';
		Close_Table();
		echo '<br />';
		$today = getdate();
		$tday = $today['mday'];
		if ($tday < 10)
		{
			$tday = '0'.$tday;
		}
		$tmonth = $today['month'];
		$ttmon = $today['mon'];
		if ($ttmon < 10)
		{
			$ttmon = '0'.$ttmon;
		}
		$tyear = $today['year'];
		$thour = $today['hours'];
		if ($thour < 10)
		{
			$thour = '0'.$thour;
		}
		$tmin = $today['minutes'];
		if ($tmin < 10)
		{
			$tmin = '0'.$tmin;
		}
		$tsec = $today['seconds'];
		if ($tsec < 10)
		{
			$tsec = '0'.$tsec;
		}
		$nowdate = $tmonth.'&nbsp;'.$tday.',&nbsp;'.$tyear.'&nbsp;@&nbsp;'.$thour.':'.$tmin.':'.$tsec;
		$qid = intval($qid);
		$result = $db->sql_query("SELECT qid, uid, firstname, lastname, subject, story, storyext, topic, alanguage FROM ".$prefix."_queue where qid='".$qid."'");
		list($qid, $uid, $firstname, $lastname, $subject, $story, $storyext, $topic, $alanguage) = $db->sql_fetchrow($result);
		$qid = intval($qid);
		$uid = intval($uid);
		$topic = intval($topic);
		$firstname = filter($firstname, "nohtml");
		$lastname = filter($lastname, "nohtml");
		$subject = filter($subject, "nohtml");
		$story = filter($story);
		$storyext = filter($storyext);
		$story1 = redir($story);
		$storyext1 = redir($storyext);
		Open_Table();
		echo "<br>";
		if(empty($topic)) {
			$topic = 1;
		}
		$result = $db->sql_query("select topicimage from ".$prefix."_topics where topicid='".$topic."'");
		list($topicimage) = $db->sql_fetchrow($result);
		echo '<table border="0" width="70%" cellpadding="0" cellspacing="1" bgcolor="'.$bgcolor2.'" align="center"><tr><td>
			  <table border="0" width="100%" cellpadding="8" cellspacing="1" bgcolor="'.$bgcolor1.'"><tr><td>
			  <img src="images/topics/'.$topicimage.'" border="0" align="right" alt="">';
		$storypre = $story1.'<br /><br />'.$storyext1;
		$pre_subject = '<font class="title">'.$subject.'</font>';
		themepreview($pre_subject, $storypre);
		echo '</td></tr></table></td></tr></table><br /><br />';
		echo '<table width="100%" border="0" cellspacing="6">
			<tr><td><form action="'.$admin_file.'.php" method="post">
			<b>'._USERNEWS.':</b></td><td>
			<b>'.$firstname.'</b><input type="hidden" name="forename" value="'.$firstname.'">
			<b>'.$lastname.'</b><input type="hidden" name="surname" value="'.$lastname.'">';
		if ($firstname != $sl_firstname && $lastname != $sl_lastname) 
		{
			$res = $db->sql_query("select user_email from ".$user_prefix."_users WHERE firstname='$firstname' AND lastname='$lastname'");
			list($email) = $db->sql_fetchrow($res);
			$email = filter($email, "nohtml");
			echo '&nbsp;&nbsp;<font class="content">[&nbsp;<a href="mailto:'.$email.'?Subject=Re:&nbsp;'.$subject.'">'._EMAIL.'</a>&nbsp;|&nbsp;<a href="index.php?name=Your_Account&amp;op=userinfo&amp;firstname='.$firstname.'&amp;lastname='.$lastname.'">'._PROFILE.'</a> ]</font>';
		}
		echo '</td></tr><tr><td><b>'._TITLE.':</b></td><td><input type="text" name="subject" size="70" value="'.$subject.'"></td></tr>';
		echo '<tr><td><b>'._TOPIC.':</b></td><td><select name="topic">';
		$toplist = $db->sql_query("select topicid, topictext from ".$prefix."_topics order by topictext");
		echo '<option value="">'._SELECTTOPIC.'</option>';
		while(list($topicid, $topics) = $db->sql_fetchrow($toplist)) 
		{
			$topicid = intval($topicid);
			$topics = filter($topics, "nohtml");
			if ($topicid == $topic) 
			{
				$sel = 'selected';
			}
			echo '<option '.$sel.' value="'.$topicid.'">'.$topics.'</option>';
			$sel = '';
		}
		echo '</select></td></tr><tr><td><b>'._ASSOTOPIC.':</b></td><td>';
		$sql = "SELECT topicid, topictext FROM ".$prefix."_topics ORDER BY topictext";
		$result = $db->sql_query($sql);
		echo '<select multiple name="assotop[]" size="3">';
		while ($row = $db->sql_fetchrow($result)) {
			$row['topicid'] = intval($row['topicid']);
			$row['topictext'] = filter($row['topictext'], "nohtml");
			echo '<option value="'.$row['topicid'].'">'.$row['topictext'].'</option>';
		}
		echo '</select></td></tr>';
		$selcat = $db->sql_query("SELECT catid, title FROM ".$prefix."_stories_cat ORDER BY title");
		$a = 1;
		echo '<tr><td><b>'._CATEGORY.':</b></td><td><select name="catid">';
		if ($cat == 0) {
			$sel = "selected";
		} else {
			$sel = "";
		}
		echo '<option name="catid" value="0" '.$sel.'>'._ARTICLES.'</option>';
		while(list($catid, $title) = $db->sql_fetchrow($selcat)) 
		{
			$catid = intval($catid);
			$title = filter($title, "nohtml");
			if ($catid == $cat) 
			{
				$sel = 'selected';
			} 
			else 
			{
				$sel = '';
			}
			echo '<option name="catid" value="'.$catid.'" '.$sel.'>'.$title.'</option>';
			$a++;
		}
		echo '</select>&nbsp;&nbsp;&nbsp;<a href="'.$admin_file.'.php?op=AddCategory"><img src="images/add.gif" alt="'._ADD.'" title="'._ADD.'" border="0" width="17" height=\"17\"></a>&nbsp;&nbsp;<a href="'.$admin_file.'.php?op=EditCategory"><img src="images/edit.gif" alt="'._EDIT.'" title="'._EDIT.'" border="0" width="17" height="17"></a>&nbsp;&nbsp;<a href="'.$admin_file.'.php?op=DelCategory"><img src="images/delete.gif" alt="'._DELETE.'" title="'._DELETE.'" border="0" width="17" height="17"></a>';
		echo "</td></tr>";
		echo "<tr><td><b>"._PUBLISHINHOME."</b></td><td>";
		if (($ihome == 0) OR (empty($ihome))) {
			$sel1 = "checked";
			$sel2 = "";
		}
		if ($ihome == 1) {
			$sel1 = "";
			$sel2 = "checked";
		}
		echo "<input type=\"radio\" name=\"ihome\" value=\"0\" $sel1>"._YES."&nbsp;"
			."<input type=\"radio\" name=\"ihome\" value=\"1\" $sel2>"._NO.""
			."&nbsp;&nbsp;<font class=\"content\">[ "._ONLYIFCATSELECTED." ]</font></td></tr>"
			."<tr><td><b>"._ACTIVATECOMMENTS."</b></td><td>";
		if (($acomm == 0) OR (empty($acomm))) {
			$sel1 = "checked";
			$sel2 = "";
		}
		if ($acomm == 1) {
			$sel1 = "";
			$sel2 = "checked";
		}
		echo "<input type=\"radio\" name=\"acomm\" value=\"0\" $sel1>"._YES."&nbsp;"
			."<input type=\"radio\" name=\"acomm\" value=\"1\" $sel2>"._NO."</font></td></tr>";
		if ($multilingual == 1) {
			echo "<tr><td><b>"._LANGUAGE.":</b></td><td>"
				."<select name=\"alanguage\">";
			$handle=opendir('language');
			while ($file = readdir($handle)) {
				if (preg_match("/^lang\-(.+)\.php/", $file, $matches)) {
					$langFound = $matches[1];
					$languageslist .= "$langFound ";
				}
			}
			closedir($handle);
			$languageslist = explode(" ", $languageslist);
			sort($languageslist);
			for ($i=0; $i < sizeof($languageslist); $i++) {
				if(!empty($languageslist[$i])) {
					echo "<option value=\"$languageslist[$i]\" ";
					if($languageslist[$i]==$alanguage) echo "selected";
					echo ">".ucfirst($languageslist[$i])."</option>\n";
				}
			}
			if (empty($alanguage)) {
				$sellang = "selected";
			} else {
				$sellang = "";
			}
			echo "<option value=\"\" $sellang>"._ALL."</option></select></td></tr>";
		} else {
			echo "<input type=\"hidden\" name=\"alanguage\" value=\"\"></td></tr>";
		}
		echo "<tr><td><b>"._STORYTEXT.":</b></td><td>"
			."<textarea wrap=\"virtual\" cols=\"70\" rows=\"15\" name=\"hometext\">$story</textarea></td></tr>"
			."<tr><td><b>"._EXTENDEDTEXT.":</b></td><td>"
			."<textarea wrap=\"virtual\" cols=\"70\" rows=\"15\" name=\"bodytext\">$storyext</textarea><br>"
			."<font class=\"content\">"._AREYOUSURE."</font></td></tr>"
			."<tr><td><b>"._NOTES.":</b></td><td>"
			."<textarea wrap=\"virtual\" cols=\"70\" rows=\"10\" name=\"notes\"></textarea></td></tr>"
			."<tr><td colspan=\"2\"><hr noshade size=\"1\"></td></tr>"
			."<tr><td><input type=\"hidden\" NAME=\"qid\" size=\"50\" value=\"$qid\">"
			."<input type=\"hidden\" NAME=\"uid\" size=\"50\" value=\"$uid\">"
			."<b>"._SCHEDULENEWS.":</b></td><td>"
			."<input type=\"radio\" name=\"automated\" value=\"1\">"._YES." &nbsp;&nbsp;"
			."<input type=\"radio\" name=\"automated\" value=\"0\" checked>"._NO."</td></tr>";
		$day = 1;
		echo "<tr><td><b>"._PUBLISHON.":</b></td><td>";
		echo ""._DAY.": <select name=\"day\">";
		while ($day <= 31) {
			if ($tday==$day) {
				$sel = "selected";
			} else {
				$sel = "";
			}
			echo "<option name=\"day\" $sel>$day</option>";
			$day++;
		}
		echo "</select>";
		$month = 1;
		echo " "._UMONTH.": <select name=\"month\">";
		while ($month <= 12) {
			if ($ttmon==$month) {
				$sel = "selected";
			} else {
				$sel = "";
			}
			echo "<option name=\"month\" $sel>$month</option>";
			$month++;
		}
		echo "</select>";
		$date = getdate();
		$year = $date['year'];
		echo " "._YEAR.": <input type=\"text\" name=\"year\" value=\"$year\" size=\"5\" maxlength=\"4\">";
		echo " <b>@</b> "._HOUR.": <select name=\"hour\">";
		$hour = 0;
		$cero = "0";
		while ($hour <= 23) {
			$dummy = $hour;
			if ($hour < 10) {
				$hour = "$cero$hour";
			}
			echo "<option name=\"hour\">$hour</option>";
			$hour = $dummy;
			$hour++;
		}
		echo "</select>";
		echo " : <select name=\"min\">";
		$min = 0;
		while ($min <= 59) {
			if (($min == 0) OR ($min == 5)) {
				$min = "0$min";
			}
			echo "<option name=\"min\">$min</option>";
			$min = $min + 5;
		}
		echo '</select>&nbsp;:&nbsp;00</td></tr>
			  <tr><td>&nbsp;</td><td>'._NOWIS.':'.$nowdate.'</td></tr>
			  <tr><td colspan="2"><hr noshade size="1"></td></tr>
			  <tr><td>&nbsp;</td><td><select name="op">
			  <option value="DeleteStory">'._DELETESTORY.'</option>
			  <option value="PreviewAgain" selected>'._PREVIEWSTORY.'</option>
			  <option value="PostStory">'._POSTSTORY.'</option>
			  </select>
			  &nbsp;&nbsp;<input type="submit" value="'._OK.'">&nbsp;&nbsp;<b>[&nbsp;<a href="'.$admin_file.'.php?op=DeleteStory&qid='.$qid.'">'._DELETE.'</a>&nbsp;]</b>
			  </td></tr></table>';
		Close_Table();
		echo '<br />';
		putpoll($pollTitle, $optionText);
		echo '</form>';
		include ('footer.php');
	}

	function previewStory($automated, $year, $day, $month, $hour, $min, $qid, $uid, $forename, $surname, $subject, $hometext, $bodytext, $topic, $notes, $catid, $ihome, $alanguage, $acomm, $pollTitle, $optionText, $assotop) 
	{
		global $user, $boxstuff, $sl_firstname, $sl_lastname, $bgcolor1, $bgcolor2, $user_prefix, $prefix, $db, $multilingual, $admin_file;
		include ('header.php');
		GraphicAdmin();
		Open_Table();
		echo '<center><font class="title"><b>'._ARTICLEADMIN.'</b></font></center>';
		Close_Table();
		echo '<br />';
		$today = getdate();
		$tday = $today['mday'];
		if ($tday < 10)
		{
			$tday = '0'.$tday;
		}
		$tmonth = $today['month'];
		$tyear = $today['year'];
		$thour = $today['hours'];
		if ($thour < 10)
		{
			$thour = '0'.$thour;
		}
		$tmin = $today['minutes'];
		if ($tmin < 10)
		{
			$tmin = '0'.$tmin;
		}
		$tsec = $today['seconds'];
		if ($tsec < 10)
		{
			$tsec = '0'.$tsec;
		}
		$nowdate = $tmonth.'&nbsp;'.$tday.',&nbsp;'.$tyear.'&nbsp;@&nbsp;'.$thour.':'.$tmin.':'.$tsec;
		$subject = filter($subject, "nohtml", 0, preview);
		$hometext = filter($hometext);
		$bodytext = filter($bodytext);
		$hometext1 = redir($hometext);
		$bodytext1 = redir($bodytext);
		$notes = filter($notes);
		Open_Table();
		echo '<br />';
		$result = $db->sql_query("select topicimage from ".$prefix."_topics where topicid='$topic'");
		list($topicimage) = $db->sql_fetchrow($result);
		echo '<table width="70%" bgcolor="'.$bgcolor2.'" cellpadding="0" cellspacing="1" border="0"align="center"><tr><td>
			<table width="100%" bgcolor="'.$bgcolor1.'" cellpadding="8" cellspacing="1" border="0"><tr><td>
			<img src="images/topics/'.$topicimage.'" border="0" align="right">';
		$pre_subject = '<font class="title">'.$subject.'</font>';
		themepreview($pre_subject, $hometext1, $bodytext1, $notes);
		echo '</td></tr></table></td></tr></table><br /><br />';
		echo '<br><table border="0" width="100%" cellspacing="6">
		      <tr><td>
			  <form action="'.$admin_file.'.php" method="post">
			  <b>'._USERNEWS.':</b></td><td>
			  <b>'.$forename.'</b><input type="hidden" name="forename" value="'.$forename.'">
			  <b>'.$surname.'</b><input type="hidden" name="surname" value="'.$surname.'">';
		if ($forename != $sl_firstname && $surname != $sl_lastname) 
		{
			$res = $db->sql_query("SELECT user_id, user_email from ".$user_prefix."_users WHERE firstname='$forename' AND lastname='$surname'");
			list($pm_userid, $email) = $db->sql_fetchrow($res);
			$pm_userid = intval($pm_userid);
			echo '&nbsp;&nbsp;<font class="content">[&nbsp;<a href="mailto:'.$email.'?Subject=Re:&nbsp;'.$subject.'">'._USER.'</a>&nbsp;|&nbsp;<a href="index.php?name=Your_Account&amp;op=userinfo&amp;firstname='.$forename.'&amp;lastname='.$surname.'">'._PROFILE.'</a>&nbsp;]</font>';
		}
		echo '</td></tr><tr><td><b>'._TITLE.':</b></td><td>
			<input type="text" name="subject" size="70" value="'.$subject.'"></td></tr>
			<tr><td><b>'._TOPIC.':</b></td><td><select name="topic">';
		$toplist = $db->sql_query("SELECT topicid, topictext FROM ".$prefix."_topics ORDER BY topictext");
		echo '<option value="">'._ALLTOPICS.'</option>';
		while(list($topicid, $topics) = $db->sql_fetchrow($toplist)) 
		{
			$topicid = intval($topicid);
			$topics = filter($topics, "nohtml");
			if ($topicid == $topic) 
			{
				$sel = 'selected';
			}
			echo '<option '.$sel.' value="'.$topicid.'">'.$topics.'</option>';
			$sel = '';
		}
		echo '</select></td></tr>';
		for ($i=0; $i<sizeof($assotop); $i++) 
		{
			$associated .= $assotop[$i].'-';
		}
		$asso_t = explode('-', $associated);
		echo '<tr><td><b>'._ASSOTOPIC.':</b></td><td>';
		$sql = "SELECT topicid, topictext FROM ".$prefix."_topics ORDER BY topictext";
		$result = $db->sql_query($sql);
		echo '<select multiple name="assotop[]" size="3">';
		while ($row = $db->sql_fetchrow($result)) 
		{
			for ($i=0; $i<sizeof($asso_t); $i++) 
			{
				if ($asso_t[$i] == $row['topicid']) 
				{
					$checked = 'selected';
					break;
				}
			}
			$row['topicid'] = intval($row['topicid']);
			$row['topictext'] = filter($row['topictext'], "nohtml");
			echo '<option value="'.$row['topicid'].'" '.$checked.'>'.$row['topictext'].'</option>';
			$checked = '';
		}
		echo '</select></td></tr>';
		$cat = $catid;
		$selcat = $db->sql_query("select catid, title from ".$prefix."_stories_cat order by title");
		$a = 1;
		echo '<tr><td><b>'._CATEGORY.':</b></td><td><select name="catid">';
		if ($cat == 0) 
		{
			$sel = 'selected';
		} 
		else 
		{
			$sel = '';
		}
		echo '<option name="catid" value="0" '.$sel.'>'._ARTICLES.'</option>';
		while(list($catid, $title) = $db->sql_fetchrow($selcat)) 
		{
			$catid = intval($catid);
			$title = filter($title, "nohtml");
			if ($catid == $cat) 
			{
				$sel = 'selected';
			} 
			else 
			{
				$sel = '';
			}
			echo '<option name="catid" value="'.$catid.'" '.$sel.'>'.$title.'</option>';
			$a++;
		}
		echo '</select>&nbsp;&nbsp;<a href="'.$admin_file.'.php?op=AddCategory"><img src="images/add.gif" alt="'._ADD.'" title="'._ADD.'" border="0" width="17" height="17"></a>&nbsp;&nbsp;<a href="'.$admin_file.'.php?op=EditCategory"><img src="images/edit.gif" alt="'._EDIT.'" title="'._EDIT.'" border="0" width="17" height="17"></a>&nbsp;&nbsp;<a href="'.$admin_file.'.php?op=DelCategory"><img src="images/delete.gif" alt="'._DELETE.'" title="'._DELETE.'" border="0" width="17" height="17"></a></td></tr>
		      <tr><td><b>'._PUBLISHINHOME.'</b></td><td>';
		if (($ihome == 0) || (empty($ihome))) 
		{
			$sel1 = 'checked';
			$sel2 = '';
		}
		if ($ihome == 1) 
		{
			$sel1 = '';
			$sel2 = 'checked';
		}
		echo '<input type="radio" name="ihome" value="0" '.$sel1.'>'._YES.'&nbsp;
			  <input type="radio" name="ihome" value="1" '.$sel2.'>'._NO.'
			  &nbsp;&nbsp;<font class="content">[&nbsp;'._ONLYIFCATSELECTED.'&nbsp;]</font></td></tr>
		      <tr><td><b>'._ACTIVATECOMMENTS.'</b></td><td>';
		if (($acomm == 0) || (empty($acomm))) 
		{
			$sel1 = 'checked';
			$sel2 = '';
		}
		if ($acomm == 1) 
		{
			$sel1 = '';
			$sel2 = 'checked';
		}
		echo '<input type="radio" name="acomm" value="0" '.$sel1.'>'._YES.'&nbsp;
			  <input type="radio" name="acomm" value="1" '.$sel2.'>'._NO.'</font></td></tr>';
		if ($multilingual == 1) 
		{
			echo '<tr><td><b>'._LANGUAGE.':</b></td><td><select name="alanguage">';
			$handle=opendir('language');
			while ($file = readdir($handle)) 
			{
				if (preg_match("/^lang\-(.+)\.php/", $file, $matches)) 
				{
					$langFound = $matches[1];
					$languageslist .= $langFound.'&nbsp;';
				}
			}
			closedir($handle);
			$languageslist = explode(" ", $languageslist);
			sort($languageslist);
			for ($i=0; $i < sizeof($languageslist); $i++) 
			{
				if(!empty($languageslist[$i])) 
				{
					echo '<option value="'.$languageslist[$i].'&nbsp;';
					if($languageslist[$i] == $alanguage) echo 'selected';
					echo '>'.ucfirst($languageslist[$i]).'</option>';
				}
			}
			if (empty($alanguage)) 
			{
				$sellang = 'selected';
			} 
			else 
			{
				$sellang = '';
			}
			echo '<option value="" '.$sellang.'>'._ALL.'</option></select></tr><td>';
		} 
		else 
		{
			echo '<input type="hidden" name="alanguage" value="'.$language.'"><tr><td>';
		}
		echo '<tr><td><b>'._STORYTEXT.':</b></td><td>
			  <textarea wrap="virtual" cols="70" rows="15" name="hometext">'.$hometext.'</textarea></td></tr>
			  <tr><td><b>'._EXTENDEDTEXT.':</b></td><td>
			  <textarea wrap="virtual" cols="70" rows="15" name="bodytext">'.$bodytext.'</textarea><br />
			  <font class="content">'._AREYOUSURE.'</font></td></tr>
			  <tr><td><b>'._NOTES.':</b></td><td>
			  <textarea wrap="virtual" cols="70" rows="10" name="notes">'.$notes.'</textarea></td></tr>
			  <tr><td colspan="2"><hr noshade size="1"></td></tr>
			  <tr><td><input type="hidden" NAME="qid" size="50" value="'.$qid.'">
			  <input type="hidden" NAME="uid" size="50" value="'.$uid.'">';
		if ($automated == 1) {
			$sel1 = "checked";
			$sel2 = "";
		} else {
			$sel1 = "";
			$sel2 = "checked";
		}
		echo '<b>'._SCHEDULENEWS.':</b></td><td>
			<input type="radio" name="automated" value="1" '.$sel1.'>'._YES.'&nbsp;&nbsp;
			<input type="radio" name="automated" value="0" '.$sel2.'>'._NO.'</td></tr>';
		$xday = 1;
		echo '<tr><td><b>'._PUBLISHON.':</b></td><td>'._DAY.':&nbsp;<select name="day">';
		while ($xday <= 31) 
		{
			if ($xday == $day) 
			{
				$sel = 'selected';
			} 
			else 
			{
				$sel = '';
			}
			echo '<option name="day" '.$sel.'>'.$xday.'</option>';
			$xday++;
		}
		echo '</select>';
		$xmonth = 1;
		echo '&nbsp;'._UMONTH.':&nbsp;<select name="month">';
		while ($xmonth <= 12) 
		{
			if ($xmonth == $month) 
			{
				$sel = 'selected';
			} 
			else 
			{
				$sel = '';
			}
			echo '<option name="month" '.$sel.'>'.$xmonth.'</option>';
			$xmonth++;
		}
		echo '</select>';
		echo '&nbsp;'._YEAR.':&nbsp;<input type="text" name="year" value="'.$year.'" size="5" maxlength="4">';
		echo '&nbsp;<b>@</b>&nbsp;'._HOUR.':&nbsp;<select name="hour">';
		$xhour = 0;
		$cero = '0';
		while ($xhour <= 23) 
		{
			$dummy = $xhour;
			if ($xhour < 10) 
			{
				$xhour = $cero.''.$xhour;
			}
			if ($xhour == $hour) 
			{
				$sel = 'selected';
			} 
			else 
			{
				$sel = '';
			}
			echo '<option name="hour" '.$sel.'>'.$xhour.'</option>';
			$xhour = $dummy;
			$xhour++;
		}
		echo '</select>&nbsp;:&nbsp;<select name="min">';
		$xmin = 0;
		while ($xmin <= 59) 
		{
			if (($xmin == 0) || ($xmin == 5)) 
			{
				$xmin = '0'.$xmin;
			}
			if ($xmin == $min) 
			{
				$sel = 'selected';
			} 
			else 
			{
				$sel = '';
			}
			echo '<option name="min" '.$sel.'>'.$xmin.'</option>';
			$xmin = $xmin + 5;
		}
		echo "</select>";
		echo '&nbsp;:&nbsp;00</td></tr>
			 <tr><td>&nbsp;</td><td>'._NOWIS.':&nbsp;'.$nowdate.'</td></tr>
			 <tr><td colspan="2"><hr noshade size="1"></td></tr>
			 <tr><td>&nbsp;</td><td><select name="op">
			 <option value="DeleteStory">'._DELETESTORY.'</option>
			 <option value="PreviewAgain" selected>'._PREVIEWSTORY.'</option>
			 <option value="PostStory">'._POSTSTORY.'</option>
			 </select>
			 &nbsp;&nbsp;<input type="submit" value="'._OK.'">&nbsp;&nbsp;<b>[&nbsp;<a href="'.$admin_file.'.php?op=DeleteStory&amp;qid='.$qid.'">'._DELETE.'</a>&nbsp;]</b>
			 </td></tr></table>';
		Close_Table();
		echo '<br />';
		putpoll($pollTitle, $optionText);
		echo '</form>';
		include ('footer.php');
	}

	function postStory($automated, $year, $day, $month, $hour, $min, $qid, $uid, $forename, $surname, $subject, $hometext, $bodytext, $topic, $notes, $catid, $ihome, $alanguage, $acomm, $pollTitle, $optionText, $assotop) 
	{
		global $aid, $aid2, $prefix, $db, $user_prefix, $admin_file;
		for ($i=0; $i<sizeof($assotop); $i++) 
		{
			$associated .= $assotop[$i].'-';
		}
		if ($automated == 1) 
		{
			if ($day < 10) 
			{
				$day = '0'.$day;
			}
			if ($month < 10) 
			{
				$month = '0'.$month;
			}
			$sec = '00';
			$date = $year.'-'.$month.'-'.$day.'&nbsp'.$hour.':'.$min.':'.$sec;
			if ($uid == 1) 
			{
			   $forename = '';
			   $surname = '';
			}
			if ($hometext == $bodytext) 
			{
			   $bodytext = '';
			}
			$subject = filter($subject, 'nohtml', 1);
			$hometext = filter($hometext, '', 1);
			$bodytext = filter($bodytext, '', 1);
			$notes = filter($notes, '', 1);
			$result = $db->sql_query("INSERT INTO ".$prefix."_autonews VALUES (NULL, '".$catid."', '".$aid."', '".$aid2."', '".$subject."', '".$date."', '".$hometext."', '".$bodytext."', '".$topic."', '".$forename."', '".$surname."', '".$notes."', '".$ihome."', '".$alanguage."', '".$acomm."', '".$associated."')");
			if (!$result) 
			{
				return;
			}
			if ($uid != 1) 
			{
				$db->sql_query("UPDATE ".$user_prefix."_users set counter=counter+1 where user_id='".$uid."'");
				$row = $db->sql_fetchrow($db->sql_query("SELECT points FROM ".$prefix."_groups_points WHERE id='4'"));
				$db->sql_query("UPDATE ".$user_prefix."_users SET points=points+".intval($row['points'])." where user_id='".$uid."'");
			}
			$db->sql_query("UPDATE ".$prefix."_authors set counter=counter+1 WHERE aid='".$aid."' AND aid2='".$aid2."'");
			$qid = intval($qid);
			$db->sql_query("DELETE FROM ".$prefix."_queue where qid='".$qid."'");
			Header("Location: ".$admin_file.".php?op=submissions");
		} 
		else 
		{
			if ($uid == 1) 
			{
			   $forename = '';
			   $surname = '';
			}
			if ($hometext == $bodytext) 
			{
			   $bodytext = '';
			}
			$subject = filter($subject, 'nohtml', 1);
			$hometext = filter($hometext, '', 1);
			$bodytext = filter($bodytext, '', 1);
			$notes = filter($notes, '', 1);
			if ((!empty($pollTitle)) AND (!empty($optionText[1])) AND (!empty($optionText[2]))) {
				$haspoll = 1;
				$timeStamp = time();
				$pollTitle = filter($pollTitle, "nohtml", 1);
				if(!$db->sql_query("INSERT INTO ".$prefix."_poll_desc VALUES (NULL, '".$pollTitle."', '".$timeStamp."', '0', '".$alanguage."', '0', '0')")) 
				{
					return;
				}
				$object = $db->sql_fetchrow($db->sql_query("SELECT pollID FROM ".$prefix."_poll_desc WHERE pollTitle='".$pollTitle."'"));
				$id = $object['pollID'];
				$id = intval($id);
				for($i = 1; $i <= sizeof($optionText); $i++) 
				{
					if($optionText[$i] != '') 
					{
						$optionText[$i] = filter($optionText[$i], 'nohtml', 1);
					}
					if(!$db->sql_query("INSERT INTO ".$prefix."_poll_data (pollID, optionText, optionCount, voteID) VALUES ('".$id."', '".$optionText[$i]."', '0', '".$i."')")) 
					{
						return;
					}
				}
			} 
			else 
			{
				$haspoll = 0;
				$id = 0;
			}
			$result = $db->sql_query("insert into ".$prefix."_stories values (NULL, '".$catid."', '".$aid."', '".$aid2."', '".$subject."', now(), '".$hometext."', '".$bodytext."', '0', '0', '".$topic."', '".$forename."', '".$surname."', '".$notes."', '".$ihome."', '".$alanguage."', '".$acomm."', '".$haspoll."', '".$id."', '0', '0', '0', '".$associated."')");
			$result = $db->sql_query("select sid from ".$prefix."_stories WHERE title='".$subject."' ORDER BY time DESC LIMIT 0,1");
			list($artid) = $db->sql_fetchrow($result);
			$artid = intval($artid);
			$db->sql_query("UPDATE ".$prefix."_poll_desc SET artid='".$artid."' WHERE pollID='".$id."'");
			if (!$result) {
				return;
			}
			if ($uid != 1) 
			{
				$row = $db->sql_fetchrow($db->sql_query("SELECT points FROM ".$prefix."_groups_points WHERE id='4'"));
				$db->sql_query("UPDATE ".$user_prefix."_users SET points=points+".intval($row['points'])." WHERE user_id='".$uid."'");
				$db->sql_query("update ".$user_prefix."_users set counter=counter+1 where user_id='".$uid."'");
			}
			$db->sql_query("update ".$prefix."_authors set counter=counter+1 where aid='".$aid."' AND aid2='".$aid2."'");
			deleteStory($qid);
		}
	}

	function editStory($sid) 
	{
		global $user, $bgcolor1, $bgcolor2, $aid, $aid2, $prefix, $db, $multilingual, $admin_file;
		$aid = substr($aid, 0,31);
		$aid2 = substr($aid2, 0,31);
		$result = $db->sql_query("select radminsuper from ".$prefix."_authors where aid='$aid' AND aid2='$aid2'");
		list($radminsuper) = $db->sql_fetchrow($result);
		$radminsuper = intval($radminsuper);
		$result = $db->sql_query("SELECT admins FROM ".$prefix."_modules WHERE title='News'");
		$row2 = $db->sql_fetchrow($db->sql_query("SELECT name FROM ".$prefix."_authors WHERE aid='$aid' AND aid2='$aid2'"));
		while ($row = $db->sql_fetchrow($result)) {
			$admins = explode(",", $row['admins']);
			$auth_user = 0;
			for ($i=0; $i < sizeof($admins); $i++) {
			if ($row2['name'] == $admins[$i]) {
					$auth_user = 1;
				}
			}
			if ($auth_user == 1) {
				$radminarticle = 1;
			}
		}
		$result2 = $db->sql_query("SELECT aid, aid2 FROM ".$prefix."_stories WHERE sid='$sid'");
		list($aaid, $aaid2) = $db->sql_fetchrow($result2);
		$aaid = substr($aaid, 0,31);
		$aaid2 = substr($aaid2, 0,31);
		if (($radminarticle == 1) && ($aaid == $aid) && ($aaid2 == $aid2) || ($radminsuper == 1)) 
		{
			include ('header.php');
			GraphicAdmin();
			Open_Table();
			echo '<center><font class="title"><b>'._ARTICLEADMIN.'</b></font></center>';
			Close_Table();
			echo '<br />';
			$result = $db->sql_query("SELECT catid, title, hometext, bodytext, topic, notes, ihome, alanguage, acomm FROM ".$prefix."_stories where sid='$sid'");
			list($catid, $subject, $hometext, $bodytext, $topic, $notes, $ihome, $alanguage, $acomm) = $db->sql_fetchrow($result);
			$catid = intval($catid);
			$topic = intval($topic);
			$subject = filter($subject, 'nohtml');
			$hometext = filter($hometext);
			$bodytext = filter($bodytext);
			$notes = filter($notes);
			$ihome = intval($ihome);
			$acomm = intval($acomm);
			$result2=$db->sql_query("select topicimage from ".$prefix."_topics where topicid='".$topic."'");
			list($topicimage) = $db->sql_fetchrow($result2);
			Open_Table();
			echo '<center><font class="option"><b>'._EDITARTICLE.'</b></font></center><br />
			<table width="80%" border="0" cellpadding="0" cellspacing="1" bgcolor="'.$bgcolor2.'" align="center"><tr><td>
			<table width="100%" border="0" cellpadding="8" cellspacing="1" bgcolor="'.$bgcolor1.'"><tr><td>
			<img src="images/topics/'.$topicimage.'" border="0" align="right">';
			themepreview($subject, $hometext, $bodytext, $notes);
			echo '</td></tr></table></td></tr></table><br /><br />
			      <form action="'.$admin_file.'.php" method="post">
			      <b>'._TITLE.'</b><br />
			      <input type="text" name="subject" size="50" value="'.$subject.'"><br /><br />
			      <b>'._TOPIC.'</b>&nbsp;<select name="topic">';
			$toplist = $db->sql_query("SELECT topicid, topictext FROM ".$prefix."_topics ORDER BY topictext");
			echo "<option value=\"\">"._ALLTOPICS."</option>\n";
			while(list($topicid, $topics) = $db->sql_fetchrow($toplist)) {
				$topicid = intval($topicid);
				$topics = filter($topics, "nohtml");
				if ($topicid==$topic) { $sel = "selected "; }
				echo "<option $sel value=\"$topicid\">$topics</option>\n";
				$sel = "";
			}
			echo "</select>";
			echo "<br><br>";
			$asql = "SELECT associated FROM ".$prefix."_stories WHERE sid='$sid'";
			$aresult = $db->sql_query($asql);
			$arow = $db->sql_fetchrow($aresult);
			$asso_t = explode("-", $arow[associated]);
			echo "<table border='0' width='100%' cellspacing='0'><tr><td width='20%'><b>"._ASSOTOPIC."</b></td><td width='100%'>"
			."<table border='0' cellspacing='0' cellpadding='0'><tr>";
			$sql = "SELECT topicid, topictext FROM ".$prefix."_topics ORDER BY topictext";
			$result = $db->sql_query($sql);
			echo "<td><select multiple name=\"assotop[]\" size=\"5\">";
			while ($row = $db->sql_fetchrow($result)) {
				for ($i=0; $i<sizeof($asso_t); $i++) {
					if ($asso_t[$i] == $row['topicid']) {
						$checked = "selected";
						break;
					}
				}
				$row['topicid'] = intval($row['topicid']);
				$row['topictext'] = filter($row['topictext'], "nohtml");
				echo "<option value='".$row['topicid']."' $checked>".$row['topictext']."</option>";
				$checked = "";
			}
			echo "</select></td>";
			echo "</tr></table></td></tr></table><br><br>";
			$cat = $catid;
			SelectCategory($cat);
			echo "<br>";
			set_home($ihome, $acomm);
			if ($multilingual == 1) {
				echo "<br><b>"._LANGUAGE.":</b>"
				."<select name=\"alanguage\">";
				$handle=opendir('language');
				while ($file = readdir($handle)) {
					if (preg_match("/^lang\-(.+)\.php/", $file, $matches)) {
						$langFound = $matches[1];
						$languageslist .= "$langFound ";
					}
				}
				closedir($handle);
				$languageslist = explode(" ", $languageslist);
				sort($languageslist);
				for ($i=0; $i < sizeof($languageslist); $i++) {
					if(!empty($languageslist[$i])) {
						echo "<option name=\"alanguage\" value=\"$languageslist[$i]\" ";
						if($languageslist[$i]==$alanguage) echo "selected";
						echo ">".ucfirst($languageslist[$i])."\n</option>";
					}
				}
				if (empty($alanguage)) {
					$sellang = "selected";
				} else {
					$sellang = "";
				}
				echo "<option value=\"\" $sellang>"._ALL."</option></select>";
			} else {
				echo "<input type=\"hidden\" name=\"alanguage\" value=\"\">";
			}
			echo "<br><br><b>"._STORYTEXT."</b><br>"
			."<textarea wrap=\"virtual\" cols=\"100\" rows=\"15\" name=\"hometext\">$hometext</textarea><br><br>"
			."<b>"._EXTENDEDTEXT."</b><br>"
			."<textarea wrap=\"virtual\" cols=\"100\" rows=\"15\" name=\"bodytext\">$bodytext</textarea><br>"
			."<font class=\"content\">"._AREYOUSURE."</font><br><br>"
			."<b>"._NOTES."</b><br>"
			."<textarea wrap=\"virtual\" cols=\"100\" rows=\"10\" name=\"notes\">$notes</textarea><br><br>"
			."<input type=\"hidden\" NAME=\"sid\" size=\"50\" value=\"$sid\">"
			."<input type=\"hidden\" name=\"op\" value=\"ChangeStory\">"
			."<input type=\"submit\" value=\""._SAVECHANGES."\">"
			."</form>";
			Close_Table();
			include ('footer.php');
		} else {
			include ('header.php');
			GraphicAdmin();
			Open_Table();
			echo "<center><font class=\"title\"><b>"._ARTICLEADMIN."</b></font></center>";
			Close_Table();
			echo "<br>";
			Open_Table();
			echo "<center><b>"._NOTAUTHORIZED1."</b><br><br>"
			.""._NOTAUTHORIZED2."<br><br>"
			.""._GOBACK."";
			Close_Table();
			include("footer.php");
		}
	}

	function removeStory($sid, $ok=0) {
		global $aid, $aid2, $prefix, $db, $admin_file;
		$aid = substr($aid, 0,31);
		$aid2 = substr($aid2, 0,31);
		$result = $db->sql_query("select counter, radminsuper from ".$prefix."_authors WHERE aid='$aid' AND aid2='$aid2'");
		list($counter, $radminsuper) = $db->sql_fetchrow($result);
		$radminsuper = intval($radminsuper);
		$counter = intval($counter);
		$sid = intval($sid);
		$result = $db->sql_query("SELECT admins FROM ".$prefix."_modules WHERE title='News'");
		$row2 = $db->sql_fetchrow($db->sql_query("SELECT name FROM ".$prefix."_authors WHERE aid='$aid' AND aid2='$aid2'"));
		while ($row = $db->sql_fetchrow($result)) {
			$admins = explode(",", $row['admins']);
			$auth_user = 0;
			for ($i=0; $i < sizeof($admins); $i++) {
			if ($row2['name'] == $admins[$i]) {
					$auth_user = 1;
				}
			}
			if ($auth_user == 1) {
				$radminarticle = 1;
			}
		}
		$result2 = $db->sql_query("SELECT aid, aid2 FROM ".$prefix."_stories WHERE sid='$sid'");
		list($aaid, $aaid2) = $db->sql_fetchrow($result2);
		$aaid = substr($aaid, 0,31);
		$aaid2 = substr($aaid2, 0,31);
		if (($radminarticle == 1) AND ($aaid == $aid) AND ($aaid2 == $aid2) OR ($radminsuper == 1)) {
			if($ok) {
				$counter--;
				$db->sql_query("DELETE FROM ".$prefix."_stories where sid='$sid'");
				$db->sql_query("DELETE FROM ".$prefix."_comments where sid='$sid'");
				$db->sql_query("update ".$prefix."_poll_desc set artid='0' where artid='$sid'");
				$result = $db->sql_query("update ".$prefix."_authors set counter='$counter' where aid='$aid' AND aid2='$aid2'");
				Header("Location: ".$admin_file.".php");
			} else {
				include("header.php");
				GraphicAdmin();
				Open_Table();
				echo "<center><font class=\"title\"><b>"._ARTICLEADMIN."</b></font></center>";
				Close_Table();
				echo "<br>";
				Open_Table();
				echo "<center>"._REMOVESTORY." $sid "._ANDCOMMENTS."";
				echo "<br><br>[ <a href=\"".$admin_file.".php\">"._NO."</a> | <a href=\"".$admin_file.".php?op=RemoveStory&amp;sid=$sid&amp;ok=1\">"._YES."</a> ]</center>";
				Close_Table();
				include("footer.php");
			}
		} else {
			include ('header.php');
			GraphicAdmin();
			Open_Table();
			echo "<center><font class=\"title\"><b>"._ARTICLEADMIN."</b></font></center>";
			Close_Table();
			echo "<br>";
			Open_Table();
			echo "<center><b>"._NOTAUTHORIZED1."</b><br><br>"
			.""._NOTAUTHORIZED2."<br><br>"
			.""._GOBACK."";
			Close_Table();
			include("footer.php");
		}
	}

	function changeStory($sid, $subject, $hometext, $bodytext, $topic, $notes, $catid, $ihome, $alanguage, $acomm, $assotop) {
		global $aid, $aid2, $prefix, $db, $admin_file;
		for ($i=0; $i<sizeof($assotop); $i++) 
		{
			$associated .= "$assotop[$i]-";
		}
		$sid = intval($sid);
		$aid = substr($aid, 0,31);
		$aid2 = substr($aid2, 0,31);
		$result = $db->sql_query("SELECT radminsuper FROM ".$prefix."_authors WHERE aid='".$aid."' AND aid2='".$aid2."'");
		list($radminsuper) = $db->sql_fetchrow($result);
		$radminsuper = intval($radminsuper);
		$result = $db->sql_query("SELECT admins FROM ".$prefix."_modules WHERE title='News'");
		$row2 = $db->sql_fetchrow($db->sql_query("SELECT name FROM ".$prefix."_authors WHERE aid='$aid' AND aid2='$aid2'"));
		while ($row = $db->sql_fetchrow($result)) {
			$admins = explode(",", $row['admins']);
			$auth_user = 0;
			for ($i=0; $i < sizeof($admins); $i++) {
			if ($row2['name'] == $admins[$i]) {
					$auth_user = 1;
				}
			}
			if ($auth_user == 1) {
				$radminarticle = 1;
			}
		}
		$result2 = $db->sql_query("select aid, aid2 from ".$prefix."_stories where sid='$sid'");
		list($aaid, $aaid2) = $db->sql_fetchrow($result2);
		$aaid = substr($aaid, 0,31);
		$aaid2 = substr($aaid2, 0,31);
		if (($radminarticle == 1) AND ($aaid == $aid) AND ($aaid2 == $aid2) OR ($radminsuper == 1)) 
		{
			$subject = filter($subject, "nohtml", 1);
			$hometext = filter($hometext, "", 1);
			$bodytext = filter($bodytext, "", 1);
			$notes = filter($notes, "", 1);
			$db->sql_query("update ".$prefix."_stories set catid='$catid', title='$subject', hometext='$hometext', bodytext='$bodytext', topic='$topic', notes='$notes', ihome='$ihome', alanguage='$alanguage', acomm='$acomm', associated='$associated' where sid='$sid'");
			Header("Location: ".$admin_file.".php?op=adminMain");
		}
	}

	function adminStory() {
		global $prefix, $db, $language, $multilingual, $admin_file;
		include ('header.php');
		GraphicAdmin();
		Open_Table();
		echo "<center><font class=\"title\"><b>"._ARTICLEADMIN."</b></font></center>";
		Close_Table();
		echo "<br>";
		$today = getdate();
		$tday = $today['mday'];
		if ($tday < 10){
			$tday = "0$tday";
		}
		$tmonth = $today['month'];
		$ttmon = $today['mon'];
		if ($ttmon < 10){
			$ttmon = "0$ttmon";
		}
		$tyear = $today['year'];
		$thour = $today['hours'];
		if ($thour < 10){
			$thour = "0$thour";
		}
		$tmin = $today['minutes'];
		if ($tmin < 10){
			$tmin = "0$tmin";
		}
		$tsec = $today['seconds'];
		if ($tsec < 10){
			$tsec = "0$tsec";
		}
		$nowdate = "$tmonth $tday, $tyear @ $thour:$tmin:$tsec";
		Open_Table();
		echo "<center><font class=\"option\"><b>"._ADDARTICLE."</b></font></center><br><br>"
			."<table width=\"100%\" border=\"0\" cellspacing=\"6\">"
			."<tr><td><form action=\"".$admin_file.".php\" method=\"post\">"
			."<b>"._TITLE.":</b></td><td><input type=\"text\" name=\"subject\" size=\"50\"></td></tr>"
			."<tr><td><b>"._TOPIC.":</b></td><td>";
		$toplist = $db->sql_query("select topicid, topictext from ".$prefix."_topics order by topictext");
		echo "<select name=\"topic\">";
		echo "<option value=\"\">"._SELECTTOPIC."</option>\n";
		while(list($topicid, $topics) = $db->sql_fetchrow($toplist)) {
			$topicid = intval($topicid);
			$topics = filter($topics, "nohtml");
			if ($topicid == $topic) {
				$sel = "selected ";
			}
			echo "<option $sel value=\"$topicid\">$topics</option>\n";
			$sel = "";
		}
		echo "</select></td></tr>"
			."<tr><td nowrap><b>"._ASSOTOPIC.":</b></td><td>";
		$sql = "SELECT topicid, topictext FROM ".$prefix."_topics ORDER BY topictext";
		$result = $db->sql_query($sql);
		echo "<select multiple name=\"assotop[]\" size=\"3\">";
		while ($row = $db->sql_fetchrow($result)) {
			$row['topicid'] = intval($row['topicid']);
			$row['topictext'] = filter($row['topictext'], "nohtml");
			echo "<option value='".$row['topicid']."'>".$row['topictext']."</option>";
		}
		echo "</select></td></tr>";
		$cat = 0;
		$selcat = $db->sql_query("select catid, title from ".$prefix."_stories_cat order by title");
		$a = 1;
		echo "<tr><td><b>"._CATEGORY.":</b></td><td>"
			."<select name=\"catid\">";
		if ($cat == 0) {
			$sel = "selected";
		} else {
			$sel = "";
		}
		echo "<option name=\"catid\" value=\"0\" $sel>"._ARTICLES."</option>";
		while(list($catid, $title) = $db->sql_fetchrow($selcat)) {
			$catid = intval($catid);
			$title = filter($title, "nohtml");
			if ($catid == $cat) {
				$sel = "selected";
			} else {
				$sel = "";
			}
			echo "<option name=\"catid\" value=\"$catid\" $sel>$title</option>";
			$a++;
		}
		echo "</select> &nbsp; <a href=\"".$admin_file.".php?op=AddCategory\"><img src=\"images/add.gif\" alt=\""._ADD."\" title=\""._ADD."\" border=\"0\" width=\"17\" height=\"17\"></a>  <a href=\"".$admin_file.".php?op=EditCategory\"><img src=\"images/edit.gif\" alt=\""._EDIT."\" title=\""._EDIT."\" border=\"0\" width=\"17\" height=\"17\"></a>  <a href=\"".$admin_file.".php?op=DelCategory\"><img src=\"images/delete.gif\" alt=\""._DELETE."\" title=\""._DELETE."\" border=\"0\" width=\"17\" height=\"17\"></a>"
			."</td></tr>"
			."<tr><td><b>"._PUBLISHINHOME."</b></td><td>";
		if (($ihome == 0) OR (empty($ihome))) {
			$sel1 = "checked";
			$sel2 = "";
		}
		if ($ihome == 1) {
			$sel1 = "";
			$sel2 = "checked";
		}
		echo "<input type=\"radio\" name=\"ihome\" value=\"0\" $sel1>"._YES."&nbsp;"
			."<input type=\"radio\" name=\"ihome\" value=\"1\" $sel2>"._NO.""
			."&nbsp;&nbsp;<font class=\"content\">[ "._ONLYIFCATSELECTED." ]</font></td></tr>";
		echo "<tr><td><b>"._ACTIVATECOMMENTS."</b></td><td>";
		if (($acomm == 0) OR (empty($acomm))) {
			$sel1 = "checked";
			$sel2 = "";
		}
		if ($acomm == 1) {
			$sel1 = "";
			$sel2 = "checked";
		}
		echo "<input type=\"radio\" name=\"acomm\" value=\"0\" $sel1>"._YES."&nbsp;"
			."<input type=\"radio\" name=\"acomm\" value=\"1\" $sel2>"._NO."</font></td></tr>";
		if ($multilingual == 1) {
			echo "<tr><td><b>"._LANGUAGE.":</b></td><td>"
				."<select name=\"alanguage\">";
			$handle=opendir('language');
			while ($file = readdir($handle)) {
				if (preg_match("/^lang\-(.+)\.php/", $file, $matches)) {
					$langFound = $matches[1];
					$languageslist .= "$langFound ";
				}
			}
			closedir($handle);
			$languageslist = explode(" ", $languageslist);
			sort($languageslist);
			for ($i=0; $i < sizeof($languageslist); $i++) {
				if(!empty($languageslist[$i])) {
					echo "<option value=\"$languageslist[$i]\" ";
					if($languageslist[$i]==$language) echo "selected";
					echo ">".ucfirst($languageslist[$i])."</option>\n";
				}
			}
			echo "<option value=\"\">"._ALL."</option></select></td></tr>";
		} else {
			echo "<input type=\"hidden\" name=\"alanguage\" value=\"$language\"></td></tr>";
		}
		echo "<tr><td><b>"._STORYTEXT.":</b></td><td>"
			."<textarea wrap=\"virtual\" cols=\"70\" rows=\"15\" name=\"hometext\"></textarea></td></tr>"
			."<tr><td><b>"._EXTENDEDTEXT.":</b></td><td>"
			."<textarea wrap=\"virtual\" cols=\"70\" rows=\"15\" name=\"bodytext\"></textarea><br>"
			."<font class=\"content\">"._ARESUREURL."</font></td></tr>"
			."<tr><td colspan=\"2\"><hr noshade size=\"1\"></td></tr>"
			."<tr><td><b>"._SCHEDULENEWS.":</b></td><td>"
			."<input type=radio name=automated value=1>"._YES." &nbsp;&nbsp;"
			."<input type=radio name=automated value=0 checked>"._NO."</td></tr>"
			."<tr><td><b>"._PUBLISHON.":</b></td><td>";
		$day = 1;
		echo ""._DAY.": <select name=\"day\">";
		while ($day <= 31) {
			if ($tday==$day) {
				$sel = "selected";
			} else {
				$sel = "";
			}
			echo "<option name=\"day\" $sel>$day</option>";
			$day++;
		}
		echo "</select>";
		$month = 1;
		echo " "._UMONTH.": <select name=\"month\">";
		while ($month <= 12) {
			if ($ttmon==$month) {
				$sel = "selected";
			} else {
				$sel = "";
			}
			echo "<option name=\"month\" $sel>$month</option>";
			$month++;
		}
		echo "</select>";
		$date = getdate();
		$year = $date['year'];
		echo " "._YEAR.": <input type=\"text\" name=\"year\" value=\"$year\" size=\"5\" maxlength=\"4\">"
		." <b>@</b> "._HOUR.": <select name=\"hour\">";
		$hour = 0;
		$cero = "0";
		while ($hour <= 23) {
			$dummy = $hour;
			if ($hour < 10) {
				$hour = "$cero$hour";
			}
			echo "<option name=\"hour\">$hour</option>";
			$hour = $dummy;
			$hour++;
		}
		echo "</select>"
		." : <select name=\"min\">";
		$min = 0;
		while ($min <= 59) {
			if (($min == 0) OR ($min == 5)) {
				$min = "0$min";
			}
			echo "<option name=\"min\">$min</option>";
			$min = $min + 5;
		}
		echo "</select>"
			." : 00</td></tr>"
			."<tr><td>&nbsp;</td><td>"._NOWIS.": $nowdate</td></tr>"
			."<tr><td colspan=\"2\"><hr noshade size=\"1\"></td></tr>"
			."<tr><td>&nbsp;</td><td><select name=\"op\">"
			."<option value=\"PreviewAdminStory\" selected>"._PREVIEWSTORY."</option>"
			."<option value=\"PostAdminStory\">"._POSTSTORY."</option>"
			."</select>"
			."&nbsp;&nbsp;<input type=\"submit\" value=\""._OK."\"></td></tr></table>";
		Close_Table();
		echo "<br>";
		putpoll("", array_fill(1, 12, ""));	
		echo "</form>";
		include ('footer.php');
	}

	function previewAdminStory($automated, $year, $day, $month, $hour, $min, $subject, $hometext, $bodytext, $topic, $catid, $ihome, $alanguage, $acomm, $pollTitle, $optionText, $assotop) {
		global $user, $bgcolor1, $bgcolor2, $prefix, $db, $alanguage, $multilingual, $admin_file;
		include ('header.php');
		if ($topic<1) {
			$topic = 1;
		}
		GraphicAdmin();
		Open_Table();
		echo "<center><font class=\"title\"><b>"._ARTICLEADMIN."</b></font></center>";
		Close_Table();
		echo "<br>";
		$today = getdate();
		$tday = $today['mday'];
		if ($tday < 10){
			$tday = "0$tday";
		}
		$tmonth = $today['month'];
		$tyear = $today['year'];
		$thour = $today['hours'];
		if ($thour < 10){
			$thour = "0$thour";
		}
		$tmin = $today['minutes'];
		if ($tmin < 10){
			$tmin = "0$tmin";
		}
		$tsec = $today['seconds'];
		if ($tsec < 10){
			$tsec = "0$tsec";
		}
		$nowdate = "$tmonth $tday, $tyear @ $thour:$tmin:$tsec";
		Open_Table();
		echo "<center><font class=\"option\"><b>"._PREVIEWSTORY."</b></font></center><br>"
			."<form action=\"".$admin_file.".php\" method=\"post\">"
			."<input type=\"hidden\" name=\"catid\" value=\"$catid\">";
		$subject = filter($subject, "nohtml", 0, preview);
		$hometext = filter($hometext);
		$bodytext = filter($bodytext);
		$result = $db->sql_query("select topicimage from ".$prefix."_topics where topicid='$topic'");
		list($topicimage) = $db->sql_fetchrow($result);
		echo "<table border=\"0\" width=\"75%\" cellpadding=\"0\" cellspacing=\"1\" bgcolor=\"$bgcolor2\" align=\"center\"><tr><td>"
			."<table border=\"0\" width=\"100%\" cellpadding=\"8\" cellspacing=\"1\" bgcolor=\"$bgcolor1\"><tr><td>"
			."<img src=\"images/topics/$topicimage\" border=\"0\" align=\"right\" alt=\"\">";
		themepreview($subject, $hometext, $bodytext);
		echo "</td></tr></table></td></tr></table><br><br>"
			."<table width=\"100%\" border=\"0\" cellspacing=\"6\">"
			."<tr><td nowrap><b>"._TITLE.":</b></td><td>"
			."<input type=\"text\" name=\"subject\" size=\"50\" value=\"$subject\"></td></tr>"
			."<tr><td nowrap><b>"._TOPIC.":</b></td><td><select name=\"topic\">";
		$toplist = $db->sql_query("select topicid, topictext from ".$prefix."_topics order by topictext");
		echo "<option value=\"\">"._ALLTOPICS."</option>\n";
		while(list($topicid, $topics) = $db->sql_fetchrow($toplist)) {
			$topicid = intval($topicid);
			$topics = filter($topics, "nohtml");
			if ($topicid==$topic) {
				$sel = "selected ";
			}
			echo "<option $sel value=\"$topicid\">$topics</option>\n";
			$sel = "";
		}
		echo "</select></td></tr>";
		for ($i=0; $i<sizeof($assotop); $i++) {
			$associated .= "$assotop[$i]-";
		}
		$asso_t = explode("-", $associated);
		echo "<tr><td nowrap><b>"._ASSOTOPIC.":</b></td><td>";
		$sql = "SELECT topicid, topictext FROM ".$prefix."_topics ORDER BY topictext";
		$result = $db->sql_query($sql);
		echo "<select multiple name=\"assotop[]\" size=\"3\">";
		while ($row = $db->sql_fetchrow($result)) {
			for ($i=0; $i<sizeof($asso_t); $i++) {
				if ($asso_t[$i] == $row['topicid']) {
					$checked = "selected";
					break;
				}
			}
			$row['topicid'] = intval($row['topicid']);
			$row['topictext'] = filter($row['topictext'], "nohtml");
			echo "<option value='".$row['topicid']."' $checked>".$row['topictext']."</option>";
			$checked = "";
		}
		echo "</select></td></tr>";
		$cat = $catid;
		$selcat = $db->sql_query("select catid, title from ".$prefix."_stories_cat order by title");
		$a = 1;
		echo "<tr><td nowrap><b>"._CATEGORY.":</b></td><td>";
		echo "<select name=\"catid\">";
		if ($cat == 0) {
			$sel = "selected";
		} else {
			$sel = "";
		}
		echo "<option name=\"catid\" value=\"0\" $sel>"._ARTICLES."</option>";
		while(list($catid, $title) = $db->sql_fetchrow($selcat)) {
			$catid = intval($catid);
			$title = filter($title, "nohtml");
			if ($catid == $cat) {
				$sel = "selected";
			} else {
				$sel = "";
			}
			echo "<option name=\"catid\" value=\"$catid\" $sel>$title</option>";
			$a++;
		}
		echo "</select> &nbsp; <a href=\"".$admin_file.".php?op=AddCategory\"><img src=\"images/add.gif\" alt=\""._ADD."\" title=\""._ADD."\" border=\"0\" width=\"17\" height=\"17\"></a>  <a href=\"".$admin_file.".php?op=EditCategory\"><img src=\"images/edit.gif\" alt=\""._EDIT."\" title=\""._EDIT."\" border=\"0\" width=\"17\" height=\"17\"></a>  <a href=\"".$admin_file.".php?op=DelCategory\"><img src=\"images/delete.gif\" alt=\""._DELETE."\" title=\""._DELETE."\" border=\"0\" width=\"17\" height=\"17\"></a>"
			."</td></tr>"
			."<tr><td nowrap><b>"._PUBLISHINHOME."</b></td><td>";
		if (($ihome == 0) OR (empty($ihome))) {
			$sel1 = "checked";
			$sel2 = "";
		}
		if ($ihome == 1) {
			$sel1 = "";
			$sel2 = "checked";
		}
		echo "<input type=\"radio\" name=\"ihome\" value=\"0\" $sel1>"._YES."&nbsp;"
			."<input type=\"radio\" name=\"ihome\" value=\"1\" $sel2>"._NO.""
			."&nbsp;&nbsp;<font class=\"content\">[ "._ONLYIFCATSELECTED." ]</font></td></tr>"
			."<tr><td nowrap><b>"._ACTIVATECOMMENTS."</b></td><td>";
		if (($acomm == 0) OR (empty($acomm))) {
			$sel1 = "checked";
			$sel2 = "";
		}
		if ($acomm == 1) {
			$sel1 = "";
			$sel2 = "checked";
		}
		echo "<input type=\"radio\" name=\"acomm\" value=\"0\" $sel1>"._YES."&nbsp;"
			."<input type=\"radio\" name=\"acomm\" value=\"1\" $sel2>"._NO."</font></td></tr>";
		if ($multilingual == 1) {
			echo "<tr><td nowrap><b>"._LANGUAGE.": </b>"
				."<select name=\"alanguage\">";
			$handle=opendir('language');
			while ($file = readdir($handle)) {
				if (preg_match("/^lang\-(.+)\.php/", $file, $matches)) {
					$langFound = $matches[1];
					$languageslist .= "$langFound ";
				}
			}
			closedir($handle);
			$languageslist = explode(" ", $languageslist);
			sort($languageslist);
			for ($i=0; $i < sizeof($languageslist); $i++) {
				if(!empty($languageslist[$i])) {
					echo "<option value=\"$languageslist[$i]\" ";
					if($languageslist[$i]==$alanguage) echo "selected";
					echo ">".ucfirst($languageslist[$i])."</option>\n";
				}
			}
			if (empty($alanguage)) {
				$sellang = "selected";
			} else {
				$sellang = "";
			}
			echo "<option value=\"\" $sellang>"._ALL."</option></select></td></tr>";
		} else {
			echo "<input type=\"hidden\" name=\"alanguage\" value=\"$language\"></td></tr>";
		}
		echo "<tr><td nowrap><b>"._STORYTEXT.":</b></td><td>"
			."<textarea wrap=\"virtual\" cols=\"70\" rows=\"15\" name=\"hometext\">$hometext</textarea></td></tr>"
			."<tr><td nowrap><b>"._EXTENDEDTEXT.":</b></td><td>"
			."<textarea wrap=\"virtual\" cols=\"70\" rows=\"15\" name=\"bodytext\">$bodytext</textarea></td></tr>";
		if ($automated == 1) {
			$sel1 = "checked";
			$sel2 = "";
		} else {
			$sel1 = "";
			$sel2 = "checked";
		}
		echo "<tr><td colspan=\"2\"><hr noshade size=\"1\"></td></tr>"
			."<tr><td nowrap><b>"._SCHEDULENEWS.":</b></td><td>"
			."<input type=\"radio\" name=\"automated\" value=\"1\" $sel1>"._YES." &nbsp;&nbsp;"
			."<input type=\"radio\" name=\"automated\" value=\"0\" $sel2>"._NO."</td></tr>";
		$xday = 1;
		echo "<tr><td nowrap><b>"._PUBLISHON.":</b></td><td>"
			.""._DAY.": <select name=\"day\">";
		while ($xday <= 31) {
			if ($xday == $day) {
				$sel = "selected";
			} else {
				$sel = "";
			}
			echo "<option name=\"day\" $sel>$xday</option>";
			$xday++;
		}
		echo "</select>";
		$xmonth = 1;
		echo " "._UMONTH.": <select name=\"month\">";
		while ($xmonth <= 12) {
			if ($xmonth == $month) {
				$sel = "selected";
			} else {
				$sel = "";
			}
			echo "<option name=\"month\" $sel>$xmonth</option>";
			$xmonth++;
		}
		echo "</select>";
		echo " "._YEAR.": <input type=\"text\" name=\"year\" value=\"$year\" size=\"5\" maxlength=\"4\">";
		echo " <b>@</b>"._HOUR.": <select name=\"hour\">";
		$xhour = 0;
		$cero = "0";
		while ($xhour <= 23) {
			$dummy = $xhour;
			if ($xhour < 10) {
				$xhour = "$cero$xhour";
			}
			if ($xhour == $hour) {
				$sel = "selected";
			} else {
				$sel = "";
			}
			echo "<option name=\"hour\" $sel>$xhour</option>";
			$xhour = $dummy;
			$xhour++;
		}
		echo "</select>";
		echo " : <select name=\"min\">";
		$xmin = 0;
		while ($xmin <= 59) {
			if (($xmin == 0) OR ($xmin == 5)) {
				$xmin = "0$xmin";
			}
			if ($xmin == $min) {
				$sel = "selected";
			} else {
				$sel = "";
			}
			echo "<option name=\"min\" $sel>$xmin</option>";
			$xmin = $xmin + 5;
		}
		echo "</select>";
		echo " : 00</td></tr>"
			."<tr><td>&nbsp;</td><td>"._NOWIS.": $nowdate</td></tr>"
			."<tr><td colspan=\"2\"><hr noshade size=\"1\"></td></tr>"
			."<tr><td>&nbsp;</td><td><select name=\"op\">"
			."<option value=\"PreviewAdminStory\" selected>"._PREVIEWSTORY."</option>"
			."<option value=\"PostAdminStory\">"._POSTSTORY."</option>"
			."</select>"
			."&nbsp;&nbsp;<input type=\"submit\" value=\""._OK."\"></td></tr></table>";
		Close_Table();
		echo "<br>";
		putpoll($pollTitle, $optionText);
		echo "</form>";
		include ('footer.php');
	}
//////////////////////
	function postAdminStory($automated, $year, $day, $month, $hour, $min, $subject, $hometext, $bodytext, $topic, $catid, $ihome, $alanguage, $acomm, $pollTitle, $optionText, $assotop) 
	{
		global $aid, $aid2, $prefix, $db, $admin_file;
		//echo var_dump($aid);
		for ($i=0; $i<sizeof($assotop); $i++) 
		{
			$associated .= $assotop[$i].'-';
		}
		if ($automated == 1) 
		{
			if ($day < 10) 
			{
				$day = '0'.$day;
			}
			if ($month < 10) 
			{
				$month = '0'.$month;
			}
			$sec = '00';
			$date = $year.'-'.$month.'-'.$day.'&nbsp;'.$hour.':'.$min.':'.$sec;
			$notes = "";
			$forename = $aid;
			$surname = $aid2;
			$subject = filter($subject, "nohtml", 1);
			$hometext = filter($hometext, "", 1);
			$bodytext = filter($bodytext, "", 1);
			$result = $db->sql_query("INSERT INTO ".$prefix."_autonews values (NULL, '".$catid."', '".$aid."', '".$aid2."', '".$subject."', '".$date."', '".$hometext."', '".$bodytext."', '".$topic."', '".$forename."', '".$surname."', '".$notes."', '".$ihome."', '".$alanguage."', '".$acomm."', '".$associated."')");
			if (!$result) 
			{
				exit();
			}
			$result = $db->sql_query("UPDATE ".$prefix."_authors set counter=counter+1 WHERE aid='".$aid."' AND aid2='".$aid2."'");
			Header("Location: ".$admin_file.".php?op=adminMain");
		} else {
			$subject = filter($subject, "nohtml", 1);
			$hometext = filter($hometext, "", 1);
			$bodytext = filter($bodytext, "", 1);
			if (($pollTitle != "") &&($optionText[1] != "") && ($optionText[2] != "")) 
			{
				$haspoll = 1;
				$timeStamp = time();
				$pollTitle = filter($pollTitle, "nohtml", 1);
				if(!$db->sql_query("INSERT INTO ".$prefix."_poll_desc VALUES (NULL, '".$pollTitle."', '".$timeStamp."', '0', '".$alanguage."', '0', '0')")) 
				{
					return;
				}
				$object = $db->sql_fetchrow($db->sql_query("SELECT pollID FROM ".$prefix."_poll_desc WHERE pollTitle='".$pollTitle."'"));
				$id = $object['pollID'];
				$id = intval($id);
				for($i = 1; $i <= sizeof($optionText); $i++) 
				{
					if(!empty($optionText[$i])) 
					{
						$optionText[$i] = filter($optionText[$i], "nohtml", 1);
					}
					if(!$db->sql_query("INSERT INTO ".$prefix."_poll_data (pollID, optionText, optionCount, voteID) VALUES ('".$id."', '".$optionText[$i]."', '0', '".$i."')")) 
					{
						return;
					}
				}
			} else {
				$haspoll = 0;
				$id = 0;
			}
			$result = $db->sql_query("insert into ".$prefix."_stories values (NULL, '$catid', '$aid', '$aid2', '$subject', now(), '$hometext', '$bodytext', '0', '0', '$topic', '$aid', '$aid2', '$notes', '$ihome', '$alanguage', '$acomm', '$haspoll', '$id', '0', '0', '0', '$associated')");
			$result = $db->sql_query("select sid from ".$prefix."_stories WHERE title='$subject' order by time DESC limit 0,1");
			list($artid) = $db->sql_fetchrow($result);
			$artid = intval($artid);
			$db->sql_query("UPDATE ".$prefix."_poll_desc SET artid='$artid' WHERE pollID='$id'");
			if (!$result) {
				exit();
			}
			$result = $db->sql_query("update ".$prefix."_authors set counter=counter+1 where aid='$aid' AND aid2='$aid2'");
			Header("Location: ".$admin_file.".php?op=adminMain");
		}
	}

	function submissions() 
	{
		global $admin, $bgcolor1, $bgcolor2, $prefix, $db, $radminsuper, $sl_firstname, $sl_lastname, $multilingual, $admin_file, $user_prefix;
		$dummy = 0;
		include ("header.php");
		GraphicAdmin();
		Open_Table();
		echo "<center><font class=\"title\"><b>"._SUBMISSIONSADMIN."</b></font></center>";
		Close_Table();
		echo "<br>";
		Open_Table();
		$result = $db->sql_query("SELECT qid, uid, firstname, lastname, subject, timestamp, alanguage FROM ".$prefix."_queue order by timestamp DESC");
		if($db->sql_numrows($result) == 0) {
			echo "<table width=\"100%\"><tr><td bgcolor=\"$bgcolor1\" align=\"center\"><b>"._NOSUBMISSIONS."</b></td></tr></table>\n";
		} else {
			echo "<center><font class=\"content\"><b>"._NEWSUBMISSIONS."</b></font><form action=\"".$admin_file.".php\" method=\"post\"><table width=\"100%\" border=\"1\"><tr><td bgcolor=\"$bgcolor2\"><b>&nbsp;"._TITLE."&nbsp;</b></td>";
			if ($multilingual == 1) {
				echo "<td bgcolor=\"$bgcolor2\"><b><center>&nbsp;"._LANGUAGE."&nbsp;</center></b></td>";
			}
			echo "<td bgcolor=\"$bgcolor2\"><b><center>&nbsp;"._AUTHOR."&nbsp;</center></b></td><td bgcolor=\"$bgcolor2\"><b><center>&nbsp;"._DATE."&nbsp;</center></b></td><td bgcolor=\"$bgcolor2\"><b><center>&nbsp;"._FUNCTIONS."&nbsp;</center></b></td></tr>\n";
			while (list($qid, $uid, $firstname, $lastname, $subject, $timestamp, $alanguage) = $db->sql_fetchrow($result)) {
				$qid = intval($qid);
				$uid = intval($uid);
				$subject = filter($subject, "nohtml");
				$row = $db->sql_fetchrow($db->sql_query("SELECT karma FROM ".$user_prefix."_users WHERE user_id='$uid'"));
				if ($row['karma'] == 0) {
					$karma = "&nbsp;";
				} elseif ($row['karma'] == 1) {
					$karma = "&nbsp;<img src=\"images/karma/".$row['karma'].".gif\" alt=\""._KARMALOW."\" title=\""._KARMALOW."\" border=\"0\">";
				} elseif ($row['karma'] == 2) {
					$karma = "&nbsp;<img src=\"images/karma/".$row['karma'].".gif\" alt=\""._KARMABAD."\" title=\""._KARMABAD."\" border=\"0\">";
				} elseif ($row['karma'] == 3) {
					$karma = "&nbsp;<img src=\"images/karma/".$row['karma'].".gif\" alt=\""._KARMADEVIL."\" title=\""._KARMADEVIL."\" border=\"0\">";
				}
				echo "<td width=\"100%\" bgcolor=\"$bgcolor1\"><font class=\"content\">\n";
				if (empty($subject)) {
					echo "&nbsp;<a href=\"".$admin_file.".php?op=DisplayStory&amp;qid=$qid\">"._NOSUBJECT."</a></font>\n";
				} else {
					echo "&nbsp;<a href=\"".$admin_file.".php?op=DisplayStory&amp;qid=$qid\">$subject</a></font>\n";
				}
				if ($multilingual == 1) {
					if (empty($alanguage)) {
						$alanguage = _ALL;
					}
					echo "</td><td align=\"center\" bgcolor=\"$bgcolor1\"><font size=\"2\">&nbsp;$alanguage&nbsp;</font>\n";
				}
				if ($firstname != $sl_firstname && $lastname != $sl_lastname) 
				{
					echo "</td><td bgcolor=\"$bgcolor1\" align=\"center\" nowrap><font size=\"2\">&nbsp;<a href='index.php?name=Your_Account&op=userinfo&amp;firstname=$firstname&amp;lastname=$lastname'>$firstname $lastname</a>$karma</font>\n";
				} 
				else 
				{
					echo "</td><td bgcolor=\"$bgcolor1\" align=\"center\" nowrap><font size=\"2\">&nbsp;$firstname&nbsp;$lastname&nbsp;</font>\n";
				}
				$timestamp = explode(" ", $timestamp);
				echo '</td><td bgcolor="'.$bgcolor1.'" align="right" nowrap><font class="content">&nbsp;'.$timestamp[0].'&nbsp;</font></td><td bgcolor="'.$bgcolor1.'" align="center"><font class="content">&nbsp;<a href="'.$admin_file.'.php?op=DisplayStory&amp;qid='.$qid.'"><img src="images/edit.gif" alt="'._EDIT.'" title="'._EDIT.'" border="0" width="17" height="17"></a>&nbsp;&nbsp;<a href="'.$admin_file.'.php?op=DeleteStory&amp;qid='.$qid.'"><img src="images/delete.gif" alt="'._DELETE.'" title="'._DELETE.'" border="0" width="17" height="17"></a>&nbsp;</td></tr>';
				$dummy++;
			}
			if ($dummy < 1) 
			{
				echo '<tr><td bgcolor="'.$bgcolor1.'" align="center"><b>'._NOSUBMISSIONS.'</b></form></td></tr></table>';
			} 
			else 
			{
				echo '</table></form>';
			}
		}
		if ($radminsuper == 1) 
		{
			echo '<br /><center>[&nbsp;<a href="'.$admin_file.'.php?op=subdelete">'._DELETE.'</a> ]</center><br />';
		}
		Close_Table();
		include ("footer.php");
	}

	function subdelete() 
	{
		global $prefix, $db, $admin_file;
		$db->sql_query("DELETE FROM ".$prefix."_queue");
		Header("Location: ".$admin_file.".php?op=adminMain");
	}

if (!isset($sid)) { $sid = ""; }

	switch($op) {

		case "EditCategory":
		EditCategory($catid);
		break;

		case "subdelete":
		subdelete();
		break;

		case "DelCategory":
		DelCategory($cat);
		break;

		case "YesDelCategory":
		YesDelCategory($catid);
		break;

		case "NoMoveCategory":
		NoMoveCategory($catid, $newcat);
		break;

		case "SaveEditCategory":
		SaveEditCategory($catid, $title);
		break;

		case "SelectCategory":
		SelectCategory($cat);
		break;

		case "AddCategory":
		AddCategory();
		break;

		case "SaveCategory":
		SaveCategory($cat_title);
		break;

		case "DisplayStory":
		displayStory($qid);
		break;

		case "PreviewAgain":
		previewStory($automated, $year, $day, $month, $hour, $min, $qid, $uid, $forename, $surname, $subject, $hometext, $bodytext, $topic, $notes, $catid, $ihome, $alanguage, $acomm, $pollTitle, $optionText, $assotop);
		break;

		case "PostStory":
		postStory($automated, $year, $day, $month, $hour, $min, $qid, $uid, $forename, $surname, $subject, $hometext, $bodytext, $topic, $notes, $catid, $ihome, $alanguage, $acomm, $pollTitle, $optionText, $assotop);
		break;

		case "EditStory":
		editStory($sid);
		break;

		case "RemoveStory":
		removeStory($sid, $ok);
		break;

		case "ChangeStory":
		changeStory($sid, $subject, $hometext, $bodytext, $topic, $notes, $catid, $ihome, $alanguage, $acomm, $assotop);
		break;

		case "DeleteStory":
		deleteStory($qid);
		break;

		case "adminStory":
		adminStory($sid);
		break;

		case "PreviewAdminStory":
		previewAdminStory($automated, $year, $day, $month, $hour, $min, $subject, $hometext, $bodytext, $topic, $catid, $ihome, $alanguage, $acomm, $pollTitle, $optionText, $assotop);
		break;

		case "PostAdminStory":
		postAdminStory($automated, $year, $day, $month, $hour, $min, $subject, $hometext, $bodytext, $topic, $catid, $ihome, $alanguage, $acomm, $pollTitle, $optionText, $assotop);
		break;

		case "autoDelete":
		autodelete($anid);
		break;

		case "autoEdit":
		autoEdit($anid);
		break;

		case "autoSaveEdit":
		autoSaveEdit($anid, $year, $day, $month, $hour, $min, $title, $hometext, $bodytext, $topic, $notes, $catid, $ihome, $alanguage, $acomm);
		break;

		case "submissions":
		submissions();
		break;

		case "publish_now":
		publish_now($anid);
		break;

	}
} 
else 
{
	include('header.php');
	GraphicAdmin();
	Open_Table();
	echo '<center><b>'._ERROR.'</b><br /><br />You do not have administration permission for module '.$module_name.'</center>';
	Close_Table();
	include('footer.php');
}
?>