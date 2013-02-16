<?php
$require_petload = 'no';
$invisible = 'yes';
$AJAX = true;

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/formatting.php';

$projectid = (int)$_POST['id'];
$room = trim($_POST['dest']);

if($room == '')
  $new_room = 'home';
else
  $new_room = 'home/' . $room;

$command = '
  UPDATE monster_projects
  SET destination=' . quote_smart($new_room) . '
  WHERE
    idnum=' . $projectid . '
    AND userid=' . $user['idnum'] . '
  LIMIT 1
';
$database->FetchNone($command, 'updating project destination');

if($database->AffectedRows() > 0)
  echo $room;
else
  echo 'error';
?>
