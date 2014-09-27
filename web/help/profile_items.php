<?php
require_once 'commons/init.php';

$require_login = 'no';
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/formatting.php';

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Help Desk &gt; Profile Items</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
  <h4><a href="/help/">Help Desk</a> &gt; Profile Items</h4>
  <p>On your profile, two kinds of items are listed:</p>
  <ol>
   <li>Any items placed on your Fireplace mantle (if you have built a Fireplace add-on for your house)</li>
   <li>Any items you have chosen to display on your profile, when they are in your house</li>
  </ol>
  <p>When you view an item's encyclopedia entry, you can choose a "Profile Display" rating, from 1 to 10.  (You can also choose "none".)</p>
  <p>Any item which has such a rating and is in your house will be displayed on your profile page.  The items are ordered according to their rating, from highest to lowest.</p>
  <p>You can view a player's profile by clicking on their name (and you can click on your name at the top of every page to visit your own profile).</p>
  <ul>
   <li><a href="/myaccount/profile_items.php">Show me all the items which I have set to display on my profile</a></li>
  </ul>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
