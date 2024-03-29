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
$optionbox = "";
$module_name = basename(dirname(__FILE__));
get_lang($module_name);

if (isset($sid)) { $sid = intval($sid); } else { $sid = ""; }
if (stristr($REQUEST_URI,"mainfile")) {
	Header("Location: index.php?name=$module_name&file=article&sid=$sid");
} elseif (empty($sid) && !isset($tid)) {
	Header("Location: index.php");
}

if ($save AND is_user($user)) {
	cookiedecode($user);
	getusrinfo($user);
	if(!isset($mode)) { $mode = $userinfo['umode']; }
	if(!isset($order)) { $order = $userinfo['uorder']; }
	if(!isset($thold)) { $thold = $userinfo['thold']; }
	$db->sql_query("UPDATE ".$user_prefix."_users SET umode='$mode', uorder='$order', thold='$thold' where uid='$cookie[0]'");
	getusrinfo($user);
	$info = base64_encode("$userinfo[user_id]:$userinfo[username]:$userinfo[user_password]:$userinfo[storynum]:$userinfo[umode]:$userinfo[uorder]:$userinfo[thold]:$userinfo[noscore]");
	setcookie("user","$info",time()+$cookieusrtime);
}

if ($op == "Reply") 
{
  $display = "";
  if(isset($mode)) { $display .= "&mode=".$mode; }
  if(isset($order)) { $display .= "&order=".$order; }
  if(isset($thold)) { $display .= "&thold=".$thold; }
  Header("Location: index.php?name=$module_name&file=comments&op=Reply&pid=0&sid=".$sid.$display);
}

$result = $db->sql_query("SELECT catid, aid, aid2, time, title, hometext, bodytext, topic, firstname, lastname, notes, acomm, haspoll, pollID, score, ratings FROM ".$prefix."_stories WHERE sid='".$sid."'");
if ($numrows = $db->sql_numrows($result) != 1) {
	Header("Location: index.php");
	die();
}
$row = $db->sql_fetchrow($result);
$catid = intval($row['catid']);
$aaid = filter($row['aid'], "nohtml");
$aaid2 = filter($row['aid2'], "nohtml");
$time = $row['time'];
$title = filter($row['title'], "nohtml");
$hometext = filter($row['hometext']);
$bodytext = filter($row['bodytext']);
$topic = intval($row['topic']);
$firstname = filter($row['firstname'], "nohtml");
$lastname = filter($row['lastname'], "nohtml");
$notes = filter($row['notes']);
$acomm = intval($row['acomm']);
$haspoll = intval($row['haspoll']);
$pollID = intval($row['pollID']);
$score = intval($row['score']);
$ratings = intval($row['ratings']);

if (empty($aaid) && empty($aaid2)) 
{
	Header("Location: index.php?name=$module_name");
}

$db->sql_query("UPDATE ".$prefix."_stories SET counter=counter+1 where sid='$sid'");

$artpage = 1;
$pagetitle = "- $title";
require("header.php");
$artpage = 0;

formatTimestamp($time);
$title = filter($title, "nohtml");
$hometext = filter($hometext);
$bodytext = filter($bodytext);
$notes = filter($notes);

if (!empty($notes)) {
	$notes = "<br><br><b>"._NOTE."</b> <i>$notes</i>";
} else {
	$notes = "";
}

if(empty($bodytext)) {
	$bodytext = "$hometext$notes";
} else {
	$bodytext = "$hometext<br><br>$bodytext$notes";
}

if(empty($firstname)) 
{
	$firstname = $sl_firstname;
}

if(empty($lastname)) 
{
	$lastname = $sl_lastname;
}

getTopics($sid);

if ($catid != 0) {
	$row2 = $db->sql_fetchrow($db->sql_query("select title from ".$prefix."_stories_cat where catid='$catid'"));
	$title1 = filter($row2['title'], "nohtml");
	$title = "<a href=\"index.php?name=$module_name&amp;file=categories&amp;op=newindex&amp;catid=$catid\"><font class=\"storycat\">$title1</font></a>: $title";
}

echo "<table width=\"100%\" border=\"0\"><tr><td valign=\"top\" width=\"100%\">\n";
themearticle($aaid, $aaid2, $firstname, $lastname, $datetime, $title, $bodytext, $topic, $topicname, $topicimage, $topictext);
echo "</td><td>&nbsp;</td><td valign=\"top\">\n";

if ($multilingual == 1) {
	$querylang = "AND (blanguage='$currentlang' OR blanguage='')";
} else {
	$querylang = "";
}

/* Determine if the article has attached a poll */
if ($haspoll == 1) {
	$url = sprintf("index.php?name=Surveys&amp;op=results&amp;pollID=%d", $pollID);
	$boxContent = "<form action=\"index.php?name=Surveys\" method=\"post\">";
	$boxContent .= "<input type=\"hidden\" name=\"pollID\" value=\"".$pollID."\">";
	$row3 = $db->sql_fetchrow($db->sql_query("SELECT pollTitle, voters FROM ".$prefix."_poll_desc WHERE pollID='$pollID'"));
	$pollTitle = filter($row3['pollTitle'], "nohtml");
	$voters = $row3['voters'];
	$boxTitle = _ARTICLEPOLL;
	$boxContent .= "<font class=\"content\"><b>$pollTitle</b></font><br><br>\n";
	$boxContent .= "<table border=\"0\" width=\"100%\">";
	for($i = 1; $i <= 12; $i++) {
		$result4 = $db->sql_query("SELECT pollID, optionText, optionCount, voteID FROM ".$prefix."_poll_data WHERE (pollID='$pollID') AND (voteID='$i')");
		$row4 = $db->sql_fetchrow($result4);
		$numrows = $db->sql_numrows($result4);
		if($numrows != 0) {
			$optionText = $row4['optionText'];
			if(!empty($optionText)) {
				$boxContent .= "<tr><td valign=\"top\"><input type=\"radio\" name=\"voteID\" value=\"".$i."\"></td><td width=\"100%\"><font class=\"content\">$optionText</font></td></tr>\n";
			}
		}
	}
	$boxContent .= "</table><br><center><font class=\"content\"><input type=\"submit\" value=\""._VOTE."\"></font><br>";
	if (is_user($user)) {
		cookiedecode($user);
	}
	for($i = 0; $i < 12; $i++) {
		$row5 = $db->sql_fetchrow($db->sql_query("SELECT optionCount FROM ".$prefix."_poll_data WHERE (pollID='$pollID') AND (voteID='$i')"));
		$optionCount = $row5['optionCount'];
		$sum = (int)$sum+$optionCount;
	}
	$boxContent .= "<font class=\"content\">[ <a href=\"index.php?name=Surveys&amp;op=results&amp;pollID=$pollID&amp;mode=".$userinfo['umode']."&amp;order=".$userinfo['uorder']."&amp;thold=".$userinfo['thold']."\"><b>"._RESULTS."</b></a> | <a href=\"index.php?name=Surveys\"><b>"._POLLS."</b></a> ]<br>";

	if ($pollcomm) {
		$result6 = $db->sql_query("select * from ".$prefix."_pollcomments where pollID='$pollID'");
		$numcom = $db->sql_numrows($result6);
		$boxContent .= "<br>"._VOTES.": <b>$sum</b><br>"._PCOMMENTS." <b>$numcom</b>\n\n";
	} else {
		$boxContent .= "<br>"._VOTES." <b>$sum</b>\n\n";
	}
	$boxContent .= "</font></center></form>\n\n";
	themesidebox($boxTitle, $boxContent);
}

$row7 = $db->sql_fetchrow($db->sql_query("select title, content, active, bposition from ".$prefix."_blocks where blockfile='block-Login.php' $querylang"));
$title = filter($row7['title'], "nohtml");
$content = filter($row7['content']);
$active = intval($row7['active']);
$position = $row7['bposition'];
$position = substr("$position", 0,1);
if (($active == 1) AND ($position == "r") AND (!is_user($user))) {
	loginbox();
}

$boxtitle = ""._RELATED."";
$boxstuff = "<font class=\"content\">";
$result8 = $db->sql_query("select name, url from ".$prefix."_related where tid='$topic'");
while ($row8 = $db->sql_fetchrow($result8)) {
	$name = filter($row8['name'], "nohtml");
	$url = filter($row8['url'], "nohtml");
	$boxstuff .= "<strong><big>&middot;</big></strong>&nbsp;<a href=\"$url\" target=\"new\">$name</a><br>\n";
}

if(is_active('Search'))
{
   $boxstuff .= "<strong><big>&middot;</big></strong>&nbsp;<a href=\"index.php?name=Search&amp;topic=$topic\">"._MOREABOUT." $topictext</a><br>\n";
   $boxstuff .= "<strong><big>&middot;</big></strong>&nbsp;<a href=\"index.php?name=Search&amp;author=$aaid\">"._NEWSBY." $aaid $aaid2</a>\n";
}   

$boxstuff .= "</font><br><hr noshade width=\"95%\" size=\"1\"><center><font class=\"content\"><b>"._MOSTREAD." $topictext:</b><br>\n";

global $multilingual, $currentlang, $admin_file, $user;
if ($multilingual == 1) {
	$querylang = "AND (alanguage='$currentlang' OR alanguage='')"; /* the OR is needed to display stories who are posted to ALL languages */
} else {
	$querylang = "";
}
$row9 = $db->sql_fetchrow($db->sql_query("select sid, title from ".$prefix."_stories where topic='$topic' $querylang order by counter desc limit 0,1"));
$topstory = intval($row9['sid']);
$ttitle = filter($row9['title'], "nohtml");

$boxstuff .= "<a href=\"index.php?name=$module_name&file=article&sid=$topstory\">$ttitle</a></font></center><br>\n";
themesidebox($boxtitle, $boxstuff);

if ($ratings != 0) {
	$rate = substr($score / $ratings, 0, 4);
	$r_image = round($rate);
	if ($r_image == 1) {
		$the_image = "<br><br><img src=\"images/articles/stars-1.gif\" border=\"1\"></center><br>";
	} elseif ($r_image == 2) {
		$the_image = "<br><br><img src=\"images/articles/stars-2.gif\" border=\"1\"></center><br>";
	} elseif ($r_image == 3) {
		$the_image = "<br><br><img src=\"images/articles/stars-3.gif\" border=\"1\"></center><br>";
	} elseif ($r_image == 4) {
		$the_image = "<br><br><img src=\"images/articles/stars-4.gif\" border=\"1\"></center><br>";
	} elseif ($r_image == 5) {
		$the_image = "<br><br><img src=\"images/articles/stars-5.gif\" border=\"1\"></center><br>";
	}
} else {
	$rate = 0;
	$the_image = "</center><br>";
}

$ratetitle = ""._RATEARTICLE."";
$ratecontent = "<center>"._AVERAGESCORE.": <b>$rate</b><br>"._VOTES.": <b>$ratings</b>$the_image";
$ratecontent .= "<form action=\"index.php?name=$module_name\" method=\"post\"><center>"._RATETHISARTICLE."</center><br>";
$ratecontent .= "<input type=\"hidden\" name=\"sid\" value=\"$sid\">";
$ratecontent .= "<input type=\"hidden\" name=\"op\" value=\"rate_article\">";
$ratecontent .= "<input type=\"radio\" name=\"score\" value=\"5\"> <img src=\"images/articles/stars-5.gif\" border=\"0\" alt=\""._EXCELLENT."\" title=\""._EXCELLENT."\"><br>";
$ratecontent .= "<input type=\"radio\" name=\"score\" value=\"4\"> <img src=\"images/articles/stars-4.gif\" border=\"0\" alt=\""._VERYGOOD."\" title=\""._VERYGOOD."\"><br>";
$ratecontent .= "<input type=\"radio\" name=\"score\" value=\"3\"> <img src=\"images/articles/stars-3.gif\" border=\"0\" alt=\""._GOOD."\" title=\""._GOOD."\"><br>";
$ratecontent .= "<input type=\"radio\" name=\"score\" value=\"2\"> <img src=\"images/articles/stars-2.gif\" border=\"0\" alt=\""._REGULAR."\" title=\""._REGULAR."\"><br>";
$ratecontent .= "<input type=\"radio\" name=\"score\" value=\"1\"> <img src=\"images/articles/stars-1.gif\" border=\"0\" alt=\""._BAD."\" title=\""._BAD."\"><br><br>";
$ratecontent .= "<center><input type=\"submit\" value=\""._CASTMYVOTE."\"></center></form>";
themesidebox($ratetitle, $ratecontent);
$optiontitle = ""._OPTIONS."";
$optionbox = "<br>";
$optionbox .= "&nbsp;<img src='images/print.gif' border='0' alt='"._PRINTER."' title='"._PRINTER."'> <a href=\"index.php?name=$module_name&amp;file=print&amp;sid=$sid\">"._PRINTER."</a><br><br>";
if (is_user($user)) {
	$optionbox .= "&nbsp;<img src='images/friend.gif' border='0' alt='"._FRIEND."' title='"._FRIEND."'> <a href=\"index.php?name=$module_name&amp;file=friend&amp;op=FriendSend&amp;sid=$sid\">"._FRIEND."</a><br><br>";
}
$optionbox .= "</center>\n";
if (is_admin($admin)) {
	$optionbox .= "<center><b>"._ADMIN."</b><br><a href=\"".$admin_file.".php?op=adminStory\"><img src=\"images/add.gif\" alt=\""._ADD."\" title=\""._ADD."\" border=\"0\" width=\"17\" height=\"17\"></a>  <a href=\"".$admin_file.".php?op=EditStory&sid=$sid\"><img src=\"images/edit.gif\" alt=\""._EDIT."\" title=\""._EDIT."\" border=\"0\" width=\"17\" height=\"17\"></a>  <a href=\"".$admin_file.".php?op=RemoveStory&sid=$sid\"><img src=\"images/delete.gif\" alt=\""._DELETE."\" title=\""._DELETE."\" border=\"0\" width=\"17\" height=\"17\"></a><br><br></center>";
}
themesidebox($optiontitle, $optionbox);

echo "</td></tr></table>\n";
cookiedecode($user);

include("modules/$module_name/associates.php");

if (((empty($mode) OR ($mode != "nocomments")) OR ($acomm == 0)) OR ($articlecomm == 1)) {
	include("modules/News/comments.php");
}
include ("footer.php");

?>