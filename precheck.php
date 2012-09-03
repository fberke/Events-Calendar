<?php

/**
 * Events Calendar
 *
 * @author Frank Berke <frank@fberke.de>
 * @link http://fberke.de
 * @copyright 2012 Frank Berke
 * @license http://www.gnu.org/licenses/gpl.html GNU Public License (GPL)
 */

// include class.secure.php to protect this file and the whole CMS!
if (defined('WB_PATH')) {
  if (defined('LEPTON_VERSION')) include (WB_PATH . '/framework/class.secure.php');
}
else {
  $oneback = "../";
  $root = $oneback;
  $level = 1;
  while (($level < 10) && (!file_exists($root . '/framework/class.secure.php'))) {
    $root .= $oneback;
    $level += 1;
  }
  if (file_exists($root . '/framework/class.secure.php')) {
    include ($root . '/framework/class.secure.php');
  }
  else {
    trigger_error(sprintf("[ <b>%s</b> ] Can't include class.secure.php!", $_SERVER['SCRIPT_NAME']), E_USER_ERROR);
  }
}
// end include class.secure.php

// Checking Requirements

$PRECHECK['PHP_VERSION'] = array(
  'VERSION' => '5.2.0',
  'OPERATOR' => '>='
);
if (!defined('LEPTON_VERSION') || version_compare(LEPTON_VERSION, '2.0.0.0', '<')) {
  $PRECHECK['WB_ADDONS'] = array(
    'dwoo' => array(
      'VERSION' => '0.11',
      'OPERATOR' => '>='
    )
  );
}

// auf UTF-8 pruefen
global $database;
$sql = "SELECT `value` FROM `" . TABLE_PREFIX . "settings` WHERE `name`='default_charset'";
$result = $database->query($sql);

// auf MySQL 5 pruefen
$sqlVersion = $database->get_one("SELECT VERSION()");

if ($result) {
  $data = $result->fetchRow(MYSQL_ASSOC);
  $PRECHECK['CUSTOM_CHECKS'] = array(
    'Default Charset' => array(
      'REQUIRED' => 'utf-8',
      'ACTUAL' => $data['value'],
      'STATUS' => ($data['value'] === 'utf-8')
    ),
    'MySQL VERSION' => array(
      'REQUIRED' => '>= 5.0.0',
      'ACTUAL' => $sqlVersion,
      'STATUS' => (version_compare($sqlVersion, '5.0.0') >= 0)
    )
  );
}

?>