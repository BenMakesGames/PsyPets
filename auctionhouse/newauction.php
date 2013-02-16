<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$wiki = 'Auction_House';
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/inventory.php';
require_once 'commons/formatting.php';
require_once 'commons/userlib.php';
require_once 'commons/messages.php';

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

if($_POST['action'] == 'host')
{
  $itemname = trim($_POST['auction-item']);
  $quantity = (int)$_POST['auction-quantity'];

  $duration = 3 * (24 * 60 * 60);

  $cost = ceil(5 * log($quantity + 1) / log(1.5));
  
  $minbid = (int)$_POST['auction-minimum-bid'];

  if($quantity <= 0)
    $CONTENT['messages'][] = '<span class="failure">You must enter a quantity.</span>';
  else if($user['money'] < $cost)
    $CONTENT['messages'][] = '<span class="failure">It would cost ' . $cost . '<span class="money">m</span> to host the auction, but you only have ' . $user['money'] . '<span class="money">m</span>.</span>';
  else if($minbid < 1)
    $CONTENT['messages'][] = '<span class="failure">The minimum bid must be at least 1<span class="money">m</span.</span>';
  else
  {
    $items = fetch_multiple('
      SELECT idnum
      FROM monster_inventory
      WHERE
        user=' . quote_smart($user['user']) . '
        AND location=\'storage\'
        AND itemname=' . quote_smart($itemname) . '
      LIMIT ' . $quantity . '
    ');

    $real_quantity = count($items);

    if($real_quantity == 0)
      $CONTENT['messages'][] = '<span class="failure">You have no such item in Storage at this time.</span>';
    else if($real_quantity < $quantity)
      $CONTENT['messages'][] = '<span class="failure">You cannot auction ' . $quantity . ', as you only have ' . $real_quantity . '.</span>';
    else
    {
      $item_ids = array();

      foreach($items as $item)
        $item_ids[] = $item['idnum'];

      $database->FetchNone('
        UPDATE monster_inventory
        SET location=\'storage/outgoing\'
        WHERE idnum IN (' . implode(',', $item_ids) . ')
        LIMIT ' . $real_quantity . '
      ');

      $moved = $database->AffectedRows();

      // unable to move all the items?! STRANGE
      if($moved < $real_quantity)
      {
        $database->FetchNone('
          UPDATE monster_inventory
          SET location=\'storage\'
          WHERE idnum IN (' . implode(',', $item_ids) . ')
          LIMIT ' . $moved . '
        ');

        $CONTENT['messages'][] = '<span class="failure">Unable to host the auction with the selected items.  Not all of the items were available (did you perhaps move, trash, or gamesell them?)</span>';
      }
      else
      {
        take_money($user, $cost, 'Auction House fee for ' . $real_quantity . '&times; ' . $itemname);

        $details = get_item_byname($itemname);

        $database->FetchNone('
          INSERT INTO psypets_auctions
          (ownerid, inventoryids, quantity, itemid, minimumbid, posttime, expirationtime)
          VALUES
          (
            ' . (int)$user['idnum'] . ',
            \'' . implode(',', $item_ids) . '\',
            ' . $real_quantity . ',
            ' . $details['idnum'] . ',
            ' . $minbid . ',
            ' . $now . ',
            ' . ($now + $duration) . '
          )
        ');

        header('Location: /auctionhouse/?msg=167');
        exit();
      }
    }
  }
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Auction House</title>
<?php include 'commons/head.php'; ?>
  <script type="text/javascript">
  function build_grouped_inventory_from_json(element_id, items)
  {
    var i, item;
  
    var table = $('<table />');
    table.append('<thead><tr><th colspan="2">Quantity</th><th></th><th>Item</th></tr></thead>');
    
    var tbody = $('<tbody />');
    
    var rowclass = 'row';
    
    for(i in items)
    {
      item = items[i];
      
      tbody.append(
        '<tr class="' + rowclass + '">' +
          '<td><input data-item-name="' + item.itemname + '" class="quantity" type="number" size="3" min="0" max="' + item.quantity + '" maxlength="' + (item.quantity + '').length + '" autocomplete="off" /></td>' +
          '<td><nobr>/ ' + item.quantity + '</nobr></td>' +
          '<td><img src="//<?= $SETTINGS['static_domain'] ?>/gfx/items/' + item.graphic + '" /></td>' +
          '<td>' + item.itemname + '</td>' +
        '</tr>'
      );
      
      rowclass = (rowclass == 'row' ? 'altrow' : 'row');
    }
    
    table.append(tbody);
    
    $(element_id).html(table);
  }
  
  $(function() {
    $('#search-submit').submit(function() {
      $('#search-results').html('<p>Searching<blink>...</blink></p>');
      $('#search-submit').attr('disabled', 'disabled');

      $.getJSON(
        '/inventory.json.php',
        {
          'location': 'storage',
          'grouped': true,
          'join': [ 'graphics' ],
          'name-part': $('#search-name').val()
        },
        function(data)
        {
          build_grouped_inventory_from_json('#search-results', data);
          $('#search-submit').removeAttr('disabled');
        }
      );

      return false;
    });

    $('#search-results').on('change', 'input.quantity', function(e) {
      $('#search-results input.quantity').not(this).val('');
      
      var qty = parseInt($(this).val());
      
      if(qty < 0)
        qty = 0;
      
      $('#auction-item').val($(this).attr('data-item-name'));
      $('#auction-quantity').val(qty);
      
      var fee = Math.ceil(5 * Math.log(qty + 1) / Math.log(1.5));
      var moneys_on_hand = parseInt($('#moneysonhand').html());
      
      $('#auction-fee').html(fee);
      
      if(fee > moneys_on_hand)
        $('#auction-fee').parent().addClass('failure');
      else
        $('#auction-fee').parent().removeClass('failure');
    });
  });
  </script>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
  <h4>Auction House</h4>
  <ul class="tabbed">
   <li><a href="/auctionhouse/">Current Auctions</a></li>
   <li class="activetab"><a href="/auctionhouse/newauction.php">Host New Auction</a></li>
  </ul>
<?php
echo '<img src="//' . $SETTINGS['static_domain'] . '/gfx/npcs/auctioneer.png" align="right" width="350" height="450" alt="(The auction house manager)" />';
include 'commons/dialog_open.php';

if($error_message)
  echo "<p>$error_message</p>";
else
  echo '<p>Search your storage for an item to auction, then choose how many of that item to auction.  Finally, set a minimum bid for the lot.  Your minimum bid will not be revealed, but if it is not met, no one will win, and the items will be returned to you.</p>';

include 'commons/dialog_close.php';
?>
<h5>Search Storage</h5>
<form id="search-submit">
<p><input type="text" id="search-name" /> <input type="submit" value="Search" /></p>
</form>
<form method="post">
<input type="hidden" name="action" value="host" />
<input type="hidden" id="auction-item" name="auction-item" value="" />
<input type="hidden" id="auction-quantity" name="auction-quantity" value="" />
<div id="search-results">
</div>
<p>Auction listing fee: <span id="auction-fee">0</span><span class="money">m</span></p>
<h5>Minimum Bid</h5>
<p><input type="number" name="auction-mininum-bid" size="8" min="1" max="10000000" maxlength="8" value="1" autocomplete="off" /><span class="money">m</span></p>
<p><input type="submit" class="bigbutton" value="Host Auction" /></p>
</form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
