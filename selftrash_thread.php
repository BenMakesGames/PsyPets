<?php
// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/threadfunc.php';

$threadid = (int)$_GET['threadid'];
$this_thread = get_thread_byidnum($threadid);

if($this_thread['createdby'] == $user['idnum'])
{
  $command = 'SELECT * FROM monster_plaza WHERE title=\'Trash\' LIMIT 1';
  $plazainfo = $database->FetchSingle($command, 'fetching plaza info');

  $command = 'UPDATE monster_plaza SET replies=replies-' . ($this_thread['replies'] + 1) . ' WHERE idnum=' . $this_thread['plaza'] . ' LIMIT 1';
  $database->FetchNone($command, 'adjusting post count in source plaza section');

  $command = 'UPDATE monster_plaza SET replies=replies+' . ($this_thread['replies'] + 1) . ' WHERE idnum=' . $plazainfo['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'adjusting post count in destination plaza section');

  $command = 'UPDATE monster_threads SET plaza=' . $plazainfo['idnum'] . ',sticky=\'no\' WHERE idnum=' . $threadid . ' LIMIT 1';
  $database->FetchNone($command, 'moving thread');

  $command = '
    INSERT INTO psypets_thread_history
    (threadid, timestamp, userid, gamenote)
    VALUES
    (' . $threadid . ', ' . $now . ', ' . $user['idnum'] . ', \'trashed by owner\')
  ';
  $database->FetchNone($command, 'logging self-deletion');
    

  header('Location: /viewplaza.php?plaza=' . $plazainfo['idnum']);
}
else
  header('Location: /viewthread.php?threadid=' . $threadid);
?>
