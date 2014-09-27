<?php
$whereat = 'florist';
$wiki = 'The_Florist';
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/inventory.php';
require_once 'commons/formatting.php';
require_once 'commons/userlib.php';
require_once 'commons/itemlib.php';

if($user['show_florist'] != 'yes')
{
  header('Location: /404');
  exit();
}

$items_in = array(
  1 => array('3-Leaf Clover', 10),
  5 => array('Cilantro', 2),
  2 => array('Greenish Leaf', 10),
  4 => array('Mint Leaves', 10),
  3 => array('Tea Leaves', 20),
);

$items_out = array(
  1 => '4-Sided Die',
  2 => 'Clover Wine',
  3 => 'Green Cloth',
  4 => 'Green Paint',
);

if($now_month == 1 || $now_month == 2)
  $items_out[5] = 'Green Envelope';

$inventory = $database->FetchMultipleBy('
  SELECT COUNT(idnum) AS qty,itemname
  FROM monster_inventory
  WHERE
    `user`=' . quote_smart($user['user']) . '
    AND `location`=\'storage\'
  GROUP BY itemname
', 'itemname');

$errors = array();

if($_POST['submit'] == 'Exchange')
{
  $in = (int)$_POST['in'];
  $out = (int)$_POST['out'];
  $quantity = (int)$_POST['quantity'];

  $payment = $items_in[$in];

  if($quantity < 1)
    $errors[] = 'You want me to trade <em>' . $quantity . '</em>? ...';
  else
  {
    if(!array_key_exists($in, $items_in) || $in <= 0)
      $errors[] = 'I really can\'t give them away for free... they represent an amount of time and effort for me...';
    else
    {
      if($inventory[$payment[0]]['qty'] < $payment[1] * $quantity)
        $errors[] = 'You do not have enough ' . $payment[0] . ' items in your storage.  I think, at most, we could do ' . floor($inventory[$payment[0]]['qty'] / $payment[1]) . ' trade.';
    }

    if(!array_key_exists($out, $items_out))
      $errors[] = 'Oh, thank you, but I couldn\'t possibly take them from you for nothing!  Please pick something for me to give you in exchange...';
  }

  if(count($errors) == 0)
  {
    delete_inventory_byname($user['user'], $payment[0], $payment[1] * $quantity, 'storage');
    
    $inventory[$payment[0]]['qty'] -= $payment[1] * $quantity;
    
    add_inventory_quantity($user['user'], '', $items_out[$out], $user['display'] . ' traded with Vanessa for this item', $user['incomingto'], $quantity);

    if($quantity > 1)
      $errors[] = 'Great!  You\'ll find the ' . $quantity . ' ' . $items_out[$out] . ' in your ' . $user['incomingto'] . '.';
    else
      $errors[] = 'Great!  You\'ll find the ' . $items_out[$out] . ' in your ' . $user['incomingto'] . '.';
  }
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; The Florist &gt; Exchanges</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <?= ($check_message ? "<p style=\"color:blue;\">$check_message</p>" : "") ?>
     <h4>The Florist &gt; Exchanges</h4>
     <ul class="tabbed">
      <li><a href="florist.php">Flower Shop</a></li>
      <li><a href="florist_anonymous.php">Flower Delivery</a></li>
      <li class="activetab"><a href="florist_exchange.php">Exchanges</a></li>
      <li><a href="giftwrapping.php">Gift-wrapping</a></li>
     </ul>
<?php
if(strlen($_GET['msg']) > 0)
  $error_message = form_message(explode(',', $_GET['msg']));

// VANESSA ROSELLE
echo '<a href="/npcprofile.php?npc=Vanessa+Roselle"><img src="//' . $SETTINGS['static_domain'] . '/gfx/npcs/flowergirl.jpg" align="right" width="350" height="706" alt="(Vanessa the Florist)" /></a>';

include 'commons/dialog_open.php';

if(count($errors) > 0)
  echo '<p>' . implode('</p><p>', $errors) . '</p>';
else
{
  echo '<p>I\'m always looking for various leaves, if you have any.  Of course I can compensate you for any you might be able to provide.</p>';
}

include 'commons/dialog_close.php';
?>
     <form action="florist_exchange.php" method="post">
     <table class="nomargin">
      <tr>
       <td valign="top">
        <table>
         <tr class="titlerow"><th></th><th></th><th>Leaves</th></tr>
<?php
$rowclass = begin_row_class();

foreach($items_in as $id=>$in)
{
  $itemname = $in[0];
  $quantity = $in[1];

  echo '<tr class="' . $rowclass . '">';
  if($inventory[$itemname]['qty'] < $quantity)
    echo '<td><input type="radio" disabled /></td><td class="failure centered">';
  else
    echo '<td><input type="radio" name="in" value="' . $id . '" /></td><td class="centered">';

  echo (int)$inventory[$itemname]['qty'] . ' / ' . $quantity . '</td><td><a href="encyclopedia2.php?item=' . $itemname . '">' . $itemname . '</a></td>' .
       '</tr>';

  $rowclass = alt_row_class($rowclass);
}
?>
        </table>
       </td>
       <td valign="top" style="padding-left: 2em;">
        <table>
         <tr class="titlerow"><th></th><th></th><th>Compensation</th></tr>
<?php
$rowclass = begin_row_class();

foreach($items_out as $id=>$potion)
{
  $details = get_item_byname($potion);
?>
         <tr class="<?= $rowclass ?>">
          <td><input type="radio" name="out" value="<?= $id ?>" /></td>
          <td class="centered"><?= item_display_extra($details) ?></td>
          <td><?= $potion ?></td>
         </tr>
<?php
  $rowclass = alt_row_class($rowclass);
}
?>
        </table>
       </td>
      </tr>
     </table>
     <p>Quantity: <input name="quantity" value="1" maxlength="3" size="3" /> <input type="submit" name="submit" value="Exchange" /></p>
     </form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
