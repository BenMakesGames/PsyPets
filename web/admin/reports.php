<?php
$IGNORE_MAINTENANCE = true;


require_once 'commons/init.php';

$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';

$plazaid = (int)$_GET['plazaid'];

$command = 'SELECT * FROM monster_plaza WHERE idnum=' . $plazaid . ' LIMIT 1';
$plazainfo = $database->FetchSingle($command, 'fetching plaza info');

if($plazainfo === false)
{
  header('Location: /plaza.php');
  exit();
}

$watcher_list = explode(',', $plazainfo['admins']);

if(in_array($user['idnum'], $watcher_list))
{
  if($_POST['action'] == 'clear')
  {
    foreach($_POST as $key=>$value)
    {
      if($key > 0 && is_numeric($key))
      {
        $command = 'DELETE FROM monster_reports WHERE threadid=' . (int)$key . ' LIMIT 1';
        $database->FetchNone(($command, 'deleting report');

        $command = "UPDATE monster_watching SET reported='no' WHERE threadid=" . (int)$key;
        $database->FetchNone(($command, 'deleting user report flags');
      }
    }
  }
}

header('Location: /viewplaza.php?plaza=' . $plazaid);

?>