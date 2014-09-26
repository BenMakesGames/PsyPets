<?php
$whereat = 'smithery';
$wiki = 'The_Smithery';
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/userlib.php';
require_once 'commons/itemlib.php';
require_once 'commons/questlib.php';
require_once 'commons/favorlib.php';
require_once 'commons/messages.php';

// any-time items
$give_away = array();

if(($now_month == 10 && $now_day >= 21) || ($now_month == 11 && $now_day <= 3))
  $give_away[1] = array('Potion of Fire-breathing', 'Permanently gives a pet the power of fire-breathing!');

if(($now_month == 12 && $now_day >= 12) || $now_month == 1)
  $give_away[2] = array('Leyden Jar', 'Gives a pet an electric aura!');

if(count($give_away) == 0)
{
  header('Location: ./smith.php');
  exit();
}

if($user['favor'] >= 300 && $_POST['action'] == 'getitem')
{
  $item_index = (int)$_POST['item'];

  $itemname = false;

  if(array_key_exists($item_index, $give_away))
    $itemname = $give_away[$item_index][0];

  if($itemname !== false)
  {
    $id = add_inventory($user['user'], $maker, $itemname, 'Purchased from The Smithery', 'storage/incoming');

    flag_new_incoming_items($user['user']);

    $record = 'bought item - ' . $itemname;

    spend_favor($user, 300, 'bought item - ' . $itemname, $id);

    header('Location: ./specialoffer_smith.php?msg=144:' . $itemname);
    exit();
  }
}

if(strlen($_GET['msg']) > 0)
  $error_message = form_message(explode(',', $_GET['msg']));

include 'commons/html.php';
?>
 <head>
  <title>PsyPets &gt; The Smithery &gt; Special Offer</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4>The Smithery &gt; Special Offer</h4>
     <ul class="tabbed">
      <li><a href="smith.php">Smith</a></li>
      <li><a href="repair.php">Repair</a></li>
      <li><a href="af_getrare2.php">Unique Item Shop</a></li>
      <li><a href="af_combinationstation3.php">Combination Station</a></li>
      <li class="activetab"><a href="specialoffer_smith.php">Special Offer <i style="color:red;">ooh!</i></a></li>
<!--      <li><a href="af_replacegraphic.php">Broken Image Repair</a></li>-->
     </ul>
<?php
// SMITHY NPC NINA
echo '<a href="/npcprofile.php?npc=Nina+Faber"><img src="//saffron.psypets.net/gfx/npcs/smithy2.png" align="right" width="350" height="280" alt="(Nina the Smithy)" /></a>';

if(strlen($_GET['msg']) > 0)
  $error_message = form_message(explode(',', $_GET['msg']));

include 'commons/dialog_open.php';

if($error_message)
  echo $error_message;
else
  echo '<p>Hey there, ' . $user['display'] . '!  Is this somethin\' you\'d be interest\'d in?</p>';

$options[] = '<a href="/buyfavors.php">Support PsyPets; get Favor</a>';

include 'commons/dialog_close.php';

echo '<p>You currently have ' . $user['favor'] . ' Favor.  Each item here costs 300 Favor.</p>';

if(count($options) > 0)
  echo '<ul><li>' . implode('</li><li>', $options) . '</li></ul>';
?>
     <form action="" method="post">
<table>
 <tr class="titlerow">
  <th></th>
  <th></th>
  <th>Item</th>
  <th>Description</th>
 </tr>
<?php
$rowstyle = begin_row_class();

foreach($give_away as $index=>$item)
{
  $details = get_item_byname($item[0]);
?>
      <tr class="<?= $rowstyle ?>">
       <td><input type="radio" name="item" value="<?= $index ?>" /></td>
       <td class="centered"><?= item_display($details) ?></td>
       <td><?= $item[0] ?></td>
       <td><?= $item[1] ?></td>
      </tr>
<?php

  $rowstyle = alt_row_class($rowstyle);
}
?>
</table>
     <p><?php
if($user['favor'] >= 300)
  echo '<input type="hidden" name="action" value="getitem" /><input class="bigbutton" type="submit" value="Buy (300 Favor)" />';
else
  echo '<input type="hidden" name="action" value="getitem" /><input class="bigbutton" type="submit" value="Buy (300 Favor)" disabled />';
?></p>
     </form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
