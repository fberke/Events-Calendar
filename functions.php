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


if (LANGUAGE_LOADED) {
  if(file_exists(WB_PATH."/modules/eventscalendar/languages/".LANGUAGE.".php")) {
    require_once(WB_PATH."/modules/eventscalendar/languages/".LANGUAGE.".php");
  } else {
    require_once(WB_PATH."/modules/eventscalendar/languages/EN.php");
  }
}


//#######################################################################
// returns count of days in given month
function DaysCount (
	$month,
	$year
	) {
//#######################################################################
	return cal_days_in_month(CAL_GREGORIAN, $month, $year);
}


//#######################################################################
function FirstDay (
	$month,
	$year
) {
//
// returns number (in week) of first day in month,  this was made for countries, where week starts on Monday
// for PHP, 0 is always Sunday, 6 is Saturday
//#######################################################################
$english_order = date("w", mktime(0, 0, 0, $month, 1, $year));
//echo("FirstDay: " . $english_order);
return ($english_order == 0) ? 7 : $english_order;
}


//#############################################################################
// This function returns value of table-cell identified by row and column number.
function Cell (
	$row,
	$column,
	$firstday,
	$dayscount,
	$section_id
) {
//
//#############################################################################

if (IsStartDayMonday($section_id) == false) {
	$retval = ($row - 1) * 7 + $column ;
	if ($firstday < 7) $retval -= $firstday;
} else {
	$retval = ($row - 1) * 7 + $column - $firstday + 1;
}

if ($retval < 1   || $retval > $dayscount) {
	return 0;
}
return $retval;
}

//#######################################################################
function GetCalRowCount (
	$dayscount, // how many days has this month
	$firstday , // 1=Monday 7=Sunday
	$section_id 
) {
//#######################################################################
$IsMondayFirstDay = IsStartDayMonday($section_id);
$Extra = $IsMondayFirstDay ? 1 : 0;
// calc how many rows are needed
$rowcount = ceil($dayscount / 7);
// calc if all days fit to table..
if ($rowcount*7 - $firstday + $Extra < $dayscount) {
	//..no, add row to show left days
	$rowcount = $rowcount+1;
}
// special case to avoid empty row
if (!$IsMondayFirstDay && $firstday==7 ) $rowcount -= 1;
// return the right value
return $rowcount;
}

//#######################################################################
function ShowDate (
	$format,
	$unixtime
) {
//
// This function changes the given unixtime into human readable date or time formats 
// It also translates the monthnames as PHP returns them for date formats 'F' and 'M'
// into the monthnames loaded with the respective language files.
// If there is no monthname to translate date is returned in the given format.
// If language actually is English, it translates anyway, but it's easier to leave it
// to this than to look up the language loaded
//#######################################################################
global $CALTEXT;
global $monthnames;

if (strpos ($format, 'F') > 0) {
	return str_replace (
		date ('F', $unixtime),
		$monthnames[(date ('n', $unixtime))],
		date ($format, $unixtime));
} else if (strpos ($format, 'M') > 0) {
	return str_replace (
		date ('M', $unixtime),
		substr ($monthnames[(date ('n', $unixtime))], 0, 3),
		date ($format, $unixtime));
}
$output = date ($format, $unixtime);
return $output;
}


//########################################################################
// Only used in frontend!
function returnEventList (
	$day,
	$month,
	$year,
	$events,
	$section_id
) {
//
// Return: array of events for given day, month or other timeframe
// At this point the 'unreal' days of multi-day events are created,
// plus this function checks whether or not to show previous events
//
//########################################################################
global $CALTEXT;
global $database, $admin, $wb;
global $settings;

($month > 1) ? ($prevmonth = $month - 1) : ($prevmonth = 12);
($month < 12) ? ($nextmonth = $month + 1) : ($nextmonth = 1);
($month == 1) ? ($prevyear = $year - 1) : ($prevyear = $year);
($month == 12) ? ($nextyear = $year + 1) : ($nextyear = $year);

$dayscount = DaysCount($month, $year); 

// true if no specific day is given
$IsMonthOverview = ($day == "") ? 1 : 0;
// true if no specific month is given
$IsYearOverview = ($month == "") ? 1 : 0;

$today = date('Ynj');

$return = array();
$count = 0;

	$show_prev_dates = $settings["show_prev_dates"];

	$sizeofEvents = sizeof($events);
	
	if ($sizeofEvents > 0) { // are there any events at all?
		for ($i=0; $i < $sizeofEvents; $i++) {
			$tmp		= $events[$i];
			$daystart	= date('j', $tmp['date_start']);
			$monthstart	= date('n', $tmp['date_start']);
			$yearstart	= date('Y', $tmp['date_start']);
			$dayend	= date('j', $tmp['date_end']);
			$monthend	= date('n', $tmp['date_end']);
			$yearend	= date('Y', $tmp['date_end']);
			
			if ($IsMonthOverview) {
				// cover multi-day events
				($dayend != "") ? $day = $dayend : $day = $daystart;
			}
			
			$anyday = $year.$month.$day;
			if ($show_prev_dates || (!$show_prev_dates && ($anyday >= $today))) {
				if (MarkDayOK($day, $month, $year, $events[$i])) {
					$return[] = $tmp;
					$count++;
				}
			}
		}
	}
	return $return;
}


//########################################################################
function fullEventsArray (
	$section_id
	) {
// returns an array of ID, DATE_START, DATE_END for all events completely
// depending on given section_id and the allowed user
//########################################################################
global $database, $admin;
global $settings;

	// same as in fillEventArray()
	if ($admin->is_authenticated() && $admin->get_user_id() == 1) { // if user is admin show all events
		$selectPrivate = "";
	} else {
		$selectPrivate = "AND private = 0 "; // public events
		if ($admin->is_authenticated()) { // if user is authenticated
			$creatorOnly = ($settings['show_private_dates'] == 0) ? " AND (owner = " . $admin->get_user_id(). " )" : "";
			$selectPrivate .= " OR ( (private != 0)".$creatorOnly." )"; // add user's own private events
		}
	}

$sql = "SELECT id, date_start, date_end 
	FROM ".TABLE_PREFIX."mod_eventscalendar_events 
	WHERE section_id='$section_id'
	$selectPrivate 
	ORDER BY date_start ASC";
	
$db = $database->query($sql);

if ($db->numRows() > 0) {
	$events = array();
	while ($record = $db->fetchRow()) {
		$events [] = $record;
	}
	return ($events);
} else 	return (false);
}


//#############################################################################
// this function returns an array of event categories
function fillCategoryArray (
	$section_id
) {
//
//#############################################################################
global $database;

	$sql = "SELECT * FROM ".TABLE_PREFIX."mod_eventscalendar_categories WHERE section_id='$section_id' ORDER by category_name ASC ";
	$db = $database->query($sql);
	$retarray = array();
	if ($db->numRows() > 0) {
		while ($record = $db->fetchRow()) {
			$retarray[$record['id']] = $record['category_name'];
		}
		return($retarray);
	} else {
		return($retarray);
	}
}


//#############################################################################
// this function returns array filled event types grabbed from database
function fillSettingsArray (
	$section_id
) {
// Returns array with settings or FALSE if empty
//#############################################################################
global $database;

	$sql = "SELECT * FROM ".TABLE_PREFIX."mod_eventscalendar_settings WHERE section_id='$section_id'";
	$db = $database->query($sql);
	if ($db->numRows() > 0) {
		$record = $db->fetchRow();
		return $record;
	} 
	else return false;
}


//#############################################################################
// this function returns a fully qualified domain name plus path to the calendar page
// this is necessary to make esp. droplets work on any sub page.
function returnCalPageURL (
	$page_id
	) {
//#############################################################################
global $database;

	$sql = "SELECT link FROM ".TABLE_PREFIX."pages WHERE page_id = '".$page_id."'";
	$db = $database->query($sql);
	if ($db->numRows() > 0) {
		$record = $db->fetchRow();
		$return = page_link($record ['link']);
		return $return;
	}
	else return "";
}	


//#############################################################################
// this function only used for fillEventArray
function cmp ($a, $b) {
	if ($a['date_start'] == $b['date_start']) {
		return 0;
	}
	return ($a['date_start'] < $b['date_start']) ? -1 : 1;
}
// this function returns array filled with event data
function fillEventArray (
	$datestart,
	$dateend,
	$section_id,
	$event_id = false,
	$category = false
) {
//
//#############################################################################
global $database, $admin;
global $settings;

	if ($admin->is_authenticated() && $admin->get_user_id() == 1) { // if user is admin show all events
		$selectPrivate = "";
	} else {
		$selectPrivate = " AND private = 0 "; // public events
		if ($admin->is_authenticated()) { // if user is authenticated
			$creatorOnly = ($settings['show_private_dates'] == 0) ? " AND (owner = " . $admin->get_user_id(). " ) " : "";
			$selectPrivate .= " OR ( (private != 0)".$creatorOnly." ) "; // add user's own private events
		}
	}
	
	if ($event_id == false) {
		$selectByDate = " AND date_start <='$dateend' AND date_end >='$datestart' ";
	} else {
		$selectByDate = " AND a.id = '$event_id' ";
	}
	
	if ($category == false) {
		$selectByCategory = "";
	} else {
		$selectByCategory = " AND a.category = '$category' ";
	}
	// Note: event_id and category cannot go together or at least it wouldn't make much sense
	
	$sql = "SELECT a.*, e.category_name as category_name, e.category_color as category_color, e.use_category_color as use_category_color
			FROM ".TABLE_PREFIX."mod_eventscalendar_events as a
  			LEFT JOIN ".TABLE_PREFIX."mod_eventscalendar_categories as e
  			ON a.category = e.id
  			WHERE a.section_id='$section_id'
 			$selectByDate
 			$selectByCategory
  			$selectPrivate
  			ORDER BY date_start";
  			
  	$db = $database->query($sql);
  	if(!$db) echo $database->get_error();
  	
  	$events = array();
  	
  	if ($db->numRows() > 0) {
  		while ($ret = $db->fetchRow()) {
			
			// Create event details link
			$daystart	= date('j', $ret['date_start']);
			$monthstart	= date('n', $ret['date_start']);
			$yearstart	= date('Y', $ret['date_start']);
			
			$link_pre = rawurlencode ($ret['event_title']);
			$link = EVENTSCAL_FQDN."?$link_pre&amp;month=$monthstart&amp;year=$yearstart&amp;day=$daystart";
			if (isset($page_id)) {
				$link .= "&amp;page_id=$page_id";
			}
			$link .= "&amp;id=".$ret['id']."&amp;section_id=$section_id&amp;detail";
			
			// Add to array
			$ret['event_link'] = $link;
			
			$events[] = $ret;
		}
		usort($events, "cmp");
		return ($events);
	} else {
		return (null);
	}
}

//#############################################################################
function MarkDayOK (
	$day,
	$month,
	$year,
	$event // Array of one event
) {
//
// Return: 0: No event on given date
//         1: Yes there is an event on given date
//
//#############################################################################
$yearstart	= date('Y', $event['date_start']);
$monthstart	= date('n', $event['date_start']);
$daystart	= date('j', $event['date_start']);

$yearend	= date('Y', $event['date_end']);
$monthend	= date('n', $event['date_end']);
$dayend	= date('j', $event['date_end']);

if (mktime (0,0,0, $monthstart, $daystart, $yearstart) < mktime (0,0,0, $month ,$day, $year)) {
	if (($monthend == $month && $day <= $dayend && $year == $year ) ||
		(($monthend > $month || $yearend > $year) && $day > $daystart) ||
		($monthend > $month || $yearend > $year)) {
			return 1;
		}
	}
	else if (($daystart == $day && $monthstart == $month )) { // event starts and ends this month
		return 1;
	}
	return 0;
}

//#############################################################################
function DateHasEvent (
	$day,
	$month,
	$year,
	$events // Array with dates
) {
//
// Return: 0: No event on given date
//         1: Yes there is an event on given date
//
//#############################################################################
$sizeofEvents = sizeof($events);

for ($i = 0; $i < $sizeofEvents; $i++) {
	if (MarkDayOK ($day, $month, $year, $events[$i])) {
		return 1;
	}
}
return 0;
}


//#############################################################################
function IsStartDayMonday (
	$section_id
) {
//
//#############################################################################
global $settings;

if ($settings != false) {
	if ($settings['startday'] == 0) return true;
	if ($settings['startday'] == 1) return false;
}
return true;
}


//######################################################################
function returnNeighbouringEvent (
	$event_id,
	$section_id,
	$neighbour = 'next'
	) {
//
//  Return: full array of next or previous event in the line
//
//######################################################################
$fullEvents = fullEventsArray ($section_id);

$sizeofEvents = sizeof($fullEvents);
for ($i = 0; $i < $sizeofEvents; $i ++) {
	$single = $fullEvents [$i];
	if ($single ['id'] == $event_id) {
		switch ($neighbour) {
		case 'next':
			return ($i != ($sizeofEvents - 1)) // is not last one
				? fillEventArray ($fullEvents [$i + 1]['date_start'], $fullEvents [$i + 1]['date_end'], $section_id, $fullEvents [$i + 1]['id'])
				: false;
		case 'prev':
			return ($i != 0) // is not first one
				? fillEventArray ($fullEvents [$i - 1]['date_start'], $fullEvents [$i - 1]['date_end'], $section_id, $fullEvents [$i +-1]['id'])
				: false;
		default: return false;
		}
	}			
}
}

//######################################################################
function returnEventByID (
	$events,
	$event_id,
	$section_id
) {
//
//  Return: event array with specific ID
//
//######################################################################
$sizeofEvents = sizeof($events);

for ($i = 0; $i < $sizeofEvents; $i++) {
	$single = $events[$i];
	if ($single['id'] == $event_id) {
		$prevEvent = returnNeighbouringEvent ($event_id, $section_id, 'prev');
		$nextEvent = returnNeighbouringEvent ($event_id, $section_id, 'next');
		$single ['prevEvent'] = ($prevEvent) ? $prevEvent [0]['event_link'] : false;
		$single ['nextEvent'] = ($nextEvent) ? $nextEvent [0]['event_link'] : false;
		return $single;
	}
}
return false; // event doesn't exist
}


//#######################################################################
function ShowCalendar (
	$month,
	$year,	
	$events,
	$section_id,
	$page_id,
	$IsBackend
) {
//
//  Return: Complete Calendar
//
//####################################################################### 
global $database, $admin, $wb;
global $monthnames, $weekdays;
global $settings;
global $dwoo;
global $CALTEXT;

$header_prefix = '';

	if ($IsBackend) {
		$link_prefix = 'modify.php';
		$header_prefix = $link_prefix;
		/*
	} else 	if ($page_id <> 0) {
		$link_prefix = EVENTSCAL_FQDN;
	} else $link_prefix = ''; // links won't work then
	*/
	} else $link_prefix = EVENTSCAL_FQDN;
	
	($month > 1) ? ($prevmonth = $month - 1) : ($prevmonth = 12);
	($month < 12) ? ($nextmonth = $month + 1) : ($nextmonth = 1);
	($month == 1) ? ($prevyear = $year - 1) : ($prevyear = $year);
	($month == 12) ? ($nextyear = $year + 1) : ($nextyear = $year);
	
	$dayscount = DaysCount ($month, $year);
	$firstday  = FirstDay ($month, $year);
	
	$sizeofEvents = sizeof($events);
	
	// Fill caption
	$caption = array (
		'previousYearLink' => $header_prefix.'?page_id=' . $page_id . '&amp;month=' . $month . '&amp;year=' . ($year-1),
		'previousYearLinkTitle' => ($year-1),
		'previousMonthLink' => $header_prefix.'?page_id=' . $page_id . '&amp;month=' . $prevmonth . '&amp;year=' . $prevyear,
		'previousMonthLinkTitle' => $monthnames[(int)$prevmonth],
		'monthname' => $monthnames[$month],
		'year' => $year,
		'nextMonthLink' => $header_prefix.'?page_id=' . $page_id . '&amp;month=' . $nextmonth . '&amp;year=' . $nextyear,
		'nextMonthLinkTitle' => $monthnames[(int)$nextmonth],
		'nextYearLink' => $header_prefix.'?page_id=' . $page_id . '&amp;month=' . $month . '&amp;year=' . ($year+1),
		'nextYearLinkTitle' => ($year+1)
		);
	
	$show_prev_dates = $settings["show_prev_dates"];
	
	$today = date('Ynj');
	
	$rowcount = GetCalRowCount ($dayscount, $firstday ,$section_id);
	
	// Fill table head
	$thead = array ();
	if (!IsStartDayMonday($section_id)) {
		$colstart = 0;
		$colend = 6;
	} else {
		$colstart = 1;
		$colend = 7;
	}
	for ($column = $colstart; $column <= $colend; $column++) {
		$thead [] = $weekdays[$column];
	}
	
	// Fill actual calendar: rows and cells
	$rows = array ();
	
	for ($row = 1; $row <= $rowcount; $row++) {
		
		$cells = array ();
		
		for ($col = 1; $col <= 7; $col++) {
			
			//print_r ($link_prefix."\n");
			
			$daysContents = array ();
			
			$day = Cell ($row, $col, $firstday, $dayscount, $section_id);
			
			if ($day != 0) {
				$anyday = $year.$month.$day;
				$istoday = ($today == $anyday);
				//echo "<p>$anyday - $today</p>";
				$HideAnyway = ((!$show_prev_dates) && ($anyday < $today) && (!$IsBackend));
				
				if (DateHasEvent($day, $month, $year, $events) && (!$HideAnyway)) {
					
					$eventEntries = array ();
					
					$daysContents ['dayType'] = ($IsBackend) ? 'eventBE' : 'event';
					$daysContents ['dayNr'] = $day;
					$daysContents ['monthname'] = $monthnames[$month];
					$daysContents ['isToday'] = $istoday;
					// F+B: Create link to event list
					$daysContents ['eventListLink'] = $link_prefix.'?page_id='.$page_id.'&amp;day='.$day.'&amp;month='.$month.'&amp;year='.$year.'&amp;list';
					
					if (($settings['maxprev'] > 0) && (!$IsBackend)) {
						
						$dayevents = returnEventList ($day, $month, $year, $events, $section_id);
						
						$exceedsMaxprev = (sizeof ($dayevents) > $settings['maxprev']);
						(!$exceedsMaxprev) ? $max = sizeof ($dayevents) : $max = $settings['maxprev'];
					
						$daysContents ['eventListHeading'] = $CALTEXT['POPUP_HEADING'].ShowDate ($settings['dateformat'], mktime (0,0,0, $month, $day, $year));
						
						for ($i = 0; $i < $max; $i++) {
							
							$single = $dayevents[$i];
							
							$eventDetails = array (
								'eventType' => 'event',
								'eventDetailsLink' => $single['event_link'],
								'eventDetailsLinkTitle' => $CALTEXT['POPUP_LINK_TITLE'],
								'eventTitle' => $single['event_title'],
								'eventOneliner' => $single['oneliner'],
								'eventCategory' => $single['category_name'],
								'eventColor' => ($single['use_category_color']) ? $single['category_color'] : 'transparent',
								'eventTime' => ShowDate ($settings['timeformat'], $single['date_start']),
								'eventTimestring' => $CALTEXT['TIMESTR']
								);
							 
							$eventEntries [] = $eventDetails;
						}
						if ($exceedsMaxprev) {
							$eventDetails = array (
								'eventType' => 'link',
								'eventListLink' => $link_prefix.'?page_id='.$page_id.'&amp;day='.$day.'&amp;month='.$month.'&amp;year='.$year.'&amp;list',
								'eventListLinkTitle' => $CALTEXT['POPUP_MORE_LINKTITLE'],
								'eventListLinkText' => $CALTEXT['POPUP_MORE_LINKTEXT']
								);
							$eventEntries [] = $eventDetails;
						}
						
						$daysContents ['events'] = $eventEntries;
						
					}
				} else { // Empty cell
					// Backend: Create new event link
					$daysContents ['dayType'] = 'normal';
					$daysContents ['dayNr'] = ($IsBackend) ? '<a href="'.$link_prefix.'?page_id='.$page_id.'&amp;day='.$day.'&amp;month='.$month.'&amp;year='.$year.'&amp;edit=new">'.$day.'</a>' : $day;
					$daysContents ['monthname'] = $monthnames[$month];
					$daysContents ['isToday'] = $istoday;
				}
			} else {
				if ($day == 0) { // day belongs to other month
					$daysContents ['dayType'] = 'noday';
					$daysContents ['dayNr'] = $day;
				}
			}
		$cells [] = $daysContents;	
		}
	$rows []['cells'] = $cells;	
	}
	
	// build final calendar array
	$calendar = array ();
	$calendar ['caption'] = $caption;
	$calendar ['thead'] = $thead;
	$calendar ['rows'] = $rows;
	
	//print_r ($calendar);
	
	// populate template
	$data = $dwoo -> get (EVENTSCAL_TEMPLATE_PATH.'calendar.tpl', $calendar);
	// Make sure wblinks and droplets are executed;
	if (!$IsBackend) $wb->preprocess($data);
	
	return $data;
}


//######################################################################
function ShowEventList (
	//$day,
	$month,
	$year,
	$events,
	$section_id
) {
//######################################################################

global $database;
global $CALTEXT, $monthnames;
global $settings;
global $page_id;
global $dwoo;
	
$sizeofEvents = sizeof ($events);
$daysinMonth = DaysCount ($month, $year);

$eventsarr = false;

if ($sizeofEvents > 0 ) {
	
	$eventsarr = array ();
	
	$data = new Dwoo_Data ();
	
	for ($day = 1; $day <= $daysinMonth; $day++) {
	// Events are ordered by date_start
		if (DateHasEvent ($day, $month, $year, $events)) {
			
			$entries = array ();
			$single = returnEventList ($day, $month, $year, $events, $section_id);
			
			foreach ($single AS $entry) {
				
				$entries [] = array (
					'eventDetailsLink' => $entry['event_link'],
					'eventDetailsLinkTitle' => $CALTEXT['POPUP_LINK_TITLE'],
					'eventTitle' => $entry['event_title'],
					'eventOneliner' => $entry['oneliner'],
					'eventSummary' => $entry['summary'],
					'eventDateStartTitle' => $CALTEXT['DATE_START'],
					'eventDateStart' => ShowDate ($settings['dateformat'], $entry['date_start']),
					'eventTimeStart' => ShowDate ($settings['timeformat'], $entry['date_start']),
					'eventDateEndTitle' => $CALTEXT['DATE_END'],
					'eventDateEnd' => ShowDate ($settings['dateformat'], $entry['date_end']),
					'eventTimeEnd' => ShowDate ($settings['timeformat'], $entry['date_end']),
					'eventTimestring' => $CALTEXT['TIMESTR'],
					'eventCategoryTitle' => $CALTEXT['CATEGORY'],
					'eventCategory' => $entry['category_name'],
					'eventColor' => ($entry['use_category_color']) ? $entry['category_color'] : 'transparent'
					);
			}
			
			$eventsarr [] = array (
				'date' => ShowDate ($settings['dateformat'], mktime (0,0,0, $month, $day, $year)),	
				'entries' => $entries
				);
		}
	}
}
	if ($month == 1) {
		$prevmonth = 12;
		$prevyear = $year - 1;
	} elseif ($month == 12) {
		$nextmonth = 1;
		$nextyear = $year + 1;
	} else {
		$prevmonth = $month - 1;
		$prevyear = $year;
		$nextmonth = $month + 1;
		$nextyear = $year;
	}
			
	$data = array (
		'events' => $eventsarr,
		'noDates' => $CALTEXT ['NODATES'],
		'prevMonthLinkText' => $CALTEXT ['PREV_MONTH'],
		'nextMonthLinkText' => $CALTEXT ['NEXT_MONTH'],
		'prevMonthName' => $monthnames[$prevmonth],
		'nextMonthName' => $monthnames[$nextmonth],
		'prevMonthLink' => '?page_id='.$page_id.'&amp;month='.$prevmonth.'&amp;year='.$prevyear.'&amp;list',
		'nextMonthLink' => '?page_id='.$page_id.'&amp;month='.$nextmonth.'&amp;year='.$nextyear.'&amp;list'
		);
	
	return $dwoo -> get (EVENTSCAL_TEMPLATE_PATH.'event_list.tpl', $data);
}


//######################################################################
function ShowEventEntry (
	$entry, // event details array
	$section_id
) {
//
//  Return: nothing
//
//######################################################################
global $CALTEXT;
global $page_id;
global $database, $admin, $wb;
global $day, $month, $year;
global $settings;
global $dwoo;

$monthstart = date ('n', $entry['date_start']);
$yearstart  = date ('Y', $entry['date_start']);

$eventsarr = array (
	'eventTitle' => $entry['event_title'],
	'eventDateStartTitle' => $CALTEXT['DATE_START'],
	'eventDateEndTitle' => $CALTEXT['DATE_END'],
	'eventDateStart' => ShowDate ($settings["dateformat"], $entry['date_start']),
	'eventDateEnd' => ShowDate ($settings["dateformat"], $entry['date_end']),
	'eventTimeStart' => ShowDate ($settings["timeformat"], $entry['date_start']),
	'eventTimeEnd' => ShowDate ($settings["timeformat"], $entry['date_end']),
	'eventTimestring' => $CALTEXT['TIMESTR'],
	'eventCategoryTitle' => $CALTEXT['CATEGORY'],
	'eventCategory' => ($entry['category'] > 0) ? $entry['category'] : 'NA',
	'eventImageURL' => ($entry['image'] != '') ? EVENTSCAL_IMAGE_URL.$entry['image'] : '',
	'eventOneliner' => $entry['oneliner'],
	'eventSummary' => $entry['summary'],
	'eventDescription' => (($entry['description']) != "") ? $entry['description'] : $CALTEXT['NO_DESCRIPTION'],
	'eventDroplet' => $entry['droplet'],
	'eventCategory' => $entry['category_name'],
	'eventColor' => ($entry['use_category_color']) ? $entry['category_color'] : 'transparent',
	'prevEventLinkTitle' => $CALTEXT['PREV_EVENT_LINK'],
	'prevEventLink' => $entry ['prevEvent'],
	'nextEventLinkTitle' => $CALTEXT['NEXT_EVENT_LINK'],
	'nextEventLink' => $entry ['nextEvent'],
	'eventListLinkTitle' => $CALTEXT['EVENT_LIST_LINK'],
	'eventListLink' => '?page_id='.$page_id.'&amp;month='.$month.'&amp;year='.$year.'&amp;list'
	);

// Add custom fields
$eventsarr ['custom1'] = (($settings ['usecustom1'] <> 0 && $entry['custom1'] <> '' )) ? $entry['custom1'] : false;
$eventsarr ['custom2'] = (($settings ['usecustom2'] <> 0 && $entry['custom2'] <> '' )) ? $entry['custom2'] : false;
$eventsarr ['custom3'] = (($settings ['usecustom3'] <> 0 && $entry['custom3'] <> '' )) ? $entry['custom3'] : false;

// populate template
$data = $dwoo -> get (EVENTSCAL_TEMPLATE_PATH.'event_entry.tpl', $eventsarr);
// Make sure wblinks and droplets are executed;
$wb->preprocess($data);
echo $data;
}


//######################################################################
// Fetch all pages current user is allowed to see

function parent_list (
	$parent,
	$templ,
	$current
	) {
//
// $parent = parent_id, start with 0
// $templ, html:->where to put page_id and page_name, uses str_replace
// $current, current from db
// 
// returns = $content, html string with all pages and page_ids
// 
//######################################################################
global $admin, $database, $content;

$query = "SELECT * FROM ".TABLE_PREFIX."pages WHERE parent = '$parent' AND visibility!='deleted' ORDER BY position ASC";
$get_pages = $database->query($query);
while ($page = $get_pages->fetchRow()) {
	if ($admin->page_is_visible($page)==false)
		continue;
		// Get user perms
		$admin_groups = explode(',', str_replace('_', '', $page['admin_groups']));
		$admin_users = explode(',', str_replace('_', '', $page['admin_users']));
		$in_group = FALSE;
		foreach($admin->get_groups_id() as $cur_gid) {
			if (in_array($cur_gid, $admin_groups)) {
				$in_group = TRUE;
			}
		}
		// Title -'s prefix
		$title_prefix = '';
		for($i = 1; $i <= $page['level']; $i++) { $title_prefix .= ' - '; }
		$select_content = '';
		if ($current == $page['page_id']) { $select_content = ' selected';  }
		$content .= str_replace(array('[PAGE_ID]','[PAGE_TITLE]', '[SELECTED]'), array($page['page_id'], $title_prefix.$page['page_title'], $select_content),$templ);
		parent_list($page['page_id'],$templ, $current);
}
return $content;
}


//######################################################################
// Function added by PCWacht
// Allow user to select a page link
function select_pagelink (
	$title,
	$name,
	$wbid,
	$text)
{
// 
// returns = nothing
// 
//######################################################################
//global $tmp;
$start = '<div class="details_section">';
$start .= '<label for="'.$name.'">'.$title.'</label>';
$start .= '<select name="'.$name.'" id="'.$name.'" class="inputbox" size="1" style="width:410px;">';
$start .= '<option value="">'.$text.'</option>';
$templ = '<option value="[PAGE_ID]" [SELECTED]>[PAGE_TITLE]</option>';
$end = '</select>';
$end .= '</div>';

echo $start.parent_list(0,$templ,$wbid).$end;
}


//######################################################################
// Function added by PCWacht
// Allow user to select an image
function select_image (
	$label1,
	$label2,
	$image
) {
// 
// returns = nothing
// 
//######################################################################
global $CALTEXT;

function loopImageDir ($path, $level = 0) {
// recursively loops through given image directory
// visual grouping is done by adding left margins, the order of the files, however, remains PHP-like :-/
	global $image;
	$return = '';
	$margin = 0.8 * $level;
	
	if ($handle = opendir($path)) {
		while (false !== ($file = readdir($handle))) {
			if ($file != "." && $file != ".." && $file != "index.php") {
				if (is_dir ($path.'/'.$file) === true) {
					$return .= '<optgroup style="margin-left:'.$margin.'em;" label="'.$file.'">&nbsp;</optgroup>'."\n";
					$return .= loopImageDir ($path.'/'.$file, $level + 1);
				} else {
					$selected = ($image == $file) ? ' selected="selected"' : '';
					$return .= '<option value="'.$file.'"'.$selected.' style="margin-left:'.$margin.'em;">'.$file.'</option>'."\n";
				}
			}
		}
		closedir($handle);
	}
	return $return;
}

$output = '<div class="details_section">'."\n";
$output .= '<label for="'.$label1.'">'.$CALTEXT['CUSTOM_UPLOAD_IMG'].'</label>'."\n";
$output .= '<input name="'.$label1.'" id="'.$label1.'" type="file" style="width:410px;" />'."\n";
$output .= '</div>'."\n";
$output .= '<div class="details_section">'."\n";
$output .= '<label for="'.$label2.'">'.$CALTEXT['CUSTOM_SELECT_IMG'].'</label>'."\n";
$output .= '<select name="'.$label2.'" id="'.$label2.'" size="1" style="width:410px;">'."\n";
$output .= '<option value="0" >'.$CALTEXT['CUSTOM_SELECT_IMG_1ST_OPTION'].'</option>'."\n";
$output .= loopImageDir (EVENTSCAL_IMAGE_PATH);
$output .= '</select>'."\n";
$output .= '</div>'."\n";

echo $output;
}


//#############################################################################
// Only used in backend!
function ShowEventListEditor
(
	$events,
	$day = NULL,
	$page_id = NULL
) {
// Funktion ist darauf ausgelegt, stumpf die Eventliste auszugeben - im Array stehen
// ohnehin nur die Events des jeweiligen Monats.
// Ein Tag wird deshalb auch meist nicht mit übergeben, sondern nur, wenn im Kalender
// ein Tag ausgewählt wurde. In diesem Fall liefert 'MarkDayOK' auch nur die Events
// des gewählten Tages.
//#############################################################################
global $database;
global $section_id;
global $categories,$monthnames;
global $month, $year;
global $CALTEXT, $private;

$HeaderText = $monthnames[$month].' '.$year;

$sizeofEvents = sizeof($events);

if ($sizeofEvents > 0) {
	?>
	<div class="event_list">
	
	<form name="eventlisteditor" action="<?php echo WB_URL; ?>/modules/eventscalendar/save.php" method="post">
	<input type="hidden" name="page_id" value="<?php echo $page_id; ?>" />
	<input type="hidden" name="section_id" value="<?php echo $section_id; ?>" />
	
	<table class="event_list_table">
	<caption><?php echo $HeaderText; ?></caption>
	<thead>
		<tr>
		<th><?php echo $CALTEXT['DATE']; ?></th>
		<th><?php echo $CALTEXT['NAME']; ?></th>
		<th><?php echo $CALTEXT['OWNER']; ?></th>
		<th><?php echo $CALTEXT['CATEGORY']; ?></th>
		<th><?php echo $CALTEXT['ACTION']; ?></th>
		</tr>
	</thead>
	<tbody>
	<?php
	
	$sql = "SELECT user_id, username  FROM ".TABLE_PREFIX."users";
	$db = $database->query($sql);
	//$users[0] = $CALTEXT['OWNER_PUBLIC'];
	while ($rec = $db->fetchRow()) {
		$users[$rec['user_id']] = $rec['username'];
	}
		
	for ($i = 0; $i < $sizeofEvents; $i++) {
		$tmp = $events[$i];
		
		$yearstart	= date('Y', $tmp['date_start']);
		$monthstart	= date('n', $tmp['date_start']);
		$daystart	= date('j', $tmp['date_start']);
		
		$yearend	= date('Y', $tmp['date_end']);
		$monthend	= date('n', $tmp['date_end']);
		$dayend	= date('j', $tmp['date_end']);

		if (($day == NULL) || MarkDayOK($day, $month, $year, $events[$i])) {
			$link = 'modify.php?month='.$monthstart.'&amp;year='.$yearstart.'&amp;day='.$daystart.'&amp;id='.$tmp['id'].'&amp;edit=edit';		
			if (isset($page_id)) {
				$link .= "&amp;page_id=$page_id";
			}
			?>
		<tr>
		
		<td>
		<?php
		echo date ('Y-m-d', $tmp['date_start']);
		if ($yearstart.$monthstart.$daystart != $yearend.$monthend.$dayend) { //only show end date if event has multiple days
			echo ' '.$CALTEXT['DATE_DIVIDER'].' '.date ('Y-m-d', $tmp['date_end']);
		}
		?>
		</td>
		
		<td>
		<?php
		echo '<a href="'.$link.'&amp;id='.$tmp["id"].'">'.$tmp['event_title'].'</a>';
		?>
		</td>
		
		<td>
		<?php
		$icon = ($tmp['private']) ? 'private' : 'visible';
		$owner = (isset($users[$tmp['owner']])) ? $users[$tmp['owner']] : 'N/A';
		echo '<img src="'.THEME_URL.'/images/'.$icon.'_16.png" alt="'.$private[$tmp['private']].'" title="'.$private[$tmp['private']].'" />&nbsp;'.$owner;	
		?>
		</td>
		
		<td><?php
		if (($tmp['category'] != 0) && (array_key_exists($tmp['category'], $categories))) {
			//&& ($categories[$tmp['category']] != null)) {
			echo $categories[$tmp['category']];	
		}
		?>
		</td>
		
		<td>
		<?php
		echo '<button class="delete float_right" type="submit" name="delete" value="'.$tmp["id"].'" />'.$CALTEXT['BTN_DELETE'].'</button>';
		?>
		</td>
		</tr>
		<?php
		}
	}
	?>
	</tbody>
	</table>
	</form>
	<?php
	if ($day != NULL) echo '<button onclick="window.location=\''.ADMIN_URL.'/pages/modify.php?page_id='.$page_id.'&amp;month='.$month.'&amp;year='.$year.'\'">'.$CALTEXT["CALENDAR-BACK-MONTH"].'</button>';
	?>
	</div>
	
	<?php
} else echo $CALTEXT['NODATES'];
}


//#############################################################################
// this function is used in modify.php for adding new events and changing details of older events
function ShowEventEditor
(
	$events, // event array
	$day, 
	//$show=0,
	$editMode,
	$month, 
	$year,
	$edit_id
) {
//
//#############################################################################
global $categories, $private;
global $page_id;
global $admin;
global $CALTEXT;
global $section_id;
global $database;
global $settings;

// Need to invert the firstday for calendar
$jscal_firstday	= 1 - $settings["startday"];
//$dateformat		= $settings["dateformat"];
$usecustom1		= $settings["usecustom1"];
$custom1		= $settings["custom1"];
$usecustom2		= $settings["usecustom2"];
$custom2		= $settings["custom2"];
$usecustom3		= $settings["usecustom3"];
$custom3		= $settings["custom3"];

// Fill temporary array with event values
if ($editMode == "new" || $editMode == "no") {
	
	// in modify.php $day is created with NULL when not in POST values
	// but we want the current day
	if ($day == 0) $day = date ('j');
	
	$tmp['event_title'] = $CALTEXT['CALENDAR-DEFAULT-TEXT'];
	$tmp['date_start'] = mktime(0, 0, 0, $month, $day, $year);
	$tmp['date_end'] = mktime(0, 0, 0, $month, $day, $year);
	$tmp['description'] = "";
	$tmp['oneliner'] = "";
	$tmp['summary'] = "";
	$tmp['image'] = "";
	$tmp['droplet'] = "";
	$tmp['category'] = 0;
	$tmp['custom1'] = "";
	$tmp['custom2'] = "";
	$tmp['custom3'] = "";
	$tmp['private'] = 0;	
	$tmp['owner'] = $admin->get_user_id();
	$tmp['id'] = 0;

} else if ($editMode == "edit") {
	$tmp = returnEventByID ($events, $edit_id, $section_id);
}

$event_id = $tmp['id'];
$owner = $tmp['owner'];

?>
   
<div id="event_entry">
	<form name="eventeditor" action="<?php echo WB_URL; ?>/modules/eventscalendar/save.php" method="post" enctype="multipart/form-data">
	<input type="hidden" name="event_id" value="<?php echo $event_id; ?>" />
	<input type="hidden" name="page_id" value="<?php echo $page_id; ?>" />
	<input type="hidden" name="section_id" value="<?php echo $section_id; ?>" />
	<input type="hidden" name="owner" value="<?php echo $owner; ?>" />
	
	<?php
	$button_row  = '<div class="buttonrow">';
	$button_row .= '<input type="button" value="'.$CALTEXT['BTN_SETTINGS'].'" class="edit_button float_right" onclick="window.location=\''.WB_URL.'/modules/eventscalendar/modify_settings.php?page_id='.$page_id.'&amp;section_id='.$section_id.'\'" />';
	$url = ADMIN_URL."/pages/modify.php?page_id=$page_id&amp;edit=new";
	if ($editMode == "new" ||$editMode == "edit") {
		$button_row .= '<input  type="submit" value="'.$CALTEXT['BTN_SAVE'].'" />';
		if ($editMode == "edit") {
			$button_row .= '<input name="saveasnew" type="submit" value="'.$CALTEXT['BTN_SAVE-AS-NEW'].'" />';
			$button_row .= '<input type="submit" class="delete" name="delete" value="'.$CALTEXT['BTN_DELETE'].'" />';
		}
	}
	$button_row .= '<input  type="button" value="'.$CALTEXT['BTN_NEW-EVENT'].'" onclick="window.location=\''.$url.'\'" />';
	$button_row .= '</div>';
	
	echo $button_row;
	
	if (($editMode == "new") || ($editMode == "edit")) {
		
		// EVENT TITLE
		?>
		<div class="details_section">
		<label for="event_title"><?php echo $CALTEXT['NAME']; ?></label>
		<input class="edit_field" name="event_title" id="event_title" type="text" value="<?php if ($tmp) { echo $tmp['event_title'];}else {echo $CALTEXT['CALENDAR-DEFAULT-TEXT'];} ?>" />
		</div>
		<?php
		
		// DATE AND TIME
		function CreateTimeSelects ( // create selects for hours and minutes
			$time, // the unixtime
			$which // 'start' time or 'end' time
		) {
			if (date ('H:i', $time) != '00:00') { // is time given?
				$hour_select = date ('H', $time);
				$minute_select = date ('i', $time);
			} else { // use current time
				$hour_select = date ('H');
				$minute_select = floor((int)date('i') / 15) * 15;
			}
			
			$select_hrs = '';
			for ($i = 0; $i < 24; $i++) {
				($i < 10) ? $hour = '0'.$i : $hour = $i;
				($hour == $hour_select) ? $option_select = ' selected="selected"' : $option_select = '';
				
				$select_hrs .= '<option value="'.$hour.'"'.$option_select.'>'.$hour.'</option>';
			}
			$select_hrs .= '</select>';
			$select_mins = '';
			for ($i = 0; $i < 4; $i++) {
				($i == 0) ? $minute = '0'.$i : $minute = $i * 15;
				($minute == $minute_select) ? $option_select = ' selected="selected"' : $option_select = '';
				$select_mins .= '<option value="'.$minute.'"'.$option_select.'>'.$minute.'</option>';
			}
			$select_mins .= '</select>';
			
			return '  <select name="'.$which.'time_hrs">'.$select_hrs.':<select name="'.$which.'time_mins">'.$select_mins;
		}
		// START DATE + TIME
		?>
		<div class="details_section">
		<label for="date1"><?php echo $CALTEXT['DATE_START']; ?></label>
		<input name="date1" id="date1" class="date-pick" value="<?php echo date('Y-m-d', $tmp['date_start']); ?>"/>
		<?php
		echo CreateTimeSelects ($tmp['date_start'], 'start');
		?>
		</div>
		
		<?php
		// END DATE + TIME
		?>
		<div class="details_section">
		<label for="date2"><?php echo $CALTEXT['DATE_END']; ?></label>
		<input name="date2" id="date2" class="date-pick" value="<?php echo date('Y-m-d', $tmp['date_end']); ?>"/>
		<?php
		echo CreateTimeSelects ($tmp['date_end'], 'end');
		?>
		</div>
		
		<?php
		// MAIN EDITOR
		?>
		<div>
		<label for="description" class="nofloat"><?php echo $CALTEXT['DESCRIPTION']; ?></label>
		<?php
		show_wysiwyg_editor("description", "description", $tmp['description'], "99%", "400px");
		?>
		</div>
		<?php
		
		// ONELINER
		?>
		<div class="details_section">
		<label for="oneliner"><?php echo $CALTEXT['ONELINER']; ?></label>
		<input type="text" name="oneliner" id="oneliner" class="edit_field" value="<?php if ($tmp) {echo $tmp['oneliner'];} ?>" />
		</div>
		
		<?php
		// SUMMARY
		?>
		<div class="field_link">
			<label for="summary"><?php echo $CALTEXT['SUMMARY']; ?></label>
			<div class="field_area" >
			<textarea name="summary" id="summary" rows="4" cols="1" class="edit_field"><?php echo $tmp['summary']; ?></textarea>
			</div>
	        </div>
	        <?php
	        
	        // IMAGE
	        select_image ('image_upload', 'image_select', $tmp['image']);
	        
	        // DROPLET
	        ?>
	        <div class="details_section">
	        <label for="droplet"><?php echo $CALTEXT['DROPLET']; ?></label>
	        <input type="text" name="droplet" id="droplet" class="edit_field" value="<?php if ($tmp) {echo $tmp['droplet'];} ?>" />
	        </div>
	        
	        <?php
	        // 3 Custom fields; I can't think of a better solution right now, so I leave it to that
	        for ($i = 1; $i <= 3; $i++) {
	        	switch (${"usecustom".$i}) {
	        	case 1:
	        		?>
	        		<div class="details_section">
	        		<label><?php echo ${"custom".$i}; ?></label>
	        		<input type="text" name="custom<?php echo $i; ?>" class="edit_field" value="<?php if ($tmp) {echo $tmp['custom'.$i];} ?>" />
	        		</div>
	        		<?php
	        		break;
	        	case 2:
	        		?>
	        		<div class="field_link" >
	        		<label><?php echo ${"custom".$i}; ?></label>
				<div class="field_area" >
				<textarea name="custom<?php echo $i; ?>" rows="4" cols="1" class="edit_field"><?php echo $tmp['custom'.$i]; ?></textarea>
				</div>
				</div>
				<?php
				break;
			case 3:
				select_pagelink(${"custom".$i}, 'custom'.$i, $tmp['custom'.$i], $CALTEXT['CUSTOM_SELECT_PAGELINK']);
				break;
			case 4:
				select_image ('custom_upload'.$i, 'custom_image'.$i, $tmp['custom'.$i]);
				break;
			}
		}
		?>
		
		<div class="details_section">
		<label><?php echo $CALTEXT['CATEGORY']; ?></label>
		<select name="category" class="edit_select">
		<option value="0"><?php echo $CALTEXT['NON-SPECIFIED']; ?></option>
		<?php
		while (list($key,$value) = each($categories)) {
			echo "<option value='$key'";
			if ($tmp['category'] == $key) {
				echo ' selected="selected"';
			}
			echo ">$value</option>";
		}
		?>
		</select>
		</div>
		<div class="details_section">
		<label><?php echo $CALTEXT['VISIBLE']; ?></label>
		<select name="private" class="edit_select">
		<?php
		while (list($key,$value) = each($private)) {
			echo "<option value='$key'";
			if ($tmp['private'] == $key) {
				echo ' selected="selected"';
			}
			echo ">$value</option>";
		}
		?>
		</select>
		</div>
	</div>
	
	<?php
	echo $button_row;
	}
	?>
</form>

<script type="text/javascript" charset="utf-8">
// Adding variables for datepicker - sent to backend_body.js:
// Only place where Y-m-d is used, calendar needs it!
var MODULE_URL	= '<?php echo WB_URL; ?>/modules/eventscalendar';
var firstDay = <?php echo $jscal_firstday; ?>; // Firstday, 0=sunday/1=monday
var format = 'yyyy-mm-dd'; // International format in backend    
var datestart = '<?php echo date('Y-m-d', $tmp['date_start']); ?>'; // start date in input field
var dateend = '<?php echo date('Y-m-d', $tmp['date_end']); ?>'; // end date in input field
var datefrom = '<?php echo date('Y-m-d', mktime(0, 0, 0, date('m'), date('d'), date('Y') - 1)); ?>';  // How long back?
<?php // Set language file, if it exists
	$jscal_lang = defined('LANGUAGE') ? strtolower(LANGUAGE) : 'en';
	$jscal_lang = $jscal_lang != '' ? $jscal_lang : 'en';
	
	if(file_exists(WB_PATH."/modules/eventscalendar/js/lang/date_".$jscal_lang.".js")) {
		echo 'var datelang = "date_'.$jscal_lang.'.js"';
	} else {
		echo 'var datelang = "none"';
	}
?>
</script>
<script type="text/javascript" src="<?php echo WB_URL; ?>/modules/eventscalendar/js/jquery-insert.js"></script>
</div> 

<?php
// End of functions.php
}
?>