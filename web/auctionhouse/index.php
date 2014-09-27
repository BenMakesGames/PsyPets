<?php
require_once 'commons/init.php';

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

$data = fetch_single('
  SELECT COUNT(idnum) AS c
  FROM psypets_auctions
  WHERE expirationtime>' . $now . '
  ORDER BY posttime DESC
');

$num_auctions = (int)$data['c'];

if($num_auctions > 0)
{
  $num_pages = ceil($num_auctions / 20);
  if($page < 1)
    $page = 1;
  else if($page > $num_pages)
    $page = $num_pages;

  $auction_items = fetch_multiple('
    SELECT a.*,c.amount
    FROM psypets_auctions AS a
      LEFT JOIN psypets_auction_bids AS c ON
        a.idnum=c.auctionid
        AND c.userid=' . $user['idnum'] . '
    WHERE a.expirationtime>' . $now . '
    ORDER BY a.posttime DESC
    LIMIT ' . (($page - 1) * 20) . ',20
  ');
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Auction House</title>
<?php include 'commons/head.php'; ?>
  <script type="text/javascript">
  $(function() {
    $('.bid-button').click(function() {
      var button = $(this);

      button.attr('disabled', 'disabled');
      $('#bid-' + auction_id).attr('disabled', 'disabled');

      var auction_id = parseInt(button.attr('data-auction-id'));
      var amount = parseInt($('#bid-' + auction_id).val());

      $.ajax({
        type: 'POST',
        dataType: 'json',
        url: '/auctionhouse/placebid.json.php',
        data: {
          'auction': auction_id,
          'amount': amount
        },
        success: function(data) {
          button.removeAttr('disabled');
          
          if(data.moneys_on_hand)
          {
            $('#moneysonhand').html(data.moneys_on_hand).parent().animateFlash('#336699');
          }

          if(data.bid && data.auctionid)
            $('#bid-' + data.auctionid).val(data.bid);

          $('#bid-' + data.auctionid).removeAttr('disabled');

          var i;

          if(data.messages)
          {
            $('#json-messages').slideUp(400, function() {
              $('#json-messages').html('');
            
              for(i in data.messages)
                $('#json-messages').append('<li>' + data.messages[i] + '</li>');

              $('#json-messages').slideDown();
            });
          }
        }
      });
    });

    $('a.cancel-auction').click(function() {
      if(confirm('Really cancel this auction?'))
        $(this).parents('form').submit();

      return false;
    });
  });
  </script>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
  <h4>Auction House</h4>
  <ul class="tabbed">
   <li class="activetab"><a href="/auctionhouse/">Current Auctions</a></li>
   <li><a href="/auctionhouse/newauction.php">Host New Auction</a></li>
  </ul>
<?php
echo '<img src="//' . $SETTINGS['static_domain'] . '/gfx/npcs/auctioneer.png" align="right" width="350" height="" alt="(The auction house manager)" />';
include 'commons/dialog_open.php';

if($error_message)
  echo "<p>$error_message</p>";
else
  echo '<p>Welcome to my Auction house, available exclusively to those with a License to Commerce.</p>' .
       '<p>If you\'d like to host an auction of your own, please let me know.</p>';

include 'commons/dialog_close.php';

if($num_auctions > 0)
{
  if($num_pages > 1)
  {
    $pages = paginate($num_pages, $page, '?page=%s');

    echo $pages;
  }

?>
  <p><i>(You may cancel your own auctions by clicking the <b style="color:red;">X</b> beside it.  "Bidding" on your own auctions changes the minimum bid, and does not cost moneys.)</i></p>
  <ul id="json-messages"></ul>
  <table>
   <thead>
    <tr>
     <th></th>
     <th></th>
     <th></th>
     <th>Item</th>
     <th class="centered">Time Remaining</th>
     <th><nobr>My Bid</nobr></th>
     <th></th>
    </tr>
   </thead>
   <tbody>
<?php
  $rowclass = begin_row_class();

  foreach($auction_items as $item)
  {
    $details = get_item_byid($item['itemid']);

    if($details['custom'] == 'yes')
      $custom = '<br />(custom item)';
    else if($details['custom'] == 'monthly')
      $custom = '<br />(erstwhile item)';
    else if($details['custom'] == 'recurring')
      $custom = '<br />(favor item)';
    else
      $custom = '';
      
    $your_bid = ($item['ownerid'] == $user['idnum'] ? $item['minimumbid'] : (int)$item['amount']);
?>
    <tr class="<?= $rowclass ?>">
     <td><?php
    if($item['ownerid'] == $user['idnum'])
      echo '<form action="/auctionhouse/cancelauction.php" method="post"><input type="hidden" name="id" value="' . $item['idnum'] . '" /><a style="font-weight:bold; color:red;" href="#" class="cancel-auction">X</a></form>';
?></td>
     <td class="righted"><?= $item['quantity'] ?>&times;</td>
     <td class="centered"><?= item_display_extra($details) ?></td>
     <td><?= $details['itemname'] . $custom ?></td>
     <td class="centered"><?= duration($item['expirationtime'] - $now, 2) ?></td>
     <td><input type="number" min="0" max="10000000" maxlength="8" size="8" value="<?= $your_bid ?>" id="bid-<?= $item['idnum'] ?>" /></td>
     <td><input type="submit" value="Place Bid" class="bid-button" data-auction-id="<?= $item['idnum'] ?>" /></td>
    </tr>
<?php
    $rowclass = alt_row_class($rowclass);
  }
?>
   </tbody>
  </table>
<?php
  if($num_pages > 1)
    echo $pages;
}
else
  echo '<p>There are no items up for auction at this time.</p>';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
