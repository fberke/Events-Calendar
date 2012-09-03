// MODULE_URL is set in modify (at the end of functions.php)

if (typeof MODULE_URL != 'undefined') {

  // Variables are set in functions.php
  // Include datepicker files:
  $.insert(MODULE_URL + '/js/date.js');        
  $.insert(MODULE_URL + '/js/jquery.datePicker.js'); 
  
  // This is only needed for ie 6 and smaller
  if ($.browser.msie && $.browser.version.substr(0,1)<7) { 
    $.insert(MODULE_URL + '/js/jquery.bgiframe.js');
  }
  // Insert language file if it is not none!
  if (datelang !== "none") {
	$.insert(MODULE_URL + '/js/lang/'+ datelang);
  }
  // firstday 0=sunday, 1=monday
  // Fomat = datefomat yyyy/mm/dd or dd mm yyy or ...
  Date.firstDayOfWeek = firstDay; 
  Date.format 		= format;

  // set up the calendars and make them work together, date from first will be minimum date for second
  $(document).ready(function()
  {
	$('.date-pick').datePicker({
		clickInput:false, 
		autoFocusNextInput: true, 
		startDate:datefrom}   // datefrom!
		);
		
	$('#date1').bind(
		// Update date2 when date changes, set starting date to date1
		'dpClosed',
		function(e, selectedDates)
		{
			var d = selectedDates[0];
			if (d) {
				d = new Date(d);
				$('#date2').dpSetStartDate(d.addDays(0).asString());
			}
		}
	).datestart;   // datestart!!
	
	$('#date2').datePicker().dpSetStartDate(datestart).dateend;   // dateend!!
  });
};

// load the ColorPicker for coloring categories
$.insert('js/mColorPicker/javascripts/mColorPicker.js');

$(document).ready(function () {
  $('.edit_field_short').bind('colorpicked', function (e,color) {
    $('input[name="category_color"]').attr('value', color);
  });
});
  