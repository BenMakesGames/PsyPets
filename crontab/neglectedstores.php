<?php
$require_petload = 'no';

$IGNORE_MAINTENANCE = true;

//ini_set('include_path', '/your/web/root');

require_once 'commons/dbconnect.php';
require_once 'commons/formatting.php';

header('MIME-Version: 1.0');
header('Content-Type: text/html');

$now = time();

$oldest_time = $now - (48 * 60 * 60);

$command = 'SELECT a.idnum,a.display,a.user,a.openstore,b.lasthour FROM monster_users AS a LEFT JOIN monster_houses AS b ON a.idnum=b.userid WHERE a.openstore=\'yes\' AND b.lasthour<' . ($oldest_time);
$open_stores = $database->FetchMultiple($command, 'fetching open stores');

echo '<html><body>' . "\n";
echo '<p>Closing the following stores...</p>' . "\n";

$ids = array();

if(count($open_stores) > 0)
{
  echo '<table><tr class="titlerow"><th>Resident</th><th>Login</th><th>ID</th><th>Open store?</th><th>Last ran hours</th></tr>' . "\n";

  $rowclass = begin_row_class();

  foreach($open_stores as $open_store)
  {
    echo '<tr class="' . $rowclass . '"><td>' . $open_store['display'] . '</td><td>' . $open_store['user'] . '</td><td>#' . $open_store['idnum'] . '</td><td>' . $open_store['openstore'] . '</td><td>' . duration($now - $open_store['lasthour'], 2) . ' ago</td></tr>' . "\n";
    $rowclass = alt_row_class($rowclass);

    $ids[] = $open_store['idnum'];
  }

  echo '</table>' . "\n";

  $command = 'UPDATE monster_users SET openstore=\'no\',storeclosed=\'yes\' WHERE idnum IN (' . implode(',', $ids) . ') LIMIT ' . count($ids);
  $database->FetchNone($command, 'closing store');

  echo '<p>' . $database->AffectedRows() . ' stores closed.</p>' . "\n";
}
else
  echo '<p>No stores to close!</p>' . "\n";

echo '</body></html>' . "\n";
?>
