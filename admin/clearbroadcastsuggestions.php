<?php
$_GET['maintenance'] = 'no';

require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';

if($user['admin']['alphalevel'] >= 6)
{
  $command = 'TRUNCATE TABLE psypets_broadcasting_topics';
  $database->FetchNone(($command, 'truncating broadcasting topics');
}

header('Location: /livebroadcast.php');
?>
