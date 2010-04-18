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
$server_name = ( isset($_POST['server_name']) ) ? trim($_POST['server_name']) : trim($_GET['server_name']);
$server_desc = ( isset($_POST['server_desc']) ) ? trim($_POST['server_desc']) : trim($_GET['server_desc']);
$server_status = (isset($_POST['server_status'])) ? true : false;
			
if($avatar_name && $server_key)
{
	$avatar_name = mysql_real_escape_string($avatar_name, $link_identifier);
	$avatar_name = substr(htmlspecialchars(str_replace("\'", "'", trim($avatar_name))), 0, 31);
	$avatar_name = rtrim($avatar_name, "\\");	
	$avatar_name = str_replace("'", "\'", $avatar_name);
	
	$chars = preg_split('/ /', $avatar_name, -1, PREG_SPLIT_OFFSET_CAPTURE);
	
	$server_key = filter($server_key, "nohtml");
	
	$server_name = mysql_real_escape_string($server_name, $link_identifier);
	$server_name = substr(htmlspecialchars(str_replace("\'", "'", trim($server_name))), 0, 63);
	$server_name = rtrim($server_name, "\\");	
	$server_name = str_replace("'", "\'", $server_name);
	
	$server_desc = mysql_real_escape_string($server_desc, $link_identifier);
	$server_desc = substr(htmlspecialchars(str_replace("\'", "'", trim($server_desc))), 0, 63);
	$server_desc = rtrim($server_desc, "\\");	
	$server_desc = str_replace("'", "\'", $server_desc);
	
	$firstname = mysql_real_escape_string($chars[0][0], $link_identifier);
	$lastname = mysql_real_escape_string($chars[1][0], $link_identifier);  
	
	$result = mysql_query("SELECT * FROM ".PREFIX."_marketplace_magicbox WHERE firstname='".$firstname."' AND lastname='".$lastname."' AND server_key='".$server_key."'");
	if (mysql_num_rows($result) == true)
	{
		$row = mysql_fetch_array($result, MYSQL_ASSOC);
		if ($server_name != $row['server_name'])
		{
			mysql_query("UPDATE ".PREFIX."_marketplace_magicbox SET server_name = '".$server_name."', server_status = '".$server_status."' WHERE firstname = '".$firstname."' AND lastname = '".$lastname."' AND server_key = '".$server_key."' LIMIT 1");
			echo _SERVER_NAME;
		}
		if ($server_desc != $row['server_desc'])
		{
			mysql_query("UPDATE ".PREFIX."_marketplace_magicbox SET server_desc = '".$server_desc."', server_status = '".$server_status."' WHERE firstname = '".$firstname."' AND lastname = '".$lastname."' AND server_key = '".$server_key."' LIMIT 1");
			echo _SERVER_DESC;
		}
		if ($server_status != $row['server_status'])
		{
			mysql_query("UPDATE ".PREFIX."_marketplace_magicbox SET server_status = '".$server_status."' WHERE firstname = '".$firstname."' AND lastname = '".$lastname."' AND server_key = '".$server_key."' LIMIT 1");
			echo _SERVER_STATUS;
		}
		else 
		{		   
		   echo _SERVER_EXISTS;
		}
	} 
	else 
	{
		mysql_query("INSERT INTO ".PREFIX."_marketplace_magicbox (id, firstname, lastname, server_key, server_name, server_desc, server_status) VALUES (NULL, '".$firstname."', '".$lastname."', '".$server_key."', '".$server_name."', '".$server_desc."', '1')");
		echo _SERVER_CREATED;
	}

}
?>