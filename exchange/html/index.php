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

require_once("mainfile.php");
global $prefix, $db, $admin_file;

if (isset($name) && $name == $_REQUEST['name']) {
	define('MODULE_FILE', true);
	$name = addslashes(trim($name));
	$modstring = strtolower($_SERVER['QUERY_STRING']);
	global $smeegouser, $prefix, $user;
	  if (is_user($user)) {
	    $smeegouser = base64_decode($user);
	    $smeegouser = addslashes($smeegouser);
	  } else {
	    $smeegouser = "";
	  }
	  $result = $db->sql_query("SELECT active, view FROM ".$prefix."_modules WHERE title='".addslashes($name)."'");
	  list($mod_active, $view) = $db->sql_fetchrow($result);
	  $mod_active = intval($mod_active);
	  $view = intval($view);
	  if (($mod_active == 1) OR ($mod_active == 0 AND is_admin($admin))) {
	    if (!isset($mop) OR $mop != $_REQUEST['mop']) $mop="modload";
	    if (!isset($file) OR $file != $_REQUEST['file']) $file="index";
	    if (stripos_clone($file,"..") OR stripos_clone($mop,"..")) die("You are so cool...");
	    $ThemeSel = get_theme();
	    if (file_exists("themes/$ThemeSel/modules/$name/".$file.".php")) {
	      $modpath = "themes/$ThemeSel/";
	    } else {
	      $modpath = "";
	    }
	    if ($view == 0) {
	      $modpath .= "modules/$name/".$file.".php";
	      if (file_exists($modpath)) {
	        include($modpath);
	      } else {
	        include("header.php");
	        Open_Table();
	        echo "<br><center>Sorry, such file doesn't exist...</center><br>";
	        Close_Table();
	        include("footer.php");
	      }


	    } elseif ($view == 1) {
	    	if (is_user($user) AND is_group($user, $name) OR is_admin($admin)) {
	      		$modpath .= "modules/$name/".$file.".php";
	      		if (file_exists($modpath)) {
	        		include($modpath);
	      		} else {
	        		include("header.php");
	        		Open_Table();
	        		echo "<br><center>Sorry, such file doesn't exist...</center><br>";
	        		Close_Table();
	        		include("footer.php");
	      		}
	    	} else {
	      		$pagetitle = "- "._ACCESSDENIED;
			    include("header.php");
			    title($sitename.": "._ACCESSDENIED);
			    Open_Table();
			    echo "<center><strong>"._RESTRICTEDAREA."</strong><br><br>"._MODULEUSERS;
			    $result2 = $db->sql_query("SELECT mod_group FROM ".$prefix."_modules WHERE title='".addslashes($name)."'");
			    list($mod_group) = $db->sql_fetchrow($result2);
			    if ($mod_group != 0) {
			    	$result3 = $db->sql_query("SELECT name FROM ".$prefix."_groups WHERE id='".intval($mod_group)."'");
			        $row3 = $db->sql_fetchrow($result3);
			        echo _ADDITIONALYGRP.": <b>".$row3['name']."</b><br><br>";
			    }
			    echo _GOBACK;
			    Close_Table();
			    include("footer.php");
			}
	    } elseif ($view == 2 AND is_admin($admin)) {
	      $modpath .= "modules/$name/".$file.".php";
	      if (file_exists($modpath)) {
	        include($modpath);
	      } else {
	        include("header.php");
	        Open_Table();
	        echo "<br><center>Sorry, such file doesn't exist...</center><br>";
	        Close_Table();
	        include("footer.php");
	      }
	    } elseif ($view == 2 AND !is_admin($admin)) {
	      $pagetitle = "- "._ACCESSDENIED;
	      include("header.php");
	      title($sitename.": "._ACCESSDENIED);
	      Open_Table();
	      echo "<center><b>"._RESTRICTEDAREA."</b><br><br>"._MODULESADMINS.""._GOBACK;
	      Close_Table();
	      include("footer.php");
	    } elseif ($view == 3 AND paid() OR is_admin($admin)) {
	      $modpath .= "modules/$name/".$file.".php";
	      if (file_exists($modpath)) {
	        include($modpath);
	      } else {
	        include("header.php");
	        Open_Table();
	        echo "<br><center>Sorry, such file doesn't exist...</center><br>";
	        Close_Table();
	        include("footer.php");
	      }
	    } else {
	      $pagetitle = "- "._ACCESSDENIED."";
	      include("header.php");
	      title($sitename.": "._ACCESSDENIED."");
	      Open_Table();
	      echo "<center><strong>"._RESTRICTEDAREA."</strong><br><br>"._MODULESSUBSCRIBER;
	      if (!empty($subscription_url)) echo "<br>"._SUBHERE;
	      echo "<br><br>"._GOBACK;
	      Close_Table();
	      include("footer.php");
	    }
	  } else {
	    include("header.php");
	    Open_Table();
	    echo "<center>"._MODULENOTACTIVE."<br><br>"._GOBACK."</center>";
	    Close_Table();
	    include("footer.php");
	  }	
}

if (isset($op) AND ($op == "ad_click") AND isset($bid)) {
	$bid = intval($bid);
	$sql = "SELECT clickurl FROM ".$prefix."_banner WHERE bid='$bid'";
	$result = $db->sql_query($sql);
	list($clickurl) = $db->sql_fetchrow($result);
	$clickurl = filter($clickurl, "nohtml");
	$db->sql_query("UPDATE ".$prefix."_banner SET clicks=clicks+1 WHERE bid='$bid'");
	update_points(21);
	Header("Location: ".addslashes($clickurl));
	die();
}

$modpath = '';
define('MODULE_FILE', true);
$_SERVER['PHP_SELF'] = "index.php";
$row = $db->sql_fetchrow($db->sql_query("SELECT main_module from ".$prefix."_main"));
$name = $row['main_module'];
define('HOME_FILE', true);

if (isset($url) AND is_admin($admin)) {
	$url = urldecode($url);
	echo "<meta http-equiv=\"refresh\" content=\"0; url=$url\">";
	die();
}

if ($httpref == 1) {
    if (isset($_SERVER['HTTP_REFERER'])) {
    $referer = $_SERVER['HTTP_REFERER'];
    $referer = check_html($referer, "nohtml");
    if (eregi("smeego_", $referer) && eregi("into", $referer) && eregi("from", $referer)) {
    	$referer = "";
    }
    }
    if (!empty($referer) && !stripos_clone($referer, "unknown") && !stripos_clone($referer, "bookmark") && !stripos_clone($referer, $_SERVER['HTTP_HOST'])) {
    $result = $db->sql_query("INSERT INTO ".$prefix."_referer VALUES (NULL, '".addslashes($referer)."')");
    }
    $numrows = $db->sql_numrows($db->sql_query("SELECT * FROM ".$prefix."_referer"));
    if($numrows>=$httprefmax) {
        $result2 = $db->sql_query("DELETE FROM ".$prefix."_referer");
    }
}
if (!isset($mop)) { $mop="modload"; }
if (!isset($mod_file)) { $mod_file="index"; }
$name = trim($name);
if (isset($file)) { $file = trim($file); }
$mod_file = trim($mod_file);
$mop = trim($mop);
if (stripos_clone($name,"..") || (isset($file) && stripos_clone($file,"..")) || stripos_clone($mod_file,"..") || stripos_clone($mop,"..")) {
	die("You are so cool...");
} else {
	$ThemeSel = get_theme();
	if (file_exists("themes/$ThemeSel/module.php")) {
		include("themes/$ThemeSel/module.php");
		if (is_active("$default_module") AND file_exists("modules/$default_module/".$mod_file.".php")) {
			$name = $default_module;
		}
	}
	if (file_exists("themes/$ThemeSel/modules/$name/".$mod_file.".php")) {
		$modpath = "themes/$ThemeSel/";
	}
	$modpath .= "modules/$name/".$mod_file.".php";
	if (file_exists($modpath)) {
		include($modpath);
	} else {
		define('INDEX_FILE', true);
		include("header.php");
		Open_Table();
		if (is_admin($admin)) {
			echo "<center><font class=\"\"><b>"._HOMEPROBLEM."</b></font><br><br>[ <a href=\"".$admin_file.".php?op=modules\">"._ADDAHOME."</a> ]</center>";
		} else {
			echo "<center>"._HOMEPROBLEMUSER."</center>";
		}
		Close_Table();
		include("footer.php");
	}
}

?>