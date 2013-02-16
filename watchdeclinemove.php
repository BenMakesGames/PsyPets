<?php
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';

$requestid = (int)$_GET['requestid'];

$command = 'SELECT * FROM monster_watchermove WHERE idnum=' . quote_smart($_GET['requestid']) . ' LIMIT 1';
$this_request = $database->FetchSingle($command, 'fetching move request');

// if we did not get the thread data OK, go to the main plaza
if($this_request === false)
{
  header('Location: ./plaza.php');
  exit();
}

$command = 'SELECT * FROM monster_plaza WHERE idnum=' . $this_request['destination'] . ' LIMIT 1';
$plazainfo = $database->FetchSingle($command, 'fetching plaza info');

$watcher_list = explode(',', $plazainfo['admins']);

if(in_array($user['idnum'], $watcher_list))
{
  $command = 'DELETE FROM monster_watchermove WHERE idnum=' . $this_request['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'deleting move request');

  $gamenote = 'declined move';

  $command = 'INSERT INTO psypets_thread_history (threadid, timestamp, userid, gamenote) VALUES ' .
             '(' . $this_request['threadid'] . ', ' . $now . ', ' . $user['idnum'] . ', ' . quote_smart($gamenote) . ')';
  $database->FetchNone($command, 'adding move request history');
}

header('Location: ./viewplaza.php?plaza=' . $this_request['destination']);
?>
