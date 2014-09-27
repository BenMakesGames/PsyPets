<?php
// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';

$glid = (int)$_POST['id'];
$page = (int)$_GET['page'];

$command = "SELECT * FROM monster_graphicslibrary WHERE idnum=$glid LIMIT 1";
$graphic = $database->FetchSingle($command, 'fetching requested graphic');

if($graphic['uploader'] == $user['idnum'])
{
  $command = "DELETE FROM monster_graphicslibrary WHERE idnum=$glid LIMIT 1";
  $database->FetchNone($command, 'deleting graphic from graphic library');
  header('Location: ./gl_browse.php?page=' . $page . '&dialog=6');
}
else
  header('Location: ./gl_browse.php?page=' . $page . '&id=' . $glid);

exit();
?>
