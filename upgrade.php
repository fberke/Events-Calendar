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


echo '<div class="info"><strong>Updating database for module: Events Calendar</strong></div>';

/*
// adding fields new in version 1.2:
//get settings table to see what needs to be created
$table=$database->query("SELECT * FROM `".TABLE_PREFIX."mod_eventscalendar_categories");
$fields = $table->fetchRow();


// If not already there, add new fields to the existing settings table
echo'<div class="info"><b>Adding new fields to the settings table</b></div>';

if (!isset($ields['format'])){
	$qs = "ALTER TABLE `".TABLE_PREFIX."mod_eventscalendar_categories` ADD `format` VARCHAR(255) NOT NULL default '' AFTER `name`";
	$database->query($qs);
	if($database->is_error()) {
		echo '<div class="warning">'.mysql_error().'</div><br />';
	} else {
		echo '<div class="info">Added new field `format` successfully</div>';
	}
}

if (!isset($ields['use_category_color'])){
	$qs = "ALTER TABLE `".TABLE_PREFIX."mod_eventscalendar_categories` ADD `use_category_color` INT NOT NULL default '0' AFTER `format`";
	$database->query($qs);
	if($database->is_error()) {
		echo '<div class="warning">'.mysql_error().'</div><br />';
	} else {
		echo '<div class="info">Added new field `use_category_color` successfully</div>';
	}
}
*/

?>
