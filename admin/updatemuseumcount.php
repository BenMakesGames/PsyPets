<?php
$_GET['maintenance'] = 'no';

require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/museumlib.php';

$command = 'SELECT COUNT(*) AS c,userid FROM psypets_museum GROUP BY(userid)';
$users = $database->FetchMultiple(($command, 'fetching users who have donated');

foreach($users as $user)
{
  $command = 'UPDATE monster_users SET museumcount=' . $user['c'] . ' WHERE idnum=' . $user['userid'] . ' LIMIT 1';
  $database->FetchMultiple(($command, 'updating museum count for user #' . $user['userid']);
  
  if($database->AffectedRows() > 0)
    echo $user['userid'] . ' had their museum count updated to ' . $user['c'] . '<br />';
}

echo 'DONE!';
?>
