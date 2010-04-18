<?php
header('Content-Type: text/html; charset=UTF-8');
header('Content-language: en-us');
header('Date: '.date('D, d M Y H:i:s', time()).' GMT');
header('Last-Modified: '.date('D, d M Y H:i:s', time()).' GMT');
header('Expires: '.date('D, d M Y H:i:s \G\M\T', time() + 10800));
?>
<?php

require_once(dirname(__FILE__) . '/../config.php');
require_once(dirname(__FILE__) . '/../includes/defines.php');
require_once(dirname(__FILE__) . '/../includes/functions.php');

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
$server_key = ( isset($_POST['server_key']) ) ? trim($_POST['server_key']) : trim($_GET['server_key']);
$server_url = ( isset($_POST['server_url']) ) ? trim($_POST['server_url']) : trim($_GET['server_url']);
$server_location = ( isset($_POST['server_location']) ) ? trim($_POST['server_location']) : trim($_GET['server_location']);

			
if($avatar_name && $server_key)
{
	$avatar_name = mysql_real_escape_string($avatar_name, $link_identifier);
	$avatar_name = substr(htmlspecialchars(str_replace("\'", "'", trim($avatar_name))), 0, 31);
	$avatar_name = rtrim($avatar_name, "\\");	
	$avatar_name = str_replace("'", "\'", $avatar_name);
	
	$chars = preg_split('/ /', $avatar_name, -1, PREG_SPLIT_OFFSET_CAPTURE);
	
	$server_key = filter($server_key, "nohtml");
	
	$firstname = mysql_real_escape_string($chars[0][0], $link_identifier);
	$lastname = mysql_real_escape_string($chars[1][0], $link_identifier);  
	
	$result = mysql_query("SELECT * FROM ".PREFIX."_marketplace_magicbox WHERE firstname='".$firstname."' AND lastname='".$lastname."' AND server_key='".$server_key."'");
	if (mysql_num_rows($result) == true)
	{
		$row = mysql_fetch_array($result, MYSQL_ASSOC);
		if ($server_url != $row['server_url'])
		{
			mysql_query("UPDATE ".PREFIX."_marketplace_magicbox SET server_url = '".$server_url."', server_location = '".$server_location."' WHERE firstname = '".$firstname."' AND lastname = '".$lastname."' AND server_key = '".$server_key."' LIMIT 1");
			echo _SERVER_RENEWED;
		}
		else 
		{		   
		   echo _SERVER_EXISTS;
		}
	} 
	else 
	{
		echo _SERVER_ERROR_REGISTERED;
	}
}
?>