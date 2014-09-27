<?php
$whereat = 'newtrade';
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/inventory.php';
require_once 'commons/formatting.php';
require_once 'commons/tradelib.php';

$tradeid = (int)$_GET['tradeid'];

$this_trade = $database->FetchSingle('SELECT * FROM `monster_trades` WHERE tradeid=' . $tradeid . ' LIMIT 1');

if($this_trade['userid2'] != $user['idnum'])
{
  header('Location: /trading.php');
  exit();
}

// have to be waiting to negotiate a trade to negotiate it
if($this_trade['step'] != 1 || $this_trade['gift'] == 'yes')
{
  header('Location: /trading.php');
  exit();
}

$target = get_user_byid($this_trade['userid1']);

if($target === false)
{
  header('Location: /trading.php');
  exit();
}

$target_message = '';
$money_message = '';
$items_messay = '';
$showitems = false;

if($_POST['submit'] == 'Propose Trade')
{
  if(!is_numeric($_POST["money"]) && $_POST["money"] != "")
    $money_message = '<p class="failure">Must specify a whole number amount.</p>';
  else if(floor($_POST["money"]) - $_POST["money"] != 0)
    $money_message = '<p class="failure">Must specify a whole number amount.</p>';
  else if($_POST["money"] < 0)
    $money_message = '<p class="failure">Negative amounts of money are not allowed.</p>';
  else if($_POST["money"] > $user["money"])
    $money_message = '<p class="failure">You do not have that much money.</p>';
  else
  {
    $item_ids = array();
    $item_data = array();

    foreach($_POST as $key=>$value)
    {
//      echo "$key, $value";
      if(is_numeric($key))
      {
        if($value == 'yes' || $value == 'on')
        {
          $item = get_inventory_byid((int)$key);
          if($item === false)
            continue;

          if($item['user'] != $user['user'] || $item['location'] != 'storage')
          {
            $items_message = "<p class=\"failure\">Some or all of the selected items are not available for trade. (They are not in your storage, or do not belong to you.)</p>";
            break;
          }

          $details = get_item_byname($item['itemname']);
          if($details === false)
            continue;

          if($details['noexchange'] == 'yes' || $details['cursed'] == 'yes')
          {
            $items_message = "<p class=\"failure\">Some or all of the selected items may not be traded.  (They are cursed, or non-exchangeable.)</p>";
            break;
          }

          $item_ids[] = (int)$key;
          $item_data[] = array('idnum' => (int)$key, 'itemname' => $item['itemname']);
        }
      }
//      echo "<br>\n";
    }
    
    if(strlen($items_message) == 0)
    {
    
      $trade_items = '';
      $trade_item_names = '';

      if(count($item_ids) > 0)
      {
        $item_counts = array();
        $item_list = array();

        foreach($item_data as $this_item)
        {
          if(strlen($trade_items) > 0)
            $trade_items .= ',';
          $trade_items .= $this_item['idnum'];

          $item_counts[$this_item['itemname']]++;
        }

        foreach($item_counts as $itemname=>$quantity)
          $item_list[] = $itemname . ';' . $quantity;

        $trade_item_names = implode('<br />', $item_list);
      }

      $command = 'UPDATE `monster_trades` ' .
                 'SET step=2, ' .
                     'items2=' . quote_smart($trade_items) . ', ' .
                     'itemsdesc2=' . quote_smart($trade_item_names) . ', ' .
                     'money2=' . (int)$_POST['money'] . ', ' .
                     'dialog=' . quote_smart($_POST['dialog']) . ', ' .
                     'timestamp=' . $now . ' ' .
                'WHERE tradeid=' . $tradeid . ' LIMIT 1';

      $database->FetchNone($command, 'updating trade');

      if((int)$_POST['money'] > 0)
      {
        $command = 'UPDATE `monster_users` SET money=money-' . (int)$_POST['money'] . ' WHERE `user`=' . quote_smart($user['user']) . ' LIMIT 1';
        $database->FetchNone($command, 'deducting money from account for trade');
      }
        
      if(count($item_ids) > 0)
      {
        $command = 'UPDATE monster_inventory SET location=\'storage/outgoing\',forsale=0 WHERE idnum IN (' . implode(',', $item_ids) . ') LIMIT ' . count($item_ids);
        $database->FetchNone($command, 'removing items from inventory');
      }

      set_new_trade_flag($target['idnum']);
      consider_new_trade_flag($user['idnum']);

      header('Location: /trading.php');
      exit();
/*
      echo "user: " . $target["user"] . "<br>\n";
      echo "money: " . $_POST["money"] . "<br>\n";
      echo "items: " . $trade_items . "<br>\n";
*/
    } // error with the items
  } // if we specified a good amount of money
} // if we want to trade
else if($_GET['showitems'] == 1)
{
  $_POST['dialog'] = stripslashes($this_trade['dialog']);
  $showitems = true;
}
else
  $_POST['dialog'] = stripslashes($this_trade['dialog']);

$my_inventory = get_inventory($whereat, "", $user);
$num_inventory_items = count($my_inventory);

if($this_trade['anonymous'] == 'yes')
  $trade_with = '<i>anonymous</i>';
else
  $trade_with = '<a href="residentprofile.php?resident=' . link_safe($target['display']) . '">' . $target['display'] . '</a>';

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Negotiate Trade</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
  <h5>Negotiate Trade</h5>
  <p>Respond to this Resident's offer, and propose the trade.  If <?= $trade_with ?> approves, the trade will go through and the goods will be exchanged.  If this trade is not acceptable, the goods will be returned to their respective owners.</p>
  <form method="post">
   <table>
    <tr>
     <th>Trade&nbsp;with:&nbsp;</th>
     <td><?= $trade_with ?></td>
     <td><?= $target_message ?></td>
    </tr>
    <tr>
     <th>Message:&nbsp;</th>
     <td><input name="dialog" maxlength="40" style="width:256px;" value="<?= $_POST['dialog'] ?>" /></td>
     <td valign="top"><?= $dialog_message ?></td>
    </tr>
    <tr>
     <th>Money:&nbsp;</th>
     <td>
      <input name="money" maxlength="7" style="width:64px;" value="<?= $_POST['money'] ?>" /><span class="money">m</span>
     </td>
     <td><?= $money_message ?></td>
    </tr>
<?php
if($showitems == true)
{
?>
    <tr>
     <th>Items:&nbsp;</th>
     <td></td>
     <td><?= $items_message ?></td>
    </tr>
<?php
}
?>
   </table>
<?php
if($showitems == true)
{
  if($num_inventory_items > 0)
    display_inventory($whereat, $my_inventory, $user, $userpets);
}

echo '<p>';

if($showitems == false)
  echo '<input type="button" value="Show Items" onclick="location.href=\'negotiatetrade.php?tradeid=' . $tradeid . '&showitems=1\'" class="bigbutton" />';

echo ' <input type="submit" name="submit" value="Propose Trade" class="bigbutton" /></p>';
?>
  </form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
