<?php
// confirm the session...
require_once "commons/dbconnect.php";
require_once "commons/rpgfunctions.php";
require_once "commons/sessions.php";
require_once 'commons/userlib.php';

if($user['admin']['manageaccounts'] != 'yes')
{
  header('Location: ./404.php');
  exit();
}

$residents = $database->FetchMultiple('
  SELECT idnum,display FROM monster_users
  WHERE display_normalized=\'\'
');

$updated = 0;

foreach($residents as $resident)
{
  $database->FetchNone('
    UPDATE monster_users
    SET display_normalized=' . quote_smart(normalized_display_name($resident['display'])) . '
    WHERE idnum=' . $resident['idnum'] . '
    LIMIT 1
  ');
  $updated++;
}

echo 'done!  ' . $updated . ' accounts updated.';
?>
