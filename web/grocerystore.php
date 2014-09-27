<?php
$wiki = 'Grocery Store';
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/userlib.php';
require_once 'commons/itemlib.php';
require_once 'commons/statlib.php';

$MAX_ITEM_ID = 19;

$item_aisles = array(
  'Produce' => array(
    1 => 'Banana',
    2 => 'Celery',
    18 => 'Cucumber',
    4 => 'Delicious',
    6 => 'White Radish',
    7 => 'Yam',
  ),
  'Meat and Dairy' => array(
    8 => 'Egg',
    10 => 'Chicken',
    3 => 'Milk',
    19 => 'Pork',
    11 => 'Raw Milk',
  ),
  'Baking Supplies' => array(
    13 => 'Baking Soda',
    16 => 'Brown Sugar',
    14 => 'Cream of Tartar',
    15 => 'Flour',
    17 => 'Shortening',
    12 => 'Sugar',
  ),
);

if($_POST['action'] == 'Purchase')
{
  foreach($item_aisles as $aisle)
  {
    foreach($aisle as $id=>$item)
    {
      $quantity = (int)$_POST['quantity_' . $id];
      
      if($quantity > 0)
      {
        $details = get_item_byname($item);

        $total_cost += ceil($details['value'] / 5.0 * 4) * $quantity;
        $total_bulk += $details['bulk'] * $quantity;
        $total_quantity += $quantity;
        
        $order[$item] += $quantity;
        
        $report[] = $quantity . '&times; ' . $item;
      }
    }
  }
  
  if($total_cost > 0)
  {
    if($total_cost <= $user['money'])
    {
      take_money($user, $total_cost, 'Grocery Store bill', implode('<br />', $report));
      
      $user['money'] -= $total_cost;
      
      foreach($order as $itemname=>$quantity)
        add_inventory_quantity($user['user'], '', $itemname, 'Purchased from the Grocery Store', 'storage/incoming', $quantity);

      record_stat($user['idnum'], 'Items Purchased from Grocery Store', $total_quantity);

      $message_list[] = '<span class="success">Thanks!  The purchased items have been placed into your <a href="incoming.php">Incoming</a>.</span>';

      $paper_bags = floor($total_bulk / 150); // every 15.0 Bulk = 1 Paper Bag (round down)

      if($paper_bags > 0)
      {
        add_inventory_quantity($user['user'], '', 'Paper Bag', 'Given by the Grocery Store', 'storage/incoming', $paper_bags);

        $message_list[] = '<span class="success">(You were also given ' . ($paper_bags == 1 ? 'a Paper Bag' : $paper_bags . ' Paper Bags') . ' to carry the groceries in.)</span>';
      }
    }
    else
      $message_list[] = '<span class="failure">You cannot afford to pay for the selected items.</span>';
  }
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Grocery Store</title>
<?php include "commons/head.php"; ?>
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

   $(function() {
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
<?php
echo '<h4>Grocery Store</h4>';

if(count($message_list) > 0)
  echo '<ul><li>', implode('</li><li>', $message_list), '</li></ul>';
?>
     <ul class="tabbed">
      <li class="activetab"><a href="grocerystore.php">Grocery Store</a></li>
      <li><a href="grocerystore_gamesold.php">Manager's Special</a></li>
     </ul>

<form method="post">
<table>
 <tr>
<?php
$first = true;

foreach($item_aisles as $aisle=>$items)
{
  if($first)
  {
    $first = false;
    $style = 'padding-right: 10px;';
  }
  else
    $style = 'border-left: 1px solid #000; padding-left: 10px; padding-right: 10px;';

  echo '
    <td valign="top" style="' . $style . '">
     <h5 style="text-align: center;">' . $aisle . '</h5>
     <table class="nomargin">
      <tr class="titlerow"><th>Quantity</th><th></th><th>Item</th><th>Price</th></tr>
  ';

  $rowclass = begin_row_class();
  
  foreach($items as $id=>$item)
  {
    $details = get_item_byname($item);
    
    echo '
      <tr class="' . $rowclass . '">
       <td><input type="number" style="width:60px;" name="quantity_' . $id . '" id="quantity_' . $id . '" /></td>
       <td class="centered">' . item_display($details) . '</td>
       <td>' . $item . '</td>
       <td class="righted"><span id="value_' . $id . '">' . ceil($details['value'] / 5.0 * 4) . '</span><span class="money">m</span></td>
      </tr>
    ';

    $rowclass = alt_row_class($rowclass);
  }
  
  echo '
     </table>
    </td>
  ';
}
?>
 </tr>
</table>
<p><b>Total:</b> <span id="total">0</span><span class="money">m</span></p>
<p><input type="submit" name="action" value="Purchase" /></p>
</form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
