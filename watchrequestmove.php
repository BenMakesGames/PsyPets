<?php
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';

$now = time();

$threadid = (int)$_GET['threadid'];
$destination = (int)$_GET['destination'];

$command = 'SELECT * FROM monster_threads WHERE idnum=' . $threadid . ' LIMIT 1';
$this_thread = $database->FetchSingle($command, 'fetching this thread');

// if we did not get the thread data, go to the main plaza
if($this_thread === false)
{
  header('Location: /plaza.php');
  exit();
}

// get info on the plaza in which this thread is located
$command = 'SELECT * FROM monster_plaza WHERE idnum=' . $this_thread['plaza'] . ' LIMIT 1';
$source_plazainfo = $database->FetchSingle($command, 'fetching source plaza info');

if($source_plazainfo === false)
{
  header('Location: /plaza.php');
  exit();
}

if($this_thread['updatedate'] < $now - 30 * 24 * 60 * 60 && $admin['manageplaza'] != 'yes')
{
  header('Location: /viewthread.php?threadid=' . $threadid . '&msg=160');
  exit();
}

if($source_plazainfo['groupid'] != 0)
{
  header('Location: /watchtools.php?threadid=' . $this_thread['idnum']);
  exit();
}

// get info on the destination plaza
$command = 'SELECT * FROM monster_plaza WHERE idnum=' . (int)$_POST['destination'] . ' LIMIT 1';
$plazainfo = $database->FetchSingle($command, 'fetching destination plaza info');

if($plazainfo !== false)
{
  $watcher_list = explode(',', $source_plazainfo['admins']);

  if(in_array($user['idnum'], $watcher_list) && $plazainfo['groupid'] == 0 && $source_plazainfo['groupid'] == 0)
  {
    $command = 'INSERT INTO monster_watchermove (timestamp, watcher, threadid, destination) VALUES ' .
               '(' . $now . ', ' . $user['idnum'] . ', ' . $this_thread['idnum'] . ', ' . $plazainfo['idnum'] . ')';
    $database->FetchNone($command, 'adding move request');

    $gamenote = 'request move: from ' . $source_plazainfo['title'] . ' to ' . $plazainfo['title'];

    $command = 'INSERT INTO psypets_thread_history (threadid, timestamp, userid, gamenote) VALUES ' .
               '(' . $threadid . ', ' . $now . ', ' . $user['idnum'] . ', ' . quote_smart($gamenote) . ')';
    $database->FetchNone($command, 'adding move request history');
  }
}

header('Location: /viewthread.php?threadid=' . $this_thread['idnum'] . '&msg=161');
?>
