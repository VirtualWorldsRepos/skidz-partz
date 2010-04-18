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

if (stristr(htmlentities($_SERVER['PHP_SELF']), "footer.php")) {
	Header("Location: index.php");
	die();
}

define('NUKE_FOOTER', true);

function footmsg() {
	global $licenced, $foot1, $foot2, $foot3, $copyright, $total_time, $start_time, $footmsg;
	$mtime = microtime();
	$mtime = explode(" ",$mtime);
	$mtime = $mtime[1] + $mtime[0];
	$end_time = $mtime;
	$total_time = ($end_time - $start_time);
	$total_time = _PAGEGENERATION." ".substr($total_time,0,4)." "._SECONDS;
	$footmsg = "<span class=\"footmsg\">\n";
	if (!empty($foot1)) {
		$footmsg .= $foot1."<br>\n";
	}
	if (!empty($foot2)) {
		$footmsg .= $foot2."<br>\n";
	}
	if (!empty($foot3)) {
		$footmsg .= $foot3."<br>\n";
	}
	// DO NOT REMOVE THE FOLLOWING COPYRIGHT LINE. YOU'RE NOT ALLOWED TO REMOVE NOR EDIT THIS.
	if(!$licenced)
	{
	   $footmsg .= $copyright."<br>$total_time<br>\n</span>\n";
	}
	else
	{
	   $footmsg .= $total_time."<br />\n</span>\n";
	}
	echo $footmsg;
}

function foot() {
	global $prefix, $user_prefix, $db, $index, $user, $cookie, $storynum, $user, $cookie, $Default_Theme, $foot1, $foot2, $foot3, $foot4, $home, $name, $admin;
	if(defined('HOME_FILE')) {
		blocks("Down");
	}
	if (basename($_SERVER['PHP_SELF']) != "index.php" AND defined('MODULE_FILE')) {
		$cpname = str_replace("_", " ", $name);
		echo "<div align=\"right\"><a href=\"javascript:openwindow()\">$cpname &copy;</a></div>";
	}
	if (basename($_SERVER['PHP_SELF']) != "index.php" AND defined('MODULE_FILE') AND (file_exists("modules/$name/admin/panel.php") && is_admin($admin))) {
		echo "<br>";
		Open_Table();
		include("modules/$name/admin/panel.php");
		Close_Table();
	}
	themefooter();
	if (file_exists("includes/custom_files/custom_footer.php")) {
		include_once("includes/custom_files/custom_footer.php");
	}
	echo "</body>\n</html>";
    ob_end_flush();
	die();
}

foot();

?>