<?php
$require_petload = 'no';

// confirm the session...
require_once "commons/dbconnect.php";
require_once "commons/rpgfunctions.php";
require_once "commons/doevent.php";
require_once "commons/sessions.php";
require_once "commons/grammar.php";
require_once "commons/formatting.php";
require_once 'commons/parklib.php';

include 'commons/html.php';
?>
 <head>
  <title>PsyPets &gt; Support PsyPets &gt; Done!</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4>Support PsyPets &gt; Done!</h4>
     <p>Your payment has been received!  Thanks :)</p>
     <p>You should receive an in-game mail shortly confirming that the payment was processed.  If you don't receive such a mail within 24 hours, please contact <a href="admincontact.php">an administrator</a>.</p>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
