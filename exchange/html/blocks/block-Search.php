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

$content = "<form onSubmit=\"this.submit.disabled='true'\" action=\"index.php?name=Search\" method=\"post\">";
$content .= "<br><center><input type=\"text\" name=\"query\" size=\"15\">";
$content .= "<br><input type=\"submit\" value=\""._SEARCH."\"></center></form>";

?>