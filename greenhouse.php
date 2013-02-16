<?php
$wiki = 'The_Greenhouse';
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/inventory.php';
require_once 'commons/formatting.php';
require_once 'commons/userlib.php';
require_once 'commons/itemlib.php';
require_once 'commons/hungryvalue.php';

// value of items produced / cost to get plant should equal ~15
// ex: 8 steak x 75m/steak = 600; 600 / 40 (cost ot steakvine) = 15
$items_out = array(
  2 => array('Amethyst Rose Bush', 70), // 18 Amethyst Roses + 1 Wood (55 x 18 + 95 = 1085)
  1 => array('Appleberry Bush', 25),    // 18 Redsberry/Delicious + 1 Wood ((15 + 20) / 2 x 18 + 95 = 410)
  3 => array('Bonsai Tree', 50),        // nothing
 11 => array('Camomile Seeds', 5),      //
 13 => array('Extra Virgin Olive Tree', 500), // SPECIAL
  4 => array('Minipalm', 35),           // 8 Coconuts + 1 Log (35 x 8 + 95 x 2.5 = 517.5)
  9 => array('Potted Cornstalk', 40),   // 8 Corn (75 x 8 = 600)
 12 => array('Potted Magic Oakle', 450), // STUFF
  5 => array('Potted Orange Tree', 45), // 12 Oranges + 1 Log (40 x 12 + 95 x 2.5 = 717.5)
  6 => array('Potted Rubber Tree', 80), // 10 Rubber + 1 Log (95 x 10 + 95 x 2.5 = 1187.5)
  8 => array('Potted Steakvine', 40),   // 8 Steak (75 x 8 = 600)
  7 => array('Potted Sugar Beets', 15), // 9 Sugar Beets (30 x 9 = 270)
 10 => array('Potted Wheat', 35),       // 7 Wheat (60 x 7 = 560)
);

if($now_month == 12)
{
  $items_out[100] = array('Candy Cane Tree', 20);   // 6 Candy Canes + 1 Log (30 x 6 + 95 x 2.5 = 417.5)
  if($now_day >= 25 - 7 + 1 && $now_day <= 25)
    $items_out[101] = array('Potted Pine', 200); // COULD BE ANYTHING
}

if($_POST['submit'] == 'Buy')
{
  $get_items = array();
  $cost = 0;
  $total_items = 0;

  foreach($_POST as $key=>$value)
  {
    if(substr($key, 0, 2) == 'i_' && (int)$value > 0)
    {
      $out = (int)substr($key, 2);

      $quantity = (int)$value;
      
      if(array_key_exists($out, $items_out))
      {
        $cost += $quantity * $items_out[$out][1];
        $total_items += $quantity;
        $get_items[$items_out[$out][0]] += $quantity;
      }
    }
  }

  if($cost > $user['greenhouse_points'])
    $dialog = '<p>That would cost you a total of ' . $cost . ' points, however you only have ' . $user['greenhouse_points'] . '!</p>';
  else if($cost > 0)
  {
    $command = 'UPDATE monster_users SET greenhouse_points=greenhouse_points-' . $cost . ' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
    $database->FetchNone($command, 'taking greenhouse points');

    $user['greenhouse_points'] -= $cost;
  
    foreach($get_items as $itemname=>$quantity)
      add_inventory_quantity($user['user'], 'u:28355', $itemname, 'Purchased from The Greenhouse', 'storage/incoming', $quantity);

    flag_new_incoming_items($user['user']);

    $dialog = '<p>Great!  You\'ll find everything in <a href="incoming.php">Incoming</a>.</p>';

    require_once 'commons/statlib.php';
    record_stat($user['idnum'], 'Bought a Plant at the Greenhouse', $total_items);
  }
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; The Florist &gt; Greenhouse &gt; Buy Plants</title>
<?php include 'commons/head.php'; ?>
  <script type="text/javascript">
   $(function() {
     $('.quantity').keyup(function() {
       $(this).change();
     });
   
     $('.quantity').change(function() {
			 var total = 0;
		 
			 $('.quantity').each(function() {

			   var quantity = parseInt($(this).val());
				 var price = parseInt($(this).parent().parent().find('.price').html());
				 
				 if(!isNaN(price) && !isNaN(quantity))
					total += quantity * price;
			 });
			 
			 $('#total').html(total);
		 });
   });
	</script>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
  <?= ($check_message ? "<p style=\"color:blue;\">$check_message</p>" : "") ?>
  <h4>Recycling &gt; Greenhouse</h4>
  <ul class="tabbed">
   <li><a href="recycling.php">Recycling</a></li>
   <li class="activetab"><a href="greenhouse.php">Greenhouse</a></li>
   <li><a href="recycling_gamesell.php">Refuse Store</a></li>
  </ul>
<?php
if(strlen($_GET['msg']) > 0)
  $error_message = form_message(explode(',', $_GET['msg']));

// RECYCLING CENTER NPC IAN
echo '<a href="/npcprofile.php?npc=Ian Hobbs"><img src="//' . $SETTINGS['static_domain'] . '/gfx/npcs/hippy2.png" align="right" width="350" height="493" alt="(Ian the Recycling Center Hippy)" /></a>';

include 'commons/dialog_open.php';

if($dialog != '')
  echo $dialog;
else
{
  echo '
    <p>I operate this greenhouse to reward recyclers by selling the plants I grow for Greenhouse Points.</p>
  ';
}

include 'commons/dialog_close.php';

if(count($options) > 0)
  echo '<ul><li>', implode('</li><li>', $options), '</li></ul>';
?>
  <p>You have <?= $user['greenhouse_points'] ?> Greenhouse Points.</p>
  <form method="post">
  <table>
   <tr class="titlerow"><th class="centered">Quantity</th><th></th><th>Item</th><th>Point Cost</th></tr>
<?php
$rowclass = begin_row_class();

foreach($items_out as $id=>$item_cost)
{
  $itemname = $item_cost[0];
  $cost = $item_cost[1];

  $details = get_item_byname($itemname);

  echo '
    <tr class="' . $rowclass . '">
  ';

  if($cost > $user['greenhouse_points'])
    echo '<td><input type="number" style="width:50px;" disabled /></td>';
  else
    echo '<td><input type="number" name="i_' . $id . '" min="0" max="' . floor($user['greenhouse_points'] / $cost) . '" style="width:50px;" class="quantity" /></td>';

  echo '
     <td class="centered">' . item_display_extra($details) . '</td>
     <td>' . $details['itemname'] . '</td>
     <td class="righted price">' . $cost . '</td>
    </tr>
  ';

  $rowclass = alt_row_class($rowclass);
}
?>
   <tr style="border-top: 1px solid #000;"><th>Total</th><td></td><td></td><td id="total" class="righted">0</td></tr>
  </table>
  <p><input type="submit" name="submit" value="Buy" /></p>
  </form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
