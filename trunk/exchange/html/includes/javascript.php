<script type="text/javascript" src="includes/lightbox/js/prototype.js"></script>
<script type="text/javascript" src="includes/lightbox/js/scriptaculous.js?load=effects,builder"></script>
<script type="text/javascript" src="includes/lightbox/js/lightbox.js"></script>
<?php

######################################################################
# Skidz Partz - Exchange
# ============================================
#
# Copyright (c) 2010 by Dazzle Development Team
# http://www.dazzlecms.com
#
# This program is free software. You can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License.
######################################################################
		
if (stristr(htmlentities($_SERVER['PHP_SELF']), "javascript.php")) {
    Header("Location: ../index.php");
    die();
}

##################################################
# Include for some common javascripts functions  #
##################################################

global $module, $name, $admin, $advanced_editor, $lang, $wysiwyg_editor;

if (file_exists("themes/".$ThemeSel."/style/editor.css")) {
    $edtcss = "editor_css : \"themes/".$ThemeSel."/style/editor.css\",";
} else {
    $edtcss = "editor_css : \"includes/tiny_mce/themes/default/editor_ui.css\",";
}

if ($wysiwyg_editor == 1) {
	if (is_admin($admin) AND !defined('NO_EDITOR')) {
		echo "<!-- tinyMCE -->
			<script language=\"javascript\" type=\"text/javascript\" src=\"includes/tiny_mce/tiny_mce.js\"></script>
			<script language=\"javascript\" type=\"text/javascript\">
		   	tinyMCE.init({
	      		mode : \"textareas\",
				theme : \"basic\",
				language : \"$lang\",
				$edtcss
				force_p_newlines: \"false\",
				force_br_newlines: \"true\"
		   	});
			</script>
			<!-- /tinyMCE -->";
	} 
	elseif (!defined('NO_EDITOR')) 
	{
		echo "<!-- tinyMCE -->
			<script language=\"javascript\" type=\"text/javascript\" src=\"includes/tiny_mce/tiny_mce.js\"></script>
			<script language=\"javascript\" type=\"text/javascript\">
		   	tinyMCE.init({
	      		mode : \"textareas\",
				theme : \"default\",
				language : \"$lang\",
				$edtcss
				force_p_newlines: \"false\",
				force_br_newlines: \"true\"
		   	});
			</script>
			<!-- /tinyMCE -->";
	}
}
//if ($module == 1 && file_exists("modules/$name/copyright.php")) {
/*
    echo '<script type="text/javascript">
          <!--
    function openwindow(){
    	window.open (\"modules/$name/copyright.php\",\"Copyright\",\"toolbar=no,location=no,directories=no,status=no,scrollbars=yes,resizable=no,copyhistory=no,width=400,height=200\");
    }
    //-->
    </SCRIPT>';
//}
*/
?>
<SCRIPT LANGUAGE="JavaScript">
<!-- Idea by:  Nic Wolfe -->
<!-- This script and many more are available free online at -->
<!-- The JavaScript Source!! http://javascript.internet.com -->

<!-- Begin
function popUp(URL) {
day = new Date();
id = day.getTime();
eval("page" + id + " = window.open(URL, '" + id + "', 'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0,width=1024,height=768');");
}
// End -->
</script>
<script type="text/javascript" src="includes/overlib/overlib.js"><!-- overLIB (c) Erik Bosrup --></script>
