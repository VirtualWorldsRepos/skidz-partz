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

// Position "1" by default is for block advertising. You can change this from your admin panel
$content = "<br>";
$content .= ads(1);
$content .= "<br>";

?>