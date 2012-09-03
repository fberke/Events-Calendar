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
$module_version	= '1.4.98';
$module_platform	= '1.x';
$module_author	= 'David Ilicz Klementa, Burkhard Hekers, Jurgen Nijhuis, John Maats, Frank Berke';
$module_license	= 'GNU General Public License';
$module_license_terms = '-';
$module_description	= 'Accessible and versatile event calendar for WB and Lepton CMS, based on ProCalendar; requires DWOO module.';
$module_home		= 'http://lepton-cms.org';
$module_guid		= 'e6eccb3a-a849-41f4-86d5-d4e6761a24be';

/* INFO */

/*
2012-05-19 - 2012-08-29 fberke
- frontend output now is template based to give you full access to the screen appearance - DWOO required!
- re-arranged backend, esp. custom fields
- 
- date and time is now stored in Unix time format in the database, i.e. in a single value
  date format in backend is Y-n-j internally and Y-m-d on screen
  the frontend date format can still be chosen in backend
- Moved settings from 'modify_customs' to 'modify_settings'
- Created an input/select combo for image size as seen on http://www.cs.tut.fi/~jkorpela/forms/combo.html
- Quick delete for events in backend
- removed lots of unused code and cleaned up/optimized what was still needed
- changed several default values
- many database changes (names and data types)
- new method for chaning event time
2012-05-18 fberke
- Cleaned up language files and re-arranged language array to meet eventual appearance in files
2012-05-15 fberke
- Added setting to hide previous calendar events
*/
?>

