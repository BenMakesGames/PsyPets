<?php
$wiki = 'The_Temple';
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

$items_in = array(
  1 => array('Feather', 20),
	2 => array('Fish', 10),
  3 => array('Leather', 20),
  4 => array('Pearl', 10),
  5 => array('Small Miracle', 4),
);

$items_out = array(
  3 => 'Clairvoyance Scroll',
  2 => 'Genealogy of Ki Ri Kashu',
  1 => 'Scroll of Local Teleportation',
);

$items = $database->FetchMultiple('SELECT itemname FROM monster_inventory WHERE `user`=' . quote_smart($user['user']) . " AND `location`='storage'");

$inventory = array();

foreach($items as $inventory_item)
  $inventory[$inventory_item['itemname']]++;

$errors = array();

if($_POST['submit'] == 'Exchange')
{
  $in = (int)$_POST['in'];
  $out = (int)$_POST['out'];
  $quantity = (int)$_POST['quantity'];
  
  $payment = $items_in[$in];

  if($quantity < 1)
    $errors[] = 'I am naturally unable to exchange fewer than 1 item...';
  else
  {
    if(!array_key_exists($in, $items_in) || $in <= 0)
      $errors[] = 'I\'m sorry to say that we cannot simply hand out items for free...';
    else
    {
      if($inventory[$payment[0]] < $payment[1] * $quantity)
        $errors[] = 'I regret to inform you that you do not have enough ' . $payment[0] . ' items in your storage to make that many exchanges.  With the amount you have, I could manage only ' . floor($inventory[$payment[0]] / $payment[1]) . ' exchanges.';
    }

    if(!array_key_exists($out, $items_out))
      $errors[] = 'If you\'d like to make a donation, we do accept moneys...';
  }

  if(count($errors) == 0)
  {
    delete_inventory_byname($user['user'], $payment[0], $payment[1] * $quantity, 'storage');
    
    $inventory[$payment[0]] -= $payment[1] * $quantity;
    
    add_inventory_quantity($user['user'], '', $items_out[$out], $user['display'] . ' traded with Lance for this item', $user['incomingto'], $quantity);

    if($quantity > 1)
      $errors[] = 'Glad to have been of service.  You\'ll find the ' . $quantity . ' ' . $items_out[$out] . ' in your ' . $user['incomingto'] . '.';
    else
      $errors[] = 'Glad to have been of service.  You\'ll find the ' . $items_out[$out] . ' in your ' . $user['incomingto'] . '.';

//    psymail_user('telkoth', 'psypets', $user['display'] . ' claimed a ' . $items_out[$out], 'they exchanged for ' . $payment[0] . 's');
  }
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; The Temple &gt; Exchanges</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4>The Temple</h4>
     <ul class="tabbed">
      <li><a href="temple.php">Donations</a></li>
      <li class="activetab"><a href="temple_exchange.php">Exchanges</a></li>
      <li><a href="af_revive2.php">Resurrections</a></li>
      <li><a href="af_respec.php">Proselytism's Broth</a></li>
     </ul>
<?php
if(strlen($_GET['msg']) > 0)
  $error_message = form_message(explode(',', $_GET['msg']));

// TEMPLE NPC LANCE
echo '<a href="npcprofile.php?npc=Lance Sussman"><img src="gfx/npcs/monk.png" align="right" width="350" height="535" alt="(Lance the Monk)" /></a>';

include 'commons/dialog_open.php';

if(count($errors) > 0)
  echo '<p>' . implode('</p><p>', $errors) . '</p>';
else
{
  echo '<p>Perhaps we can help each other...</p>';
}

include 'commons/dialog_close.php';
?>
     <form method="post">
     <table class="nomargin">
      <tr>
       <td valign="top">
        <table>
         <tr class="titlerow"><th></th><th></th><th>Resource</th></tr>
<?php
$rowclass = begin_row_class();

foreach($items_in as $id=>$in)
{
  $itemname = $in[0];
  $quantity = $in[1];

  echo '<tr class="' . $rowclass . '">';
  if($inventory[$itemname] < $quantity)
    echo '<td><input type="radio" disabled /></td><td class="failure centered">';
  else
    echo '<td><input type="radio" name="in" value="' . $id . '" /></td><td class="centered">';

  echo (int)$inventory[$itemname] . ' / ' . $quantity . '</td><td>' . item_text_link($itemname) . '</td>' .
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
     <p>Quantity: <input name="quantity" maxlength="3" size="3" value="1" /> <input type="submit" name="submit" value="Exchange" /></p>
     </form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
