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

//#############################################################################
function ShowDatesDebug
(
	$month, 
	$year, 
	$events
)
//
//#############################################################################
{
   $AnzTage = sizeof($events);
   
   // Loop through number of days with dates in given month
   for ($day=0; $day < $AnzTage; $day++)
    {
      if($AnzTage)
      {
         $Termin     = $events[$day];    
         $dayend     = substr($Termin['date_end'],-2);
         $monthend   = substr($Termin['date_end'],5,2);
         $yearend    = substr($Termin['date_end'],0,4);
         $daystart   = substr($Termin['date_start'],8,2);
         $monthstart = substr($Termin['date_start'],5,2);
         $yearstart  = substr($Termin['date_start'],0,4);
         
         echo "Date at $daystart.$monthstart.$yearstart - $dayend.$monthend.$yearend ";

         if(StartDateIsInPast("$year-$month-$day","$yearstart-$monthstart-$daystart") == 1)
           echo "--> Date is in past";
           
         echo "<br/>";           
    }
  }
}

//#############################################################################
function PrintArray
(
	$array
)
//
//#############################################################################
{
  while (list($key,$value) = each($array)) 
  {
    echo "$key: $value ";
  }
}

?>
