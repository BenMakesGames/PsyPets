<?php
require_once 'commons/dbconnect.php';
require_once 'commons/itemlib.php';
require_once 'commons/userlib.php';

$now = time();

$command = 'SELECT itemname FROM monster_items WHERE durability=0 AND is_equipment=\'yes\'';
$equips = $database->FetchMultiple($command, 'fetching equipment with 0 durability');

echo '<ul>';

foreach($equips as $equip)
{
  $command = 'UPDATE monster_inventory SET health=0 WHERE itemname=' . quote_smart($equip['itemname']);
  $database->FetchNone($command, 'fixing equips with 0 durability');

  echo '<li>' . $equip['itemname'] . ': ' . $database->AffectedRows() . ' items fixed.</li>';
}

echo '</ul>';
?>
