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

$module_name = "Marketplace";
include_once("modules/$module_name/admin/language/lang-".$currentlang.".php");


switch($op) {

	case "downloads":
	case "DownloadsDelNew":
	
	
	case "DownloadsAddDownload":
	case "DownloadsAddEditorial":
	case "DownloadsModEditorial":
	case "DownloadsDownloadCheck":
	case "DownloadsValidate":
	case "DownloadsDelEditorial":
	case "DownloadsCleanVotes":
	case "DownloadsListBrokenDownloads":
	case "DownloadsDelBrokenDownloads":
	case "DownloadsIgnoreBrokenDownloads":
	case "DownloadsListModRequests":
	case "DownloadsChangeModRequests":
	case "DownloadsChangeIgnoreRequests":

	case "DownloadsModDownloadS":
	case "DownloadsDelDownload":
	case "DownloadsDelVote":
	case "DownloadsDelComment":
	
	case "check_download":
	
	case "Marketplace":
	case "settings":
	case "informative":
	case "save_informative":
	case "marketplace_category":
	case "category_save":
	case "subcategory_save":
	case "marketplace_modcategory":
	case "modify_category":
	case "delete_category":
	case "update_category":
	case "transfer_categories":
	case "transfer_save":
	case "modify_items":
	case "marketplace_item":
	case "save_item":
	
	include("modules/$module_name/admin/index.php");
	break;

}

?>