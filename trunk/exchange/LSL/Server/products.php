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
$server_key = ( isset($_POST['server_key']) ) ? trim($_POST['server_key']) : trim($_GET['server_key']);
$product_name = ( isset($_POST['product_name']) ) ? trim($_POST['product_name']) : trim($_GET['product_name']);
$product_type = ( isset($_POST['product_type']) ) ? trim($_POST['product_type']) : trim($_GET['product_type']);

if($avatar_name && $avatar_key);
{
	$past = time()-86400;
	$time_now = time();
	
	$avatar_name = mysql_real_escape_string($avatar_name, $link_identifier);
	$avatar_name = substr(htmlspecialchars(str_replace("\'", "'", trim($avatar_name))), 0, 31);
	$avatar_name = rtrim($avatar_name, "\\");	
	$avatar_name = str_replace("'", "\'", $avatar_name);
	
	$chars = preg_split('/ /', $avatar_name, -1, PREG_SPLIT_OFFSET_CAPTURE);
	
	$avatar_key = filter($avatar_key, "nohtml");
	
	$server_key = filter($server_key, "nohtml");
	
	$product_name = mysql_real_escape_string($product_name, $link_identifier);
	$product_name = substr(htmlspecialchars(str_replace("\'", "'", trim($product_name))), 0, 63);
	$product_name = rtrim($product_name, "\\");	
	$product_name = str_replace("'", "\'", $product_name);
	
    $product_type = intval($product_type);
	
	$firstname = mysql_real_escape_string($chars[0][0], $link_identifier);
	$lastname = mysql_real_escape_string($chars[1][0], $link_identifier);
	
	$result = mysql_query("SELECT * FROM ".PREFIX."_marketplace_magicbox_products WHERE firstname='".$firstname."' AND lastname='".$lastname."' AND avatar_key='".$avatar_key."' AND server_key='".$server_key."' AND product_name='".$product_name."' AND product_type='".$product_type."'");
	if (mysql_num_rows($result) == true)
	{
		$row = mysql_fetch_array($result, MYSQL_ASSOC);
		if ($row['product_date'] < $past && $product_name == $row['product_name'])
		{
			mysql_query("UPDATE ".PREFIX."_marketplace_magicbox_products SET product_date = '".$time_now."' WHERE product_name = '".$product_name."' AND product_type = '".$product_type."' AND server_key = '".$server_key."' LIMIT 1");
			echo _PRODUCTS_DATE;
		} 
		else 
		{		   
		   mysql_query("UPDATE ".PREFIX."_marketplace_magicbox_products SET product_date = '".$time_now."' WHERE firstname = '".$firstname."' AND lastname = '".$lastname."' AND avatar_key = '".$avatar_key."' AND server_key = '".$server_key."' LIMIT 1");
		   echo $product_name . '&nbsp;'._PRODUCTS_EXISTS;
		}
	} 
	else 
	{
		mysql_query("INSERT INTO ".PREFIX."_marketplace_magicbox_products (id, firstname, lastname, avatar_key, server_key, product_name, product_type, product_date) VALUES (NULL, '".$firstname."', '".$lastname."', '".$avatar_key."', '".$server_key."', '".$product_name."', '".$product_type."', '".$time_now."')");
		echo _PRODUCTS_NEW."&nbsp;".$product_name;
	}

}
?>