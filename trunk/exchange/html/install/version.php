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

$version_number = "1.0";
$filedate = filemtime("version.php");
$filedate = date("F d, Y", $filedate);
$version = "Skidz Partz - Exchange $version_number (Release Date: $filedate)";

?>