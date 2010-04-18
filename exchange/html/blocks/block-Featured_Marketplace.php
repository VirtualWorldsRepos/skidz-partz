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

$content = '<table border="0" width="100%"><tr>';
global $db, $prefix;
$numrows = $db->sql_numrows($db->sql_query("SELECT * FROM ".$prefix."_marketplace_items"));
if ($numrows > 1) {
    $result = $db->sql_query("SELECT category FROM ".$prefix."_marketplace_items");
    while ($row = $db->sql_fetchrow($result)) {
	$itemsid = intval($row['category']);
	$items_array .= "$itemsid-";
    }
    $r_items = explode("-",$items_array);
    mt_srand((double)microtime()*1000000);
    $numrows = $numrows-1;
    $items = mt_rand(0, $numrows);
    $items = $r_items[$items];
} else {
    $items = 1;
}
$result2 = $db->sql_query("SELECT id, title, lindens, thumbnail FROM ".$prefix."_marketplace_items WHERE enhancements = '6' AND category = '".$items."' order by title limit 0,8");
while(list($id, $title, $lindens, $thumbnail) = $db->sql_fetchrow($result2))
{
$content .= '<td align="center" style="max-width: 33%" width="33%">
	<a href="index.php?name=Marketplace&amp;mode=view_details&amp;lid='.$id.'" target="_parent"><img src="'.$thumbnail.'"><br />
		<b>'.$title.'<br />
		L$'.$lindens.'</b></a></td>';
}	
$content .= '</tr></table>';		
?>