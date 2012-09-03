<?php

// Include Dwoo Template Engine
if (!class_exists('Dwoo')) {
	require_once WB_PATH.'/modules/dwoo/include.php';
}
// set cache and compile path for the template engine
$cache_path = WB_PATH.'/temp/cache';
if (!file_exists($cache_path))
	mkdir($cache_path, 0755, true);
$compiled_path = WB_PATH.'/temp/compiled';
if (!file_exists($compiled_path))
	mkdir($compiled_path, 0755, true);
// init the template engine
global $dwoo;
if (!is_object($dwoo))
	$dwoo = new Dwoo($compiled_path, $cache_path);

// custom Dwoo plugin
function truncate_weekday (Dwoo $dwoo, $day, $reverse=false, $length=2) {
	return (!$reverse) ? substr ($day, 0, $length) : substr ($day, $length);
}

$dwoo->addPlugin('truncate_weekday', 'truncate_weekday');

?>
