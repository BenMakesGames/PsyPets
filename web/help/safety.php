<?php
require_once 'commons/init.php';

$require_login = "no";

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/formatting.php';

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Help Desk &gt; Pet Care &gt; Safety</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/help/">Help Desk</a> &gt; <a href="/help/petcare.php">Pet Care</a> &gt; Safety</h4>
     <p>A pet's Safety decreases by 1 per hour.  When it gets to 0 or below, the pet cowers in a corner, and the increase of Love, Esteem, and experience points becomes impossible.</p>
     <p>A sleeping pet does not lose any Safety.</p>
     <p>As a pet feels less safe, it is more likely to spend its hourly action being comforted by safety items in the house (pillows and plushies, for example).</p>
     <p>A few foods provide Safety when fed to a pet, and many half-hourly actions - including petting - provide Safety, when taken.</p>
     <p>The Moat add-on provides a passive safety bonus to pets in your house from hour to hour, slowing their decline.  Having a house full of friendly pets can also maintain a feeling of safety in this way.</p>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
