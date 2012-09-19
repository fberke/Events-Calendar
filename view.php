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

require_once ('dwoo_inc.php');

// define constants
define ("EVENTSCAL_IMAGE_PATH", WB_PATH.MEDIA_DIRECTORY.'/eventscalendar/');
define ("EVENTSCAL_IMAGE_URL", WB_URL.MEDIA_DIRECTORY.'/eventscalendar/');
define ("EVENTSCAL_TEMPLATE_PATH", WB_PATH.'/modules/eventscalendar/templates/');

// define globals
global $wb;
global $settings, $categories, $fullEvents, $monthnames, $weekdays, $CALTEXT;
global $day, $month, $year;

require_once('functions.php');

if (isset($_GET['day'])) {
	$day = (int)$_GET['day'];
}
if (isset($_GET['month'])) {
	$month = (int)$_GET['month'];
} else {
	$month = date('n');
}
if (isset($_GET['year'])) {
	$year = (int)$_GET['year'];
} else {
	$year = date('Y');
}
if (isset($_GET['show'])) {
	$show = (int)$_GET['show'];
} else {
	$show = 0;
}
$list = (isset($_GET['list']));
$detail = (isset($_GET['detail']));
if (isset($_GET['id'])) {
	$event_id = (int)$_GET['id'];
} else {
	$event_id = 0;
}

define ("EVENTSCAL_FQDN", returnCalPageURL ($page_id));

$date_start = mktime (0, 0, 0, $month, 1, $year);
$date_end = mktime (23, 59, 59, $month, DaysCount($month,$year), $year);

$settings = fillSettingsArray ($section_id);
$events = fillEventArray($date_start, $date_end, $section_id);

if ($detail) {
	ShowEventEntry (returnEventByID ($events, $event_id, $section_id), $section_id);
//} elseif ($list) {
} else {
	echo ShowEventList($month, $year, $events, $section_id);
} /*else {
	echo ShowCalendar($month, $year, $events, $section_id, $page_id, false);	
}*/
?>