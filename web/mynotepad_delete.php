<?php
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/formatting.php';
require_once 'commons/notepadlib.php';

$noteid = (int)$_GET['id'];

$this_note = get_note_byid($noteid);

if($this_note === false || $this_note['userid'] != $user['idnum'])
{
  header('Location: ./mynotepad.php');
  exit();
}

delete_note_byid($noteid);

require_once 'commons/statlib.php';
record_stat($user['idnum'], 'Deleted a My Notepad Note', 1);

header('Location: ./mynotepad.php');
?>
