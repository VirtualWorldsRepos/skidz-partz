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

$module_name = "Surveys";
include_once("modules/$module_name/admin/language/lang-".$currentlang.".php");

switch($op) {

    case "create":
    case "createPosted":
    case "ChangePoll":
    case "remove":
    case "removePosted":
    case "polledit":
    case "savepoll":
    case "polledit_select":
    include("modules/$module_name/admin/index.php");
    break;

}

?>