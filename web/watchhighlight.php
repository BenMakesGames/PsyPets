<?php
// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/threadfunc.php';

$threadid = (int)$_GET['threadid'];
$destination = (int)$_GET['destination'];

$command = 'SELECT * FROM monster_threads WHERE idnum=' . $threadid . ' LIMIT 1';
$this_thread = $database->FetchSingle($command, 'watchhighlight.php');

// if we did not get the thread data, go to the main plaza
if($this_thread === false)
{
  header('Location: /plaza.php');
  exit();
}

if($this_thread['highlight'] != 0 && !in_array($this_thread['highlight'], $THREAD_HIGHLIGHTS_ALLOWED))
{
  header('Location: /watchtools.php?threadid=' . $threadid);
  exit();
}

if($this_thread['updatedate'] < $now - 30 * 24 * 60 * 60 && $admin['manageplaza'] != 'yes')
{
  add_cookie_message('<span class="failure">This thread is over a month old.  If it needs to be moved, trashed, stickied, etc, please ask ' . resident_link($SETTINGS['author_resident_name']) . ' to do so.</span>');
  header('Location: /viewthread.php?threadid=' . $threadid);
  exit();
}

// get info on the plaza in which this thread is located
$source_plazainfo = $database->FetchSingle('SELECT * FROM monster_plaza WHERE idnum=' . $this_thread['plaza'] . ' LIMIT 1');

if($source_plazainfo === false)
{
  header('Location: /watchtools.php?threadid=' . $this_thread['idnum']);
  exit();
}

$watcher_list = explode(',', $source_plazainfo['admins']);

if(in_array($user['idnum'], $watcher_list))
{
  $highlight = (int)$_POST['highlight'];
  
  if($highlight == 0 || in_array($highlight, $THREAD_HIGHLIGHTS_ALLOWED))
  {
    $database->FetchNone('UPDATE monster_threads SET highlight=' . $highlight . ' WHERE idnum=' . $threadid . ' LIMIT 1');

    header('Location: /watchtools.php?threadid=' . $threadid . '&msg=93');
  }
  else
    header('Location: /watchtools.php?threadid=' . $threadid);
}
else
  header('Location: /viewthread.php?threadid=' . $threadid);
?>
