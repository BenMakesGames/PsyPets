<?php
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';

$command = 'SELECT * FROM monster_threads WHERE idnum=' . (int)$_GET['threadid'] . ' LIMIT 1';
$this_thread = $database->FetchSingle($command, 'fetching thread');

if($this_thread === false)
{
  header('Location: ./plaza.php');
  exit();
}

$command = "SELECT * FROM monster_plaza WHERE idnum=" . $this_thread["plaza"] . " LIMIT 1";
$plazainfo = $database->FetchSingle($command, 'fetching plaza');

$watcher_list = explode(",", $plazainfo["admins"]);

if(in_array($user["idnum"], $watcher_list))
{
  if($this_thread["sticky"] == "yes")
    $sticky = "no";
  else
    $sticky = "yes";

  $command = "UPDATE monster_threads SET sticky='$sticky' WHERE idnum=" . $this_thread["idnum"] . " LIMIT 1;";
  $database->FetchNone($command, 'toggling thread stickiness');

  header('Location: ./watchtools.php?threadid=' . $this_thread['idnum']);
}
else
  header('Location: ./viewthread.php?threadid=' . $this_thread['idnum']);
?>
