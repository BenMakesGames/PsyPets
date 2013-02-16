<?php
$require_petload = 'no';
$invisible = 'yes';
$AJAX = true;

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/itemlib.php';

$itemid = (int)$_POST['itemid'];
$ranking = (int)$_POST['ranking'];

$details = get_item_byid($itemid);

if($details === false || $details['custom'] == 'secret')
  echo 0;
else if($ranking == 0)
{
  $database->FetchNone('DELETE FROM psypets_profile_treasures WHERE userid=' . (int)$user['idnum'] . ' AND itemid=' . $itemid . ' LIMIT 1');
  echo 0;
}
else
{
  $database->FetchNone('UPDATE psypets_profile_treasures SET ranking=' . $ranking . ' WHERE userid=' . (int)$user['idnum'] . ' AND itemid=' . $itemid . ' LIMIT 1');

  if($database->AffectedRows() == 0)
    $database->FetchNone('INSERT INTO psypets_profile_treasures (userid, itemid, ranking) VALUES (' . (int)$user['idnum'] . ','. $itemid . ',' . $ranking . ')');

  echo $ranking;
}
?>