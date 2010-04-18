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
$avatar_key = ( isset($_POST['avatar_key']) ) ? trim($_POST['avatar_key']) : trim($_GET['avatar_key']);
$uuid_key = ( isset($_POST['uuid_key']) ) ? trim($_POST['uuid_key']) : trim($_GET['uuid_key']);
$server_url = ( isset($_POST['server_url']) ) ? trim($_POST['server_url']) : trim($_GET['server_url']);

if($avatar_name && $avatar_key)
{
	$avatar_name = mysql_real_escape_string($avatar_name, $link_identifier);
	$avatar_name = substr(htmlspecialchars(str_replace("\'", "'", trim($avatar_name))), 0, 31);
	$avatar_name = rtrim($avatar_name, "\\");	
	$avatar_name = str_replace("'", "\'", $avatar_name);
	
	$chars = preg_split('/ /', $avatar_name, -1, PREG_SPLIT_OFFSET_CAPTURE);
	
	$avatar_key = filter($avatar_key, "nohtml");
	$uuid_key = filter($uuid_key, "nohtml");
	
	$server_url = filter($server_url, "nohtml");
	
	$firstname = mysql_real_escape_string($chars[0][0], $link_identifier);
	$lastname = mysql_real_escape_string($chars[1][0], $link_identifier);  
	$result = mysql_query("SELECT * FROM ".PREFIX."_marketplace_withdrawal WHERE firstname='".$firstname."' AND lastname='".$lastname."' AND avatar_key='".$avatar_key."'");
	if (mysql_num_rows($result) == true)
	{
		$row = mysql_fetch_array($result, MYSQL_ASSOC);
		if ($server_url != $row['server_url'])
		{
			mysql_query("UPDATE ".PREFIX."_marketplace_withdrawal SET server_url = '".$server_url."' WHERE firstname = '".$firstname."' AND lastname = '".$lastname."' AND avatar_key = '".$avatar_key."' LIMIT 1");
			echo _BANK_SERVER_UPDATED. ":" . $server_url;
		} 
		else 
		{		   
		   mysql_query("DELETE FROM ".PREFIX."_marketplace_withdrawal WHERE avatar_key = '".$avatar_key."' LIMIT 1");
		   mysql_query("INSERT INTO ".PREFIX."_marketplace_withdrawal (id, firstname, lastname, avatar_key, uuid_key, server_url) VALUES (NULL, '".$firstname."', '".$lastname."', '".$avatar_key."', '".$uuid_key."', '".$server_url."')");
		   echo _BANK_SERVER_RENEWED . ":" . $server_url;
		}
	} 
	else 
	{
		mysql_query("INSERT INTO ".PREFIX."_marketplace_withdrawal (id, firstname, lastname, avatar_key, uuid_key, server_url) VALUES (NULL, '".$firstname."', '".$lastname."', '".$avatar_key."', '".$uuid_key."', '".$server_url."')");
		echo _BANK_SERVER_URL . ":" . $server_url;
	}

}
?>