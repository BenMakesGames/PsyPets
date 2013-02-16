<?php
$require_petload = "no";
$child_safe = false;

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';

$threadid = (int)$_GET['threadid'];

$command = 'SELECT * FROM monster_threads WHERE idnum=' . $threadid . ' LIMIT 1';
$this_thread = $database->FetchSingle($command, 'threadsubscribe.php?threadid=' . $threadid);

if($this_thread === false)
{
  Header("Location: ./plaza.php");
  exit();
}

$command = 'SELECT * FROM psypets_watchedthreads WHERE userid=' . $user['idnum'] . ' AND threadid=' . $threadid . ' LIMIT 1';
$thread_subscription = $database->FetchSingle($command, 'viewthread.php?threadid=' . $threadid);

if($thread_subscription === false)
{
  $command = 'INSERT INTO psypets_watchedthreads (userid, threadid) VALUES (' . $user['idnum'] . ', ' . $threadid . ')';
  $database->FetchNone($command, 'threadsubscribe.php?threadid=' . $threadid);
}

header('Location: ./viewthread.php?threadid=' . $threadid . '&page=' . $_GET['page']);
?>
