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


$monthnames = array (1 => "Januari", "Februari", "Maart", "April", "Mei", "Juni", "Juli", "Augustus", "September", "Oktober", "November", "December");
$weekdays = array ("Zondag", "Maandag", "Dinsdag", "Woensdag", "Donderdag", "Vrijdag", "Zaterdag", "Zondag");
$private = array ("Openbaar", "Priv&eacute;");


/************************************/
/*         BACKEND SETS             */
/************************************/

// Buttons
$CALTEXT['BTN_SAVE'] = "Opslaan";
$CALTEXT['BTN_DELETE'] = "Verwijderen";
$CALTEXT['BTN_BACK'] = "Terug";
$CALTEXT['BTN_SETTINGS'] = "Instellingen";
$CALTEXT['BTN_NEW-EVENT'] = "Nieuw";
$CALTEXT['BTN_SAVE-AS-NEW'] = "Opslaan als nieuw";
$CALTEXT['CUSTOM_FIELDS'] = "Extra velden";

// modify_settings.php
// Options
$CALTEXT['CAL-OPTIONS'] = "Instellingen";
$CALTEXT['CAL-OPTIONS-STARTDAY'] = "Weekstart";
$CALTEXT['CAL-OPTIONS-STARTDAY-0'] = "Maandag";
$CALTEXT['CAL-OPTIONS-STARTDAY-1'] = "Zondag";
$CALTEXT['CAL-OPTIONS-DATEFORMAT'] = "Datumweergave";
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
$CALTEXT['CATEGORY_MANAGEMENT'] = "Categoriebeheer";
$CALTEXT['CATEGORY_CHOOSE'] = "Choose event category";
$CALTEXT['CATEGORY_SELECT'] = "Categorie&hellip;";
$CALTEXT['CATEGORY_CHANGEE_BGCOLOR'] = "Event category and color";
$CALTEXT['CATEGORY_COLORCHOICE_HELP'] = "U kunt een kleur toewijzen aan iedere categorie door te klikken op de Kleurenwiel";
$CALTEXT['CATEGORY_USE_BGCOLOR'] = "Deze kleur toepassen in de kalender?";
// Advanced Options
$CALTEXT['ADVANCED_SETTINGS'] = "Geavanceerde instellingen";
// Support Information
$CALTEXT['SUPPORT_INFO'] = "Hulpinformatie";
$CALTEXT['SUPPORT_INFO_INTRO'] = "Voordat u deze module gebruikt, lees a.u.b. eerst de ";
// modify_customs.php
$CALTEXT['CUSTOM_FIELDTYPE'] = "Custom field type";
$CALTEXT['CUSTOM_FIELDNAME'] = "Veldnaam";
$CALTEXT['CUSTOM_OPTIONS-0'] = "Ongebruikt";
$CALTEXT['CUSTOM_OPTIONS-1'] = "Tekstveld";
$CALTEXT['CUSTOM_OPTIONS-2'] = "Tekstvak";
$CALTEXT['CUSTOM_OPTIONS-3'] = "WB-link";
$CALTEXT['CUSTOM_OPTIONS-4'] = "Afbeelding";
$CALTEXT['CUSTOM_TEMPLATE'] = "Veld-template";


/************************************/
/*         FRONTEND SETS            */
/************************************/

// modify.php / eventList(Editor)
// This is somewhat mixed FE/BE as eventList and eventListEditor have same field names
$CALTEXT['DATE'] = "Datum";
$CALTEXT['TIME'] = "Tijd";
$CALTEXT['DATE_START'] = "Start date";
$CALTEXT['DATE_END'] = "End date";
$CALTEXT['ONELINER'] = "Short Description (\"Oneliner\")";
$CALTEXT['DROPLET'] = "Droplet";
$CALTEXT['SUMMARY'] = "Summary";
$CALTEXT['DATE_DIVIDER'] = "-";
$CALTEXT['NAME'] = "Naam";
$CALTEXT['CUSTOM_SELECT_PAGELINK'] = "Selecteer pagina";
$CALTEXT['CUSTOM_UPLOAD_IMG'] = "Upload image";
$CALTEXT['CUSTOM_SELECT_IMG'] = "Selecteer afbeelding";
$CALTEXT['CUSTOM_SELECT_IMG_1ST_OPTION'] = "Geen afbeelding";
$CALTEXT['CATEGORY_OVERVIEW'] = "Category overview";
$CALTEXT['CATEGORY'] = "Categorie";
$CALTEXT['NON-SPECIFIED'] = "n.v.t.";
$CALTEXT['OWNER'] = "Owner";
$CALTEXT['ACTION'] = "Actions";
$CALTEXT['CALENDAR-BACK-MONTH'] = "Maandoverzicht";
$CALTEXT['PREV_MONTH'] = "Previous month";
$CALTEXT['NEXT_MONTH'] = "Next month";
$CALTEXT['VISIBLE'] = "Zichtbaarheid";
$CALTEXT['DESCRIPTION'] = "Beschrijving";
$CALTEXT['NODATES'] = "Geen eventiviteiten&hellip;";
$CALTEXT['NEXT_EVENT_LINK'] = "Go to next event&hellip;";
$CALTEXT['CALENDAR-DEFAULT-TEXT'] = ""; // put default event title here if you like

// Calendar Event-Popups
$CALTEXT ['POPUP_HEADING'] = "Events on ";
$CALTEXT ['POPUP_LINK_TITLE'] = "Find out more about ";
$CALTEXT ['POPUP_MORE_LINKTEXT'] = "Show all events&hellip;";
$CALTEXT ['POPUP_MORE_LINKTITLE'] = "Show all events&hellip;";

// ShowEventEntry
$CALTEXT['DATE-AND-TIME'] = "Datum en tijd";
$CALTEXT['TIMESTR'] = "uur";
$CALTEXT['NO_DESCRIPTION'] = "Geen beschrijving beschikbaar&hellip;";
$CALTEXT['PREV_EVENT_LINK'] = "Previous Event";
$CALTEXT['NEXT_EVENT_LINK'] = "Next Event";
$CALTEXT['EVENT_LIST_LINK'] = "Return to month view";

?>