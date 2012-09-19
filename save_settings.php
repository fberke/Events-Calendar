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


// Include WB admin wrapper script
require(WB_PATH.'/modules/admin.php');

$type		= $admin->get_post('type');
$page_id	= $admin->get_post('page_id');
$section_id	= $admin->get_post('section_id');

switch ($type) {

	case "manage_categories":
		$group_id	= $admin->get_post('group_id');
		$group_name	= $admin->get_post('group_name');
		$delete	= $admin->get_post('delete');
		$category_color = $admin->get_post('category_color');
		$use_category_color 	= $admin->get_post('use_category_color');
		if (!isset($use_category_color)) $use_category_color = 0;
		
		if ($delete) {
			$sql = "DELETE FROM ".TABLE_PREFIX."mod_eventscalendar_categories WHERE id=$group_id";
			$database->query($sql);
			$sql = "UPDATE ".TABLE_PREFIX."mod_eventscalendar_events SET category='0' WHERE category='$group_id'";
			$database->query($sql);
		} else {
			if ($group_name != "") {
				if (($group_id == 0)) {
					//echo "INSERT -> page_id: $page_id - group_name: $group_name  <br>";
					$sql = "INSERT INTO ";
					$sql .= TABLE_PREFIX."mod_eventscalendar_categories SET ";
					$sql .= "section_id='$section_id', ";
					$sql .= "category_name='$group_name', ";
					$sql .= "category_color='$category_color', ";
					$sql .= "use_category_color='$use_category_color' ";
				} else {
					//echo "UPDATE -> group_id: <br>";
					$sql = "UPDATE ";
					$sql .= TABLE_PREFIX."mod_eventscalendar_categories SET ";
					$sql .= "section_id='$section_id', ";
					$sql .= "category_name='$group_name', ";
					$sql .= "category_color='$category_color', ";
					$sql .= "use_category_color='$use_category_color' ";
					$sql .= " WHERE id=$group_id";
				}
				$database->query($sql);
			}
		}
		break;
		
	case "general_settings":
		$startday	= $admin->get_post('startday');
		$dateformat	= $admin->get_post('dateformat');
		$timeformat	= $admin->get_post('timeformat');
		$prevdates	= $admin->get_post('prevdates');
		$private	= $admin->get_post('private');
		$maxprev	= $admin->get_post('maxprev');
		$resize      	= $admin->get_post_escaped('resize');
		$resize_other 	= $admin->get_post_escaped('resize_other');

		$sql = "UPDATE ";
		$sql .= TABLE_PREFIX."mod_eventscalendar_settings SET "; // create rest of the sql-query
		$sql .= "startday='$startday', ";
		$sql .= "show_prev_dates='$prevdates', ";
		$sql .= "show_private_dates='$private', ";
		$sql .= "maxprev='$maxprev', ";
		$sql .= "dateformat='$dateformat', ";
		$sql .= "timeformat='$timeformat', ";
		(isset($resize_other)) ? $sql .= "resize='$resize_other' " : $sql .= "resize='$resize' ";
		$sql .= " WHERE section_id=$section_id";
		
		$database->query($sql);
		break;
	
	case "modify_customs":
		$page_id = $admin->get_post('page_id');
		$section_id = $admin->get_post('section_id');
		
		$usecustom1 = $admin->get_post_escaped('usecustom1');
		$custom1 = $admin->get_post_escaped('custom1');
		
		$usecustom2 = $admin->get_post_escaped('usecustom2');
		$custom2 = $admin->get_post_escaped('custom2');
		
		$usecustom3 = $admin->get_post_escaped('usecustom3');
		$custom3 = $admin->get_post_escaped('custom3');
		
		$sql = "UPDATE ";
		$sql .= TABLE_PREFIX."mod_eventscalendar_settings SET "; // create rest of the sql-query
		$sql .= "usecustom1='$usecustom1', ";
		
		$sql .= "custom1='$custom1', ";
		$sql .= "usecustom2='$usecustom2', ";
		
		$sql .= "custom2='$custom2', ";
		$sql .= "usecustom3='$usecustom3', ";
		
		$sql .= "custom3='$custom3'";
		
		$database->query($sql);
		
		break;

}

if ($database->is_error()) {
	$admin->print_error($database->get_error(), $js_back);
} else {
	if ($type == "change_category" ) { 
	  $admin->print_success($TEXT['SUCCESS'], WB_URL."/modules/eventscalendar/modify_settings.php?page_id=".$page_id."&section_id=".$section_id);
	} else {
	  $admin->print_success($MESSAGE['PAGES']['SAVED'], ADMIN_URL.'/pages/modify.php?page_id='.$page_id);
	}
}


$admin->print_footer();

?>