<?php
$wiki = 'Totem_Pole_Garden';
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/itemlib.php';
require_once 'commons/formatting.php';
require_once 'commons/totemlib.php';
require_once 'commons/questlib.php';

if($user['show_totemgardern'] == 'no')
{
  header('Location: ./myhouse.php');
  exit();
}

$log = get_item_byname('Log');
$totem = get_item_byname('Wide-Mouthed Totem');
$mytotem = get_totem_byuserid($user['idnum']);
$totems = take_apart(',', $mytotem['totem']);
$height = count($totems);

$command = 'SELECT COUNT(idnum) AS c FROM monster_inventory WHERE itemname IN (\'Amethyst Rose Perfume\', \'Lotus Perfume\') AND user=' . quote_smart($user['user']);  
$data = $database->FetchSingle($command, 'fetching perfume count');

$num_perfumes = $data['c'];

$command = 'SELECT COUNT(idnum) AS c FROM monster_inventory WHERE itemname=\'Log\' AND user=' . quote_smart($user['user']) . ' AND location=\'storage\'';
$data = $database->FetchSingle($command, 'fetching log count');

$num_logs = $data['c'];

if($_POST['action'] == 'exchange' && $num_logs > 0)
{
  delete_inventory_byname($user['user'], 'Log', 1, 'storage');
  add_inventory($user['user'], '', 'Wide-Mouthed Totem', 'Traded for at The Totem Pole Garden', 'storage');

  $message = 'Thanks a lot!  If you need anymore, let me know.';
  $num_logs--;
}

$options = array('<a href="totempoles.php">Show me my Totem Pole</a>');

$quest_value_name = 'lunar eclipse 2010';
$quest_eclipse = get_quest_value($user['idnum'], $quest_value_name);

if(date('M d') == 'Dec 21')
{
  if($quest_eclipse === false)
  {
    $eclipse_dialog = true;
    add_inventory($user['user'], '', 'Total Lunar Eclipse Memento', '', 'storage/incoming');
    add_quest_value($user['idnum'], $quest_value_name, 1);
    flag_new_incoming_items($user['user']);
  }
}

$alchemist_quest = get_quest_value($user['idnum'], 'AlchemistQuest');
$hephaestus_charm_quest = get_quest_value($user['idnum'], 'hephaestus charm');

if($alchemist_quest['value'] >= 2 && $hephaestus_charm_quest === false)
{
  if($_GET['dialog'] == 'compliment')
  {
    $message = 'Oh, thanks!  My pet, Trillian, made all of it.  She\'s always had a talent for jeweling, but ever since I got that Hephaestus Charm from Lakisha, Trillian\'s been churning out all kinds of great stuff!';

    add_quest_value($user['idnum'], 'hephaestus charm', 1);
  }
  else
    $options[] = '<a href="?dialog=compliment">Compliment her jewelry</a>';
}

$quest_totem = get_quest_value($user['idnum'], 'totem quest');

if($quest_totem['value'] == 2)
{
  if($_GET['dialog'] == 3)
  {
    $totem_quest_dialog = true;
    update_quest_value($quest_totem['idnum'], 3);
  }
  else
    $options[] = '<a href="totemgarden.php?dialog=3">Tell her about the Silly Totem with Markings</a>';
}

if($_GET['dialog'] == 'totems')
{
  $message = 'For the most part, your pets will make the Totems.  Those PsyPets really are clever creatures!</p>' .
             '<p>But a few can be found in other ways.  I hear there\'s one in The Pattern, if you\'re familiar with that place.  And I know Nina can smith a Chess Totem, but she doesn\'t mention the option unless you already have all the supplies in your Storage.  You need... well, a Log, but also some Black Dye, White Paint, and a couple chess pieces, though I forget which ones.</p>' .
             '<p>Oh, and there\'s a couple Totems you can only get for trading in your totem pole.</p>' .
             '<p>If you want a list of all the Totems in existence, search <a href="encyclopedia.php">The Encyclopedia</a> that HERG offers.</p>' .
             '<p>Good luck!';
}
else
  $options[] = '<a href="totemgarden.php?dialog=totems">Ask about finding Totems</a>';

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; The Totem Pole Garden</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4>The Totem Pole Garden</h4>
     <ul class="tabbed">
      <li class="activetab"><a href="totemgarden.php">Information</a></li>
      <li><a href="totemgardenview.php">Browse Garden</a></li>
      <li><a href="mahjong.php">Mahjong Exchange</a></li>
<?= $st_patricks ? '<li class="stpatrick"><a href="stpatricks.php?where=totem">St. Patrick\'s Day Competition</a></li>' : '' ?>
     </ul>
<?php
// TOTEM POLE GARDEN NPC MATALIE
echo '<a href="/npcprofile.php?npc=Matalie Mansur"><img src="//' . $SETTINGS['static_domain'] . '/gfx/npcs/totemgirl.jpg" align="right" width="350" height="501" alt="(Totem Pole aficionado Matalie)" /></a>';

include 'commons/dialog_open.php';

if(strlen($message) == 0)
{
  if($totem_quest_dialog)
  {
    echo '<p>I thought so!  It\'s that Ancient Script - or whatever - that Eve and the others are always going on about over at the HERG lab, isn\'t it?</p>' .
         '<p>Hey, we should have, um, Julio take a look at it.  I think that was his name...  the archaeologist... always going on about Ancients from the Hollow Earth.  Anyway, I bet he can tell us what this writing says.</p>' .
         '<p>I hate to be a bother, but could you take the totem to him?  I have to keep an eye on things here.  He\'s probably in the "City Hall."  Ask at the Help Desk there - I\'m sure they can show you the way.</p>'; 
  }
  else if($eclipse_dialog)
  {
    echo '<p>Did you see the total lunar eclipse?  I know it wasn\'t visible from all parts of the world, but it was visible from right here, in my Totem Garden....  beautiful!</p>' .
         '<p>Take this this little memento to remember it by.  It could never compare to the real thing - ah - but that\'s why it\'s called a memento, right?</p>' .
         '<p><i>(You received the Total Lunar Eclipse Memento.   Find it in your Incoming.)</i></p>';
  }
  else if($_GET['dialog'] == '1')
  {
?>
     <p>The ranking system is kind of complicated, but I'll outline it for you...</p>
     <p>There are seven ratings, from 'poor' to 'amazing', and finally 'legendary', though I rarely see a pole worthy of legendary status!</p>
     <p>Height is a very important factor!  The taller the totem, the better the rating.  But also important - possibly more so - is the variety of the totems you use.</p>
     <p>It's impossible to completely avoid repeating a totem in a 100-totem totem pole, but the less you repeat your totems, the better your score will be.</p>
     <p>Cat Totems, for example, seem very easy to get, and while you could make a totem pole of 100 Cat Totems, such a totem pole would only be half as good as a totem pole made of 50 Cat Totems and 50 - I dunno - Silly Totems, or anything else.</p>
     <p>And that's another point worth making: the exact totems used don't matter!  A Cat Totem is worth as much as the rarer Jay Totem, so don't worry too much about getting a huge quantity of all the rarest totems.</p>
     <p>Well, I think that's all the advice I have.  Have fun building your totem!</p>
<?php
    if($mytotem['rating'] >= 55)
      $options[] = '<a href="totemgarden.php">Tell me about my Totem Pole offers again.</a>';
  }
  else
  {
    if($num_perfumes > 0)
    {
      echo '<p>Is that perfume?  I absolutely <em>love</em> perfume.  I have some Amethyst Rose Perfume on now, but of course nothing compares to Lotus Perfume.  God, that stuff is expensive, but I guess since it has both White <em>and</em> Black Lotus in it, that\'s to be expected.</p>' .
           '<p>Interesting to note: if you\'re looking to get your pets pregnant, give them some perfume... <i>*cough, cough*</i></p>' .
           '<p>But you\'re here to build a totem pole, right?  Not listen to me go on about perfume!</p>';
    }

    if($mytotem['rating'] < 30 || $_GET['dialog'] == 2)
    {
?>
     <p><?php if($mytotem['rating'] < 25 && $num_perfumes == 0) { ?>Oh, hi!  Welcome to the Totem Pole Garden!  <?php } ?>This is a public space where people are allowed to build their own totem poles.  Sounds fun, right?</p>
     <p>We do ask that you build no higher than 100 totems on your pole, however.  There was a case once where a totem pole was <em>so</em> high, a weather balloon crashed into it.  The scientists at HERG were very upset - apparently the balloon had some expensive equipment on it.</p>
     <p>Anyway, if you find any Totems, feel free to add them to your pole!  And if you're having trouble finding any, I can make you one out of a Log.  Just ask!</p>
<?php
      if($mytotem['rating'] >= 55)
        $options[] = '<a href="totemgarden.php">Tell me about my Totem Pole offers again.</a>';
    }
    else
    {
      $options[] = '<a href="totemgarden.php?dialog=2">Tell me about the Totem Pole Garden again.</a>';
?>
     <p><?= $user['display'] ?>!  I have some exciting news for you!  Word of your totem pole has been getting around, and I have some people here who would like to buy it!</p>
     <h5 style="margin-top:8px;">Ian</h5>
     <p>First, there's Ian.  You know, the guy who works at the Recycling Center?  He's prepared to give you <?= $height ?> <?= item_text_link('Log') ?>s for your totem pole.</p>
     <form action="totemtrade.php?offer=1" method="post"><p><input type="submit" value="Accept Offer" onclick="javascript:return confirm('Really accept this offer?');" class="bigbutton" /></p></form>
<?php

      if($mytotem['rating'] >= 55)
      {
?>
     <h5 style="margin-top:8px;">Matalie</h5>
     <p>That's right: I'd like to buy your totem pole!  I can offer you this rare totem: the <?= item_text_link('Cardinal Totem') ?>.  So, is it a deal?</p>
     <form action="totemtrade.php?offer=6" method="post"><p><input type="submit" value="Accept Offer" onclick="javascript:return confirm('Really accept this offer?');" class="bigbutton" /></p></form>
<?php
      }

      if($mytotem['rating'] >= 80 && !($mytotem['rating'] >= 400))
      {
?>
     <h5 style="margin-top:8px;">Lakisha</h5>
     <p>Lakisha, one of the Bank clerks, will buy your totem pole for <?= ceil(.65 * $log['value'] * $height) ?><span class="money">m</span>.</p>
     <form action="totemtrade.php?offer=3" method="post"><p><input type="submit" value="Accept Offer" onclick="javascript:return confirm('Really accept this offer?');" class="bigbutton" /></p></form>
<?php
      }

      if($mytotem['rating'] >= 120)
      {
?>
     <h5 style="margin-top:8px;">Mysterious Client</h5>
     <p>Someone slid a folded piece of paper with your name on it under the door.  It reads:</p>
     <p>"Your totem pole interests us.  We offer four <?= item_text_link('Maze Piece Summoning Scroll') ?>s.  If this deal is acceptable, sign this note, and leave it outside the Garden."</p>
     <p>Sounds suspicious to me, but hey: it's your totem pole.</p>
     <form action="totemtrade.php?offer=4" method="post"><p><input type="submit" value="Accept Offer" onclick="javascript:return confirm('Really accept this offer?');" class="bigbutton" /></p></form>
<?php
      }

      if($mytotem['rating'] >= 150)
      {
?>
     <h5 style="margin-top:8px;">Nina</h5>
     <p>The Smith, Nina, will part with one of her <?= item_text_link('Hephaestus\' Hammer') ?>s for your totem pole.</p>
     <form action="totemtrade.php?offer=2" method="post"><p><input type="submit" value="Accept Offer" onclick="javascript:return confirm('Really accept this offer?');" class="bigbutton" /></p></form>
<?php
      }

      if($mytotem['rating'] >= 200)
      {
?>
     <h5 style="margin-top:8px;">Marian</h5>
     <p>Marian, the librarian, is offering a copy of <?= item_text_link('The Importance of Being Earnest: Act III') ?> in exchange for your totem pole.</p>
     <form action="totemtrade.php?offer=12" method="post"><p><input type="submit" value="Accept Offer" onclick="javascript:return confirm('Really accept this offer?');" class="bigbutton" /></p></form>
<?php
      }

      if($mytotem['rating'] >= 250)
      {
?>
     <h5 style="margin-top:8px;">Mysterious Client</h5>
     <p>Whoever slid that folded piece of paper with your name on it also left me a voice mail on my cellphone... how did they get my cellphone number!?</p>
     <p>Anyway, they say that if you want a <?= item_text_link('White Dragon') ?> Mahjong tile, to write on a piece of paper that you accept the trade, and sign it, and leave it outside the Garden.</p>
     <p>I mean, I like Mahjong, but whoever this is, they're kind of freaking me out.</p>
     <form action="totemtrade.php?offer=13" method="post"><p><input type="submit" value="Accept Offer" onclick="javascript:return confirm('Really accept this offer?');" class="bigbutton" /></p></form>
<?php
      }

      if($mytotem['rating'] >= 300)
      {
?>
     <h5 style="margin-top:8px;">Thaddeus</h5>
     <p>Thaddeus, has made another offer for your totem pole: a sack full of alchemical materials, the exact contents of which were not given to me.</p>
     <form action="totemtrade.php?offer=7" method="post"><p><input type="submit" value="Accept Offer" onclick="javascript:return confirm('Really accept this offer?');" class="bigbutton" /></p></form>
<?php
      }

      // 13 unique totems means that, at best, you can have a score of 65 at height 13
      // after this, you will not be able to keep up with the maximum (unless you previously
      // sacrificed the maximum in order to have a unique top later)

      if($mytotem['rating'] >= 400)
      {
?>
     <h5 style="margin-top:8px;">Lakisha</h5>
     <p>Lakisha, one of the Bank clerks, will buy your totem pole for <?= ceil(.85 * $log['value'] * $height) ?><span class="money">m</span>.</p>
     <form action="totemtrade.php?offer=10" method="post"><p><input type="submit" value="Accept Offer" onclick="javascript:return confirm('Really accept this offer?');" class="bigbutton" /></p></form>
<?php
      }

      if($mytotem['rating'] >= 450)
      {
?>
     <h5 style="margin-top:8px;">Mysterious Client</h5>
     <p>Whoever slid that folded piece of paper with your name on it, and also left me a voice mail on my cellphone... they also posted on my on-line journal...</p>
     <p>This is going to drive me crazy, but anyway, this time they're offering a <?= item_text_link('Red Dragon') ?> Mahjong tile, and again, if you want it... well, you know the drill.</p>
     <form action="totemtrade.php?offer=14" method="post"><p><input type="submit" value="Accept Offer" onclick="javascript:return confirm('Really accept this offer?');" class="bigbutton" /></p></form>
<?php
      }

      if($mytotem['rating'] >= 550)
      {
?>
     <h5 style="margin-top:8px;">Lance</h5>
     <p>Lance, one of the Temple monks, would like your totem pole.  In exchange, he offers a vial of extremely rare <?= item_text_link('Holy Water') ?>, and a burnt-up copy of the book <?= item_text_link('The Passage of Time') ?>.</p>
     <form action="totemtrade.php?offer=5" method="post"><p><input type="submit" value="Accept Offer" onclick="javascript:return confirm('Really accept this offer?');" class="bigbutton" /></p></form>
<?php
      }
      
      if($mytotem['rating'] >= 650)
      {
?>
     <h5 style="margin-top:8px;">Eve</h5>
     <p>One of the HERG scientists, Eve, has an interesting offer: a <?= item_text_link('Domesticated Robot Monkey') ?>.  You may have seen your pet defeating these in the wild.  Eve promises that when trained, however, they're very useful around the house.</p>
     <form action="totemtrade.php?offer=8" method="post"><p><input type="submit" value="Accept Offer" onclick="javascript:return confirm('Really accept this offer?');" class="bigbutton" /></p></form>
<?php
      }

      if($mytotem['rating'] >= 700)
      {
?>
     <h5 style="margin-top:8px;">Thaddeus</h5>
     <p>Thaddeus says he'll give you two <?= item_text_link('Magic Voucher', false, 'Magic Vouchers') ?> for your Totem Pole.</p>
     <p>Hey, wait: doesn't he run that <a href="/af_trinkets.php">Rare Trinkets</a> shop thing?  That's kind of sneaky of him!</p>
     <form action="totemtrade.php?offer=15" method="post"><p><input type="submit" value="Accept Offer" onclick="javascript:return confirm('Really accept this offer?');" class="bigbutton" /></p></form>
<?php
      }

      if($mytotem['rating'] >= 700)
      {
?>
     <h5 style="margin-top:8px;">Matalie</h5>
     <p>I have another offer for you.  I happen to own a very rare totem: the <?= item_text_link('Golden Phoenix Totem') ?>.  Actually putting it on a totem pole would be a waste of its beauty, however; it'd be much better suited on your mantle.  Anyway, I'll give you one of these totems in exchange for your entire totem pole.  Is it a deal?</p>
     <form action="totemtrade.php?offer=11" method="post"><p><input type="submit" value="Accept Offer" onclick="javascript:return confirm('Really accept this offer?');" class="bigbutton" /></p></form>
<?php
      }
    }
  }
}
else
  echo '<p>' . $message . '</p>';

include 'commons/dialog_close.php';

echo '<ul><li>' . implode('</li><li>', $options) . '</li></ul>';

if($num_logs > 0)
{
?>
     <form action="totemgarden.php" method="post">
     <table>
      <tr>
       <td><?= item_display($log, '') ?></td>
       <td>Log</td>
       <td><img src="gfx/lookright.gif" width="16" height="16" alt="exchanges for" /></td>
       <td><?= item_display($totem, '') ?></td>
       <td>Wide-Mouthed Totem</td>
       <td><input type="hidden" name="action" value="exchange" /><input type="submit" value="Make Trade" /></td>
      </tr>
     </table>
     </form>
<?php
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
