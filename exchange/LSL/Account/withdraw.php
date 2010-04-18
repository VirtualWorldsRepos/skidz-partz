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
$avatar_key = ( isset($_POST['avatar_key']) ) ? trim($_POST['avatar_key']) : trim($_GET['avatar_key']);
$currency = ( isset($_POST['currency']) ) ? trim($_POST['currency']) : trim($_GET['currency']);
$owner_name = ( isset($_POST['owner_name']) ) ? trim($_POST['owner_name']) : trim($_GET['owner_name']);
$owner_key = ( isset($_POST['owner_key']) ) ? trim($_POST['owner_key']) : trim($_GET['owner_key']);
//Respect Destiny
if($avatar_name && $avatar_key) 
{
	$avatar_chars = preg_split('/ /', $avatar_name, -1, PREG_SPLIT_OFFSET_CAPTURE);
	$owner_chars = preg_split('/ /', $owner_name, -1, PREG_SPLIT_OFFSET_CAPTURE);
	$avatar_key = filter($avatar_key, "nohtml", 1);
	$currency = intval($currency);
	$avatar_firstname = mysql_real_escape_string($avatar_chars[0][0], $link_identifier);
	$avatar_lastname = mysql_real_escape_string($avatar_chars[1][0], $link_identifier);
	$owner_firstname = mysql_real_escape_string($owner_chars[0][0], $link_identifier);
	$owner_lastname = mysql_real_escape_string($owner_chars[1][0], $link_identifier);	
	$owner_key = filter($owner_key, "nohtml", 1);
	$result = mysql_query("SELECT * FROM ".USER_PREFIX."_users WHERE firstname='$avatar_firstname' AND lastname='$avatar_lastname' AND avatar_key='$avatar_key'");
	$result2 = mysql_query("SELECT * FROM ".PREFIX."_marketplace_withdrawal WHERE firstname LIKE '%$owner_firstname%' AND lastname LIKE '%$owner_lastname%' AND avatar_key LIKE '%$owner_key%' ORDER BY id DESC limit 0,1");
	if (mysql_num_rows($result) == 1)
	{
		$row = mysql_fetch_array($result, MYSQL_ASSOC);
		$server = mysql_fetch_array($result2, MYSQL_ASSOC);
		if ($avatar_key == $row['avatar_key'] && $currency <= $row['currency'])
		{
            mysql_query("UPDATE ".USER_PREFIX."_users SET currency = currency - '".$currency."' WHERE firstname = '".$firstname."' AND lastname = '".$lastname."' AND avatar_key = '".$avatar_key."'");
			$funds = $avatar_key.",".$currency;
			$magicbox_key = $server['uuid_key'];
			//echo $magicbox_key;
			$transmit = CallLSLScript($server['server_url'], $funds);
			if(!$transmit)
			{ 
			   mail($magicbox_key."@lsl.secondlife.com", $sitename, $funds);
			}			
			$balance = $row['currency'] -= $currency;
			echo $row['firstname'].'&nbsp;'.$row['lastname'].'&nbsp;'._WITHDRAW_MSG .'&nbsp;L$&nbsp;'.$currency .'&nbsp;you know have L$&nbsp;'. $balance;
		} 
		else 
		{
		   echo _WITHDRAW_ERROR1;
		}
	} 
	else 
	{
		echo _WITHDRAW_ERROR2;
	}

}
?>