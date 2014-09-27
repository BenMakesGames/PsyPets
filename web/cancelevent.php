<?php
// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/inventory.php';
require_once 'commons/formatting.php';
require_once 'commons/checkpet.php';
require_once 'commons/parklib.php';

$eventid = (int)$_GET['idnum'];

$refund = array();

$my_event = $database->FetchSingle('
  SELECT *
  FROM `monster_events`
  WHERE
    host=' . quote_smart($user['user']) . '
    AND idnum=' . $eventid . '
  LIMIT 1
');

if($my_event === false)
{
  header('Location: /park.php?msg=76');
  exit();
}

if($my_event['finished'] == 'no')
{
  delete_and_refund_event($my_event, false);

  header('location: /park.php?msg=110');
}
else
  header('Location: /park.php?msg=77');
?>
