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

if (!defined('MODULE_FILE')) {
	die ("You can't access this file directly...");
}

$module_name = basename(dirname(__FILE__));
require("modules/".$module_name."/config.php");  
require_once("mainfile.php");

$outsidevotes = 0;
$anonvotes = 0;
$outsidevoteval = 0;
$anonvoteval = 0;
$regvoteval = 0;	
$truecomments = $totalvotesDB;
while($vrow = $db->sql_fetchrow($voteresult)) 
{
    $ratingDB = intval($vrow['rating']);
    $rating_firstname = $vrow['firstname'];
	$rating_lastname = $vrow['lastname'];
    $ratingcommentsDB = filter($vrow['comments']);
    if ($ratingcommentsDB=="") 
	{ 
	   $truecomments--;
	}
    if ($rating_firstname == $sl_firstname && $rating_lastname == $sl_lastname) 
	{
	   $anonvotes++;
	   $anonvoteval += $ratingDB;
    }
    if ($rating_firstname != $sl_firstname && $rating_lastname != $sl_firstname) 
	{ 
	   $regvoteval += $ratingDB;
    }
}
$regvotes = $totalvotesDB - $anonvotes - $outsidevotes;
if ($totalvotesDB == 0) 
{ 
    $finalrating = 0;
} 
else 
{
    /* Registered User vs. Anonymous vs. Outside User Weight Calutions */
    $impact = $anonweight;
    if ($regvotes == 0) 
	{
	   $regvotes = 0;
    } 
	else 
	{ 
	   $avgRU = $regvoteval / $regvotes;
    }
    if ($anonvotes == 0) 
	{
	   $avgAU = 0;
    } 
	else 
	{
	   $avgAU = $anonvoteval / $anonvotes;
    }
    $impactRU = $regvotes;
    $impactAU = $anonvotes / $impact;
    $finalrating = ($avgRU * $impactRU) + ($avgAU * $impactAU) / ($impactRU + $impactAU + $impactOU);
    $finalrating = number_format($finalrating, 4); 
}

?>