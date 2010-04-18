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

$module_name = "Your_Account";
include_once("modules/$module_name/admin/language/lang-".$currentlang.".php");

switch($op) {

    case "mod_users":
    case "modifyUser":
    case "updateUser":
    case "delUser":
    case "delUserConf":
    case "addUser":
    include("modules/$module_name/admin/index.php");
    break;

}

?>