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

	case "options_menu":
	case "general":
	case "savegeneral":
	case "paypal":
	case "savepaypal":
	case "metatags":
	case "savemetatags":
	case "themes":
	case "savethemes":
	case "users":
	case "saveusers":
	case "comments":
	case "savecomments":
	case "languages":
	case "savelanguages":
	case "footer":
	case "savefooter":
	case "backend":
	case "savebackend":
	case "referers":
	case "savereferers":
	case "mailing":
	case "savemailing":
	case "other":
	case "saveother":
	case "Configure":
	include("admin/modules/settings.php");
	break;

}

?>