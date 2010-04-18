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

if (stristr(htmlentities($_SERVER['PHP_SELF']), "meta.php")) {
    Header("Location: ../index.php");
    die();
}

global $sitename, $slogan, $meta_keywords;
##################################################
# Include for Meta Tags generation               #
##################################################

$metastring = "<META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset="._CHARSET."\">\n";
$metastring .= "<META HTTP-EQUIV=\"EXPIRES\" CONTENT=\"0\">\n";
$metastring .= "<META NAME=\"RESOURCE-TYPE\" CONTENT=\"DOCUMENT\">\n";
$metastring .= "<META NAME=\"DISTRIBUTION\" CONTENT=\"GLOBAL\">\n";
$metastring .= "<META NAME=\"AUTHOR\" CONTENT=\"$sitename\">\n";
$metastring .= "<META NAME=\"COPYRIGHT\" CONTENT=\"Copyright (c) by $sitename\">\n";
$metastring .= "<META NAME=\"KEYWORDS\" CONTENT=\"$meta_keywords\">\n";
$metastring .= "<META NAME=\"DESCRIPTION\" CONTENT=\"$slogan\">\n";
$metastring .= "<META NAME=\"ROBOTS\" CONTENT=\"INDEX, FOLLOW\">\n";
$metastring .= "<META NAME=\"REVISIT-AFTER\" CONTENT=\"1 DAYS\">\n";
$metastring .= "<META NAME=\"RATING\" CONTENT=\"GENERAL\">\n";

###############################################
# DO NOT REMOVE THE FOLLOWING COPYRIGHT LINE! #
# YOU'RE NOT ALLOWED TO REMOVE NOR EDIT THIS. #
###############################################

// DO NOT REMOVE THE FOLLOWING CODE LINE. ACCORDING WITH THE GPL LICENSE SECTION 2(C) YOU'RE NOT ALLOWED TO REMOVE IT.
// PLAY FAIR AND SUPPORT THE DEVELOPMENT, PLEASE!
$metastring .= "<META NAME=\"GENERATOR\" CONTENT=\"Skidz Partz - Exchange Copyright (c) 2010 by Dazzle Development Team. This is free software, and you may redistribute it under the GPL (http://www.dazzlemods.com/files/gpl.txt). Skidz Partz - Exchange comes with absolutely no warranty, for details, see the license (http://www.dazzlemods.com/files/gpl.txt).\">\n";

echo $metastring;

?>