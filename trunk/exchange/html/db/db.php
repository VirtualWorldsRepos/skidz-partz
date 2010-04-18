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

if (stristr($_SERVER['PHP_SELF'], "db.php")) {
    Header("Location: index.php");
    die();
}

if (defined('FORUM_ADMIN')) {
    $the_include = "../../../db";
} elseif (defined('INSIDE_MOD')) {
    $the_include = "../../db";
} else {
    $the_include = "db";
}

switch($dbtype) {

	case 'MySQL':
		include("".$the_include."/mysql.php");
		break;

	case 'mysql4':
		include("".$the_include."/mysql4.php");
		break;

	case 'sqlite':
		include("".$the_include."/sqlite.php");
		break;

	case 'postgres':
		include("".$the_include."/postgres7.php");
		break;

	case 'mssql':
		include("".$the_include."/mssql.php");
		break;

	case 'oracle':
		include("".$the_include."/oracle.php");
		break;

	case 'msaccess':
		include("".$the_include."/msaccess.php");
		break;

	case 'mssql-odbc':
		include("".$the_include."/mssql-odbc.php");
		break;
	
	case 'db2':
		include("".$the_include."/db2.php");
		break;

}

$db = new sql_db($dbhost, $dbuname, $dbpass, $dbname, false);
if(!$db->db_connect_id) {
    die("<br><br><center><img src=images/logo.gif><br><br><b>There seems to be a problem with the $dbtype server, sorry for the inconvenience.<br><br>We should be back shortly.</center></b>");
}

?>