<?php
require_once 'commons/dbconnect.php';

$command = 'SELECT userid,COUNT(graphic) AS c FROM psypets_ark GROUP BY userid ORDER BY c DESC';
$users = $database->FetchMultiple($command, 'fetching ark users');

echo '<ul>';

foreach($users as $user)
{
  echo '<li>' . $user['userid'] . ' has ' . $user['c'] . ' pets.</li>';

  $command = 'UPDATE monster_users SET arkcount=' . $user['c'] . ' WHERE idnum=' . $user['userid'] . ' LIMIT 1';
  $database->FetchNone($command, 'updating ark count');
}

echo '</ul>';
?>
