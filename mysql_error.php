<?php
require_once 'commons/settings.php';
require_once 'commons/formatting.php';

$NO_LOGIN = true;

$id = (int)$_GET['id'];

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Home</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php
include 'commons/header_2.php';
?>
     <h4>MySQL connection error <?= $id ?></h4>
     <p>MySQL is the database which stores all of your user and pet information, inventory, mail, and so on.</p>
     <p>Apparently the server had trouble connecting to it.</p>
     <p>Not to worry!  Try again in a few minutes.  If the problem persists, I probably know about it, and am working on a solution.</p>
     <center><a href="/">Take a chance.  Try again.</a></center>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
