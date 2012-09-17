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


$module_directory	= 'eventscalendar';
$module_name		= 'Events Calendar';
$module_function	= 'page';
$module_version	= '1.5.2';
$module_platform	= '1.x';
$module_author	= 'Frank Berke';
$module_license	= 'GNU General Public License';
$module_license_terms = '-';
$module_description	= 'Accessible and versatile event calendar for WB and Lepton CMS; requires DWOO module.';
$module_home		= 'https://github.com/fberke/Events-Calendar';
$module_guid		= 'cb47fc9f-7aa7-413a-a512-69cc5536299f';

?>

