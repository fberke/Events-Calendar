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

// we only remove the instance of the calendar that belongs to '$section_id'
// if it was the only one, the database will remain empty, but still there
$database->query("DELETE FROM ".TABLE_PREFIX."mod_eventscalendar_events WHERE section_id = '$section_id'");
$database->query("DELETE FROM ".TABLE_PREFIX."mod_eventscalendar_settings WHERE section_id = '$section_id'"); 
$database->query("DELETE FROM ".TABLE_PREFIX."mod_eventscalendar_categories WHERE section_id = '$section_id'"); 

?>