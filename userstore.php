<?php
$whereat = 'userstore';
$wiki = 'Flea_Market';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/inventory.php';
require_once 'commons/formatting.php';
require_once 'commons/itemlib.php';
require_once 'commons/houselib.php';

if($user['license'] == 'no')
{
  header('Location: /ltc.php?dialog=2');
  exit();
}

$aisle = unlink_safe($_GET['user']);

$store_user = get_user_bydisplay($aisle);

if($store_user === false)
{
  header('Location: /fleamarket/');
  exit();
}

$may_buy = ($store_user['idnum'] != $user['idnum']);

if($store_user['openstore'] == 'no')
{
  header('Location: /residentprofile.php?resident=' . link_safe($aisle));
  exit();
}

$locid = $user['locid'];
$house = get_house_byuser($user['idnum'], $locid);

// form submittal time!
$num_items = 0;
$total_size = 0;

if($_POST['submit'] == 'Buy' && $may_buy)
{
  $itemids = array();
  $messages = array();
  $item_list = array();

  foreach($_POST as $key=>$value)
  {
    $quantity = (int)$value;
  
    if(substr($key, 0, 4) == 'buy_' && $quantity > 0)
    {
      $itemid = (int)substr($key, 4);

      $command = 'SELECT itemname,forsale,health,creator FROM monster_inventory WHERE idnum=' . $itemid . ' AND forsale>0 AND user=' . quote_smart($store_user['user']) . ' LIMIT 1';
      $item = $database->FetchSingle($command, 'fetching sample sale item');
      
      if($item === false)
      {
        $messages[] = '<span class="failure">One of the items selected is no longer available for sale.</span>';
      }
      else if($quantity == 1)
      {
        $total_cost += $item['forsale'];
        $itemids[] = $itemid;
        $messages[] = '<span class="success">Bought 1&times; ' . item_text_link($item['itemname']) . ' for ' . $item['forsale'] . '<span class="money">m</span>.</span>';
        $item_list[] = '1&times; ' . $item['itemname'];
      }
      else
      {
        // fetch items, ignoring creator
        if($store_user['stack_mystore_items'] == 'yes')
        {
          $command = '
            SELECT idnum
            FROM monster_inventory
            WHERE
              itemname=' . quote_smart($item['itemname']) . '
              AND forsale=' . $item['forsale'] . '
              AND health=' . $item['health'] . '
              AND user=' . quote_smart($store_user['user']) . '
            LIMIT ' . $quantity;
        }
        // fetch items, respecting creator
        else
        {
          $command = '
            SELECT idnum
            FROM monster_inventory
            WHERE
              itemname=' . quote_smart($item['itemname']) . '
              AND forsale=' . $item['forsale'] . '
              AND health=' . $item['health'] . '
              AND creator=' . quote_smart($item['creator']) . '
              AND user=' . quote_smart($store_user['user']) . '
            LIMIT ' . $quantity;
        }

        $items = $database->FetchMultiple($command, 'fetching items for sale');

        $num_items = count($items);

        $total_cost += $num_items * $item['forsale'];

        foreach($items as $this_item)
          $itemids[] = $this_item['idnum'];

        $messages[] = '<span class="success">Bought ' . $num_items . '&times; ' . item_text_link($item['itemname']) . ' for ' . ($num_items * $item['forsale']) . '<span class="money">m</span>.</span>';
        $item_list[] = $num_items . '&times; ' . $item['itemname'];
      }
    }
  }
  
  $item_count = count($itemids);
  
  if(count($itemids) > 0)
  {
    if($total_cost > $user['money'])
      $messages = array('<span class="failure">The selected items would cost a total of ' . $total_cost . '<span class="money">m</span>, however you only have ' . $user['money'] . '<span class="money">m</span>.</span>');
    else
    {
      $user['money'] -= $total_cost;
      take_money($user, $total_cost, $aisle . '\'s Store', implode('<br />', $item_list));

      flag_madesale($store_user['idnum']);

      $store_user['money'] += $total_cost;
      $num_items = count($items);

      $command = 'UPDATE monster_users ' .
                 "SET money=money+$total_cost,totalvalue=totalvalue+$total_cost,totalsells=totalsells+" . $item_count . ' ' .
                 'WHERE `user`=' . quote_smart($store_user['user']) . ' LIMIT 1';
      $database->FetchNone($command, 'giving store owner money');

      add_transaction($store_user['user'], $now, 'Sold from your store to ' . resident_link($user['display']), $total_cost, implode('<br />', $item_list));

      $command = '
        UPDATE monster_inventory
        SET
          `user`=' . quote_smart($user['user']) . ',
          `location`=' . quote_smart($user['incomingto']) . ',
          changed=' . $now . ',
          forsale=0,
          ' . ($store_user['stack_mystore_items'] == 'yes' ? 'creator=\'\',' : '') . '
          `message2`=' . quote_smart('Bought from ' . $store_user['display'] . '\'s store.') . '
          WHERE idnum IN (' . implode(',', $itemids) . ')
          LIMIT ' . $item_count . '
      ';

      $database->FetchNone($command, 'transfering ownership of ' . $item_count . ' item(s)');

      if($user['incomingto'] == 'storage/incoming')
        flag_new_incoming_items($user['user']);
    }
  }
}

// get the store data
if($store_user['stack_mystore_items'] == 'yes')
  $command = '
    SELECT
      COUNT(a.idnum) AS quantity,
      a.idnum,
      a.forsale,
      a.itemname,
      a.health,
      a.creator,
      b.graphictype,
      b.graphic,
      b.durability
    FROM monster_inventory AS a,monster_items AS b
    WHERE
      a.itemname=b.itemname AND
      a.user=' . quote_smart($store_user['user']) . ' AND
      location=\'storage/mystore\' AND
      forsale>0
    GROUP BY a.itemname, a.forsale, a.health
  ';
else
  $command = '
    SELECT
      COUNT(a.idnum) AS quantity,
      a.idnum,
      a.forsale,
      a.itemname,
      a.health,
      a.creator,
      b.graphictype,
      b.graphic,
      b.durability
    FROM monster_inventory AS a,monster_items AS b
    WHERE
      a.itemname=b.itemname AND
      a.user=' . quote_smart($store_user['user']) . ' AND
      location=\'storage/mystore\' AND
      forsale>0
    GROUP BY a.itemname, a.forsale, a.health, a.creator
  ';

$my_inventory = $database->FetchMultiple($command, 'fetching store items');

$num_inventory_items = count($my_inventory);

if($num_inventory_items == 0 && $user['openstore'] == 'yes')
{
  $user['openstore'] = 'no';

  $command = 'UPDATE monster_users ' .
             "SET `openstore`='no' " .
             'WHERE `user`=' . quote_smart($store_user['user']) . ' LIMIT 1';
  $database->FetchNone($command, 'userstore.php');
}

include 'commons/html.php';
?>
 <head>
<?php include 'commons/head.php'; ?>
  <title><?= $SETTINGS['site_name'] ?> &gt; Flea Market &gt; <?= htmlspecialchars($store_user['storename']) ?></title>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/fleamarket/">Flea Market</a> &gt; <?= htmlspecialchars($store_user['storename']) ?></h4>
<?php
if(count($messages) > 0)
  echo '<ul><li>' . implode('</li><li>', $messages) . '</li></ul>';

echo '<a href="residentprofile.php?resident=' . link_safe($store_user['display']) . '"><img src="shopkeep.php?id=' . $store_user['idnum'] . '" width="350" height="500" alt="" border="0" align="right" /></a>';
include 'commons/dialog_open.php';
echo '<p>Hello!</p>';
include 'commons/dialog_close.php';
?>
     <ul>
      <li><a href="residentprofile.php?resident=<?= link_safe($store_user['display']) ?>">View <?= $store_user["display"] ?>'s profile</a></li>
     </ul>
     <p>Items purchased here will be put into <?= $user['incomingto'] ?>.</p>
     <form method="post">
<?php
if($num_inventory_items > 0)
{
?>
<p><input type="submit" name="submit" value="Buy"<?= ($may_buy ? '' : ' disabled="disabled"') ?> /></p>
<table>
 <thead>
  <tr class="titlerow">
   <th colspan="2" class="centered">Quantity</th>
   <th></th>
   <th>Item</th>
   <th>Condition</th>
<?php if($store_user['stack_mystore_items'] == 'no') echo '<th>Maker</th>'; ?>
   <th>Price</th>
  </tr>
 </thead>
 <tbody>
<?php
  $rowclass = begin_row_class();

  foreach($my_inventory as $item)
  {
    $max_length = strlen($item['quantity']);
?>
  <tr class="<?= $rowclass ?>">
   <td><input name="buy_<?= $item['idnum'] ?>" style="width:60px;" maxlength="<?= $max_length ?>" type="number" min="0" max="<?= $item['quantity'] ?>" /></td>
   <td><nobr>/ <?= $item['quantity'] ?></nobr></td>
   <td class="centered"><?= item_display($item, '') ?></td>
   <td><?= $item['itemname'] ?></td>
   <td><?= durability($item['health'], $item['durability']) ?></td>
<?php if($store_user['stack_mystore_items'] == 'no') echo '<td>' . item_maker_display($item['creator'], true) . '</td>'; ?>
   <td class="righted"><?= $item['forsale'] ?><span class="money">m</span></td>
  </tr>
<?php
    $rowclass = alt_row_class($rowclass);
  }
?>
 </tbody>
</table>
<p><input type="submit" name="submit" value="Buy"<?= ($may_buy ? '' : ' disabled="disabled"') ?> /></p>
<?php
}
 else
   echo '<p>The store is empty.</p>';
?>
     </form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
