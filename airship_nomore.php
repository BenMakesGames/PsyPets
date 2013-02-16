<?php
// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/formatting.php';
require_once 'commons/grammar.php';
require_once 'commons/utility.php';

include 'commons/html.php';
?>
 <head>
<?php include 'commons/head.php'; ?>
  <title><?= $SETTINGS['site_name'] ?> &gt; Aeronautical Society &gt; Airship No Longer Exists</title>
  <style type="text/css">
   #family td
   {
     padding-left: 3em;
   }
  </style>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
  <h4>Aeronautical Society &gt; Airship No Longer Exists</h4>
<?php
include 'commons/dialog_open.php';
?>
  <p>This Airship no longer exists (it was retired by its owner), or never did (you're playing with the number in the URL).</p>
  <p>Terribly sorry about the inconvenience.</p>
<?php
include 'commons/dialog_close.php';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
