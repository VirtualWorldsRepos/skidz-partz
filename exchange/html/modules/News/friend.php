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
if (stripos_clone($_SERVER['QUERY_STRING'],'%25')) header("Location: index.php");
$module_name = basename(dirname(__FILE__));
get_lang($module_name);
$pagetitle = "- "._RECOMMEND."";

if (!is_user($user)) {
	Header("Location: index.php?name=$module_name&file=article&sid=$sid");
	die();	
}

function FriendSend($sid) {
	global $user, $cookie, $prefix, $db, $user_prefix, $module_name;
	$sid = intval($sid);
	if(!isset($sid)) { exit(); }
	include ("header.php");
	$row = $db->sql_fetchrow($db->sql_query("SELECT title FROM ".$prefix."_stories WHERE sid='$sid'"));
	$title = filter($row['title'], "nohtml");
	title(""._FRIEND."");
	Open_Table();
	echo "<center><font class=\"content\"><b>"._FRIEND."</b></font></center><br><br>"
	.""._YOUSENDSTORY." <b>$title</b> "._TOAFRIEND."<br><br>"
	."<form action=\"index.php?name=$module_name&amp;file=friend\" method=\"post\">"
	."<input type=\"hidden\" name=\"sid\" value=\"$sid\">";
	if (is_user($user)) {
		$row2 = $db->sql_fetchrow($db->sql_query("SELECT firstname, lastname, user_email FROM ".$user_prefix."_users WHERE user_id = '".intval($cookie[0])."'"));
		$firstname = filter($row2['firstname'], "nohtml");
		$lastname = filter($row2['lastname'], "nohtml");
		$ye = filter($row2['user_email'], "nohtml");
	}
	echo "<b>"._YOURFIRSTNAME." </b> $firstname <input type=\"hidden\" name=\"yfirstname\" value=\"$firstname\"><br /><br />\n"
	."<b>"._YOURLASTNAME." </b> $lastname <input type=\"hidden\" name=\"ylastname\" value=\"$lastname\"><br /><br /><br />\n"
	."<b>"._FYOUREMAIL." </b> $ye <input type=\"hidden\" name=\"ymail\" value=\"$ye\"><br /><br /><br />\n"
	."<b>"._FFRIENDFIRSTNAME." </b> <input type=\"text\" name=\"ffirstname\"><br /><br />\n"
	."<b>"._FFRIENDLASTNAME." </b> <input type=\"text\" name=\"flastname\"><br /><br />\n"
	."<b>"._FFRIENDEMAIL." </b> <input type=\"text\" name=\"fmail\"><br /><br />\n"
	."<input type=\"hidden\" name=\"op\" value=\"SendStory\">\n"
	."<input type=\"submit\" value="._SEND.">\n"
	."</form>\n";
	Close_Table();
	include ('footer.php');
}

function SendStory($sid, $yfirstname, $ylastname, $ymail, $ffirstname, $flastname, $fmail) {
	global $sitename, $site_url, $prefix, $db, $module_name;
	$ffirstname = removecrlf(filter($ffirstname, "nohtml"));
	$flastname = removecrlf(filter($flastname, "nohtml"));
	$fmail = removecrlf(filter($fmail, "nohtml"));
	$yfirstname = removecrlf(filter($yfirstname, "nohtml"));
	$ylastname = removecrlf(filter($ylastname, "nohtml"));
	$ymail = removecrlf(filter($ymail, "nohtml"));
	$sid = intval($sid);
	$row = $db->sql_fetchrow($db->sql_query("SELECT title, time, topic FROM ".$prefix."_stories WHERE sid='$sid'"));
	$title = filter($row['title'], "nohtml");
	$time = $row['time'];
	$topic = intval($row['topic']);
	$row2 = $db->sql_fetchrow($db->sql_query("SELECT topictext FROM ".$prefix."_topics WHERE topicid='$topic'"));
	$topictext = filter($row2['topictext'], "nohtml");
	$subject = ""._INTERESTING." $sitename";
	$message = ""._HELLO." $ffirstname $flastname:\n\n"._YOURFRIEND." $yfirstname $ylastname "._CONSIDERED."\n\n\n$title\n("._FDATE." $time)\n"._FTOPIC." $topictext\n\n"._URL.": $site_url/index.php?name=$module_name&file=article&sid=$sid\n\n"._YOUCANREAD." $sitename\n$site_url";
	mail($fmail, $subject, $message, "From: \"$yfirstname $ylastname\" <$ymail>\nX-Mailer: PHP/" . phpversion());
	update_points(6);
	$title = urlencode($title);
	$ffirstname = urlencode($ffirstname);
	$flastname = urlencode($flastname);
	Header("Location: index.php?name=$module_name&file=friend&op=StorySent&title=$title&ffirstname=$ffirstname&flastname=$flastname");
}

function StorySent($title, $ffirstname, $flastname) {
	include ("header.php");
	$title = filter($title, "nohtml");
	$ffirstname = filter($ffirstname, "nohtml");
	$flastname = filter($flastname, "nohtml");
	Open_Table();
	echo "<center><font class=\"content\">"._FSTORY." <b>$title</b> "._HASSENT." $ffirstname... "._THANKS."</font></center>";
	Close_Table();
	include ("footer.php");
}

switch($op) {

	case "SendStory":
	SendStory($sid, $yfirstname, $ylastname, $ymail, $ffirstname, $flastname, $fmail);
	break;

	case "StorySent":
	StorySent($title, $ffirstname, $flastname);
	break;

	case "FriendSend":
	FriendSend($sid);
	break;

}

?>