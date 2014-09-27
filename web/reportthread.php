<?php
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';

$command = 'SELECT * FROM monster_threads WHERE idnum=' . (int)$_GET['threadid'] . ' LIMIT 1';
$this_thread = $database->FetchSingle($command, 'fetching thread');

if($this_thread === false)
{
  header('Location: /plaza.php');
  exit();
}

$plazainfo = $database->FetchSingle('SELECT * FROM monster_plaza WHERE idnum=' . $this_thread['plaza'] . ' LIMIT 1');

if($this_thread['plaza'] == 30 || $plazainfo['groupid'] == 0)
{
  header('Location: /viewthread.php?threadid=' . $this_thread['idnum']);
  exit();
}

// set the read status of this thread
$thread_watch = $database->FetchSingle('
	SELECT *
  FROM monster_watching
  WHERE `user`=' . quote_smart($user['user']) . '
  AND threadid=' . quote_smart($_GET['threadid']) . '
	LIMIT 1
');

if($thread_watch !== false)
{
  if($thread_watch["reported"] == "no")
  {
    $database->FetchNone("UPDATE monster_watching SET reported='yes' WHERE user=" . quote_smart($user["user"]) . " AND threadid=" . quote_smart($_GET["threadid"]) . " LIMIT 1");

    $database->FetchNone("INSERT INTO monster_reports (plazaid, threadid, reports) VALUES " .
                 "('" . $plazainfo["idnum"] . "', '" . $this_thread["idnum"] . "', '1')");
  }
}

header('Location: /viewthread.php?threadid=' . $_GET['threadid']);
exit();
?>
