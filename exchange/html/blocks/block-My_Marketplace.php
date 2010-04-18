<?php

if ( !defined('BLOCK_FILE') ) {
    Header("Location: ../index.php");
}

define('SLL_BALANCE', 'SLL Balance');
define('USD_BALANCE', 'USD Balance');
define('SL_DEPOSIT', 'Deposit');
define('SL_FAVORITE', 'My Favorite Items');
define('SL_UNRATED', 'Unrated Items');
define('SL_MYITEMS', 'My Items');
define('SL_MYINVENTORY', 'My Inventory');
define('SL_MYSALES', 'Today\'s Sales');
define('SL_MYTRAFFIC', 'Today\'s Traffic');
define('LINDEN_CURRENCY', 'L$');
define('PAYPAL_CURRENCY', '$');

global $db, $prefix, $user_prefix, $userinfo, $count_inventory, $admin, $user, $cookie, $sitekey, $gfx_chk, $admin_file;
mt_srand ((double)microtime()*1000000);
$maxran = 1000000;
$random_num = mt_rand(0, $maxran);
$result2 = $db->sql_query("SELECT * FROM ".$user_prefix."_users WHERE firstname='".$cookie[1]."' AND lastname='".$cookie[2]."'");
$userinfo = $db->sql_fetchrow($result2);
cookiedecode($user);
getusrinfo($user);
$result = $db->sql_query("SELECT * FROM ".$prefix."_marketplace_magicbox_products WHERE firstname='".$cookie[1]."' AND lastname='".$cookie[2]."'");
$count_inventory = $db->sql_numrows($result);
$result3 = $db->sql_query("SELECT * FROM ".$prefix."_marketplace_items WHERE firstname='".$cookie[1]."' AND lastname='".$cookie[2]."'");
$count_products = $db->sql_numrows($result3);
if (!is_user($user))
{
$content = "<form onsubmit=\"this.submit.disabled='true'\" action=\"index.php?name=Your_Account\" method=\"post\">";
$content .= "<center><font class=\"content\">"._NICKNAME."<br>";
$content .= "<input type=\"text\" name=\"firstname\" size=\"10\" maxlength=\"25\"><br>";
$content .= "<center><font class=\"content\">"._LASTNAME."<br>";
$content .= "<input type=\"text\" name=\"lastname\" size=\"10\" maxlength=\"25\"><br>";
$content .= ""._PASSWORD."<br>";
$content .= "<input type=\"password\" name=\"user_password\" size=\"10\" maxlength=\"20\"><br>";
if (extension_loaded('gd') AND ($gfx_chk == 2 OR $gfx_chk == 4 OR $gfx_chk == 5 OR $gfx_chk == 7)) {
    $content .= ""._SECURITYCODE.": <img src='?gfx=gfx&random_num=$random_num' border='1' alt='"._SECURITYCODE."' title='"._SECURITYCODE."'><br>\n";
    $content .= ""._TYPESECCODE."<br><input type=\"text\" NAME=\"gfx_check\" SIZE=\"7\" MAXLENGTH=\"6\">\n";
    $content .= "<input type=\"hidden\" name=\"random_num\" value=\"$random_num\"><br>\n";
} else {
    $content .= "<input type=\"hidden\" name=\"random_num\" value=\"$random_num\">";
    $content .= "<input type=\"hidden\" name=\"gfx_check\" value=\"$code\">";
}
$content .= "<input type=\"hidden\" name=\"op\" value=\"login\">";
$content .= "<input type=\"submit\" value=\""._LOGIN."\"></font></center></form>";
$content .= "<center><font class=\"content\">"._ASREGISTERED."</font></center>";

}
else
{
$content .= '<table border="0" width="100%" cellspacing="0" cellpadding="3">';
$content .= '<tr><td valign="left"><b><a href="index.php?name=Your_Account&op=fundsmain">'.SLL_BALANCE.'</a></b></td><td align="right">'.LINDEN_CURRENCY.''.$userinfo['currency'].'</td></tr>';
$content .= '<tr><td valign="left"><b><a href="index.php?name=Your_Account&op=fundsmain">'.USD_BALANCE.'</a></b></td><td align="right">'.PAYPAL_CURRENCY.''.$userinfo['dollar'].'</td></tr>';
$content .= '<tr><td valign="left"></td><td align="right">'.SL_DEPOSIT.'</td></tr>';
//$content .= '<tr><td valign="left"><b><a href="#">'.SL_FAVORITE.'</a></b></td><td align="right">'.$temp_value.'</td></tr>';
//$content .= '<tr><td valign="left"><b><a href="#">'.SL_UNRATED.'</a></b></td><td align="right">'.$temp_value.'</td></tr>';
$content .= '<tr><td valign="left"><b><a href="index.php?name=Marketplace&file=Edit">'.SL_MYITEMS.'</a></b></td><td align="right">'.$count_products.'</td></tr>';
$content .= '<tr><td valign="left"><b><a href="index.php?name=Marketplace&file=Inventory">'.SL_MYINVENTORY.'</a></b></td><td align="right">'.$count_inventory.'</td></tr>';
//$content .= '<tr><td valign="left"><b><a href="#">'.SL_MYSALES.'</a></b></td><td align="right">'.$temp_value.'</td></tr>';
//$content .= '<tr><td valign="left"><b><a href="#">'.SL_MYTRAFFIC.'</a></b></td><td align="right">'.$temp_value.'</td></tr>';
$content .= '</table>';
}
?>
