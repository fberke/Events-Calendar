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

require_once (WB_PATH.'/framework/functions.php');
require_once ('dwoo_inc.php');
require_once ('functions.php');

// define constants
define ("EVENTSCAL_IMAGE_PATH", WB_PATH.MEDIA_DIRECTORY.'/eventscalendar/');
define ("EVENTSCAL_IMAGE_URL", WB_URL.MEDIA_DIRECTORY.'/eventscalendar/');
define ("EVENTSCAL_TEMPLATE_PATH", WB_PATH.'/modules/eventscalendar/templates/');


$year=date('Y');
$month=date('n');
$day=date('j');

if ((isset($_GET['day'])) && ($_GET['day']!="")) {
	$day = (int)$_GET['day'];
} else $day = 0;

if (isset($_GET['edit'])) {
	$editMode = $_GET['edit'];
} else $editMode = "no";

if ((isset($_GET['month']))and($_GET['month']!="")) {
	$month = (int)$_GET['month'];
}

if ((isset($_GET['year']))and($_GET['year']!="-")) {
	$year = (int)$_GET['year'];
} 

if (isset($_GET['id'])) {
	$edit_id = (int)$_GET['id'];
} else $edit_id = 0;

$date_start = mktime (0, 0, 0, $month, 1, $year);
$date_end = mktime (23, 59, 59, $month, DaysCount($month,$year), $year);
$events = fillEventArray($date_start, $date_end, $section_id);
$categories = fillCategoryArray($section_id);
global $settings;
$settings = fillSettingsArray ($section_id);

// For some php reason this must be here and not in the functions file where it was.
// If in functions the ckeditor will error with array_key_exists() expects parameter 2 to be array,
// null given in .../modules/ckeditor/include.php on line 182
// It seems like global doesn't work from a included function.

if(!isset($wysiwyg_editor_loaded)) {
	$wysiwyg_editor_loaded=true;
	if (!defined('WYSIWYG_EDITOR') OR WYSIWYG_EDITOR=="none" OR !file_exists(WB_PATH.'/modules/'.WYSIWYG_EDITOR.'/include.php')) {
		function show_wysiwyg_editor($name,$id,$content,$width,$height) {
			echo '<textarea name="'.$name.'" id="'.$id.'" style="width: '.$width.'; height: '.$height.';">'.$content.'</textarea>';
		}
	} else {
		$id_list=array("short","long");
		require(WB_PATH.'/modules/'.WYSIWYG_EDITOR.'/include.php');
	}
}
?>

<div class="modify_content">
<?php
	echo ShowCalendar($month, $year, $events, $section_id, $page_id, true);
	ShowEventListEditor($events, $day, $page_id);
	//ShowEventEditor($events, $day, $show, $editMode, $month, $year, $edit_id);
	ShowEventEditor($events, $day, $editMode, $month, $year, $edit_id);
?>
</div>