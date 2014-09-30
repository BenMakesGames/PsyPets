<?php
require_once 'commons/dbconnect.php';
require_once 'commons/itemlib.php';
require_once 'commons/userlib.php';

$now = time();

$command = 'SELECT DISTINCT(userid) FROM psypets_ark';
$users = $database->FetchMultiple($command, 'fetching ark users');

foreach($users as $this_user)
{
  $userid = $this_user['userid'];

  $command = 'SELECT DISTINCT(graphic) FROM psypets_ark WHERE userid=' . $userid;
  $graphics = $database->FetchMultiple($command, 'fetching donated graphics');
  
  $report = array();
  $extra = 0;

  foreach($graphics as $graphic)
  {
    $command = 'SELECT COUNT(userid) AS c FROM psypets_ark WHERE userid=' . $userid . ' AND graphic=\'' . $graphic['graphic'] . '\' AND gender=\'male\'';
    $data = $database->FetchSingle($command, 'fetching males');
    $num_males = $data['c'];
    
    if($num_males > 1)
    {
      $report[] = 'Has ' . $num_males . ' male ' . $graphic['graphic'];
      $extra += $num_males - 1;
/*
      $command = 'DELETE FROM psypets_ark WHERE userid=' . $userid . ' AND graphic=\'' . $graphic['graphic'] . '\' AND gender=\'male\' LIMIT ' . ($num_males - 1);
      $database->FetchNone($command, 'deleting extra males');
*/
    }

    $command = 'SELECT COUNT(userid) AS c FROM psypets_ark WHERE userid=' . $userid . ' AND graphic=\'' . $graphic['graphic'] . '\' AND gender=\'female\'';
    $data = $database->FetchSingle($command, 'fetching females');
    $num_females = $data['c'];

    if($num_females > 1)
    {
      $report[] = 'Has ' . $num_females . ' female ' . $graphic['graphic'];
      $extra += $num_females - 1;
/*
      $command = 'DELETE FROM psypets_ark WHERE userid=' . $userid . ' AND graphic=\'' . $graphic['graphic'] . '\' AND gender=\'female\' LIMIT ' . ($num_females - 1);
      $database->FetchNone($command, 'deleting extra females');
*/
    }
  }
  
  if(count($report) > 0)
  {
    $command = 'SELECT user,display FROM monster_users WHERE idnum=' . $userid . ' LIMIT 1';
    $user = $database->FetchSingle($command, 'fetching user #' . $userid);
  
    echo '<h4>' . $user['display'] . ' (' . $user['user'] . '; #' . $userid . ')</h4><ul><li>' . implode('</li><li>', $report) . '</li></ul>';
    echo '<p>Total of ' . $extra . ' extra pets.</p>';
/*
    add_inventory_quantity($user['user'], '', 'Scroll of Monster Summoning', 'refunded Ark pet', 'storage/incoming', $extra);

    psymail_user($user['user'], 'psypets', 'Duplicate pets were removed from your Ark', 'A handy-dandy little script removed ' . $extra . ' duplicate pets from your Ark.  You probably donated these by accident in the early days of the Ark, before it properly disallowed multiple submissions of the same pet.  You have been given one Scroll of Monster Summoning for each pet removed in this way.');
*/
  }
}
?>
