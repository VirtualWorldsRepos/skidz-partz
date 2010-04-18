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

######################################################################
# Downloads Preferences (Some variables are valid also for Downloads)
#
# $perpage:      	    	How many downloads to show on each page?
# $popular:      	    	How many hits need a download to be listed as popular?
# $newdownloads:     	    	How many downloads to display in the New Downloads Page?
# $topdownloads:     	    	How many downloads to display in The Best Downloads Page? (Most Popular)
# $downloadsresults: 	    	How many downloads to display on each search result page?
# $downloads_anonadddownloadlock:   	Lock Unregistered users from Suggesting New Downloads? (1=Yes 0=No)
# $anonwaitdays:        	Number of days anonymous users need to wait to vote on a download
# $outsidewaitdays:     	Number of days outside users need to wait to vote on a download (checks IP)
# $useoutsidevoting:        	Allow Webmasters to put vote downloads on their site (1=Yes 0=No)
# $anonweight:          	How many Unregistered User vote per 1 Registered User Vote?
# $outsideweight:       	How many Outside User vote per 1 Registered User Vote?
# $detailvotedecimal:       	Let Detailed Vote Summary Decimal out to N places. (no max)
# $mainvotedecimal:     	Let Main Vote Summary Decimal show out to N places. (max 4)
# $topdownloadspercentrigger:   	1 to Show Top Downloads as a Percentage (else # of downloads)
# $topdownloads:            	Either # of downloads OR percentage to show (percentage as whole number. #/100)
# $mostpopdownloadspercentrigger:	1 to Show Most Popular Downloads as a Percentage (else # of downloads)
# $mostpopdownloads:        	Either # of downloads OR percentage to show (percentage as whole number. #/100)
# $featurebox:          	1 to Show Feature Download Box on downloads Main Page? (1=Yes 0=No)
# $downloadvotemin:         	Number votes needed to make the 'top 10' list
# $blockunregmodify:        	Block unregistered users from suggesting downloads changes? (1=Yes 0=No)
# $show_links_num:              Show the number of links for each category? (1=Yes 0=No)
######################################################################

$anonweight = 10;
$perpage = 7;
$popular = 10;
$anonwaitdays = 1;
// popular
$mostpopularpercentrigger = 1;
$mostpopular = 25;
$percentage = '.1';
$enhancements_price6 = "2899";
$enhancements_price5 = "999";
$enhancements_price4 = "899";
$enhancements_price3 = "299";
$enhancements_price2 = "299";
$enhancements_price1 = "99";
$enhancements_price0 = "0";
?>