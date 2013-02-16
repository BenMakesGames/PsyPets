<?php
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';

$command = 'SELECT tradeid,user1,user2 FROM monster_trades WHERE userid1=0 AND userid2=0';
$trades = $database->FetchMultiple($command, 'fetching trades');

foreach($trades as $trade)
{
  echo 'Trade #' . $trade['tradeid'] . '... ';

  $user1 = get_user_byuser($trade['user1'], 'idnum');
  $user2 = get_user_byuser($trade['user2'], 'idnum');

  if($user1 === false)
    $user1['idnum'] = 0;

  if($user2 === false)
    $user2['idnum'] = 0;

  echo $trade['user1'] . ' (' . $user1['idnum'] . ') and ' . $trade['user2'] . ' (' . $user2['idnum'] . ')... ';

  if($user1['idnum'] == 0 && $user2['idnum'] == 0)
  {
    $command = 'DELETE FROM monster_trades WHERE tradeid=' . $trade['tradeid'] . ' LIMIT 1';
    $database->FetchNone($command, 'deleting trade ' . $trade['tradeid']);
    
    echo 'deleted.<br />';
  }
  else
  {
    $command = 'UPDATE monster_trades SET userid1=' . $user1['idnum'] . ',userid2=' . $user2['idnum'] . ' WHERE tradeid=' . $trade['tradeid'] . ' LIMIT 1';
    $database->FetchNone($command, 'updating trade ' . $trade['tradeid']);

    echo 'updated.<br />';
  }
}

echo '<br />Done!';
?>
