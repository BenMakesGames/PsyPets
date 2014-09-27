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
  <title><?= $SETTINGS['site_name'] ?> &gt; Help Desk &gt; Pet Care &gt; Park Events &gt; Digcraft Build</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/help/">Help Desk</a> &gt; <a href="/help/petcare.php">Pet Care</a> &gt; Park Events &gt; Digcraft Build</h4>
     <p>In a Digcraft Build, pets compete to build mighty structures of stone!  However, the stone is not provided for them; they must dig it up from the earth themselves.  Whatever the pets find, they may use for their constructions.</p>
     <p>A Digcraft Build tests the following skills:</p>
     <ul>
      <li>Mining</li>
      <li>Strength</li>
      <li>Stamina</li>
      <li>Sculpture</li>
      <li>Handicrafts</li>
     </ul>
     <p>Additionally, a pet open to new ideas will craft more-impressive sculptures.</p>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
