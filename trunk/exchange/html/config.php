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

if (stristr(htmlentities($_SERVER['PHP_SELF']), "config.php")) {
	   Header("Location: index.php");
    die();
}

######################################################################
# Database & System Config
#
# dbhost:       SQL Database Hostname
# dbuname:      SQL Username
# dbpass:       SQL Password
# dbname:       SQL Database Name
# $prefix:      Your Database table's prefix
# $user_prefix: Your Users' Database table's prefix (To share it)
# $dbtype:      Your Database Server type. Supported servers are:
#               MySQL, mysql4, sqlite, postgres, mssql, oracle,
#               msaccess, db2 and mssql-odbc
#               Be sure to write it exactly as above, case SeNsItIvE!
# $sitekey:	Security Key. CHANGE it to whatever you want, as long
#               as you want. Just don't use quotes.
# $subscription_url: If you manage subscriptions on your site, you
#                    must write here the url of the subscription
#                    information/renewal page. This will send by
#                    email if set.
# $admin_file: Administration panel filename. "admin" by default for
#   		   "admin.php". To improve security please rename the file
#              "admin.php" and change the $admin_file value to the
#              new filename (without the extension .php)
######################################################################

$dbhost = "localhost";
$dbuname = "database_username";
$dbpass = "database_password";
$dbname = "database_name";
$prefix = "secondlife";
$user_prefix = "secondlife";
$dbtype = "MySQL";
$sitekey = "F89dxgMudWlXpu0AgYiIOj2kSITl1cwpGJd8DEaY";
$subscription_url = "";
$admin_file = "admin";

/**********************************************************************/
/* You finished to configure the Database. Now you can change all     */
/* you want in the Administration Section.   To enter just launch     */
/* your web browser pointing it to http://xxxxxx.xxx/admin.php        */
/* (Change xxxxxx.xxx to your domain name, for example: smeego.com)  */
/*                                                                    */
/* Remember to go to Preferences section where you can configure your */
/* new site. In that menu you can change all you need to change.      */
/*                                                                    */
/* Congratulations! now you have an automated news portal!            */
/* Thanks for choose Smeego: The Future of the Web                  */
/**********************************************************************/

// DO NOT TOUCH ANYTHING BELOW THIS LINE UNTIL YOU KNOW WHAT YOU'RE DOING

$prefix = empty($user_prefix) ? $prefix : $user_prefix;
$reasons = array("As Is","Offtopic","Flamebait","Troll","Redundant","Insighful","Interesting","Informative","Funny","Overrated","Underrated");
$badreasons = 4;
/* If you don't want to use the IMG tag in your HTML code, to include images in News for example, you must use the following line and comment out the old $AllowableHTML */
/* $AllowableHTML = array("font"=>3,"b"=>1,"i"=>1,"strike"=>1,"div"=>2,"u"=>1,"a"=>2,"em"=>1,"br"=>1,"strong"=>1,"blockquote"=>1,"tt"=>1,"li"=>1,"ol"=>1,"ul"=>1); */
/* or just remove "img"=>2 in the $AllowableHTML array bellow. THIS FEATURE HAS POSSIBLE SECURITY ISSUES */
$AllowableHTML = array("img"=>2,"font"=>3,"b"=>1,"i"=>1,"strike"=>1,"div"=>2,"u"=>1,"a"=>2,"em"=>1,"br"=>1,"strong"=>1,"blockquote"=>1,"tt"=>1,"li"=>1,"ol"=>1,"ul"=>1);
//$AllowableHTML = array("img"=>2,"b"=>1,"i"=>1,"strike"=>1,"div"=>2,"u"=>1,"a"=>2,"em"=>1,"br"=>1,"strong"=>1,"blockquote"=>1,"tt"=>1,"li"=>1,"ol"=>1,"ul"=>1);

$CensorList = array("fuck","cunt","fucker","fucking","pussy","cock","c0ck","cum","twat","clit","bitch","fuk","fuking","motherfucker");
$tipath = "images/topics/";
?>