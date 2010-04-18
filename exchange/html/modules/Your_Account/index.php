<?php

######################################################################
# Skidz Partz - Exchange
# ============================================
#
# Copyright (c) 2010 by Skidz Partz Open Source Team
# http://www.skidzpartz.com
#
# This program is free software. You can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License.
######################################################################

if (!defined('MODULE_FILE')) {
	die ("You can't access this file directly...");
}

require_once('mainfile.php');
$module_name = basename(dirname(__FILE__));
get_lang($module_name);

$modname = ereg_replace('_', ' ', $module_name);
$pagetitle = '-&nbsp;' . $modname;

$userpage = 1;
if(isset($_GET['redirect'])) $redirect = substr($_SERVER['QUERY_STRING'], strpos($_SERVER['QUERY_STRING'], "redirect=") + strlen("redirect="), strlen($_SERVER['QUERY_STRING']));

//firstname lastname
if (isset($firstname) && (ereg("[^a-zA-Z0-9_-]", $firstname)) && isset($lastname) && (ereg("[^a-zA-Z0-9_-]", $lastname))) 
{
    die("Illegal Username"); // @todo genberate an better error message thats means something 
}

if(is_user($user)) 
{
	include('modules/'.$module_name.'/navbar.php');
}

function CallLSLScript($URL, $Data, $Timeout = 10)
{
 //Parse the URL into Server, Path and Port
 $Host = str_ireplace("http://", "", $URL);
 $Path = explode("/", $Host, 2);
 $Host = $Path[0];
 $Path = $Path[1];
 $PrtSplit = explode(":", $Host);
 $Host = $PrtSplit[0];
 $Port = $PrtSplit[1];
 
 //Open Connection
 $Socket = @fsockopen($Host, $Port, $Dummy1, $Dummy2, $Timeout);
 if ($Socket)
 {
  //Send Header and Data
  @fputs($Socket, "POST /$Path HTTP/1.1\r\n");
  @fputs($Socket, "Host: $Host\r\n");
  @fputs($Socket, "Content-type: application/x-www-form-urlencoded\r\n");
  @fputs($Socket, "User-Agent: Opera/9.01 (Windows NT 5.1; U; en)\r\n");
  @fputs($Socket, "Accept-Language: de-DE,de;q=0.9,en;q=0.8\r\n");
  @fputs($Socket, "Content-length: ".strlen($Data)."\r\n");
  @fputs($Socket, "Connection: close\r\n\r\n");
  @fputs($Socket, $Data);
 
  //Receive Data
  while(!@feof($Socket))
   {$res .= @fgets($Socket, 4096);}
  fclose($Socket);
 }
 
 //ParseData and return it
 $res = explode("\r\n\r\n", $res);
 return $res[1];
}

function userCheck($firstname, $lastname, $user_email) 
{
	$firstname = filter($firstname, 'nohtml', 1);
	$lastname = filter($lastname, 'nohtml', 1);
	$user_email = filter($user_email, 'nohtml', 1);
	global $stop, $user_prefix, $db;
	if ((!$user_email) || (empty($user_email)) || (!eregi("^[_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,6}$", $user_email))) $stop = '<center>'._ERRORINVEMAIL.'</center><br />';
	if (strrpos($user_email,' ') > 0) $stop = '<center>'._ERROREMAILSPACES.'</center>';
	if ((!$firstname) || (empty($firstname)) || (ereg("[^a-zA-Z0-9_-]", $firstname))) $stop = '<center>'._ERRORINVNICK.'</center><br />';
	if ((!$lastname) || (empty($lastname)) || (ereg("[^a-zA-Z0-9_-]", $lastname))) $stop = '<center>'._ERRORINVNICK.'</center><br />';
	if (strlen($firstname) > 31) $stop = '<center>'._NICK2LONG.'</center>';
	if (strlen($lastname) > 31) $stop = '<center>'._NICK2LONG.'</center>';
	if (eregi("^((root)|(adm)|(linux)|(webmaster)|(admin)|(god)|(administrator)|(administrador)|(nobody)|(anonymous)|(anonimo)|(anónimo)|(operator)|(JackFromWales4u2))$", $firstname)) $stop = '<center>'._NAMERESERVED.'</center>';
	if (eregi("^((root)|(adm)|(linux)|(webmaster)|(admin)|(god)|(administrator)|(administrador)|(nobody)|(anonymous)|(anonimo)|(anónimo)|(operator)|(JackFromWales4u2))$", $lastname)) $stop = '<center>'._NAMERESERVED.'</center>';
	if (strrpos($firstname, ' ') > 0) $stop = '<center>'._NICKNOSPACES.'</center>';
	if (strrpos($lastname, ' ') > 0) $stop = '<center>'._NICKNOSPACES.'</center>';
	if ($db->sql_numrows($db->sql_query("SELECT firstname, lastname FROM ".$user_prefix."_users WHERE firstname='".$firstname."' AND lastname='".$lastname."'")) > 0) $stop = '<center>'._NICKTAKEN.'</center><br />';
	if ($db->sql_numrows($db->sql_query("SELECT firstname, lastname FROM ".$user_prefix."_users_temp WHERE firstname='".$firstname."' AND lastname='".$lastname."'")) > 0) $stop = '<center>'._NICKTAKEN.'</center><br />';
	if ($db->sql_numrows($db->sql_query("SELECT user_email FROM ".$user_prefix."_users WHERE user_email='".$user_email."'")) > 0) $stop = '<center>'._EMAILREGISTERED.'</center><br />';
	if ($db->sql_numrows($db->sql_query("SELECT user_email FROM ".$user_prefix."_users_temp WHERE user_email='".$user_email."'")) > 0) $stop = '<center>'._EMAILREGISTERED.'</center><br />';
	return $stop;
}

function confirmNewUser($firstname, $lastname, $user_email, $user_password, $user_password2, $random_num, $gfx_check) 
{
	global $stop, $EditedMessage, $sitename, $module_name, $minpass;
	include('header.php');
	include('config.php');
	$firstname = substr(htmlspecialchars(str_replace("\'", "'", trim($firstname))), 0, 31);
	$firstname = rtrim($firstname, "\\");	
	$firstname = str_replace("'", "\'", $firstname);
	$lastname = substr(htmlspecialchars(str_replace("\'", "'", trim($lastname))), 0, 31);
	$lastname = rtrim($lastname, "\\");	
	$lastname = str_replace("'", "\'", $lastname);
	$user_email = filter($user_email, "nohtml");
	$user_viewemail = "0";
	userCheck($firstname, $lastname, $user_email);
	$user_email = validate_mail($user_email);
	$user_password = htmlspecialchars(stripslashes($user_password));
	$user_password2 = htmlspecialchars(stripslashes($user_password2));
	if (!$stop) 
	{
		$datekey = date("F j");
		$rcode = hexdec(md5($_SERVER['HTTP_USER_AGENT'] . $sitekey . $_POST['random_num'] . $datekey));
		$code = substr($rcode, 2, 6);
		if (extension_loaded("gd") AND $code != $gfx_check AND ($gfx_chk == 3 OR $gfx_chk == 4 OR $gfx_chk == 6 OR $gfx_chk == 7)) 
		{
			title(_NEWUSERERROR);
			Open_Table();
			echo '<center><b>'._SECCODEINCOR.'</b><br><br />'._GOBACK.'</center>';
			Close_Table();
			include("footer.php");
			die();
		}
		if (empty($user_password) AND empty($user_password2)) 
		{
			$user_password = makepass();
		} 
		elseif ($user_password != $user_password2) 
		{
			title(_NEWUSERERROR);
			Open_Table();
			echo '<center><b>'._PASSDIFFERENT.'</b><br><br>'._GOBACK.'</center>';
			Close_Table();
			include("footer.php");
			die();
		} 
		elseif ($user_password == $user_password2 AND strlen($user_password) < $minpass) 
		{
			title(_NEWUSERERROR);
			Open_Table();
			echo '<center>'._YOUPASSMUSTBE.'&nbsp;<b>'.$minpass.'</b>&nbsp;'._CHARLONG.'<br /><br />'._GOBACK.'</center>';
			Close_Table();
			include("footer.php");
			die();
		}
		title($sitename .':&nbsp;'._USERREGLOGIN);
		Open_Table();
		echo '<center><b>'._USERFINALSTEP.'</b><br /><br />'.$firstname.'&nbsp;'.$lastname.',&nbsp;'._USERCHECKDATA.'</center><br /><br />
		<table align="center" border="0">
		<tr><td><b>'._FIRSTNAME.':</b>&nbsp;'.$firstname.'&nbsp;<br /></td></tr>
		<tr><td><b>'._LASTNAME.':</b>&nbsp;'.$lastname.'<br /></td></tr>
		<tr><td><b>'._EMAIL.':</b>&nbsp;'.$user_email.'</td></tr></table><br /><br />
		<center><b>'._NOTE.'</b>&nbsp;'._YOUWILLRECEIVE.'
		<form action="index.php?name='.$module_name.'" method="post">
		<input type="hidden" name="random_num" value="'.$random_num.'">
		<input type="hidden" name="gfx_check" value="'.$gfx_check.'">
		<input type="hidden" name="firstname" value="'.$firstname.'">
		<input type="hidden" name="lastname" value="'.$lastname.'">
		<input type="hidden" name="user_email" value="'.$user_email.'">
		<input type="hidden" name="user_password" value="'.$user_password.'">
		<input type="hidden" name="op" value="finish"><br /><br />
		<input type="submit" value="'._FINISH.'"> &nbsp;&nbsp;'._GOBACK.'</form></center>';
		Close_Table();
	} 
	else 
	{
		Open_Table();
		echo '<center><font class="title"><b>Registration Error!</b></font><br /><br />';
		echo '<font class="content">'.$stop.'<br />'._GOBACK.'</font></center>';
		Close_Table();
	}
	include("footer.php");
}

function finishNewUser($firstname, $lastname, $user_email, $user_password, $random_num, $gfx_check) 
{
	global $stop, $full_debug, $EditedMessage, $adminmail, $sitename, $Default_Theme, $user_prefix, $db, $storyhome, $module_name, $site_url;
	include('header.php');
	include('config.php');
	userCheck($firstname, $lastname, $user_email);
	$user_email = validate_mail($user_email);
	$user_regdate = date("M d, Y");
	$user_password = htmlspecialchars(stripslashes($user_password));
	if (!isset($stop)) 
	{
		$datekey = date("F j");
		$rcode = hexdec(md5($_SERVER['HTTP_USER_AGENT'] . $sitekey . $random_num . $datekey));
		$code = substr($rcode, 2, 6);
		if (extension_loaded("gd") && $code != $gfx_check && ($gfx_chk == 3 || $gfx_chk == 4 || $gfx_chk == 6 || $gfx_chk == 7)) 
		{
			Header('Location: index.php?name=' . $module_name);
			die();
		}
		mt_srand ((double)microtime()*1000000);
		$maxran = 1000000;
		$check_num = mt_rand(0, $maxran);
		$check_num = md5($check_num);
		$time = time();
        $finishlink = $check_num;
		$new_password = md5($user_password);
		$new_password = htmlspecialchars(stripslashes($new_password));
		
		$firstname = substr(htmlspecialchars(str_replace("\'", "'", trim($firstname))), 0, 31);
		$firstname = rtrim($firstname, "\\");	
		$firstname = str_replace("'", "\'", $firstname);

		$lastname = substr(htmlspecialchars(str_replace("\'", "'", trim($lastname))), 0, 31);
		$lastname = rtrim($lastname, "\\");	
		$lastname = str_replace("'", "\'", $lastname);
		
		$user_email = filter($user_email, 'nohtml', 1);
		$result = $db->sql_query("INSERT INTO ".$user_prefix."_users_temp (user_id, firstname, lastname, user_email, user_password, user_regdate, check_num, time) VALUES (NULL, '$firstname', '$lastname', '$user_email', '$new_password', '$user_regdate', '$check_num', '$time')");
		if(!$result) 
		{
			echo ""._ERROR."<br>";
		} 
		else 
		{
			$message = _ACCOUNT_WELCOME.'&nbsp;'.$sitename.'!<br /><br />'._YOUUSEDEMAIL.'&nbsp;('.$user_email.')&nsbp;'._TOREGISTER.'&nbsp;'.$sitename.'.<br /><br />'._TOFINISHUSER.'<br /><br />'._ACTIVATION.'&nbsp;'.$finishlink.'<br /><br />'._FOLLOWINGMEM.'<br /><br />'._FIRSTNAME.':&nbsp;'.$firstname.'<br />'._LASTNAME.':&nbsp;'.$lastname.'<br />'._UPASSWORD.'&nbsp;'.$user_password;
			$subject = _ACTIVATIONSUB;
			
			// Content Types
            $headers  = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset='._CHARSET.'' . "\r\n";
            
			// Additional headers
            $headers .= 'To: '.$firstname.' '.$lastname.' <'.$user_email.'>' . "\r\n";
            $headers .= 'From: '.$sitename.' <'.$adminmail.'>' . "\r\n";
			
			if($full_debug == true)
			{
			  echo '<br /> '.$user_email.' <br /> '.$subject.' <br /> '.$message.' <br /> '.var_dump($headers);
			}
			else
			{
			   mail($user_email, $subject, $message, $headers);
			}
			
			//mail($user_email, $subject, $message, "From: $from\nX-Mailer: PHP/" . phpversion());
			title($sitename.':&nbsp;'._USERREGLOGIN);
			Open_Table();
			echo '<center><b>'._ACCOUNTCREATED.'</b><br><br />'._YOUAREREGISTERED.'<br /><br />'._FINISHUSERCONF.'<br /><br />'._THANKSUSER.'&nbsp;'.$sitename.'!</center>';
			Close_Table();
		}
	} else {
		echo "$stop";
	}
	include("footer.php");
}


function activate($firstname, $lastname, $check_num) 
{
	global $db, $user_prefix, $module_name, $language, $prefix;
	
	$firstname = substr(htmlspecialchars(str_replace("\'", "'", trim($firstname))), 0, 31);
	$firstname = rtrim($firstname, "\\");	
	$firstname = str_replace("'", "\'", $firstname);
	$lastname = substr(htmlspecialchars(str_replace("\'", "'", trim($lastname))), 0, 31);
	$lastname = rtrim($lastname, "\\");	
	$lastname = str_replace("'", "\'", $lastname);
	
	$past = time()-86400;
	$db->sql_query("DELETE FROM ".$user_prefix."_users_temp WHERE time < ".$past."");
	$sql = "SELECT * FROM ".$user_prefix."_users_temp WHERE firstname='".$firstname."' AND lastname='".$lastname."' AND check_num='".$check_num."'";
	$result = $db->sql_query($sql);
	if ($db->sql_numrows($result) == 1) 
	{
		$row = $db->sql_fetchrow($result);
		$user_password = htmlspecialchars(stripslashes($row['user_password']));
		if ($check_num == $row['check_num']) 
		{
			$db->sql_query("INSERT INTO ".$user_prefix."_users (user_id, firstname, lastname, user_email, user_password, user_regdate) VALUES (NULL, '".$row['firstname']."', '".$row['lastname']."', '".$row['user_email']."', '".$user_password."', '".$row['user_regdate']."')");
			$db->sql_query("DELETE FROM ".$user_prefix."_users_temp WHERE firstname='".$firstname."' AND lastname='".$lastname."' AND check_num='".$check_num."'");
			include('header.php');
			title(_ACTIVATIONYES);
			Open_Table();
			echo '<center><b>'.$row['firstname'].'&nbsp;'.$row['firstname'].':</b>&nbsp;'._ACTMSG.'</center>';
			Close_Table();
			include('footer.php');
			die();
		} 
		else 
		{
			include('header.php');
			title(_ACTIVATIONERROR);
			Open_Table();
			echo '<center>'._ACTERROR1.'</center>';
			Close_Table();
			include('footer.php');
			die();
		}
	} 
	else 
	{
		include('header.php');
		title(_ACTIVATIONERROR);
		Open_Table();
		echo '<center>'._ACTERROR2.'</center>';
		Close_Table();
		include('footer.php');
		die();
	}

}

function userinfo($firstname, $lastname, $bypass = 0, $hid = 0, $url = 0) 
{
	global $articlecomm, $user, $cookie, $sitename, $prefix, $user_prefix, $db, $admin, $broadcast_msg, $my_headlines, $module_name, $subscription_url, $admin_file;
	
	$firstname = substr(htmlspecialchars(str_replace("\'", "'", trim($firstname))), 0, 31);
	$firstname = rtrim($firstname, "\\");	
	$firstname = str_replace("'", "\'", $firstname);

	$lastname = substr(htmlspecialchars(str_replace("\'", "'", trim($lastname))), 0, 31);
	$lastname = rtrim($lastname, "\\");	
	$lastname = str_replace("'", "\'", $lastname);
	
	$sql = "SELECT * FROM ".$prefix."_bbconfig";
	$result = $db->sql_query($sql);
	while ( $row = $db->sql_fetchrow($result) )
	{
		$board_config[$row['config_name']] = $row['config_value'];
	}
	$sql2 = "SELECT * FROM ".$user_prefix."_users WHERE firstname='".$firstname."' AND lastname='".$lastname."'";
	$result2 = $db->sql_query($sql2);
	$num = $db->sql_numrows($result2);
	if ($num != 1) 
	{
		Header('Location: index.php?name=' . $module_name);
		die();
	}
	$userinfo = $db->sql_fetchrow($result2);
	if(!$bypass) cookiedecode($user);
	include("header.php");
	Open_Table();
	echo "<center>";
	if ($firstname != '' && $lastname != '') // SecurityReason.com Fix 2005 [sp3x] 
	if((isset($cookie[1])) && (strtolower($firstname) == strtolower($cookie[1])) && (isset($cookie[2])) && (strtolower($lastname) == strtolower($cookie[2])) && ($userinfo['user_password'] == $cookie[3])) 
	{
		echo '<font class="option">'.htmlentities($firstname).'&nbsp;'.htmlentities($lastname).',&nbsp;'._WELCOMETO.'&nbsp;'.$sitename.'!</font><br /><br />';
		echo '<font class="content">'._THISISYOURPAGE.'</font></center><br /><br />';
		nav(1);
		echo '<br /><br />';
	} 
	else 
	{
		echo '<font class="title">'._PERSONALINFO.': '.htmlentities($firstname).'&nbsp;'.htmlentities($lastname).'</font></center><br /><br />';
	}	
	else 
	Header('Location: index.php?name=' . $module_name);
	if ($userinfo['user_website']) 
	{
	   if (!preg_match('#^http[s]?:\/\/#i', $userinfo['user_website'])) 
	   {
	      $userinfo['user_website'] = 'http://' . $userinfo['user_website'];
	   }
	   if (!preg_match('#^http[s]?\\:\\/\\/[a-z0-9\-]+\.([a-z0-9\-]+\.)?[a-z]+#i', $userinfo['user_website'])) 
	   {
	      $userinfo['user_website'] = '';
	   }
    }
	if(($num == 1) && ($userinfo['user_website'] || $userinfo['femail'] || $userinfo['user_sig'])) 
	{
		echo "<center><font class=\"content\">";
		if ($userinfo['user_website'] != "http://" AND !empty($userinfo['user_website'])) { echo ""._MYHOMEPAGE." <a href=\"".$userinfo['user_website']."\" target=\"new\">".$userinfo['user_website']."</a><br>\n"; }
		if ($userinfo['femail']) { echo ""._MYEMAIL." <a href=\"mailto:".$userinfo['femail']."\">".$userinfo['femail']."</a><br>\n"; }
		$userinfo['user_sig'] = nl2br($userinfo['user_sig']);
		if ($userinfo['user_sig']) echo "<br><b>"._SIGNATURE.":</b><br>".$userinfo['user_sig']."<br>\n";
		$sql2 = "SELECT firstname FROM ".$prefix."_session WHERE firstname='".$firstname."' AND lastname='".$lastname."'";//@todo add lastname
		$result2 = $db->sql_query($sql2);
		$row2 = $db->sql_fetchrow($result2);
		$firstname_pm = $firstname;//@todo add lastname
		$firstname_online = $row2['firstname']; //@todo make lastname version
		if (empty($firstname_online) && empty($lastname_online)) 
		{
			$online = _OFFLINE;
		} 
		else 
		{
			$online = _ONLINE;
		}
		echo "<br><br>"._USERSTATUS.": <b>$online</b><br>\n";
		if (($userinfo['newsletter'] == 1) && ($firstname == $cookie[1]) && ($lastname == $cookie[2]) && ($userinfo['user_password'] == $cookie[3]) || (is_admin($admin) && ($userinfo['newsletter'] == 1))) {
			echo "<i>"._SUBSCRIBED."</i><br>";
		} elseif ((isset($cookie[1])) && ($userinfo['newsletter'] == 0) && ($firstname == $cookie[1]) && (isset($cookie[2])) && ($lastname == $cookie[2]) && ($userinfo['user_password'] == $cookie[3]) || (is_admin($admin) || ($userinfo['newsletter'] == 0))) {
			echo "<i>"._NOTSUBSCRIBED."</i><br>";
		}
		if (is_user($user) && $cookie[1] == $firstname && $cookie[2] == $lastname || is_admin($admin)) 
		{
			$numpoints = $db->sql_fetchrow($db->sql_query("SELECT points FROM ".$user_prefix."_users WHERE user_id = '".intval($cookie[0])."'"));
			$n_points = intval($numpoints['points']);
			echo _YOUHAVEPOINTS.'&nbsp;<b>'.$n_points.'</b><br />';
			if (paid()) 
			{
				$row = $db->sql_fetchrow($db->sql_query("SELECT * FROM ".$prefix."_subscriptions WHERE userid='".intval($cookie[0])."'"));
				if (!empty($subscription_url)) 
				{
					$content = '<br /><center>'._YOUARE.'&nbsp;<a href="'.$subscription_url.'">'._SUBSCRIBER.'</a>&nbsp;'._OF.'&nbsp;' . $sitename . '<br />';
				} 
				else 
				{
					$content = '<br /><center>'._YOUARE.'&nbsp;'._SUBSCRIBER.'&nbsp;'._OF.'&nbsp;'.$sitename.'<br />';
				}
				$diff = $row['subscription_expire'] - time();
				$yearDiff = floor($diff/60/60/24/365);
				$diff -= $yearDiff*60*60*24*365;
				if ($yearDiff < 1) 
				{
					$diff = $row['subscription_expire'] - time();
				}
				$daysDiff = floor($diff/60/60/24);
				$diff -= $daysDiff*60*60*24;
				$hrsDiff = floor($diff/60/60);
				$diff -= $hrsDiff*60*60;
				$minsDiff = floor($diff/60);
				$diff -= $minsDiff*60;
				$secsDiff = $diff;
				if ($yearDiff < 1) 
				{
					$rest = $daysDiff . '&nbsp;'._SBDAYS.',&nbsp;'.$hrsDiff.'&nbsp;'._SBHOURS.',&nbsp;'.$minsDiff.'&nbsp;'._SBMINUTES.',&nbsp;'.$secsDiff.'&nbsp;'._SBSECONDS;
				} 
				elseif ($yearDiff == 1) 
				{
					$rest = $yearDiff . '&nbsp;' . _SBYEAR . ',&nbsp;' . $daysDiff. '&nbsp;'._SBDAYS.',&nbsp;' . $hrsDiff . '&nbsp;'._SBHOURS.',&nbsp;' . $minsDiff . '&nbsp;'._SBMINUTES.',&nbsp;' . $secsDiff . '&nbsp;'._SBSECONDS;
				} 
				elseif ($yearDiff > 1) 
				{
					$rest = $yearDiff . '&nbsp;'._SBYEARS.',&nbsp;' . $daysDiff . '&nbsp;'._SBDAYS.',&nbsp;' . $hrsDiff . '&nbsp;'._SBHOURS.',&nbsp;' . $minsDiff . '&nbsp;&nbsp;'._SBMINUTES.',&nbsp;' . $secsDiff . '&nbsp;'._SBSECONDS;
				}
				    $content .= '<b>'._SUBEXPIREIN.'<br / ><font color="#FF0000">' . $rest . ' </font></b></center>';
			} 
			else 
			{
				if (!empty($subscription_url)) 
				{
					$content .= '<br /><center>'._NOTSUB.'&nbsp;' . $sitename . '.&nbsp;'._SUBFROM.'&nbsp;&nbsp;<a href="'.$subscription_url.'">'._HERE.'</a>&nbsp;'._NOW;
				} 
				else 
				{
					$content .= '<br /><center>'._NOTSUB.'&nbsp;' . $sitename;
				}
			}
			echo $content . ' <br / ><br />';
			if (is_admin($admin)) 
			{
				$subnum = $db->sql_numrows($db->sql_query("SELECT * FROM ".$prefix."_subscriptions WHERE userid='".intval($userinfo['user_id'])."'"));
				if ($subnum != 0) 
				{
					echo '<center><b>'._ADMSUB.'</b></center><br />';
					$row = $db->sql_fetchrow($db->sql_query("SELECT * FROM ".$prefix."_subscriptions WHERE userid='".intval($userinfo['user_id'])."'"));
					$diff = $row['subscription_expire']-time();
					$yearDiff = floor($diff/60/60/24/365);
					$diff -= $yearDiff*60*60*24*365;
					if ($yearDiff < 1) {
						$diff = $row['subscription_expire']-time();
					}
					$daysDiff = floor($diff/60/60/24);
					$diff -= $daysDiff*60*60*24;
					$hrsDiff = floor($diff/60/60);
					$diff -= $hrsDiff*60*60;
					$minsDiff = floor($diff/60);
					$diff -= $minsDiff*60;
					$secsDiff = $diff;
					if ($yearDiff < 1) 
					{
						$rest = $daysDiff . '&nbsp;' . _SBDAYS . ',&nbsp;'.$hrsDiff.'&nbsp;'._SBHOURS.',&nbsp;'.$minsDiff.'&nbsp;'._SBMINUTES.',&nbsp;'.$secsDiff.'&nbsp;'._SBSECONDS;
					} 
					elseif ($yearDiff == 1) 
					{
						$rest = $yearDiff . '&nbsp;' . _SBYEAR . ',&nbsp;' . $daysDiff . '&nbsp;'._SBDAYS.',&nbsp;' . $hrsDiff . '&nbsp;'._SBHOURS.',&nbsp;' . $minsDiff . '&nbsp;'._SBMINUTES . ',&nbsp;' . $secsDiff . '&nbsp;' . _SBSECONDS;
					} 
					elseif ($yearDiff > 1) 
					{
						$rest = $yearDiff . '&nbsp;'. _SBYEARS . ',&nbsp; ' . $daysDiff . '&nbsp;'._SBDAYS.',&nbsp;'.$hrsDiff.'&nbsp;'._SBHOURS.',&nbsp;'.$minsDiff.'&nbsp;'._SBMINUTES.',&nbsp;' . $secsDiff . '&nbsp;'._SBSECONDS;
					}
					$content = '<b>'._ADMSUBEXPIREIN.'<br /><font color="#FF0000">' . $rest . '</font></b><br /><br />';
					echo $content;
				} 
				else 
				{
					echo '<center><b>'._ADMNOTSUB.'</b><br /><br />';
			}
		}
		}		
		if (is_admin($admin)) 
		{
			echo '<br />';
			Open_Table2();
			if ($userinfo['last_ip'] != 0)
			{
				echo '<center><font class="title">'._ADMINFUNCTIONS.'</font><br /><br />'._LASTIP.' <b>'.$userinfo['last_ip'].'</b><br /><br />
				[ <a href="'.$admin_file.'.php?op=ipban&amp;ip='.$userinfo['last_ip'].'">'._BANTHIS.'</a> | <a href="'.$admin_file.'.php?op=modifyUser&amp;firstname='.$userinfo['firstname'].'&amp;lastname='.$userinfo['lastname'].'">'._EDITUSER.'</a> ]</center>';
			} 
			else 
			{
				echo '<center>[ <a href="'.$admin_file.'.php?op=modifyUser&amp;firstname='.$userinfo['firstname'].'&amp;lastname='.$userinfo['lastname'].'">'._EDITUSER.'</a> ]</center>';
			}
			if ($userinfo['karma'] == 0) 
			{ 
				$karma = _KARMAGOOD;
				$karma_help = _KARMAGOODHLP;
				$change_karma = '<a href="index.php?name='.$module_name.'&amp;op=change_karma&ampuser_id='.$userinfo['user_id'].'&amp;karma=1"><img src="images/karma/1.gif" border="0" alt="'._KARMALOW.'" title="'._KARMALOW.'" hspace="5"></a>';
				$change_karma .= '<a href="index.php?name='.$module_name.'&amp;op=change_karma&amp;user_id='.$userinfo['user_id'].'&amp;karma=2"><img src="images/karma/2.gif" border="0" alt="'._KARMABAD.'" title="'._KARMABAD.'" hspace="5"></a>';
				$change_karma .= '<a href="index.php?name='.$module_name.'&amp;op=change_karma&amp;user_id='.$userinfo['user_id'].'&amp;karma=3"><img src="images/karma/3.gif" border="0" alt="'._KARMADEVIL.'" title="'._KARMADEVIL.'" hspace="5"></a>';
			} 
			elseif ($userinfo['karma'] == 1) 
			{
				$karma = _KARMALOW;
				$karma_help = _KARMALOWHLP;
				$change_karma = '<a href="index.php?name='.$module_name.'&amp;op=change_karma&amp;user_id='.$userinfo['user_id'].'&amp;karma=0"><img src="images/karma/0.gif" border="0" alt="'._KARMAGOOD.'" title="'._KARMAGOOD.'" hspace="5"></a>';
				$change_karma .= '<a href="index.php?name='.$module_name.'&amp;op=change_karma&amp;user_id='.$userinfo['user_id'].'&amp;karma=2"><img src="images/karma/2.gif" border="0" alt="'._KARMABAD.'" title="'._KARMABAD.'" hspace="5"></a>';
				$change_karma .= '<a href="index.php?name='.$module_name.'&amp;op=change_karma&amp;user_id='.$userinfo['user_id'].'&amp;karma=3"><img src="images/karma/3.gif" border="0" alt="'._KARMADEVIL.'" title="'._KARMADEVIL.'" hspace="5"></a>';
			} 
			elseif ($userinfo['karma'] == 2) 
			{
				$karma = _KARMABAD;
				$karma_help = _KARMABADHLP;
				$change_karma = '<a href="index.php?name='.$module_name.'&amp;op=change_karma&amp;user_id='.$userinfo['user_id'].'&amp;karma=0"><img src="images/karma/0.gif" border="0" alt="'._KARMAGOOD.'" title="'._KARMAGOOD.'" hspace="5"></a>';
				$change_karma .= '<a href="index.php?name='.$module_name.'&amp;op=change_karma&amp;user_id='.$userinfo['user_id'].'&amp;karma=1"><img src="images/karma/1.gif" border="0" alt="'._KARMALOW.'" title="'._KARMALOW.'" hspace="5"></a>';
				$change_karma .= '<a href="index.php?name=$module_name&amp;op=change_karma&amp;user_id='.$userinfo['user_id'].'&amp;karma=3"><img src="images/karma/3.gif" border="0" alt="'._KARMADEVIL.'" title="'._KARMADEVIL.'" hspace="5"></a>';
			} 
			elseif ($userinfo['karma'] == 3) 
			{
				$karma = _KARMADEVIL;
				$karma_help = _KARMADEVILHLP;
				$change_karma = '<a href="index.php?name='.$module_name.'&amp;op=change_karma&amp;user_id='.$userinfo['user_id'].'&amp;karma=0"><img src="images/karma/0.gif" border="0" alt="'._KARMAGOOD.'" title="'._KARMAGOOD.'" hspace="5"></a>';
				$change_karma .= '<a href="index.php?name='.$module_name.'&amp;op=change_karma&amp;user_id='.$userinfo['user_id'].'&amp;karma=1"><img src="images/karma/1.gif" border="0" alt="'._KARMALOW.'" title="'._KARMALOW.'" hspace="5"></a>';
				$change_karma .= '<a href="index.php?name='.$module_name.'&amp;op=change_karma&amp;user_id='.$userinfo['user_id'].'&amp;karma=2"><img src="images/karma/2.gif" border="0" alt="'._KARMABAD.'" title="'._KARMABAD.'" hspace="5"></a>';
			}
			echo '<center><br /><br />'._USERKARMA.' <img src="images/karma/'.$userinfo['karma'].'.gif" border="0" alt="'.$karma.'" title="'.$karma.'"> ('.$karma.')<br />('.$karma_help.')</center><br /><br />';
			Open_Table2();
			echo '<center><b>'._CHANGEKARMA.' <i>'.$userinfo['firstname'].'&nbsp;'.$userinfo['lastname'].'</i></b><br /><br />';
			echo $change_karma . '</center>';
			Close_Table2();
			echo '<br />';
			echo '<table border="0" width="80%" cellpadding="3" cellspacing="3" align="center">
			<tr><td valign="middle"><img src="images/karma/0.gif" border="0" alt="'._KARMAGOOD.'" title="'._KARMAGOOD.'"></td><td>'._KARMAGOODREF.'</td></tr>
			<tr><td valign="middle"><img src="images/karma/1.gif" border="0" alt="'._KARMALOW.'" title="'._KARMALOW.'"></td><td>'._KARMALOWREF.'</td></tr>
			<tr><td valign="middle"><img src="images/karma/2.gif" border="0" alt="'._KARMABAD.'" title="'._KARMABAD.'"></td><td>'._KARMABADREF.'</td></tr>
			<tr><td valign="middle"><img src="images/karma/3.gif" border="0" alt="'._KARMADEVIL.'" title="'._KARMADEVIL.'"></td><td>'._KARMADEVILREF.'</td></tr></table>';
			Close_Table2();
		}
		echo '</center></font>';
	} 
	else 
	{
		echo '<center>'._NOINFOFOR.'&nbsp;'.htmlentities($firstname).'&nbsp;'.htmlentities($lastname).'</center>';
	}
	Close_Table();
	if ((isset($cookie[1])) && $my_headlines == 1 && ($firstname == $cookie[1]) && (isset($cookie[2])) && ($lastname == $cookie[2]) && ($userinfo['user_password'] == $cookie[3])) {
		echo '<br>';
		Open_Table();
		echo '<center><b>'._MYHEADLINES.'</b><br /><br />
		'._SELECTASITE.'<br /><br />
		<form action="index.php?name='.$module_name.'" method="post">
		<input type="hidden" name="op" value="userinfo">
		<input type="hidden" name="firstname" value="'.$firstname.'">
		<input type="hidden" name="lastname" value="'.$lastname.'">
		<input type="hidden" name="bypass" value="'.$bypass.'">
		<input type="hidden" name="url" value="0">
		<select name="hid" onChange="submit()">
		<option value="0">'._SELECTASITE2.'</option>';
		$sql4 = "SELECT hid, sitename FROM ".$prefix."_headlines ORDER BY sitename";
		$headl = $db->sql_query($sql4);
		while($row4 = $db->sql_fetchrow($headl)) 
		{
			$nhid = intval($row4['hid']);
			$hsitename = filter($row4['sitename'], 'nohtml');
			if ($hid == $nhid ) 
			{
				$sel = 'selected';
			} 
			else 
			{
				$sel = '';
			}
			echo '<option value="'.$nhid.'" '.$sel.'>'.$hsitename.'</option>';
		}
		echo '</select></form>
		'._ORTYPEURL.'<br /><br />
		<form action="index.php?name='.$module_name.'" method="post">
		<input type="hidden" name="op" value="userinfo">
		<input type="hidden" name="firstname" value="'.$firstname.'">
		<input type="hidden" name="lastname" value="'.$lastname.'">
		<input type="hidden" name="bypass" value="'.$bypass.'">
		<input type="hidden" name="hid" value="0">
		<input type="text" name="url" size="40" maxlength="200" value="http://">&nbsp;&nbsp;
		<input type="submit" value="'._GO.'"></form>
		</center><br />';
		if ($hid != 0 || ($hid == 0 && $url != "0" && $url != "http://") && !empty($url)) 
		{
			if ($hid != 0) {
				$sql5 = "SELECT sitename, headlinesurl FROM ".$prefix."_headlines WHERE hid='".$hid."'";
				$result5 = $db->sql_query($sql5);
				$row5 = $db->sql_fetchrow($result5);
				$nsitename = filter($row5['sitename'], 'nohtml');
				$url = filter($row5['headlinesurl'], 'nohtml');
				$title = filter($nsitename, 'nohtml');
				$siteurl = eregi_replace('http://', '', $url);
				$siteurl = explode('/', $siteurl);
			} 
			else 
			{
				if (!ereg('http://', $url)) 
				{
					$url = 'http://' . $url;
				}
				$siteurl = eregi_replace('http://', '', $url);
				$siteurl = explode('/', $siteurl);
				$title = 'http://' . $siteurl[0];
			}
			$rdf = parse_url($url);
			$fp = fsockopen($rdf['host'], 80, $errno, $errstr, 15);
			if (!$fp) 
			{
				$content = '<center><font class="content">'._RSSPROBLEM.'</font></center>';
			}
			if ($fp) 
			{
				fputs($fp, 'GET ' . $rdf['path'] . '?' . $rdf['query'] . " HTTP/1.0\r\n");
				fputs($fp, 'HOST: ' . $rdf['host'] . "\r\n\r\n");
				$string	= '';
				while(!feof($fp)) {
					$pagetext = fgets($fp,300);
					$string .= chop($pagetext);
				}
				fputs($fp, "Connection: close\r\n\r\n");
				fclose($fp);
				$items = explode('</item>', $string);
				$content = '<font class="content">';
				for ($i=0;$i<10;$i++) 
				{
					$link = ereg_replace('.*<link>', '', $items[$i]);
					$link = ereg_replace('</link>.*', '',$link);
					$link = stripslashes(check_html($link, 'nohtml'));
					$title2 = ereg_replace('.*<title>', '',$items[$i]);
					$title2 = ereg_replace('</title>.*', '',$title2);
					$title2 = stripslashes(check_html($title2, 'nohtml'));
					if (empty($items[$i]) && $cont != 1) 
					{
						$content = '<center>'._RSSPROBLEM.'</center>';
					} 
					else 
					{
						if (strcmp($link,$title2) && !empty($items[$i])) 
						{
							$cont = 1;
							$content .= '<img src="images/arrow.gif" border="0" hspace="5"><a href="'.$link.'" target="new">'.$title2.'</a><br />';
						}
					}
				}
			}
			if (!empty($content)) 
			{
				Open_Table2();
				echo '<center><b>'._HEADLINESFROM.' <a href="http://' . $siteurl[0].'" target="new">'.$title.'</a></b></center><br />';
				echo $content;
				Close_Table2();
			} 
			elseif (($cont == 0) || (empty($content))) 
			{
				Open_Table2();
				echo '<center>'._RSSPROBLEM.'</center><br />';
				Close_Table2();
			}
			echo '<br />';
		}
		Close_Table();
	}
	if ((isset($cookie[1])) AND $broadcast_msg == 1 AND ($firstname == $cookie[1]) && (isset($cookie[2])) AND ($lastname == $cookie[2]) AND ($userinfo['user_password'] == $cookie[3])) 
	{
		echo "<br>";
		Open_Table();//@todo add lastname support maybe redo the who for names
		echo '<center><b>'._BROADCAST.'</b><br /><br />'._BROADCASTTEXT.'<br /><br />
		<form action="index.php?name='.$module_name.'" method="post">
		<input type="hidden" name="firstname" value="'.$firstname.'">
		<input type="hidden" name="lastname" value="'.$lastname.'">
		<input type="hidden" name="op" value="broadcast">
		<input type="text" size="60" maxlength="255" name="the_message">&nbsp;&nbsp;<input type="submit" value="'._SEND.'">
		</form></center>';
		Close_Table();
	}
	if ($articlecomm == 1) 
	{
		echo '<br />';
		Open_Table();
		echo '<b>'._LAST10COMMENTS.' '.$userinfo['firstname'].'&nbsp;'.$userinfo['lastname'].':</b><br />';
		$sql6 = "SELECT tid, sid, subject FROM ".$prefix."_comments WHERE firstname='".$userinfo['firstname']."' AND lastname='".$userinfo['lastname']."' ORDER BY tid DESC LIMIT 0,10";
		$result6 = $db->sql_query($sql6);
		while($row6 = $db->sql_fetchrow($result6)) 
		{
			$tid = intval($row6['tid']);
			$sid = intval($row6['sid']);
			$subject = filter($row6['subject'], 'nohtml');
			echo '<li><a href="index.php?name=News&amp;file=article&amp;thold=-1&amp;mode=flat&amp;order=0&amp;sid='.$sid.'#'.$tid.'">'.$subject.'</a><br />';
		}
		Close_Table();
	}
	echo "<br />";
	Open_Table();
	echo '<b>'._LAST10SUBMISSIONS.'&nbsp;'.$userinfo['firstname'].'&nbsp;'.$userinfo['lastname'].':</b><br>';
	$sql7 = "SELECT sid, title FROM ".$prefix."_stories WHERE firstname='".$userinfo['firstname']."' AND lastname='".$userinfo['lastname']."' ORDER BY sid DESC LIMIT 0,10";
	$result7 = $db->sql_query($sql7);
	while($row7 = $db->sql_fetchrow($result7)) 
	{
		$sid = intval($row7['sid']);
		$title = filter($row7['title'], 'nohtml');
		echo '<li><a href="index.php?name=News&amp;file=article&amp;sid='.$sid.'">'.$title.'</a><br />';
	}
	Close_Table();
	//bottom();
	include("footer.php");
}

function main($user) {
	global $stop, $module_name, $redirect, $mode, $t, $f, $gfx_chk;
	if(!is_user($user)) {
		include('header.php');
		if ($stop) {
			Open_Table();
			echo '<center><font class="title"><b>'._LOGININCOR.'</b></font></center>';
			Close_Table();
			echo '<br />';
		} 
		else 
		{
			Open_Table();
			echo '<center><font class="title"><b>'._USERREGLOGIN.'</b></font></center>';
			Close_Table();
			echo '<br />';
		}
		if (!is_user($user)) 
		{
			Open_Table();
			mt_srand ((double)microtime()*1000000);
			$maxran = 1000000;
			$random_num = mt_rand(0, $maxran);
			echo '<form action="index.php?name='.$module_name.'" method="post">
			<b>'._USERLOGIN.'</b><br /><br />
			<table border="0"><tr><td>
			'._FIRSTNAME.':</td><td><input type="text" name="firstname" size="15" maxlength="31"></td></tr>
			<tr><td>'._LASTNAME.':</td><td><input type="text" name="lastname" size="15" maxlength="31"></td></tr>
			<tr><td>'._PASSWORD.':</td><td><input type="password" name="user_password" size="15" maxlength="20"></td></tr>';
			if (extension_loaded("gd") && ($gfx_chk == 2 || $gfx_chk == 4 || $gfx_chk == 5 || $gfx_chk == 7)) 
			{
				echo '<tr><td colspan="2">'._SECURITYCODE.': <img src="?gfx=gfx&amp;random_num='.$random_num.'" border="1" alt="'._SECURITYCODE.'" title="'._SECURITYCODE.'"></td></tr>
				<tr><td colspan="2">'._TYPESECCODE.': <input type="text" name="gfx_check" SIZE="7" MAXLENGTH="6"></td></tr>
				<input type="hidden" name="random_num" value="'.$random_num.'">';
			}
			echo '</table><input type="hidden" name="redirect" value="'.$redirect.'">
			<input type="hidden" name="mode" value='.$mode.'>
			<input type="hidden" name="f" value='.$f.'>
			<input type="hidden" name="t" value='.$t.'>
			<input type="hidden" name="op" value="login">
			<input type="submit" value="'._LOGIN.'"></form><br />
			<center><font class="content">[ <a href="index.php?name='.$module_name.'&amp;op=pass_lost">'._PASSWORDLOST.'</a> | <a href="index.php?name='.$module_name.'&amp;op=new_user">'._REGNEWUSER.'</a> ]</font></center>';
			Close_Table();
		}
		include("footer.php");
	} 
	elseif (is_user($user)) 
	{
		global $cookie;
		cookiedecode($user);
		userinfo($cookie[1], $cookie[2]);
	}
}

function new_user() 
{
	global $my_headlines, $module_name, $db, $gfx_chk, $user;
	if (!is_user($user)) 
	{
		mt_srand ((double)microtime()*1000000);
		$maxran = 1000000;
		$random_num = mt_rand(0, $maxran);
		include('header.php');
		Open_Table();
		echo '<center><font class="title"><b>'._USERREGLOGIN.'</b></font></center>';
		Close_Table();
		echo '<br />';
		Open_Table();
		echo '<form action="index.php?name='.$module_name.'" method="post">
		<b>'._REGNEWUSER.'</b> ('._ALLREQUIRED.')<br /><br />
		<table cellpadding="0" cellspacing="10" border="0">
		<tr><td>'._FIRSTNAME.':</td><td><input type="text" name="firstname" size="30" maxlength="31"></td></tr>
		<tr><td>'._LASTNAME.':</td><td><input type="text" name="lastname" size="30" maxlength="31"></td></tr>
		<tr><td>'._EMAIL.':</td><td><input type="text" name="user_email" size="30" maxlength="255"></td></tr>
		<tr><td>'._PASSWORD.':</td><td><input type="password" name="user_password" size="11" maxlength="40"></td></tr>
		<tr><td>'._RETYPEPASSWORD.':</td><td><input type="password" name="user_password2" size="11" maxlength="40"><br /><font class="tiny">('._BLANKFORAUTO.')</font></td></tr>';
		if (extension_loaded("gd") && ($gfx_chk == 3 || $gfx_chk == 4 || $gfx_chk == 6 || $gfx_chk == 7)) 
		{
			echo '<tr><td>'._SECURITYCODE.':</td><td><img src="?gfx=gfx&amp;random_num='.$random_num.'" border="1" alt="'._SECURITYCODE.'" title="'._SECURITYCODE.'"></td></tr>
			<tr><td>'._TYPESECCODE.':</td><td><input type="text" NAME="gfx_check" SIZE="7" MAXLENGTH="6"></td></tr>
			<input type="hidden" name="random_num" value="'.$random_num.'">';
		}
		echo '<tr><td colspan="2">
		<input type="hidden" name="op" value="new user">
		<input type="submit" value="'._NEWUSER.'">
		</td></tr></table>
		</form><br />
		'._YOUWILLRECEIVE.'<br /><br />
		'._COOKIEWARNING.'<br />
		'._ASREGUSER.'<br />
		<ul>
		<li>'._ASREG1.'
		<li>'._ASREG2.'
		<li>'._ASREG3.'
		<li>'._ASREG4.'
		<li>'._ASREG5;
		$handle = opendir('themes');
		$thmcount = 0;
		while ($file = readdir($handle)) 
		{
			if ((!ereg("[.]",$file) && file_exists('themes/'.$file.'/theme.php'))) 
			{
				$thmcount++;
			}
		}
		closedir($handle);
		if ($thmcount > 1) 
		{
			echo '<li>'._ASREG6;
		}
		$sql = "SELECT custom_title FROM ".$prefix."_modules WHERE active='1' AND view='1' AND inmenu='1'";
		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result)) 
		{
			$custom_title = filter($row['custom_title'], "nohtml");
			if (!empty($custom_title)) 
			{
				echo '<li>'._ACCESSTO.'&nbsp;' .$custom_title;
			}
		}
		$sql = "SELECT title FROM ".$prefix."_blocks WHERE active='1' AND view='1'";
		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result)) 
		{
			$b_title = filter($row[title], "nohtml");
			if (!empty($b_title)) 
			{
				echo '<li>'._ACCESSTO.'&nbsp;' . $b_title;
			}
		}
		if (is_active("Journal")) 
		{
			echo '<li>'._CREATEJOURNAL;
		}
		if ($my_headlines == 1) 
		{
			echo '<li>'._READHEADLINES;
		}
		echo '<li>'._ASREG7.'
		</ul>
		'._REGISTERNOW.'<br />
		'._WEDONTGIVE.'<br /><br />
		<center><font class="content">[ <a href="index.php?name='.$module_name.'">
		'._USERLOGIN.'</a> | <a href="index.php?name='.$module_name.'&amp;op=pass_lost">
		'._PASSWORDLOST.'</a> ]</font></center>';
		Close_Table();
		include("footer.php");
	} 
	elseif (is_user($user)) 
	{
		global $cookie;
		cookiedecode($user);
		userinfo($cookie[1], $cookie[2]);
	}
}

function pass_lost() 
{
	global $user, $module_name;
	if (!is_user($user)) 
	{
		include('header.php');
		Open_Table();
		echo '<center><font class="title"><b>'._USERREGLOGIN.'</b></font></center>';
		Close_Table();
		echo '<br />';
		Open_Table();
		echo '<b>'._PASSWORDLOST.'</b><br /><br />
		'._NOPROBLEM.'<br /><br />
		<form action="index.php?name='.$module_name.'" method="post">
		<table border="0"><tr><td>
		'._FIRSTNAME.':</td><td><input type="text" name="firstname" size="15" maxlength="31"></td></tr>
		'._LASTNAME.':</td><td><input type="text" name="firstname" size="15" maxlength="31"></td></tr>
		<tr><td>'._CONFIRMATIONCODE.':</td><td><input type="text" name="code" size="11" maxlength="10"></td></tr></table><br />
		<input type="hidden" name="op" value="mailpasswd">
		<input type="submit" value="'._SENDPASSWORD.'"></form><br />
		<center><font class="content">[ <a href="index.php?name='.$module_name.'">'._USERLOGIN.'</a> | <a href="index.php?name='.$module_name.'&amp;op=new_user">'._REGNEWUSER.'</a> ]</font></center>';
		Close_Table();
		include('footer.php');
	} 
	elseif(is_user($user)) 
	{
		global $cookie;
		cookiedecode($user);
		userinfo($cookie[1], $cookie[2]);
	}
}

function logout() 
{
	global $prefix, $db, $user, $cookie, $redirect;
	cookiedecode($user);
	$r_firstname = $cookie[1];
	$r_lastname = $cookie[2];
	setcookie("user", false);
	$db->sql_query("DELETE FROM ".$prefix."_session WHERE firstname='".$r_firstname."' AND lastname='".$r_lastname."'");
	$user = '';
	include('header.php');
	Open_Table();
	if (!empty($redirect)) {
		echo '<META HTTP-EQUIV="refresh" content="3;URL=index.php?name='.$redirect.'">';
	} else {
		echo '<META HTTP-EQUIV="refresh" content="3;URL=index.php">';
	}
	echo '<center><font class="option"><b>'._YOUARELOGGEDOUT.'</b></font></center>';
	Close_Table();
	include("footer.php");
}

//@todo add lastname support
function mail_password($firstname, $lastname, $code) 
{
	global $sitename, $adminmail, $site_url, $user_prefix, $db, $module_name;
	
	$firstname = substr(htmlspecialchars(str_replace("\'", "'", trim($firstname))), 0, 31);
	$firstname = rtrim($firstname, "\\");	
	$firstname = str_replace("'", "\'", $firstname);
	$lastname = substr(htmlspecialchars(str_replace("\'", "'", trim($lastname))), 0, 31);
	$lastname = rtrim($lastname, "\\");	
	$lastname = str_replace("'", "\'", $lastname);
	
	$sql = "SELECT user_email, user_password FROM ".$user_prefix."_users WHERE firstname='".$firstname."' AND lastname='".$lastname."'";
	$result = $db->sql_query($sql);
	if($db->sql_numrows($result) == 0) 
	{
		include('header.php');
		Open_Table();
		echo '<center>'._SORRYNOUSERINFO.'</center>';
		Close_Table();
		include('footer.php');
	} 
	else 
	{
		$host_name = $_SERVER['REMOTE_ADDR'];
		$row = $db->sql_fetchrow($result);
		$user_email = filter($row['user_email'], "nohtml");
		$user_password = $row['user_password'];
        $user_password = htmlspecialchars(stripslashes($user_password));
		$areyou = substr($user_password, 0, 10);
		if ($areyou == $code) 
		{
			$newpass = makepass();
			$message = _USERACCOUNT.'&nbsp;'.$firstname.'&nbsp;'.$lastname.'&nbsp;'._AT.'&nbsp;'.$sitename.'&nbsp;'._HASTHISEMAIL.'&nbsp;&nbsp;'._AWEBUSERFROM.'&nbsp;'.$host_name.'&nbsp;'._HASREQUESTED.'<br /><br />'._YOURNEWPASSWORD.'&nbsp;'.$newpass.'<br /><br />&nbsp;'._YOUCANCHANGE.'&nbsp;'.$site_url.'/index.php?name='.$module_name.'<br /><br />'._IFYOUDIDNOTASK;
			$subject = _USERPASSWORD4.'&nbsp;'.$firstname.'&nbsp;'.$lastname;
			//@todo html email
			mail($user_email, $subject, $message, "From: $adminmail\nX-Mailer: PHP/" . phpversion());
			/* Next step: add the new password to the database */
			$cryptpass = md5($newpass);
			$query = "UPDATE ".$user_prefix."_users SET user_password='$cryptpass' WHERE firstname='".$firstname."' AND lastname='".$lastname."'";
			if (!$db->sql_query($query)) 
			{
				echo _UPDATEFAILED;
			}
			include ('header.php');
			Open_Table();
			echo '<center>'._PASSWORD4.'&nbsp;'.$firstname.'&nbsp;'.$lastname.'&nbsp;'._MAILED.'<br /><br />'._GOBACK.'</center>';
			Close_Table();
			include ('footer.php');
			/* If no Code, send it */
		} 
		else 
		{
			$sql = "SELECT user_email, user_password FROM ".$user_prefix."_users WHERE firstname='$firstname' AND lastname='$lastname'";
			$result = $db->sql_query($sql);
			if($db->sql_numrows($result) == 0) 
			{
				include ('header.php');
				Open_Table();
				echo '<center>'._SORRYNOUSERINFO.'</center>';
				Close_Table();
				include ('footer.php');
			} 
			else 
			{
				$host_name = $_SERVER['REMOTE_ADDR'];
				$row = $db->sql_fetchrow($result);
				$user_email = filter($row['user_email'], "nohtml");
				$user_password = $row['user_password'];
				$areyou = substr($user_password, 0, 10);
				$message = _USERACCOUNT.'&nbsp;'.$firstname.'&nbsp;'.$lastname.'&nbsp;'._AT.'&nbsp;'.$sitename.'&nbsp;'._HASTHISEMAIL.'&nbsp;'._AWEBUSERFROM.'&nbsp;'.$host_name.'&nbsp;'._CODEREQUESTED.'<br /><br />'._YOURCODEIS.'&nbsp;'.$areyou.'<br /><br />'._WITHTHISCODE.'&nbsp;'.$site_url.'/index.php?name='.$module_name.'&amp;op=pass_lost<br />'._IFYOUDIDNOTASK2;
				$subject = _CODEFOR . '&nbsp;' . $firstname . '&nbsp;' . $lastname;
				//@todo html email
				mail($user_email, $subject, $message, "From: $adminmail\nX-Mailer: PHP/" . phpversion());
				
				include ('header.php');
				Open_Table();
				echo '<center>'._CODEFOR.'&nbsp;'.$firstname.'&nbsp;'.$lastname.'&nbsp;'._MAILED.'<br /><br />'._GOBACK.'</center>';
				Close_Table();
				include ('footer.php');
			}
		}
	}
}

function docookie($uid, $firstname, $lastname, $password, $storynum, $umode, $uorder, $thold, $noscore, $ublockon, $theme, $commentmax, $mature) 
{
	$info = base64_encode($uid.':'.$firstname.':'.$lastname.':'.$password.':'.$storynum.':'.$umode.':'.$uorder.':'.$thold.':'.$noscore.':'.$ublockon.':'.$theme.':'.$commentmax.':'.$mature);
	setcookie('user', $info, time()+2592000);
}

function login($firstname, $lastname, $user_password, $redirect, $mode, $f, $t, $random_num, $gfx_check) 
{
	global $setinfo, $user_prefix, $db, $module_name, $prefix;
	//$user_password = htmlspecialchars(stripslashes($user_password));
	$user_password = htmlspecialchars(stripslashes($user_password));
	include('config.php');
	$sql = "SELECT user_password, user_id, storynum, umode, uorder, thold, noscore, ublockon, theme, commentmax, mature FROM ".$user_prefix."_users WHERE firstname='".$firstname."' AND lastname='".$lastname."'";
	$result = $db->sql_query($sql);
	$setinfo = $db->sql_fetchrow($result);
	$forward = ereg_replace('redirect=', '', $redirect);
	if (($db->sql_numrows($result) == true) && ($setinfo['user_id'] != 1) && (!empty($setinfo['user_password']))) 
	{
		$dbpass=$setinfo['user_password'];
		$non_crypt_pass = $user_password;
		$old_crypt_pass = crypt($user_password,substr($dbpass,0,2));
		$new_pass = md5($user_password);
		if (($dbpass == $non_crypt_pass) || ($dbpass == $old_crypt_pass)) {
			$db->sql_query("UPDATE ".$user_prefix."_users SET user_password='$new_pass' WHERE firstname='$firstname' AND lastname='$lastname'");
			$sql = "SELECT user_password FROM ".$user_prefix."_users WHERE firstname='".$firstname."' AND lastname='".$lastname."'";
			$result = $db->sql_query($sql);
			$row = $db->sql_fetchrow($result);
			$dbpass = $row['user_password'];
		}
		if ($dbpass != $new_pass) {
			Header("Location: index.php?name=$module_name&stop=1");
			return;
		}
		$datekey = date("F j");
		$rcode = hexdec(md5($_SERVER['HTTP_USER_AGENT'] . $sitekey . $random_num . $datekey));
		$code = substr($rcode, 2, 6);
		if (extension_loaded("gd") AND $code != $gfx_check AND ($gfx_chk == 2 OR $gfx_chk == 4 OR $gfx_chk == 5 OR $gfx_chk == 7)) 
		{
			Header('Location: index.php?name='.$module_name.'&stop=1');
			die();
		} 
		else 
		{
			docookie($setinfo['user_id'], $firstname, $lastname, $new_pass, $setinfo['storynum'], $setinfo['umode'], $setinfo['uorder'], $setinfo['thold'], $setinfo['noscore'], $setinfo['ublockon'], $setinfo['theme'], $setinfo['commentmax'], $setinfo['mature']);
			$ip_address = $_SERVER['REMOTE_ADDR'];
			$uname = $_SERVER['REMOTE_ADDR'];
			//@todo lastname + redo uname
			$guest_lastname = '';
			$db->sql_query("DELETE FROM ".$prefix."_session WHERE firstname='".$uname."' AND lastname='".$guest_lastname."' AND guest='1'");
			$db->sql_query("UPDATE ".$prefix."_users SET last_ip='".$ip_address."' WHERE firstname='".$firstname."' AND lastname='".$lastname."'");
		}
		if (empty($redirect) || empty($mode) || empty($t)) 
		{
			Header('Location: index.php?name=Your_Account&amp;op=userinfo&amp;bypass=1&amp;firstname='.$firstname.'&amp;lastname='.$lastname);
		}
	} 
	else 
	{
		Header('Location: index.php?name='.$module_name.'&stop=1');
	} 
}

function edituser() 
{
	global $prefix, $db, $user, $userinfo, $cookie, $module_name, $bgcolor2, $bgcolor3;
	cookiedecode($user);
	getusrinfo($user);
	echo $userinfo['lastname'];
	if ((is_user($user)) && (strtolower($userinfo['firstname']) == strtolower($cookie[1])) && (strtolower($userinfo['lastname']) == strtolower($cookie[2])) && ($userinfo['user_password'] == $cookie[3])) 
	{
		include('header.php');
		Open_Table();
		echo '<center><font class="title"><b>'._PERSONALINFO.'</b></font></center>';
		Close_Table();
		echo '<br />';
		Open_Table();
		nav();
		Close_Table();
		echo '<br />';
		if (!preg_match('#^http[s]?:\/\/#i', $userinfo['user_website'])) 
		{
			$userinfo['user_website'] = 'http://' . $userinfo['user_website'];
		}
		if (!preg_match('#^http[s]?\\:\\/\\/[a-z0-9\-]+\.([a-z0-9\-]+\.)?[a-z]+#i', $userinfo['user_website'])) {
			$userinfo['user_website'] = '';
		}
		Open_Table();
		echo '<table class=forumline cellpadding="3" border="0" width="100%">
		<form name="Register" action="index.php?name='.$module_name.'" method="post">
		<tr><td><b>'._USRFIRSTNAME.'</b>:</td><td><b>'.$userinfo['firstname'].'</b></td></tr>
		<tr><td><b>'._USRLASTNAME.'</b>:</td><td><b>'.$userinfo['lastname'].'</b></td></tr>
		<tr><td><b>'._UREALNAME.'</b>:<br />'._OPTIONAL.'</td><td>
		<input type="text" name="realname" value="'.$userinfo['name'].'" size="50" maxlength="60"></td></tr>
		<tr><td><b>'._UREALEMAIL.':</b><br>'._REQUIRED.'</td>
		<td><input type="text" name="user_email" value="'.$userinfo['user_email'].'" size="50" maxlength="255"><br />'._EMAILNOTPUBLIC.'</td></tr>
		<tr><td><b>'._UFAKEMAIL.':</b><br />'._OPTIONAL.'</td>
		<td><input type="text" name="femail" value="'.$userinfo['femail'].'" size="50" maxlength="255"><br />'._EMAILPUBLIC.'</td></tr>
		<tr><td><b>'._YOURHOMEPAGE.':</b><br />'._OPTIONAL.'</td>
		<td><input type="text" name="user_website" value="'.$userinfo['user_website'].'" size="50" maxlength="255"></td></tr>';
		echo '<tr><td><b>'._RECEIVENEWSLETTER.'</b></td><td>';
		if ($userinfo['newsletter'] == 1) 
		{
			echo '<input type="radio" name="newsletter" value="1" checked>'._YES.'&nbsp;<input type="radio" name="newsletter" value="0">'._NO;
		} 
		elseif ($userinfo['newsletter'] == 0) 
		{
			echo '<input type="radio" name="newsletter" value="1">'._YES.' &nbsp;<input type="radio" name="newsletter" value="0" checked>'._NO;
		}
		echo '</td></tr>
		<tr><td><b>'._SIGNATURE.':</b><br>'._OPTIONAL.'</td>
		<td><input type=\"text\" name="user_sig" value="'.$userinfo['user_sig'].'" size="50" maxlength="255"><br />'._255CHARMAX.'</td></tr>
		<tr><td><b>'._PASSWORD.'</b>:</td><br>
		<td><input type="password" name="user_password" size="22" maxlength="20">&nbsp;&nbsp;&nbsp;<input type="password" name="vpass" size="22" maxlength="20"><br />'._TYPENEWPASSWORD.'</td></tr>
		<tr><td>&nbsp;</td><td>
		<input type="hidden" name="firstname" value="'.$userinfo['firstname'].'">
		<input type="hidden" name="lastname" value="'.$userinfo['lastname'].'">
		<input type="hidden" name="user_id" value="'.intval($userinfo['user_id']).'">
		<input type="hidden" name="op" value="saveuser">
		<input class=button type="submit" value="'._SAVECHANGES.'">
		</form></td></tr></table>';
		Close_Table();
		include("footer.php");
	} else {
		main($user);
	}
}

##############################
//@todo mature
function saveuser($realname, $user_email, $femail, $user_website, $newsletter, $user_sig, $user_password, $vpass, $firstname, $lastname, $user_id) 
{
	global $user, $cookie, $userinfo, $EditedMessage, $user_prefix, $db, $module_name, $minpass;
	$user_password = htmlspecialchars(stripslashes($user_password));
	cookiedecode($user);
	$check_firstname = $cookie[1];
	$check_firstname = filter($check_firstname, "nohtml", 1);

	$check_lastname = $cookie[2];
	$check_lastname = filter($check_lastname, "nohtml", 1);	
	
	$check_password = $cookie[3];//check2
	$sql = "SELECT user_id, user_password FROM ".$user_prefix."_users WHERE firstname='$check_firstname' AND lastname='$check_lastname'";
	$result = $db->sql_query($sql);
	$row = $db->sql_fetchrow($result);
	$vuid = intval($row['user_id']);
	$ccpass = filter($row['user_password'], "nohtml", 1);
	$ccpass = htmlspecialchars(stripslashes($ccpass));
	$user_sig = filter($user_sig, "", 1);
	$user_email = filter($user_email, "nohtml", 1);
	$femail = filter($femail, "nohtml", 1);
	$user_website = filter($user_website, "nohtml", 1);
	$realname = filter($realname, "nohtml", 1);
	$newsletter = intval($newsletter);
	$firstname = filter($firstname, "nohtml", 1);
	$lastname = filter($lastname, "nohtml", 1);
	$user_id = intval($user_id);
	if (($user_id == $vuid) && ($check_password == $ccpass)) {
		if (!preg_match('#^http[s]?:\/\/#i', $user_website)) 
		{
			$user_website = "http://" . $user_website;
		}
		if (!preg_match('#^http[s]?\\:\\/\\/[a-z0-9\-]+\.([a-z0-9\-]+\.)?[a-z]+#i', $user_website)) 
		{
			$user_website = '';
		}
		if ((isset($user_password)) && ("$user_password" != "$vpass")) 
		{
			echo "<center>"._PASSDIFFERENT."</center>";
		} 
		elseif ((!empty($user_password)) && (strlen($user_password) < $minpass)) 
		{
			echo "<center>"._YOUPASSMUSTBE." <b>$minpass</b> "._CHARLONG."</center>";
		} 
		else 
		{
			if (!empty($user_password)) 
			{
				cookiedecode($user);
				$db->sql_query("LOCK TABLES ".$user_prefix."_users WRITE");
				$user_password = md5($user_password);
				$db->sql_query("UPDATE ".$user_prefix."_users SET name='$realname', user_email='$user_email', femail='$femail', user_website='$user_website', user_password='$user_password', user_sig='$user_sig', newsletter='$newsletter' WHERE user_id='$user_id'");
				$sql = "SELECT user_id, firstname, lastname, user_password, storynum, umode, uorder, thold, noscore, ublockon, theme, mature FROM ".$user_prefix."_users WHERE firstname='$firstname' AND lastname='$lastname' AND user_password='$user_password'";
				$result = $db->sql_query($sql);
				if ($db->sql_numrows($result) == 1) 
				{
					$userinfo = $db->sql_fetchrow($result);
					docookie($userinfo['user_id'], $userinfo['firstname'], $userinfo['lastname'], $userinfo['user_password'], $userinfo['storynum'], $userinfo['umode'], $userinfo['uorder'], $userinfo['thold'], $userinfo['noscore'], $userinfo['ublockon'], $userinfo['theme'], $userinfo['commentmax'], $userinfo['mature']);
				} 
				else 
				{
					echo '<center>'._SOMETHINGWRONG.'</center><br />';
				}
				$db->sql_query("UNLOCK TABLES");
			} 
			else 
			{
				$db->sql_query("UPDATE ".$user_prefix."_users SET name='$realname', user_email='$user_email', femail='$femail', user_website='$user_website', user_sig='$user_sig', newsletter='$newsletter' WHERE user_id='$user_id'");
			}
			Header('Location: index.php?name='.$module_name.'&amp;op=edituser');
		}
	}
}

function edithome() 
{
	global $user, $userinfo, $Default_Theme, $cookie, $broadcast_msg, $user_news, $storyhome, $module_name;
	cookiedecode($user);
	getusrinfo($user);
	if ((is_user($user)) && (strtolower($userinfo['firstname']) == strtolower($cookie[1])) && (strtolower($userinfo['lastname']) == strtolower($cookie[2])) && ($userinfo['user_password'] == $cookie[3])) 
	{
		include ('header.php');
		Open_Table();
		echo '<center><font class="title"><b>'._HOMECONFIG.'</b></font></center>';
		Close_Table();
		echo '<br />';
		Open_Table();
		nav();
		Close_Table();
		echo '<br />';
		if(empty($userinfo['theme'])) 
		{
			$userinfo['theme'] = $Default_Theme;
		}
		Open_Table();
		if ($userinfo['storynum'] == '10') { $sel1 = 'selected'; }
		if ($userinfo['storynum'] == '20') { $sel2 = 'selected'; }
		if ($userinfo['storynum'] == '30') { $sel3 = 'selected'; }
		if ($userinfo['storynum'] == '40') { $sel4 = 'selected'; }
		if ($userinfo['storynum'] == '50') { $sel5 = 'selected'; }
		
		echo '<table width="100%"><tr><td>';		
		echo '<form action="index.php?name='.$module_name.'" method="post">';
		if ($user_news == 1) 
		{
			echo '<b>'._NEWSINHOME.'</b></td><td>
			<select name="storynum">
			<option value="10" '.$sel1.'>10</option>
			<option value="20" '.$sel2.'>20</option>
			<option value="30" '.$sel3.'>30</option>
			<option value="40" '.$sel4.'>40</option>
			<option value="50" '.$sel5.'>50</option>
			</select>
			</td></tr><tr><td>';
		} 
		else 
		{
			echo '<input type="hidden" name="storynum" value="'.$storyhome.'">';
		}
		if ($userinfo['ublockon']==1) 
		{
			$sel = 'checked';
		} 
		else 
		{ 
			$sel = ''; 
		}
		if ($broadcast_msg == 1) 
		{
			if ($userinfo['broadcast'] == 1) 
			{
				$sel1 = 'checked';
				$sel2 = '';
			} 
			elseif ($userinfo['broadcast'] == 0) 
			{
				$sel1 = '';
				$sel2 = 'checked';
			}
			echo '<tr><td>';
			echo '<b>'._MESSAGEACTIVATE.'</b></td><td><input type="radio" name="broadcast" value="1" '.$sel1.'> '._YES.' &nbsp;&nbsp;<input type="radio" name="broadcast" value="0" '.$sel2.'>'._NO;
			echo '</td></tr>';
		} 
		else 
		{
			echo '<input type="hidden" name="broadcast" value="1">';
		}
		echo '<tr><td><input type="checkbox" name="ublockon" '.$sel.'>
		 <b>'._ACTIVATEPERSONAL.'</b>
		<br>'._CHECKTHISOPTION.'
		</td><td><textarea cols="55" rows="10" name="ublock">'.$userinfo['ublock'].'</textarea><br />'._YOUCANUSEHTML.'</td></tr><tr><td>
		<input type="hidden" name="firstname" value="'.$userinfo['firstname'].'">
		<input type="hidden" name="lastname" value="'.$userinfo['lastname'].'">
		<input type="hidden" name="user_id" value="'.intval($userinfo['user_id']).'">
		<input type="hidden" name="op" value="savehome">
		&nbsp;</td><td><input type="submit" value=\"'._SAVECHANGES.'">
		</form></td></tr></table>';
		Close_Table();
		include ("footer.php");
	} else {
		main($user);
	}
}

function chgtheme() 
{
	global $user, $userinfo, $Default_Theme, $cookie, $module_name, $db, $prefix;
	cookiedecode($user);
	getusrinfo($user);
	if ((is_user($user)) AND (strtolower($userinfo['firstname']) == strtolower($cookie[1])) AND (strtolower($userinfo['lastname']) == strtolower($cookie[2])) AND ($userinfo['user_password'] == $cookie[3])) 
	{
		$row = $db->sql_fetchrow($db->sql_query("SELECT overwrite_theme from ".$prefix."_config"));
		$overwrite_theme = intval($row['overwrite_theme']);
		if ($overwrite_theme != 1) 
		{
			Header('Location: index.php?name=' . $module_name);	
			die();
		}
		include ('header.php');
		Open_Table();
		echo '<center><font class="title"><b>'._THEMESELECTION.'</b></font></center>';
		Close_Table();
		echo '<br />';
		Open_Table();
		nav();
		Close_Table();
		echo '<br />';
		Open_Table();
		echo '<center>
		<form action="index.php?name='.$module_name.'" method="post">
		<b>'._SELECTTHEME.'</b><br /><br />
		<select name="theme">';
		$handle = opendir('themes');
		while ($file = readdir($handle)) 
		{
			if ( (!ereg("[.]",$file) && file_exists('themes/'.$file.'/theme.php')) ) 
			{
				$themelist .= $file . ' ';
			}
		}
		closedir($handle);
		$themelist = explode(' ', $themelist);
		sort($themelist);
		for ($i=0; $i < sizeof($themelist); $i++) 
		{
			if(!empty($themelist[$i])) 
			{
				echo '<option value="'.$themelist[$i].'"';
				if(((empty($userinfo['theme'])) && ($themelist[$i]==$Default_Theme)) || ($userinfo['theme']==$themelist[$i])) echo 'selected';
				echo '>'.$themelist[$i]."\n";
			}
		}
		if(empty($userinfo['theme'])) $userinfo['theme'] = $Default_Theme;
		echo '</select><br /><br />
		'._THEMETEXT1.'<br />
		'._THEMETEXT2.'<br />
		'._THEMETEXT3.'<br /><br />
		<input type="hidden" name="user_id" value="'.$userinfo['user_id'].'">
		<input type="hidden" name="op" value="savetheme">
		<input type="submit" value="'._SAVECHANGES.'">
		</form>';
		Close_Table();
		include ('footer.php');
	} else {
		main($user);
	}
}


function savehome($user_id, $firstname, $lastname, $storynum, $ublockon, $ublock, $broadcast) 
{
	global $user, $cookie, $userinfo, $user_prefix, $db, $module_name;
	cookiedecode($user);
	$check_firstname = $cookie[1];
	$check_firstname = filter($check_firstname, "nohtml", 1);
	$check_lastname = $cookie[2];
	$check_lastname = filter($check_lastname, "nohtml", 1);
	$check_password = $cookie[3];
	$sql = "SELECT user_id, user_password FROM ".$user_prefix."_users WHERE firstname='".$check_firstname."' AND lastname='".$check_lastname."'";
	$result = $db->sql_query($sql);
	$row = $db->sql_fetchrow($result);
	$vuid = intval($row['user_id']);
	$ccpass = filter($row['user_password'], 'nohtml', 1);
	if (($user_id == $vuid) && ($check_password == $ccpass)) 
	{
		if(isset($ublockon)) $ublockon = true; else $ublockon = false;
		$ublock = FixQuotes($ublock);
		$db->sql_query("UPDATE ".$user_prefix."_users SET storynum='".$storynum."', ublockon='".$ublockon."', ublock='".$ublock."', broadcast='".$broadcast."' WHERE user_id='".$user_id."'");
		getusrinfo($user);
		docookie($userinfo['user_id'], $userinfo['firstname'], $userinfo['lastname'], $userinfo['user_password'], $userinfo['storynum'], $userinfo['umode'], $userinfo['uorder'], $userinfo['thold'], $userinfo['noscore'], $userinfo['ublockon'], $userinfo['theme'], $userinfo['commentmax'], $userinfo['mature']);
		Header('Location: index.php?name='.$module_name.'&amp;op=edithome');
	}
}

function savetheme($user_id, $theme) 
{
	global $prefix, $user, $cookie, $userinfo, $user_prefix, $db, $module_name;
	$row = $db->sql_fetchrow($db->sql_query("SELECT overwrite_theme from ".$prefix."_config"));
	$overwrite_theme = intval($row['overwrite_theme']);
	if ($overwrite_theme != 1) 
	{
		Header('Location: index.php?name=' . $module_name);	
		die();
	}
	cookiedecode($user);
	$user_id = intval($user_id);
	$check_firstname = $cookie[1];
	$check_firstname = filter($check_firstname, "nohtml", 1);
	$check_lastname = $cookie[2];
	$check_lastname = filter($check_lastname, 'nohtml', 1);
	$check_password = $cookie[3];
	$theme_error = "";
	$sql = "SELECT user_id, user_password FROM ".$user_prefix."_users WHERE firstname='".$check_firstname."' AND lastname='".$check_lastname."'";
	$result = $db->sql_query($sql);
	$row = $db->sql_fetchrow($result);
	$vuid = intval($row['user_id']);
	$ccpass = filter($row['user_password'], "nohtml", 1);
	if (($user_id == $vuid) && ($check_password == $ccpass)) 
	{
		$db->sql_query("UPDATE ".$user_prefix."_users SET theme='".$theme."' WHERE user_id='".$user_id."'");
		getusrinfo($user);
		docookie($userinfo['user_id'], $userinfo['firstname'], $userinfo['lastname'], $userinfo['user_password'], $userinfo['storynum'], $userinfo['umode'], $userinfo['uorder'], $userinfo['thold'], $userinfo['noscore'], $userinfo['ublockon'], $userinfo['theme'], $userinfo['commentmax'], $userinfo['mature']);
		Header('Location: index.php?name='.$module_name.'&amp;op=chgtheme');
	}
}

function editcomm() 
{
	global $user, $userinfo, $cookie, $module_name;
	cookiedecode($user);
	getusrinfo($user);
	if ((is_user($user)) AND (strtolower($userinfo['firstname']) == strtolower($cookie[1])) AND (strtolower($userinfo['lastname']) == strtolower($cookie[2])) AND ($userinfo['user_password'] == $cookie[3])) 
	{
		include ('header.php');
		Open_Table();
		echo '<center><font class="title"><b>'._COMMENTSCONFIG.'</b></font></center>';
		Close_Table();
		echo '<br />';
		Open_Table();
		nav();
		Close_Table();
		echo '<br />';
		Open_Table();
		echo '<table cellpadding="8" border="0"><tr><td>
		<form action="index.php?name='.$module_name.'\" method="post">
		<b>'._DISPLAYMODE.'</b></td><td>
		<select name="umode">';
	?>
    <option value="nocomments" <?php if ($userinfo['umode'] == 'nocomments') { echo 'selected'; } ?>><?php echo _NOCOMMENTS ?>
    <option value="nested" <?php if ($userinfo['umode'] == 'nested') { echo 'selected'; } ?>><?php echo _NESTED ?>
    <option value="flat" <?php if ($userinfo['umode'] == 'flat') { echo 'selected'; } ?>><?php echo _FLAT ?>
    <option value="thread" <?php if (!isset($userinfo['umode']) || (empty($userinfo['umode'])) || $userinfo['umode']=='thread') { echo "selected"; } ?>><?php echo _THREAD ?>
    </select>
    </td></tr><tr><td>
    <b><?php echo _SORTORDER ?></b>
    </td><td>
	<select name="uorder">
    <option value="0" <?php if (!$userinfo['uorder']) { echo 'selected'; } ?>><?php echo _OLDEST ?>
    <option value="1" <?php if ($userinfo['uorder']==1) { echo 'selected'; } ?>><?php echo _NEWEST ?>
    <option value="2" <?php if ($userinfo['uorder']==2) { echo 'selected'; } ?>><?php echo _HIGHEST ?>
    </select>
    </td></tr><tr><td>
    <b><?php echo _THRESHOLD ?></b>
	</td><td>
    <select name="thold">
    <option value="-1" <?php if ($userinfo['thold']==-1) { echo 'selected'; } ?>>-1: <?php echo _UNCUT ?>
    <option value="0" <?php if ($userinfo['thold']==0) { echo 'selected'; } ?>>0: <?php echo _EVERYTHING ?>
    <option value="1" <?php if ($userinfo['thold']==1) { echo 'selected'; } ?>>1: <?php echo _FILTERMOSTANON ?>
    <option value="2" <?php if ($userinfo['thold']==2) { echo 'selected'; } ?>>2: <?php echo _USCORE ?> +2
    <option value="3" <?php if ($userinfo['thold']==3) { echo 'selected'; } ?>>3: <?php echo _USCORE ?> +3
    <option value="4" <?php if ($userinfo['thold']==4) { echo 'selected'; } ?>>4: <?php echo _USCORE ?> +4
    <option value="5" <?php if ($userinfo['thold']==5) { echo 'selected'; } ?>>5: <?php echo _USCORE ?> +5
    </select><br>
    <?php echo _COMMENTSWILLIGNORED ?><br>
	<i><?php echo _SCORENOTE ?></i>
	</td></tr><tr><td>
    <INPUT type="checkbox" name="noscore" <?php if ($userinfo['noscore']==1) { echo 'checked'; } ?>><b> <?php echo _NOSCORES ?></b></td><td><?php echo _HIDDESCORES ?>
    </td></tr><tr><td>
    <b><?php echo _MAXCOMMENT ?></b>
	</td><td>
	<?php
	$commentmax = intval($userinfo['commentmax']);
	if ($commentmax == 1024) { $sel1 = 'selected'; }
	if ($commentmax == 2048) { $sel2 = 'selected'; }
	if ($commentmax == 3072) { $sel3 = 'selected'; }
	if ($commentmax == 4096) { $sel4 = 'selected'; }
	if ($commentmax == 999999999) { $sel5 = 'selected'; }
	echo '<select name="commentmax">
	<option value="1024" '.$sel1.'>1024 Bytes</option>
	<option value="2048" '.$sel2.'>2048 Bytes</option>
	<option value="3072" '.$sel3.'>3072 Bytes</option>
	<option value="4096" '.$sel4.'>4096 Bytes</option>
	<option value="999999999" '.$sel5.'>Unlimited</option>
	</select>';
	?>
	<br />
	<?php echo _TRUNCATES ?>	
    </td></tr><tr><td>&nbsp;</td><td>
    <input type="hidden" name="firstname" value="<?php echo $userinfo['firstname']; ?>">
	<input type="hidden" name="lastname" value="<?php echo $userinfo['lastname']; ?>">
    <input type="hidden" name="user_id" value="<?php echo intval($userinfo['user_id']); ?>">
    <input type="hidden" name="op" value="savecomm">
    <input type="submit" value="<?php echo _SAVECHANGES ?>">
    </form></td></tr></table>
    <?php
    Close_Table();
    echo '<br /><br />';
    include ('footer.php');
	} 
	else 
	{
		main($user);
	}
}

//@todo mature
function savecomm($user_id, $firstname, $lastname, $umode, $uorder, $thold, $noscore, $commentmax) 
{
	global $user, $cookie, $userinfo, $user_prefix, $db, $module_name;
	cookiedecode($user);
	$check_firstname = $cookie[1];
	$check_firstname = filter($check_firstname, "nohtml", 1);
	
	$check_lastname = $cookie[2];
	$check_lastname = filter($check_lastname, "nohtml", 1);
	
	$check_password = $cookie[3];
	$sql = "SELECT user_id, user_password FROM ".$user_prefix."_users WHERE firstname='".$check_firstname."' AND lastname='".$check_lastname."'";
	$result = $db->sql_query($sql);
	$row = $db->sql_fetchrow($result);
	$vuid = intval($row['user_id']);
	$ccpass = filter($row['user_password'], "nohtml", 1);
	if (($user_id == $vuid) && ($check_password == $ccpass)) 
	{
		if(isset($noscore)) $noscore = true; else $noscore = false;
		$db->sql_query("UPDATE ".$user_prefix."_users SET umode='".$umode."', uorder='".$uorder."', thold='".$thold."', noscore='".$noscore."', commentmax='".$commentmax."' WHERE user_id='".$user_id."'");
		getusrinfo($user);
		docookie($userinfo['user_id'], $userinfo['firstname'], $userinfo['lastname'], $userinfo['user_password'], $userinfo['storynum'], $userinfo['umode'], $userinfo['uorder'], $userinfo['thold'], $userinfo['noscore'], $userinfo['ublockon'], $userinfo['theme'], $userinfo['commentmax'], $userinfo['mature']);
		Header('Location: index.php?name='.$module_name.'&amp;op=editcomm');
	}
}

// @todo add lastname support
// @changed $who for $username
function broadcast($the_message, $firstname, $lastname) 
{
	global $prefix, $db, $broadcast_msg, $module_name, $cookie, $user, $userinfo, $user_prefix;
	cookiedecode($user);
	getusrinfo($user);
	$row = $db->sql_fetchrow($db->sql_query("SELECT karma FROM ".$user_prefix."_users WHERE user_id = '".intval($cookie[0])."'"));
	if (($row['karma'] == 2) || ($row['karma'] == 3)) {
		Header("Location: index.php?name=".$module_name);
		die();
	}
	if ((is_user($user)) && (strtolower($firstname) == strtolower($cookie[1])) && (strtolower($userinfo['firstname']) == strtolower($cookie[1])) && (strtolower($lastname) == strtolower($cookie[2])) && (strtolower($userinfo['lastname']) == strtolower($cookie[2])) && ($userinfo['user_password'] == $cookie[3])) 
	{
		$firstname = $cookie[1];
		$lastname = $cookie[1];
		$the_message = filter($the_message, "nohtml", 1);
		if ($broadcast_msg == 1) 
		{
			include('header.php');
			title(_BROADCAST);
			Open_Table();
			$numrows = $db->sql_numrows($db->sql_query("SELECT * FROM ".$prefix."_public_messages WHERE firstname='".$firstname."' AND lastname='".$lastname."'"));
			if (!empty($the_message) && $numrows == 0) 
			{
				$the_time = time();
				$firstname = filter($firstname, "nohtml", 1);
				$lastname = filter($lastname, "nohtml", 1);
				$db->sql_query("INSERT INTO ".$prefix."_public_messages VALUES (NULL, '".$the_message."', '".$the_time."', '".$firstname."', '".$lastname."')");
				update_points(20);
				echo '<center>'._BROADCASTSENT.'<br /><br />[ <a href="index.php?name='.$module_name.'">'._RETURNPAGE.'</a> ]</center>';
			} 
			else 
			{
				echo '<center>'._BROADCASTNOTSENT.'<br /><br />[ <a href="index.php?name='.$module_name.'">'._RETURNPAGE.'</a> ]</center>';
			}
			Close_Table();
			include('footer.php');
		} 
		else 
		{
			echo 'I don\'t like you...';
		}
	}
}

function my_headlines($hid, $url = 0) 
{
	global $prefix, $db, $user;
	if (!is_user($user) || empty($url)) 
	{
		die();
	}
	$hid = intval($hid);
	$sql = "SELECT headlinesurl FROM ".$prefix."_headlines WHERE hid='".$hid."'";
	$result = $db->sql_query($sql);
	$row = $db->sql_fetchrow($result);
	$url = filter($row['headlinesurl'], "nohtml");
	$rdf = parse_url($url);
	$fp = fsockopen($rdf['host'], 80, $errno, $errstr, 15);
	if (!$fp) {
		$content = '<font class="content">Problema!</font>';
		return;
	}
	if ($fp) {
		fputs($fp, 'GET ' . $rdf['path'] . '?' . $rdf['query'] . " HTTP/1.0\r\n");
		fputs($fp, 'HOST: ' . $rdf['host'] . "\r\n\r\n");
		$string	= '';
		while(!feof($fp)) {
			$pagetext = fgets($fp,300);
			$string .= chop($pagetext);
		}
		fputs($fp, "Connection: close\r\n\r\n");
		fclose($fp);
		$items = explode('</item>', $string);
		$content = '<font class="content">';
		for ($i=0;$i<10;$i++) 
		{
			$link = ereg_replace('.*<link>', '', $items[$i]);
			$link = ereg_replace('</link>.*', '', $link);
			$title2 = ereg_replace('.*<title>', '', $items[$i]);
			$title2 = ereg_replace('</title>.*', '', $title2);
			if (empty($items[$i])) 
			{
				$content = '';
				return;
			} 
			else 
			{
				if (strcmp($link, $title)) 
				{
					$cont = 1;
					$content .= '<img src="images/arrow.gif" border="0" hspace="5"><a href="'.$link.'" target="new">'.$title2.'</a><br />';
				}
			}
		}
	}
	echo $content;
}

function CoolSize($size) 
{
	$mb = 1024*1024;
	if ( $size > $mb ) {
		$mysize = sprintf ('%01.2f', $size/$mb) . ' MB';
	} elseif ( $size >= 1024 ) {
		$mysize = sprintf ('%01.2f', $size/1024) . ' Kb';
	} else {
		$mysize = $size . ' bytes';
	}
	return $mysize;
}

function change_karma($user_id, $karma) 
{
	global $admin, $user_prefix, $db, $module_name;
	if (!is_admin($admin)) 
	{
		Header('location: index.php');
		die();
	} 
	else 
	{
		if ($user_id > 1) 
		{
			$karma = intval($karma);
			$db->sql_query("UPDATE ".$user_prefix."_users SET karma='".$karma."' WHERE user_id='".$user_id."'");
			$row = $db->sql_fetchrow($db->sql_query("SELECT firstname, lastname FROM ".$user_prefix."_users WHERE user_id='".$user_id."'"));
			$firstname = filter($row['firstname'], "nohtml");
			$lastname = filter($row['lastname'], "nohtml");
			Header('location: index.php?name='.$module_name.'&amp;op=userinfo&amp;firstname='.$firstname.'&amp;lastname='.$lastname);
			die();
		}
	}
}

function fundsmain() 
{
	global $user, $cookie, $sitename, $prefix, $user_prefix, $db, $admin, $module_name, $admin_file;
	$sql2 = "SELECT * FROM ".$user_prefix."_users WHERE firstname='".$cookie[1]."' AND lastname='".$cookie[2]."'";
	$result2 = $db->sql_query($sql2);
	$num = $db->sql_numrows($result2);
	if ($num != 1) 
	{
		Header('Location: index.php?name=' . $module_name);
		die();
	}
	$userinfo = $db->sql_fetchrow($result2);
	cookiedecode($user);
	getusrinfo($user);
	if ((is_user($user)) && (strtolower($userinfo['firstname']) == strtolower($cookie[1])) && (strtolower($userinfo['lastname']) == strtolower($cookie[2])) && ($userinfo['user_password'] == $cookie[3])) 
	{
		include('header.php');
		Open_Table();
		echo '<center><font class="title"><b>'._MYFUNDS.'</b></font></center>';
		Close_Table();
		echo '<br />';
		Open_Table();
		nav();
		Close_Table();
		echo '<br />';
		Open_Table();
		echo '<center><font class="title"><b>'._ACCOUNT_BALANCES.'</b></font></center>';
		Close_Table();
		echo '<br />';
		Open_Table();
		echo '<table width="400" border="0" align="center">
		  <tr>
		    <td>'.LINDEN_DOLLAR.'</td>
		    <td>'.$userinfo['currency'].'</td>
			<td><a href="index.php?name='.$module_name.'&amp;op=fundshistory_currency">'._VIEW_HISTORY.'</a></td>
		    <td><a href="index.php?name='.$module_name.'&amp;op=fundsdeposit_currency">'._DEPOSIT.'</a></td>
		    <td><a href="index.php?name='.$module_name.'&amp;op=fundswithdraw_currency">'._WITHDRAW.'</a></td>
		  </tr>
		  <tr>
		    <td>'.USD_DOLLAR.'</td>
		    <td>'.$userinfo['dollar'].'</td>
		    <td><a href="index.php?name='.$module_name.'&amp;op=fundshistory_dollar">'._VIEW_HISTORY.'</a></td>
		    <td><a href="index.php?name='.$module_name.'&amp;op=fundsdeposit_dollar">'._DEPOSIT.'</a></td>
		    <td><a href="index.php?name='.$module_name.'&amp;op=fundswithdraw_dollar">'._WITHDRAW.'</a></td>
		  </tr>
		</table>';		
		Close_Table();
		include("footer.php");
	} 
	else 
	{
		main($user);
	}
}

function fundswithdraw_dollar() 
{
	global $user, $cookie, $sitename, $prefix, $user_prefix, $db, $admin, $module_name, $admin_file;
	$sql2 = "SELECT * FROM ".$user_prefix."_users WHERE firstname='".$cookie[1]."' AND lastname='".$cookie[2]."'";
	$result2 = $db->sql_query($sql2);
	$num = $db->sql_numrows($result2);
	if ($num != 1) 
	{
		Header('Location: index.php?name=' . $module_name);
		die();
	}
	$userinfo = $db->sql_fetchrow($result2);
	cookiedecode($user);
	getusrinfo($user);
	include('header.php');
	Open_Table();
	echo '<center><font class="title"><b>'._WITHDRAW_USD.'&nbsp;'.$sitename.'</b></font></center>';
	echo '<br />';
	echo '<center><font class="title"><b>'._WITHDRAW_USD_DISABLED.'</b></font></center>';
	Close_Table();
	include("footer.php");
		
}

/*
$paypal_email = filter($row['paypal_email'], "nohtml");
$paypal_title = filter($row['paypal_title'], "nohtml");
$currency_code = filter($row['currency_code'], "nohtml");
$paypal_return = filter($row['paypal_return'], "nohtml");
$paypal_cancel = filter($row['paypal_cancel'], "nohtml");
*/
function fundsdeposit_dollar()
{
	global $module_name, $paypal_email, $paypal_title, $currency_code, $paypal_return, $paypal_cancel;
	include('header.php');
	Open_Table();
	echo '<center><font class="title"><b>'._DEPOSIT_USD.'</b></font></center>';
	Close_Table();
	echo '<br />';
	Open_Table();
	nav();
	Close_Table();
	echo '<br />';		
	echo '<br />';
	Open_Table();
	echo '<center><font class="title"><b>'._PAYPAL_LIMITS.'</b></font></center>';
	echo '<br />';
    echo '<form action="index.php?name='.$module_name.'" method="post">
	<table cellpadding="0" cellspacing="10" border="0">
    <tr><td>'._PAYPAL_AMOUNT.':</td><td><input type="text" name="merchant_amount" size="10" maxlength="20">&nbsp;'._PAYPAL_DOLLARS.'<br /><font class="tiny">('._PAYPAL_MESSAGE.')</font></td></tr>
	<tr><td colspan="2">
	<input type="hidden" name="merchant_email" value="'.$paypal_email.'">
	<input type="hidden" name="merchant_title" value="'.$paypal_title.'">
	<input type="hidden" name="merchant_currency" value="'.$currency_code.'">
	<input type="hidden" name="merchant_return" value="'.$paypal_return.'">
	<input type="hidden" name="merchant_cancel" value="'.$paypal_cancel.'">
	<input type="hidden" name="op" value="dollar_submit">
    <input type="submit" value="Start Deposit">	
	</td></tr></table>
	</form>
	<br /><img src="modules/'.$module_name.'/images/paypal.gif" border="0"><br />
	<br />'._CREDIT_USAGE.'<br /><br />';
	echo '<center><font class="title"><b>'._ALTERNATE_PAYMENT.'</b></font></center>';	
	Close_Table();
	include("footer.php");
}

function fundswithdraw_currency()
{
    global $module_name, $user, $cookie, $prefix, $user_prefix, $db, $site_url;
	$result = $db->sql_query("SELECT * FROM ".$user_prefix."_users WHERE firstname='".$cookie[1]."' AND lastname='".$cookie[2]."'");
	//$result2 = $db->sql_query($sql2);
	$num = $db->sql_numrows($result);
	if ($num != true) 
	{
		Header('Location: index.php?name=' . $module_name);
		die();
	}
	$userinfo = $db->sql_fetchrow($result);
	cookiedecode($user);
	getusrinfo($user);
	if (is_user($user)) 
    {	
	   include('header.php');
       Open_Table();
       echo '<center><font class="title"><b>'._WITHDRAW_LINDENS.'</b></font></center>';
       Close_Table();
       echo '<br />';
       Open_Table();
       nav();
       Close_Table();
       echo '<br />';		
       echo '<br />';
       Open_Table();
       echo '<center><font class="title"><b>'._WITHDRAW_NOTE.'</b></font></center>';
       echo '<br />';
       echo '<form action="index.php?name='.$module_name.'" method="post">
       <table cellpadding="0" cellspacing="10" border="0">
       <tr><td>'._LINDENS_AMOUNT.':</td><td><input type="text" name="merchant_amount" size="10" maxlength="20">&nbsp;'._LINDEN_DOLLARS.'<br /><font class="tiny">('._LINDEN_MESSAGE.')</font></td></tr>
       <tr><td colspan="2">
       <input type="hidden" name="merchant_key" value="'.$userinfo['avatar_key'].'">
       <input type="hidden" name="op" value="withdraw_submit">
       <input type="submit" value="Start Withdraw">	
	   </td></tr></table>
	   </form>';	
	Close_Table();
	include('footer.php');
	}
	else 
	{
		main($user);
		//echo "something wrong here";
	}	
}

function withdraw_submit($merchant_amount, $merchant_key)
{
    
	global $module_name, $owner_firstname, $owner_lastname, $user, $cookie, $prefix, $user_prefix, $db, $site_url;
    include('config.php');	
	$result1 =  $db->sql_query("SELECT * FROM ".$user_prefix."_users WHERE firstname='".$cookie[1]."' AND lastname='".$cookie[2]."' AND avatar_key='".$merchant_key."'");
	$result2 = $db->sql_query("SELECT * FROM ".$prefix."_marketplace_withdrawal WHERE firstname LIKE '%$owner_firstname%' AND lastname LIKE '%$owner_lastname%' AND avatar_key LIKE '%$owner_key%' ORDER BY id DESC limit 0,1");
	//$result2 = mysql_query("SELECT * FROM ".$prefix."_marketplace_withdrawal WHERE firstname='".$firstname."' AND lastname='".$lastname."' AND avatar_key='".$avatar_key."'");	
	if (is_user($user)) 
    {
	   include('header.php');
       Open_Table();
		$row = $db->sql_fetchrow($result1);
		//echo var_dump($row);
		$server = $db->sql_fetchrow($result2);	   
		if ($merchant_amount <= $row['currency'])
		{
            mysql_query("UPDATE ".$user_prefix."_users SET currency = currency - '".$merchant_amount."' WHERE firstname = '".$cookie[1]."' AND lastname = '".$cookie[2]."' AND avatar_key = '".$merchant_key."'");
			$funds = $merchant_key.",".$merchant_amount;
			$magicbox_key = $server['uuid_key'];
			//echo $magicbox_key;
			$transmit = mail($magicbox_key."@lsl.secondlife.com", $sitename, $funds);
			if(!$transmit)
			{ 
			   echo _WITHDRAW_ERROR2;
			   die();
			}			
			$balance = $row['currency'] -= $merchant_amount;
			echo $row['firstname'].'&nbsp;'.$row['lastname'].'&nbsp;'._WITHDRAW_MSG .'&nbsp;L$&nbsp;'.$merchant_amount .'&nbsp;you know have L$&nbsp;'. $balance;
		} 
		else 
		{
		   echo _WITHDRAW_ERROR1;
		}
	   Close_Table();
	   include('footer.php');
    }	
}
function dollar_submit($merchant_amount, $merchant_email, $merchant_title, $merchant_currency, $merchant_return, $merchant_cancel)
{
global $user, $cookie, $user_prefix, $db, $site_url, $paypal_url;
    if (is_user($user)) 
    {
       cookiedecode($user);
       $firstname = $cookie[1];
	   $lastname = $cookie[2];
       $uid = $cookie[0];
       $oid = $cookie[0]."-".time();
       $oid = str_pad($oid, 19, "0", STR_PAD_LEFT);
       //$oid = $merchant_title.$oid;
	   //echo $oid;
	
	include('header.php');
	Open_Table();
	echo '<center><font class="title"><b>'._DEPOSIT_USD.'</b></font></center>';
	Close_Table();
	echo '<br />';	
	Open_Table();
	nav();
	Close_Table();
	echo '<br />';
	Open_Table();
	echo '<center><font class="title"><b>' . _DEPOSIT.'&nbsp;'.$merchant_amount.'&nbsp;'.$merchant_currency . '</b></font><br />';
	echo '<font class="title"><b>' . _CREDIT_USD . '&nbsp;'.$merchant_amount.'&nbsp;'.$merchant_currency . '</b></font><br /><br />';
    echo '<form target="_blank" action="'.$paypal_url.'" method="post">
	<table cellpadding="0" cellspacing="10" border="0">
    <input type="hidden" name="cmd" value="_ext-enter">
    <input type="hidden" name="redirect_cmd" value="_xclick">
    <input type="hidden" name="business" value="'.$merchant_email.'">
    <input type="hidden" name="item_name" value="'.$merchant_title.'">
    <input type="hidden" name="amount" value="'.$merchant_amount.'">
    <input type="hidden" name="txn_type" value="web_accept">
    <input type="hidden" name="tax" value="0">
    <input type="hidden" name="no_shipping" value="1">
    <input type="hidden" name="no_note" value="1">
    <input type="hidden" name="custom" value="'.$uid.'">
    <input type="hidden" name="invoice" value="'.$oid.'">
    <input type="hidden" name="currency_code" value="'.$merchant_currency.'">
    <input type="hidden" name="return" value="'.$site_url.'/'.$merchant_return.'">
    <input type="hidden" name="cancel_return" value="'.$site_url.'/'.$merchant_cancel.'">
    <input type="hidden" name="notify_url" value="'.$site_url.'/pp_ipn.php">
    <input type="button" value="Cancel" onclick="history.go(-1);">
	<input type="submit" value="Start Deposit">
	</td></tr></table></form></center>';
	Close_Table();
	include("footer.php");
    } 
	else 
	{
	include('header.php');
	Open_Table();
	echo "somethign went wrong here!";
	Close_Table();
	include("footer.php");	   
    }	
}
if (!isset($hid)) { $hid = ""; }
if (!isset($url)) { $url = ""; }
if (!isset($bypass)) { $bypass = ""; }
if (!isset($op)) { $op = ""; }

switch($op) {

	case "change_karma":
	change_karma($user_id, $karma);
	break;

	case "logout":
	logout();
	break;

	case "broadcast":
	broadcast($the_message, $firstname, $lastname);
	break;

	case "lost_pass":
	lost_pass();
	break;

	case "new user":
	confirmNewUser($firstname, $lastname, $user_email, $user_password, $user_password2, $random_num, $gfx_check);
	break;

	case "finish":
	finishNewUser($firstname, $lastname, $user_email, $user_password, $random_num, $gfx_check);
	break;

	case "mailpasswd":
	mail_password($firstname, $lastname, $code);
	break;

	case "userinfo":
	userinfo($firstname, $lastname, $bypass, $hid, $url);
	break;

	case "login":
	login($firstname, $lastname, $user_password, $redirect, $mode, $f, $t, $random_num, $gfx_check);
	break;

	case "edituser":
	edituser();
	break;

	case "saveuser":
	saveuser($realname, $user_email, $femail, $user_website, $newsletter, $user_sig, $user_password, $vpass, $firstname, $lastname, $user_id);
	break;

	case "edithome":
	edithome();
	break;

	case "chgtheme":
	chgtheme();
	break;

	case "savehome":
	savehome($user_id, $firstname, $lastname, $storynum, $ublockon, $ublock, $broadcast);
	break;

	case "savetheme":
	savetheme($user_id, $theme);
	break;

	case "editcomm":
	editcomm();
	break;

	case "savecomm":
	savecomm($user_id, $firstname, $lastname, $umode, $uorder, $thold, $noscore, $commentmax);
	break;

	case "pass_lost":
	pass_lost();
	break;

	case "new_user":
	new_user();
	break;

    case "my_headlines":
	if (is_user($user)) 
	{
	    if (!empty($url)) 
		{
            my_headlines($hid, $url);
		} 
		else 
		{ 
		   die(); 
		}
	} 
	else 
	{
       Header('Location: index.php?name=' . $module_name);
    }
	break;

	case "gfx":
	gfx($random_num);
	break;

	case "activate":
	activate($firstname, $lastname, $check_num);
	break;

	case "CoolSize":
	CoolSize($size);
	break;
	
	// new functions added since conversion
	case "fundsmain":
	fundsmain();
	break;
	
	case "fundswithdraw_dollar":
	fundswithdraw_dollar();
	break;
	
	case "withdraw_submit":
	withdraw_submit($merchant_amount, $merchant_key);
	break;
	
	case "fundsdeposit_dollar":
	fundsdeposit_dollar();
	break;
	
	case "dollar_submit":
	dollar_submit($merchant_amount, $merchant_email, $merchant_title, $merchant_currency, $merchant_return, $merchant_cancel);
	break;

    case "fundswithdraw_currency":
	fundswithdraw_currency();
	break;
	
	default:
	main($user);
	break;

}

?>