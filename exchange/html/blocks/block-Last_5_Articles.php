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

global $prefix, $multilingual, $currentlang, $db;

if ($multilingual == 1) {
    $querylang = "WHERE (alanguage='$currentlang' OR alanguage='')";
} else {
    $querylang = "";
}
$content = "<table width=\"100%\" border=\"0\">";
$result = $db->sql_query("SELECT sid, title, comments, counter FROM " . $prefix . "_stories $querylang ORDER BY sid DESC LIMIT 0,5");
while ($row = $db->sql_fetchrow($result)) {
    $sid = intval($row['sid']);
    $title = filter($row['title'], "nohtml");
    $comtotal = intval($row['comments']);
    $counter = intval($row['counter']);
    $content .= "<tr><td align=\"left\"><strong><big>&middot;</big></strong>&nbsp;<a href=\"index.php?name=News&amp;file=article&amp;sid=$sid\">$title</a></td><td align=\"right\">[ $comtotal "._COMMENTS." - $counter "._READS." ]</td></tr>";
}
$content .= "</table>";
$content .= "<br><center>[ <a href=\"index.php?name=News\">"._MORENEWS."</a> ]</center>";

?>