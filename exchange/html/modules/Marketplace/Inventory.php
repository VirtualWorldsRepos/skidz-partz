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
$pagetitle = "- "._MY_INVENTORY."";
define('INDEX_FILE', true);

function index()
{
    global $db, $prefix, $cookie, $user, $module_name, $sitename;
    if (is_user($user)) 
	{
		$user2 = base64_decode($user);
		$user2 = addslashes($user2);
	   	$cookie = explode(":", $user2);		
		cookiedecode($user);
		$uid = $cookie[0];
		//$firstname = $cookie[1];
		//$lastname = $cookie[2];
		include("header.php");
		menu(false);
        echo "<br />";
	    Merchants_Menu();
	    echo "<br />";
 	    echo "<table width=\"100%\" valign=\"top\"><tr><td valign='top'>";		
    	Open_Table();
		$ThemeSel = get_theme();
		echo'<center><table border="0" cellpadding="0" cellspacing="0"><tr><td><font class="title">'._MY_INVENTORY.'</font>
	     	 <a href="'._HELP_LINK.'"><img src="themes/'.$ThemeSel.'/images/icon_help.gif" border="0" align="middle" title="'._MARKETPLACE_HELP.'" /></a>
	         <br /><br />'._MISSING_LISTED.'<br /><br />
	         <table border="0" cellspacing="0" cellpadding="1" width="100%">
		     <tr><td valign="top" width="50%">
		     <span class="bold">'._MARKETPLACE_UNASSOCIATED.':</span><br />
	         '.LISTED_INVENTORY.'	<br /><br />
	         <center>';
		$products = $db->sql_query("SELECT id, inventory FROM ".$prefix."_marketplace_items WHERE firstname='".$cookie[1]."' AND lastname='".$cookie[2]."' ORDER BY inventory");
		while(list($search_id, $search_inventory) = $db->sql_fetchrow($products))
		{
		   $unassociated_products = $db->sql_query("SELECT * FROM ".$prefix."_marketplace_magicbox_products WHERE firstname='".$cookie[1]."' AND lastname='".$cookie[2]."' AND product_name = '".$search_inventory."' ORDER BY product_name");
           $numrows2 = $db->sql_numrows($unassociated_products);		
		   if ($numrows2 == false)
		   {
		      echo '<tr bgcolor="#EAEAEA"><td>
		            <image src="themes/'.$ThemeSel.'/images/inventory/missing.png"/><b>&nbsp;'.$search_inventory.'&nbsp;</b>
		            </td></tr><tr bgcolor=""><td>';
		   }
		}
	
		      echo '</center>
                    </td><td width=1 bgcolor="#999999">
                    </td><td valign="top" width="50%">
	                <span class="bold red">'.UNASSOCIATED_INVENTORY.':</span><br />'.INVENTORY_MSG1.'&nbsp;'.$sitename.'&nbsp;'.INVENTORY_MSG2.'<br /><br />
                    <center><table border=0 cellspacing=0 cellpadding=3><tr bgcolor="#EAEAEA"><td><b>'.INVENTORY_NAME.'</b></td></tr>';    
		      $unassociated_servers = $db->sql_query("SELECT * FROM ".$prefix."_marketplace_magicbox_products WHERE firstname='".$cookie[1]."' AND lastname='".$cookie[2]."' AND product_name like \"%".$inventory2."%\" AND product_active = '0' ORDER BY product_name");
		      $numrows = $db->sql_numrows($unassociated_servers);
		      if ($numrows == true)
		      {
			     while ($row = $db->sql_fetchrow($unassociated_servers)) 
			     {
				    $product_name3 = $row["product_name"];
				    $product_type3 = $row["product_type"];
				    echo '<tr bgcolor=""><td><image src="themes/'.$ThemeSel.'/images/inventory/'.$product_type3.'.png"/>&nbsp;'.$product_name3.'</td></tr>';
			     }
		      }
		      echo '</table></center></td></tr><tr><td width="50%">&nbsp;
	                </td><td width=1 bgcolor="#999999"></td><td width="50%">&nbsp;
		            </td></tr><tr><td height=1 width="50%" bgcolor="#999999">
		            </td><td width=1 bgcolor="#999999">
		            </td><td height=1 width="50%" bgcolor="#999999"></td></tr><tr><td width="50%">&nbsp;
		            </td><td width=1 bgcolor="#999999">
		            </td><td width="50%">&nbsp;
		            </td></tr><tr><td valign="top" width="50%">
		            <font class="bold">'.REGISTERED_SERVERS.':</font><br />
		            '.INVENTORY_MSG3.'.<ul>
	                <li><i>'.SERVER_STATUS_ACTIVE.'</i>&nbsp;'.SERVER_AVAILABLE.'.&nbsp;<li><i>'.SERVER_STATUS_INACTIVE.'</i>&nbsp;'.INVENTORY_MSG4.'.&nbsp;'.INVENTORY_MSG5.'.</ul>
	                <center>
	                <table border=0 cellspacing=0 cellpadding=3>
	                <tr bgcolor="#EAEAEA"><td><b>'.INVENTORY_NAME.'</b>
	                </td><td nowrap align="center"><b>'.INVENTORY_DESC.'</b>
	                </td><td><b>'.INVENTORY_LOCATION.'</b>
	                </td><td><b>'.INVENTORY_STATUS.'</b>
	                </td><td>&nbsp;</td></tr>';
		  
		      $servers = $db->sql_query("SELECT id, server_key, server_url, server_location, server_name, server_desc, server_status FROM ".$prefix."_marketplace_magicbox WHERE firstname='".$cookie[1]."' AND lastname='".$cookie[2]."' ORDER BY server_name");
		      while(list($id, $server_key, $server_url, $server_location, $server_name, $server_desc, $server_status) = $db->sql_fetchrow($servers))
		      {
		         if($server_status == false) {$status_text = SERVER_STATUS_INACTIVE;}
			     if($server_status == true) {$status_text = SERVER_STATUS_ACTIVE;}
			     echo '<tr bgcolor=""><td>'.$server_name.'</td>
			           <td nowrap align="center">'.$server_desc.'</td>
			           <td><a href="javascript:popUp(\''.$server_location.'\')">'.INVENTORY_MAP.'</td>
			           <td>'.$status_text.'</td>
			           <td><a href="index.php?name='.$module_name.'&file=Inventory&amp;op=remove_serverid&amp;sid='.$id.'">'.INVENTORY_REMOVE.'</a></td>';
		      }			
			     echo '</tr></table></center></td><td width=1 bgcolor="#999999"></td><td valign="top" width="50%">
                       <font class="bold">'.ACTIVE_INVENTORY.':</font><br />'.INVENTORY_DELIVERY.'.<br /><br /><center>
			           <table border=0 cellspacing=0 cellpadding=3><tr bgcolor="#EAEAEA"><td><b>Name</b></td></tr><tr bgcolor=""><td>';
			     $server_products = $db->sql_query("SELECT id, avatar_key, server_key, product_name, product_type, product_date FROM ".$prefix."_marketplace_magicbox_products WHERE firstname='".$cookie[1]."' AND lastname='".$cookie[2]."' AND product_active = '1' ORDER BY product_name");
			     while(list($id, $avatar_key, $server_key, $product_name, $product_type, $product_date) = $db->sql_fetchrow($server_products))
			     {
			        $products = $db->sql_query("SELECT id, inventory FROM ".$prefix."_marketplace_items WHERE inventory='".$product_name."' ORDER BY inventory");
				    while(list($id, $inventory) = $db->sql_fetchrow($products))
				    {
				       echo '<tr bgcolor="#EAEAEA"><td>
				             <image src="themes/'.$ThemeSel.'/images/inventory/'.$product_type.'.png"/><b>&nbsp;'.$inventory.'&nbsp;</b>
			                 </td></tr><tr bgcolor=""><td>';
				    }
			     }
			     echo '</table></center></td></tr></table></td></tr></table>';	
			     Close_Table();
	             echo "</td>";	
	             categories(0);
	}	
}

function remove_serverid($sid, $ok = 0)
{
   global $prefix, $db, $user, $module_name;  
   $sid = intval($sid);
   if($user)
   {
      if($ok == 1) 
      {
         $servers = $db->sql_query("SELECT id, server_key FROM ".$prefix."_marketplace_magicbox WHERE id='".$sid."' ORDER BY server_name");
         while(list($id, $server_key) = $db->sql_fetchrow($servers))
         {
	       $id = intval($id);
	       $db->sql_query("DELETE FROM " . $prefix . "_marketplace_magicbox WHERE id='".$id."'");
	       $db->sql_query("DELETE FROM " . $prefix . "_marketplace_magicbox_products WHERE server_key='".$server_key."'");
         }
	     Header('Location: index.php?name='.$module_name.'.php?&amp;file=Inventory');
      }
      else
      {
         $servers2 = $db->sql_query("SELECT id, server_key FROM ".$prefix."_marketplace_magicbox WHERE id='".$sid."' ORDER BY server_name");
		 while ($row3 = $db->sql_fetchrow($servers2)) 
		 {
	        $id2 = intval($row3['id']);
			$server_key = filter($row3['server_key'], "nohtml");
			$servers3 = $db->sql_query("SELECT * from " . $prefix . "_marketplace_magicbox_products where server_key='".$server_key."'");
			$link = $db->sql_numrows($servers3);
		 }
         include("header.php");
         Open_Table();
		 echo '<br /><center><font class="option">';
		 echo '<b>' . _EZTHEREIS . ' '.$link.' ' . _ITEMS. ' ' . _ATTACHEDTOSERVER . '</b><br />';
		 echo '<b>' . _DELEZSERVERWARNING . '</b><br /><br />'; 			
      }
	  echo '[ <a href="index.php?name='.$module_name.'&amp;file=Inventory&amp;op=remove_serverid&amp;sid='.$sid.'&amp;ok=1">' . _YES . '</a> | <a href="index.php?name='.$module_name.'&amp;file=Inventory">' . _NO . '</a> ]<br /><br />';
	  Close_Table();
	  include("footer.php");
   }	
}

switch($op) 
{
    case "remove_serverid":
	remove_serverid($sid, $ok);
	break;
	
	default:
    index();
    break;
}

?>