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


//require('functions.php');

function endar_search($func_vars) {
	extrevent($func_vars, EXTR_PREFIX_ALL, 'func');
	
	// how many lines of excerpt we want to have at most
	$max_excerpt_num = $func_default_max_excerpt;
	$divider = ".";
	$result  = false;
	
	// Set start- and end date for query
	$year  = date('Y', time());
	$month = date('n', time());
	$datestart = "$year-$month-1";
	$yearend = $year + 15;
	$dateend = "$yearend-$month-".cal_days_in_month(CAL_GREGORIAN, $month,$year);
	
	$table = TABLE_PREFIX."mod_eventscalendar_events";
	$query = $func_database->query("
		SELECT id,event_title,description,page_id
		FROM $table
		WHERE section_id='$func_section_id'
		
		AND date_start <='$dateend' AND date_end >='$datestart' AND private = 0
		");
	
	$PageName = $func_page_title;
	
	if($query->numRows() > 0) {
		while($res = $query->fetchRow()) {
			$text = "";
			
			$text .= $res['event_title'].$divider.$res['description'].$divider; // Default search: only the WYSIWYG-fields
			//$text .= $res['event_title'].$divider.$res['description'].$divider.$res['custom1'].$divider.$res['custom2'].$divider.$res['custom3'].$divider;
			
			// Use the line above to add 1, 2 or 3 Custom fields to the search
			
			$func_page_title = $PageName.":<br/>".$res['event_title'];
			
			$link = "&amp;page_id=".$res['page_id']."&amp;id=".$res['id']."&amp;detail=1";
			
			//$func_page_description = "func_page_description is not used";
			$mod_vars = array (
				'page_link'          => $func_page_link,
				'page_link_target'   => $link,
				'page_title'         => $func_page_title,
				'page_description'   => $func_page_description,
				'page_modified_when' => $func_page_modified_when,
				'page_modified_by'   => $func_page_modified_by,
				'text'               => $text,
				'max_excerpt_num'    => $max_excerpt_num
				);
			
			if(print_excerpt2($mod_vars, $func_vars)) {
				$result = true;
			}
		}
	}
	return $result;
}

?>
