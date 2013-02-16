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
require_once 'commons/checkpet.php';
require_once 'commons/petblurb.php';
require_once 'commons/love.php';
require_once 'commons/messages.php';
require_once 'commons/houselib.php';

$house = get_house_byuser($user['idnum']);

if($house === false)
{
  echo "Failed to load your house.<br />\n";
  exit();
}

$rooms = take_apart(',', $house['rooms']);
$actual_rooms = array('home', 'fireplace', 'storage', 'pet', 'seized', 'storage/incoming', 'storage/locked', 'storage/mystore', 'storage/outgoing', 'trade');
foreach($rooms as $room)
  $actual_rooms[] = 'home/' . $room;

if($_GET['action'] == 'find')
{
  $command = 'UPDATE monster_inventory SET location=\'storage/incoming\',changed=' . time() . ' WHERE user=' . quote_smart($user['user']) . ' AND location NOT IN (\'' . implode('\',\'', $actual_rooms) . '\')';
  $result = $database->FetchNone($command, 'lostitems.php');

  flag_new_incoming_items($user['user']);

  header('Location: ./incoming.php');
  exit();
}

$command = 'SELECT * FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND location NOT IN (\'' . implode('\',\'', $actual_rooms) . '\')';
$items = $database->FetchMultiple($command, 'lostitems.php');

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $user['display'] ?>'s House &gt; <?= $sayroom ?> Room</title>
<?php include 'commons/head.php'; ?>
 </head>
<?php include 'commons/header_2.php'; ?>
     <h4>Lost Items</h4>
     <p>Below is a list of items you own that are "lost" in rooms that don't exist.</p>
     <p><strong>HEY:</strong> If you find items listed here, please tell me (<?= $SETTINGS['author_resident_name'] ?>).  If items are repeatedly showing up, I need to know so I can fix it.</p>
<?php
if(count($items) > 0)
{
?>
     <ul>
<?php
  foreach($items as $item)
    echo '<li>' . $item['itemname'] . ' in "' . $item['location'] . '"</li>';
?>
     </ul>
     <ul>
      <li><a href="lostitems.php?action=find">Put these items in Incoming</a></li>
     </ul>
<?php
}
else
  echo '<p>No such items were found.</p>';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
