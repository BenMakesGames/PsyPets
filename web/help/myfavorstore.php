<?php
require_once 'commons/init.php';

$require_login = "no";

// confirm the session...
require_once "commons/dbconnect.php";
require_once "commons/rpgfunctions.php";
require_once "commons/sessions.php";
require_once "commons/formatting.php";

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Help Desk &gt; My Favor Store</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/help/">Help Desk</a> &gt; My Favor Store</h4>
     <h5>Why aren't all my custom items listed here?</h5>
     <p>There are two reasons why an item would not be listed for you to sell:</p>
     <ol>
      <li>It is already listed!  Double-check <a href="/myfavorstore.php">My Favor Store</a> to see if that's the case.</li>
      <li>Custom items made prior to May 1st, 2010 may not be listed.</li>
     </ol>
     <h5>Why can't items made before May 1st, 2010 be listed for sale?</h5>
     <p>The custom item creation system has changed over the years, allowing people to create custom items in different ways.  In April 2010 particularly, several sweeping changes were made to items and to pet behavior.  To make sure that all the items being sold in Favor Stores are remotely comparable, only items created since May 1st, 2010 may be listed for sale in the Favor Stores.</p>
     <p>You can still arrange to exchange older custom items by communicating with their creators directly.</p>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
