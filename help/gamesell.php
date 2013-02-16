<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$require_login = "no";

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/formatting.php';

include 'commons/html.php';
?>
 <head>
  <title>City Hall &gt; Help Desk &gt; Gamesell</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
  <h4><a href="/cityhall.php">City Hall</a> &gt; <a href="/help/">Help Desk</a> &gt; Gamesell</h4>
  <p>You can "gamesell" items for a little money.  While items can be sold to other players (if you have a <a href="/ltc.php">License to Commerce</a>), it is sometimes quicker and easier to just "sell the item to the game".</p>
  <p>For new players especially, selling items in this way is the quickest way to make money.</p>
  <h5>Tricky Details</h5>
  <ol>
   <li><p>When you gamesell an item, if someone has a higher offer in the <a href="/reversemarket.php">Seller's Market</a>, the item will be sold to that player, and you'll receive the price they were offering.</p></li>
   <li><p>50% of all gamesold items are put into the <a href="/recycling_gamesell.php">Refuse Store</a> or <a href="/grocerystore_gamesold.php">Grocery Store</a> (depending on whether or not the item was a food; the rest of the gamesold items are gone forever).  Items in the Refuse Store and Grocery Store remain there for a few days, and are sold for much higher than their gamesell value.</p></li>
  </ol>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
