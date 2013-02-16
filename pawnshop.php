<?php
$whereat = 'pawnshop';
$wiki = 'Pawn_Shop';
$require_petload = 'no';

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
require_once 'commons/itemstats.php';

if($user['license'] == 'no')
{
  header('Location: ./ltc.php');
  exit();
}

$storage_items = $database->FetchMultiple('SELECT * FROM monster_inventory WHERE user=' . quote_smart($user['user']) . " AND location='storage' ORDER BY itemname");

if(count($storage_items) == 0)
{
  header('Location: /pawnshopempty.php');
  exit();
}

// trade groups:
// 0 - undefined
// 1 - food
// 2 - non-food/non-resources
// 3 - resources
/*
$hunt_item = get_global("dailyhunt");
$hunt_item_details = get_item_byname($hunt_item);
*/
function total_ord($s)
{
  $total = 0;
  for($i = 0; $i < strlen($s); ++$i)
    $total += ord($s{$i});

  return $total;
}

function pawnshop_item($item, $seed)
{
  global $EASTER;

  $seed += total_ord($item['itemname']);

  srand($seed);

  if(rand(1, 10) == 1 || $item['can_pawn_with'] != 'yes')
    return array('', 0, 0);
  else
  {
    $min_value = ceil($item['value'] * 0.66);
    $max_value = floor($item['value'] * (1 + 0.66) / 2);

    if($max_value < $min_value)
      $max_value = $min_value;

//    echo "for " . $item["itemname"] . "<br>\n";
//    echo "min_value = $min_value<br>max_value = $max_value<br>\n";

    $actual_value = rand($min_value, $max_value);
    $min_act_value = $actual_value - ceil($item['value'] / 10);
    $max_act_value = $actual_value + ceil($item['value'] / 10);

//    echo "actual_value = $actual_value<br>\n";
     
    if($min_act_value < $min_value)
      $min_act_value = $min_value;
    if($max_act_value > $max_value)
      $max_act_value = $max_value;

//    echo "min_act_value = $min_act_value<br>max_act_value = $max_act_value<br>\n";

    if($st_patricks)
      $min_act_value = $min_act_value . ' AND itemname NOT LIKE \'%green%\'';

    if($EASTER > 2)
      $command = 'SELECT * FROM monster_items WHERE ' .
                 "value<=$max_act_value AND value>=1 AND custom='no' AND rare='no' AND can_pawn_with='no' AND itemname LIKE '%chocolate%'";
    else
      $command = 'SELECT * FROM monster_items WHERE ' .
                 "value<=$max_act_value AND value>=$min_act_value AND can_pawn_for='yes'";

		$possible_items = $GLOBALS['database']->FetchMultiple($command);

    if(count($possible_items) == 0)
      return array('', 0, 0);
    else
    {
      $possibility = $possible_items[array_rand($possible_items)];

      return array($possibility['itemname'], $min_act_value, $max_act_value);
    }
  }
}

if($_GET['dialog'] == 5)
{
  if(delete_inventory_byname($user['user'], 'Pyrestone Bear Trap', 1, 'storage') == 1)
  {
    add_inventory($user['user'], '', 'Hammer Blueprint', '', $user['incomingto']);
    $thanks_dialog = true;

    require_once 'commons/statlib.php';
    record_stat($user['idnum'], 'Traded with Tony for a Hammer Blueprint', 1);
  }
}

$pawnshop_now = floor($now / (60 * 60 * 25));

//$havehuntitems = 0;
$bear_trap_trade = false;

foreach($storage_items as $item)
{
  $details = get_item_byname($item['itemname']);

  if($item['itemname'] == 'Pyrestone Bear Trap')
    $bear_trap_trade = true;

  if($item['health'] < $details['durability'])
    continue;

  $tradefor = pawnshop_item($details, $user['signupdate'] + $pawnshop_now);

  if($tradefor[0] == '')
    continue;

  $items_to_trade[$item['itemname']]++;
  $item_trades[$item['itemname']] = $tradefor;
}

if($_POST['submit'] == 'Trade')
{
  if($pawnshop_now != $_POST['current_time'])
  {
    $dialog_generic = '
      <p>Hey, I actually just got word of some... important information, while you were deciding.  I gotta\' switch some stuff around here.</p>
      <p>Sorry about that.  That\'s life, you know?</p>
    ';
  }
  else
  {
    
    $check_store_stock = false;
    $trades = 0;

    foreach($_POST as $key=>$value)
    {
      if(is_numeric($value) && floor($value) == $value && $value > 0)
      {
        $itemname = urldecode($key);

        if(array_key_exists($itemname, $items_to_trade))
        {
          if($value > $items_to_trade[$itemname])
            $value = $items_to_trade[$itemname];

          $details = get_item_byname($itemname);

          $value = delete_inventory_fromstorage($user['user'], $itemname, $value);

          if($value > 0)
          {
            add_inventory_quantity($user['user'], '', $item_trades[$itemname][0], 'Traded for at the Pawn Shop', $user['incomingto'], $value);

            $trades += $value;

            record_item_disposal($itemname, 'pawned', $value);
            record_item_acquisition($item_trades[$itemname][0], $value);

            if($itemname == 'Barrel' && $item_trades[$itemname][0] == 'Fish')
            {
              $badges = get_badges_byuserid($user['idnum']);
              
              if($badges['fish-in-barrel'] == 'no')
              {
                set_badge($user['idnum'], 'fish-in-barrel');
                $fish_badge = true;
              }
            }
          }
        }
      }
    }

    if($trades > 0)
    {
      header('Location: /pawnshop.php?dialog=' . ($fish_badge ? 'thanks-for-all-the-barrels' : 'thanks'));
      exit();
    }
    else
    {
      header('Location: /pawnshop.php?dialog=gotnothing');
      exit();
    }
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
      <li class="activetab"><a href="pawnshop.php">Pawn Shop</a></li>
     </ul>
<?php
if(strlen($_GET['msg']) > 0)
  $error_message = form_message(explode(',', $_GET['msg']));

if($error_message)
  echo '<p class="error">' . $error_message . '</p>';

echo '<a href="/npcprofile.php?npc=Tony+Cables"><img src="//' . $SETTINGS['static_domain'] . '/gfx/npcs/tony.png" align="right" width="350" height="305" alt="(Tony "Shady" Cables)" /></a>';

include 'commons/dialog_open.php';

if($success_message)
  echo '<p>' . $success_message . '</p>';
else if($dialog_generic != '')
  echo $dialog_generic;
else if($_GET['dialog'] == 'thanks')
  echo '<p>Good doing business with you, man.  You\'ll find the items in your ' . $user['incomingto'] . '.</p>';
else if($_GET['dialog'] == 'thanks-for-all-the-barrels')
  echo '<p>Ha.</p><p><i>(You received the Fish in a Barrel Badge!)</i></p><p>*ahem*</p><p>Good doing business with you, man.  You\'ll find the items in your ' . $user['incomingto'] . '.</p>';
else if($_GET['dialog'] == 'gotnothing')
  echo '<p>I don\'t think I heard you right... let\'s try this again.</p>';
else if($broccoli_start_dialog)
{
?>
     <p>Hey, I heard Thaddeus ran in to some trouble with those Rogue Broccoli.  Heh.</p>
     <p>Ksh - get that accusing look off your face.  It's not like that.  I hate the little bastards myself, and trust me: I'd rather fight 100 of 'em than deal with Thaddeus.</p>
     <p>No, I was hoping maybe you could help me... take care of a few of them.</p>
<?php
  $options[] = '<a href="?dialog=2">Ask what he has in mind...</a>';
}
else if($broccoli_intro_dialog)
{
?>
     <p>Heh-heh.  I knew I could count on you, <?= $user['display'] ?>.  But listen: don't tell any of those HERG guys.  They'd throw a hissy.  This requires a little discreteness, you know?</p>
     <p>So here's what I've got in mind:  have your pets bring back the heads of the bastards whenever they beat one up.  Bring the heads to me, and we\'ll work something out.</p>
     <p>Sound good?</p>
<?php
  $options[] = '<a href="?dialog=3">Accept</a>';
}
else if($broccoli_accept_dialog)
{
?>
     <p>Excellent!  That's why I like you, <?= $user['display'] ?>.</p>
     <p>I'll be seeing you around, then.</p>
<?php
  $options[] = '<a href="/pawnshop_broccoli.php">Present Rogue Broccoli loot</a>';
}
else if($broccoli_progress_dialog)
{
?>
     <p>So, <?= $user['display'] ?>... how's the ah, <em>treasure hunt</em> going?</p>
<?php
  $options[] = '<a href="/pawnshop_broccoli.php">Present Rogue Broccoli loot</a>';
}
else if($thanks_dialog)
{
?>
     <p>Dude, thanks a lot.  You'll find the blueprint in your <?= ucfirst($user['incomingto']) ?>.</p>
<?php
  if($bear_trap_trade)
  {
    echo '<p>I notice you\'ve got more of those Pyrestone Bear Traps.  I\'ll take every last one, if you\'ll let me.</p>';
    $options[] = '<a href="?dialog=5">Trade another Pyrestone Bear Trap for another Hammer Blueprint</a>';
  }
}
else if($EASTER)
{
  echo '<p>Eh?  What do you mean my trade offers seem weird today?</p>';
}
else if($st_patricks)
{
  echo '<p>Hey, Lakisha and Matalie cleaned me out of every item with the word "green" in it... something about a St. Patrick\'s Day competition?  Anyway, if you were looking for Green Dragons or something, go talk to them.  They have every one I owned.</p>';
}
else if(($now_month == 10 && $now_day >= 30) || ($now_month == 11 && $now_day == 1))
{
  echo '<p>Psst!  Hey, ' . $user['display'] . '!  C\'mere!</p>';
  $options[] = '<a href="pawnshop_exchange.php">Follow him to the back room...</a>';
}
else
{
  if(count($items_to_trade) > 0)
  {
?>
     <p>Enter the number of items you want to trade from the quantity available in your Storage, if any.  Remember: I don't deal with damaged goods, and I <strong>don't</strong> do refunds.</p>
     <p>If you have any items in Storage that aren't listed here, it's because I don't have an offer for them at the moment.  If you don't like what you see now, come back later; I may have different offers available.</p>
<?php
  }
  else
    echo '<p>I only trade for items that are currently in your storage.  Oh, and I don\'t exchange damaged goods.</p>';

  if($bear_trap_trade)
  {
    echo '<p>Oh, and hey, is that a Pyrestone Bear Trap you got there?  Hey, uh, don\'t ask why, but I really need some of those.  Tell you what, how about I give you a Hammer Blueprint in exchange.  Deal?</p>';
    $options[] = '<a href="?dialog=5">Trade a Pyrestone Bear Trap for a Hammer Blueprint</a>';
  }
}

include 'commons/dialog_close.php';

if(count($options) > 0)
  echo '<ul><li>' . implode('</li><li>', $options) . '</li></ul>';

if(count($items_to_trade) > 0)
{
  $any_items = false;

  $rowclass = begin_row_class();

  foreach($items_to_trade as $itemname=>$quantity)
  {
    $details = get_item_byname($itemname);
    
    if($any_items === false)
    {
?>
     <form method="post">
     <input type="hidden" name="current_time" value="<?= $pawnshop_now ?>" />
     <table>
      <tr class="titlerow">
       <th class="centered">Quantity</th>
       <th></th>
       <th>Item</th>
       <th><img src="gfx/shim.gif" width="24" height="1" alt="" /></th>
       <th><img src="gfx/shim.gif" width="16" height="1" alt="" /></th>
       <th><img src="gfx/shim.gif" width="24" height="1" alt="" /></th>
       <th></th>
       <th>Offered&nbsp;Item</th>
      </tr>
<?php
      $any_items = true;
    }

    $tradefor = $item_trades[$itemname][0];
    $details2 = get_item_byname($tradefor);
    $tradeimg = item_display($details2, '');
?>
      <tr class="<?= $rowclass ?>">
       <td><nobr><input type="number" min="0" max="<?= $quantity ?>" name="<?= urlencode($itemname) ?>" maxlength="<?= strlen($quantity) ?>" style="width:60px;" /> / <?= $quantity ?></nobr></td>
       <td class="centered"><?= item_display($details, '') ?></td>
       <td><?= $itemname ?></td>
       <td></td>
       <td><img src="/gfx/lookright.gif" width="16" height="16" alt="(trades for)" /></td>
       <td></td>
       <td class="centered"><?= $tradeimg ?></td>
       <td><?= $tradefor ?></td>
      </tr>
<?php
    $rowclass = alt_row_class($rowclass);
  }
  
  if($any_items === true)
  {
?>
      <tr>
       <td colspan="10"></td>
      </tr>
      <tr>
       <td colspan="5" class="centered"><input type="submit" name="submit" value="Trade" /></td>
       <td colspan="5"></td>
      </tr>
     </table>
     </form>
<?php
  }
  else
    echo '<p>None of your items in Storage have trade offers at this time.</p>';
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
