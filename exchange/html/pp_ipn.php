<?php

require_once("config.php");
require_once("db/db.php");
$livefeed = 0; // Set to 0 to run test payments thru PayPal's sandbox
//$config = $db->sql_fetchrow($db->sql_query("SELECT * FROM ".$prefix."_config"));
$row = $db->sql_fetchrow($db->sql_query("SELECT * FROM ".$prefix."_config"));
$paypal_email = $row['paypal_email'], "nohtml");
###################
# Start PayPal IPN
###################
// read the post from PayPal system and add 'cmd'
$req = 'cmd=_notify-validate';
foreach ($_POST as $key => $value) 
{
  $value = urlencode(stripslashes($value));
  $req .= "&$key=$value";
}
if($livefeed > 0) 
{
  // post back to PayPal system to validate
  $header  = "POST /cgi-bin/webscr HTTP/1.0\r\n";
  $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
  $header .= 'Content-Length: ' . strlen($req) . "\r\n\r\n";
  $fp = fsockopen ('www.paypal.com', 80, $errno, $errstr, 30);
} 
else 
{
  // post back to PayPal Sandbox system to validate, used for testing IPN
  $header  = "POST /cgi-bin/webscr HTTP/1.0\r\n";
  $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
  $header .= 'Content-Length: ' . strlen($req) . "\r\n\r\n";
  $fp = fsockopen ('www.sandbox.paypal.com', 80, $errno, $errstr, 30);
}

//YOU RECIEVE EMAILS WHEN THIS SCRIPT IS ACCESSED
$req2 = eregi_replace("&","\r\n",$req);
$req2 = urldecode($req2);
mail("dazzlecms@hotmail.com", "Testing", $req2, "From: ".$row['adminmail']."\r\n");

// assign posted variables to local variables
// note: additional IPN variables also available -- see IPN documentation
$receiver_email = $_POST['receiver_email'];
$business_email = $_POST['business'];
$custom = $_POST['custom'];
$option_name1 = $_POST['option_name1'];
$option_selection1 = $_POST['option_selection1'];
$reason_code = $_POST['reason_code'];
$payment_status = $_POST['payment_status'];
$txn_type = $_POST['txn_type'];
$payer_status = $_POST['payer_status'];
$payment_type = $_POST['payment_type'];
$amount = $_POST['mc_gross'];

mail("dazzlecms@hotmail.com", "Email Check", $row['paypal_email'] . " = " . $receiver_email, "From: ".$row['adminmail']."\r\n");

########################
# Start IPN Validator
########################
if (!$fp) {
  echo "Problem: Error Number: $errno Error String: $errstr";
  exit;
} else {
  fputs($fp, $header . $req);
  while(!feof($fp)) {
    $res = fgets($fp, 1024);
    $res = trim($res);
    if (strcasecmp($res, "VERIFIED") == 0) 
	{
      if (!empty($receiver_email))
	  {
        if(!empty($custom)) 
		{
            if ($payment_status == "Completed")
			{
              $db->sql_query("UPDATE ".$prefix."_users SET dollar = dollar + '".$amount."' WHERE user_id='".$custom."'");
            }
			elseif ($payment_status == "Reversed")
			{
              $db->sql_query("UPDATE ".$prefix."_users SET dollar = dollar - '".$amount."' WHERE user_id='".$custom."'");
            }
        }
      }
    } 
	else 
	{//if (strcmp($res, "INVALID") == 0)
      // log for manual investigation
      mail($row['adminmail'], "IPN Failure", $req2, "From: ".$row['adminmail']."\r\n");
    }
  }
  fclose($fp);
}


?>