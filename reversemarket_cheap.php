<?php
$whereat = 'marketsquare';
$wiki = 'Seller\'s Market';
$require_petload = 'no';

$url = 'reversemarket.php';

require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/formatting.php';
require_once 'commons/inventory.php';
require_once 'commons/sellermarketlib.php';
require_once 'commons/itemlib.php';
require_once 'commons/messages.php';
require_once 'commons/userlib.php';

if($NO_PVP)
{
  header('Location: ./lostdata.php');
  exit();
}

$messages = array();

if($_POST['submit'] == 'Sell')
{
  foreach($_POST as $key=>$value)
  {
    if(is_numeric($value) && (int)$value == $value && (int)$value > 0)
    {
      $quantity = (int)$value;
      $itemname = urldecode($key);

      $itemdetails = get_item_byname($itemname);

      if($itemdetails === false)
        $messages[] = '<span class="failure">There is no item called "' . $itemname . '"...</span>';
      else
      {
        $command = 'SELECT COUNT(idnum) AS c FROM monster_inventory WHERE itemname=' . quote_smart($itemname) . ' AND user=' . quote_smart($user['user']) . ' AND location=\'storage\' AND health=' . $itemdetails['durability'];
        $data = $database->FetchSingle($command, 'fetching item count');
        $owned = (int)$data['c'];

        if($owned == 0)
          $messages[] = '<span class="failure">You have no ' . $itemname . ' in Storage.</span>';
        else if($quantity > $owned)
          $messages[] = '<span class="failure">You do not have that many ' . $itemname . ' items in your Storage.</span>';
        else
        {
          $bid = get_highbid_byitem($itemname, 0);

          if($bid === false)
            $messages[] = '<span class="failure">There are no bids for ' . $itemname . '.  Someone must have beat you to it!</span>';
          else
          {
            $quantity = meet_bids($itemname, $quantity, $bid['bid']);

            if($quantity == 0)
              $messages[] = '<span class="failure">There are no bids for ' . $itemname . '.  Someone must have beat you to it...</span>';
            else
            {
              $command = 'DELETE FROM monster_inventory WHERE itemname=' . quote_smart($itemname) . ' AND user=' . quote_smart($user['user']) . ' AND location=\'storage\' AND health=' . $itemdetails['durability'] . ' LIMIT ' . $quantity;
              $database->FetchNone($command, 'deleting sold items');

              $money = $quantity * $bid['bid'];
              $user['money'] += $money;

              give_money($user, $money, 'Sold ' . $quantity . 'x ' . $itemname . ' at Seller\'s Market');

              $messages[] = '<span class="success">Sold ' . $quantity . 'x ' . $itemname . ' for ' . $money . '<span class="money">m</span>.</span>';

              if($quantity < (int)$_POST['quantity'])
                $messages[] = '<span class="success"> <i>(Someone must have sold ' . ((int)$_POST['quantity'] - $quantity) . ' just before you did!)</i></span>';
            } // if there are any bids (2)
          } // if there are any bids (1)
        } // if you have enough items to sell
      } // if the item exists!
    } // if value is a numeric integer greater than 0
  } // for each $_POST
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Seller's Market &gt; Cheap Offers</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4>Seller's Market &gt; Cheap Offers</h4>
<?php
if(strlen($_GET["msg"]) > 0)
  $get_message = form_message(explode(',', $_GET['msg']));

if($get_message)
  echo "<p>$get_message</p>";

if(count($messages) > 0)
  echo '<ul><li>' . implode('</li><li>', $messages) . '</li></ul>';
?>
     <ul class="tabbed">
      <li><a href="reversemarket.php">Fair Offers</a></li>
      <li class="activetab"><a href="reversemarket_cheap.php">Cheap Offers</a></li>
      <li><a href="reversemarket_browse.php?page=1">All Bids</a></li>
<?php
if($user['license'] == 'yes')
  echo '<li><a href="reversemarket_bid.php">My Bids / New Bid</a></li>';
?>
     </ul>
     <p>This page shows bids for items which you have in your Storage, but which are <strong>below</strong> Gamesell value!</p>
     <p>You would get more money Gameselling these items.</p>
<?php
$command = 'SELECT COUNT(a.idnum) AS quantity,a.itemname FROM monster_inventory AS a,monster_items AS b WHERE a.itemname=b.itemname AND a.user=' . quote_smart($user['user']) . ' AND a.location=\'storage\' AND a.health=b.durability GROUP BY a.itemname ORDER BY a.itemname ASC';
$items = $database->FetchMultiple($command, 'fetching storage inventory');

if(count($items) > 0)
{
  $anybids = false;
  $rowclass = begin_row_class();

  foreach($items as $item)
  {
    $itemdetails = get_item_byname($item['itemname']);
    $bid = get_low_highbid_byitem($item['itemname']);

    if($bid !== false && $bid['bid'] < ceil($itemdetails['value'] * sellback_rate()))
    {
      if($anybids === false)
      {
?>
     <form action="<?= $url ?>" method="post">
     <p><input type="submit" name="submit" value="Sell" /></p>
     <table>
      <tr class="titlerow">
       <th>&nbsp;</th>
       <th>Item Name</th>
       <th class="centered">Bids</th>
       <th class="centered">High Bid</th>
       <th>Quantity</th>
      </tr>
<?php
        $anybids = true;
      }
?>
      <tr class="<?= $rowclass ?>">
       <td class="centered"><?= item_display_extra($itemdetails) ?></td>
       <td><?= $item['itemname'] ?></td>
       <td class="centered"><?= $bid['quantity'] ?></td>
       <td class="centered"><?= $bid['bid'] ?><span class="money">m</span></td>
       <td><input name="<?= urlencode($item['itemname']) ?>" size="3" maxlength="3" /> / <?= $item['quantity'] ?></td>
      </tr>
<?php
      $rowclass = alt_row_class($rowclass);
    }
  }

  if($anybids === false)
    echo '<p>No one is bidding on any of the items in your Storage.</p>';
  else
  {
?>
     </table>
     <p><input type="submit" name="submit" value="Sell" /></p>
     </form>
<?php
  }
}
else
  echo '<p>You have no items in Storage.</p>';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
