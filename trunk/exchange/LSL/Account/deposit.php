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
$currency = ( isset($_POST['currency']) ) ? trim($_POST['currency']) : trim($_GET['currency']);
$avatar_key = ( isset($_POST['avatar_key']) ) ? trim($_POST['avatar_key']) : trim($_GET['avatar_key']);
$owner_name = ( isset($_POST['owner_name']) ) ? trim($_POST['owner_name']) : trim($_GET['owner_name']);
$owner_key = ( isset($_POST['owner_key']) ) ? trim($_POST['owner_key']) : trim($_GET['owner_key']);


if($avatar_name && $avatar_key) 
{
	$chars = preg_split('/ /', $avatar_name, -1, PREG_SPLIT_OFFSET_CAPTURE);
	$owner_chars = preg_split('/ /', $owner_name, -1, PREG_SPLIT_OFFSET_CAPTURE);
	$avatar_key = filter($avatar_key, "nohtml", 1);
	$currency = intval($currency);	
	$firstname = mysql_real_escape_string($chars[0][0], $link_identifier);
	$firstname = substr(htmlspecialchars(str_replace("\'", "'", trim($firstname))), 0, 31);
	$firstname = rtrim($firstname, "\\");	
	$firstname = str_replace("'", "\'", $firstname);	
	$lastname = mysql_real_escape_string($chars[1][0], $link_identifier);
	$lastname = substr(htmlspecialchars(str_replace("\'", "'", trim($lastname))), 0, 31);
	$lastname = rtrim($lastname, "\\");	
	$lastname = str_replace("'", "\'", $lastname);
	$owner_firstname = mysql_real_escape_string($owner_chars[0][0], $link_identifier);
	$owner_firstname = substr(htmlspecialchars(str_replace("\'", "'", trim($owner_firstname))), 0, 31);
	$owner_firstname = rtrim($owner_firstname, "\\");	
	$owner_firstname = str_replace("'", "\'", $owner_firstname);
	$owner_lastname = mysql_real_escape_string($owner_chars[1][0], $link_identifier);	
	$owner_lastname = substr(htmlspecialchars(str_replace("\'", "'", trim($owner_lastname))), 0, 31);
	$owner_lastname = rtrim($owner_lastname, "\\");	
	$owner_lastname = str_replace("'", "\'", $owner_lastname);
	$owner_key = filter($owner_key, "nohtml", 1);	
	$sql = "SELECT * FROM ".USER_PREFIX."_users WHERE firstname='".$firstname."' AND lastname='".$lastname."' AND avatar_key='".$avatar_key."'";
	$result = mysql_query($sql);
	$result2 = mysql_query("SELECT * FROM ".PREFIX."_marketplace_withdrawal WHERE firstname LIKE '%$owner_firstname%' AND lastname LIKE '%$owner_lastname%' AND avatar_key LIKE '%$owner_key%' ORDER BY id DESC limit 0,1");
	$server = mysql_fetch_array($result2, MYSQL_ASSOC);
	if (mysql_num_rows($result) == 1)
	{
		$row = mysql_fetch_array($result, MYSQL_ASSOC);
		if ($avatar_key == $row['avatar_key'])
		{
            mysql_query("UPDATE ".USER_PREFIX."_users SET currency = currency + '".$currency."' WHERE firstname = '".$firstname."' AND lastname = '".$lastname."' AND avatar_key = '".$avatar_key."'");
			$balance = $row['currency'] += $currency;
			echo $row['firstname'].'&nbsp;'.$row['lastname'].' '._DEPOSIT_MSG .'&nbsp;L$'.$currency .'&nbsp;you know have '. $balance;
		} 
		else 
		{
		   echo _DEPOSIT_ERROR1;
		}
	} 
	else 
	{
		$funds = $avatar_key.",".$currency;
		$magicbox_key = $server['uuid_key'];
		//echo var_dump($server);
		$transmit = CallLSLScript($server['server_url'], $funds);
		if(!$transmit)
		{ 
			mail($magicbox_key."@lsl.secondlife.com", SITENAME, $funds);
		}
		echo _DEPOSIT_ERROR2;
	}

}
?>