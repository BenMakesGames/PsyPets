<?php
$wiki = 'The_Alchemist\'s';
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
require_once 'commons/questlib.php';

if($now - $user['signupdate'] > 56 * 24 * 60 * 60)
{
  $questval = get_quest_value($user['idnum'], 'AlchemistQuest');
  if((int)$questval['value'] < 2)
  {
    header('Location: ./alchemist_problem.php');
    exit();
  }
}

$seed = (int)($now / (28 * 24 * 60 * 60) + 1);

$change_time = (int)($now / (28 * 24 * 60 * 60) + 1) * (28 * 24 * 60 * 60);

// quantities are based on having ~350 players active in a 168-hour (1-week) period

// 0-24: do not accept (too rare)
// 25-100:  1
// 101-250: 2
// 250-500: 3
// 501-750: 4
// 751-1000: 5
// 1001-1250: 6
// 1251-1500: 7
// 1501-1750: 8
// 1751-2000: 10
// 2001-2500: 15
// 2501+: do not accept (too common)

// level 0-9: do not accept (too low-level)
// level 10-14: +1
// level 15-19: no change
// level 20+: -1

// very expensive materials: -1
// easy-to-acquire materials: +1

$items_in = array(
  -11 => 'bindings',
  
   1 => array('Hungry Cap', 2),               // 426 (-1 for high level)
   2 => array('Elfin Pride', 1),              // 46 (-1 for high level)
   3 => array('Shadehunter\'s Sash', 1),      // 163 (-1 for expensive materials)
   4 => array('Pluto and Charon', 4),         // 617
   5 => array('The Salamander\'s Cape', 2),   // 222
   6 => array('Dree-oog Flail', 2),           // 74 (+1 until better-established numbers)
   7 => array('Infinite Measuring Tape', 5),  // 378 (+1 for low level, +1 for easy materials)

  -7 => 'carpentry',

  11 => array('Small Greek Trireme', 1), // 169 (-1 for high level; -1 for huge material list)
  12 => array('Darkwood Shamisen', 2),   // 37
  13 => array('Champignon', 3),          // 90 (+1 for low level; +1 for easy materials)
  14 => array('Amber Stick', 4),         // 383 (+1 for low level)
  15 => array('Couch', 6),               // 759 (+1 for low level)
  16 => array('Leather Couch', 5),       // 747 (+1 for low level)
  17 => array('Triangle Bow', 2),        // 225

  -12 => 'chemistry',
  
 101 => array('Big Bada Boom', 1),  // 39
 102 => array('Love', 2),           // 267 (-1 for high level)
  
  -1 => 'electronics',

  21 => array('PSYCHE', 2),                // 336 (-1 for high level)
  22 => array('8kHz Bow', 1),              // 87 (-1 for high level)
  23 => array('Eye-Con 5000', 3),          // 272
  24 => array('Synthesizer', 4),           // 313 (+1 for low level)
  25 => array('Timey Wimey Detector', 1),  // 73
  26 => array('Anaglyphic IR Goggles', 2), // 45 (+1 for low level)

 -10 => 'handicrafts',

  31 => array('The Margrave', 1),         // 17 (-1 for high level)
  32 => array('Voodoo', 2),               // 66 (+1 for low level)
  33 => array('Blue Yardstick', 1),       // 27
  34 => array('Blue Bow Bow', 6),         // 664 (+1 for low level; +1 until better-established numbers)

  -6 => 'jewelry',

  41 => array('Lothlórien', 2),        // 223
  42 => array('Gold Chandelier', 4),   // 543
  43 => array('Silver Chandelier', 5), // 528 (+1 to artificially reduce value of silver)
  44 => array('Imperial Scepter', 1),  // 218 (-1 for high level)
  45 => array('Vexed Earrings', 4),    // 354 (+1 for low level)

 -13 => 'leatherworks',
 
 111 => array('Orion', 1),             // 70
 112 => array('Bagpipes', 6),          // 1087
 113 => array('Goldleaf Belt', 2),     // 51 (+1 until better-established numbers)
 114 => array('White Stag', 5),        // 567 (+1 for low level)
 115 => array('Mercury', 4),           // 625

  -9 => 'mechanics',

  51 => array('Solar Sail (folded)', 1),          // ?
  52 => array('Corn Syruper', 4),                 // 281 (+1 for low level)
  53 => array('Cannon', 4),                       // 419 (+1 for low level)
  54 => array('6-Cylinder Combustion Engine', 4), // 310 (+1 for low level)
  55 => array('Planetarium Projector', 5),        // 962
  56 => array('Kobold\'s Wheelbarrow', 2),        // 72 (+1 until better-established numbers)

  -8 => 'paintings',

  61 => array('Egyptian Mural', 4),                  // 426 (+1 for low level)
  62 => array('Mural', 5),                           // 707 (+1 for low level)
  63 => array('Portrait of Adele Bloch-Bauer I', 2), // 120 (-1 for high  level; +1 until better-established numbers)
  64 => array('Nude, Green Leaves and Bust', 2),     // 112 (-1 for high  level; +1 until better-established numbers)
  65 => array('Berserker', 4),                       // 239 (+1 for low level; +1 until better-established numbers)

  -5 => 'sculptures',

  71 => array('Unreasonably Large Hoard of Unreasonably Large Swords', 1), // 320 (-1 for high level; -1 for ridiculous material requirements)
  72 => array('Model Colossus', 2),     // 55 (+1 until better-established numbers)
  73 => array('Midheaven Vase', 7),     // 1282
  74 => array('Moki Mask', 6),          // 764 (+1 for low level)
  75 => array('Bat Totem', 7),          // 1514 (+1 for low level; -2 for conflict of interests)

  -2 => 'smiths',

  81 => array('Dwimordene', 2),         // 139
  82 => array('Phoenix Axe', 2),        // 166
  83 => array('Hadalglaive', 3),        // 319
  84 => array('Soulhammer', 2),         // 190
  85 => array('Spear of Destiny', 1),   // 222 (-1 for high level)
  86 => array('Eaty Hammer', 1),        // 65
  87 => array('Knefarious Knife', 5),   // 745 (+1 for low level)
  88 => array('Sardonic Axe', 4),       // 352 (+1 for low level)

  -3 => 'tailory',
  
  92 => array('Gaea\'s Embrace', 1),        // 73
  93 => array('An Almost-Fanatical Devotion to the Pope', 4), // 262 (+1 for low level)
  94 => array('Electrodyne Cape', 3),       // 228 (+1 for low level)
  69 => array('Serpent\'s Pass', 3),        // 220 (+1 for low level)
  97 => array('Wintergreen Cloak', 5),      // 684 (+1 for low level)

  -4 => 'miscellaneous',
  
 1000 => array('Potion Ticket', 1),
 1100 => array('1st Place', 1),
 1101 => array('2nd Place', 1),
 1102 => array('3rd Place', 1),
 1200 => array('Password Minigame', 6),
);

mt_srand($seed);

for($x = 1; $x < 100; ++$x)
{
  if(array_key_exists($x, $items_in) && mt_rand(1, 5) <= 3)
    unset($items_in[$x]);
}

mt_srand();

$items_out = array(
 100 => 'Potion Ticket',
   2 => 'Child\'s Play',
   3 => 'Phoenix\'s Tears',
   4 => 'Atrophy',
  23 => 'Aquaphobia',
   5 => 'Blur',
  30 => 'Chip',
   6 => 'Cloud',
  26 => 'Collapse',
  36 => 'Conform',
   7 => 'Crash',
  29 => 'Dilute',
  19 => 'Discipline',
  25 => 'Domesticate',
  31 => 'Dull',
  28 => 'Entropy',
   9 => 'Expose',
  35 => 'Experiment',
  20 => 'Extrovert',
  10 => 'Fray',
  11 => 'Forget',
  12 => 'Hackney',
  21 => 'Introvert',
  32 => 'Mold',
  13 => 'Pacify',
  24 => 'Paradox',
  37 => 'Play',
  14 => 'Quit',
  33 => 'Rebel',
  34 => 'Rely',
  15 => 'Rust',
  27 => 'Smear',
   8 => 'Static',
  16 => 'Stumble',
  17 => 'Urbanize',
  18 => 'Waste',
  38 => 'Work',
  22 => 'Laze',
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

if($_POST['submit'] == 'Exchange Items')
{
  $in = (int)$_POST['in'];
  $out = (int)$_POST['out'];
  $quantity = (int)$_POST['quantity'];

  // Potion Ticket for Potion Ticket
  if($in == 20 && $out == 18)
    $errors[] = 'Ah, okay.  I guess, give me your Potion Ticket... good.  And now I\'ll give it back to you.  There.  Exchange made <img src="gfx/emote/suspicious.gif" width="16" height="16" alt="" />';

  $payment = $items_in[$in];

  if($quantity < 1)
    $errors[] = 'You\'d like to make "' . trim($_POST['quantity']) . '" exchanges...?';

  if(!array_key_exists($in, $items_in) || $in <= 0)
    $errors[] = 'Unfortunately I must insist on receiving payment.  The list is long and varried, however; you can pick whichever is most convenient for you.';
  else
  {
    if($inventory[$payment[0]]['qty'] < $payment[1] * $quantity)
      $errors[] = 'You do not have enough ' . $payment[0] . ' items in your storage...';
  }

  if(!array_key_exists($out, $items_out))
    $errors[] = 'Which potion would you like?  You didn\'t choose one...';

  if(count($errors) == 0)
  {
    delete_inventory_byname($user['user'], $payment[0], $payment[1] * $quantity, 'storage');

    $inventory[$payment[0]]['qty'] -= $payment[1] * $quantity;

    add_inventory_quantity($user['user'], 'u:24628', $items_out[$out], $user['display'] . ' traded with Thaddeus for this item', $user['incomingto'], $quantity);

    if($quantity == 1)
      $error_message = 'Excellent.  You\'ll find the ' . $items_out[$out] . ' in your ' . $user['incomingto'] . '.';
    else
      $error_message = 'Excellent.  You\'ll find the ' . $items_out[$out] . ' in your ' . $user['incomingto'] . ' - all ' . $quantity . ' of them.';

    require_once 'commons/statlib.php';
    record_stat($user['idnum'], 'Made an Exchange at the Potion Shop', $quantity);
  }
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; The Alchemist's &gt; Potion Shop</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4>The Alchemist's &gt; Potion Shop</h4>
     <ul class="tabbed">
      <li><a href="/alchemist.php">General Shop</a></li>
      <li class="activetab"><a href="/alchemist_potions.php">Potion Shop</a></li>
      <li><a href="/alchemist_pool.php">Cursed Pool</a></li>
      <li><a href="/alchemist_transmute.php">Pet Transmutations</a></li>
     </ul>
<?php
if(strlen($_GET["msg"]) > 0)
  $error_message = form_message(explode(',', $_GET['msg']));

echo '<a href="/npcprofile.php?npc=Thaddeus"><img src="//' . $SETTINGS['static_domain'] . '/gfx/npcs/thaddeus.png" align="right" width="350" height="250" alt="(Thaddeus the Alchemist)" /></a>';

$options = array();

include 'commons/dialog_open.php';

if($error_message)
  echo "<p>$error_message</p>";
else if(count($errors) > 0)
  echo '<p class="failure">' . implode('</p><p class="failure">', $errors) . '</p>';
else if($_GET['dialog'] == 1)
{
?>
  <p>If you are interested in making your own potions, I recommend getting an laboratory of your own.  Preferably up in a high tower.</p>
  <p>Hm?  Why a tower?  Alchemy is very closely linked with astronomy and astrology.  Your laboratory will also be an observatory.</p>
  <p>Anyway, there are a few things you can make in your home kitchen, even without an advanced laboratory, but you need to be careful!  For Ki Ri Kashu's sake, never mix Bleach and Ammonia!  Also, Aging Root, Venom, and Poison Ivy are a bad combination.  But other than that, feel free to experiment.  You never know what you might come up with.</p>  
<?php
}
else
{
?>
  <p>Interested in my specialty potions?  I cannot recommend them for human consumption, but they're quite useful for pets.  Each potion targets a specific...</p>
  <p>Sorry, explaining it that way will be too complicated.  Well, think of these potions as a way to go back in time.  Undo past mistakes.  Did a pet accidentally develop unnecessary strength?  Atrophy it away!  Head filled with useless knowledge?  You can use Forget...</p>
  <p>The pet will feel younger again, ready to develop new skills as if it never had the old ones.</p>
  <p>Quite handy, eh?</p>
  <p>Oh, and Child's Play is just a general mental growth potion - useful for anyone.</p>
  <p>I'll take any of the items listed on the left for any of the potions on the right.  Mix and match to your heart's content!</p>
<?php
  $options[] = '<a href="alchemist_potions.php?dialog=1">Ask for tips on brewing your own potions</a>';
}

include 'commons/dialog_close.php';

if(count($options) > 0)
  echo '<ul><li>' . implode('</li><li>', $options) . '</li></ul>';

echo '<p><i>(The items accepted will change in ' . duration($change_time - $now, 2) . '.)</i></p>';
?>
     <form action="alchemist_potions.php" method="post">
     <table>
      <tr>
       <td valign="top">
        <div style="height:400px;overflow:auto;">
        <table>
<?php
$rowclass = begin_row_class();

foreach($items_in as $id=>$in)
{
  if($id < 0)
  {
    $new_category = '<tr class="titlerow"><th></th><th colspan="2">Payment (' . $in . ')</th></tr>';
    $rowclass = begin_row_class();
    continue;
  }
  else if($new_category != '')
  {
    echo $new_category;
    $new_category = '';
  }

  $itemname = $in[0];
  $quantity = $in[1];

  echo '<tr class="' . $rowclass . '">';
  if($inventory[$itemname]['qty'] < $quantity)
  {
    echo '<td><input type="radio" disabled /></td><td class="failure centered"><nobr>' .
         (int)$inventory[$itemname]['qty'] . ' / ' . $quantity . '</nobr></td><td>' . item_text_link($itemname, 'failure') . '</td>';
  }
  else
  {
    echo '<td><input type="radio" name="in" value="' . $id . '" /></td><td class="centered"><nobr>' .
         (int)$inventory[$itemname]['qty'] . ' / ' . $quantity . '</nobr></td><td>' . item_text_link($itemname) . '</td>';
  }
  echo '</tr>';

  $rowclass = alt_row_class($rowclass);
}
?>
        </table>
        </div>
       </td>
       <td valign="top" style="padding-left: 2em;">
        <div style="height:400px;overflow:auto;">
        <table>
         <tr class="titlerow"><th></th><th></th><th>Potion</th></tr>
<?php
$rowclass = begin_row_class();

foreach($items_out as $id=>$potion)
{
  $details = get_item_byname($potion);
?>
         <tr class="<?= $rowclass ?>">
          <td><input type="radio" name="out" value="<?= $id ?>" /></td>
          <td class="centered"><?= item_display($details) ?></td>
          <td><?= $potion ?></td>
         </tr>
<?php
  $rowclass = alt_row_class($rowclass);
}
?>
        </table>
        </div>
       </td>
      </tr>
     </table>
     <p>Quantity: <input type="number" min="1" name="quantity" style="width:60px;" value="1" maxlength="2" /> <input type="submit" name="submit" value="Exchange Items" class="bigbutton" /></p>
     </form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
