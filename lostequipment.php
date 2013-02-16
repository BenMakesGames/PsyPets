<?php
$whereat = 'home';
$wiki = 'My_House#Lost_Items';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/inventory.php';

$command = 'SELECT idnum,itemname FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND location=\'pet\'';
$tools = $database->FetchMultiple($command, 'fetching all items you own that think they are equipped to pets');

foreach($tools as $tool)
{
  $command = 'SELECT idnum FROM monster_pets WHERE toolid=' . $tool['idnum'] . ' OR keyid=' . $tool['idnum'] . ' LIMIT 1';
  $data = $database->FetchSingle($command, 'fetching equipped pet');
  
  if($data === false)
  {
    $lost_ids[] = $tool['idnum'];
    $lost_items[] = $tool['itemname'];
  }
}

if($_GET['action'] == 'find')
{
  $command = 'UPDATE monster_inventory SET location=\'storage/incoming\' WHERE user=' . quote_smart($user['user']) . ' AND idnum IN (' . implode(',', $lost_ids) . ') LIMIT ' . count($lost_ids);
  $database->FetchNone($command, 'moving the lost equipment to incoming');

  flag_new_incoming_items($user['user']);

  header('Location: ./incoming.php');
  exit();
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $user['display'] ?>'s House &gt; <?= $sayroom ?> Room</title>
<?php include 'commons/head.php'; ?>
 </head>
<?php include 'commons/header_2.php'; ?>
     <h4>Lost Equipment</h4>
     <p>Below is a list of items you own which think they are equipped to a pet, but which no pet has equipped!</p>
<?php
if(count($lost_items) > 0)
{
?>
     <ul>
<?php
  foreach($lost_items as $itemname)
    echo '<li>' . $itemname . '</li>';
?>
     </ul>
     <ul>
      <li><a href="lostequipment.php?action=find">Put these items in Incoming</a></li>
     </ul>
<?php
}
else
  echo '<p>No such items were found!  (Good!)</p>';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
