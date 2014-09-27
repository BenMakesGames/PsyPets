<?php
$require_petload = "no";
$child_safe = false;

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';

$idnums = array();

if(array_key_exists('subscriptionid', $_GET))
{
  $idnums[] = (int)$_GET['subscriptionid'];
  $return_to = 'viewthread.php?threadid=' . (int)$_GET['threadid'] . '&page=' . (int)$_GET['page'];
}
else
{
  $return_to = 'threadsubscriptions.php';

  foreach($_POST as $key=>$value)
  {
    if(substr($key, 0, 2) == 's_')
    {
      if($value == 'yes' || $value == 'on')
        $idnums[] = (int)substr($key, 2);
    }
  }
}

if(count($idnums) > 0)
{
  $command = 'DELETE FROM psypets_watchedthreads WHERE userid=' . $user['idnum'] . ' AND idnum IN (' . implode(',', $idnums) . ') LIMIT ' . count($idnums);
  $database->FetchNone($command, 'threadunsubscribe.php');
}

header('Location: ./' . $return_to);
?>
