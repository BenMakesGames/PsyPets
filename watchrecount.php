<?php
// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';

if(!is_numeric($_GET['plazaid']))
{
  header('Location: /plaza.php');
  exit();
}

$plazainfo = $database->FetchSingle('SELECT * FROM monster_plaza WHERE idnum=' . (int)$_GET['plazaid'] . ' LIMIT 1');

if($plazainfo === false)
{
  header('Location: /plaza.php');
  exit();
}

$watcher_list = explode(',', $plazainfo['admins']);

if(in_array($user['idnum'], $watcher_list))
{
	$threads = $database->FetchMultiple('SELECT replies FROM monster_threads WHERE plaza=' . $plazainfo['idnum']);

  $total = 0;

  foreach($threads as $thread)
    $total += $thread['replies'] + 1;

  $command = "UPDATE monster_plaza SET replies=$total WHERE idnum=" . $plazainfo['idnum'] . ' LIMIT 1';
  $database->FetchNone($command);
}

header('Location: /viewplaza.php?plaza=' . $plazainfo['idnum']);
?>
