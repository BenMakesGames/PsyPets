<?php
require_once 'commons/init.php';

$whereat = "home";
$wiki = "Fireplace";
$THIS_ROOM = 'Fireplace';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/checkpet.php';
require_once 'commons/messages.php';
require_once 'commons/houselib.php';
require_once 'commons/fireplacelib.php';
require_once 'commons/utility.php';

if(!addon_exists($house, 'Fireplace'))
{
  header('Location: /myhouse.php');
  exit();
}

$first_visit = false;

$fireplace = get_fireplace_byuser($user['idnum'], $user['locid']);
if($fireplace === false)
{
  create_fireplace($user["idnum"], $user["locid"]);
  $fireplace = get_fireplace_byuser($user["idnum"], $user["locid"]);
  if($fireplace === false)
  {
    echo "Failed to load your fireplace.  Try reloading this page; if the problem persists, contact <a href=\"admincontact.php\">an administrator</a>.<br />\n";
    exit();
  }

  $first_visit = true;
}

$command = 'SELECT * FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND location=\'fireplace\'';
$mantle = fetch_multiple_by($command, 'idnum', 'fetching mantle items');

if(strlen($fireplace['mantle']) > 0)
  sort_items_by_mantle($mantle, explode(',', $fireplace['mantle']));

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $user["display"] ?>'s House &gt; Fireplace &gt; Mantle</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/myhouse.php"><?= $user['display'] ?>'s House</a> &gt; Fireplace &gt; Mantle</h4>
<?php
echo $check_message;
echo $message;
room_display($house);
?>
<ul class="tabbed">
 <li><a href="/myhouse/addon/fireplace.php">Fire</a></li>
 <li class="activetab"><a href="/myhouse/addon/fireplace_mantle.php">Mantle</a></li>
</ul>
     <p>You may put up to 10 items on the mantle to be displayed on your profile page before your other treasures.  Even items that do not normally appear on the profile can be displayed in this way.  These items will not count as being in the house (size, hourly effects, etc).</p>
<?php
if(count($mantle) > 0)
{
?>
<ul><li><a href="/myhouse/addon/fireplace_mantle_sort.php">Change order of mantle items</a></li></ul>
<form action="/moveinventory2.php?confirm=1" method="post" id="form1">
<table>
 <tr class="titlerow">
  <th><input type="checkbox" class="checkall" id="check1" /></th>
  <th></th>
  <th>Item</th>
  <th>Comment</th>
 </tr>
<?php
  $rowclass = begin_row_class();

  foreach($mantle as $item)
  {
    $details = get_item_byname($item['itemname']);
?>
 <tr class="<?= $rowclass ?>">
  <td><input name="<?= $item['idnum'] ?>" type="checkbox" /></td>
  <td class="centered"><?= item_display_extra($details, '', false) ?></td>
  <td><?= $item['itemname'] ?></td>
  <td><?= $item['message'] . '<br />' . $item['message2'] ?></td>
 </tr>
<?php
    $rowclass = alt_row_class($rowclass);
  }
?>
</table>
<p><input type="submit" name="submit" value="Move to" />&nbsp;<select id="move1" name="move1" />
<?php
  echo '<option value="Storage">Storage</option>';
  echo '<option value="Common">Common</option>';

  $rooms = take_apart(',', $house['rooms']);

  foreach($rooms as $room)
    echo '<option value="' . $room . '">' . ($room{0} == '$' ? substr($room, 1) : $room) . '</option>';

  if(array_search('Basement', $addons) !== false)
    echo '<option value="Basement">Basement</option>';
?>
</select></p>
</form>
<?php
}

if(count($mantle) < 10)
{
?>
     <h5>Add an Item to the Mantle</h5>
     <form action="/myhouse/addon/fireplace_mantle_add.php" method="post">
     <p>Search your house for an item to add by entering all or part of the item's name.  Your protected rooms will be included in the search.</p>
     <p><input name="itemname" maxlength="64" size="48" /> <input type="hidden" name="action" value="search" /><input type="submit" value="Search" /></p>
     </form>
<?php
}
?>
<?php include "commons/footer_2.php"; ?>
 </body>
</html>
