<?php
$wiki = 'The_Alchemist\'s';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/inventory.php';
require_once 'commons/formatting.php';
require_once 'commons/messages.php';
require_once 'commons/userlib.php';
require_once 'commons/itemlib.php';
require_once 'commons/houselib.php';
require_once 'commons/alchemylib.php';
require_once 'commons/questlib.php';
require_once 'commons/zodiac.php';

if(time() - $user['signupdate'] > 56 * 24 * 60 * 60)
{
  $questval = get_quest_value($user['idnum'], 'AlchemistQuest');
  if((int)$questval['value'] < 2)
  {
    header('Location: /alchemist_problem.php');
    exit();
  }
}

$now = time();

// fetch exchanges
$this_alchemist = $database->FetchMultipleBy(
	"SELECT * FROM monster_smith WHERE type='alchemy' ORDER BY makes ASC",
	'idnum'
);

// fetch inventory
$inventory = $database->FetchMultipleBy('
  SELECT COUNT(idnum) AS qty,itemname
  FROM monster_inventory
  WHERE
    `user`=' . quote_smart($user['user']) . '
    AND `location`=\'storage\'
  GROUP BY itemname
', 'itemname');

$brewid = (int)$_POST['brewid'];

if($brewid > 0)
{
  $quantity = (int)$_POST['quantity'];

  $smith_recipe = $this_alchemist[$brewid];
  if($quantity < 1)
    $error_message = '<em>How</em> many did you want me to make?';
  else if($smith_recipe['idnum'] > 0)
  {
    $ingredients = explode(',', $smith_recipe['supplies']);
    $itemcounts = array();
    foreach($ingredients as $item)
    {
      $datas = explode('|', $item);
      $itemcounts[$datas[1]] += $datas[0];
    }

    $ok = true;

    $itemdescripts = array();
    foreach($itemcounts as $item=>$count)
    {
      if($inventory[$item]['qty'] < $count * $quantity)
        $ok = false;
    }

    if(!$ok)
      $error_message = 'You don\'t have the items I need to make that.';

    if($ok)
    {
      $transaction_value = $smith_recipe["cost"];

      foreach($itemcounts as $item=>$count)
      {
        delete_inventory_byname($user['user'], $item, $count * $quantity, 'storage');
        $inventory[$item]['qty'] -= $count * $quantity;

        $item_details = get_item_byname($item, 'value');
        $transaction_value += $item_details['value'] * $count;
      }

      $alchemist_npc = get_user_byuser('thaddeus', 'idnum');

      $make_list = explode(',', $smith_recipe['makes']);
      foreach($make_list as $item)
        add_inventory_quantity($user['user'], 'u:' . $alchemist_npc['idnum'], $item, 'This item was created by The Alchemist.', $user['incomingto'], $quantity);

      if(in_array('The Writ of Chaos', $make_list))
        header('Location: /alchemist.php?dialog=writ-of-chaos');
      else
        header('Location: /alchemist.php?msg=13:' . $user['incomingto']);

      exit();
    }
  }
  else
    $error_message = 'What\'s that you say?';
}

if($_GET['dialog'] == 'writ-of-chaos')
{
  $dialog = '
    <p>Ah, The Writ of Chaos.  That\'s a strange one, isn\'t it?</p>
    <p>Lance tells me it\'s part of an older religion of the Hollow Earth, predating Ki Ri Kashu.</p>
    <p>I always felt like there must be some deeper meaning to it, though...</p>
    <p class="success"><i>(You received the Writ of Chaos.)</i></p>
  ';
}

// check to see if you have a tower, to give extra options
$house = get_house_byuser($user['idnum']);

if($house === false)
{
  echo "Failed to load your house.<br />\n";
  exit();
}

$addons = take_apart(',', $house['addons']);

$has_tower = (array_search('Tower', $addons) !== false);
$has_shrine = (array_search('Shrine', $addons) !== false);

$options = array();

if($user['show_florist'] == 'no')
{
  if($_GET['dialog'] == 'flowers')
  {
    $florist_dialog = true;
    $database->FetchNone('UPDATE monster_users SET show_florist=\'yes\' WHERE idnum=' . $user['idnum'] . ' LIMIT 1', 'revealing the florist');
    $user['show_florist'] = 'yes';
  }
  else
    $options[] = '<a href="?dialog=flowers">Ask about making the Nettling Scroll</a>';
}

$hephaestus_charm_quest = get_quest_value($user['idnum'], 'hephaestus charm');

if($hephaestus_charm_quest['value'] == 2)
{
  if($_GET['dialog'] == 'hephaestuscharm')
  {
    $dialog = '<p>Ah, terribly sorry, ' . $user['display'] . '.  I\'m afraid I don\'t sell those anymore.  Though... I may...</p><p>Lemme take a look around here.  I may have one or two sitting around...</p>';
    $options[] = '<a href="?dialog=waitforsearch">Wait while he rummages around</a>';
  }
  else if($_GET['dialog'] == 'waitforsearch')
  {
    $dialog = '<p>Yes, yes!  Here we are! <i>(He waves a Hephaestus Charm from behind a few boxes before returning to his desk.)</i></p><p>I had to stop selling them a while ago, you see-- ... well, the reasons aren\'t important.</p><p>Hm...</p><p>You know what, ' . $user['display'] . ', take it.  It\'s yours.  <i>(He hands you the Hephaestus Charm.  Find it in your incoming!)</i>  Consider it a thank-you for helping me out in the past.</p>';

    update_quest_value($hephaestus_charm_quest['idnum'], 3);
    add_inventory($user['user'], 'u:24628', 'Hephaestus Charm', '', 'storage/incoming');
  }
  else
    $options[] = '<a href="?dialog=hephaestuscharm">Ask about buying a Hephaestus Charm</a>';
}

$quest_pattern = get_quest_value($user['idnum'], 'PatternActivation');
if($quest_pattern['value'] == 1)
{
  if($_GET['dialog'] == 5)
    $pattern_dialog = true;
  else if($_GET['dialog'] == 6)
  {
    $pattern_dialog2 = true;
    $user['show_pattern'] = 'yes';
    update_quest_value($quest_pattern['idnum'], 2);
    
    $database->FetchNone('UPDATE monster_users SET show_pattern=\'yes\' WHERE idnum=' . $user['idnum'] . ' LIMIT 1');
  }
  else
    $options[] = '<a href="?dialog=5">Ask about Maze Pieces</a>';
}

$hourglass_quest = get_quest_value($user['idnum'], 'time\'s hourglass');

if($hourglass_quest === false)
{
  if($_GET['dialog'] == 'stereotyping')
  {
    $dialog = '
      <p>Hohoho!  What makes you think I know about anything like that?  Just because I\'m an old man selling potions doesn\'t mean I\'m a conjurer, or enchanter, or something like that!</p>
      <p>Of course... it doesn\'t mean I\'m <em>not</em>, either...</p>
      <p>Haha!  The look on your face, ' . $user['display'] . '!  Fantastic!</p>
      <p>Ah...</p>
      <p>Here, here... perhaps this little trinket will be of interest to you.  It\' a little something I picked up during my travels.</p>
      <p>"Time\'s Hourglass" they call it.  Use this item, and-- ... well, try it yourself, and see.</p>
      <p><i>(You received Time\'s Hourglass!  Find it in your <a href="/incoming.php">Incoming</a>.)</i></p>
    ';

    add_quest_value($user['idnum'], 'time\'s hourglass', 1);

    add_inventory($user['user'], '', 'Time\'s Hourglass', '', 'storage/incoming');
  }
  else
    $options[] = '<a href="?dialog=stereotyping">Ask about the secrets of time!</a>';
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; The Alchemist's</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <?= ($check_message ? "<p style=\"color:blue;\">$check_message</p>" : "") ?>
     <h4>The Alchemist's</h4>
     <ul class="tabbed">
      <li class="activetab"><a href="/alchemist.php">General Shop</a></li>
      <li><a href="/alchemist_potions.php">Potion Shop</a></li>
      <li><a href="/af_trinkets.php">Rare Trinkets</a></li>
      <li><a href="/alchemist_pool.php">Cursed Pool</a></li>
      <li><a href="/alchemist_transmute.php">Pet Transmutations</a></li>
     </ul>
<?php
if(strlen($_GET['msg']) > 0)
  $error_message = form_message(explode(',', $_GET['msg']));

echo '<a href="/npcprofile.php?npc=Thaddeus"><img src="//' . $SETTINGS['static_domain'] . '/gfx/npcs/thaddeus.png" align="right" width="350" height="250" alt="(Thaddeus the Alchemist)" /></a>';

include 'commons/dialog_open.php';

if($dialog)
  echo $dialog;
else if($error_message)
  echo "<p>$error_message</p>";
else
{
  if($florist_dialog)
  {
    echo '
      <p>Ah, of course: you\'ll need a Scabious flower!  Wonderful flower; very useful.</p>
      <p>Not native to this area, however.  It doesn\'t seem to do well in this soil...</p>
      <p>Well, I often have need of it myself, so I got a few seeds for Vanessa and asked that she grow some.  You can buy them from her Flower Shop, if you\'d like.</p>
      <p>Eh?  You\'ve never been to her Flower Shop?  Preposterous!  She runs a great place, and is very knowledgeable... if you haven\'t already gone, you must - I insist.</p>
      <p><i>(The Florist and Greenhouse have been revealed to you!  Find them in the Services menu.)</i></p>
    ';
  }
  else if($pattern_dialog)
  {
    echo '<p>"The Pattern always has been, and always will be, like some abstract thought tacked on in haste.</p>' .
         '<p>"It had been lost to us, blocked by an oily black road, yet those strong and clever enough can still see it, and there are prizes there for those willing to take on the challenge."</p>';

    $options[] = '<a href="?dialog=6">What?</a>';
    $options[] = '<a href="/alchemist.php">Never mind...</a>';
  }
  else if($pattern_dialog2)
  {
?>
     <p>It's something Saberclaw, hecklee, Fexar and Rainribbon told me about <strong>The Pattern</strong>, an odd place to which those Maze Pieces are the key.</p>
     <p>You saw the path on it's surface, yes?  When you go to The Pattern, place that piece somewhere so that the paths line up.  The paths you lay down there cannot be taken, however, without paying a tribute - Pillows, Bear Traps, and other little things.  If you can pay that tribute, however, you may receive a great item in exchange.  I have even heard of people finding <a href="encyclopedia2.php?item=Wand%20of%20Wonder">Wands of Wonder</a> there, though these are quite rare.</p>
     <p><i>(The Pattern has been revealed to you!  Find it in the Recreation menu.)</i></p>
<?php
  }
  else if($_GET['dialog'] == '1' && $has_tower)
  {
?>
     <p><?= $user['display'] ?>, a student of Alchemy?  I can't say I saw it coming.</p>
     <p>The Philosopher's Stone, eh?  It is a dangerous thing.  But if you're asking about it, you'll find out one way or another.</p>
     <p>It does exist, that stone.  I saw it once myself - <em>touched</em> it myself.  It's difficult to describe... it's alluring.  It calls to you, as if it had a mind of its own...</p>
     <p>I'm sure you're more interested in how it's made, though, eh?  Heh.  Honestly I don't precisely know, but I have heard a few things!</p>
     <p>Let me find this... [Thaddeus pulls a book out of his cloak and starts to flip through it.]  Ah, here we are: <q>The Philosopher's Stone is made in the image of the Creation of the World, for one must have its chaos and its prime matter, in which the elements float hither and thither, all mixed together, until they are separated by the fiery spirit.</q></p>
     <p>A bit mysterious, yes?  Here's something else I remember: the Philosopher's Stone is linked closely with astrology: planetary alignments, the zodiac... you get the idea.  One theory goes that you must take some base substance and pass it through 12 stages, one for each zodiac, until it becomes the Philosopher's Stone, a material over which the zodiac no longer holds any power.  That is: it reaches a state that transcends fate and destiny.</p>
     <p>Well, there are as many theories as there are stars in the sky; who knows which is closest to the truth.</p>
     <p>There is one known thing about the Philosopher's Stone, though: those who have searched for it have gone mad, turned up dead, gone missing, and in many cases I'm sure, all three.  I'd advise you to leave it alone, <?= $user['display'] ?>.  Forget about the Philosopher's Stone!</p>
     <p>Well... enough of that.  I'll let you continue browsing over my wares here... let me know if there's anything else you need.</p>
<?php
    $options[] = '<a href="?dialog=2">Ask about the Zodiac</a>';
  }
  else if($_GET['dialog'] == '2')
  {
    if($has_tower)
    {
?>
     <p>We're currently in <?= $WESTERN_ZODIAC[get_western_zodiac($now)] ?>, but you should know that already, <?= $user['display'] ?>.  I suppose you're looking for something more specific, eh?</p>
     <p>The signs hold more sway over the world than personality tendancies.  You've probably realized by now that some alchemical transmutations are easier during different times of the year, or more specifically, during different signs.</p>
     <p>Though Gold and Silver are common among many of the signs, gemstones tend to be much more specific.  Of course it's easy to move from one month to the next - and impossible to go backwards.  Alchemy requires patience, <?= $user['display'] ?>!</p>
<?php
      $options[] = '<a href="?dialog=3">Ask about the Zodiac gemstones</a>';
    }
    else
    {
?>
     <p>Well, we're currently in <?= $WESTERN_ZODIAC[get_western_zodiac($now)] ?>.  You can figure it out yourself easily by looking up the night sky with a telescope.  I recommend getting somewhere high up, away from all this light pollution.</p>
     <p>Er, or you can look at a calendar, I suppose, but that's less interesting...</p>
     <p>Anyway, the positions of the planets, the phases of the moon: these are all important for an Alchemist.  Not only personality, but the transmutation of substances is controlled by the movement of the planets.</p>
     <p>Ask me about it again sometime later once you've had a good look at the sky yourself.</p>
<?php
    }
  }
  else if($_GET['dialog'] == 3 && $has_tower)
  {
?>
     <p>This is invaluable information, so commit it to heart!</p>
     <ol>
      <li>Aries commands the Small Giamond</li>
      <li>Taurus commands both the Small Gemerald and the Small Gemuline</li>
      <li>Gemini commands the Pearl</li>
      <li>Cancer commands the Sardonyx</li>
      <li>Leo commands the Ruby</li>
      <li>Virgo commands the Sapphire</li>
      <li>Libra commands the Opal</li>
      <li>Scorpius commands the Topaz</li>
      <li>Sagittarius commands the Turquoise</li>
      <li>Capricornus commands the Garnet</li>
      <li>Aquarius commands the Amethyst</li>
      <li>Pisces commands the Bloodstone</li>
     </ol>
     <p>There's something else special about Cancer: the Moonstone.  During this sign and this sign alone can Moonstone be transmuted.</p>
<?php
  }
  else if($has_shrine && $_GET['dialog'] == 8)
  {
?>
     <p>Hm?  Looking for spells?  Yes, I do know a couple, of course.  Use caution when dealing with candle magic, though... there are some spells you should never cast...<p>
     <p>Mm, but let's see here... ah yes!  I know one for summoning scrolls!  Before I had the skill to write my own, I used this spell quite a bit!  I should warn you that it's a little unreliable...  The candle arrangement for that one goes: Silver, White, Amethyst Rose.</p>
     <p>Ah, there was also a good one for keeping fires going... yes, yes, how did that one go... Yellow, Fire Spice, Red?  Or maybe it was the other way around.  I always get it wrong.</p>
     <p>And then there's Dancing Lights - named after a similar effect displayed by Wands of Wonder.  It's a delight for the pets!  Anyway, that one's easy: just put a Silver candle after any other two candles.</p>
     <p>I'm sure you can find more spells in old books and tomes.  Just keep an eye out.  You'll find 'em.</p>
<?php
  }
  else
  {
    if(date('M d') == 'Jun 21')
    {
      echo '<p>Ahh, the Summer Solstice.  I do hope Lady June is doing well...</p>' .
           '<p>Oh, sorry, ' . $user['display'] . '.  \'Got distracted... reminiscing.</p>' .
           '<p>Hm...</p>' .
           '<p>How can I help you today?</p>';
    }
    else
    {
?>
     <p>Come in, come in, and look around!  For these are things this side of the world rarely has the opportunity to see, potions brewed using techniques passed down from the Sons and Daughters of Ki Ri Kashu since the beginning of time.</p>
     <p>Heh-heh-heh.</p>
     <p>But please, really, do have a look around, and don't hesitate to ask for anything.</p>
<?php
    }

    $options[] = '<a href="?dialog=2">Ask about the Zodiac</a>';
  }

  if($has_tower && $_GET['dialog'] != '1' && $_GET['dialog'] != '5' && $_GET['dialog'] != '6')
    $options[] = '<a href="?dialog=1">Ask about the Philosopher\'s Stone</a>';

  if($has_shrine && $_GET['dialog'] != 8)
    $options[] = '<a href="?dialog=8">Ask about the Shrine</a>';
}

include 'commons/dialog_close.php';

if(count($options) > 0)
  echo '<ul><li>' . implode('</li><li>', $options) . '</li></ul>';
?>
     <form method="post">
     <table>
      <tr class="titlerow">
       <th></th>
       <th></th>
       <th>Item&nbsp;Wanted</th>
       <th>Items&nbsp;Needed</th>
      </tr>
<?php
$rowclass = begin_row_class();

foreach($this_alchemist as $smith_recipe)
{
  $ingredients = explode(',', $smith_recipe['supplies']);
  $itemcounts = array();
  foreach($ingredients as $item)
  {
    $datas = explode('|', $item);
    $itemcounts[$datas[1]] += $datas[0];
  }

  $ok = true;

  $itemdescripts = array();
  foreach($itemcounts as $item=>$count)
  {
    if($inventory[$item]['qty'] < $count)
    {
      if($inventory[$item]['qty'] == 0)
        $inventory[$item]['qty'] = '0';

      $itemdescripts[] = item_text_link($item, 'failure') . ($count > 1 ? ' <span class="failure">(' . $inventory[$item]['qty'] . ' / ' . $count . ')</span>' : '');
      $ok = false;
    }
    else
      $itemdescripts[] = item_text_link($item) . ' (' . $inventory[$item]['qty'] . ' / ' . $count . ')';
  }

  $supplies = implode('<br />', $itemdescripts);

  if($ok == false && $smith_recipe['secret'] == 'yes')
    continue;
?>
      <tr class="<?= $rowclass ?>">
<?php
  if($ok)
  {
?>
       <td><input type="radio" name="brewid" value="<?= $smith_recipe['idnum'] ?>" /></td>
<?php
  }
  else
  {
?>
       <td><input type="radio" name="brewid" value="0" disabled /></td>
<?php
  }

  $itemdetails = get_item_byname($smith_recipe['makes']);
?>
       <td align="center"><?= item_display($itemdetails) ?></td>
       <td><?= $smith_recipe['makes'] ?></td>
       <td>
        <?= $supplies ?>
       </td>
      </tr>
<?php
  $rowclass = alt_row_class($rowclass);
}
?>
     </table>
     <p>Quantity: <input type="number" min="1" name="quantity" style="width:60px;" value="1" maxlength="2" /> <input type="submit" value="Exchange Items" class="bigbutton" /></p>
     </form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
