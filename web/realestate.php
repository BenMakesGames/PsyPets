<?php
$wiki = 'Real_Estate';
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/houselib.php';
require_once 'commons/userlib.php';
require_once 'commons/economylib.php';

$locid = $user['locid'];
$house = get_house_byuser($user['idnum'], $locid);

$addons = take_apart(',', $house['addons']);
$have_basement = (array_search('Basement', $addons) !== false);

$max_amount = min(1000, 5000 - $house['maxbulk']);

$special_price = value_with_inflation(400);
$regular_price = value_with_inflation($max_amount * 2);

if($_POST['action'] == 'buy' && $house['maxbulk'] < 5000)
{
  if($user['money'] >= $regular_price)
  {
    home_improvement($user['locid'], $user['idnum'], $max_amount);
    take_money($user, $regular_price, 'Real Estate');

    $house['maxbulk'] += $max_amount;
    $user['money'] -= $regular_price;
  }
}
else if($_POST['action'] == 'buyspecial' && $house['maxbulk'] < 2000)
{
  if($user['money'] >= $special_price)
  {
    home_improvement($user['locid'], $user['idnum'], 250);
    take_money($user, $special_price, 'Real Estate');

    $house['maxbulk'] += 250;
    $user['money'] -= $special_price;
  }
}

$badges = get_badges_byuserid($user['idnum']);

if($badges['mansion'] == 'no' && $house['maxbulk'] >= 5000)
{
  $badge_dialog .= '<p>Oh, a size-' . ($house['maxbulk'] / 10) . ' house?  More like a mansion, at that size!</p>' .
                  '<p><i>(You have been awarded the Mansion Badge.)</i></p>';
  set_badge($user['idnum'], 'mansion');
}

if($badges['castle'] == 'no' && $house['maxbulk'] >= 20000)
{
  $badge_dialog .= '<p>Oh, a size-' . ($house['maxbulk'] / 10) . ' house?  It\'s a veritable castle!</p>' .
                  '<p><i>(You have been awarded the Castle Badge.)</i></p>';
  set_badge($user['idnum'], 'castle');
}

if($badges['island'] == 'no' && $house['maxbulk'] >= 100000)
{
  $badge_dialog .= '<p>Oh, a size-' . ($house['maxbulk'] / 10) . ' house?  You must own your own island!</p>' .
                  '<p><i>(You have been awarded the Island Badge.)</i></p>';
  set_badge($user['idnum'], 'island');
}

if($badges['islandplus'] == 'no' && $house['maxbulk'] >= 200000)
{
  $badge_dialog .= '<p>Oh, a size-' . ($house['maxbulk'] / 10) . ' house?  You must own your own island!  A... really big island!</p>' .
                  '<p><i>(You have been awarded the Big Island Badge.)</i></p>';
  set_badge($user['idnum'], 'islandplus');
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Real Estate</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h5>Real Estate</h5>
     <ul class="tabbed">
      <li class="activetab"><a href="realestate.php">Buy Land</a></li>
      <li><a href="realestate_lake.php">Build Lake</a></li>
      <li><a href="realestate_deeds.php">Acquire Deeds</a></li>
     </ul>
<?php
// NPC AMANDA BRANAMAN
echo '<a href="npcprofile.php?npc=Amanda+Branaman"><img src="gfx/npcs/real-estate-agent.png" align="right" width="350" height="490" alt="(Amanda, the Real Estate agent)" /></a>';

include 'commons/dialog_open.php';

if($error_message)
  echo '     <p class="failure">' . $error_message . "</p>\n";

if($badge_dialog)
  echo $badge_dialog;

if($have_basement)
  echo '     <p>You currently have a size ' . ($house['maxbulk'] / 10) . ' estate, and a ' . ($house['maxbasement'] / 100) . '-level basement.</p>';
else
  echo '     <p>You currently have a size ' . ($house['maxbulk'] / 10) . ' estate.</p>';

if($house['maxbulk'] > max_house_size())
  echo "     <p>City ordinances restrict estate sizes to " . (max_house_size() / 10) . ".  You are " . (($house['maxbulk'] - max_house_size()) / 10) . " over this limit.  We can't let you use this space, however you are able to sell it to other players.  If you're interested, I can give you a deed to some of your space for you to sell or give away as you see fit.</p>";
else if($house['maxbulk'] > 4000)
  echo '
    <p>City ordinances prevent us from selling land above 500 units to Residents.</p>
    <p>If you\'d like to continue expanding your house, however, you\'re free to do so on your own!  With <a href="encyclopedia.php?submit=Search&itemtype=construction/wall&standard=on">walls</a>, and other materials, you can expand your house up to a maximum size of ' . (max_house_size() / 10) . '!</p>
  ';
else if($house['maxbulk'] < 2000)
  echo '<p>We have a special offer for Residents with under 200 units of space: you may buy 25 units for only ' . $special_price . '<span class="money">m</span>.</p>';

include 'commons/dialog_close.php';

if($house['maxbulk'] < 5000)
{
?>
     <table>
      <tr class="titlerow">
       <th>Estate Add-on</th>
       <th align="right">Price</th>
       <th></th>
      </tr>
<?php
  $rowclass = begin_row_class();

  if($house['maxbulk'] < 2000)
  {
    $disabled = ($user['money'] < $special_price ? ' disabled="disabled"' : '');
?>
      <tr class="<?= $rowclass ?>">
       <td>25</td>
       <td align="right"><?= $special_price ?><span class="money">m</span></td>
       <td><form action="realestate.php" method="post"><input type="hidden" name="action" value="buyspecial" /><input type="submit" value="Buy"<?= $disabled ?> /></form></td>
      </tr>
<?php
    $rowclass = alt_row_class($rowclass);
  }

  $disabled = ($user['money'] < $regular_price ? ' disabled="disabled"' : '');
?>
      <tr class="<?= $rowclass ?>">
       <td><?= ($max_amount / 10) ?></td>
       <td align="right"><?= $regular_price ?><span class="money">m</span></td>
       <td><form action="realestate.php" method="post"><input type="hidden" name="action" value="buy" /><input type="submit" value="Buy"<?= $disabled ?> /></form></td>
      </tr>
     </table>
<?php
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
