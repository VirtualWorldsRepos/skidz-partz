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
if($avatar_name && $avatar_key) 
{
	$chars = preg_split('/ /', $avatar_name, -1, PREG_SPLIT_OFFSET_CAPTURE);
	$avatar_key = filter($avatar_key, "nohtml", 1);
	$firstname = mysql_real_escape_string($chars[0][0], $link_identifier);
	$firstname = substr(htmlspecialchars(str_replace("\'", "'", trim($firstname))), 0, 31);
	$firstname = rtrim($firstname, "\\");	
	$firstname = str_replace("'", "\'", $firstname);	
	$lastname = mysql_real_escape_string($chars[1][0], $link_identifier);
	$lastname = substr(htmlspecialchars(str_replace("\'", "'", trim($lastname))), 0, 31);
	$lastname = rtrim($lastname, "\\");	
	$lastname = str_replace("'", "\'", $lastname);	
	$sql = "SELECT * FROM ".USER_PREFIX."_users WHERE firstname='".$firstname."' AND lastname='".$lastname."' AND avatar_key='".$avatar_key."'";
	$result = mysql_query($sql);
	if (mysql_num_rows($result) == 1)
	{
		$row = mysql_fetch_array($result, MYSQL_ASSOC);
		if ($avatar_key == $row['avatar_key'])
		{
            echo $row['firstname'].'&nbsp;'.$row['lastname'].'&nbsp;'._BALANCEMSG .'&nbsp;L$&nbsp;'. $row['currency'];
		} 
		else 
		{
		   echo _BALANCEERROR1;
		}
	} 
	else 
	{
		echo _BALANCEERROR2;
	}

}
?>