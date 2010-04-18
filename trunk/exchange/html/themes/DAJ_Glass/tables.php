<?php

/************************************************************/
/* OpenTable Functions                                      */
/*                                                          */
/* Define the tables look&feel for you whole site. For this */
/* we have two options: OpenTable and OpenTable2 functions. */
/* Then we have CloseTable and CloseTable2 function to      */
/* properly close our tables. The difference is that        */
/* OpenTable has a 100% width and OpenTable2 has a width    */
/* according with the table content                         */
/************************************************************/

function Open_Table() {
	echo '<table width="100%" border="0" cellspacing="0" cellpadding="7">
  <tr>
    <td>
<table width="100%"  border="0" cellpadding="1" cellspacing="0" bgcolor="#192832">
  <tr>
    <td><table width="100%"  border="0" cellpadding="2" cellspacing="0" bgcolor="#F5F5F5">
      <tr>
        <td><table width="100%"  border="0" cellspacing="0" cellpadding="2">
          <tr>
            <td>';
}

function Open_Table2() {
    global $name, $bgcolor1, $bgcolor2;
	echo "<table width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"0\" ><tr><td class=extra>\n";
    echo "<table width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"8\" ><tr><td>\n";
}
function Close_Table() 
{
	echo '</td>
          </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>
</table>
</td>
  </tr>
</table>';
}
function Close_Table2() 
{
	echo '</td></tr></table></td></tr></table>';
}

?>