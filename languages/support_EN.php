<?php

require('../../../config.php');

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

if (LANGUAGE_LOADED) {
	if (file_exists(WB_PATH."/modules/eventscalendar/languages/".LANGUAGE.".php")) {
		require_once(WB_PATH."/modules/eventscalendar/languages/".LANGUAGE.".php");
	} else {
		require_once(WB_PATH."/modules/eventscalendar/languages/EN.php");
	}
}

?>

<div id="helppage">
<h1><?php echo $CALTEXT['SUPPORT_INFO']; ?></h1>
<p>Events Calendar for WebsiteBaker and Lepton CMS is a feature richt, versatile and accessible event calender.
<br />It comes with a clear and coherent backend, and there isn't much needed to get you started quickly.</p>

<h2>Backend</h2>
<h3>Main page</h3>
<p>The first thing you see when entering the Events Calendar backend, is the calendar with a list of this
month's events below. The calendar is basically the same as in frontend, so it can be navigated the same way.
Clicking on a day without event, the 'New event' form will be opend with that day preselected. Clicking on a day
marked as carrying an event triggers the filter for the event list to day view.
<br />The event list provides the most important information about the events, and can be switched between month view
an day view (see above) - to go back to month view click on the corresponding button below the list.</p>
<p>By clicking the 'New event' button, a form opens below the event list where you can insert the event details.
Most options are self-explanatory, but some things are good to know, though.
<br />First, start and end date (and time) are always active. By default they are filled in with today's date
and the current time, unless you picked a date in the calendar. Date must be inserted in YYYY-MM-DD format, or can
alternatively be chosen by clicking the small icon between the date field and the time selects. This opens a JavaScript
based calendar where you can comfortably select your date(s).
<br />The time can be chosen in 15-minute-steps only - for most cases this is sufficient. Note, that in backend
time is shown in 24 hr format, but the frontend output can easily be configured.</p>
<p>Uploading an image instead of chosing an available one automatically adds it to this event. Alternatively, use
the media management of your CMS or any FTP client to upload images to <code>/media/eventscalendar</code>. Image
select supports subdirectories, so you can group images in folders to find them easier.</p>
<p>It is possible to use droplets, but they will only be executed in event details view. To insert a droplet use
the same syntax as you would elsewhere, that is, with the double brackets <code>[[Droplet?answer=42]]</code></p>

<h3>Settings</h3>
<p>The settings only consist of what is really necessary. Again, most things here are self-explanatory - just
a few things and you can get started :-)
<br />It is possible to hide previous events in the frontend. The admin of course sees them all, but if you don't
want your visitors to rummage in your past events, it's tuned off by a mouseclick.
<br />An important setting is the handling of Private Events. In the default setting, any user with an account
can see them, but it is also possible to hide them from anybody except the creator himself. A mixture of both
behaviours is not possible unless you use two calendars on your website.
<br />You can choose to resize uploaded images to fit into your calendar layout, or just leave the images untouched.
There are some predefined values given, but if none meets your requirements, you can enter one manually.
<p>As for categories, to insert a new one, just type its name into the input field and choose a colour for it,
then klick on the Save button - it's as simple as that. The category colour is not by default shown in the calendar,
instead you have to explicitly mark the corresponding checkbox for each category that you wish to see.
<p>With Custom Fields you can extend your calendar with up to three individual fields. By default they are turned
off, but if for example you fell like having one or more additional images, just activate them with the needed
setting. Hence they will show up every time you create a new event or edit an old one.</p>
<p>In the smaller right column of the settings page you can quickly edit the <code>frontend.css</code> file
with a built-in editor.
<br />The table with the category overview may become useful if you want to pick certain events by their category,
maybe in a droplet. With this list, you can easily see the category ID you have to use.</p> 


<h2>Templates</h2>
<p>One of the most advanced features of Events Calendar is the fact that its output is driven by <a href="http://dwoo.org/" target="_blank">Dwoo</a>, a
powerful template engine, which gives you all the flexibility you need in a demanding environment. If you
are not yet familiar with Dwoo's possibilities, I advise a visit to the <a href="http://wiki.dwoo.org/" target="_blank">Dwoo wiki</a>
or the  <a href="http://forum.dwoo.org/" target="_blank">Dwoo forum</a> for a start.
<br />As especially the calendar template is quite tricky to handle, I advise you to start changing a template with
a more simple structure, which is the event entry template. Also, have a look at the style sheets in <code>frontend.css</code>
where you can already change a lot about Events Calendar's appearance without need of touching the HTML.</p>
<p>There are currently three template files:</p>
<p><em>In some 'content' columns you'll find values starting with <code>$CALTEXT</code>. These are taken from
	the language files in <code>/eventscalendar/languages</code> and you can change them there, if you must.</em></p>
<ul>
	<li><h3>calendar.tpl</h3>
	<p>This one is the template for the actual calendar. It's table-based, but very pure and modern
	HTML as	styling is done through CSS. There is an alternative CSS file named <code>frontend_hoverup.css</code> which makes
	the event tooltips, that by default open downwards, pop up to the top. <em>As it's a pure HTML solution I advise its use
	only to HTML experts, because handling the table becomes a bit delicate then.</em></p>
	<p>Within the template many precautions have been taken to make the calendar accessible for the visually impaired.
	<br />You may notice that the table is to be divided into <code>&lt;caption&gt;</code>, <code>&lt;thead&gt;</code>,
	and <code>&lt;tbody&gt;</code>. In thead, weekday names are written as a whole, but by default, only the first two letters
	are visible. The same goes for tbody, where each weekday is supplied with the monthname as an additional, but invisible
	information.</p>
	<p>Naturally, the event tooltips show up in forms of a list, enclosed in only a single <code>&lt;div&gt;</code>.</p>
	<p>The calendar is not shown by default - to be more flexible putting it on your website it has been made a droplet.</p>
	<p>Note: 'calendar.tpl' is the only template which is both used in frontend (your website) and the CMS backend!</p>
	
	<h3>Available Variables</h3>
	<table>
	<thead>
	<tr>
		<th>Name</th>
		<th>Content</th>
	</tr>
	</thead>
	<tbody>
	<tr class="subheading">
		<td><code>caption</code></td>
		<td>Values for the table caption</td>
	</tr>
	<tr>
		<td><code>previousYearLink</code></td>
		<td>The URL to call the previous year</td>
	</tr>
	<tr>
		<td><code>previousYearLinkTitle</code></td>
		<td>Value: the previous year as a number, e.g. 2011</td>
	</tr>
	<tr>
		<td><code>previousMonthLink</code></td>
		<td>The URL to call the previous month</td>
	</tr>
	<tr>
		<td><code>previousMonthLinkTitle</code></td>
		<td>Value: the name of the previous month, e.g. October</td>
	</tr>
	<tr>
		<td><code>monthname</code></td>
		<td>Value: the name of the currently displayed month</td>
	</tr>
	<tr>
		<td><code>year</code></td>
		<td>Value: the currently displayed year as a number</td>
	</tr>
	<tr>
		<td><code>nextMonthLink</code></td>
		<td>The URL to call the next month</td>
	</tr>
	<tr>
		<td><code>nextMonthLinkTitle</code></td>
		<td>Value: the name of the next month, e.g. December</td>
	</tr>
	<tr>
		<td><code>nextYearLink</code></td>
		<td>The URL to call the next year</td>
	</tr>
	<tr>
		<td><code>nextYearLinkTitle</code></td>
		<td>Value: the next year as a number, e.g. 2013</td>
	</tr>
	<tr class="subheading">
		<td><code>thead (weekdays)</code></td>
		<td>Value: an array of weekdays, either starting with Monday or Sunday
	</tr>
	<tr>
		<td><code>weekdays</code></td>
		<td>In the template file, the function 'truncate_weekday()' is used to break up the
		weekday name as described above.
		<br /><strong>Usage of that function</strong>
		<ul>
		<li>1st parameter: day (string)</li>
		<li>2nd parameter: reverse (boolean)
		<br />default is false and returns the initial characters, true returns the rest</li>
		<li>3rd parameter: length (number)
		<br />length of the initial characters; default is 2, but any number can be used, most commonly 1 or 3</li>
		</ul>
		</td>
	</tr>
	<tr class="subheading">
		<td><code>rows</code></td>
		<td>Value: an array of 7 cells (= days)</td>
	</tr>
	<tr class="subheading">
		<td><code>cells</code></td>
		<td>Value: an array of multiple values, different for each calendar day</td>
	</tr>
	<tr>
		<td><code>dayType</code></td>
		<td>It can have 4 different values:
		<ul>
		<li>noday: empty day in calendar sheet
		<br />e.g. if 1st of month is a Wednesday, Monday and Tuesday are of type 'noday'</li>
		<li>event: day with event  
		<br />If this is the case, an array with event details is inserted; see below.</li>
		<li>eventBE: marks a day as event in backend mode</li>
		<li>normal: a day which is neither event nor noday</li>
		</ul>
		</td>
	</tr>
	<tr>
		<td><code>dayNr</code></td>
		<td>Number of day in month; if dayType is noday, value is '0' (zero)</td>
	</tr>
	<tr>
		<td><code>monthname</code></td>
		<td>Value: the name of the currently displayed month, applies to all daytypes, except 'noday'</td>
	</tr>
	<tr>
		<td><code>isToday</code></td>
		<td>Value: true or false, applies to all daytypes, except 'noday'</td>
	</tr>
	<tr>
		<td><code>eventListLink</code></td>
		<td>Value: URL to call the event list; applies to daytypes 'event' and 'eventBE'</td>
	</tr>
	<tr>
		<td><code>eventListHeading</code></td>
		<td>Value: Heading for the dropdown/popup event preview, applies to daytype event only
		<br />It is combined of <code>$CALTEXT['POPUP_HEADING']</code> and the date
		<br />Example output: &quot;Events on 2012/10/21&quot;</td>
	</tr>
	<tr class="subheading">
		<td><code>events</code></td>
		<td>Value: an array of events for the preview
		<br />Note that this array will only be created if the 'Event preview in calendar'
		setting is not 0. In this case 'events' will return 'false'.</td>
	</tr>
	<tr>
		<td><code>eventType</code></td>
		<td>Value: 'event' (array with event details follows), or 'link' (the number of this day's events
			exceeds the maximum event preview)</td>
	</tr>
	<tr>
		<td><code>eventDetailsLink</code></td>
		<td>Value: URL to open the deteails view of the event</td>
	</tr>
	<tr>
		<td><code>eventDetailsLinkTitle</code></td>
		<td>Value: <code>$CALTEXT['POPUP_LINK_TITLE']</code>; string used for the title tag in the
		'eventDetailsLink' anchor element</td>
	</tr>
	<tr>
		<td><code>eventTitle</code></td>
		<td>Value: string containing the title of the event</td>
	</tr>
	<tr>
		<td><code>eventOneliner</code></td>
		<td>Value: string containing the oneliner, which is a very brief description of the event</td>
	</tr>
	<tr>
		<td><code>eventCategory</code></td>
		<td>Value: string containing the category the event has been assiciated with</td>
	</tr>
	<tr>
		<td><code>eventColor</code></td>
		<td>Value: string containing the chosen category color as a number in hex format (e.g. #E3FF90)</td>
	</tr>
	<tr>
		<td><code>eventTime</code></td>
		<td>Value: the start time of the event</td>
	</tr>
	<tr>
		<td><code>eventTimestring</code></td>
		<td>Value: <code>$CALTEXT['TIMESTR']</code>; in some countries, the time notation requires
		(or is commonly used with) an addition, e.g. &quot;13:15&nbsp;Uhr&quot; in Germany</td>
	</tr>
	<tr>
		<td><code>eventListLink</code></td>
		<td>Value: URL to call the event list (only applies to 'eventType'='link')</td>
	</tr>
	<tr>
		<td><code>eventListLinkTitle</code></td>
		<td>Value: <code>$CALTEXT['POPUP_MORE_LINKTITLE']</code>; string used for the title tag in the
		'eventListLink' anchor element (only applies to 'eventType'='link')</td>
	</tr>
	<tr>
		<td><code>eventListLinkText</code></td>
		<td>Value: <code>$CALTEXT['POPUP_MORE_LINKTITLE']</code>; string used between the
		'eventListLink' anchor elements (only applies to 'eventType'='link')</td>
	</tr>
	<tbody>
	</table>
	
	</li>
	
	<li><h3>event_list.tpl</h3>
	<p>This is the template wich determines the ordered output of a list of events.
	<br />The HTML actually is a big, nested list, so accessibility is granted, as is easy styling through CSS.</p>
	<p>This list should be the standard output on the calendar page.
	<br />It shows brief information about each event, including start and end time, and an unobtrusive means of displaying
	the category color, if you chose to show it. Also, you now can navigate backwards and forwards through the months.</p>
	<p>Note: The event list is fixed to month view; other groupings (e.g. weekly) aren't yet implemented.</p>
	
	<h3>Available Variables</h3>
	<table>
	<thead>
	<tr>
		<th>Name</th>
		<th>Content</th>
	</tr>
	</thead>
	<tbody>
	<tr>
		<td><code>noDates</code></td>
		<td>Value: <code>$CALTEXT ['NODATES']</code>; message to display if current month has no events</td>
	</tr>
	<tr>
		<td><code>prevMonthLinkText</code></td>
		<td>Value: <code>$CALTEXT ['PREV_MONTH']</code>; text shown between the
		'prevMonthLink' anchor elements</td>
	</tr>
	<tr>
		<td><code>nextMonthLinkText</code></td>
		<td>Value: <code>$CALTEXT ['NEXT_MONTH']</code>; text shown between the
		'nextMonthLink' anchor elements</td>
	</tr>
	<tr>
		<td><code>prevMonthName</code></td>
		<td>Value: string; previous month's name</td>
	</tr>
	<tr>
		<td><code>nextMonthName</code></td>
		<td>Value: string; next month's name</td>
	</tr>
	<tr>
		<td><code>prevMonthLink</code></td>
		<td>Value: URL that leads to previous month view</td>
	</tr>
	<tr>
		<td><code>nextMonthLink</code></td>
		<td>Value: URL that leads to next month view</td>
	</tr>
	<tr>
		<td class="subheading"><code>events</code></td>
		<td>Array with sub-arrays for every day containing the actual events</td>
	</tr>
	<tr>
		<td><code>date</code></td>
		<td>Value: a date in your chosen date format</td>
	</tr>
	<tr>
		<td class="subheading"><code>entries</code></td>
		<td>Array with the event entries for that day</td>
	</tr>
	<tr>
		<td><code>eventDetailsLink</code></td>
		<td>Value: URL that leads to the details view of the event</td>
	</tr>
	<tr>
		<td><code>eventDetailsLinkTitle</code></td>
		<td>Value: <code>$CALTEXT['POPUP_LINK_TITLE']</code>; text shown between the
		'eventDetailsLink' anchor elements and used as the anchor's title text</td>
	</tr>
	<tr>
		<td><code>eventTitle</code></td>
		<td>Value: string; the title of the event</td>
	</tr>
	<tr>
		<td><code>eventOneliner</code></td>
		<td>Value: string; the oneliner is a very brief description of the event, just a few words</td>
	</tr>
	<tr>
		<td><code>eventSummary</code></td>
		<td>Value: string; a summary of the event (yes, you have to write that, too)</td>
	</tr>
	<tr>
		<td><code>eventDateStartTitle</code></td>
		<<td>Value: <code>$CALTEXT['DATE_START']</code>; label for the start date</td>
	</tr>
	<tr>
		<td><code>eventDateStart</code></td>
		<td>Value: the start date in your chosen date format</td>
	</tr>
	<tr>
		<td><code>eventTimeStart</code></td>
		<td>Value: the start time in your chosen time format</td>
	</tr>
	<tr>
		<td><code>eventDateEndTitle</code></td>
		<<td>Value: <code>$CALTEXT['DATE_END']</code>; label for the end date</td>
	</tr>
	<tr>
		<td><code>eventDateEnd</code></td>
		<td>Value: the end date in your chosen date format</td>
	</tr>
	<tr>
		<td><code>eventTimeEnd</code></td>
		<td>Value: the end time in your chosen time format</td>
	</tr>
	<tr>
		<td><code>eventTimestring</code></td>
		<td>Value: <code>$CALTEXT['TIMESTR']</code>; in some countries, the time notation requires
		(or is commonly used with) an addition, e.g. &quot;13:15&nbsp;Uhr&quot; in Germany</td>
	</tr>
	<tr>
		<td><code>eventCategoryTitle</code></td>
		<td>Value: <code>$CALTEXT['CATEGORY']</code>; label for the category name</td>
	</tr>
	<tr>
		<td><code>eventCategory</code></td>
		<td>Value: string; the name of the category this event is in</td>
	</tr>
	<tr>
		<td><code>eventColor</code></td>
		<td>Value: string containing the chosen category color as a number in hex format (e.g. #E3FF90)</td>
	</tr>
	<tbody>
	</table>
	
	</li>
	
	<li><h3>event_entry.tpl</h3>
	<p>This is the template to display the actual event with all its details.
	<br />The default layout is not very exciting and the most simple to adapt, as there are no loops in the template.</p>
	<p>Most of the placeholders are the same as in 'event_list.tpl' section 'entries', except for 'eventDetailsLink'
	and 'eventDetailsLinkTitle' of course. So I won't repeat them here.</p>
	
	<h3>Available Variables</h3>
	<table>
	<thead>
	<tr>
		<th>Name</th>
		<th>Content</th>
	</tr>
	</thead>
	<tbody>
	<tr>
		<td><code>eventImageURL</code></td>
		<td>Value: URL to show an image associated with this event.</td>
	</tr>
	<tr>
		<td><code>eventDescription</code></td>
		<td>Value: string; this is the full description of this event.
		<br />Due to the use of a rich text editor, is contains HTML tags as formattings.
		<br />If a description is missing, <code>$CALTEXT['NO_DESCRIPTION']</code> will be returned.</td>
	</tr>
	<tr>
		<td><code>eventDroplet</code></td>
		<td>Yes, you can add a droplet to your events.</td>
	</tr>
	<tr>
		<td><code>prevEventLinkTitle</code></td>
		<td>Value: string; <code>$CALTEXT['PREV_EVENT_LINK']</code> text shown between the
		'prevEventLink' anchor elements and/or used as the anchor's title text</td>
	</tr>
	<tr>
		<td><code>prevEventLink</code></td>
		<td>Value: URL that leads to previous event</td>
	</tr>
	<tr>
		<td><code>nextEventLinkTitle</code></td>
		<td>Value: string; <code>$CALTEXT['NEXT_EVENT_LINK']</code> text shown between the
		'nextEventLink' anchor elements and/or used as the anchor's title text</td>
	</tr>
	<tr>
		<td><code>nextEventLink</code></td>
		<td>Value: URL that leads to next event</td>
	</tr>
	<tr>
		<td><code>eventListLinkTitle</code></td>
		<td>Value: <code>$CALTEXT['EVENT_LIST_LINK']</code> text shown between the
		'eventListLink' anchor elements and/or used as the anchor's title text</td>
	</tr>
	<tr>
		<td><code>eventListLink</code></td>
		<td>Value: URL that leads back to the events list</td>
	</tr>
	<tr>
		<td><code>custom1 - custom3</code></td>
		<td>Three custom fields are available to extend the event contents to your requirements.
		While turned off in backend or empty, they are set to 'false'.</td>
	</tr>
	
	<tbody>
	</table>
	
	</li>
</ul>

</div>

<input type="button"  value="<?php echo $CALTEXT['BTN_BACK']; ?>" onclick="window.location = '<?php echo WB_URL."/modules/eventscalendar/modify_settings.php?page_id=$page_id&amp;section_id=$section_id"; ?>';" />
<?php
$admin->print_footer();
?>
