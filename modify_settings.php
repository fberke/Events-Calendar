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


// Include WB admin wrapper script
require(WB_PATH.'/modules/admin.php');

// include core functions of WB 2.7 to edit the optional module CSS files (frontend.css, backend.css)
@include_once(WB_PATH .'/framework/module.functions.php');

@include_once(WB_PATH .'/modules/eventscalendar/functions.php');


if (LANGUAGE_LOADED) {        // load languagepack
  if(file_exists(WB_PATH."/modules/eventscalendar/languages/".LANGUAGE.".php")) {    // if exist proper language mutation
    require_once(WB_PATH."/modules/eventscalendar/languages/".LANGUAGE.".php");    // load it
  } else {
    require_once(WB_PATH."/modules/eventscalendar/languages/EN.php");        // else use english
  }
}
$fillvalue = "";

$group_id = 0;
if (isset($_GET['group_id']) && is_numeric($_GET['group_id']))  $group_id = $_GET['group_id'];

$settings = fillSettingsArray ($section_id);
$startday		= $settings["startday"];
$show_prev_dates 	= $settings["show_prev_dates"];
$show_private_dates 	= $settings["show_private_dates"];
$dateformat   		= $settings["dateformat"];
$resize		= $settings["resize"];
$maxprev		= $settings["maxprev"];

?>
<div class="container" style="overflow:hidden;">
<div class="leftcol" style="float:left;width:65%;">
	<form name="general_settings" method="post" action="<?php echo WB_URL; ?>/modules/eventscalendar/save_settings.php">
	<input type="hidden" name="page_id" value="<?php echo $page_id; ?>">
	<input type="hidden" name="section_id" value="<?php echo $section_id; ?>">
	<input type="hidden" name="type" value="general_settings">

	<h2><?php echo $CALTEXT['CAL-OPTIONS']; ?></h2>
		<div class="details_section">
		<label for="startday"><?php echo $CALTEXT['CAL-OPTIONS-STARTDAY'];?></label>
		<select class="edit_select_short" id="startday" name="startday" >
		<?php
		echo '<option value="0" '; // Monday (default)
		if ($startday == 0)
			echo ' selected="selected"';
		echo ">".$CALTEXT['CAL-OPTIONS-STARTDAY-0'].'</option>';
		echo '<option value="1" '; // Sunday
		if ($startday == 1)
			echo ' selected="selected"';
		echo ">".$CALTEXT['CAL-OPTIONS-STARTDAY-1'].'</option>';
		?>
		</select>
		</div>
		<div class="details_section">
		<?php
		// Create array
		$DATE_FORMATS = array();
		// Get the current time (in the users timezone if required)
		$actual_time = time();
		// Add values to list
		$DATE_FORMATS['d.m.Y'] = ShowDate('d.m.Y', $actual_time); // default
		$DATE_FORMATS['j.n.Y'] = ShowDate('j.n.Y', $actual_time);
		$DATE_FORMATS['m/d/Y'] = ShowDate('m/d/Y', $actual_time);
		$DATE_FORMATS['n/j/Y'] = ShowDate('n/j/Y', $actual_time);
		$DATE_FORMATS['Y-m-d'] = ShowDate('Y-m-d', $actual_time);
		$DATE_FORMATS['j.|F|Y'] = ShowDate('j. F Y', $actual_time);
		$DATE_FORMATS['j.|M|Y'] = ShowDate('j. M Y', $actual_time);
		?>
		
		<label for="dateformat"><?php echo $CALTEXT['CAL-OPTIONS-DATEFORMAT'];?></label>
		<select class="edit_select_short" id="dateformat" name="dateformat">
		<?php
		foreach($DATE_FORMATS AS $format => $title) {
			$format = str_replace('|', ' ', $format); // Adds whitespace (not able to be stored in array key)
			$selectval = ($format == $dateformat) ? ' selected="selected"' : '';
			echo '<option value="'.$format.'"'.$selectval.'>'.$title.' &ndash; ('.$format.')</option>';
		}
		?>
		</select>
		</div>
		<div class="details_section">
		<?php
		// Create array
		$TIME_FORMATS = array();
		// Get the current time (in the users timezone if required)
		$actual_time = time();
		// Add values to list
		$TIME_FORMATS['H:i'] = ShowDate('H:i', $actual_time); // default
		$TIME_FORMATS['G.i'] = ShowDate('G.i', $actual_time);
		$TIME_FORMATS['g:ia'] = ShowDate('g:ia', $actual_time);
		$TIME_FORMATS['g:i|A'] = ShowDate('g:i A', $actual_time);
		?>
		<label for="timeformat"><?php echo $CALTEXT['CAL-OPTIONS-TIMEFORMAT']; ?></label>
		<select class="edit_select_short" id="timeformat" name="timeformat">
            	<?php
            	foreach($TIME_FORMATS AS $format => $title) {
            		$format = str_replace('|', ' ', $format); // Adds whitespace (not able to be stored in array key)
            		$selectval = ($format == $dateformat) ? ' selected="selected"' : '';
            		echo '<option value="'.$format.'"'.$selectval.'>'.$title.' &ndash; ('.$format.')</option>';
	        }
	        ?>
	        </select>
	        </div>
		
	        <div class="details_section">
	        <label for="prevdates"><?php echo $CALTEXT['CAL-OPTIONS-PREVDATES'];?></label>
	        <select class="edit_select_short" id="prevdates" name="prevdates">
	        <?php
	        echo '<option value="1" '; // show previous dates
	        if ($show_prev_dates == 1)
	        	echo ' selected="selected"';
	        echo ">".$CALTEXT['CAL-OPTIONS-PREVDATES-1'].'</option>';
	        echo '<option value="0" '; // don't show (default)
	        if ($show_prev_dates == 0)
	        	echo ' selected="selected"';
	        echo ">".$CALTEXT['CAL-OPTIONS-PREVDATES-0'].'</option>';
	        ?>
	        </select>
	        </div>
		
	        
	        <div class="details_section">
	        <label for="private"><?php echo $CALTEXT['CAL-OPTIONS-PRIVATE'];?></label>
	        <select class="edit_select_short" id="private" name="private">
	        <?php
	        echo '<option value="1" '; // show private events to any logged-in user (default)
	        if ($show_private_dates == 1)
	        	echo ' selected="selected"';
	        echo ">".$CALTEXT['CAL-OPTIONS-PRIVATE-1'].'</option>';
	        echo '<option value="0" '; // show private events to creator only
	        if ($show_private_dates == 0)
	        	echo ' selected="selected"';
	        echo ">".$CALTEXT['CAL-OPTIONS-PRIVATE-0'].'</option>';
	        ?>
	        </select>
	        </div>
		
	        <div class="details_section">
	        <?php
	        $select_maxprev = '<select name="maxprev" id="maxprev">';
	        for ($i = 0; $i < 6; $i++) {
	        	($maxprev == $i) ? $option_select = ' selected="selected"' : $option_select = '';
	        	$select_maxprev .= '<option value="'.$i.'"'.$option_select.'>'.$i.'</option>';
	        }
	        $select_maxprev .= '</select>';
	        ?>
	        <label for="maxprev"><?php echo $CALTEXT['CAL-OPTIONS-MAXPREV']; ?></label>
	        <?php
	        echo $select_maxprev;
	        ?>
	        </div>
		<div class="details_section">
	        <?php
	        // Idea and code of the following JS gratefully taken from http://www.cs.tut.fi/~jkorpela/forms/combo.html
	        ?>
	        <script type="text/javascript" language="JavaScript"><!--
	        function activate(field) {
	        	if (document.styleSheets)
	        		field.style.display  = 'block';
	        	field.focus();
	        }
	        
	        function last_choice(selection) {
	        	return selection.selectedIndex==selection.length - 1;
	        }
	        
	        function process_choice(selection,textfield) {
	        	if (last_choice(selection)) {
	        		activate(textfield);
	        	} else {
	        		if (document.styleSheets)
	        			textfield.style.display  = 'none';
	        		textfield.value = '';
	        }}
	        
	        function valid(menu,txt) {
	        	if (menu.selectedIndex == 0) {
	        		alert('You must make a selection from the menu');
	        		return false;
	        	}
	        	
	        	if (txt.value == '') {
	        		if (last_choice(menu)) {
	        			alert('You need to type your choice into the text box');
	        			return false;
	        		} else {
	        			return true;
	        	}} else {
	        		if (!last_choice(menu)) {
	        			alert('Incompatible selection');
	        			return false;
	        		} else {
	        			return true;
	        }}}
	        
	        function check_choice() {
	        	if (!last_choice(document.general_settings.resize)) {
	        		document.general_settings.resize_other.blur();
	        		alert('Please check your menu selection first');
	        		document.general_settings.resize.focus();
	        }}
	        //--></script>
	        </div>
		<div class="details_section">
	        <label for="resize"><?php echo $CALTEXT['RESIZE_IMAGE_TO']; ?>:</label>
	        <div id="fields_wrappper" style="margin-left:170px;">
	        <select id="resize" name="resize" class="edit_select_short" onchange="process_choice(this,document.general_settings.resize_other)">
	        <option value=""><?php echo $CALTEXT['RESIZE_IMAGE_NONE']; ?></option>
	        <?php
	        $SIZES['100'] = 'Max. 100px';
	        $SIZES['125'] = 'Max. 125px';
	        $SIZES['150'] = 'Max. 150px';
	        $SIZES['175'] = 'Max. 175px';
	        $SIZES['200'] = 'Max. 200px';
	        $SIZES['225'] = 'Max. 225px';
	        $SIZES['250'] = 'Max. 250px';
	        foreach ($SIZES AS $size => $size_name) {
	        	//if ($resize == $size) { $selected = ' selected="selected"'; } else { $selected = ''; }
	        	$selected = ($resize == $size) ? ' selected="selected"' : '';
	        	echo '<option value="'.$size.'"'.$selected.'>'.$size_name.'</option>';
	        }
	        if ($resize != 0 && $selected == '') { // picture has a size not covered by the array
	        	echo '<option value="other" selected="selected">'.$CALTEXT['RESIZE_IMAGE_OTHER'].'</option>';
	        	$resize_other_value = ' value="'.$resize.'"';
	        } else {
	        	echo '<option value="other">'.$CALTEXT['RESIZE_IMAGE_OTHER'].'</option>';
	        	$resize_other_value = '';
	        }
	        ?>
	        </select>
	        
	        <noscript>
	        <?php
	        echo '<input type="text" id="resize_other" name="resize_other" maxlength="5"'.$resize_other_value.' class="edit_field_short" />';
	        ?>
	        </noscript>
	        <script type="text/javascript" language="JavaScript"><!--
	        otherval = '<?php echo $resize_other_value; ?>';
	        document.write('<input type="text" id="resize_other" name="resize_other" maxlength="5" class="edit_field_short"'+otherval+' onfocus="check_choice()" />');
	        if (document.styleSheets)
	        	document.general_settings.resize_other.style.display  = 'none';
	        -->
	        </script>
	        
	        </div>
	        </div>
		
	        <input  type="submit" value="<?php echo $CALTEXT['BTN_SAVE']; ?>">
	</form>

	<form name="manage_categories" method="post" action="<?php echo WB_URL; ?>/modules/eventscalendar/save_settings.php">
	<input type="hidden" name="page_id" value="<?php echo $page_id; ?>">
	<input type="hidden" name="section_id" value="<?php echo $section_id; ?>">
	<input type="hidden" name="type" value="manage_categories">
	
	<h2><?php echo $CALTEXT['CATEGORY_MANAGEMENT']; ?></h2>
	
	<div class="details_section">
	<label for="group_id"><?php echo $CALTEXT['CATEGORY_CHOOSE']; ?></label>
	<select class="edit_select_short" id="group_id" name="group_id" onchange="
		var groupID = '&group_id='+this.value;
		window.location='<?php echo WB_URL."/modules/eventscalendar/modify_settings.php?page_id=$page_id&amp;section_id=$section_id"?>'+groupID">
		<option value="0"><?php echo $CALTEXT['CATEGORY_SELECT']; ?></option>
		<?php
		$sql = "SELECT * FROM ".TABLE_PREFIX."mod_eventscalendar_categories WHERE section_id=$section_id ORDER BY category_name ASC ";
		$db = $database->query($sql);
		
		$dayChecked = '';
		if ($db->numRows() > 0) {
			while ($rec = $db->fetchRow()) {
				echo "<option value='".$rec["id"]."'";
				if (isset($group_id) AND ($group_id == $rec['id'])) {
					echo ' selected="selected"';
					$fillvalue = $rec['category_name'];
					$bghex = $rec['category_color'];
					($rec['use_category_color'] == 1) ? $dayChecked = 'checked="checked"' : $dayChecked = '';
				}
				echo ">".$rec['category_name']."</option>";
			}
		}
		?>
	</select>
	</div>
	<div class="details_section">
	<?php
	(isset ($bghex)) ? $bgStyle = 'style="background-color: '.$bghex.';"' : $bgStyle = '';
	?>
	<label for="group_name"><?php echo $CALTEXT['CATEGORY_CHANGEE_BGCOLOR']; ?></label>
	<input class="edit_field_short color" <?php echo $bgStyle; ?> data-hex="true" type="text" title="<?php echo $CALTEXT['CATEGORY_COLORCHOICE_HELP']; ?>" value="<?php echo $fillvalue; ?>" id="group_name" name="group_name">
	</div>
	<div class="details_section">
	<label for=""><?php echo $CALTEXT['CATEGORY_USE_BGCOLOR']; ?></label>
	<input type="checkbox" name="use_category_color" value="1" <?php echo $dayChecked; ?>>
	<input type="hidden" name="category_color" value="<?php if (isset ($bghex)) echo $bghex; ?>">
	</div>
	
	<input  type="submit" value="<?php echo $CALTEXT['BTN_SAVE']; ?>">
	<input class="delete" type="submit" name="delete" value="<?php echo $CALTEXT['BTN_DELETE']; ?>">
	</form>
	
	
	<h2><?php echo $CALTEXT['CUSTOM_FIELDS']; ?></h2>
	<?php
	
	$usecustom1	= $settings["usecustom1"];
	$custom1	= $settings["custom1"];
	$usecustom2	= $settings["usecustom2"];
	$custom2	= $settings["custom2"];
	$usecustom3	= $settings["usecustom3"];
	$custom3	= $settings["custom3"];
	
	$CTypes['0'] = $CALTEXT['CUSTOM_OPTIONS-0'];
	$CTypes['1'] = $CALTEXT['CUSTOM_OPTIONS-1'];
	$CTypes['2'] = $CALTEXT['CUSTOM_OPTIONS-2'];
	$CTypes['3'] = $CALTEXT['CUSTOM_OPTIONS-3'];
	$CTypes['4'] = $CALTEXT['CUSTOM_OPTIONS-4'];
	?>
	<form name="modify_customs" method="post" action="<?php echo WB_URL; ?>/modules/eventscalendar/save_settings.php">
	<input type="hidden" name="page_id" value="<?php echo $page_id; ?>">
	<input type="hidden" name="section_id" value="<?php echo $section_id; ?>">
	<input type="hidden" name="type" value="modify_customs">
	
	<?php
	for ($i = 1; $i <= 3; $i++) {
		
		echo '<div class="details_section">';
		echo '<label for="usecustom'.$i.'">'.$CALTEXT['CUSTOM_FIELDTYPE'].' '.$i.'</label>';
		echo '<select name="usecustom'.$i.'">';	
		foreach ($CTypes AS $type => $type_name) {
			//(${usecustom.$i} == $type) ? $selected = ' selected="selected"' : $selected = '';
			$selected = (${"usecustom".$i} == $type) ? ' selected="selected"' : '';
			echo '<option value="'.$type.'"'.$selected.'>'.$type_name.'</option>';
		}
		echo '</select>';
		echo '</div>';
		
		echo '<div class="details_section">';
		echo '<label for="usecustom'.$i.'">'.$CALTEXT['CUSTOM_FIELDNAME'].' '.$i.'</label>';
		$insert = (isset(${"custom".$i}) && (${"custom".$i} != "")) ? 'value="'.${"custom".$i}.'"' : 'placeholder="Insert field name"';
		echo '<input name="custom'.$i.'" class="edit_field_short" type="text" '.$insert.' />';
		echo '</div>';
	}
	?>

	<div>
		<input type="submit" value="<?php echo $CALTEXT['BTN_SAVE']; ?>">
	</div>
	</form>
</div>

<div class="rightcol" style="float:right;width:30%;">

        <h2><?php echo $CALTEXT['SUPPORT_INFO']; ?></h2>
        <?php echo $CALTEXT['SUPPORT_INFO_INTRO']; ?>
        <a href="<?php if (LANGUAGE_LOADED) {
        	if (file_exists(WB_PATH."/modules/eventscalendar/languages/support-".LANGUAGE.".php")) {
        		echo (WB_URL."/modules/eventscalendar/languages/support-".LANGUAGE.".php?page_id=$page_id&amp;section_id=$section_id");
        	} else {
        		echo (WB_URL."/modules/eventscalendar/languages/support-EN.php?page_id=$page_id&amp;section_id=$section_id");
        	}
	}
	?>">
	<?php echo $CALTEXT['SUPPORT_INFO']; ?></a>.
	
        <h2><?php echo $CALTEXT['ADVANCED_SETTINGS']; ?></h2>
        <?php 
        if (function_exists('edit_module_css')) {
        	edit_module_css('eventscalendar');
        }
        ?>
        
        <h2><?php echo $CALTEXT['CATEGORY_OVERVIEW']; ?></h2>
        <?php
        $categories = fillCategoryArray ($section_id);
        echo '<table>';
        echo '<thead>';
        echo '<tr>';
        echo '<th>';
        echo 'ID';
        echo '</th>';
        echo '<th>';
        echo $CALTEXT['CATEGORY'];
        echo '</th>';
        echo '</tr>';
        echo '</thead>';
        
        echo '<tbody>';
        while (list($key,$value) = each($categories)) {
        echo '<tr>';
        echo '<td>';
        echo $key;
        echo '</td>';
        echo '<td>';
        echo $value;
        echo '</td>';
        echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
        ?>

</div>

</div> <!-- wrapper -->

<script type="text/javascript" src="<?php echo WB_URL; ?>/modules/eventscalendar/js/jquery-insert.js"></script>

<input class="button back" type="button" value="<?php echo $CALTEXT['BTN_BACK']; ?>" onclick="window.location = '<?php echo ADMIN_URL; ?>/pages/modify.php?page_id=<?php echo $page_id; ?>';" />

<?php
$admin->print_footer();
?>
