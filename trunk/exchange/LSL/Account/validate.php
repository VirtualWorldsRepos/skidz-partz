<?php
header('Content-Type: text/html; charset=UTF-8');
header('Content-language: en-us');
header('Date: '.date('D, d M Y H:i:s', time()).' GMT');
header('Last-Modified: '.date('D, d M Y H:i:s', time()).' GMT');
header('Expires: '.date('D, d M Y H:i:s \G\M\T', time() + 10800));
?>
<?php

require_once(dirname(__FILE__) . '/../config.php');
require_once(dirname(__FILE__) . '/../includes/functions.php');
require_once(dirname(__FILE__) . '/../includes/defines.php');

$link_identifier = mysql_connect(DBHOST, DBUSER, DBPASS);
if (!$link_identifier) 
{
    die('Could not connect: ' . mysql_error());
}

$db_selected = mysql_select_db(DATABASE, $link_identifier);
if (!$db_selected) 
{
    die ('Can\'t use : ' . DATABASE . 'database' . mysql_error());
}

$avatar_name = ( isset($_POST['avatar_name']) ) ? trim($_POST['avatar_name']) : trim($_GET['avatar_name']);
$check_num = ( isset($_POST['check_num']) ) ? trim($_POST['check_num']) : trim($_GET['check_num']);
$avatar_key = ( isset($_POST['avatar_key']) ) ? trim($_POST['avatar_key']) : trim($_GET['avatar_key']);
if($avatar_name && $check_num && $avatar_key) 
{
	$chars = preg_split('/ /', $avatar_name, -1, PREG_SPLIT_OFFSET_CAPTURE);
	$language = "english";
	$past = time()-86400;
	$avatar_key = filter($avatar_key, "nohtml", 1);
	$check_num = filter($check_num, "nohtml", 1);
	
	$firstname = mysql_real_escape_string($chars[0][0], $link_identifier);
	$firstname = substr(htmlspecialchars(str_replace("\'", "'", trim($firstname))), 0, 31);
	$firstname = rtrim($firstname, "\\");	
	$firstname = str_replace("'", "\'", $firstname);
	
	$lastname = mysql_real_escape_string($chars[1][0], $link_identifier);
	$lastname = substr(htmlspecialchars(str_replace("\'", "'", trim($lastname))), 0, 31);
	$lastname = rtrim($lastname, "\\");	
	$lastname = str_replace("'", "\'", $lastname);
	
	mysql_query("DELETE FROM ".USER_PREFIX."_users_temp WHERE time < ".$past."");
	$sql = "SELECT * FROM ".USER_PREFIX."_users_temp WHERE firstname='".$firstname."' AND lastname='".$lastname."' AND check_num='".$check_num."' AND avatar_key='".$avatar_key."'";
	$result = mysql_query($sql);
	if (mysql_num_rows($result) == true)
	{
		$row = mysql_fetch_array($result, MYSQL_ASSOC);
		$user_password = htmlspecialchars(stripslashes($row['user_password']));
		if ($check_num == $row['check_num']) 
		{
			mysql_query("INSERT INTO ".USER_PREFIX."_users (user_id, firstname, lastname, avatar_key, user_email, user_password, user_regdate) VALUES (NULL, '".$row['firstname']."', '".$row['lastname']."', '".$row['avatar_key']."', '".$row['user_email']."', '".$user_password."', '".$row['user_regdate']."')");
			mysql_query("DELETE FROM ".USER_PREFIX."_users_temp WHERE firstname='".$firstname."' AND lastname='".$lastname."' AND check_num='".$check_num."' AND avatar_key='".$avatar_key."'");
			echo $row['firstname'].'&nbsp;'.$row['lastname'].':&nbsp;'._VALIDATEMSG;
		} 
		else 
		{
		   echo _VALIDATE_ERROR1;
		}
	} 
	else 
	{
		echo _VALIDATE_ERROR2;
	}

}
?>