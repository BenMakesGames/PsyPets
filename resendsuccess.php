<?php
$require_login = "no";

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/formatting.php';

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Sign Up &gt; Success</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4>Resend Confirmation E-mail &gt; Success</h4>
     <p>The confirmation e-mail has been sent.</p>
     <p>Check the e-mail address you provided for instructions on how to activate your account.</p>
     <p>If you still do not receive the e-mail, try the following things:</p>
     <ul class="spacedlist">
      <li>If you have a spam filter, check your "spam" folder, if you have one, to see if the mail got sent there.</li>
      <li>If you have a spam filter, try adding <?= $SETTINGS['site_mailer'] ?> to your address book, then have the confirmation e-mail sent again.  Most spam filters will automatically accept any mail from an address in your address book.  You can always remove <?= $SETTINGS['site_mailer'] ?> once you get the confirmation e-mail.</li>
      <li>If you have a spam filter, try turning it off, then have the confirmation e-mail sent again.  Once you receive it (or if you don't receive it soon), be sure to turn your filter back on!  <em>Don't forget to turn it back on!</em></li>
      <li>If all else fails, <a href="contactme.php">contact me</a> with your login name and e-mail address, and I will send you your activation key myself.</li>
     </ul>
     <ul>
      <li><a href="index.php">Back to login page</a></li>
      <li><a href="resendactivation.php">Resend activation e-mail</a></li>
     </ul>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
