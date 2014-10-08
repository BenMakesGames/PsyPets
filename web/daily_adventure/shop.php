<?php
require_once 'commons/init.php';

$require_petload = 'no';
$wiki = 'Daily_Adventure';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/formatting.php';
require_once 'commons/challengelib.php';
require_once 'commons/questlib.php';

$challenge = get_challenge($user['idnum']);
if($challenge === false)
{
  header('Location: ./challenge.php');
  exit();
}

// tokens exchange 3 for 1 (up), or 1 for 2 (down)
if($_GET['exchange'] == 1 && $challenge['copper'] >= 3)
{
  $challenge['copper'] -= 3;
  $challenge['silver']++;
  
  update_challenge($challenge);
  
  $dialog_exchanged = true;
}
else if($_GET['exchange'] == 2 && $challenge['silver'] >= 3)
{
  $challenge['silver'] -= 3;
  $challenge['gold']++;

  update_challenge($challenge);

  $dialog_exchanged = true;
}
else if($_GET['exchange'] == 3 && $challenge['gold'] >= 3)
{
  $challenge['gold'] -= 3;
  $challenge['platinum']++;

  update_challenge($challenge);

  $dialog_exchanged = true;
}
else if($_GET['exchange'] == 4 && $challenge['plastic'] >= 3)
{
  $challenge['plastic'] -= 3;
  $challenge['copper']++;
  
  update_challenge($challenge);

  $dialog_exchanged = true;
}
else if($_GET['exchange'] == 5 && $challenge['silver'] > 0)
{
  $challenge['silver']--;
  $challenge['copper'] += 2;
  
  update_challenge($challenge);
  
  $dialog_exchanged = true;
}
else if($_GET['exchange'] == 6 && $challenge['gold'] > 0)
{
  $challenge['gold']--;
  $challenge['silver'] += 2;

  update_challenge($challenge);

  $dialog_exchanged = true;
}
else if($_GET['exchange'] == 7 && $challenge['platinum'] > 0)
{
  $challenge['platinum']--;
  $challenge['gold'] += 2;

  update_challenge($challenge);

  $dialog_exchanged = true;
}

$tokens = array('plastic', 'copper', 'silver', 'gold', 'platinum');

$store = array(
  1 => array('Wheat Bread', 1, 'copper'),
 14 => array('Tin', 1, 'copper'),
 13 => array('Ghost in a Blanket Costume', 2, 'copper'),
 16 => array('Rubble', 2, 'copper'),
  2 => array('6-Sided Die', 3, 'copper'),
  3 => array('Apiary Blueprint', 5, 'copper'),
  4 => array('Wax', 1, 'silver'),
 18 => array('Aging Root', 1, 'silver'),
 12 => array('Vine Staff Blueprint', 1, 'silver'),
 15 => array('Key to Kundrav\'s Lair', 2, 'silver'),
  5 => array('Bicycle Blueprint', 3, 'silver'),
 11 => array('Magic Carpet', 6, 'silver'),
  6 => array('Apiary Blueprint', 1, 'gold'),
 17 => array('Gossamer', 1, 'gold'),
  7 => array('Rock Smasher Blueprint', 2, 'gold'),
  9 => array('Maze Piece Summoning Scroll', 3, 'gold'),
 10 => array('Map Room Blueprint', 1, 'platinum'),
  8 => array('Hungry Tapestry (level 0)', 3, 'platinum'),
);

$magic_healing_quest = get_quest_value($user['idnum'], 'magic healing');

if($magic_healing_quest === false || $magic_healing_quest['value'] == 0)
{
  if($_GET['dialog'] == 'skills')
  {
    $dialog = '
      <p>Oh, sure, lots of things!  I had a friend who threw around fireballs, and he taught me some stuff!</p>
      <p>Though to be honest, I find it difficult to control fire well enough to even do as much as fry an egg.</p>
      <p>Although one thing I <em>did</em> get good at is this... revitalizing charm.  Say the right words, make the right gestures, and *poof* all your problems are melting away... it\'s tangible.</p>
      <p>It\'s also really tiring, so I don\'t do it often.</p>
      <p>Hm...</p>
      <p>Still, you know what, it\'s been a while.  So how about it: I\'ll cast this charm for you and your pets.  Just this once.</p>
    ';
    
    $options[] = '<a href="?dialog=revitalizeme">Excitedly agree!</a>';
    $options[] = '<a href="?dialog=dontrevitalizeme">Recommend, instead, another time.</a>';
  }
  else if($_GET['dialog'] == 'revitalizemecheck')
  {
    $dialog = '
      <p>Haha!  Alright, cool!  I haven\'t done this in a while.</p>
      <p>It really takes a lot out of me, so just this once, alright?</p>
    ';

    $options[] = '<a href="?dialog=revitalizeme">Agreed!</a>';
    $options[] = '<a href="?dialog=dontrevitalizeme">Wait!  Tell him another time would be better.</a>';
  }
  else if($_GET['dialog'] == 'revitalizeme')
  {
    load_user_pets($user, $userpets);

    foreach($userpets as $target_pet)
    {
      $safety = max_safety($target_pet);
      $love   = max_love($target_pet);
      $esteem = max_esteem($target_pet);

      $old_food = $target_pet['food'];
      $old_energy = $target_pet['energy'];
      $target_pet['food'] = 1;
      $target_pet['energy'] = 1;

      gain_safety($target_pet, $safety);
      gain_love($target_pet, $love);
      gain_esteem($target_pet, $esteem);

      $target_pet['food'] = $old_food;
      $target_pet['energy'] = $old_energy;
      
      save_pet($target_pet, array('food', 'energy', 'safety', 'love', 'esteem'));
    }
      
    $dialog = '
      <p>[Mumbles some unfamiliar language while moving his hands according to some equally-unfamiliar choreography.]</p>
      <p><i>(Nothing seems to happen.)</i></p>
      <p>Done!</p>
      <p>Eh?  Eh?  So how do you feel?  Worried?  Nervous?  It\'s all gone, right?!</p>
    ';
    
    $options[] = '<a href="?dialog=worried">Explain that you\'re worried nothing happened.</a>';
  
    if($magic_healing_quest === false)
      add_quest_value($user['idnum'], 'magic healing', 1);
    else
      update_quest_value($magic_healing_quest['idnum'], 1);
  }
  else if($_GET['dialog'] == 'dontrevitalizeme')
  {
    $message .= '<p>Any time!  Just let me know!</p>';

    if($magic_healing_quest === false)
      add_quest_value($user['idnum'], 'magic healing', 0);
  }
  else
  {
    if($magic_healing_quest === false)
    {
      $options[] = '<a href="?dialog=skills">Ask what he\'s learned during his years of adventuring.</a>';
    }
    else if($magic_healing_quest['value'] == 0)
    {
      $options[] = '<a href="?dialog=revitalizemecheck">Ask him to perform that "Revitalizing Charm".</a>';
    }
  }
}

if($_POST['submit'] == 'Buy')
{
  $buy = (int)$_POST['buy'];

  if(array_key_exists($buy, $store))
  {
    if($challenge[$store[$buy][2]] >= $store[$buy][1])
    {
      $challenge[$store[$buy][2]] -= $store[$buy][1];
      update_challenge($challenge);
      
      add_inventory($user['user'], '', $store[$buy][0], 'Purchased from the Adventurer\'s Store', $user['incomingto']);

      $message = '<p class="success">The ' . $store[$buy][0] . ' has been put into your ' . ucfirst($user['incomingto']) . '.</p>';

      require_once 'commons/statlib.php';
      record_stat($user['idnum'], 'Bought Something at the Adventurer\'s Shop', 1);

      require_once 'commons/questlib.php';

      $adventurer = get_quest_value($user['idnum'], 'adventure purchases');
      $purchase_count = (int)$adventurer['value'] + 1;

      if($adventurer === false)
        add_quest_value($user['idnum'], 'adventure purchases', $purchase_count);
      else
        update_quest_value($adventurer['idnum'], $purchase_count);

      $badges = get_badges_byuserid($user['idnum']);
      if($purchase_count >= 1)
      {
        if($badges['koboldkiller'] == 'no')
        {
          set_badge($user['idnum'], 'koboldkiller');
          
          $message .= '<p class="success"><i>(You received the Kobold Killer Badge!)</i></p>';
        }
        
        if($purchase_count >= 10)
        {
          if($badges['sirenslayer'] == 'no')
          {
            set_badge($user['idnum'], 'sirenslayer');

            $message .= '<p class="success"><i>(You received the Siren Slayer Badge!)</i></p>';
          }

          if($purchase_count >= 100)
          {
            if($badges['manticoremenace'] == 'no')
            {
              set_badge($user['idnum'], 'manticoremenace');

              $message .= '<p class="success"><i>(You received the Manticore Menace Badge!)</i></p>';
            }
          }
        }
      }
    }
    else
      $message = '<p class="failure">You do not have enough ' . ucfirst($store[$buy][2]) . ' Tokens to buy ' . $store[$buy][0] . '.</p>';
  }
  else
    $message = '<p class="failure">Buy what?</p>';
}

$command = "SELECT COUNT(*) AS c FROM monster_inventory WHERE user=" . quote_smart($user["user"]) . " AND location='storage' AND itemname='Medallion of Fate'";
$data = fetch_single($command, 'fetching medallions from storage');
$medallion_count = (int)$data['c'];

if($_GET['dialog'] == 2 && $medallion_count > 0)
{
  if(delete_inventory_byname($user['user'], 'Medallion of Fate', 1, 'storage') == 1)
  {
    add_inventory($user['user'], $maker, 'Egg of Destiny', '', $user['incomingto']);
    $thanks_dialog = true;
    $medallion_count--;
  }
}

if($_GET['dialog'] == 'worried')
{
  $dialog = '
    <p>Ha!  ' . $user['display'] . '!  That\'s why I like you: such a joker!</p>
    <p>Well, have fun out there; I\'ll see you later.</p>
  ';
}
else if($_GET['dialog'] == 'adventure_tips')
{
  $dialog = '
    <p>Tips?  Hm...</p>
    <p>Well, the main focus for anyone fighting monsters should of course be to improve your skills in combat!</p>
    <p>You have to be strong!  Tough!  If you\'re not, the monsters will get the best of you.</p>
    <p>There are some monsters out there, though, that aren\'t just physically strong... they\'re tricky!</p>
    <p>Sometimes you have to be sneaky... or clever... or you\'ll never find them.  They\'ll avoid you.  You\'ll never know they exist.</p>
    <p>And yet others live in remote, hard-to-find areas; harsh environments.  If you don\'t know your way through the woods, or how to survive the bitter cold, you can forget about them.</p>
    <p><strong>But</strong>, by and large, sheer, physical strength is the best way to go.  Overwhelm your opponent with your might!  Haha!</p>
  ';

  $options[] = '<a href="?dialog=adventure_rewards">Ask about the rewards of Adventuring</a>';
}
else if($_GET['dialog'] == 'adventure_rewards')
{
  $dialog = '
    <p>Ha!  You\'ve got the mind of a true adventurer.  It\'s not just about the excitement, is it?  Oh no!  There\'s the treasure, too!</p>
    <p>Sure, there are bounties on the heads of most monsters that you - well, or your pets - can claim for cold, hard moneys... but there\'s gold, jewelry.  And stranger things.</p>
    <p>And then there\'s Kundrav...</p>
  ';

  $options[] = '<a href="?dialog=kundrav">Ask what a Kundrav is</a>';
}
else if($_GET['dialog'] == 'kundrav')
{
  $dialog = '
    <p>Huh?</p>
    <p>Oh, no-no!  Kundrav\'s not a treasure!  He\'s a monster.  Perhaps the fiercest that ever was.</p>
    <p>But he does <em>possess</em> a treasure.  For the fiercest monster, the greatest treasure - the greatest in all in the world, they say... the power over life and death!  Reincarnation!</p>
    <p>I don\'t know much more than that.  Well, not about the treasure, anyway.   I do know about a man who fought with Kundrav, and even defeated him: Keresaspa!</p>
  ';

  $options[] = '<a href="?dialog=keresaspa">Ask about Keresaspa</a>';
}
else if($_GET['dialog'] == 'keresaspa')
{
  $dialog = '
    <p>Keresaspa was a great warrior, said to have defeated Kundrav!  Though it would seem Kundrav\'s defeat was temporary...</p>
    <p>Anyway, whether or not Keresaspa gained the power of reincarnation, who knows.  Unfortunately, no one can ask him about it: he died hundreds - thousands of years ago.  If he is among us today, he\'s either hiding it, or forgotten who he was...</p>
  ';

  $options[] = '<a href="?dialog=adventure_tips">Ask for tips for becoming a good Adventurer</a>';
}
else if($_GET['dialog'] == 'adventure')
{
  $dialog = '
    <p>Hm?</p>
    <p>Oh!  Like when pets go out to adventure on their own?  Beat up monsters?  Yes, I guess I do know a bit about that as well.</p>
    <p>Well what do you want to know?</p>
  ';

  $options[] = '<a href="?dialog=adventure_tips">Ask for tips for becoming a good Adventurer</a>';
  $options[] = '<a href="?dialog=adventure_rewards">Ask about the rewards of Adventuring</a>';
}
else
  $options[] = '<a href="?dialog=adventure">Ask about Adventuring pets</a>';

// remove the Bat Costume if the date is not Oct 15-31
if($now_month != 10 || $now_day <= 15)
  unset($store[13]);
else if($_GET['dialog'] != 'batcostume')
  $options[] = '<a href="?dialog=costume">Ask about the Ghost in a Blanket Costume</a>';

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Daily Adventure</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4>Daily Adventure</h4>
     <ul class="tabbed">
      <li><a href="/daily_adventure/">Go On an Adventure</a></li>
      <li><a href="/daily_adventure/rankings.php">Most Adventurous Residents</a></li>
      <li class="activetab"><a href="/daily_adventure/shop.php">Adventurer's Shop</a></li>
     </ul>
<?php
echo '<a href="/npcprofile.php?npc=Jerrad+Shiflett"><img src="//' . $SETTINGS['static_domain'] . '/gfx/npcs/adventurer.png" align="right" width="350" height="410" alt="(Jerrad the Adventurer)" /></a>';

include 'commons/dialog_open.php';

if($dialog != '')
  echo $dialog;
else if($thanks_dialog)
{
  echo '<p>Here\'s your Egg of Destiny, as promised, right into your ' . ucfirst($user['incomingto']) . '.  Have fun!</p>';
}
else if($dialog_exchanged)
{
  echo '<p>Done.  If you need anything else, give me a shout.</p>';
}
else if($_GET['dialog'] == 'costume')
{
  echo '<p>Oh, that?  I\'m working with Valerie (you know, <a href="/tailor.php">The Tailory</a> girl) to help get costumes out to new Residents so that their pets can participate in the Trick-or-Treating event.</p>' .
       '<p>Even if you\'ve only recently started playing, you should be able to do six Plastic-level adventures pretty easily.  Once you have the six Plastic Tokens, exchange them in for two Coppers, and you\'re set!</p>';

  $options[] = '<a href="?dialog=3">Ask about token exchange rates</a>';
}
else if($_GET['dialog'] == 3)
{
  echo '
    <p>I\'d happily exchange 3 tokens for a single better token - like, 3 Plastic Tokens for one Copper, for example - or one token for two lesser tokens - say... 1 Gold for 2 Silver.</p>
    <p>Well, if you have any tokens you want to exchange, just ask!</p>
  ';
}
else
{
?>
<p>You can spend the Tokens you earn from completing adventures here.  I can also exchange Tokens of one type for another.</p>
<?php
  if($medallion_count > 0)
  {
?>
<p>Oh, oh, what's this?  A Medallion of Fate!  It is my obligation-- no, my <em>destiny</em> to exchange those medallions for Eggs of Destiny.  If you'd like to make such an exchange, let me know.</p>
<?php
  }
  
  $options[] = '<a href="?dialog=3">Ask about token exchange rates</a>';
}

if($medallion_count > 0)
  $options[] = '<a href="?dialog=2">Trade a Medallion of Fate for an Egg of Destiny</a> (you have ' . $medallion_count . ' Medallion' . ($medallion_count == 1 ? '' : 's') . ' of Fate in storage)';

if($challenge['plastic'] >= 3)
  $options[] = '<a href="?exchange=4">Trade 3 Plastic Tokens for 1 Copper Token</a>';

if($challenge['copper'] >= 3)
  $options[] = '<a href="?exchange=1">Trade 3 Copper Tokens for 1 Silver Token</a>';

if($challenge['silver'] > 0)
  $options[] = '<a href="?exchange=5">Trade 1 Silver Token for 2 Copper Tokens</a>';
if($challenge['silver'] >= 3)
  $options[] = '<a href="?exchange=2">Trade 3 Silver Tokens for 1 Gold Token</a>';

if($challenge['gold'] > 0)
  $options[] = '<a href="?exchange=6">Trade 1 Gold Token for 2 Silver Tokens</a>';
if($challenge['gold'] >= 3)
  $options[] = '<a href="?exchange=3">Trade 3 Gold Tokens for 1 Platinum Token</a>';

if($challenge['platinum'] > 0)
  $options[] = '<a href="?exchange=7">Trade 1 Platinum Token for 2 Gold Tokens</a>';

include 'commons/dialog_close.php';

if(count($options) > 0)
  echo '<ul><li>' . implode('</li><li>', $options) . '</li></ul>';
?>
     <h5 style="margin-bottom:0;">Tokens</h5>
     <table>
<?php
foreach($tokens as $token)
{
  if($token == 'plastic')
    echo '      <td>';
  else
    echo '      <td style="padding-left:15px;">';
    
  echo '<img src="/gfx/items/token_' . $token . '.png"></td><td>' . ucfirst($token) . '</td><td>x ' . $challenge[$token] . '</td>' . "\n";
}
?>
      </tr>
     </table>
     <h5>Shop</h5>
<?php
if($message)
  echo '<ul><li>' . $message . '</li></ul>';
?>
     <form method="post">
     <table>
      <tr class="titlerow">
       <th></th><th></th><th>Item</th><th>Price</th>
      </tr>
<?php
$rowclass = begin_row_class();

foreach($store as $id=>$details)
{
  $item_details = get_item_byname($details[0]);

  if($challenge[$details[2]] >= $details[1])
  {
?>
      <tr class="<?= $rowclass ?>">
       <td><input type="radio" name="buy" value="<?= $id ?>" /></td>
       <td class="centered"><?= item_display($item_details, '') ?></td>
       <td><?= $details[0] ?></td>
       <td><?= $details[1] . ' ' . ucfirst($details[2]) . ' Token' . ($details[1] != 1 ? 's' : '') ?></td>
      </tr>
<?php
  }
  else
  {
?>
      <tr class="<?= $rowclass ?>">
       <td><input type="radio" name="buy" value="0" disabled /></td>
       <td class="centered"><?= item_display($item_details, '') ?></td>
       <td><?= $details[0] ?></td>
       <td class="failure"><?= $details[1] . ' ' . ucfirst($details[2]) . ' Token' . ($details[1] != 1 ? 's' : '') ?></td>
      </tr>
<?php
  }

  $rowclass = alt_row_class($rowclass);
}
?>
     </table>
     <p><input type="submit" name="submit" value="Buy" /></p>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
