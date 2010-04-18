<?php

/************************************************************/
/* Theme Colors Definition                                  */
/*                                                          */
/* bg1 "main cell background"                               */
/* bg2 "cell headers"                                       */
/* bg3 ?													*/
/* bg4 ?                                                    */
/* Define colors for your web site. $bgcolor2 is generaly   */
/* used for the tables border as you can see on OpenTable() */
/* function, $bgcolor1 is for the table background and the  */
/* other two bgcolor variables follows the same criteria.   */
/* $texcolor1 and 2 are for tables internal texts           */
/************************************************************/

$bgcolor1 = "#FFFFFF";
$bgcolor2 = "#F5F5F5";
$bgcolor3 = "#FFFFFF";
$bgcolor4 = "#FFFFFF";
$textcolor1 = "#323232";
$textcolor2 = "#323232";

include("themes/DAJ_Glass/tables.php");

/************************************************************/
/* Function themeheader()                                   */
/*                                                          */
/* Control the header for your site. You need to define the */
/* BODY tag and in some part of the code call the blocks    */
/* function for left side with: blocks(left);               */
/************************************************************/

function themeheader() {
    global  $admin, $user, $banners, $sitename, $slogan, $cookie, $prefix, $db, $siteurl, $sl_firstname, $sl_lastname, $name;
    cookiedecode($user);
    $firstname = $cookie[1];
	$lastname = $cookie[2];
    if ($firstname == "") {
        $firstname = $sl_firstname;
    }    
	if ($lastname == "") {
        $lastname = $sl_lastname;
    }
    echo "<body text=\"#323232\" topmargin=\"0\">";
   ?>
   <div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>   
   <?php    
	if ($firstname == $sl_firstname && $lastname == $sl_lastname) {
	$theuser = "<a href=\"modules.php?name=Your_Account\">"._LOGIN."</a> or <a href=\"modules.php?name=Your_Account&op=new_user\">"._BREG."</a>";
    } else {
	$theuser = ""._BWEL." $firstname $lastname!";
    }
    
                	    $datetime = "<script type=\"text/javascript\">\n\n"
	        ."<!--   // Array ofmonth Names\n"
	        ."var monthNames = new Array( \""._JANUARY."\",\""._FEBRUARY."\",\""._MARCH."\",\""._APRIL."\",\""._MAY."\",\""._JUNE."\",\""._JULY."\",\""._AUGUST."\",\""._SEPTEMBER."\",\""._OCTOBER."\",\""._NOVEMBER."\",\""._DECEMBER."\");\n"
	        ."var now = new Date();\n"
	        ."thisYear = now.getYear();\n"
	        ."if(thisYear < 1900) {thisYear += 1900}; // corrections if Y2K display problem\n"
	        ."document.write(monthNames[now.getMonth()] + \" \" + now.getDate() + \", \" + thisYear);\n"
	        ."// -->\n\n"
	        ."</script>";
	        
	$public_msg = public_message();
	$advertising = ads(0);
    $tmpl_file = "themes/DAJ_Glass/header.html";
    $thefile = implode("", file($tmpl_file));
    $thefile = addslashes($thefile);
    $thefile = "\$r_file=\"".$thefile."\";";
    eval($thefile);
    print $r_file;
    blocks(left); 
    $tmpl_file = "themes/DAJ_Glass/left_center.html";
    $thefile = implode("", file($tmpl_file));
    $thefile = addslashes($thefile);
    $thefile = "\$r_file=\"".$thefile."\";";
    eval($thefile);
    print $r_file;
}

/************************************************************/
/* Function themefooter()                                   */
/*                                                          */
/* Control the footer for your site. You don't need to      */
/* close BODY and HTML tags at the end. In some part call   */
/* the function for right blocks with: blocks(right);       */
/* Also, $index variable need to be global and is used to   */
/* determine if the page your're viewing is the Homepage or */
/* and internal one.                                        */
/************************************************************/

function themefooter() {
    global $index, $foot1, $foot2, $foot3, $foot4, $copyright, $totaltime, $footer_message;
    //if ($index == 1) {
	if (defined('INDEX_FILE')) {
	$tmpl_file = "themes/DAJ_Glass/center_right.html";
	$thefile = implode("", file($tmpl_file));
	$thefile = addslashes($thefile);
	$thefile = "\$r_file=\"".$thefile."\";";
	eval($thefile);
	print $r_file;
	blocks(right);
    }
                echo "</td></tr></table><center>\n";
        $footer_message = footmsg();
            echo "</center>\n";
// PLEASE DO NOT TOUCH THE NEXT LINE.
// YOU CAN ONLY ADD TO IT IF YOU MODIFY THIS THEME :-)
        echo "<center><br><font class=\"small\">Style Based on DAJ_Glass phpbb2 style by <a href=\"http://www.dumbassjones.com\" target=\"_blank\">Dustin Baccetti</a> Modified by <a href=\"http://www.dazzlecms.com\" target=\"_blank\">www.dazzlecms.com</a></font></center>";  
	echo "<br>\n";	
    $tmpl_file = "themes/DAJ_Glass/footer.html";
    $thefile = implode("", file($tmpl_file));
    $thefile = addslashes($thefile);
    $thefile = "\$r_file=\"".$thefile."\";";
    eval($thefile);
    print $r_file;

}

/************************************************************/
/* Function themeindex()                                    */
/*                                                          */
/* This function format the stories on the Homepage         */
/************************************************************/

function themeindex ($aid, $aid2, $firstname, $lastname, $time, $title, $counter, $topic, $thetext, $notes, $morelink, $topicname, $topicimage, $topictext) 
{
    global $sl_firstname, $sl_lastname, $tipath;
    if ($notes != "") {
	$notes = "<br><br><b>"._NOTE."</b> <i>$notes</i>\n";
    } else {
	$notes = "";
    }
    if ($aid == $firstname && $aid2 == $lastname) {
	$content = "$thetext$notes\n";
    } else {
	if($firstname != "" && $lastname != "") {
	    $content = "<a href=\"modules.php?name=Your_Account&amp;op=userinfo&amp;firstname=$firstname&amp;lastname=$lastname\">$firstname $lastname</a> ";
	} else {
	    $content = $sl_firstname.'&nbsp;' . $sl_lastname;
	}
	$content .= ""._WRITES." <i>\"$thetext\"</i>$notes\n";
    }
    $posted = ""._POSTEDBY." ";
    $posted .= get_author($aid, $aid2);
    $posted .= " "._ON." $time $timezone ($counter "._READS.")";
    $tmpl_file = "themes/DAJ_Glass/story_home.html";
    $thefile = implode("", file($tmpl_file));
    $thefile = addslashes($thefile);
    $thefile = "\$r_file=\"".$thefile."\";";
    eval($thefile);
    print $r_file;
}

/************************************************************/
/* Function themearticle()                                  */
/*                                                          */
/* This function format the stories on the story page, when */
/* you click on that "Read More..." link in the home        */
/************************************************************/

function themearticle ($aid, $aid2, $firstname, $lastname, $datetime, $title, $thetext, $topic, $topicname, $topicimage, $topictext) {
    global $admin, $sid, $tipath;
    $posted = ""._POSTEDON." $datetime "._BY." ";
    $posted .= get_author($aid, $aid2);
    if ($notes != "") {
	$notes = "<br><br><b>"._NOTE."</b> <i>$notes</i>\n";
    } else {
	$notes = "";
    }
    if ($aid == $firstname && $aid2 == $lastname) {
	$content = "$thetext$notes\n";
    } else {
	if($firstname != "" && $lastname != "") {
	    $content = "<a href=\"modules.php?name=Your_Account&amp;op=userinfo&amp;firstname=$firstname&amp;lastname=$lastname\">$firstname&nbsp;$lastname</a> ";
	} else {
	    $content = "$sl_firstname ";
	}
	$content .= ""._WRITES." <i>\"$thetext\"</i>$notes\n";
    }
    $tmpl_file = "themes/DAJ_Glass/story_page.html";
    $thefile = implode("", file($tmpl_file));
    $thefile = addslashes($thefile);
    $thefile = "\$r_file=\"".$thefile."\";";
    eval($thefile);
    print $r_file;
}

/************************************************************/
/* Function themesidebox()                                  */
/*                                                          */
/* Control look of your blocks. Just simple.                */
/************************************************************/

function themesidebox($title, $content) 
{
    $tmpl_file = "themes/DAJ_Glass/blocks.html";
    $thefile = implode("", file($tmpl_file));
    $thefile = addslashes($thefile);
    $thefile = "\$r_file=\"".$thefile."\";";
    eval($thefile);
    print $r_file;
}

?>
