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
    die("You can't access this file directly...");
}

if (!is_user($user)) {
  exit("Access Denied");
}

	require_once("mainfile.php");
	get_lang("Your_Account");
	
	function menuimg($gfile) {
	    $ThemeSel = get_theme();
	    if (file_exists("themes/$ThemeSel/images/menu/$gfile")) {
			$menuimg = "themes/$ThemeSel/images/menu/$gfile";
	    } else {
			$menuimg = "modules/Your_Account/images/$gfile";
	    }
	    return($menuimg);
	}
	
	function nav($main_up=0) {
	    global $module_name, $articlecomm, $db, $prefix;
		$row = $db->sql_fetchrow($db->sql_query("SELECT overwrite_theme from ".$prefix."_config"));
		$overwrite_theme = intval($row['overwrite_theme']);
		$thmcount = 0;
	    $handle=opendir('themes');
	    while ($file = readdir($handle)) {
			if ( (!ereg("[.]",$file)) ) {
				$thmcount++;
			}
	    }
	    closedir($handle);
	    echo "<table border=\"0\" width=\"100%\" align=\"center\"><tr><td width=\"10%\">";
/*
	    $menuimg = menuimg("info.gif");
	    echo "<font class=\"content\">"
		."<center><a href=\"index.php?name=Your_Account&amp;op=edituser\"><img src=\"$menuimg\" border=\"0\" alt=\""._CHANGEYOURINFO."\" title=\""._CHANGEYOURINFO."\"></a><br>"
		."<a href=\"index.php?name=Your_Account&amp;op=edituser\">"._CHANGEYOURINFO."</a>"
		."</center></font></td>";
*/		
	    // Preferences
	    $menuimg = menuimg('preferences.gif');
	    echo '<font class="content">
	    <center><a href="index.php?name='.$module_name.'&amp;op=edituser"><img src="'.$menuimg.'" border="0" alt="'._CHANGEYOURINFO.'" title="'._CHANGEYOURINFO.'"></a><br />
	    <a href="index.php?name='.$module_name.'&amp;op=edituser">'._CHANGEYOURINFO.'</a>
	    </center></font></td>';		

	    // Comments		
	    if ($articlecomm == 1) 
	    {
	    $menuimg = menuimg('comments.gif');
	    echo '<td width="10%"><font class="content">
	        <center><a href="index.php?name='.$module_name.'&amp;op=editcomm"><img src="'.$menuimg.'" border="0" alt="'._CONFIGCOMMENTS.'" title="'._CONFIGCOMMENTS.'"></a><br>
	        <a href="index.php?name='.$module_name.'&amp;op=editcomm">'._CONFIGCOMMENTS.'</a>
	        </center></form></font></td>';
	    }
		
//
	    // Funds
	    $menuimg = menuimg('funds.gif');
	    echo '<td width="10%"><font class="content">
	    <center><a href="index.php?name='.$module_name.'&amp;op=fundsmain"><img src="'.$menuimg.'" border="0" alt="'._YOURFUNDS.'" title="'._YOURFUNDS.'"></a><br />
	    <a href="index.php?name='.$module_name.'&amp;op=fundsmain">'._YOURFUNDS.'</a>
	    </center></form></font></td>';

	    // Subscriptions
/*		
	    $menuimg = menuimg('subscriptions.gif');
	    echo '<td width="10%"><font class="content">
	    <center><a href="index.php?name='.$module_name.'&amp;op=subscriptions"><img src="'.$menuimg.'" border="0" alt="'._YOURSUBSCRIPTIONS.'" title="'._YOURSUBSCRIPTIONS.'"></a><br />
	    <a href="index.php?name='.$module_name.'&amp;op=subscriptions">'._YOURSUBSCRIPTIONS.'</a>
	    </center></form></font></td>';
*/	
	    // Exchange
		/*
		we add this in another version maybe ...
	    $menuimg = menuimg('exchange.gif');
	    echo '<td width="10%"><font class="content">
	    <center><a href="index.php?name=Currency&amp;file=orders"><img src="'.$menuimg.'" border="0" alt="'._YOUREXCHANGE.'" title="'._YOUREXCHANGE.'"></a><br />
	    <a href="index.php?name=Currency&amp;file=orders">'._YOUREXCHANGE.'</a>
	    </center></form></font></td>';*/

	    // Items
	    $menuimg = menuimg('items.gif');
	    echo '<td width="10%"><font class="content">
	    <center><a href="index.php?name=Marketplace&amp;file=Edit"><img src="'.$menuimg.'" border="0" alt="'._YOURITEMS.'" title="'._YOURITEMS.'"></a><br />
	    <a href="index.php?name=Marketplace&amp;file=Edit">'._YOURITEMS.'</a>
	    </center></form></font></td>';
	
	    // Inventory
	    $menuimg = menuimg('inventory.gif');
	    echo '<td width="10%"><font class="content">
	    <center><a href="index.php?name=Marketplace&amp;file=Inventory"><img src="'.$menuimg.'" border="0" alt="'._YOURINVENTORY.'" title="'._YOURINVENTORY.'"></a><br />
	    <a href="index.php?name=Marketplace&amp;file=Inventory">'._YOURINVENTORY.'</a>
	    </center></form></font></td>';

	    // Purchases will be added in update
		/*
	    $menuimg = menuimg('purchases.gif');
	    echo '<td width="10%"><font class="content">
	    <center><a href="index.php?name='.$module_name.'&amp;op=purchasehistory"><img src="'.$menuimg.'" border="0" alt="'._YOURPURCHASES.'" title="'._YOURPURCHASES.'"></a><br />
	    <a href="index.php?name='.$module_name.'&amp;op=purchasehistory">'._YOURPURCHASES.'</a>
	    </center></form></font></td>';
	*/
	    // Sales
	    $menuimg = menuimg('sales.gif');
	    echo '<td width="10%"><font class="content">
	    <center><a href="index.php?name=Marketplace&amp;file=Sales"><img src="'.$menuimg.'" border="0" alt="'._YOURSALES.'" title="'._YOURSALES.'"></a><br />
	    <a href="index.php?name=Marketplace&amp;file=Sales">'._YOURSALES.'</a>
	    </center></form></font></td>';
	
	    $menuimg = menuimg('traffic.gif');
	    echo '<td width="10%"><font class="content">
	    <center><a href="index.php?name=Marketplace&file=Traffic"><img src="'.$menuimg.'" border="0" alt="'._YOURTRAFFIC.'" title="'._YOURTRAFFIC.'"></a><br>
	    <a href="index.php?name=Marketplace&file=Traffic">'._YOURTRAFFIC.'</a>
	    </center></form></font></td>';

/*		we add this in another version maybe ...
	    $menuimg = menuimg('estate.gif');
	    echo '<td width="10%"><font class="content">
	    <center><a href="index.php?name=Marketplace&file=itemstats"><img src="'.$menuimg.'" border="0" alt="'._YOURESTATE.'" title="'._YOURESTATE.'"></a><br>
	    <a href="index.php?name=Marketplace&file=itemstats">'._YOURESTATE.'</a>
	    </center></form></font></td>';
*/		
//		
/*
        //@todo merge with pref
	    $menuimg = menuimg("home.gif");
	    echo "<td width=\"10%\"><font class=\"content\">"
		."<center><a href=\"index.php?name=Your_Account&amp;op=edithome\"><img src=\"$menuimg\" border=\"0\" alt=\""._CHANGEHOME."\" title=\""._CHANGEHOME."\"></a><br>"
		."<a href=\"index.php?name=Your_Account&amp;op=edithome\">"._CHANGEHOME."</a>"
		."</center></form></font></td>";
*/
/*		
	    if ($articlecomm == 1) {
			$menuimg = menuimg("comments.gif");
			echo "<td width=\"10%\"><font class=\"content\">"
			    ."<center><a href=\"index.php?name=Your_Account&amp;op=editcomm\"><img src=\"$menuimg\" border=\"0\" alt=\""._CONFIGCOMMENTS."\" title=\""._CONFIGCOMMENTS."\"></a><br>"
			    ."<a href=\"index.php?name=Your_Account&amp;op=editcomm\">"._CONFIGCOMMENTS."</a>"
			    ."</center></form></font></td>";
	    }
*/
		$menuimg = menuimg('exit.gif');
		echo '<td width="10%"><font class="content">
		<center><a href="index.php?name='.$module_name.'&amp;op=logout"><img src="'.$menuimg.'" border="0" alt="'._LOGOUTEXIT.'" title="'._LOGOUTEXIT.'"></a><br>
		<a href="index.php?name='.$module_name.'&amp;op=logout">'._LOGOUTEXIT.'</a>
		</center></form></font>';
		
	    echo "</td></tr></table>";
	    if ($main_up != 1) {
		echo "<br><center>[ <a href=\"index.php?name=Your_Account\">"._RETURNACCOUNT."</a> ]</center>\n";
	    }
}

?>