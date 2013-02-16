<?php
$_GET['maintenance'] = 'no';

require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/formatting.php';
require_once 'commons/featuredrivelib.php';

if($user['admin']['alphalevel'] < 6)
{
  header('Location: ./featuredrive.php');
  exit();
}

$command = 'TRUNCATE TABLE psypets_feature_drive';
$database->FetchNone(($command, 'truncating feature drive votes');

header('Location: ./featuredrive.php');
exit();
?>
