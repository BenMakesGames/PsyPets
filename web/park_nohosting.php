<?php
// THE PARK CODE IS IMMUNE TO MISSING ACCOUNTS
// it does not need to be disabled with $NO_PVP

$wiki = 'The_Park';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/doevent.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/checkpet.php';
require_once 'commons/parklib.php';
require_once 'commons/messages.php';
require_once 'commons/questlib.php';

if($user['show_park'] != 'yes')
{
  header('Location: /404');
  exit();
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; The Park</title>
<?php include 'commons/head.php'; ?>
  <script type="text/javascript" src="//<?= $SETTINGS['static_domain'] ?>/js/adrate3.js"></script>
 </head>
 <body>
<?php
include 'commons/header_2.php';
?>
     <?= ($check_message ? "<p style=\"color:blue;\">$check_message</p>" : "") ?>
     <h4>The Park</h4>
<?php
if(strlen($_GET['msg']) > 0)
  $error_message = form_message(explode(',', $_GET['msg']));

if($error_message)
  echo "<p>$error_message</p>";

echo '
  <ul class="tabbed">
   <li><a href="park.php">Browse Events</a></li>
   <li class="activetab"><a href="hostevent1.php">Host a new event</a></li>
   <li><a href="park_exchange.php">Exchanges</a></li>
  </ul>
';

include 'commons/bcmessage2.php';
?>
<p>You must have a License to Commerce to host a Park Event.  You can get one from <a href="bank.php">The Bank</a> if you don't already have one.</p>
<p>Your account must also be at least 24 hours old.</p>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
