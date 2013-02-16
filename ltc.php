<?php
$wiki = 'Bank';
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/formatting.php';
require_once 'commons/grammar.php';
require_once 'commons/inventory.php';
require_once 'commons/economylib.php';

$ltc_cost = value_with_inflation(500);

if($_POST['submit'] == 'Purchase License')
{
  if($user['license'] == 'no')
  {
    if($user['money'] >= $ltc_cost)
    {
      $user['license'] = 'yes';
      $user['money'] -= $ltc_cost;

      take_money($user, $ltc_cost, 'License to Commerce');
      
      $command = 'UPDATE psypets_badges SET ltc=\'yes\' WHERE userid=' . $user['idnum'] . ' LIMIT 1';
      $database->FetchNone($command, 'granting LtC badge');

      $command = 'UPDATE monster_users SET license=\'yes\' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
      $database->FetchNone($command, 'adding LtC');

      $success_message = true;
    }
    else
      $error_message = 'The License to Commerce costs ' . $ltc_cost . '<span class="money">m</span>, but you only have ' . $user['money'] . '<span class="money">m</span>.  Please come back once you\'ve saved up a little.';
  }
  else
    $error_message = 'You already have a License to Commerce.';
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; The Bank &gt; License to Commerce</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4>The Bank &gt; License to Commerce</h4>
     <ul class="tabbed">
      <li><a href="bank.php">The Bank</a></li>
      <li><a href="bank_groupcurrencies.php">Group Currencies</a></li>
      <li><a href="bank_exchange.php">Exchanges</a></li>
      <li class="activetab"><a href="ltc.php">License to Commerce</a></li>
      <li><a href="allowance.php">Allowance Preference</a></li>
<?= $st_patricks ? '<li class="stpatrick"><a href="stpatricks.php?where=bank">St. Patrick\'s Day Competition</a></li>' : '' ?>
     </ul>
<?php
// BANKER LAKISHA
echo '<a href="npcprofile.php?npc=The Banker"><img src="//' . $SETTINGS['static_domain'] . '/gfx/npcs/thebanker.png" align="right" width="350" height="" alt="(The Banker)" /></a>';

if($user['license'] != 'yes')
{
  include 'commons/dialog_open.php';

  if($_GET['dialog'] == 2)
    echo '     <p><strong>Sorry, you need a License to Commerce to do that.</strong></p>';
?>
     <p>The License to Commerce allows you to send packages to other users, set up shop, trade with other users, and host events at the Park.</p>
     <p><i>(Purchasing the License to Commerce will unlock several new areas of the game.)</i></p>
     <p>It costs <?= $ltc_cost ?><span class="money">m</span> to purchase and is non-refundable.  I know it may seem a little pricy at first, but the License to Commerce quickly pays for itself!</p>
<?php
  include 'commons/dialog_close.php';

  if($user['money'] >= $ltc_cost)
  {
?>
     <form action="ltc.php" method="post">
     <p><input type="submit" name="submit" value="Purchase License" class="bigbutton" /></p>
     </form>
<?php
  }
}
else
{
  include 'commons/dialog_open.php';

  if($success_message === true)
    echo 'Thank you, ' . $user['display'] . ', and good luck with your business.' .
         '<p><i>(The following locations have been revealed to you: My Store, Trading House, Auction House, Flea Market, Advertising, Pawn Shop, and you may now host events at The Park.)</i></p>' .
         '<p><i>(Also, you won the License to Commerce Badge!)</i></p>';
  else
    echo '     <p>You already have the License to Commerce.</p>' .
         '     <p>If you\'re looking for something to do with it, I recommend selling items in the <a href="/fleamarket/">Flea Market</a>, or hosting events in <a href="/park.php">The Park</a>.  Personally, I love the <a href="/auctionhouse.php">Auction House</a>.  With the License, you\'re also free to send packages to other players via the <a href="/post.php">Post Office</a>.</p>';

  include 'commons/dialog_close.php';
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
