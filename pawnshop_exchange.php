<?php
$whereat = 'pawnshop';
$wiki = 'Pawn_Shop';
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';

if(!(($now_month == 10 && $now_day >= 30) || ($now_month == 11 && $now_day == 1)))
{
  header('Location: ./pawnshop.php');
  exit();
}

require_once 'commons/grammar.php';
require_once 'commons/inventory.php';
require_once 'commons/formatting.php';
require_once 'commons/userlib.php';
require_once 'commons/itemlib.php';

$items_out = array(
  1 => array('Caramel Squares', 2),
  2 => array('Amethyst Rose Candle', 3),
  3 => array('Mint Tea Candle', 4),
  4 => array('Steak', 5),
  5 => array('Orange Cloth', 6),
  6 => array('7 Circle', 7),
  7 => array('Pumpkin Totem', 8),
  8 => array('Tartan', 9),
  9 => array('Magic Pixie Dust', 10),
);

$command = 'SELECT COUNT(idnum) AS c FROM monster_inventory WHERE `user`=' . quote_smart($user['user']) . " AND `location`='storage' AND itemname='Alien Taser'";
$data = $database->FetchSingle($command, 'fetching Alien Taser count');

$taser_count = (int)$data['c'];

$errors = array();

if($_POST['submit'] == 'Exchange')
{
  $out = (int)$_POST['out'];
  $quantity = (int)$_POST['quantity'];
  
  if($quantity < 1)
    $errors[] = 'You gotta\' ask for at least one, dude.';
  else if(!array_key_exists($out, $items_out))
    $errors[] = 'Hey-hey - just what\'re you trying to pull?';
  else if($taser_count < $items_out[$out][1] * $quantity)
    $errors[] = 'You don\'t have enough Alien Tasers items... with the number you have, you could get ' . floor($taser_count / $items_out[$out][1]) . ' ' . $items_out[$out][0] . ', at most.';

  if(count($errors) == 0)
  {
    delete_inventory_byname($user['user'], 'Alien Taser', $items_out[$out][1] * $quantity, 'storage');
    
    $taser_count -= $items_out[$out][1] * $quantity;
    
    add_inventory_quantity($user['user'], '', $items_out[$out][0], $user['display'] . ' traded with Tony for this item', $user['incomingto'], $quantity);

    if($quantity > 1)
      $errors[] = 'Nice, nice... I\'ll have \'em dropped off at your ' . $user['incomingto'] . '.';
    else
      $errors[] = 'Nice, nice... I\'ll have it dropped off at your ' . $user['incomingto'] . '.';
  }
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Pawn Shop</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <?= ($check_message ? "<p style=\"color:blue;\">$check_message</p>" : "") ?>
     <h4>Pawn Shop</h4>
     <ul class="tabbed">
      <li><a href="pawnshop.php">Pawn Shop</a></li>
      <li class="activetab"><a href="pawnshop_exchange.php">Back Room</a></li>
     </ul>
<?php
if(strlen($_GET['msg']) > 0)
  $error_message = form_message(explode(',', $_GET['msg']));

// PAWN SHOP NPC TONY
echo '<a href="/npcprofile.php?npc=Tony+Cables"><img src="gfx/npcs/tony.png" align="right" width="350" height="305" alt="(Tony "Shady" Cables)" /></a>';

include 'commons/dialog_open.php';

if(count($errors) > 0)
  echo '<p>' . implode('</p><p>', $errors) . '</p>';
else
{
  echo '<p>Yeah, okay, you\'re here now, so keep it down, will ya\'?</p>' .
       '<p>I struck a deal recently enabling me to offer you... unprecedented deals.  Take a look at this shit and tell me it\'s not good.</p>' .
       '<p>Eh?  Yeah?  Ha, ha!</p>' .
       '<p>That\'s what I thought. <strong>Here\'s the deal though:</strong> I can only offer you this stuff in exchange for Alien Tasers, and only through November 1st.  Alright?  Oh, and also: you tell no one.  This is between you and me.</p>' .
       '<p>So whaddya say?</p>';
}

include 'commons/dialog_close.php';
?>
     <p>"Prices" here are given in Alien Tasers.  You currently have <?= $taser_count ?> in Storage.</p>
     <form action="pawnshop_exchange.php" method="post">
        <table>
         <tr class="titlerow"><th></th><th></th><th>Item</th><th>Price</th></tr>
<?php
$rowclass = begin_row_class();

foreach($items_out as $id=>$option)
{
  $details = get_item_byname($option[0]);
?>
         <tr class="<?= $rowclass ?>">
          <td><input type="radio" name="out" value="<?= $id ?>" /></td>
          <td class="centered"><?= item_display_extra($details) ?></td>
          <td><?= $option[0] ?></td>
          <td class="centered"><?= $option[1] ?></td>
         </tr>
<?php
  $rowclass = alt_row_class($rowclass);
}
?>
        </table>
     <p>Quantity: <input name="quantity" maxlength="3" size="3" value="1" /> <input type="submit" name="submit" value="Exchange" /></p>
     </form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
