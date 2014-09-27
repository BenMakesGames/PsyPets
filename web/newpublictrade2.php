<?php
$whereat = 'bank';
$wiki = 'Trading_House';
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/inventory.php';
require_once 'commons/formatting.php';
require_once 'commons/messages.php';
require_once 'commons/userlib.php';
require_once 'commons/inventory_ajax.php';
require_once 'commons/publictradinglib.php';
require_once 'commons/psypetsformatting.php';
require_once 'commons/economylib.php';

if($NO_PVP)
{
  header('Location: ./lostdata.php');
  exit();
}

if($user['license'] == 'no')
{
  header('Location: ./ltc.php?dialog=2');
  exit();
}

$command = 'SELECT COUNT(idnum) AS c FROM psypets_trading_house_requests WHERE userid=' . $user['idnum'];
$data = $database->FetchSingle($command, 'trading_public.php');

$my_trade_count = (int)$data['c'];

$post_cost = post_public_trade_cost($my_trade_count);

if($post_cost >= $user['money'])
{
  header('Location: ./trading_public2.php');
  exit();
}

if($_POST['action'] == 'Post Trade')
{
  $sdesc = trim($_POST['sdesc']);
  $ldesc = trim($_POST['ldesc']);

  if($sdesc == '')
    $message_list[] = '<span class="failure">You need to tell us what you\'re asking for!</span>';
  else
  {
    $idnums = array();
    $item_data = array();
    $groups = array();
    $errored = false;

    foreach($_POST as $key=>$value)
    {
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
            $message_list[] = '<span class="failure">Some or all of the selected items are not available for trade. (They are not in your storage, or do not belong to you.)</span>';
            $errored = true;
            break;
          }

          $details = get_item_byname($item['itemname']);
          if($details === false)
            continue;

          if($details['noexchange'] == 'yes' || $details['cursed'] == 'yes')
          {
            $message_list[] = '<span class="failure">Some or all of the selected items may not be traded.  (They are cursed, or non-exchangeable.)</span>';
            $errored = true;
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
          $message_list[] = '<span class="failure">Some or all of the selected items may not be traded.  (They are cursed, or non-exchangeable.)</span>';
          $errored = true;
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

    if(!$errored)
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
          $item_counts[$this_item['itemname']]++;

        $trade_items = implode(',', $idnums);

        foreach($item_counts as $itemname=>$quantity)
          $item_list[] = item_text_link($itemname) . ' &times;' . $quantity;

        $trade_item_names = '<li>' . implode('</li><li>', $item_list) . '</li>';

        $command = '
          INSERT INTO psypets_trading_house_requests (userid, timestamp, itemids, itemtext, sdesc, ldesc)
          VALUES (
            ' . $user['idnum'] . ', ' . $now . ', \'' . $trade_items . '\',
            ' . quote_smart($trade_item_names) . ', ' . quote_smart($sdesc) . ',
            ' . quote_smart($ldesc) . '
          )
        ';
        $database->FetchNone($command, 'adding public trade');

        $command = 'UPDATE monster_inventory SET forsale=0,location=\'storage/outgoing\' WHERE idnum IN (' . $trade_items . ') LIMIT ' . count($idnums);
        $database->FetchNone($command, 'moving items for trade');

        take_money($user, $post_cost, 'Public trade posting fee');

        header('Location: ./trading_public2.php');
        exit();
      }
      else
        $message_list[] = '<span class="failure">You didn\'t select any items to trade.</span>';
    } // no item error
  }
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Trading House &gt; New Public Trade</title>
<?php include 'commons/head.php'; ?>
  <script type="text/javascript" src="//<?= $SETTINGS['static_domain'] ?>/js/ajaxtrades.js"></script>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="trading_public2.php">Trading House</a> &gt; New Public Trade</h4>
<?php
if($post_cost > 0)
{
  echo '
    <p>Since you have ' . $my_trade_count . ' open public trade' . ($my_trade_count != 1 ? 's' : '') . ', it will cost you ' . $post_cost . '<span class="money">m</span> to post another.  The more open trades you have, the more it costs to post another.  It is always free to post a public trade if you have no other open public trades.</p>
    <p>This fee will not be refunded if you later decide to cancel the trade.</p>
  ';
}
?>
     <form action="newpublictrade2.php" method="post">
     <h5>Asking</h5>
     <p>A short description stating what you want for trade.  Trading House searches will search this description.</p>
     <p><input type="text" name="sdesc" maxlength="40" style="width:500px;" value="<?= $sdesc ?>" /></p>
     <h5>Long Description (optional)</h5>
     <p>For additional conditions, or anything else you want to say.  Trading House searches do not search this description.</p>
     <p><textarea cols="80" rows="6" style="width:500px;" name="ldesc"><?= $ldesc ?></textarea></p>
     <h5>Items for Trade</h5>
     <p>Only items from your Storage are listed here.  You may select a range of items by clicking the first, then holding shift while clicking the last.</p>
     <p><input type="submit" name="action" value="Post Trade" /></p>
<?php select_multiple_from_storage($user['user']); ?>
     <p><input type="submit" name="action" value="Post Trade" /></p>
     </form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
