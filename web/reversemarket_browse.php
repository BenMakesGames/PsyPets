<?php
$whereat = 'marketsquare';
$wiki = 'Seller\'s Market';
$require_petload = 'no';

require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/formatting.php';
require_once 'commons/inventory.php';
require_once 'commons/sellermarketlib.php';
require_once 'commons/itemlib.php';
require_once 'commons/messages.php';
require_once 'commons/userlib.php';

if(!array_key_exists('page', $_GET))
  $page = 2;
else
  $page = (int)$_GET['page'];

$command = 'SELECT COUNT(DISTINCT(a.itemname)) AS c FROM psypets_reversemarket AS a LEFT JOIN monster_items AS b ON a.itemname=b.itemname WHERE a.bid>CEIL(b.value*' . sellback_rate() . ')';
$data = $database->FetchSingle($command, 'total items bid on');

$max_pages = ceil($data['c'] / 10);

if($page < 1 || $page > $max_pages)
  $page = 1;

$order = ($_GET['order'] == 2 ? 2 : 1);

if($order == 2)
  $command = 'SELECT SUM(a.quantity) AS total_quantity,a.itemname FROM psypets_reversemarket AS a LEFT JOIN monster_items AS b ON a.itemname=b.itemname GROUP BY(a.itemname) ORDER BY itemname ASC LIMIT ' . (($page - 1) * 10) . ',10';
else
  $command = 'SELECT SUM(a.quantity) AS total_quantity,a.itemname FROM psypets_reversemarket AS a LEFT JOIN monster_items AS b ON a.itemname=b.itemname GROUP BY(a.itemname) ORDER BY total_quantity DESC LIMIT ' . (($page - 1) * 10) . ',10';
  
$top_wanted = $database->FetchMultiple($command, 'fetching top 10 most-bid-for items');

$pages = paginate($max_pages, $page, 'reversemarket_browse.php?order=' . $order . '&page=%s');

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Seller's Market &gt; All Bids</title>
<?php include 'commons/head.php'; ?>
  <style type="text/css">
  .bid-details
  {
    background: url(//<?= $SETTINGS['static_domain'] ?>/gfx/ui/more.png) no-repeat right center;
    padding-right: 16px;
    display: block;
    height: 16px;
  }
  
  #bidding-details
  {
    border: 1px solid #666;
    border-radius: 4px;
    display: block;
    padding: 5px;
    margin-left: 400px;
    width: 300px;
    display: none;
  }
  </style>
  <script type="text/javascript">
  $(function() {
    $('.bid-details').click(function() {
      var itemname = $(this).attr('data-item-name');
      var top = (parseInt($(this).attr('data-row')) * 30);

      $('#bidding-details')
        .html('<center><img src="/gfx/throbber.gif" width="16" height="16" alt="loading..." /></center>')
        .css({'display': 'block', 'margin-top': top + 'px'})
      ;
      
      $.getJSON(
        '/biddingmarket/getdetails.php',
        { 'itemname': itemname },
        function(data)
        {
          var html = '<h5>' + itemname + '</h5><table><thead><tr><th>Bids</th><th>Price</th></tr></thead><tbody>';

          for(i in data)
            html += '<tr><td>' + data[i].qty + '</td><td>' + data[i].bid + '<span class="money">m</span></td></tr>';
            
          html += '</tbody></table>';
          
          $('#bidding-details').html(html);
        }
      );
      
      return false;
    });
  });
  </script>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4>Seller's Market &gt; All Bids</h4>
<?php
if(strlen($_GET["msg"]) > 0)
  $get_message = form_message(explode(',', $_GET['msg']));
?>
     <ul class="tabbed">
      <li><a href="reversemarket.php">Fair Offers</a></li>
      <li><a href="reversemarket_cheap.php">Cheap Offers</a></li>
      <li class="activetab"><a href="reversemarket_browse.php?page=1">All Bids</a></li>
<?php
if($user['license'] == 'yes')
  echo '<li><a href="reversemarket_bid.php">My Bids / New Bid</a></li>';
?>
     </ul>
<?= $pages ?>
<table style="float:left; margin-right:30px;">
 <thead>
  <tr class="titlerow">
   <th></th>
   <th><a href="?order=2">Item <?= ($order == 2 ? '&#9660;' : '&#9661;') ?></a></th>
   <th><a href="?order=1">Total Bids <?= ($order == 1 ? '&#9660;' : '&#9661;') ?></a></th>
  </tr>
 </thead>
 <tbody>
<?php
$rowclass = begin_row_class();

$row_i = 0;

foreach($top_wanted as $item)
{
  echo ' <tr class="' . $rowclass . '">';

  $itemdetails = get_item_byname($item['itemname']);
?>
 <tr class="<?= $rowclass ?>">
  <td class="centered"><?= item_display_extra($itemdetails) ?></td>
  <td><?= $item['itemname'] ?></td>
  <td class="centered"><a href="#" class="bid-details" data-row="<?= $row_i ?>" data-item-name="<?= $item['itemname'] ?>"><?= $item['total_quantity'] ?></a></td>
 </tr>
<?php
  $rowclass = alt_row_class($rowclass);
  $row_i++;
}
?>
</table>
<div id="bidding-details">
</div>
<div style="clear:both;"></div>
<?= $pages ?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
