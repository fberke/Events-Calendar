<?php

require('../../config.php');

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


//if (!isset($_POST['event_id'])) exit("Cannot access this file directly");


if (LANGUAGE_LOADED) {
  if(file_exists(WB_PATH."/modules/eventscalendar/languages/".LANGUAGE.".php")) {
    require_once(WB_PATH."/modules/eventscalendar/languages/".LANGUAGE.".php");
  } else {
    require_once(WB_PATH."/modules/eventscalendar/languages/EN.php");
  }
}

//$update_when_modified = true;
require(WB_PATH.'/modules/admin.php');
// Include WB functions file
require(WB_PATH.'/framework/functions.php');

// Write immediately needed POST data into variables
$deleteevent	= $admin->get_post('delete');
$event_id	= $admin->get_post('event_id');
$date_start	= $admin->get_post('date1');
$date_end	= $admin->get_post('date2');
$starttime_hrs = $admin->get_post('starttime_hrs'); // BE always 24 hr
$starttime_mins = $admin->get_post('starttime_mins');
$endtime_hrs	= $admin->get_post('endtime_hrs'); // BE always 24 hr
$endtime_mins	= $admin->get_post('endtime_mins');

	// Check values and correct them if necessary
	if (!isset($date_start)) {
		// mktime is executed at a later point
		$date_start = date("Y-n-j");
	}
	if (!isset($date_end)) {
		$date_end = $date_start;
	}
	
	if ($starttime_hrs == "") {
		$starttime_hrs = 0;
	}
	if ($starttime_mins == "") {
		$starttime_mins = 0;
	}
	if ($endtime_hrs == "") {
		$endtime_hrs = 0;
	}
	if ($endtime_mins == "") {
		$endtime_mins = 0;
	}
	
	// Create Unix timestamps to store into database
	$tmp_time = explode ("-", $date_start);
	//debug print_r ($tmp_time);
	$start_timestamp = mktime (
		$starttime_hrs,
		$starttime_mins,
		0,
		$tmp_time[1], // month
		$tmp_time[2], // day
		$tmp_time[0]  // year
	);
	// Used for building finishing URL
	$year_start = $tmp_time[0];
	$month_start = $tmp_time[1];
	
	$tmp_time = explode ("-", $date_end);
	//debug print_r ($tmp_time);
	$end_timestamp = mktime (
		$endtime_hrs,
		$endtime_mins,
		0,
		$tmp_time[1], // month
		$tmp_time[2], // day
		$tmp_time[0]  // year
	);
	
	// Check if start date is smaller than end date, otherwise correct end date
	if ($start_timestamp > $end_timestamp) {
		$end_timestamp = $start_timestamp;
	}
	/* debug
	echo '<p>Unix Timestamp '.time();
	echo '<p>Start Timestamp '.$start_timestamp;
	echo '<p>End Timestamp '.$end_timestamp;
	*/
	
if (isset($deleteevent) && isset($event_id)) { // delete from editevent
	$deleteevent = $event_id;
}
if (isset($deleteevent) && is_numeric($deleteevent)) { // delete from eventlisteditor
	$sql = "DELETE FROM ".TABLE_PREFIX."mod_eventscalendar_events WHERE id='$deleteevent'";
	//print_r ($sql);
	$database->query($sql);
	//$success = !$database->is_error();
} else {
	global $settings;
	$usecustom1	= $settings["usecustom1"];
	$usecustom2	= $settings["usecustom2"];
	$usecustom3	= $settings["usecustom3"];
	$resize	= $settings["resize"];
	
	// Write other POST data into variables
	$success = true;
	$out = "";
	$SaveAsNew	= $admin->get_post('saveasnew');
	//$dateformat	= $admin->get_post('dateformat'); // PHP-like, Y-m-d and variations
	//$timeformat	= $admin->get_post('timeformat'); // PHP-like, H:i and variations
	$section_id	= $admin->get_post('section_id');
	$page_id	= $admin->get_post('page_id');
	$owner		= $admin->get_post('owner');
	$private	= $admin->get_post('private');
	$category	= $admin->get_post('category');
	$event_title	= $admin->get_post_escaped('event_title');
	$description	= $admin->add_slashes($admin->get_post('description'));
	$oneliner	= $admin->add_slashes($admin->get_post('oneliner'));
	$summary	= $admin->add_slashes($admin->get_post('summary'));
	$image		= $admin->get_post_escaped('image_select');
	$droplet	= $admin->add_slashes($admin->get_post('droplet'));

	// Process POST data
	(isset($SaveAsNew)) ? $event_id = 0 : $event_id = $admin->get_post('event_id');
	
	function is_image($path) {
		$a = getimagesize($path);
		$image_type = $a[2];
		
		if (in_array($image_type, array(IMAGETYPE_GIF , IMAGETYPE_JPEG ,IMAGETYPE_PNG , IMAGETYPE_BMP))) {
			return true;
		}
		return false;
	}

	// Check if the user gave an image for upload
	function saveImage ($uploaded, $defined) {
		if (!defined ('EVENTSCAL_IMAGE_PATH'))
			define ('EVENTSCAL_IMAGE_PATH', WB_PATH.MEDIA_DIRECTORY.'/eventscalendar/');
		if (!defined ('EVENTSCAL_IMAGE_URL'))
			define ('EVENTSCAL_IMAGE_URL', WB_URL.MEDIA_DIRECTORY.'/eventscalendar/');
		
		global $admin, $resize, $MESSAGE;
		
		if ($defined == '0') $defined='';
		
		if (isset($_FILES[$uploaded]['tmp_name']) && ($_FILES[$uploaded]['tmp_name'] != '') && (is_image ($_FILES[$uploaded]['tmp_name']))) {
			
			// Get real filename and set new filename
			$filename = $_FILES[$uploaded]['name'];
			$new_filename = EVENTSCAL_IMAGE_PATH.$filename;
			
			// Make sure the target directory exists
			make_dir(EVENTSCAL_IMAGE_PATH);
			
			// Upload image
			move_uploaded_file($_FILES[$uploaded]['tmp_name'], $new_filename);
			
			// Check if we need to create a thumb
			if ($resize != 0) {
				// Resize the image
				$filename = 'thumb_'.$filename.'.jpg';
				$thumb_location = EVENTSCAL_IMAGE_PATH.$filename;
				if (make_thumb($new_filename, $thumb_location, $resize)) {
					// Delete the eventual image and replace with the resized version
					unlink($new_filename);
					rename($thumb_location, $new_filename);
				}
			}
			return $filename;
		}
		return $defined;
	}
	
	$image	= saveImage('image_upload', $image);
	if ($usecustom1 <> 0) $custom1 = $admin->get_post_escaped('custom1');
	if ($usecustom1 == 4) $custom1 = saveImage('custom_image1', $custom1);
	if ($usecustom2 <> 0) $custom2 = $admin->get_post_escaped('custom2');
	if ($usecustom2 == 4) $custom2 = saveImage('custom_image2', $custom2);
	if ($usecustom3 <> 0) $custom3 = $admin->get_post_escaped('custom3');
	if ($usecustom3 == 4) $custom3 = saveImage('custom_image3', $custom3);
	
	if(trim($event_title)!="") {
		if ($event_id==0) {
			$sql = "INSERT INTO ".TABLE_PREFIX."mod_eventscalendar_events SET ";
			$sql .= "section_id='$section_id', ";
			$sql .= "page_id='$page_id', ";
			$sql .= "owner='$owner', ";
			$sql .= "private='$private', ";
			$sql .= "date_start='$start_timestamp', ";
			$sql .= "date_end='$end_timestamp', ";
			$sql .= "category='$category', ";
			
			if ($usecustom1 <> 0) $sql .= "custom1='$custom1', ";
			if ($usecustom2 <> 0) $sql .= "custom2='$custom2', ";
			if ($usecustom3 <> 0) $sql .= "custom3='$custom3', ";
			
			$sql .= "event_title='$event_title', ";
			$sql .= "description='$description', ";
			$sql .= "oneliner='$oneliner', ";
			$sql .= "summary='$summary', ";
			$sql .= "image='$image', ";
			$sql .= "droplet='$droplet'";
		} else {
			$sql = "UPDATE ".TABLE_PREFIX."mod_eventscalendar_events SET ";
			$sql .= "section_id='$section_id', ";
			$sql .= "page_id='$page_id', ";
			$sql .= "owner='$owner', ";
			$sql .= "private='$private', ";
			$sql .= "date_start='$start_timestamp', ";
			$sql .= "date_end='$end_timestamp', ";
			$sql .= "category='$category', ";
			
			if ($usecustom1 <> 0) $sql .= "custom1='$custom1', ";
			if ($usecustom2 <> 0) $sql .= "custom2='$custom2', ";
			if ($usecustom3 <> 0) $sql .= "custom3='$custom3', ";
			
			$sql .= "event_title='$event_title', ";
			$sql .= "description='$description', ";
			$sql .= "oneliner='$oneliner', ";
			$sql .= "summary='$summary', ";
			$sql .= "image='$image', ";
			$sql .= "droplet='$droplet' ";
			
			$sql .= "WHERE id='$event_id'";
		}
		$database->query($sql);
	}
}

//Check if there is a database error, otherwise say successful
if ($database->is_error()) {
	$admin->print_error($database->get_error(), $js_back);
	flush();
	sleep(5);
} else {
	$admin->print_success($MESSAGE['PAGES']['SAVED'], ADMIN_URL.'/pages/modify.php?page_id='."$page_id&month=$month_start&year=$year_start");
	//flush();
	//sleep(3);
}

// Print admin footer
$admin->print_footer()

?>