<?php
// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/threadfunc.php';

$command = "SELECT * FROM monster_watchermove WHERE idnum=" . (int)$_GET["requestid"] . ' LIMIT 1';
$this_request = $database->FetchSingle($command, 'fetching move request');

// if we did not get the thread data OK, go to the main plaza
if($this_request === false)
{
  header('Location: /plaza.php');
  exit();
}

$this_thread = get_thread_byidnum($this_request['threadid']);

$command = "SELECT * FROM monster_plaza WHERE idnum=" . $this_request["destination"] . " LIMIT 1";
$plazainfo = $database->FetchSingle($command, 'fetching plaza info');

$watcher_list = explode(',', $plazainfo['admins']);

if(in_array($user['idnum'], $watcher_list))
{

  if($this_thread['updatedate'] < $now - 30 * 24 * 60 * 60 && $admin['manageplaza'] != 'yes')
  {
    add_cookie_message('<span class="failure">This thread is over a month old.  If it needs to be moved, trashed, stickied, etc, please ask ' . resident_link($SETTINGS['author_resident_name']) . ' to do so.</span>');
    header('Location: /viewplaza.php?plaza=' . $this_request['destination']);
    exit();
  }

  $command = "UPDATE monster_plaza SET replies=replies-" . ($this_thread['replies'] + 1) . " WHERE idnum=" . $this_thread["plaza"] . " LIMIT 1";
  $database->FetchNone($command, 'adjusting post count in source plaza section');

  $command = "UPDATE monster_plaza SET replies=replies+" . ($this_thread['replies'] + 1) . " WHERE idnum=" . $this_request["destination"] . " LIMIT 1";
  $database->FetchNone($command, 'adjusting post count in destination plaza section');

  $command = "UPDATE monster_threads SET plaza=" . $this_request["destination"] . " WHERE idnum=" . $this_request["threadid"] . " LIMIT 1";
  $database->FetchNone($command, 'moving thread');

  $command = "DELETE FROM monster_watchermove WHERE idnum=" . $this_request["idnum"] . " LIMIT 1";
  $database->FetchNone($command, 'deleting move request');

  $gamenote = 'accepted move';

  $command = 'INSERT INTO psypets_thread_history (threadid, timestamp, userid, gamenote) VALUES ' .
             '(' . $this_request['threadid'] . ', ' . $now . ', ' . $user['idnum'] . ', ' . quote_smart($gamenote) . ')';
  $database->FetchNone($command, 'adding move request history');
}

header('Location: /viewplaza.php?plaza=' . $this_request['destination']);
?>
