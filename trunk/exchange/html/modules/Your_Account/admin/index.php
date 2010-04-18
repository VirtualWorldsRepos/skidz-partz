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

global $prefix, $db, $admin_file;
$aid = substr($aid, 0,31);
$aid2 = substr($aid2, 0,31);
$row = $db->sql_fetchrow($db->sql_query("SELECT title, admins FROM ".$prefix."_modules WHERE title='Your_Account'"));
$row2 = $db->sql_fetchrow($db->sql_query("SELECT name, radminsuper FROM ".$prefix."_authors WHERE aid='".$aid."' AND aid2='".$aid2."'"));
$admins = explode(",", $row['admins']);
$auth_user = 0;
for ($i=0; $i < sizeof($admins); $i++) {
	if ($row2['name'] == $admins[$i] AND !empty($row['admins'])) {
		$auth_user = 1;
	}
}

if ($row2['radminsuper'] == 1 || $auth_user == 1) {

	/*********************************************************/
	/* Users Functions                                       */
	/*********************************************************/

	function displayUsers() {
		global $admin, $admin_file;
		include("header.php");
		GraphicAdmin();
		Open_Table();
		echo "<center><font class=\"title\"><b>" . _USERADMIN . "</b></font></center>";
		Close_Table();
		echo "<br />";
		Open_Table();
		echo "<center><font class=\"option\"><b>" . _EDITUSER . "</b></font><br><br>"
		."<form method=\"post\" action=\"".$admin_file.".php\">"
		."<b>" . _FIRSTNAME . ": </b> <input type=\"text\" name=\"firstname\" size=\"20\">\n"
		."<b>" . _LASTNAME . ": </b> <input type=\"text\" name=\"lastname\" size=\"20\">\n"
		."<select name=\"op\">"
		."<option value=\"modifyUser\">" . _MODIFY . "</option>\n"
		."<option value=\"delUser\">" . _DELETE . "</option></select>\n"
		."<input type=\"submit\" value=\"" . _OK . "\"></form></center>";
		Close_Table();
		echo "<br>";
		Open_Table();
		echo "<center><font class=\"option\"><b>" . _ADDUSER . "</b></font><br><br>"
		."<form action=\"".$admin_file.".php\" method=\"post\">"
		."<table border=\"0\" width=\"100%\">"
		."<tr><td width=\"100\">" . _FIRSTNAME . "</td>"
		."<td><input type=\"text\" name=\"add_firstname\" size=\"30\" maxlength=\"25\"> <font class=\"tiny\">" . _REQUIRED . "</font></td></tr>"
		."<tr><td width=\"100\">" . _LASTNAME . "</td>"
		."<td><input type=\"text\" name=\"add_lastname\" size=\"30\" maxlength=\"25\"> <font class=\"tiny\">" . _REQUIRED . "</font></td></tr>"
		."<tr><td>" . _NAME . "</td>"
		."<td><input type=\"text\" name=\"add_name\" size=\"30\" maxlength=\"50\"></td></tr>"
		."<tr><td>" . _EMAIL . "</td>"
		."<td><input type=\"text\" name=\"add_email\" size=\"30\" maxlength=\"60\"> <font class=\"tiny\">" . _REQUIRED . "</font></td></tr>"
		."<tr><td>" . _FAKEEMAIL . "</td>"
		."<td><input type=\"text\" name=\"add_femail\" size=\"30\" maxlength=\"60\"></td></tr>"
		."<tr><td>" . _URL . "</td>"
		."<td><input type=\"text\" name=\"add_url\" size=\"30\" maxlength=\"60\"></td></tr>"
		."<tr><td>" . _NEWSLETTER . "</td>"
		."<td><input type=\"radio\" name=\"add_newsletter\" value=\"1\">" . _YES . "<br>"
		."<input type=\"radio\" name=\"add_newsletter\" value=\"0\" checked>" . _NO . "</td></tr>"
		."<tr><td>" . _CURRENCY . "</td>"
		."<td><input type=\"text\" name=\"add_currency\" size=\"30\" maxlength=\"60\"></td></tr>"
		."<tr><td>" . _DOLLARS . "</td>"
		."<td><input type=\"text\" name=\"add_dollars\" size=\"30\" maxlength=\"60\"></td></tr>"		
		."<tr><td>" . _SIGNATURE . "</td>"
		."<td><textarea name=\"add_user_sig\" rows=\"15\" cols=\"70\"></textarea></td></tr>"
		."<tr><td>" . _PASSWORD . "</td>"
		."<td><input type=\"password\" name=\"add_pass\" size=\"12\" maxlength=\"12\"> <font class=\"tiny\">" . _REQUIRED . "</font></td></tr>"
		."<input type=\"hidden\" name=\"op\" value=\"addUser\">"
		."<tr><td><input type=\"submit\" value=\"" . _ADDUSERBUT . "\"></form></td></tr>"
		."</table>";
		Close_Table();
		include("footer.php");
	}

	function modifyUser($firstname, $lastname) 
	{
		echo $firstname . ' ' . $lastname;
		global $prefix, $user_prefix, $db, $admin_file;
		include("header.php");
		GraphicAdmin();
		Open_Table();
		echo "<center><font class=\"title\"><b>" . _USERADMIN . "</b></font></center>";
		Close_Table();
		echo "<br>";
		$firstname = stripslashes(check_html($firstname, "nohtml"));
		$lastname = stripslashes(check_html($lastname, "nohtml"));
		$result = $db->sql_query("SELECT user_id, firstname, lastname, name, user_website, user_email, femail, user_sig, user_password, newsletter, currency, dollar from " . $user_prefix . "_users where firstname='$firstname' AND lastname='$lastname'");
		$numrows = $db->sql_numrows($result);
		if($numrows > 0) 
		{
			$row = $db->sql_fetchrow($result);
			$chng_uid = intval($row['user_id']);
			$chng_firstname = filter($row['firstname'], "nohtml");
			$chng_lastname = filter($row['lastname'], "nohtml");
			$chng_name = filter($row['name'], "nohtml");
			$chng_url = filter($row['user_website'], "nohtml");
			$chng_email = filter($row['user_email'], "nohtml");
			$chng_femail = filter($row['femail'], "nohtml");
			$chng_user_sig = filter($row['user_sig']);
			
			// Change Currency
			$chng_currency = filter($row['currency']);
			
			// Change Dollar
			$chng_dollar = filter($row['dollar']);
			
			$chng_pass = filter($row['user_password'], "nohtml");
			$chng_newsletter = intval($row['newsletter']);
			Open_Table();
			echo "<center><font class=\"option\"><b>" . _USERUPDATE . ": <i>$firstname $lastname</i></b></font></center>"
			."<form action=\"".$admin_file.".php\" method=\"post\">"
			."<table border=\"0\">"
			."<tr><td>" . _USERID . "</td>"
			."<td><b>$chng_uid</b></td></tr>"
			."<tr><td>" . _NICKNAME . "</td>"
			."<td><input type=\"text\" name=\"chng_firstname\" value=\"$chng_firstname\"> <font class=\"tiny\">" . _REQUIRED . "</font></td></tr>"
			."<tr><td>" . _LASTNAME . "</td>"
			."<td><input type=\"text\" name=\"chng_lastname\" value=\"$chng_lastname\"> <font class=\"tiny\">" . _REQUIRED . "</font></td></tr>"
			."<tr><td>" . _NAME . "</td>"
			."<td><input type=\"text\" name=\"chng_name\" value=\"$chng_name\"></td></tr>"
			."<tr><td>" . _URL . "</td>"
			."<td><input type=\"text\" name=\"chng_url\" value=\"$chng_url\" size=\"30\" maxlength=\"60\"></td></tr>"
			."<tr><td>" . _EMAIL . "</td>"
			."<td><input type=\"text\" name=\"chng_email\" value=\"$chng_email\" size=\"30\" maxlength=\"60\"> <font class=\"tiny\">" . _REQUIRED . "</font></td></tr>"
			."<tr><td>" . _FAKEEMAIL . "</td>"
			."<td><input type=\"text\" name=\"chng_femail\" value=\"$chng_femail\" size=\"30\" maxlength=\"60\"></td></tr>";
			if ($chng_newsletter == 1) {
				echo "<tr><td>" . _NEWSLETTER . "</td><td><input type=\"radio\" name=\"chng_newsletter\" value=\"1\" checked>" . _YES . "&nbsp;&nbsp;"
				."<input type=\"radio\" name=\"chng_newsletter\" value=\"0\">" . _NO . "</td></tr>";
			} elseif ($chng_newsletter == 0) {
				echo "<tr><td>" . _NEWSLETTER . "</td><td><input type=\"radio\" name=\"chng_newsletter\" value=\"1\">" . _YES . "&nbsp;&nbsp;"
				."<input type=\"radio\" name=\"chng_newsletter\" value=\"0\" checked>" . _NO . "</td></tr>";
			}
			echo "<tr><td>" . _USER_CURRENCY . "</td>"
			."<td><input type=\"text\" name=\"chng_currency\" value=\"$chng_currency\"></td></tr>"
			."<tr><td>" . _USER_DOLLAR . "</td>"
			."<td><input type=\"text\" name=\"chng_dollar\" value=\"$chng_dollar\"></td></tr>";
			
			$subnum = $db->sql_numrows($db->sql_query("SELECT * FROM " . $prefix . "_subscriptions WHERE userid='$chng_uid'"));
			$content = "";
			if ($subnum == 0) {
				$content .= "<tr><td>" . _SUBUSERASK . "</td><td><input type='radio' name='subscription' value='1'> " . _YES . "&nbsp;&nbsp;&nbsp;<input type='radio' name='subscription' value='0' checked> " . _NO . "</td></tr>";
				$content .= "<tr><td>" . _SUBPERIOD . "</td><td><select name='subscription_expire'>";
				$content .= "<option value='0' selected>" . _NONE . "</option>";
				$content .= "<option value='1'>1 "._YEAR."</option>";
				$content .= "<option value='2'>2 "._YEARS."</option>";
				$content .= "<option value='3'>3 "._YEARS."</option>";
				$content .= "<option value='4'>4 "._YEARS."</option>";
				$content .= "<option value='5'>5 "._YEARS."</option>";
				$content .= "<option value='6'>6 "._YEARS."</option>";
				$content .= "<option value='7'>7 "._YEARS."</option>";
				$content .= "<option value='8'>8 "._YEARS."</option>";
				$content .= "<option value='9'>9 "._YEARS."</option>";
				$content .= "<option value='10'>10 "._YEARS."</option>";
				$content .= "</select><input type='hidden' name='reason' value='0'></td></tr>";
			} elseif ($subnum == 1) {
				$content .= "<tr><td>"._UNSUBUSER."</td><td><input type='radio' name='subscription' value='0'> "._YES."&nbsp;&nbsp;&nbsp;<input type='radio' name='subscription' value='1' checked> "._NO."</td></tr>";
				$content .= "<tr><td>"._ADDSUBPERIOD."</td><td><select name='subscription_expire'>";
				$content .= "<option value='0' selected>"._NONE."</option>";
				$content .= "<option value='1'>1 "._YEAR."</option>";
				$content .= "<option value='2'>2 "._YEARS."</option>";
				$content .= "<option value='3'>3 "._YEARS."</option>";
				$content .= "<option value='4'>4 "._YEARS."</option>";
				$content .= "<option value='5'>5 "._YEARS."</option>";
				$content .= "<option value='6'>6 "._YEARS."</option>";
				$content .= "<option value='7'>7 "._YEARS."</option>";
				$content .= "<option value='8'>8 "._YEARS."</option>";
				$content .= "<option value='9'>9 "._YEARS."</option>";
				$content .= "<option value='10'>10 "._YEARS."</option>";
				$content .= "</select></td></tr>";
				$content .= "<tr><td>"._ADMSUBEXPIREIN."</td><td>";
				$rows = $db->sql_fetchrow($db->sql_query("SELECT * FROM ".$prefix."_subscriptions WHERE userid='$chng_uid'"));
				$diff = $rows['subscription_expire']-time();
				$yearDiff = floor($diff/60/60/24/365);
				$diff -= $yearDiff*60*60*24*365;
				if ($yearDiff < 1) {
					$diff = $rows['subscription_expire']-time();
				}
				$daysDiff = floor($diff/60/60/24);
				$diff -= $daysDiff*60*60*24;
				$hrsDiff = floor($diff/60/60);
				$diff -= $hrsDiff*60*60;
				$minsDiff = floor($diff/60);
				$diff -= $minsDiff*60;
				$secsDiff = $diff;
				if ($yearDiff < 1) {
					$rest = "$daysDiff "._SBDAYS.", $hrsDiff "._SBHOURS.", $minsDiff "._SBMINUTES.", $secsDiff "._SBSECONDS."";
				} elseif ($yearDiff == 1) {
					$rest = "$yearDiff "._SBYEAR.", $daysDiff "._SBDAYS.", $hrsDiff "._SBHOURS.", $minsDiff "._SBMINUTES.", $secsDiff "._SBSECONDS."";
				} elseif ($yearDiff > 1) {
					$rest = "$yearDiff "._SBYEARS.", $daysDiff "._SBDAYS.", $hrsDiff "._SBHOURS.", $minsDiff "._SBMINUTES.", $secsDiff "._SBSECONDS."";
				}
				$content .= "<font color='#FF0000'>$rest</font></td></tr>";
				$content .= "<tr><td>"._SUBREASON."</td><td><textarea name='reason' cols='70' rows='15'></textarea></td></tr>";
			}
			echo "$content";
	
			echo "<tr><td>" . _SIGNATURE . "</td>"
			."<td><textarea name=\"chng_user_sig\" rows=\"15\" cols=\"70\">$chng_user_sig</textarea></td></tr>"
			."<tr><td>" . _PASSWORD . "</td>"
			."<td><input type=\"password\" name=\"chng_pass\" size=\"12\" maxlength=\"12\"></td></tr>"
			."<tr><td>" . _RETYPEPASSWD . "</td>"
			."<td><input type=\"password\" name=\"chng_pass2\" size=\"12\" maxlength=\"12\"> <font class=\"tiny\">" . _FORCHANGES . "</font></td></tr>"
			."<input type=\"hidden\" name=\"chng_uid\" value=\"$chng_uid\">"
			."<input type=\"hidden\" name=\"op\" value=\"updateUser\">"
			."<tr><td><input type=\"submit\" value=\"" . _SAVECHANGES . "\"></form></td></tr>"
			."</table>";
			Close_Table();
		} 
		else 
		{
			Open_Table();
			echo '<center><b>' . _USERNOEXIST . '</b><br /><br />
			' . _GOBACK . '</center>';
			Close_Table();
		}
		include("footer.php");
	}

	function updateUser($chng_uid, $chng_firstname, $chng_lastname, $chng_name, $chng_url, $chng_email, $chng_femail, $chng_user_sig, $chng_pass, $chng_pass2, $chng_newsletter, $chng_currency, $chng_dollar, $subscription, $subscription_expire, $reason) {
		global $user_prefix, $db, $prefix, $site_url, $sitename, $adminmail, $subscription_url, $admin_file;
		$chng_uid = intval($chng_uid);
		$chng_firstname = filter($chng_firstname, "nohtml", 1);
		$chng_lastname = filter($chng_lastname, "nohtml", 1);
		$chng_name = filter($chng_name, "nohtml", 1);
		$chng_url = filter($chng_url, "nohtml", 1);
		$chng_email = filter($chng_email, "nohtml", 1);
		$chng_femail = filter($chng_femail, "nohtml", 1);
		$chng_user_sig = filter($chng_user_sig, "", 1);
		$chng_pass = filter($chng_pass, "nohtml", 1);
		$chng_pass2 = filter($chng_pass2, "nohtml", 1);
		$chng_newsletter = intval($chng_newsletter);
		$chng_currency = filter($chng_currency, "nohtml", 1);
		$chng_dollar = filter($chng_dollar, "nohtml", 1);
		$tmp = 0;
		if (!empty($chng_pass2)) {
			if($chng_pass != $chng_pass2) {
				include("header.php");
				GraphicAdmin();
				Open_Table();
				echo "<center><font class=\"title\"><b>" . _USERADMIN . "</b></font></center>";
				Close_Table();
				echo "<br>";
				Open_Table();
				echo "<center>" . _PASSWDNOMATCH . "<br><br>"
				."" . _GOBACK . "</center>";
				Close_Table();
				include("footer.php");
				exit;
			}
			$tmp = 1;
		}
		if ($tmp == 0) {
			$db->sql_query("update " . $user_prefix . "_users set firstname='$chng_firstname', lastname='$chng_lastname', name='$chng_name', user_email='$chng_email', femail='$chng_femail', user_website='$chng_url', user_sig='$chng_user_sig', newsletter='$chng_newsletter', currency='$chng_currency', dollar='$chng_dollar' where user_id='$chng_uid'");
		}
		if ($tmp == 1) {
			$cpass = md5($chng_pass);
			$db->sql_query("update " . $user_prefix . "_users set firstname='$chng_firstname', lastname='$chng_lastname', name='$chng_name', user_email='$chng_email', femail='$chng_femail', user_website='$chng_url', user_sig='$chng_user_sig', user_password='$cpass', newsletter='$chng_newsletter', currency='$chng_currency', dollar='$chng_dollar' where user_id='$chng_uid'");
		}
		$subnum = $db->sql_numrows($db->sql_query("SELECT * FROM ".$prefix."_subscriptions WHERE userid='$chng_uid'"));
		$row = $db->sql_fetchrow($db->sql_query("SELECT * FROM ".$prefix."_subscriptions WHERE userid='$chng_uid'"));
		$row2 = $db->sql_fetchrow($db->sql_query("SELECT firstname, lastname, user_email FROM ".$user_prefix."_users WHERE user_id='$chng_uid'"));
		if (empty($reason)) {
			$reason = 0;
		}
		if ($subnum == 1) {
			if ($subscription == 0) {
				$from = "$sitename <$adminmail>";
				$subject = "$sitename "._SUBCANCELLED."";
				if ($reason == "0") {
					if (!empty($subscription_url)) {
						$body = ""._HELLO." ".$row2['firstname']." ".$row2['lastname']."!\n\n"._SUBCANCEL."\n\n"._SUBNEEDTOAPPLY." $subscription_url\n\n"._SUBTHANKSATT."\n\n$sitename "._TEAM."\n$site_url";
					} else {
						$body = ""._HELLO." ".$row2['firstname']." ".$row2['lastname']."!\n\n"._SUBCANCEL."\n\n"._SUBTHANKSATT."\n\n$sitename "._TEAM."\n$site_url";
					}
				} else {
					if (!empty($subscription_url)) {
						$body = ""._HELLO." ".$row2['firstname']." ".$row2['lastname']."!\n\n"._SUBCANCELREASON."\n\n$reason\n\n"._SUBNEEDTOAPPLY." $subscription_url\n\n"._SUBTHANKSATT."\n\n$sitename "._TEAM."\n$site_url";
					} else {
						$body = ""._HELLO." ".$row2['firstname']." ".$row2['lastname']."!\n\n"._SUBCANCELREASON."\n\n$reason\n\n"._SUBTHANKSATT."\n\n$sitename "._TEAM."\n$site_url";
					}
				}
				$db->sql_query("DELETE FROM ".$prefix."_subscriptions WHERE userid='$chng_uid'");
				mail($row2['user_email'], $subject, $body, "From: $from\nX-Mailer: PHP/" . phpversion());
			} elseif ($subscription == 1) {
				if ($subscription_expire != 0) {
					$from = "$sitename <$adminmail>";
					$subject = "$sitename "._SUBUPDATEDSUB."";
					$body = ""._HELLO." ".$row2['firstname']." ".$row2['lastname']."!\n\n"._SUBUPDATED." $subscription_expire "._SUBYEARSTOACCOUNT."\n\n"._SUBTHANKSSUPP."\n\n$sitename "._TEAM."\n$site_url";
					$expire = $subscription_expire*31536000;
					if ($subnum == 0) {
						$expire = $expire+time();
					}
					$expire = $expire+$row['subscription_expire'];
					$db->sql_query("UPDATE ".$prefix."_subscriptions SET subscription_expire='$expire' WHERE userid='$chng_uid'");
					mail($row2['user_email'], $subject, $body, "From: $from\nX-Mailer: PHP/" . phpversion());
				}
			}
		} elseif ($subnum == 0 AND $subscription == 1) {
			if ($subscription_expire != 0) {
				$expire = $subscription_expire*31536000;
				$expire = $expire+time();
				$db->sql_query("INSERT INTO ".$prefix."_subscriptions VALUES (NULL, '$chng_uid', '$expire')");
				$from = "$sitename <$adminmail>";
				$subject = "$sitename "._SUBACTIVATED."";
				$body = ""._HELLO." ".$row2['firstname']." ".$row2['lastname']."!\n\n"._SUBOPENED." $subscription_expire "._SUBOPENED2."\n\n"._SUBHOPELIKE."\n"._SUBTHANKSSUPP2."\n\n$sitename "._TEAM."\n$site_url";
				mail($row2['user_email'], $subject, $body, "From: $from\nX-Mailer: PHP/" . phpversion());
			}
		}
		Header("Location: ".$admin_file.".php?op=mod_users");
	}

	switch($op) {

		case "mod_users":
		displayUsers();
		break;

		case "modifyUser":
		modifyUser($firstname, $lastname);
		break;

		case "updateUser":
		updateUser($chng_uid, $chng_firstname, $chng_lastname, $chng_name, $chng_url, $chng_email, $chng_femail, $chng_user_sig, $chng_pass, $chng_pass2, $chng_newsletter, $chng_currency, $chng_dollar, $subscription, $subscription_expire, $reason);
		break;

		case "delUser":
		include("header.php");
		GraphicAdmin();
		Open_Table();
		echo "<center><font class=\"title\"><b>" . _USERADMIN . "</b></font></center>";
		Close_Table();
		echo "<br>";
		Open_Table();
		echo "<center><font class=\"option\"><b>" . _DELETEUSER . "</b></font><br><br>"
		."" . _SURE2DELETE . " $firstname $lastname?<br><br>"
		."[ <a href=\"".$admin_file.".php?op=delUserConf&amp;del_firstname=$firstname&amp;del_lastname=$lastname\">" . _YES . "</a> | <a href=\"".$admin_file.".php?op=mod_users\">" . _NO . "</a> ]</center>";
		//echo $chng_uid;
		Close_Table();
		include("footer.php");
		break;

		case "delUserConf":
		$result = $db->sql_query("SELECT user_id from " . $user_prefix . "_users where firstname='$del_firstname' AND lastname='$del_lastname'");
		$row = $db->sql_fetchrow($result);
		$del_user_id = intval($row['user_id']);
		echo $del_user_id;
		$db->sql_query("delete from " . $user_prefix . "_users where user_id='$del_user_id'");
		$row2 = $db->sql_fetchrow($result2);
		$del_group_id = intval($row2['group_id']);
		Header("Location: ".$admin_file.".php?op=mod_users");
		break;

		case "addUser":
		$add_pass = md5($add_pass);
		if (!($add_firstname && $add_lastname && $add_email && $add_pass)) {
			include("header.php");
			GraphicAdmin();
			Open_Table();
			echo "<center><font class=\"title\"><b>" . _USERADMIN . "</b></font></center>";
			Close_Table();
			echo "<br>";
			Open_Table();
			echo "<center><b>" . _NEEDTOCOMPLETE . "</b><br><br>"
			."" . _GOBACK . "";
			Close_Table();
			include("footer.php");
			return;
		}
		$numrow = $db->sql_numrows($db->sql_query("SELECT user_id FROM ".$user_prefix."_users WHERE firstname='$add_firstname' AND lastname='$add_lastname'"));
		if ($numrow > 0) {
			include("header.php");
			GraphicAdmin();
			Open_Table();
			echo "<center><font class=\"title\"><b>" . _USERADMIN . "</b></font></center>";
			Close_Table();
			echo "<br>";
			Open_Table();
			echo "<center><b>" . _USERALREADYEXISTS . "</b><br><br>"
			."" . _GOBACK . "";
			Close_Table();
			include("footer.php");
			return;
		} else {
			$user_regdate = date("M d, Y");
			$add_firstname = filter($add_firstname, "nohtml", 1);
			$add_lastname = filter($add_lastname, "nohtml", 1);
			$add_name = filter($add_name, "nohtml", 1);
			$add_url = filter($add_url, "nohtml", 1);
			$add_email = filter($add_email, "nohtml", 1);
			$add_femail = filter($add_femail, "nohtml", 1);
			$add_user_sig = filter($add_user_sig, "", 1);
			$add_pass = filter($add_pass, "nohtml", 1);
			$add_newsletter = intval($add_newsletter);
			$add_currency = filter($add_currency, "nohtml", 1);
			$add_dollar = filter($add_dollar, "nohtml", 1);
			$sql = "insert into " . $user_prefix . "_users ";
			$sql .= "(user_id,name,firstname,lastname, user_email,femail,user_website,user_regdate,user_sig,user_password,newsletter,currency,dollar,broadcast) ";
			$sql .= "values (NULL,'$add_name','$add_firstname','$add_lastname','$add_email','$add_femail','$add_url','$user_regdate','$add_user_sig','$add_pass','$add_newsletter','$add_currency','$add_dollar','1')";
			$result = $db->sql_query($sql);
			if (!$result) {
				return;
			}
		}
		Header("Location: ".$admin_file.".php?op=mod_users");
		break;

	}

} else {
	include("header.php");
	GraphicAdmin();
	Open_Table();
	echo "<center><b>"._ERROR."</b><br><br>You do not have administration permission for module \"$module_name\"</center>";
	Close_Table();
	include("footer.php");
}

?>