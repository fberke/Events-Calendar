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

if (LANGUAGE_LOADED) {        // load languagepack
  if(file_exists(WB_PATH."/modules/eventscalendar/languages/".LANGUAGE.".php")) {    // if exist proper language mutation
    require_once(WB_PATH."/modules/eventscalendar/languages/".LANGUAGE.".php");    // load it
  } else {
    require_once(WB_PATH."/modules/eventscalendar/languages/EN.php");        // else use english
  }
}

?>

<div style="width:60%;">
    <h2><?php echo $CALTEXT['SUPPORT_INFO']; ?></h2>
    <h3>&nbsp;</h3>
    <h3>Options</h3>
    <p>The basic options you can set for an event are:
<ul>
        <li><strong>Start date:</strong> the day the event starts. This may be the only day, if it's a one-day event.</li>
        <li><strong>Name:</strong> the name or title of the event.</li>
        <li><strong>Category:</strong> the category or type event, for example workshop, training, meeting, conference. You can add unlimited categories in the Settings. After that, you can select the category of your choice when entering the data for an event.</li>
        <li><strong>Visibility:</strong> you can set the event to a public or a private status. When set to public, every site visitor can view the event. When set to private, only logged in visitors can view it.</li>
        <li><strong>Description:</strong> this is the description of the event, created in a full WYSIWYG editor field.</li>
    </ul>
    <p><strong>Event start date &amp; end date<br />
    </strong>The options offer the possibility to use either a start date, or use both a start date and an end date. If you will have only one-day events, obviously there's no need to use end dates. If you have  events that span multiple days, you can set the option to use both start and end dates. </p>
  <p>If you set the option for both start and end dates, you can still use only the start date field if you like. If the end date field is left empty in the backend, the field title won't be visible on the website. It's best to use the date picker for entering a date, otherwise you may enter an end date that's earlier then the start date. Using the date picker will prevent that.</p>
  <p><strong>Event times<br />
  </strong>You may choose to add times to your start and end dates. If you choose to use times, time fields will be available when entering a new event. However, you are allowed to leave them empty if you like. </p>
  <p>Leaving  a time field empty or setting it to 00:00 will prevent it from being visible on the website. So you can set the option to Use Times, but not eventually using the time fields when entering an event, if you like.</p>
    <hr />
<h3>Custom Fields</h3>
    <p>You can add up to 9 extra input fields to the calendar settings. They are called custom fields and appear as extra input fields when entering the information for a new event. The input from the backend is shown on the frontend, in the context you set in the custom field template. There are several types of custom fields:</p>
    <ul>
      <li><strong>Text field:</strong> a single line of information, typically used for a few words or a single sentence.</li>
      <li><strong>Text area:</strong> multiple lines of information,typically used for small texts consisting of several sentences.</li>
      <li><strong>WB link:</strong> a link to another WB-page on the same website.</li>
      <li><strong>Image:</strong> an image you can upload or refer to in the Media section. The image may be automatically resized to the maximum width or height you set in the first option on the Custom Fields page.</li>
    </ul>
    <p>You are free to use any of the 9 available custom fields by setting the type, and modifying the field template. Only custom fields that are &quot;switched on&quot; by setting the type, will be available when entering a new event. You can set the field name to anything you like, and change the field template to your liking. </p>
  <p>The default field templates are:</p>
  <p><strong>Text field</strong> / <strong>Text area</strong><br />
    <code>&lt;div class=&quot;field_line&quot;&gt;   <br />
    &nbsp;&nbsp;&nbsp;&lt;h4&gt;[CUSTOM_NAME]&lt;/div&gt;   <br />
&nbsp;&nbsp;&nbsp;[CUSTOM_CONTENT] <br />
&lt;/div&gt;</code></p>
  <p><strong>WB link</strong><br />
    <code>&lt;div class=&quot;field_line&quot;&gt;     <br />
    &nbsp;&nbsp;&nbsp;&lt;a href=&quot;[wblink[CUSTOM_CONTENT]]&quot;&gt;[CUSTOM_NAME]&lt;/a&gt; <br />
  &lt;/div&gt; </code></p>
  <p><strong>Image</strong><br />
    <code>&lt;div class=&quot;field_line&quot;&gt;<br />
    &nbsp;&nbsp;&nbsp;&lt;img src=&quot;[CUSTOM_CONTENT]&quot; border =&quot;0&quot; alt=&quot;[CUSTOM_NAME]&quot; /&gt; <br />
&lt;/div&gt;</code> </p>
<hr />
<h3>Template</h3>
<p>Todo: Explain new Dwoo templates here.</p>
<p><strong>Post  (=event detail page)</strong> <br />
  The post template field may contain text, HTML, droplets and the special ProCalendar tags: [EVENT_TITLE], [DATE_SIMPLE], [DATE_FULL], [CATEGORY], [CUSTOM1], [CUSTOM2], [CUSTOM3], [CUSTOM4], [CUSTOM5], [CUSTOM6], [CATEGORY], [CONTENT], and [BACK]. You are free to use or delete tags or move them around. </p>
<p>The difference between [DATE_SIMPLE] and [DATE_FULL] is that the simple version just shows the  date without HTML/CSS markup. The full version shows the date with complete markup, like:<br />
  <code>&lt;div class=&quot;field_line&quot;&gt; <br />
&nbsp;&nbsp;&nbsp;&lt;h4&gt;(the date name in your language):&lt;/div&gt; <br />
&nbsp;&nbsp;&nbsp;(the date output in your country format) <br />
&lt;/div&gt;</code></p>
<p>The default post template is:</p>
<p><code>&lt;div class=&quot;event_entry&quot;&gt;   <br />
  &nbsp;&nbsp;&nbsp;&lt;h2&gt;[EVENT_TITLE]&lt;/h2&gt;   <br />
  &nbsp;&nbsp;&nbsp;&lt;div class=&quot;info_block&quot;&gt;     <br />
  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[DATE_FULL] <br />
  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[CUSTOM1] <br />
  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[CUSTOM2] <br />
  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[CUSTOM3] <br />
  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[CUSTOM4] <br />
  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[CUSTOM5] <br />
  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[CUSTOM6] <br />
  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[CATEGORY] <br />
  &nbsp;&nbsp;&nbsp;&lt;/div&gt; <br />
  &nbsp;&nbsp;&nbsp;[CONTENT] <br />
  &lt;/div&gt; <br />
  [BACK] </code></p>
<p> <strong>The combination of the custom fields and the master template offers a very flexible and powerful system to modify the calendar setup and output exevently to your wishes!</strong></p>
<hr />
<h3>Edit CSS</h3>
<p>Like many other WB modules, ProCalendar gives you the opportunity to edit the stylesheets for both frontend and backend. Make sure the CSS-files have writing permissions, otherwise your changes won't be saved.</p>
</div>
<br />
<input type="button"  value="<?php echo $CALTEXT['BTN_BACK']; ?>" onclick="window.location = '<?php echo WB_URL."/modules/eventscalendar/modify_settings.php?page_id=$page_id&amp;section_id=$section_id"; ?>';" />
<?php
$admin->print_footer();
?>
