<?php
// confirm the session...
require_once 'commons/dbconnect.php';

$command = 'SELECT a.idnum,a.costumed,c.itemname,c.itemtype FROM monster_pets AS a,monster_inventory AS b,monster_items AS c WHERE a.costumed=\'yes\' AND a.toolid>0 AND a.toolid=b.idnum AND b.itemname=c.itemname';
$pets = $database->FetchMultiple($command, 'fetching costumed pets');

echo '<h4>Costumed Pets with Equipment</h4>';

foreach($pets as $pet)
{
  echo 'pet #' . $pet['idnum'] . ' - ' . $pet['itemname'] . ' (' . $pet['itemtype'] . ')<br />';
  $pet_ids[] = $pet['idnum'];
}

$command = 'SELECT a.idnum FROM monster_pets WHERE a.costumed=\'yes\' AND a.toolid=0';
$pets = $database->FetchMultiple($command, 'fetching more-different costumed pets');

echo '<h4>Costumed Pets without Equipment</h4>';

foreach($pets as $pet)
{
  echo 'pet #' . $pet['idnum'] . '<br />';
}

//$command = 'SELECT a.idnum FROM `monster_pets` AS a LEFT JOIN monster_inventory AS b ON a.toolid=b.idnum WHERE a.costumed=\'yes\'';
$command = 'SELECT a.idnum FROM `monster_pets` AS a,monster_inventory AS b WHERE a.costumed=\'yes\' AND a.toolid=b.idnum';
$pets = $database->FetchMultiple($command, 'fetching trick or treating pets');

echo '<h4>Pets Picked By Code From trickortreat.php</h4>';

foreach($pets as $pet)
{
  if(in_array($pet['idnum'], $pet_ids))
    echo 'pet #' . $pet['idnum'] . '<br />';
  else
    echo 'pet #' . $pet['idnum'] . ' - not found by the first list!<br />';
}