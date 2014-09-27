<?php
$whereat = 'storage';
$wiki = 'Storage#Seized';

$THIS_ROOM = 'Storage';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/inventory.php';
require_once 'commons/formatting.php';
require_once 'commons/messages.php';
require_once 'commons/houselib.php';

$locid = $user['locid'];
$house = get_house_byuser($user['idnum'], $locid);

if($house["locid"] != $locid)
{
  echo "Failed to load your house.<br />\n";
  exit();
}

if(strlen($_POST['submit']) > 0)
{
  $itemids = array();

  foreach($_POST as $key=>$value)
  {
    if(is_numeric($key))
    {
      if($value == 'yes' || $value == 'on')
        $itemids[] = (int)$key;
    }
  }

  if($_POST['submit'] == 'Throw Out' && count($itemids) > 0)
  {
    $command = 'DELETE FROM monster_inventory WHERE location=\'seized\' AND user=' . quote_smart($user['user']) . ' AND idnum IN (' . implode(',', $itemids) . ') LIMIT ' . count($itemids);
    $database->FetchNone($command, 'storage_seized.php');
    $error_message = '<span class="success">Threw out ' . count($itemids) . ' item' . (count($itemids) != 1 ? 's' : '') . '.</span>';
  }
  else if($_POST['submit'] == 'Buy' && count($itemids) > 0)
  {
    $total_cost = 0;

		$items = $database->FetchMultiple('SELECT idnum,itemname,changed FROM monster_inventory WHERE location=\'seized\' AND user=' . quote_smart($user['user']) . ' AND idnum IN (' . implode(',', $itemids) . ') LIMIT ' . count($itemids));
    
    if(count($items) > 0)
    {
      $for_real_ids = array();

      foreach($items as $this_item)
      {
        $for_real_ids[] = $this_item['idnum'];

        $days = ceil(($now - $this_item['changed']) / (60 * 60 * 24));

        $details = get_item_byname($this_item['itemname']);

        $total_cost += ceil($details['bulk'] * $days / 100);
      }

      if($user['money'] >= $total_cost)
      {
        $command = 'UPDATE monster_inventory SET location=\'storage\',changed=' . $now . ' WHERE idnum IN (' . implode(',', $for_real_ids) . ') LIMIT ' . count($for_real_ids);
        $database->FetchNone($command, 'storage_seized.php');
        $error_message = '<span class="success">You bought back ' . count($for_real_ids) . ' item' . (count($for_real_ids) > 1 ? 's' : '') . ', which have been put into your storage.  Total cost was ' . $total_cost . ' moneys.</span>';
        
        take_money($user, $total_cost, 'Bought back ' . count($for_real_ids) . ' seized item' . (count($for_real_ids) > 1 ? 's' : '') . '.');
        $user['money'] -= $total_cost;
      }
      else
        $error_message = '<span class="failure">It would cost you ' . $total_cost . ' moneys to buy the selected item' . (count($for_real_ids) > 1 ? 's' : '') . ', however you only have ' . $user['money'] . '.</span>';
    }
  }
}

$items = $database->FetchMultiple('SELECT * FROM monster_inventory WHERE location=\'seized\' AND user=' . quote_smart($user['user']) . ' ORDER BY itemname ASC');

$item_count = count($items);

if($item_count == 0)
{
  header('Location: ./storage.php');
  exit();
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $user["display"] ?>'s Seized Storage</title>
<?php include 'commons/head.php'; ?>
  <script type="text/javascript">
   function check_all()
   {
     f = document.getElementById('itemlist');
     i = f.elements.length;
     for(j = 1; j < i; ++j)
       f.elements[j].checked = document.getElementById('checkall').checked;
   }
  </script>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><?= $user['display'] ?>'s Seized Storage</h4>
<?php
if(strlen($_GET['msg']) > 0)
  $error_message = form_message(explode(',', $_GET['msg']));

if($error_message)
  echo "<p>$error_message</p>";

room_display($house);
?>
    <p>Items seized must be bought back at a cost in moneys equal to their sizes.  Every day an item remains seized, its cost to retreive is increased by its size.</p>
    <form action="storage_seized.php" method="post" id="itemlist">
     <table>
      <tr>
       <td><input type="submit" name="submit" value="Buy" style="width:100px;" /></td>
       <td><input type="submit" name="submit" value="Throw Out" style="width:100px;" onclick="return confirm('Really throw out these items?');" /></td>
      </tr>
     </table>
<table>
 <tr class="titlerow">
  <th><input type="checkbox" id="checkall" onclick="check_all()" /></th>
  <th></th>
  <th>Size/Weight</th>
  <th>Name</th>
  <th>Maker</th>
  <th>Comment</th>
  <th>Seizure Date</th>
  <th>Buy&nbsp;Cost</th>
 </tr>
<?php
  $rowstyle = begin_row_class();
  $total_cost = 0;

  foreach($items as $my_item)
  {
    $item = get_item_byname($my_item['itemname']);

    $rowstyle = alt_row_class($rowstyle);

    $namevalue = $my_item["idnum"];

    $itemmaker = item_maker_display($my_item['creator']);
    
    $days = ceil(($now - $my_item['changed']) / (60 * 60 * 24));

    $total_cost += ceil($item['bulk'] * $days / 100);
?>
 <tr class="<?= $rowstyle ?>">
  <td><input type="checkbox" name="<?= $namevalue ?>"<?= ($_POST[$namevalue] == 'on' || $_POST[$namevalue] == "yes" ? " checked" : "") ?> /></td>
  <td class="centered"><?= item_display_extra($item, "", ($user['inventorylink'] == 'yes')) ?></td>
  <td class="centered"><?= ($item['bulk'] / 10) . ' / ' . ($item['weight'] / 10) ?></td>
  <td><?= $item['itemname'] ?></td>
  <td><?= $itemmaker ?></td>
  <td><?= format_text($my_item['message']) . '<br />' .
    (strlen($my_item["message2"]) > 0 ? format_text($my_item['message2']) . '<br />' : '') ?></td>
  <td class="centered"><?= local_time($my_item['changed'], $user['timezone'], $user['daylightsavings']) ?></td>
  <td align="right"><?= ceil($item['bulk'] * $days / 100) ?><span class="money">m</span></td>
 </tr>
<?php
  }

  if($item_count > 1)
  {
?>
 <tr>
  <td></td>
  <td colspan="6"><b>Total cost to retreive all <?= $item_count ?> items:</b></td>
  <td align="right"><b><?= $total_cost ?><span class="money">m</span></b></td>
 </tr>
<?php
  }
?>
  
</table>
     <table>
      <tr>
       <td><input type="submit" name="submit" value="Buy" style="width:100px;" /></td>
       <td><input type="submit" name="submit" value="Throw Out" style="width:100px;" onclick="return confirm('Really throw out these items?');" /></td>
      </tr>
     </table>
     </form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
