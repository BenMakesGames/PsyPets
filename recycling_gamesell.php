<?php
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

if($_POST['action'] == 'Purchase')
{
  $total_cost = 0;

  foreach($_POST as $key=>$value)
  {
    $qty = (int)$value;
  
    if(substr($key, 0, 2) == 'i_' && $qty > 0)
    {
      $itemname = itemname_from_form_value(substr($key, 2));
      
			$details = get_item_byname($itemname);
			$selling[$itemname] = $database->FetchSingle('SELECT COUNT(idnum) AS qty FROM monster_inventory WHERE user=\'ihobbs\' AND itemname=' . $database->Quote($itemname));
			$selling[$itemname]['value'] = $details['value'];
			
      if($qty > $selling[$itemname]['qty'])
      {
        $not_enough[$itemname] += $qty - $selling[$itemname]['qty'];
        $qty = $selling[$itemname]['qty'];
      }

      $total_cost += $details['value'] * $qty;
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
					user=\'ihobbs\' AND
					itemname=' . quote_smart($itemname) . '
				LIMIT ' . $qty . '
			');

      $real_qty = $database->AffectedRows();
      
      if($real_qty < $qty)
        $not_enough[$itemname] += $qty - $real_qty;
      
      $real_total_qty += $real_qty;
      $real_total_cost += $selling[$itemname]['value'] * $real_qty;
    }
    
    if($real_total_cost > 0)
    {
      take_money($user, $real_total_cost, 'Purchased ' . $real_total_qty . ' items from Recycling');
      $user['money'] -= $real_total_cost;

      $dialog_generic = '<p>Fantastic!  You\'ll find everything in your <a href="incoming.php">Incoming box</a>.</p>';

      require_once 'commons/statlib.php';
      record_stat($user['idnum'], 'Items Purchased from Recycling', $real_total_qty);
      record_Stat($user['idnum'], 'Moneys Spent Purchasing Items from Recycling', $real_total_cost);
    }
    else
      $dialog_generic = '<p class="failure">Oh, sorry!  It looks like someone beat you to it!</p>';
  }
}

if(count($not_enough) > 0)
{
  $dialog_generic .= '<p class="obstacle">Hm!  Some items were purchased by another player just before you!</p><ul>';
  
  foreach($not_enough as $itemname=>$qty)
    $dialog_generic .= '<li class="obstacle">' . $qty . '&times; ' . $itemname . '</li>';

  $dialog_generic .= '</ul><p class="obstacle">Sorry about that!</p>';
}

// get inventory
if(array_key_exists('letter', $_GET))
	$letter = strtoupper(substr($_GET['letter'], 0, 1));
else
	$letter = 'A';

if(ctype_alpha($letter))
	$letter_likes[] = 'itemname LIKE \'' . $letter . '%\'';
else
{
	$letter = '#';
	$letter_likes[] = 'itemname LIKE \'0%\'';
	$letter_likes[] = 'itemname LIKE \'1%\'';
	$letter_likes[] = 'itemname LIKE \'2%\'';
	$letter_likes[] = 'itemname LIKE \'3%\'';
	$letter_likes[] = 'itemname LIKE \'4%\'';
	$letter_likes[] = 'itemname LIKE \'5%\'';
	$letter_likes[] = 'itemname LIKE \'6%\'';
	$letter_likes[] = 'itemname LIKE \'7%\'';
	$letter_likes[] = 'itemname LIKE \'8%\'';
	$letter_likes[] = 'itemname LIKE \'9%\'';
	$letter_likes[] = 'itemname LIKE \'"%\'';
	$letter_likes[] = 'itemname LIKE \'+%\'';
	$letter_likes[] = 'itemname LIKE \'-%\'';
}

$inventory = $database->FetchMultipleBy('
	SELECT itemname,COUNT(idnum) AS qty
	FROM monster_inventory
	WHERE
		user=\'ihobbs\'
		AND (' . implode(' OR ', $letter_likes) . ')
	GROUP BY(itemname)
	ORDER BY itemname
', 'itemname');

$MAX_ITEM_ID = count($inventory);

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Recycling &gt; Refuse</title>
<?php include 'commons/head.php'; ?>
  <script type="text/javascript" src="//<?= $SETTINGS['static_domain'] ?>/js/jquery.tablesorter.min.js"></script>
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

     update_total();
   }

   $(function() {
     $('.inventory-quantity')
       .keyup(update_total)
       .blur(validate)
     ;
   });
  </script>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
  <h4>Recycling &gt; Refuse Store</h4>
  <ul class="tabbed">
   <li><a href="/recycling.php">Recycling</a></li>
   <li><a href="greenhouse.php">Greenhouse</a></li>
   <li class="activetab"><a href="/recycling_gamesell.php">Refuse Store</a></li>
  </ul>
<?php
if(strlen($_GET['msg']) > 0)
  $error_message = form_message(explode(',', $_GET['msg']));

// RECYCLING CENTER NPC IAN
echo '<a href="/npcprofile.php?npc=Ian Hobbs"><img src="//' . $SETTINGS['static_domain'] . '/gfx/npcs/hippy2.png" align="right" width="350" height="493" alt="(Ian the Recycling Center Hippy)" /></a>';

include 'commons/dialog_open.php';

if($error_message)
  echo '<p class="failure">' . $error_message . '</p>';
else if(strlen($message) > 0)
  echo '<p class="success">' . $message . '</p>';
else if($dialog_generic !== false)
  echo $dialog_generic;
else
{
  echo '
    <p>I recover as much of the stuff people "gamesell" as possible, and resell it here!</p>
    <p>As they say, "one man\'s trash is another man\'s treasure."</p>
    <p class="size8">Recycling... reusing... If only I could find some way to <em>reduce</em>.  Hm...</p>
  ';

  if(count($inventory) == 0)
    echo '<p>Unfortunately I\'m out of stock right now, but check back later.  You never know when something new might pop up!</p>';
}

include 'commons/dialog_close.php';

if(count($inventory) > 0)
{
	$letters = array(
		'#', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L',
	  'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'
	);
?>
	<ul class="pagination">
		<li class="skip">Page:</li>
<?php
foreach($letters as $this_letter)
{
	if($letter == $this_letter)
		echo '<li class="current"><span>' . $this_letter . '</span></li>';
	else
		echo '<li><a href="?letter=' . $this_letter . '">' . $this_letter . '</a></li>';
}
?>
	</ul>
  <form method="post">
  <table id="refuse-inventory">
   <thead>
    <tr>
     <th colspan="2" class="centered">Quantity</th><th></th><th>Item</th><th class="centered"><div style="padding-right:10px;">Price</div></th>
    </tr>
   </thead>
   <tbody>
<?php

  $rowclass = begin_row_class();
  $index = 1;

  foreach($inventory as $item)
  {
		$details = get_item_byname($item['itemname']);

    echo '
      <tr class="' . $rowclass . '">
       <td><input class="inventory-quantity" type="number" min="0" max="' . $item['qty'] . '" style="width:50px;" id="quantity_' . $index . '" name="i_' . itemname_to_form_value($item['itemname']) . '" maxlength="' . strlen($item['qty']) . '" /></td>
       <td><nobr>/ ' . $item['qty'] . '</nobr></td>
       <td class="centered">' . item_display($details) . '</td>
       <td>' . $item['itemname'] . '</td>
       <td class="righted"><span id="value_' . $index . '">' . $details['value'] . '</span><span class="money">m</span></td>
      </tr>
    ';
    
    $rowclass = alt_row_class($rowclass);
    $index++;
  }

?>
   </tbody>
  </table>
  <p><b>Total:</b> <span id="total">0</span><span class="money">m</span></p>
  <p><input type="submit" name="action" value="Purchase" /></p>
  </form>
	<ul class="pagination">
		<li class="skip">Page:</li>
<?php
foreach($letters as $this_letter)
{
	if($letter == $this_letter)
		echo '<li class="current"><span>' . $this_letter . '</span></li>';
	else
		echo '<li><a href="?letter=' . $this_letter . '">' . $this_letter . '</a></li>';
}
?>
	</ul>
<?php
}
else
  echo '<p>No items are available at this time.</p>';

?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
