<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$require_login = 'no';
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/formatting.php';

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Help Desk &gt; Pet Care &gt; Park Events &gt; Strategy Game</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/help/">Help Desk</a> &gt; <a href="/help/petcare.php">Pet Care</a> &gt; Park Events &gt; Strategy Game</h4>
     <p>In a Strategy Game, pets are challeneged against each other in a game of wits!</p>
     <p>Strategy Games test the following skills:</p>
     <ul>
      <li>Intelligence and quick wits</li>
      <li>A willingness to try new and unusual ideas</li>
     </ul>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
