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



$database->query("DELETE FROM ".TABLE_PREFIX."search WHERE name = 'module' AND value = 'mod_eventscalendar_settings'");
$database->query("DELETE FROM ".TABLE_PREFIX."search WHERE extra = 'mod_eventscalendar_settings'");
$database->query("DROP TABLE ".TABLE_PREFIX."mod_eventscalendar_settings");
$database->query("DROP TABLE ".TABLE_PREFIX."mod_eventscalendar_events");
$database->query("DROP TABLE ".TABLE_PREFIX."mod_eventscalendar_categories");

?>