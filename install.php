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



if (defined('WB_URL')) {
	
	$database->query("DROP TABLE IF EXISTS ".TABLE_PREFIX."mod_eventscalendar_settings");
	$database->query("DROP TABLE IF EXISTS ".TABLE_PREFIX."mod_eventscalendar_events");
	$database->query("DROP TABLE IF EXISTS ".TABLE_PREFIX."mod_eventscalendar_categories");
	
	$database->query("CREATE TABLE ".TABLE_PREFIX."mod_eventscalendar_settings (
		section_id INT NOT NULL,
		page_id INT NOT NULL,
		startday TINYINT default '0',
		timeformat VARCHAR(15) NOT NULL default 'H:i',
		show_prev_dates TINYINT default '1',
		show_private_dates TINYINT default '1',
		maxprev TINYINT default '2',
		resize SMALLINT default '0',
		dateformat VARCHAR(15) NOT NULL default 'd.m.Y',
		usecustom1 TINYINT default '0',
		custom1 VARCHAR(50) NOT NULL,
		usecustom2 TINYINT default '0',
		custom2 VARCHAR(50) NOT NULL,
		usecustom3 TINYINT default '0',
		custom3 VARCHAR(50) NOT NULL,
		PRIMARY KEY (section_id))");
	
	$database->query("CREATE TABLE ".TABLE_PREFIX."mod_eventscalendar_events (
		id INT NOT NULL AUTO_INCREMENT,
		section_id INT NOT NULL,
		page_id INT NOT NULL,
		owner TINYINT,
		private TINYINT default '0',
		date_start INT,
		date_end INT,
		category TINYINT,
		event_title VARCHAR(125),
		description TEXT,
		oneliner VARCHAR(255),
		summary VARCHAR(500),
		image VARCHAR(125),
		droplet VARCHAR(125),
		custom1 TEXT,
		custom2 TEXT,
		custom3 TEXT,
		PRIMARY KEY (id))");
	
	$database->query("CREATE TABLE ".TABLE_PREFIX."mod_eventscalendar_categories (
		id TINYINT NOT NULL AUTO_INCREMENT,
		section_id INT NOT NULL,
		category_name VARCHAR(80) default '',
		category_color VARCHAR(15) default '',
		use_category_color TINYINT default '0',
		PRIMARY KEY (id))");
	
        
	// Insert info into the search table
	// Module query info
	$field_info = array();
	$field_info['page_id'] = 'page_id';
	$field_info['title'] = 'page_title';
	$field_info['link'] = 'link';
	$field_info['description'] = 'description';
	$field_info['modified_when'] = 'modified_when';
	$field_info['modified_by'] = 'modified_by';
	$field_info = serialize($field_info);
	
	$database->query("INSERT INTO ".TABLE_PREFIX."search (name,value,extra) VALUES ('module', 'endar', '$field_info')");
	
	// Query start
	$query_start_code = "SELECT [TP]pages.page_id, [TP]pages.page_title,  [TP]pages.link, [TP]pages.description, [TP]pages.modified_when, [TP]pages.modified_by FROM [TP]mod_eventscalendar_events, [TP]pages WHERE ";
	$database->query("INSERT INTO ".TABLE_PREFIX."search (name,value,extra) VALUES ('query_start', '$query_start_code', 'endar')");
	
	// Query body
	$query_body_code = "
	[TP]pages.page_id    = [TP]mod_eventscalendar_events.page_id AND [TP]mod_eventscalendar_events.name LIKE \'%[STRING]%\'
	OR [TP]pages.page_id = [TP]mod_eventscalendar_events.page_id AND [TP]mod_eventscalendar_events.description LIKE \'%[STRING]%\'";
	$database->query("INSERT INTO ".TABLE_PREFIX."search (name,value,extra) VALUES ('query_body', '$query_body_code', 'endar')");
	
	// Query end
	$query_end_code = "";
	$database->query("INSERT INTO ".TABLE_PREFIX."search (name,value,extra) VALUES ('query_end', '$query_end_code', 'endar')");
	
	// Insert blank row (there needs to be at least on row for the search to work)
	$database->query("INSERT INTO ".TABLE_PREFIX."mod_eventscalendar_events (page_id,section_id) VALUES ('0','0')");
	
	// Make calendar images directory
	make_dir(WB_PATH.MEDIA_DIRECTORY.'/calendar/');  
}



?>