<?php
$wiki = 'The_Bank';
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
  1 => array('Banana Taffy', 10), // more, since banana can be bought from the grocery store
  2 => array('Green Taffy', 6),
  3 => array('Lychee Taffy', 5),
  4 => array('Mintberry Swirls', 5),
  5 => array('Orange Taffy', 10), // more, since orange can be bought from the grocery store
  6 => array('Pineapple Drop', 4), // less, since you need a cornsyruper
  7 => array('Sour Taffy', 6),
  8 => array('Watermelon Taffy', 5),
);

$items_out = array(
  1 => 'Aging Root',
  3 => 'Cadmium Yellow',
  2 => 'Log',
);

if($now_month == 1 || $now_month == 2)
{
  $items_out[4] = 'Red Envelope';
  $items_out[5] = 'White Envelope';
}

$inventory = $database->FetchMultipleBy('
	SELECT COUNT(idnum) AS qty,itemname
	FROM monster_inventory
	WHERE
		`user`=' . $database->Quote($user['user']) . '
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
    $errors[] = 'I can\'t make fewer than 1 exchange, obviously...';
  else
  {
    if(!array_key_exists($in, $items_in) || $in <= 0)
      $errors[] = 'Well what are you offering in exchange?  Surely you don\'t expect me to hand out things for <em>free</em>...';
    else
    {
      if($inventory[$payment[0]]['qty'] < $payment[1] * $quantity)
        $errors[] = 'You do not have enough ' . $payment[0] . ' items in your storage.  At most, I figure you could do ' . floor($inventory[$payment[0]]['qty'] / $payment[1]) . '.';
    }

    if(!array_key_exists($out, $items_out))
      $errors[] = 'Unfortunately, I\'m not allowed to accept tips...';
  }

  if(count($errors) == 0)
  {
    delete_inventory_byname($user['user'], $payment[0], $payment[1] * $quantity, 'storage');
    
    $inventory[$payment[0]]['qty'] -= $payment[1] * $quantity;
    
    add_inventory_quantity($user['user'], '', $items_out[$out], $user['display'] . ' traded with Lakisha for this item', $user['incomingto'], $quantity);

    if($quantity > 1)
      $errors[] = 'It\'s a deal, then!  You\'ll find the ' . $quantity . ' ' . $items_out[$out] . ' in your ' . $user['incomingto'] . '.';
    else
      $errors[] = 'It\'s a deal, then!  You\'ll find the ' . $items_out[$out] . ' in your ' . $user['incomingto'] . '.';

//    psymail_user('telkoth', 'psypets', $user['display'] . ' claimed a ' . $items_out[$out], 'they exchanged for ' . $payment[0] . 's');
  }
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; The Bank</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4>The Bank</h4>
     <ul class="tabbed">
      <li><a href="/bank.php">The Bank</a></li>
      <li><a href="/bank_groupcurrencies.php">Group Currencies</a></li>
      <li class="activetab"><a href="/bank_exchange.php">Exchanges</a></li>
      <li><a href="/ltc.php">License to Commerce</a></li>
      <li><a href="/allowance.php">Allowance Preference</a></li>
      <li><a href="/af_favortickets.php">Get Favor Tickets</a></li>
      <li><a href="/af_favortransfer2.php">Transfer Favor</a></li>
<?= $st_patricks ? '<li class="stpatrick"><a href="/stpatricks.php?where=bank">St. Patrick\'s Day Competition</a></li>' : '' ?>
     </ul>
<?php
if(strlen($_GET['msg']) > 0)
  $error_message = form_message(explode(',', $_GET['msg']));

// BANKER NPC LAKISHA
echo '<a href="/npcprofile.php?npc=Lakisha+Pawlak"><img src="//' . $SETTINGS['static_domain'] . '/gfx/npcs/banker_lakisha.png" align="right" width="350" height="427" alt="(Lakisha the Banker)" /></a>';

include 'commons/dialog_open.php';

if(count($errors) > 0)
  echo '<p>' . implode('</p><p>', $errors) . '</p>';
else
{
  echo '<p>I\'ve been trying to clean out my Basement recently... there\'s so much stuff down there that I really have absolutely no use for.  Maybe you could take some off my hands?</p><p>Oh, and did I mention I have a bit of a sweet tooth?</p>';
}

include 'commons/dialog_close.php';
?>
     <form action="bank_exchange.php" method="post">
     <table class="nomargin">
      <tr>
       <td valign="top">
        <table>
         <tr class="titlerow"><th></th><th></th><th>Candy</th></tr>
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

  echo (int)$inventory[$itemname]['qty'] . ' / ' . $quantity . '</td><td>' . item_text_link($itemname) . '</td>' .
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
