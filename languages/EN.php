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


$monthnames = array (1 => "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
$weekdays = array ("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
$private = array ("public", "private");


/************************************/
/*         BACKEND SETS             */
/************************************/

// Buttons
$CALTEXT['BTN_SAVE'] = "Save";
$CALTEXT['BTN_DELETE'] = "Delete";
$CALTEXT['BTN_BACK'] = "Back";
$CALTEXT['BTN_SETTINGS'] = "Settings";
$CALTEXT['BTN_NEW-EVENT'] = "New event";
$CALTEXT['BTN_SAVE-AS-NEW'] = "Save as new";
$CALTEXT['CUSTOM_FIELDS'] = "Custom fields";

// modify_settings.php
// Options
$CALTEXT['CAL-OPTIONS'] = "Settings";
$CALTEXT['CAL-OPTIONS-STARTDAY'] = "Week starts on";
$CALTEXT['CAL-OPTIONS-STARTDAY-0'] = "Monday";
$CALTEXT['CAL-OPTIONS-STARTDAY-1'] = "Sunday";
$CALTEXT['CAL-OPTIONS-DATEFORMAT'] = "Date format";
$CALTEXT['CAL-OPTIONS-TIMEFORMAT'] = "Time format";
$CALTEXT['CAL-OPTIONS-MAXPREV'] = "Event preview in calendar";
$CALTEXT['CAL-OPTIONS-PREVDATES'] = "Previous Events";
$CALTEXT['CAL-OPTIONS-PREVDATES-0'] = "Don't show";
$CALTEXT['CAL-OPTIONS-PREVDATES-1'] = "Show";
$CALTEXT['CAL-OPTIONS-PRIVATE'] = "Private Events";
$CALTEXT['CAL-OPTIONS-PRIVATE-0'] = "Show to creator only";
$CALTEXT['CAL-OPTIONS-PRIVATE-1'] = "Show to any logged in user";
$CALTEXT['RESIZE_IMAGE'] = "Image resize";
$CALTEXT['RESIZE_IMAGE_TO'] = "Choose image size";
$CALTEXT['RESIZE_IMAGE_NONE'] = "None";
$CALTEXT['RESIZE_IMAGE_OTHER'] = "Other, please select:";
// Manage Categories
$CALTEXT['CATEGORY_MANAGEMENT'] = "Category management";
$CALTEXT['CATEGORY_CHOOSE'] = "Choose event category";
$CALTEXT['CATEGORY_SELECT'] = "Category&hellip;";
$CALTEXT['CATEGORY_CHANGEE_BGCOLOR'] = "Event category and color";
$CALTEXT['CATEGORY_COLORCHOICE_HELP'] = "You can set a colour for each category by clicking on the chromatic circle";
$CALTEXT['CATEGORY_USE_BGCOLOR'] = "Use this color in calendar?";
// Advanced Options
$CALTEXT['ADVANCED_SETTINGS'] = "Advanced settings";
// Support Information
$CALTEXT['SUPPORT_INFO'] = "Support information";
$CALTEXT['SUPPORT_INFO_INTRO'] = "Before using this module, please read the ";
// modify_customs.php
$CALTEXT['CUSTOM_FIELDTYPE'] = "Custom field type";
$CALTEXT['CUSTOM_FIELDNAME'] = "Field name";
$CALTEXT['CUSTOM_OPTIONS-0'] = "Unused";
$CALTEXT['CUSTOM_OPTIONS-1'] = "Text field";
$CALTEXT['CUSTOM_OPTIONS-2'] = "Text area";
$CALTEXT['CUSTOM_OPTIONS-3'] = "Page-Link";
$CALTEXT['CUSTOM_OPTIONS-4'] = "Image";
$CALTEXT['CUSTOM_TEMPLATE'] = "Field template";


/************************************/
/*         FRONTEND SETS            */
/************************************/

// modify.php / eventList(Editor)
// This is somewhat mixed FE/BE as eventList and eventListEditor have same field names
$CALTEXT['DATE'] = "Date";
$CALTEXT['TIME'] = "Time";
$CALTEXT['DATE_START'] = "Start date";
$CALTEXT['DATE_END'] = "End date";
$CALTEXT['ONELINER'] = "Short Description (\"Oneliner\")";
$CALTEXT['DROPLET'] = "Droplet";
$CALTEXT['SUMMARY'] = "Summary";
$CALTEXT['DATE_DIVIDER'] = "-";
$CALTEXT['NAME'] = "Name";
$CALTEXT['CUSTOM_SELECT_PAGELINK'] = "Select page";
$CALTEXT['CUSTOM_UPLOAD_IMG'] = "Upload image";
$CALTEXT['CUSTOM_SELECT_IMG'] = "Select image";
$CALTEXT['CUSTOM_SELECT_IMG_1ST_OPTION'] = "No image";
$CALTEXT['CATEGORY_OVERVIEW'] = "Category overview";
$CALTEXT['CATEGORY'] = "Category";
$CALTEXT['NON-SPECIFIED'] = "none";
$CALTEXT['OWNER'] = "Owner";
$CALTEXT['ACTION'] = "Actions";
$CALTEXT['CALENDAR-BACK-MONTH'] = "Month view";
$CALTEXT['PREV_MONTH'] = "Previous month";
$CALTEXT['NEXT_MONTH'] = "Next month";
$CALTEXT['VISIBLE'] = "Visibility";
$CALTEXT['DESCRIPTION'] = "Description";
$CALTEXT['NODATES'] = "No events set&hellip;";
$CALTEXT['NEXT_EVENT_LINK'] = "Go to next event&hellip;";
$CALTEXT['CALENDAR-DEFAULT-TEXT'] = ""; // put default event title here if you like

// Calendar Event-Popups
$CALTEXT ['POPUP_HEADING'] = "Events on ";
$CALTEXT ['POPUP_LINK_TITLE'] = "Find out more about ";
$CALTEXT ['POPUP_MORE_LINKTEXT'] = "Show all events&hellip;";
$CALTEXT ['POPUP_MORE_LINKTITLE'] = "Show all events&hellip;";

// ShowEventEntry
$CALTEXT['DATE-AND-TIME'] = "Date and time";
$CALTEXT['TIMESTR'] = "";
$CALTEXT['NO_DESCRIPTION'] = "No description available&hellip;";
$CALTEXT['PREV_EVENT_LINK'] = "Previous Event";
$CALTEXT['NEXT_EVENT_LINK'] = "Next Event";
$CALTEXT['EVENT_LIST_LINK'] = "Return to month view";

?>