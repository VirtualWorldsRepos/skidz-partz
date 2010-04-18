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
$email = ( isset($_POST['email']) ) ? trim($_POST['email']) : trim($_GET['email']);
$avatar_key = ( isset($_POST['avatar_key']) ) ? trim($_POST['avatar_key']) : trim($_GET['avatar_key']);

$row = mysql_fetch_array(mysql_query("SELECT * FROM ".USER_PREFIX."_users WHERE firstname='".$firstname."' AND lastname='".$lastname."' AND avatar_key='".$avatar_key."'"), MYSQL_ASSOC);
if($avatar_name && $email && $avatar_key != $row['avatar_key']) 
{
    $chars = preg_split('/ /', $avatar_name, -1, PREG_SPLIT_OFFSET_CAPTURE);
	$user_regdate = date("M d, Y");
	$datekey = date("F j");	
    $user_password = htmlspecialchars(stripslashes(makePass()));
	mt_srand ((double)microtime()*1000000);
	$maxran = 1000000;
	$check_num = mt_rand(0, $maxran);
	$check_num = md5($check_num);
	$time = time();
	$finishlink = $check_num;
	$new_password = md5($user_password);
	$new_password = htmlspecialchars(stripslashes($new_password));
	
	$firstname = mysql_real_escape_string($chars[0][0], $link_identifier);
	$firstname = substr(htmlspecialchars(str_replace("\'", "'", trim($firstname))), 0, 31);
	$firstname = rtrim($firstname, "\\");	
	$firstname = str_replace("'", "\'", $firstname);

	$lastname = mysql_real_escape_string($chars[1][0], $link_identifier);
	$lastname = substr(htmlspecialchars(str_replace("\'", "'", trim($lastname))), 0, 31);
	$lastname = rtrim($lastname, "\\");	
	$lastname = str_replace("'", "\'", $lastname);
		
	//$email = base64_decode($email);
	$email = filter($email, "nohtml", 1);
	//$avatar_key = base64_decode($avatar_key);
	$avatar_key = filter($avatar_key, "nohtml", 1);
	$result = mysql_query("INSERT INTO ".PREFIX."_users_temp (user_id, firstname, lastname, avatar_key, user_email, user_password, user_regdate, check_num, time) VALUES (NULL, '".$firstname."', '".$lastname."', '".$avatar_key."', '".$email."', '".$new_password."', '".$user_regdate."', '".$check_num."', '".$time."')");
	
	if($result) 
	{
		$message = _WELCOMETO.'&nbsp;'.SITENAME.'!<br /><br />'._YOUUSEDEMAIL.'&nbsp;('.$email.')&nbsp;'._TOREGISTER.'&nbsp;'.SITENAME.'<br /><br />'._TOFINISHUSER.'<br /><br />'._ACTIVATION.'&nbsp;'.$finishlink.'<br /><br />'._FOLLOWINGMEM.'<br /><br />'._FIRSTNAME.'&nbsp;'.$firstname.'<br />'._LASTNAME.'&nbsp;'.$lastname.'<br />'._UPASSWORD.'&nbsp;'.$user_password;
		$subject = _ACTIVATIONSUB;
		$from = ADMIN_EMAIL;
		// Content Types
        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset='._CHARSET.'' . "\r\n";
        // Additional headers
        $headers .= 'To: '.$firstname.' '.$lastname.' <'.$email.'>' . "\r\n";
        $headers .= 'From: '.SITENAME.' <'.ADMIN_EMAIL.'>' . "\r\n";
		mail($email, $subject, $message, $headers);
		echo _CHECKEMAIL;
	}
}
?>