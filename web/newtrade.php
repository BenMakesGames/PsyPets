<?php
$whereat = 'newtrade';
$require_petload = 'no';
$invisible = 'yes';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/inventory.php';
require_once 'commons/formatting.php';
require_once 'commons/userlib.php';
require_once 'commons/tradelib.php';

if($user['license'] != 'yes')
{
  header('Location: /trading.php');
  exit();
}

$target_message = '';
$money_message = '';
$items_message = '';

if(strlen($_GET['user']) > 0)
  $_POST['sendto'] = $_GET['user'];

if($_POST['action'] == 'Start Trade')
{
  $trade_money = (int)$_POST['money'];

  if(strtolower($_POST['sendto']) == strtolower($user['display']))
    $target_message = '<p><span class="failure">You can\'t trade with yourself...</span></p>';
  else if(!is_numeric($_POST['money']) && $_POST['money'] != '')
    $money_message = '<p><span class="failure">Must specify a whole number amount.</span></p>';
  else if($_POST['money'] - (int)$_POST['money'] != 0)
    $money_message = '<p><span class="failure">Must specify a whole number amount.</span></p>';
  else if($trade_money < 0)
    $money_message = '<p><span class="failure">Negative amounts of money are not allowed.</span></p>';
  else if($trade_money > $user['money'])
    $money_message = '<p><span class="failure">You do not have that much money.</span></p>';
  else
  {
    $target = get_user_bydisplay($_POST['sendto'], 'idnum,license,activated,disabled');

    if($target === false)
      $target_message = '<p><span class="failure">A Resident by that name does not exist.</span></p>';
    else if(is_enemy($target, $user) || is_enemy($user, $target))
      $target_message = '<p><span class="failure">You cannot trade with this Resident.</span></p>';
    else if($target['license'] == 'no')
      $target_message = '<p><span class="failure">This Resident does not have a License to Commerce.</span></p>';
    else if($target['activated'] == 'no' || $target['disabled'] == 'yes')
      $target_message = '<p><span class="failure">A Resident by that name does not exist.</span></p>';
    else
    {
      $idnums = array();
      $item_data = array();
      $groups = array();

      foreach($_POST as $key=>$value)
      {
//          echo "$key, $value";
        if(substr($key, 0, 2) == 'i_')
        {
          $itemid = (int)substr($key, 2);

          if($value == 'yes' || $value == 'on')
          {
            $item = get_inventory_byid($itemid, 'idnum,itemname');
            if($item === false)
              continue;

            if($item['user'] != $user['user'] || $item['location'] != 'storage')
            {
              $items_message = "<p><span class=\"failure\">Some or all of the selected items are not available for trade. (They are not in your storage, or do not belong to you.)</span></p>";
              break;
            }

            $details = get_item_byname($item['itemname']);
            if($details === false)
              continue;

            if($details['noexchange'] == 'yes' || $details['cursed'] == 'yes')
            {
              $items_message = "<p><span class=\"failure\">Some or all of the selected items may not be traded.  (They are cursed, or non-exchangeable.)</span></p>";
              break;
            }

            $idnums[] = $itemid;
            $item_data[] = $item;
          }
        }
        else if(substr($key, 0, 2) == 'g_')
        {
          $itemid = (int)substr($key, 2);

          $details = get_item_byid($itemid);
          if($details === false)
            continue;

          if($details['noexchange'] == 'yes' || $details['cursed'] == 'yes')
          {
            $items_message = "<p><span class=\"failure\">Some or all of the selected items may not be traded.  (They are cursed, or non-exchangeable.)</span></p>";
            break;
          }

          $groups[$itemid] = true;
          $itemnames[$itemid] = $details['itemname'];
        }
        else if(substr($key, 0, 2) == 'n_')
        {
          $itemid = (int)substr($key, 2);

          unset($groups[$itemid]);
        }
      }

      if(strlen($items_message) == 0)
      {
        $trade_items = '';
        $trade_item_names = '';

        if(count($groups) > 0)
        {
          foreach($groups as $group=>$dummy)
          {
            $command = 'SELECT idnum,itemname FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND location=\'storage\' AND itemname=' . quote_smart($itemnames[$group]);
            $data = $database->FetchMultiple($command, 'fetching itemids');

            foreach($data as $item)
            {
              $idnums[] = $item['idnum'];
              $item_data[] = $item;
            }
          }
        }

        if(count($idnums) > 0)
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

        $q_dialog = quote_smart($_POST['dialog']);

        if($_POST['anon'] == 'yes' || $_POST['anon'] == 'on')
          $anonymous = 'yes';
        else
          $anonymous = 'no';

        if($_POST['gift'] == 'yes' || $_POST['gift'] == 'on')
          $gift = 'yes';
        else
          $gift = 'no';

        $command = 'INSERT INTO `monster_trades` ' .
                   '(`dialog`, `userid1`, `userid2`, `timestamp`, `anonymous`, `gift`, `step`, `items1`, `itemsdesc1`, `money1`) ' .
                   'VALUES ' .
                   "($q_dialog, " . $user['idnum'] . ', ' . $target['idnum'] . ', ' . $now . ', ' . quote_smart($anonymous) . ', ' . quote_smart($gift) . ', \'1\', ' . quote_smart($trade_items) . ', ' . quote_smart($trade_item_names) . ', ' . $trade_money . ')';
        $database->FetchNone($command, 'adding trade');

        if($trade_money > 0)
        {
          $database->FetchNone('
            UPDATE `monster_users`
            SET money=money-' . $trade_money . '
            WHERE `user`=' . quote_smart($user["user"]) . '
            LIMIT 1
          ');
        }

        if(count($idnums) > 0)
        {
          $command = 'UPDATE monster_inventory SET forsale=0,location=\'storage/outgoing\' WHERE idnum IN (' . implode(',', $idnums) . ') LIMIT ' . count($idnums);
          $database->FetchNone($command, 'moving items for trade');
        }

        set_new_trade_flag($target['idnum']);

        header('Location: ./trading.php');
        exit();
      } // no item error
/*
        echo "user: " . $target["user"] . "<br>\n";
        echo "money: " . $_POST["money"] . "<br>\n";
        echo "items: " . $trade_items . "<br>\n";
*/
    } // if everything else checks out
  } // if the user exists
} // if we want to trade

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; New Trade</title>
<?php include 'commons/head.php'; ?>
  <script type="text/javascript" src="//<?= $SETTINGS['static_domain'] ?>/js/ajaxtrades.js"></script>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="trading.php">Trading House</a> &gt; New Trade</h4>
<?php
if($error_message)
  echo '<p class="failure">' . $error_message . '</p>';
?>
     <p>Choose the Resident who you will trade with, and the money and/or items you will give him or her in the trade.  Once that Resident has specified the money and/or items they will give you in return, you will be given the opportunity to accept or decline the trade.</p>
     <p>You may also send a (short) message along with your trade.</p>
     <form method="post">
     <table>
      <tr>
       <th>Trade&nbsp;with:&nbsp;</th>
       <td>
        <input name="sendto" value="<?= $_POST['sendto'] ?>" style="width:120px;" />
        &nbsp;&larr;&nbsp;
        <select name="buddylist" style="width:120px;" onchange="sendto.value=buddylist.value;">
         <option value=""></option>
<?php
$friends = $database->FetchMultiple('
  SELECT b.display
  FROM psypets_user_friends AS a
    LEFT JOIN monster_users AS b
      ON a.friendid=b.idnum
  WHERE a.userid=' . (int)$user['idnum'] . '
  ORDER BY b.display ASC
');

foreach($friends as $friend)
  echo '<option value="' . $friend['display'] . '">' . $friend['display'] . '</option>';
?>
        </select>
       </td>
       <td><?= $target_message ?></td>
      </tr>
      <tr>
       <th>Anonymous:&nbsp;</th>
       <td><input type="checkbox" name="anon"<?= $_POST['anon'] == 'yes' || $_POST['anon'] == 'on' ? ' checked' : '' ?> /> (hides your name from the other Resident)</td>
       <td></td>
      </tr>
      <tr>
       <th>Gift:&nbsp;</th>
       <td><input type="checkbox" name="gift"<?= $_POST['gift'] == 'yes' || $_POST['gift'] == 'on' ? ' checked' : '' ?> /> (prevents the other player from negotiating; they may only accept or decline the gift)</td>
       <td></td>
      </tr>
      <tr>
       <th>Message:&nbsp;</th>
       <td><input name="dialog" maxlength="40" style="width:256px;" value="<?= $_POST['dialog'] ?>" /></td>
       <td valign="top"><?= $dialog_message ?></td>
      </tr>
      <tr>
       <th>Money:&nbsp;</th>
       <td><input name="money" maxlength="7" style="width:64px;" value="<?= $trade_money ?>" /><span class="money">m</span></td>
       <td><?= $money_message ?></td>
      </tr>
     </table>
     <div id="items_to_trade"></div>
     <p><button class="bigbutton" onclick="show_items_to_trade(); return false;" id="show_items_button">Show Items</button> <input type="submit" name="action" value="Start Trade" class="bigbutton" /></p>
     </form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
