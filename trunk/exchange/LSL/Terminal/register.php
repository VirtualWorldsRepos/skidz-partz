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

$uuid = ( isset($_POST['uuid']) ) ? trim($_POST['uuid']) : trim($_GET['uuid']);
$location = ( isset($_POST['location']) ) ? trim($_POST['location']) : trim($_GET['location']);
$slurl = ( isset($_POST['slurl']) ) ? trim($_POST['slurl']) : trim($_GET['slurl']);
if($uuid && $location && $slurl) 
{
	$uuid = filter($uuid, "nohtml", 1);
	$slurl = filter($slurl, "nohtml", 1);
	$location = filter($location, "nohtml", 1);
	$result = mysql_query("SELECT * FROM ".PREFIX."_terminals WHERE uuid='".$uuid."'");
	if (mysql_num_rows($result) == false)
	{
		$row = mysql_fetch_array($result, MYSQL_ASSOC);
		if ($uuid != $row['uuid'])
		{
			mysql_query("INSERT INTO ".PREFIX."_terminals (id, uuid, location, slurl) VALUES (NULL, '".$uuid."', '".$location."', '".$slurl."')");
			echo _REGISTERMSG . '&nbsp;' . $location .'&nbsp;SLURL&nbsp;' . $slurl;
		} 
		else 
		{
		   echo _REGISTERERROR1;
		}
	} 
	else 
	{
		echo _REGISTERERROR2;
	}

}
?>