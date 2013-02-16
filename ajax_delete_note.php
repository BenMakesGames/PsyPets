<?php
$require_petload = 'no';
$invisible = 'yes';
$AJAX = true;

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';

$noteid = (int)$_POST['id'];

$command = 'DELETE FROM psypets_notes WHERE idnum=' . $noteid . ' AND userid=' . $user['idnum'] . ' LIMIT 1';
$database->FetchNone($command, 'deleting note');

if($database->AffectedRows() > 0)
  echo 'success';
else
  echo 'Could not delete note.  Reload the page and try again.';
?>
