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
require_once('mainfile.php');
require_once('modules/'.$module_name.'/includes/functions.php');
get_lang($module_name);
$pagetitle = "- "._STARTED."";
define('INDEX_FILE', true);

function index()
{
		global $prefix, $db, $admin_file;
		include ("header.php");
	    menu(false);
        echo "<br />";
	    Merchants_Menu();
	    echo "<br />";
	    echo "<table width=\"100%\" valign=\"top\"><tr><td valign='top'>";		
		//GraphicAdmin();
		//options_menu("footer");
		Open_Table();
		$row = $db->sql_fetchrow($db->sql_query("SELECT message1  from ".$prefix."_marketplace_messages"));
		$started = filter($row['message1'], "nohtml", 1);
		echo "<center><font class='option'><b>"._STARTED."</b></font></center>"
			."<table border=\"0\" align=\"center\" cellpadding=\"3\"><tr><td>"
			."&nbsp;</td><td>" . stripslashes($started) . ""
			."</td></tr><tr><td>&nbsp;</td><td halign=\"left\"></td></tr></table>";		
	    Close_Table();
	    echo "</td>";	
	    categories(0);		
}

switch($op) 
{
	
	default:
    index();
    break;
}
?>