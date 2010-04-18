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
# the Free Software Foundation; either version 3 or later of the License.
######################################################################

if (!defined('MODULE_FILE')) {
    die ("You can't access this file directly...");
}

if (isset($min)) {
    $min = intval($min);
}

if (isset($show)) {
    $show = intval($show);
}

define('INDEX_FILE', true);
require_once("mainfile.php");
$module_name = basename(dirname(__FILE__));
get_lang($module_name);
$pagetitle = "- "._MARKETPLACE."";
require_once("modules/$module_name/config.php");
define('INDEX_FILE', true);
if (isset($slurl) && (!preg_match('#^http[s]?\\:\\/\\/[a-z0-9\-]+\.([a-z0-9\-]+\.)?[a-z]+#i', $slurl))) {
    die("Illegal url...");
}

function menu() 
{
    global $prefix, $module_name, $query;
    Open_Table();
    $ThemeSel = get_theme();
    if (file_exists('themes/'.$ThemeSel.'/images/terminal-logo.gif')) 
	{
	   echo '<br /><center><a href="index.php?name='.$module_name.'"><img src="themes/'.$ThemeSel.'/images/terminal-logo.gif" border="0" alt="'._TERMINAL_LOCATIONS.'"></a><br /><br />';
    } 
	else 
	{
	   echo '<br /><center><a href="index.php?name='.$module_name.'"><img src="modules/'.$module_name.'/images/terminal-logo.gif" border="0" alt="'._TERMINAL_LOCATIONS.'"></a><br /><br />';
    }
    echo '<form action="index.php?name='.$module_name.'&amp;op=search&amp;query='.$query.'" method="post">
	<font class="content"><input type="text" size="25" name="query"> <input type="submit" value="'._SEARCH.'"></font>
	</form></font></center>';
    Close_Table();
}

function atm_locations()
{
    global $prefix, $db, $show_links_num, $module_name;
    include('header.php');
	menu();
    echo '<br />';
    Open_Table();
    echo '<center><font class="title"><b>'._ATM_TYPE_0.'</b></font></center><br />
          <table border="0" cellspacing="10" cellpadding="0" align="center"><tr>';
	$sql = "SELECT id, location, slurl FROM ".$prefix."_terminals WHERE type = '0' ORDER BY location";
	$result = $db->sql_query($sql);
    $count = 0;
    while ($row = $db->sql_fetchrow($result)) 
	{
	   $id = intval($row['id']);
	   $location = filter($row['location'], "nohtml");
	   $slurl = filter($row['slurl']);
	echo '<td><font class="option"><strong><big>&middot;</big></strong> <a href="'.$slurl.'"><b>'.$location.'</b></a></font>';
	$space = 0;
    $cnum = "";
	if ($count<1) 
	{
	    echo '</td><td>&nbsp;&nbsp;&nbsp;&nbsp;</td>';
	    $dum = 1;
	}
	$count++;
	if ($count==2) 
	{
	    echo '</td></tr><tr>';
	    $count = 0;
	    $dum = 0;
	}
    }
    if ($dum == 1) 
	{
	   echo '</tr></table>';
    } 
	elseif ($dum == 0) 
	{
	   echo '<td></td></tr></table>';
    }
    echo '<center><font class="title"><b>'._ATM_TYPE_1.'</b></font></center><br />
          <table border="0" cellspacing="10" cellpadding="0" align="center"><tr>';
	$sql = "SELECT id, location, slurl FROM ".$prefix."_terminals WHERE type = '1' ORDER BY location";
	$result = $db->sql_query($sql);
    $count = 0;
    while ($row = $db->sql_fetchrow($result)) 
	{
	   $id = intval($row['id']);
	   $location = filter($row['location'], "nohtml");
	   $slurl = filter($row['slurl']);
	echo '<td><font class="option"><strong><big>&middot;</big></strong> <a href="'.$slurl.'"><b>'.$location.'</b></a></font>';
	$space = 0;
    $cnum = "";
	if ($count<1) 
	{
	    echo '</td><td>&nbsp;&nbsp;&nbsp;&nbsp;</td>';
	    $dum = 1;
	}
	$count++;
	if ($count==2) 
	{
	    echo '</td></tr><tr>';
	    $count = 0;
	    $dum = 0;
	}
    }
    if ($dum == 1) 
	{
	   echo '</tr></table>';
    } 
	elseif ($dum == 0) 
	{
	   echo '<td></td></tr></table>';
    }
    echo '<center><font class="title"><b>'._ATM_TYPE_2.'</b></font></center><br />
          <table border="0" cellspacing="10" cellpadding="0" align="center"><tr>';
	$sql = "SELECT id, location, slurl FROM ".$prefix."_terminals WHERE type = '2' ORDER BY location";
	$result = $db->sql_query($sql);
    $count = 0;
    while ($row = $db->sql_fetchrow($result)) 
	{
	   $id = intval($row['id']);
	   $location = filter($row['location'], "nohtml");
	   $slurl = filter($row['slurl']);
	echo '<td><font class="option"><strong><big>&middot;</big></strong> <a href="'.$slurl.'"><b>'.$location.'</b></a></font>';
	$space = 0;
    $cnum = "";
	if ($count<1) 
	{
	    echo '</td><td>&nbsp;&nbsp;&nbsp;&nbsp;</td>';
	    $dum = 1;
	}
	$count++;
	if ($count==2) 
	{
	    echo '</td></tr><tr>';
	    $count = 0;
	    $dum = 0;
	}
    }
    if ($dum == 1) 
	{
	   echo '</tr></table>';
    } 
	elseif ($dum == 0) 
	{
	   echo '<td></td></tr></table>';
    }
    echo '<center><font class="title"><b>'._ATM_TYPE_3.'</b></font></center><br />
          <table border="0" cellspacing="10" cellpadding="0" align="center"><tr>';
	$sql = "SELECT id, location, slurl FROM ".$prefix."_terminals WHERE type = '3' ORDER BY location";
	$result = $db->sql_query($sql);
    $count = 0;
    while ($row = $db->sql_fetchrow($result)) 
	{
	   $id = intval($row['id']);
	   $location = filter($row['location'], "nohtml");
	   $slurl = filter($row['slurl']);
	echo '<td><font class="option"><strong><big>&middot;</big></strong> <a href="'.$slurl.'"><b>'.$location.'</b></a></font>';
	$space = 0;
    $cnum = "";
	if ($count<1) 
	{
	    echo '</td><td>&nbsp;&nbsp;&nbsp;&nbsp;</td>';
	    $dum = 1;
	}
	$count++;
	if ($count==2) 
	{
	    echo '</td></tr><tr>';
	    $count = 0;
	    $dum = 0;
	}
    }
    if ($dum == 1) 
	{
	   echo '</tr></table>';
    } 
	elseif ($dum == 0) 
	{
	   echo '<td></td></tr></table>';
    }	
    Close_Table();
    include("footer.php");
}

function convertorderbyin($orderby) {
	if ($orderby != "locationA" AND $orderby != "slurlA" AND $orderby != "locationD" AND $orderby != "slurlD") {
	    Header("Location: index.php");
	    die();
	}
    if ($orderby == "locationA")	$orderby = "location ASC";
    if ($orderby == "slurlA")	$orderby = "slurl ASC";
    if ($orderby == "locationD")	$orderby = "location DESC"; 
    if ($orderby == "slurlD")	$orderby = "slurl DESC";
    return $orderby;
}

function convertorderbyout($orderby) {
	if ($orderby != "location ASC" AND $orderby != "slurl ASC" AND $orderby != "slurl DESC" AND $orderby != "location DESC") {
	    Header("Location: index.php");
	    die();
	}
    if ($orderby == "location ASC")		$orderby = "locationA";
    if ($orderby == "slurl ASC")			$orderby = "slurlA";
    if ($orderby == "location DESC")		$orderby = "locationD";
    if ($orderby == "slurl DESC")		$orderby = "slurlD";
    return $orderby;
}

function search($query, $min, $orderby, $show) 
{
    global $prefix, $db, $admin, $bgcolor2, $module_name, $admin_file, $datetime, $transfertitle, $locale;
    include('modules/'.$module_name.'/config.php');
    include('header.php');
    if (!isset($min)) $min = 0;
    if (!isset($max)) $max = $min + $search_results;
    if(!empty($orderby)) 
	{
	   $orderby = convertorderbyin($orderby);
    } 
	else 
	{
	   $orderby = "title ASC";
    }
    if ($show!="") 
	{
	   $search_results = $show;
    } 
	else 
	{
	   $show = $search_results;     
    }
    $query1 = filter($query, "nohtml", 1);
    $query1 = addslashes($query1);
	$query2 = filter($query, "", 1);
    if(!is_numeric($min))
	{
       $min=0;
    }
    $result = $db->sql_query("SELECT id, uuid, location, slurl FROM ".$prefix."_terminals WHERE location LIKE '%$query1%' OR slurl LIKE '%$query2%' ORDER BY $orderby LIMIT $min, $search_results");
    $fullcountresult = $db->sql_query("SELECT * FROM ".$prefix."_terminals WHERE location LIKE '%$query1%' OR slurl LIKE '%$query2%' ");
    $totalselecteddownloads = $db->sql_numrows($fullcountresult);
    $nrows = $db->sql_numrows($result);
    $x=0;
    $the_query = filter($query, "nohtml");
    $the_query = FixQuotes($the_query);
    menu(1);
    echo "<br />";
    Open_Table();
    if (!empty($query)) 
	{
    	if ($nrows>0) 
		{
	    while(list($id, $uuid, $location, $slurl) = $db->sql_fetchrow($result)) 
		{
            $id = intval($id);
			$location = filter($location, "nohtml");
			$slurl = filter($slurl, "nohtml");	    
			$transfertitle = str_replace (" ", "_", $location);
			$location = ereg_replace($query1, "<b>$query1</b>", $location);
    		global $prefix, $db, $admin;
		    echo "<img src=\"modules/$module_name/images/lwin.gif\" border=\"0\" alt=\"\">&nbsp;&nbsp;";
		    echo '<a href="'.$slurl.'">'.$location.'</a><br />';
		$x++;
	    }
	    echo "</font>";
    	    $orderby = convertorderbyout($orderby);
	} 
	else 
	{
	    echo "<br /><br /><center><font class=\"option\"><b>"._NOMATCHES."</b></font><br /><br />"._GOBACK."<br /></center>";
	}
	
    /* Calculates how many pages exist.  Which page one should be on, etc... */
    $downloadpagesint = ($totalselecteddownloads / $search_results);			
    $downloadpageremainder = ($totalselecteddownloads % $search_results);		
    if ($downloadpageremainder != 0) 
	{					 
    	$downloadpages = ceil($downloadpagesint);				
        if ($totalselecteddownloads < $search_results) 
		{
    	    $downloadpageremainder = 0;
		}
    } 
	else 
	{
    	$downloadpages = $downloadpagesint;
    } 
	
    /* Page Numbering */
    if ($downloadpages!=1 && $downloadpages!=0) 
	{
	   echo "<br /><br />"._SELECTPAGE.": ";
	   $prev = $min - $search_results;
	   
	   if ($prev>=0) 
	   {
    	    echo "&nbsp;&nbsp;<b>[ <a href=\"index.php?name=$module_name&amp;op=search&amp;query=$the_query&amp;min=$prev&amp;orderby=$orderby&amp;show=$show\">"
    		." &lt;&lt; "._PREVIOUS."</a> ]</b> ";
       }
	   $counter = 1;
       $currentpage = ($max / $search_results);
       while ($counter<=$downloadpages ) 
	   {
    	    $cpage = $counter;
            $mintemp = ($perpage * $counter) - $search_results;
            if ($counter == $currentpage) 
			{
		       echo "<b>$counter</b> ";
	        } 
			else 
			{
		       echo "<a href=\"index.php?name=$module_name&amp;op=search&amp;query=$the_query&amp;min=$mintemp&amp;orderby=$orderby&amp;show=$show\">$counter</a> ";
	        }
            $counter++; 	
        }    	
        $next = $min + $search_results;
        if ($x >= $perpage) 
		{
    	    echo "&nbsp;&nbsp;<b>[ <a href=\"index.php?name=$module_name&amp;op=search&amp;query=$the_query&amp;min=$max&amp;orderby=$orderby&amp;show=$show\">"
    		." "._NEXT." &gt;&gt;</a> ]</b>";
        }
    }
    echo "<br /><br /><center><font class=\"content\">"
	.""._TRY2SEARCH." \"$the_query\" "._INOTHERSENGINES."<br />"
	."<a target=\"_blank\" href=\"http://www.altavista.com/cgi-bin/query?pg=q&amp;sc=on&amp;hl=on&amp;act=2006&amp;par=0&amp;q=$the_query&amp;kl=XX&amp;stype=stext\">Alta Vista</a> - "
	."<a target=\"_blank\" href=\"http://search.yahoo.com/bin/search?p=$the_query\">Yahoo</a> - "
	."<a target=\"_blank\" href=\"http://www.google.com/search?q=$the_query\">Google</a>"
	."</font>";
    } 
	else 
	{
	   echo "<center><font class=\"option\"><b>"._NOMATCHES."</b></font></center><br /><br />";
    }
    Close_Table();
    include("footer.php");
}

if (!(isset($op))) { $op = ""; }
if (!(isset($min))) { $min = 0; }
if (!(isset($orderby))) { $orderby = ""; }
if (!(isset($show))) { $show = ""; }

switch($op) {

    case "search":
    search($query, $min, $orderby, $show);
    break;

    default:
    atm_locations();
    break;

}

?>