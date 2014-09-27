<?php
require_once 'commons/init.php';

$wiki = 'The_Pattern';
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
  1 => array('Maze Piece (N)', 2),
  2 => array('Maze Piece (E)', 2),
  3 => array('Maze Piece (S)', 2),
  4 => array('Maze Piece (W)', 2),
);

$items_out = array(
  1 => 'Maze Piece (NE)',
  2 => 'Maze Piece (ES)',
  3 => 'Maze Piece (SW)',
  4 => 'Maze Piece (NW)',
  5 => 'Maze Piece (NS)',
  6 => 'Maze Piece (EW)',
  7 => 'Maze Piece Summoning Scroll',
);

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
    $errors[] = $quantity . ' exchanges?!  I don\'t know what you mean!';
  else
  {
    if(!array_key_exists($in, $items_in) || $in <= 0)
      $errors[] = 'I\'ll need some pieces from you, however... I can\'t work my power without them!';
    else
    {
      if($inventory[$payment[0]]['qty'] < $payment[1] * $quantity)
        $errors[] = 'How unfortunate!  You don\'t have enough ' . $payment[0] . ' items in your storage.  You could only manage ' . floor($inventory[$payment[0]]['qty'] / $payment[1]) . ' such exchanges, at most.';
    }

    if(!array_key_exists($out, $items_out))
      $errors[] = 'But what should I turn them in to?';
  }

  if(count($errors) == 0)
  {
    delete_inventory_byname($user['user'], $payment[0], $payment[1] * $quantity, 'storage');

    $inventory[$payment[0]]['qty'] -= $payment[1] * $quantity;

    add_inventory_quantity($user['user'], '', $items_out[$out], $user['display'] . ' traded with The Pattern for this item', $user['incomingto'], $quantity);

    if($items_out[$out] == 'Maze Piece Summoning Scroll')
      $errors[] = 'Leaving it up to chance, hm?</p>Well, here you go, then!  Enjoy!';
    else if($quantity > 1)
      $errors[] = 'Let me just... flip these around...</p><p>Ah!  Hahaha!  Is there nothing I <em>can\'t</em> do?!</p><p>You\'ll find the ' . $quantity . ' ' . $items_out[$out] . ' in your ' . $user['incomingto'] . '.</p><p>Feel free to call upon my services again at any time.';
    else
      $errors[] = 'Let me just... flip these around...</p><p>Ah!  Hahaha!  Is there nothing I <em>can\'t</em> do?!</p><p>You\'ll find the ' . $items_out[$out] . ' in your ' . $user['incomingto'] . '.</p><p>Feel free to call upon my services again at any time.';

//    psymail_user('telkoth', 'psypets', $user['display'] . ' claimed a ' . $items_out[$out], 'they exchanged for ' . $payment[0] . 's');
  }
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; The Pattern &gt; Exchanges</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4>The Pattern &gt; Exchanges</h4>
     <ul class="tabbed">
      <li><a href="/pattern/">The Pattern</a></li>
      <li class="activetab"><a href="/pattern/exchange.php">Exchanges</a></li>
     </ul>
<?php
if(strlen($_GET['msg']) > 0)
  $error_message = form_message(explode(',', $_GET['msg']));

// NPC
//echo '<a href="npcprofile.php?npc=Lakisha+Pawlak"><img src="//saffron.psypets.net/gfx/npcs/banker_lakisha.png" align="right" width="350" height="427" alt="(Lakisha the Banker)" /></a>';

include 'commons/dialog_open.php';

if(count($errors) > 0)
  echo '<p>' . implode('</p><p>', $errors) . '</p>';
else
{
  echo '
    <p>I know a powerful secret!  I call it "The Secret of Rotation," and it gives me a fantastic power...</p>
    <p>I would use this power for your benefit.  Call on me at any time.</p>
    <p>Oh: also: I have some scrolls, if you\'d like those instead.</p>
  ';
}

include 'commons/dialog_close.php';
?>
     <form method="post">
     <table class="nomargin">
      <tr>
       <td valign="top">
        <table>
         <tr class="titlerow"><th></th><th></th><th>Maze Pieces</th></tr>
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

  echo (int)$inventory[$itemname]['qty'] . ' / ' . $quantity . '</td><td><a href="/encyclopedia2.php?item=' . $itemname . '">' . $itemname . '</a></td>' .
       '</tr>';

  $rowclass = alt_row_class($rowclass);
}
?>
        </table>
       </td>
       <td valign="top" style="padding-left: 2em;">
        <table>
         <tr class="titlerow"><th></th><th></th><th>Startling Result</th></tr>
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
