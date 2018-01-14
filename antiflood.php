<?php
define('IN_MYBB', 1); require "./global.php";


add_breadcrumb("No permission to reply", "antiflood.php");
$lang->load("antiflood");
$lang->antiflood = $mybb->settings["antiflood_message"];
eval("\$page = \"".$templates->get("antiflood")."\";"); 
output_page($page); 
?>
