<?php
$IGNORE_MAINTENANCE = true;


require_once 'commons/init.php';

require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/formatting.php';
require_once 'commons/featuredrivelib.php';

if($user['admin']['coder'] != 'yes')
{
  header('Location: ./featuredrive.php');
  exit();
}

$command = 'TRUNCATE TABLE psypets_feature_drive';
$database->FetchNone($command, 'truncating feature drive votes');

header('Location: ./featuredrive.php');
