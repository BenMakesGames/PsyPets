<?php
$whereat = 'marketsquare';
$wiki = 'Seller\'s Market';
$require_petload = 'no';

require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/inventory.php';
require_once 'commons/formatting.php';
require_once 'commons/sellermarketlib.php';
require_once 'commons/itemlib.php';
require_once 'commons/messages.php';

if($user['license'] == 'no')
{
  header('Location: ./ltc.php');
  exit();
}

$max_quantity_allowed = 1000;
$current_quantity = 0;

$sort = (int)$_GET['sort'];

$bids = get_bids_byuser($user['idnum'], $sort);

foreach($bids as $bid)
  $current_quantity += $bid['quantity'];

$quantity_allowed = $max_quantity_allowed - $current_quantity;

if($_POST['submit'] == 'Place Bid')
{
  $item = get_item_byname($_POST['itemname']);
  
  if($item === false)
  {
    $error_message = '<span class="failure">There is no item called "' . $_POST['itemname'] . '" (<a href="encyclopedia.php">Browse the Encyclopedia</a>).</span>';
  }
  else if($item['custom'] != 'no')
  {
    $error_message = '<span class="failure">You may not place bids on custom items or monthly items.</span>';
  }
  else if($item['cursed'] == 'yes' || $item['noexchange'] == 'yes')
  {
    $error_message = '<span class="failure">The named item cannot be exchanged between players.</span>';
  }
  else
  {
    $quantity = (int)$_POST['quantity'];
    $bid = (int)$_POST['bid'];
    
    $total_bid = ceil($quantity * $bid * (1 + sellers_fee()));
    
    if($quantity < 1)
    {
      $message = '<span class="failure">You must bid for at least 1 item...</span>';
      $prefill_itemname = $item['itemname'];
      $prefill_value = $bid;
    }
/*
    else if($bid < ceil($item['value'] * sellback_rate()))
    {
      $message = '<span class="failure">You cannot place a bid below the current sellback value of an item (the current sellback for ' . $item['itemname'] . ' is ' . ceil($item['value'] * sellback_rate()) . '<span class="money">m</span>).</span>'; 
      $prefill_itemname = $item['itemname'];
      $prefill_quantity = $quantity;
    }
*/
    else if($quantity > $quantity_allowed)
    {
      $message = '<span class="failure">You may not bid on more than ' . $max_quantity_allowed . ' items at a time.';

      if($current_quantity > 0)
        $message .= ' <i>(You currently have bids on ' . $current_quantity . ', and therefore may not bid for more than ' . $quantity_allowed . '.)</i>'; 

      $message .= '</span>';
      
      $prefill_itemname = $item['itemname'];
      $prefill_value = $bid;
    }
    else if($total_bid == 0)
    {
      $message = '<span class="failure">You may not place a bid for 0<span class="money">m</span>...';
    }
    else if($user['money'] < $total_bid)
    {
      $message = '<span class="failure">You do not have enough money to pay for the items wanted.</span>';
      $prefill_itemname = $item['itemname'];
      $prefill_value = $bid;
      $prefill_quantity = $quantity;
    }
    else
    {
      if(add_sellermarketbid($item['itemname'], $user['idnum'], $quantity, $bid))
      {
        $bids[] = get_bid_byid($database->InsertID());

        take_money($user, $total_bid, 'Seller\'s Market fee', 'Bid on ' . $quantity . 'x ' . $item['itemname'] . ' for ' . $bid . ' each');
        $user['money'] -= $total_bid;

        $message = '<span class="success">Success!</span>';

        $quantity_allowed -= $quantity;
        $total_quantity += $quantity_allowed;
        $current_quantity += $quantity;
      }
      else
        $message = '<span class="failure">Failed to list item for no good reason (database problems).  Please try again, and if this continues, notify an administrator.</span>';
    }
  }
}
else if($_POST['submit'] == 'Cancel')
{
  foreach($_POST as $key=>$value)
  {
    if(substr($key, 0, 3) == 'bid' && ($value == 'yes' || $value == 'on'))
    {
      $bidid = substr($key, 3);
      $bid = get_sellermarket_byid($bidid);
      
      $refund = 0;
      
      if($bid['buyer'] == $user['idnum'])
      {
        $refund += $bid['bid'] * $bid['quantity'];
        delete_sellermarket_byid($bidid);
      }

      if($refund > 0)
      {
        give_money($user, $refund, 'Seller\'s Market refund');
        $msgs[] = '118:' . $refund;
      }
    }
  }
  
  if(count($msgs) > 0)
  {
    header('Location: ./reversemarket_bid.php?msg=' . implode(',', $msgs));
    exit();
  }
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Seller's Market &gt; Place Bid</title>
  <script type="text/javascript">
  function confirm_bid()
  {
    var value = Math.ceil(document.getElementById('quantity').value * document.getElementById('bid').value * <?= (1 + sellers_fee()) ?>);
    return confirm('Your total payment: ' + value + ' moneys.'); 
  }
  </script>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="reversemarket.php">Seller's Market</a> &gt; Place Bid</h4>
<?= $message ? '<p>' . $message . '</p>' : '' ?>
<?php
if(strlen($_GET['msg']) > 0)
  $error_message = form_message(explode(',', $_GET['msg']));

if($error_message)
  echo "<p>$error_message</p>";
?>
     <ul class="tabbed">
      <li><a href="reversemarket.php">Fair Offers</a></li>
      <li><a href="reversemarket_cheap.php">Cheap Offers</a></li>
      <li><a href="reversemarket_browse.php?page=1">All Bids</a></li>
<?php
if($user['license'] == 'yes')
  echo '<li class="activetab"><a href="reversemarket_bid.php">My Bids / New Bid</a></li>';
?>
     </ul>
<?php
if($quantity_allowed > 0)
{
?>
     <p>Enter the item you want, how many you want, and how much you are willing to pay <strong>for each one</strong> (not for the total quantity).</p>
     <p>The total amount must be paid up front, plus a <?= sellers_fee() * 100 ?>% listing fee.  You may later rescind a bid and reclaim the money, however the listing fee will not be refunded.</p>
     <form action="/reversemarket_bid.php" method="post">
     <table>
      <tr>
       <th>Itemname:</th>
       <td><input name="itemname" value="<?= $prefill_itemname ?>" /></td>
      </tr>
      <tr>
       <th>Quantity:</th>
       <td><input name="quantity" maxlength="4" size="4" id="quantity" value="<?= $prefill_quantity ?>" /></td>
      </tr>
      <tr>
       <th>Bid (for one):</th>
       <td><input name="bid" maxlength="7" size="7" id="bid" value="<?= $prefill_value ?>" /></td>
      </tr>
     </table>
     <p><input type="submit" name="submit" value="Place Bid" onclick="return confirm_bid()" /></p>
     </form>
<?php
}
else
  echo '<p>You may not place bids for more than ' . $max_quantity_allowed . ' items.</p>';

if(count($bids) > 0)
{
  $item_sort = '<a href="reversemarket_bid.php?sort=0">&#9651;</a>';
  $bid_sort = '<a href="reversemarket_bid.php?sort=1">&#9661;</a>';
  $quantity_sort = '<a href="reversemarket_bid.php?sort=2">&#9661;</a>';

  if($sort == 1)
    $bid_sort = '&#9660;';
  else if($sort == 2)
    $quantity_sort = '&#9660;';
  else
    $item_sort = '&#9650;';
?>
<h5>Current Bids (<?= $current_quantity . '/' . $max_quantity_allowed ?> items)</h5>
<form action="reversemarket_bid.php" method="post">
<table>
 <tr class="titlerow">
  <th></th>
  <th></th>
  <th>Item <?= $item_sort ?></th>
  <th>Quantity <?= $quantity_sort ?></th>
  <th>Your Bid <?= $bid_sort ?></th>
  <th>High Bid</th>
  <th>Sellback</th>
 </tr>
<?php
  $rowclass = begin_row_class();

  foreach($bids as $bid)
  {
    $details = get_item_byname($bid['itemname']);
    $highbid = get_highbid_byitem($bid['itemname']);
    
    if($details['nosellback'] == 'yes')
      $sellback = '&mdash;';
    else
      $sellback = ceil($details['value'] * sellback_rate()) . '<span class="money">m</span>';
?>
 <tr class="<?= $rowclass ?>">
  <td><input type="checkbox" name="bid<?= $bid['idnum'] ?>" /></td>
  <td class="centered"><?= item_display($details, '') ?></td>
  <td><?= $bid['itemname'] ?></td>
  <td class="centered"><?= $bid['quantity'] ?></td>
  <td class="centered"><?= $bid['bid'] ?><span class="money">m</span></td>
  <td class="centered"><?= ($highbid === false ? 'none' : $highbid['bid'] . '<span class="money">m</span>') ?></td>
  <td class="centered"><?= $sellback ?></td>
 </tr>
<?php
    $rowclass = alt_row_class($rowclass);
  }
?>
</table>
<p><input type="submit" name="submit" value="Cancel" /></p>
</form>
<?php
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
