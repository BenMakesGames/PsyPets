<?php
$whereat = 'bank';
$wiki = 'Trading_House';
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/messages.php';
require_once 'commons/userlib.php';
require_once 'commons/publictradinglib.php';
require_once 'commons/inventory_ajax.php';

if($NO_PVP)
{
  header('Location: /lostdata.php');
  exit();
}

if($user['license'] == 'no')
{
  header('Location: /ltc.php?dialog=2');
  exit();
}

$tradeid = (int)$_GET['id'];

$command = 'SELECT * FROM psypets_trading_house_requests WHERE idnum=' . $tradeid . ' LIMIT 1';
$trade = $database->FetchSingle($command, 'trading_public.php');

if($trade === false)
{
  header('Location: /trading_public2.php');
  exit();
}

if($trade['userid'] == $user['idnum'])
{
  header('Location: ./trading_public_view.php?id=' . $tradeid);
  exit();
}
else
{
  $my_bid = has_bid_on_trade($user['idnum'], $tradeid);

  if($my_bid !== false)
  {
    header('Location: ./trading_public_view.php?id=' . $tradeid);
    exit();
  }
}

if($_POST['action'] == 'Post Bid')
{
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

      $trade_item_table = '';
      $rowclass = begin_row_class();

      foreach($item_counts as $itemname=>$quantity)
      {
        $item_list[] = $itemname . ' &times;' . $quantity;

        $details = get_item_byname($itemname);

        $trade_item_table .= '<tr class="' . $rowclass . '"><td class="centered">' . item_display_extra($details) . '</td><td>' . $details['itemname'] . '</td><td class="centered">' . $quantity . '</td></tr>';
        $rowclass = alt_row_class($rowclass);
      }

      $trade_item_names = '<li>' . implode('</li><li>', $item_list) . '</li>';

      $command = '
        INSERT INTO psypets_trading_house_bids (userid, tradeid, timestamp, itemids, itemtext, itemtable)
        VALUES (
          ' . $user['idnum'] . ', ' . $tradeid . ', ' . $now . ', \'' . $trade_items . '\',
          ' . quote_smart($trade_item_names) . ', ' . quote_smart($trade_item_table) . '
        )
      ';
      $database->FetchNone($command, 'adding public trade bid');

      $command = 'UPDATE monster_inventory SET forsale=0,location=\'storage/outgoing\' WHERE idnum IN (' . $trade_items . ') LIMIT ' . count($idnums);
      $database->FetchNone($command, 'moving items for trade');

      $command = 'UPDATE monster_users SET new_bid=\'yes\' WHERE idnum=' . $trade['userid'] . ' LIMIT 1';
      $database->FetchNone($command, 'notifying owner of new bid');

      header('Location: ./trading_public_view.php?id=' . $tradeid);
      exit();
    }
    else
      $message_list[] = '<span class="failure">You didn\'t select any items to offer.</span>';
  } // no item error
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Trading House &gt; <?= $trade['sdesc'] ?> &gt; Place Bid</title>
<?php include 'commons/head.php'; ?>
  <script type="text/javascript" src="//<?= $SETTINGS['static_domain'] ?>/js/ajaxtrades.js"></script>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="trading_public2.php">Trading House</a> &gt; <a href="trading_public_view.php?id=<?= $tradeid ?>"><?= $trade['sdesc'] ?></a> &gt; Place Bid</h4>
     <p>Posted by <?= resident_link($owner['display']) . ' ' . duration($now - $trade['timestamp'], 2) ?> ago.</p>
<?php
if(strlen($trade['ldesc']) > 0)
  echo '<p>' . format_text($trade['ldesc']) . '</p>';
?>
     <h5>Offering</h5>
     <ul><?= $trade['itemtext'] ?></ul>
     <h5>My Bid</h5>
     <form action="trading_public_bid.php?id=<?= $tradeid ?>" method="post">
<?php select_multiple_from_storage($user['user']); ?>
     <p><input type="submit" name="action" value="Post Bid" /></p>
     </form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
