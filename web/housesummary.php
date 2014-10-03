<?php
$whereat = 'home';
$wiki = 'My_House#Inventory_Summary';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/formatting.php';
require_once 'commons/messages.php';
require_once 'commons/houselib.php';

$house = get_house_byuser($user['idnum']);

if($house === false)
{
  echo "Failed to load your house.<br />\n";
  exit();
}

$command = 'SELECT COUNT(itemname) AS c,itemname,location FROM monster_inventory WHERE location LIKE \'home%\' AND user=' . quote_smart($user['user']) . ' GROUP BY itemname,location ORDER BY location,itemname ASC';
$inventory = $database->FetchMultiple($command, 'fetching house inventory summary');

$total_total = 0;

foreach($inventory as $item)
{
  $rooms[$item['location']] = true;
  $item_total[$item['location']] += $item['c'];
  $total_total += $item['c'];
}

function say_room($room)
{
  if($room == 'home')
    return 'Common';
  else
  {
    $room = substr($room, 5);
    if($room{0} == '$')
    {
      $room = substr($room, 1);
      return '<img src="gfx/roomlock.png" border="0" alt="" /> ' . $room;
    }
    else
      return $room;
  }
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $user['display'] ?>'s House &gt; Inventory Summary</title>
<?php include 'commons/head.php'; ?>
  <style type="text/css">
   .col2 { padding-left: 2em; }
   h6 { padding-top: 1em; }
  </style>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
<h4><a href="myhouse.php"><?= $user['display'] ?>'s House</a> &gt; Inventory Summary</h4>
<ul class="tabbed">
 <li class="activetab"><a href="housesummary.php">House Summary</a></li>
<?php
if(addon_exists($house, 'Basement'))
  echo '<li><a href="basementsummary.php">Basement Summary</a></li>';
?>
 <li><a href="storagesummary.php">Storage Summary</a></li>
</ul>
<?php
if(count($inventory) > 0)
{
?>
<!--
<ul>
<?php
  $room_id = 0;
  foreach($rooms as $room=>$dummy)
  {
    $room_id++;
    echo '<li><a href="#r' . $room_id . '">' . say_room($room) . '</a></li>';
  }
?>
</ul>
-->
<table>
<?php
  $cur_room = '';
  $col = 1;
  $room_id = 0;
  $items = 0;

  foreach($inventory as $item)
  {
    if($item['location'] != $cur_room)
    {
      $cur_room = $item['location'];
      
      if($col == 2)
      {
        echo '<td colspan="2"></td></tr>';
        $col = 1;
      }

      if($cur_room == 'home')
        $room_link = 'myhouse.php';
      else
        $room_link = 'myhouse.php?room=' . substr($cur_room, 5);

      $room_id++;
      echo '<tr><td colspan="4"><h5 id="r' . $room_id . '" class="separator"><a href="' . $room_link . '">' . say_room($cur_room) . '</a> (' . $item_total[$cur_room] . ' items)</h5></td></tr>' .
           '<tr class="titlerow"><th>Qty</th><th>Item</th><th class="col2">Qty</th><th>Item</th></tr>';

      $row_class = begin_row_class();
    }

    if($col == 1)
    {
      echo '<tr class="' . $row_class . '"><td class="centered">';
      $row_class = alt_row_class($row_class);
    }
    else
      echo '<td class="col2 centered">';

    echo $item['c'] . '</td><td>' . $item['itemname'] . '</td>';

    if($col == 2)
    {
      echo '</tr>';
      $col = 1;
    }
    else
      $col = 2;
  }

  if($col == 2)
    echo '<td colspan="2"></td></tr>';
?>
</table>
<ul><li><a href="myhouse.php">Back to my house</a></li></ul>
<?php
}
else
  echo '<p>You don\'t have any items in your house.  Not a single one.</p>';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
