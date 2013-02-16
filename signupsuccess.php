<?php
$require_login = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/formatting.php';

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Sign Up &gt; Activation E-mail Sent!</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4>Sign Up &gt; Activation E-mail Sent!</h4>
     <p><strong>Your account was created successfully, but still needs to be activated!</strong>  If your account is not activated within the week it will be deleted.</p>
     <p>Check the e-mail address you provided for instructions on how to activate your account.</p>
     <h5>Help! I Didn't Get It!</h5>
     <ol>
      <li><p>Check your spam folder.  Sometimes activation e-mails are incorrectly categorized as spam due to the strange-looking links they contain.</p></li>
      <li><p>Wait a couple minutes.  On busy days, the activation e-mail may take a minute or two.</p></li>
      <li><p>Still not seeing it?  <a href="/resendactivation.php">Have the activation e-mail resent!</a></li>
     </ol>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
