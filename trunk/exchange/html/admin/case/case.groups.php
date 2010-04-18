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

if (!defined('ADMIN_FILE')) {
	die ("Access Denied");
}

switch($op) {

    case "Groups":
    case "grp_add":
    case "grp_edit":
    case "grp_edit_save":
    case "grp_del":
    case "points_update":
    include("admin/modules/groups.php");
    break;

}

?>