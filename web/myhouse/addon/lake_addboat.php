<?php
require_once 'commons/init.php';

$whereat = 'home';
$wiki = 'Lake';
$THIS_ROOM = 'Lake';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/checkpet.php';
require_once 'commons/messages.php';
require_once 'commons/houselib.php';
require_once 'commons/lakelib.php';
require_once 'commons/utility.php';
require_once 'commons/moonphase.php';

if(!addon_exists($house, 'Lake'))
{
  header('Location: /myhouse.php');
  exit();
}

$lake = get_lake_byuser($user['idnum']);
if($lake === false)
{
  header('Location: /myhouse/addon/lake.php');
  exit();
}

$boats = take_apart(',', $lake['boats']);
$num_boats = count($boats);

if($num_boats >= 6)
{
  header('Location: /myhouse/addon/lake.php');
  exit();
}

$BOAT_LIST = array(
  'Black Swan Boat',
  'Dugout Canoe',
  'Large Paper Boat',
  'Small Greek Trireme',
  'Swan Boat',
);

$command = 'SELECT COUNT(idnum) AS c,itemname FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND location LIKE \'home%\' AND location NOT LIKE \'home/$%\' ' .
           'AND itemname IN (\'' . implode('\', \'', $BOAT_LIST) . '\') GROUP BY itemname';
$inventory = fetch_multiple_by($command, 'itemname', 'fetching boats at home');

if(array_key_exists('boat', $_GET))
{
  $itemname = urldecode($_GET['boat']);

  if(array_key_exists($itemname, $inventory))
  {
    $deleted = delete_inventory_fromhome($user['user'], $itemname, 1);
    
    if($deleted == 1)
    {
      $boats[] = $itemname;
      
      $command = 'UPDATE psypets_lakes SET boats=' . quote_smart(implode(',', $boats)) . ' WHERE userid=' . $user['idnum'] . ' LIMIT 1';
      fetch_none($command, 'adding boat');

      if($inventory[$itemname]['c'] == 1)
        unset($inventory[$itemname]);
      else
        $inventory[$itemname]['c']--;

      // if you had 5 boats, or if you have no more boats left at home
      if($num_boats == 5 || count($inventory) == 0)
      {
        header('Location: /myhouse/addon/lake.php');
        exit();
      }
      else
        $message .= '<p class="success">The ' . $itemname . ' has been added to your lake.</p>';
    }
  }
}

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $user["display"] ?>'s House &gt; Lake &gt; Add Boat</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/myhouse.php"><?= $user['display'] ?>'s House</a> &gt; <a href="/myhouse/addon/lake.php">Lake</a> &gt; Add Boat</h4>
<?php
echo $message;

room_display($house);

if(count($inventory) > 0)
{
?>
     <p>The quantity displayed is the number owned, not the number you will add to the Lake.  (You will only add one.)</p>
     <table>
      <thead>
       <tr class="titlerow">
        <th></th><th></th><th>Boat</th><th>Qty</th>
       </tr>
      </thead>
      <tbody>
<?php
  $rowclass = begin_row_class();

  foreach($inventory as $item)
  {
    $details = get_item_byname($item['itemname']);
?>
       <tr class="<?= $rowclass ?>">
        <td><a href="/myhouse/addon/lake_addboat.php?boat=<?= urlencode($item['itemname']) ?>">Add</a></td>
        <td class="centered"><?= item_display($details, '') ?></td>
        <td><?= $item['itemname'] ?></td>
        <td class="centered"><?= $item['c'] ?></td>
       </tr>
<?php
    $rowclass = alt_row_class($rowclass);
  }
?>
      </tbody>
     </table>
<?php
}
else
  echo '<p>You have no boats in your house.  (Or at least none that aren\'t in protected rooms.)</p>';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
