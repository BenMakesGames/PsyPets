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
     <h5>Hello, Kiddo</h5>
     <p>It's been noted that you're currently 14 or younger.</p>
     <p><?= $SETTINGS['site_name'] ?> is a very open site, with a strong respect for freedom of speech.  Many <?= $SETTINGS['site_name'] ?> players are in high school, college, or even older, so not all discussion may be appropriate for kids.</p>
     <p>That being said, the forums and in-game mailing system are optional!  <?= $SETTINGS['site_name'] ?> can largely be played while ignoring that other players exist.</p>
     <p>Still, while some accommodations have been made, <?= $SETTINGS['site_name'] ?> does not necessarily cater to children.  If this does not sit well with you, please do not activate your account.</p>
     <p>If you do play, here are some considerations which apply to internet use in general (not just on <?= $SETTINGS['site_name'] ?>) for children under the age of 14:</p>
     <ul>
      <li>Do not reveal your real name, location, or any other personally-identifying information, not even to site administrators.</li>
      <li>Do not post any phone numbers, screen names, e-mail addresses, or any other contact information.</li>
      <li>Do not post pictures of yourself.</li>
     </ul>
     <p>Thanks for reading all this - it is important!</p>
     <p>Have fun!</p>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
