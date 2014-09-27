<?php
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

$special_offer = (($now_month == 10 && $now_day >= 21) || ($now_month == 11 && $now_day <= 3));
$special_offer = $special_offer || (($now_month == 12 && $now_day >= 12) || $now_month == 1);

$favor_cost = 300;

//                                            offset
$monthly_index = $now_year * 12 + $now_month - 24084;

$recurring_monthly = array(
  1 => array(
    1 => array('New Year Fireworks', 'Provides two new profile backgrounds'),
    2 => array('Nanobots', 'Transforms into Nanobot Teddy Bear and Nanobot Cloth Blueprint'),
    3 => array('Wand of Meteors', 'Attack your friends and enemies with meteor showers!'),
  ),
	2 => array(
    1 => array('ABC Blocks', 'Take Apart to get a variety of colored letter blocks'),
    2 => array('Andromeda', 'Powerful equipment; combine with Milky Way and Aging Root for Milkomeda'),
    3 => array('Milky Way', 'Powerful equipment; combine with Andromeda and Aging Root for Milkomeda'),
  ),
  3 => array(
    1 => array('Pot of Gold', 'Loot for gold; refill at The Smithery'),
    2 => array('Hungry Triangle', 'Its final form reveals a unique power!'),
    3 => array('Noodle Wand', 'Indestructible equipment that boosts intelligence; also summons pastas and noodles!'),
  ),
  4 => array(
    1 => array('Egg Despeckler', 'Removes the Speckles from Speckled Eggs'),
    2 => array('Wand of Blunders', 'Turns into a Wand of Wonder; may do a few silly things first'),
  ),
  5 => array(
    1 => array('Hungry Shovel (level 0)', 'A mining equipment of increasing power!'),
    2 => array('May Flowers', 'Grows a May Flower every 22 hours'),
    3 => array('Caduceus', 'Mythical equipment for chemists and magic-binders'),
  ),
  6 => array(
    1 => array('Phoenix Egg', 'Hatch one of a few unique pets'),
    2 => array('Refreshing Spring Blueprint', 'House add-on that provides a half-hourly action, and several titles'),
    3 => array('Juno', 'Indestructible equipment; can be transformed into Hera'),
    4 => array('Hera', 'Indestructible equipment; can be transformed into Juno'),
  ),
  7 => array(
    1 => array('Magic Paintbrush', 'Provides five new profile backgrounds'),
    2 => array('Hungry Sidewalk Blueprint', 'Provides exchanges for common materials and other items'),
    3 => array('Snow Cone Staff', 'Indestructible equipment; summons Unflavored Snowballs, which can be prepared into treats for your pets'),
  ),
  8 => array(
    1 => array('Hungry Chicken', 'Lays many kinds of eggs, including the rare Crystal Egg'),
    2 => array('Augustus', 'Allows you to send pets on a quest for Rome'),
    3 => array('Bubble Wand', 'Indestructible equipment; use soaps to change your profile; more!'),
  ),
  9 => array(
    1 => array('Text Adventure Game', 'Rewards the player with two other unique items'),
    2 => array('Prismatic Fountain', 'Lets you rainbow-fy plaza posts; provides hourly love'),
  ),
  10 => array(
    1 => array('Midnight Scythe', 'Indestructible equipment'),
    2 => array('Magic Hat', 'Summons a Rabbit and vanishes; may summon other items before vanishing'),
  ),
  11 => array(
    1 => array('The Pen Is Mightier Than The Sword', 'Indestructible equipment for crafters'),
    2 => array('Black Friday', 'Indestructible equipment'),
    3 => array('Scroll of Sparkles', 'Gives your pet a special shine!'),
  ),
  12 => array(
    1 => array('Abominable Snow Machine DX 2000', 'Changes your profile background'),
    2 => array('Dreidel', 'Half-hourly toy; lets you play a PsyPets-wide game of Dreidel'),
  ),
);

// any-time items
$give_away = array(
  1 => array('Erstwhile Wand', 'Summons a single <a href="/encyclopedia.php?monthly=on&submit=Search">Erstwhile item</a> at random, then vanishes!'),
  3 => array('Teleporter', 'Allows you to send items to other Teleporter-owning players, without their permission!'),
  5 => array('Post Office Box Ticket', 'Increases post office box size by 400'),
  2 => array('Deck of Many Things', 'Does something random, and sometimes something bad.  For masochists and collectors only * <i>(see below)</i>'),
  4 => array('Group Charter', 'Starts a new Group * <i>(see below)</i>'),
);

$customs = array();

// <= 0, so we catch free-made items
$command = 'SELECT favor FROM psypets_favor_history WHERE userid=' . $user['idnum'] . ' AND value<=0 ORDER BY timestamp DESC';
$favors = $database->FetchMultiple($command, 'fetching favors');

$index = 100;

if(count($favors) > 0)
{
  foreach($favors as $favor)
  {
    if(substr($favor['favor'], 0, 15) == 'custom item - "')
      $customs[] = substr($favor['favor'], 15, strlen($favor['favor']) - 16);
    else if(substr($favor['favor'], 0, 22) == 'custom avatar item - "')
      $customs[] = substr($favor['favor'], 22, strlen($favor['favor']) - 23);
  }
}

if($user['favor'] >= $favor_cost && $_POST['action'] == 'getitem')
{
  list($category, $index) = explode('_', $_POST['item']);

  $itemname = false;

  if($category == 'monthly' && $index == $monthly_index)
    $itemname = $unique_monthly[$monthly_index][0];
  else if($category == 'recurring' && array_key_exists($index, $recurring_monthly[$now_month]))
    $itemname = $recurring_monthly[$now_month][$index][0];
  else if($category == 'anytime' && array_key_exists($index, $give_away))
    $itemname = $give_away[$index][0];
  else if($category == 'custom' && array_key_exists($index, $customs))
    $itemname = $customs[$index];

  if($itemname !== false)
  {
    $id = add_inventory($user['user'], '', $itemname, 'Purchased from the Unique Item Shop', 'storage/incoming');

    flag_new_incoming_items($user['user']);

    $record = 'bought item - ' . $itemname;

    spend_favor($user, $favor_cost, 'bought item - ' . $itemname, $id);

    require_once 'commons/dailyreportlib.php';
    record_daily_report_stat('Unique Item Shop: Someone Bought a ' . $itemname, 1);
    
    header('Location: ./af_getrare2.php?msg=144:' . $itemname);
    exit();
  }
}

if(array_key_exists($monthly_index, $unique_monthly))
{
  $monthly_item = $unique_monthly[$monthly_index];
}
else
  $monthly_item = false;

if($user['is_artist'] == 'yes' && $monthly_item !== false)
{
  $free_item = get_quest_value($user['idnum'], 'artist monthly ' . $now_year . ' ' . $now_month);

  $dialog_extra = '<p>As a PsyPets artist, you may claim this month\'s unique item for free.';

  if($free_item === false)
  {
    $dialog_extra .= '</p>';

    if($_GET['dialog'] == 'artistmonthly')
    {
      add_quest_value($user['idnum'], 'artist monthly ' . $now_year . ' ' . $now_month, 1);

      $id = add_inventory($user['user'], '', $monthly_item[0], 'Thank you! :)', 'storage/incoming');

      flag_new_incoming_items($user['user']);

      $message = '<span class="success">Done!  You\'ll find the ' . $monthly_item[0] . ' in <a href="incoming.php">Incoming</a>.</span>';
    }
    else
      $options[] = '<a href="af_getrare2.php?dialog=artistmonthly">Claim a free ' . $monthly_item[0] . '</a>';
  }
  else
    $dialog_extra .= '  It looks like you\'ve already done so this month, but don\'t forget to come back in ' . date('F', strtotime('+1 month')) . '!</p>';
}

if(strlen($_GET['msg']) > 0)
  $error_message = form_message(explode(',', $_GET['msg']));

include 'commons/html.php';
?>
 <head>
  <title>PsyPets &gt; The Smithery &gt; Unique Item Shop</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="smith.php">The Smithery</a> &gt; Unique Item Shop</h4>
     <ul class="tabbed">
      <li><a href="smith.php">Smith</a></li>
      <li><a href="repair.php">Repair</a></li>
      <li class="activetab"><a href="af_getrare2.php">Unique Item Shop</a></li>
      <li><a href="af_combinationstation3.php">Combination Station</a></li>
<?php
if($special_offer)
  echo '<li><a href="specialoffer_smith.php">Special Offer <i style="color:red;">ooh!</i></a></li>';
?>
<!--      <li><a href="af_replacegraphic.php">Broken Image Repair</a></li>-->
     </ul>
<a href="/npcprofile.php?npc=Nina+Faber"><img src="//saffron.psypets.net/gfx/npcs/smithy2.png" align="right" width="350" height="280" alt="(Nina the Smithy)" /></a>
<?php
include 'commons/dialog_open.php';

if($error_message)
  echo '<p>' . $error_message . '</p>';
else if($message)
  echo '<p>' . $message . '</p>';
else
{
?>
<p>Apart from mining, collection of antiques and rare items is another one of my hobbies.  I always keep at least one of everything myself, but I do not mind to sell all the extras I collect.</p>
<p>If you see what you want, let me know. All items here cost <strong>300 Favor</strong>.</p>
<?php
  echo $dialog_extra;
}

$options[] = '<a href="/buyfavors.php">Support PsyPets; get Favor</a>';

include 'commons/dialog_close.php';

if(count($options) > 0)
  echo '<ul><li>' . implode('</li><li>', $options) . '</li></ul>';

$rowstyle = begin_row_class();
?>
     <form action="af_getrare2.php" method="post">
     <table>
<?php
if($monthly_item !== false)
{
  $details = get_item_byname($monthly_item[0]);
?>
      <tr class="titlerow">
       <th></th>
       <th></th>
       <th>Item</th>
       <th>Available <?= date('F Y') ?> only</th>
      </tr>
      <tr class="<?= $rowstyle ?>">
       <td><input type="radio" name="item" value="monthly_<?= $monthly_index ?>" /></td>
       <td class="centered"><?= item_display($details) ?></td>
       <td><?= $monthly_item[0] ?></td>
       <td><?= $monthly_item[1] ?></td>
      </tr>
<?php
}
?>
      <tr class="titlerow">
       <th></th>
       <th></th>
       <th>Item</th>
       <th>Available every <?= date('F') ?></th>
      </tr>
<?php
foreach($recurring_monthly[$now_month] as $index=>$item)
{
  $rowstyle = alt_row_class($rowstyle);
  $details = get_item_byname($item[0]);
?>
      <tr class="<?= $rowstyle ?>">
       <td><input type="radio" name="item" value="recurring_<?= $index ?>" /></td>
       <td class="centered"><?= item_display($details) ?></td>
       <td><?= $item[0] ?></td>
       <td><?= $item[1] ?></td>
      </tr>
<?php
}
?>
      <tr class="titlerow">
       <th></th>
       <th></th>
       <th>Item</th>
       <th>Available any time</th>
      </tr>
<?php
foreach($give_away as $index=>$item)
{
  $rowstyle = alt_row_class($rowstyle);
  $details = get_item_byname($item[0]);
?>
      <tr class="<?= $rowstyle ?>">
       <td><input type="radio" name="item" value="anytime_<?= $index ?>" /></td>
       <td class="centered"><?= item_display($details) ?></td>
       <td><?= $item[0] ?></td>
       <td><?= $item[1] ?></td>
      </tr>
<?php
}
?>
      <tr class="titlerow">
       <th></th>
       <th></th>
       <th>Item</th>
       <th>Custom-made by you</th>
      </tr>
<?php
foreach($customs as $index=>$item)
{
    $rowstyle = alt_row_class($rowstyle);
    $details = get_item_byname($item);
?>
      <tr class="<?= $rowstyle ?>">
       <td><input type="radio" name="item" value="custom_<?= $index ?>" /></td>
       <td class="centered"><?= item_display($details) ?></td>
       <td colspan="2"><?= $item ?></td>
      </tr>
<?php
}
?>
     </table>
     <p><?php
if($user['favor'] >= $favor_cost)
  echo '<input type="hidden" name="action" value="getitem" /><input type="submit" class="bigbutton" value="Gimme (300 Favor)" />';
else
  echo '<input type="hidden" name="action" value="getitem" /><input type="submit" class="bigbutton" value="Gimme (300 Favor)" disabled />';
?></p>
     </form>
     <p>* Though these items can be obtained through normal game play (without spending Favor), they may be very difficult to acquire in practice.</p>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
