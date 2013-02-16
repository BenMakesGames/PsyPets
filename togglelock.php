<?php
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';

$threadid = (int)$_GET['threadid'];

$command = 'SELECT * FROM monster_threads WHERE idnum=' . $threadid . ' LIMIT 1';
$this_thread = $database->FetchSingle($command, 'togglelock.php');

if($this_thread === false)
{
  header('Location: ./plaza.php');
  exit();
}

if($user['admin']['manageplaza'] != 'yes')
{
  header('Location: ./watchtools.php?threadid=' . $this_thread['idnum']);
  exit();
}

$plazainfo = $database->FetchSingle("SELECT * FROM monster_plaza WHERE idnum=" . $this_thread["plaza"] . " LIMIT 1");

$watcher_list = explode(",", $plazainfo["admins"]);

if(in_array($user["idnum"], $watcher_list))
{
  if($this_thread["locked"] == "yes")
    $locked = "no";
  else
    $locked = "yes";

  $command = "UPDATE monster_threads SET locked='$locked' WHERE idnum=" . $this_thread["idnum"] . " LIMIT 1";
  $database->FetchNone($command, 'toggling thread lock');

  header('Location: ./watchtools.php?threadid=' . $this_thread['idnum']);
}
else
  header('Location: ./viewthread.php?threadid=' . $this_thread['idnum']);
?>
