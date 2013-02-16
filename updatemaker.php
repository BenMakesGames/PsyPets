<?php
require_once 'commons/dbconnect.php';
require_once 'commons/userlib.php';

$items = array();
$updated = 0;

ob_start();

echo '<html><head>';

$command = 'SELECT * FROM monster_inventory WHERE maker=\'psypets\' LIMIT 100';
$items = $database->FetchMultiple($command, 'updatemaker.php');

if(count($items) > 0)
  echo '<meta http-equiv="refresh" content="2" />';

echo '</head><body>';

foreach($items as $item)
{
  echo 'item #' . $item['idnum'] . ' (' . $item['maker'] . ')... ';

  $command = 'UPDATE monster_inventory SET maker=\'\' WHERE idnum=' . $item['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'updatemaker.php');
  echo 'updated!<br />';

  $updated++;
}

echo 'updated ' . $updated . ' items.<br />';

echo '</body></html>';

ob_flush();
?>
