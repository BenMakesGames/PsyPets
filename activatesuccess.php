<?php
$require_login = 'no';
$invisible = 'yes';

require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/encryption.php';
require_once 'commons/dbconnect.php';
require_once 'commons/formatting.php';

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Activate Account</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4>Your account has been activated!  w00t!</h4>
     <p>You may now log in to <?= $SETTINGS['site_name'] ?> using your login name and password.  Your activation number will no longer be needed.</p>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
