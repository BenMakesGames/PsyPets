<?php
$whereat = 'aerosoc';
$wiki = 'Aeronautical Society';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/inventory.php';
require_once 'commons/checkpet.php';
require_once 'commons/blimplib.php';

if($user['show_aerosoc'] != 'yes' && $user['user'] != 'telkoth')
{
  header('Location: /');
  exit();
}

$exchanges = array(
  11 => array(1, 'Utterly Exhaustive List of Things That Don\'t Exist', 'Invocations For Aerial Combat'), 
//   5 => array(1, 'Tin-Foil Hat', 'Mint Tea Candle'),
//  12 => array(2, 'The Astronomer', 'Invocations For Aerial Combat'), 
//   6 => array(2, 'Large Wooden Mallet', 'Hookshot'),
//  15 => array(2, 'Talon', 'Logic Board Blueprint'),
   1 => array(3, 'Comfy Chair', 'Pilot\'s Seat'),
   2 => array(3, 'Gaudy Chair', 'Pilot\'s Seat'),
   3 => array(3, 'Leather Chair', 'Pilot\'s Seat'),
   4 => array(3, 'Sunshine Chair', 'Pilot\'s Seat'),
//   8 => array(4, 'White Paint', 'Mint Tea Candle'),
//   9 => array(5, 'Skeleton Key Blade', 'Maze Piece Summoning Scroll'),
//  14 => array(6, 'Wood', 'Bag of Rupees'),
//  10 => array(7, '1 Bamboo', 'Mint Tea Candle'),
//  13 => array(9, 'Shooting Star', 'Spaceship Totem'),
//   7 => array(10, 'Complex Circuit', 'Mint Tea Candle'),
);

$exchangeid = (int)$_GET['exchange'];

if(array_key_exists($exchangeid, $exchanges))
{
  $exchange = $exchanges[$exchangeid];

  $details1 = get_item_byname($exchange[1]);
  $details2 = get_item_byname($exchange[2]);

  $command = 'SELECT idnum FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND location=\'storage\' AND itemname=' . quote_smart($exchange[1]) . ' LIMIT 1';
  $item = $database->FetchSingle($command, $url);
  
  if($item === false)
    $message = '<p>Hm... you aren\'t trying to pull a fast on me, are you?  You don\'t seem to have a ' . $exchange[1] . ' in your storage at all.</p>';
  else
  {
    delete_inventory_byid($item['idnum']);
    add_inventory($user['user'], '', $exchange[2], 'Traded at the Aeronautical Society', $user['incomingto']);
    $message = '<p>Jolly good!  You\'ll find the ' . $exchange[2] . ' in your ' . $user['incomingto'] . '.</p>';
  }
}

//$pvp['rank'] = 10;

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Aeronautical Society</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <?= ($check_message ? "<p style=\"color:blue;\">$check_message</p>" : "") ?>
     <h4>Aeronautical Society</h4>
<?php
if($error_message)
  echo '<p class="failure">' . $error_message . '</p>';
?>
<img src="//' . $SETTINGS['static_domain'] . '/gfx/npcs/aeronautical.png" align="right" width="350" height="437" alt="" />
<?php
include 'commons/dialog_open.php';

if(strlen($message) > 0)
{
  echo $message;
}
else
{
  if($pvp['rank'] >= 10)
    echo '<p>A pleasure to see you, as always, ' . $user['display'] . '!</p><p>Don\'t hesitate to ask for anything.  The Aeronautical Society is entirely at your disposal.</p>';
  else if($pvp['rank'] >= 6)
    echo '<p>Tip of the hat to you, ' . $user['display'] . '.</p><p>And what can the Aeronautical Society do for you today?</p>';
  else if($pvp['rank'] >= 3)
    echo '<p>Ah, ' . $user['display'] . '!  Back for more, eh?  Well, we\'re always happy to help a fellow aviator.</p>';
  else
    echo '<p>' . $user['display'] . ', was it?  Well, as a fellow aviator you\'re of course welcome here.  Take a look around, and let me know if you need anything.</p>';
}

include 'commons/dialog_close.php';

if(count($options) > 0)
  echo '<ul><li>' . implode('</li><li>', $options) . '</li></ul>';

?>
<h4>Exchange</h4>
<form action="aerosoc.php" method="post">
<table>
 <thead>
  <tr>
   <th></th>
   <th></th>
   <th>Asking</th>
   <th></th>
   <th></th>
   <th>Offering</th>
  </tr>
 </thead>
<?php
$class = begin_row_class();

foreach($exchanges as $idnum=>$exchange)
{
/*
  if($exchange[0] > $pvp['rank'] + 2)
    continue;
*/
  $asking = $exchange[1];
  $offering = $exchange[2];
  
  $asking_item = get_item_byname($asking);
  $offering_item = get_item_byname($offering);

  echo '<tr class="' . $class . '">' . "\n";

  $command = 'SELECT idnum FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND location=\'storage\' AND itemname=' . quote_smart($asking) . ' LIMIT 1';
  $item = $database->FetchSingle($command, $url);

  if($item === false)
    echo ' <td class="dim">Accept</td>';
  else
    echo ' <td><a href="' . $url . '?exchange=' . $idnum . '">Accept</a></td>';
?>
  <td class="centered"><?= item_display($asking_item) ?></td>
  <td><?= $asking ?></td>
  <td><img src="gfx/lookright.gif" alt="" /></td>
  <td class="centered"><?= item_display($offering_item) ?></td>
  <td><?= $offering ?></td>
 </tr>
<?php
  $class = alt_row_class($class);
}
?>
</table>
</form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
