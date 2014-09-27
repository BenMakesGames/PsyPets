<?php
$wiki = 'The_Park';
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

if($user['show_park'] != 'yes')
{
  header('Location: /404');
  exit();
}

$items_in = array(
  2 => array('American Football', 4),
  7 => array('Baseball Cap', 3),
	4 => array('Gold Bat', 2),
  1 => array('Line Marker', 1),
	6 => array('Shounen Bat', 1),
  3 => array('Tennis Racket', 3),
	5 => array('T-Rex\'s Bat', 4),
  1000 => array('Park Token', 3),
);

$items_out = array(
  3 => 'Bundle of Blueprints',
  2 => 'Construction Cone',
  1 => 'Crate of Milk',
);

if($now_month == 1 || $now_month == 2)
  $items_out[3] = 'Blue Envelope';

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
    $errors[] = 'How do you expect me to make fewer than 1 exchange?.';
  else
  {
    if(!array_key_exists($in, $items_in) || $in <= 0)
      $errors[] = 'And what do I get out of it?';
    else
    {
      if($inventory[$payment[0]]['qty'] < $payment[1] * $quantity)
        $errors[] = 'You don\'t have enough ' . $payment[0] . ' items in your storage.  At most, you could do ' . floor($inventory[$payment[0]]['qty'] / $payment[1]) . ' exchanges.';
    }

    if(!array_key_exists($out, $items_out))
      $errors[] = 'You\'re a good kid, but really, I insist: pick something you want in exchange.';
  }

  if(count($errors) == 0)
  {
    delete_inventory_byname($user['user'], $payment[0], $payment[1] * $quantity, 'storage');
    
    $inventory[$payment[0]]['qty'] -= $payment[1] * $quantity;
    
    add_inventory_quantity($user['user'], '', $items_out[$out], $user['display'] . ' traded with the park manager for this item', $user['incomingto'], $quantity);

    if($quantity > 1)
      $errors[] = 'Done!  You\'ll find the ' . $quantity . ' ' . $items_out[$out] . ' in your ' . $user['incomingto'] . '.';
    else
      $errors[] = 'Done!  You\'ll find the ' . $items_out[$out] . ' in your ' . $user['incomingto'] . '.';

		require_once 'commons/statlib.php';
    record_stat($user['idnum'], 'Exchanges Made at The Park', $quantity);
  }
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; The Park</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4>The Park</h4>
<?php
$command = 'SELECT idnum,name FROM `monster_events` ' .
           'WHERE host=' . quote_smart($user['user']) . ' AND finished=\'no\' LIMIT 1';
$this_event = $database->FetchSingle($command, 'fetching existing event');

echo '
  <ul class="tabbed">
   <li><a href="/park.php">Browse Events</a></li>
';

if($this_event !== false)
  echo '<li><a href="/eventdetails.php?idnum=' . $this_event['idnum'] . '">View/cancel my running event, "' . $this_event['name'] . '"</a></li>';
else if($user['event_step'] > 0)
  echo '<li><a href="/hostevent' . ($user['event_step'] + 1) . '.php">Continue making an event where I left off</a></li>';
else
  echo '<li><a href="/hostevent1.php">Host a new event</a></li>';

echo '
   <li class="activetab"><a href="/park_exchange.php">Exchanges</a></li>
  </ul>
';

if(strlen($_GET['msg']) > 0)
  $error_message = form_message(explode(',', $_GET['msg']));

//echo '<a href="npcprofile.php?npc=Lakisha Pawlak"><img src="gfx/npcs/banker_lakisha.png" align="right" width="350" height="427" alt="(Lakisha the Banker)" /></a>';

include 'commons/dialog_open.php';

if(count($errors) > 0)
  echo '<p>' . implode('</p><p>', $errors) . '</p>';
else
{
  echo '<p>Maintaining the Park needs requires a lot of work, and equipment.  I\'d be greatful for any help you could provide.</p>';
}

include 'commons/dialog_close.php';
?>
     <form method="post">
     <table class="nomargin">
      <tr>
       <td valign="top">
        <table>
         <tr class="titlerow"><th></th><th></th><th>Equipment</th></tr>
<?php
$rowclass = begin_row_class();

foreach($items_in as $id=>$in)
{
  $itemname = $in[0];
  $quantity = $in[1];

  if($id == 1000)
    echo '<tr class="' . $rowclass . '" style="border-top: 1px solid #999">';
  else
    echo '<tr class="' . $rowclass . '">';

  if($inventory[$itemname]['qty'] < $quantity)
    echo '<td><input type="radio" disabled /></td><td class="failure centered">';
  else
    echo '<td><input type="radio" name="in" value="' . $id . '" /></td><td class="centered">';

  echo (int)$inventory[$itemname]['qty'] . ' / ' . $quantity . '</td><td>' . item_text_link($itemname) . '</a></td>' .
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
