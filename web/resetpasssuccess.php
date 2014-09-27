<?php
$require_login = 'no';

require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/encryption.php';
require_once 'commons/formatting.php';

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Reset Lost Password</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h5>Reset Lost Password</h5>
     <p class="success">Your password was reset successfully!  Try logging in now!</p>
     <p>The new password was given in the "<?= $SETTINGS['site_name'] ?> lost password" e-mail that you received in response to the password reset request.</p>
     <p>Once you are able to login, <strong>please</strong> reset your password to something you can remember, so you don't forget it again!</p>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
