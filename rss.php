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


// Include WB files
require_once(WB_PATH.'/framework/class.frontend.php');

// Check that GET values have been supplied
if(isset($_GET['page_id']) AND is_numeric($_GET['page_id'])) {
	$page_id = $_GET['page_id'];
} else {
	header('Location: /');
	exit(0);
}
// Set defaults
date_default_timezone_set('UTC');
$year  = date('Y', time()); 
$month = date('n', time());

// Editable values
// Show how many items, defaults to 10?
$max   = 10; 

// Set time frame for coming events, default one year
$year2 = $year + 1;
$month2 = $month;

$wb = new frontend();
$wb->page_id = $page_id;
$wb->get_page_details();
$wb->get_website_settings();

//checkout if a charset is defined otherwise use UTF-8
if(defined('DEFAULT_CHARSET')) {
	$charset=DEFAULT_CHARSET;
} else {
	$charset='utf-8';
}

// Get page link, needed for linkage
if ($page_id <> 0) {
   $sql = "SELECT link FROM ".TABLE_PREFIX."pages WHERE page_id = '".$page_id."'";
   $result = $database->query($sql);
   if ( $result->numRows() > 0 ) {
      while( $row = $result->fetchRow() ) {
         $page_link = $wb->page_link($row['link']);
      }
   }
}

// Sending XML header
header("Content-type: text/xml; charset=$charset" );

// Header info
// Required by CSS 2.0
echo '<?xml version="1.0" encoding="'.$charset.'"?>';
?> 
<rss version="2.0">
	<channel>
		<title><?php echo PAGE_TITLE; ?></title>
		<link>http://<?php echo $_SERVER['SERVER_NAME']; ?></link>
		<description> <?php echo PAGE_DESCRIPTION; ?></description>
<?php
// Optional header info 
?>
		<language><?php echo strtolower(DEFAULT_LANGUAGE); ?></language>
		<copyright><?php $thedate = date('Y'); $websitetitle = WEBSITE_TITLE; echo "Copyright {$thedate}, {$websitetitle}"; ?></copyright>
		<managingEditor><?php echo SERVER_EMAIL; ?></managingEditor>
		<webMaster><?php echo SERVER_EMAIL; ?></webMaster>
		<category><?php echo WEBSITE_TITLE; ?></category>
		<generator>WebsiteBaker Content Management System</generator>
<?php
// Get items from database

// Set start- and end date for query
$datestart = "$year-$month-1";
$dateend = "$year2-$month2-".cal_days_in_month(CAL_GREGORIAN, $month2,$year2);

// Fetch the items
// $sql = "SELECT DAY(date_start) AS day, id, custom1, date_start, time_start, date_end, time_end, name FROM ".TABLE_PREFIX."mod_eventscalendar_events WHERE ".$extrasql." date_start <='$dateend' AND date_end >='$datestart' AND private = 0 ORDER BY date_start,time_start LIMIT 0, ".$max." ";

$sql = "SELECT * FROM ".TABLE_PREFIX."mod_eventscalendar_events WHERE page_id = '$page_id' AND date_start <='$dateend' AND date_end >='$datestart' AND private = 0 ORDER BY date_start,time_start LIMIT 0, ".$max." ";

$result = $database->query($sql);

//Generating the news items
while($item = $result->fetchRow()){ 
	// Build url like : pages/kalendar.php?id=2&detail=1    
	$link = $page_link.'?id='.$item['id'].'&amp;detail=1';
	?>
		<item>
			<title><![CDATA[<?php echo $item["date_start"]." - ".stripslashes($item['event_title']); ?>]]></title>
			<description><![CDATA[<?php echo stripslashes($item["description"]); ?>]]></description>
			<guid><?php echo $link; ?></guid>
			<link><?php echo $link; ?></link>
		</item>
<?php } ?>
	</channel>
</rss>