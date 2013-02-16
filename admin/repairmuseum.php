<?php
$_GET['maintenance'] = 'no';

require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

/*
   Returns to owner any items in the Museum which should not have been placed there >_>
   (in fairness, this is 99% of the time my fault for not properly marking an item
   as "limited", "custom", etc.)
*/

require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/museumlib.php';

$command = 'SELECT a.idnum FROM psypets_museum AS a LEFT JOIN monster_users AS b ON a.userid=b.idnum WHERE b.display IS NULL';
$rows = $database->FetchMultiple(($command, 'fetching idnums of items donated by deleted accounts');

if(count($rows) > 0)
{
  foreach($rows as $row)
    $ids[] = $row['idnum'];

  $command = 'DELETE FROM psypets_museum WHERE idnum IN (' . implode(',', $ids) . ') LIMIT ' . count($ids);
  $database->FetchNone(($command, 'deleting items');
  
  echo '<p>Deleted ', $database->AffectedRows(), ' orphaned museum items.</p>';
}
else
  echo '<p>No orphaned museum items to delete.  Neat-o.</p>';
  
$command = 'SELECT itemname,idnum FROM monster_items WHERE custom!=\'no\'';
$non_customs = $database->FetchMultiple(_by($command, 'idnum', 'fetching non-standard item info');

$idnum = array();
foreach($non_customs as $non_custom)
  $idnums[] = $non_custom['idnum'];

$command = 'SELECT itemid,userid FROM psypets_museum WHERE itemid IN (' . implode(',', $idnums) . ')';
$bad_museum_items = $database->FetchMultiple(($command, 'fetching non-standard items in the museum');

if(count($bad_museum_items) > 0)
{
  echo '<ul>';

  foreach($bad_museum_items as $bad_museum_item)
  {
    $user = get_user_byid($bad_museum_item['userid'], 'user');
  
    echo '<li>', $user['user'], ' needs to be refunded a ', $non_customs[$bad_museum_item['itemid']]['itemname'], '... ';

    $id = add_inventory($user['user'], '', $non_customs[$bad_museum_item['itemid']]['itemname'], 'Returned to you from the Museum', 'storage/incoming');

    echo $id, ' - done; deleting Museum record... ';

    $command = 'DELETE FROM psypets_museum WHERE userid=' . $bad_museum_item['userid'] . ' AND itemid=' . $bad_museum_item['itemid'] . ' LIMIT 1';
    $database->FetchNone(($command, 'deleted entry!'); 

    echo 'done!</li>';
  }

  echo '</ul><p>All done!</p>';
}
else
  echo '<p>No items need to be returned from the Museum.  Cooooooo\'...</p>';

?>
