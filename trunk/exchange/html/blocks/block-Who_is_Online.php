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

if ( !defined('BLOCK_FILE') ) {
    Header("Location: ../index.php");
    die();
}

global $user, $cookie, $prefix, $db, $user_prefix, $sl_firstname, $sl_lastname;

cookiedecode($user);
if (isset($_SERVER['REMOTE_ADDR'])) { $ip = $_SERVER['REMOTE_ADDR']; }
if (is_user($user))
{
    $firstname = $cookie[1];
	$lastname = $cookie[2];
    $guest = 0;
}
else
{
    //if (!empty($ip)) { 
      $firstname = $sl_firstname;
	  $lastname = $sl_lastname;
    //} else {
    //  $uname = "";
    //}
    $guest = 1;
}

$guest_online_num = $db->sql_numrows($db->sql_query("SELECT * FROM ".$prefix."_session WHERE guest='1'"));
$member_online_num = $db->sql_numrows($db->sql_query("SELECT * FROM ".$prefix."_session WHERE guest='0'"));

$who_online_num = $guest_online_num + $member_online_num;
$who_online = "<div align=\"center\"><span class=\"content\">"._CURRENTLY." $guest_online_num "._GUESTS." $member_online_num "._MEMBERS."<br>";

$content = "$who_online";

$row2 = $db->sql_fetchrow($db->sql_query("SELECT title FROM ".$prefix."_blocks WHERE bkey='online'"));
$title = filter($row2['title'], "nohtml");

if (is_user($user)) {
    $content .= "<br>"._YOUARELOGGED." <b>$firstname $lastname</b>.<br>";
    $content .= "</span></div>";
} else {
    $content .= "<br>"._YOUAREANON."</span></div>";
}

?>