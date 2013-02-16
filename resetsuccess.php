<?php
$require_login = 'no';
$invisible = 'yes';

require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/formatting.php';

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Reset Lost Password</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4>Reset Lost Password</h4>
     <p class="success">Fantastic!  You should receive an e-mail shortly.</p>
     <p>If you still have trouble logging in to your account, <a href="contactme.php">contact me</a>, and we'll see if we can figure something out.  (Be sure to tell me everything you tried, and why it didn't work.)</p>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
