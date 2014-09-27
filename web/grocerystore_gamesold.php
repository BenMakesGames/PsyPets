<?php
$whereat = 'recylcing';
$wiki = 'Recycling';
$require_petload = 'no';

$url = 'recycling_gamesell.php';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/inventory.php';
require_once 'commons/formatting.php';
require_once 'commons/userlib.php';
require_once 'commons/globals.php';
require_once 'commons/messages.php';
require_once 'commons/questlib.php';

$dialog_generic = false;

$refuse_inventory_command = 'SELECT a.itemname,b.value,b.graphictype,b.graphic,COUNT(a.idnum) AS qty FROM monster_inventory AS a LEFT JOIN monster_items AS b ON a.itemname=b.itemname WHERE a.user=\'grocerystore\' AND b.nosellback=\'no\' GROUP BY(a.itemname) ORDER BY a.itemname';

$inventory = $database->FetchMultipleBy($refuse_inventory_command, 'itemname', 'fetching refuse inventory');

if($_POST['action'] == 'Purchase')
{
  $total_cost = 0;

  foreach($_POST as $key=>$value)
  {
    $qty = (int)$value;
  
    if(substr($key, 0, 2) == 'i_' && $qty > 0)
    {
      $itemname = itemname_from_form_value(substr($key, 2));
      
      if($qty > $inventory[$itemname]['qty'])
      {
        $not_enough[$itemname] += $qty - $inventory[$itemname]['qty'];
        $qty = $inventory[$itemname]['qty'];
      }

      $total_cost += ceil($inventory[$itemname]['value'] / 5.0 * 4) * $qty;
      $buying[$itemname] += $qty;
    }
  }
  
  if($total_cost == 0)
  {
    $dialog_generic = '<p>You didn\'t select any items to buy!</p>';
  }
  else if($total_cost > $user['money'])
  {
    $dialog_generic = '<p>The selected items would cost ' . $total_cost . '<span class="money">m</span>, but you only have ' . $user['money'] . '<span class="money">m</span>...</p>';
  }
  else
  {
    $real_total_cost = 0;
    $real_total_qty = 0;
  
    foreach($buying as $itemname=>$qty)
    {
      $command = $database->FetchNone('
				UPDATE monster_inventory
				SET
					user=' . quote_smart($user['user']) . ',
					changed=' . (int)$now . ',
					location=\'storage/incoming\'
				WHERE
					user=\'grocerystore\' AND
					itemname=' . quote_smart($itemname) . '
				LIMIT ' . $qty . '
			');

      $real_qty = $database->AffectedRows();
      
      if($real_qty < $qty)
        $not_enough[$itemname] += $qty - $real_qty;
      
      $real_total_qty += $real_qty;
      $real_total_cost += ceil($inventory[$itemname]['value'] / 5.0 * 4) * $real_qty;
    }
    
    if($real_total_cost > 0)
    {
      take_money($user, $real_total_cost, 'Purchased ' . $real_total_qty . ' items from The Grocery Store');
      $user['money'] -= $real_total_cost;

      $dialog_generic = '<p>Fantastic!  You\'ll find everything in your <a href="incoming.php">Incoming box</a>.</p>';

      require_once 'commons/statlib.php';
      record_stat($user['idnum'], 'Items Purchased from Recycling', $real_total_qty);
      record_Stat($user['idnum'], 'Moneys Spent Purchasing Items from Recycling', $real_total_cost);
    }
    else
      $dialog_generic = '<p class="failure">Oh, sorry!  It looks like someone beat you to it!</p>';

    $inventory = $database->FetchMultipleBy($refuse_inventory_command, 'itemname', 'fetching refuse inventory');
  }
}

if(count($not_enough) > 0)
{
  $dialog_generic .= '<p class="obstacle">Hm!  Some items were purchased by another player just before you!</p><ul>';
  
  foreach($not_enough as $itemname=>$qty)
    $dialog_generic .= '<li class="obstacle">' . $qty . '&times; ' . $itemname . '</li>';

  $dialog_generic .= '</ul><p class="obstacle">Sorry about that!</p>';
}

$MAX_ITEM_ID = count($inventory);

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Grocery Store &gt; Farmer's Market</title>
<?php include 'commons/head.php'; ?>
  <script type="text/javascript">
   function update_total()
   {
     var value = 0;

     for(var i = 1; i <= <?= $MAX_ITEM_ID ?>; ++i)
     {
       quantity = parseInt($('#quantity_' + i).val());

       if(quantity > 0)
         value += quantity * $('#value_' + i).html();
     }

     $('#total').html(value);
   }

   function validate()
   {
     var value = parseInt($(this).val());

     if(!value || value <= 0)
       $(this).val(0);
     else
       $(this).val(value);

     recalc();
   }

   $(document).ready(function() {
     for(var i = 1; i <= <?= $MAX_ITEM_ID ?>; ++i)
     {
       $('#quantity_' + i).keyup(update_total);
       $('#quantity_' + i).blur(validate);
     }
   });
  </script>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4>Grocery Store &gt; Manager's Special</h4>
     <ul class="tabbed">
      <li><a href="grocerystore.php">Grocery Store</a></li>
      <li class="activetab"><a href="grocerystore_gamesold.php">Manager's Special</a></li>
     </ul>
<?php
if(strlen($_GET['msg']) > 0)
  $error_message = form_message(explode(',', $_GET['msg']));

// RECYCLING CENTER NPC IAN
//echo '<a href="npcprofile.php?npc=Ian Hobbs"><img src="gfx/npcs/hippy2.png" align="right" width="350" height="493" alt="(Ian the Recycling Center Hippy)" /></a>';

if($error_message)
  echo '<p class="failure">' . $error_message . '</p>';
else if(strlen($message) > 0)
  echo '<p class="success">' . $message . '</p>';
else if($dialog_generic !== false)
  echo $dialog_generic;

if(count($inventory) > 0)
{
  echo '
    <form method="post">
    <table>
     <tr class="titlerow">
      <th colspan="2" class="centered">Quantity</th><th></th><th>Item</th><th class="centered">Price</th>
     </tr>
  ';

  $rowclass = begin_row_class();
  $index = 1;

  foreach($inventory as $item)
  {
    echo '
      <tr class="' . $rowclass . '">
       <td><input type="number" min="0" max="' . $item['qty'] . '" style="width:60px;" id="quantity_' . $index . '" name="i_' . itemname_to_form_value($item['itemname']) . '" maxlength="' . strlen($item['qty']) . '" /></td>
       <td>/ ' . $item['qty'] . '</td>
       <td class="centered">' . item_display($item) . '</td>
       <td>' . $item['itemname'] . '</td>
       <td class="righted"><span id="value_' . $index . '">' . ceil($item['value'] / 5.0 * 4) . '</span><span class="money">m</span></td>
      </tr>
    ';
    
    $rowclass = alt_row_class($rowclass);
    $index++;
  }

  echo '
    </table>
    <p><b>Total:</b> <span id="total">0</span><span class="money">m</span></p>
    <p><input type="submit" name="action" value="Purchase" /></p>
    </form>
  ';
}
else
  echo '<p>No items are available at this time.</p>';

?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
