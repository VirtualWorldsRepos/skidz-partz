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
$row = $db->sql_fetchrow($db->sql_query("SELECT radminsuper FROM " . $prefix . "_authors WHERE aid='$aid' AND aid2='$aid2'"));
if ($row['radminsuper'] == 1) {

	/*********************************************************/
	/* Referer Functions to know who links us                */
	/*********************************************************/

	function hreferer() {
		global $bgcolor2, $prefix, $db, $admin_file;
		include ("header.php");
		GraphicAdmin();
		Open_Table();
		echo "<center><font class=\"title\"><b>" . _HTTPREFERERS . "</b></font></center>";
		Close_Table();
		echo "<br>";
		Open_Table();
		echo "<center><b>" . _WHOLINKS . "</b></center><br><br>"
			."<table border=\"0\" width=\"100%\">";
		$row = $db->sql_fetchrow($db->sql_query("SELECT httprefmode from ".$prefix."_config"));
		$httprefmode = intval($row['httprefmode']);
		$result = $db->sql_query("SELECT rid, url from " . $prefix . "_referer");
		while ($row = $db->sql_fetchrow($result)) {
			$rid = intval($row['rid']);
			$url = filter($row['url'], "nohtml");
			$url2 = urlencode($url);
			$title = $url;
			if ($httprefmode == 1) {
				$url = explode("/", $url);
				$url = "http://$url[2]";
			}
			echo "<tr><td bgcolor=\"$bgcolor2\"><font class=\"content\">$rid</td>"
				."<td bgcolor=\"$bgcolor2\"><font class=\"content\"><a href=\"index.php?url=$url2\" target=\"_new\" title=\"$title\">$url</a></td></tr>";
		}
		echo "</table>"
			."<form action=\"".$admin_file.".php\" method=\"post\">"
			."<input type=\"hidden\" name=\"op\" value=\"delreferer\">"
			."<center><input type=\"submit\" value=\"" . _DELETEREFERERS . "\"></center>";
		Close_Table();
		include ("footer.php");
	}

	function delreferer() {
		global $prefix, $db, $admin_file;
		$db->sql_query("delete from " . $prefix . "_referer");
		Header("Location: ".$admin_file.".php?op=adminMain");
	}

	switch($op) {

		case "hreferer":
		hreferer();
		break;

		case "delreferer":
		delreferer();
		break;

	}

} else {
	echo "Access Denied";
}
?>