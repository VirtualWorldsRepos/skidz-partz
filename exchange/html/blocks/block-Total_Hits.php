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

global $prefix, $startdate, $db;

$row = $db->sql_fetchrow($db->sql_query("SELECT count FROM ".$prefix."_counter WHERE type='total' AND var='hits'"));
$content = "<font class=\"tiny\"><center>"._WERECEIVED."<br><b><a href=\"index.php?name=Statistics\">$row[0]</a></b><br>"._PAGESVIEWS." $startdate</center></font>";

?>