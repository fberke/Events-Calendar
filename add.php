<?php

// include class.secure.php to protect this file and the whole CMS!
if (defined('WB_PATH')) {   
   include(WB_PATH.'/framework/class.secure.php');
} else {
   $oneback = "../";
   $root = $oneback;
   $level = 1;
   while (($level < 10) && (!file_exists($root.'/framework/class.secure.php'))) {
      $root .= $oneback;
      $level += 1;
   }
   if (file_exists($root.'/framework/class.secure.php')) {
      include($root.'/framework/class.secure.php');
   } else {
      trigger_error(sprintf("[ <b>%s</b> ] Can't include class.secure.php!", $_SERVER['SCRIPT_NAME']), E_USER_ERROR);
   }
}
// end include class.secure.php


// insert data into pages table
$database->query("INSERT INTO ".TABLE_PREFIX."mod_eventscalendar_settings SET
	page_id = '$page_id', 
	section_id = '$section_id',
	
	usecustom1 = 0,
	custom1 = '',
	
	usecustom2 = 0,
	custom2 = '',
	
	usecustom3 = 0,
	custom3 = ''
");
?>