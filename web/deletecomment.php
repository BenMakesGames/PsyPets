<?php
$require_petload = "no";

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';

$idnum = (int)$_GET['idnum'];

$command = 'SELECT userid FROM psypets_profilecomments WHERE idnum=' . $idnum . ' LIMIT 1';
$comment = $database->FetchSingle($command, 'deletecomment.php?idnum=' . $idnum);

if($comment['userid'] == $user['idnum'])
{
  $command = 'DELETE FROM psypets_profilecomments WHERE idnum=' . $idnum . ' LIMIT 1';
  $database->FetchNone($command, 'deletecomment.php?idnum=' . $idnum);
}

if(array_key_exists('commentspage', $_GET))
  header('Location: ./viewcomments.php?resident=' . link_safe($user['display']));
else
  header('Location: ./residentprofile.php?resident=' . link_safe($user['display']) . '#comments');
?>
