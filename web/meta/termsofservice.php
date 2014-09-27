<?php
require_once 'commons/init.php';

$require_login = 'no';
$require_petload = 'no';
$reading_tos = true;
$whereat = 'getbirthday';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';

if($_POST['action'] == 'ireadit' && (int)$user['idnum'] > 0)
{
  $command = 'UPDATE monster_users SET readtos=\'yes\' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
  fetch_none($command, 'read terms of service');

  header('Location: /');
  exit();
}

include 'commons/html.php';
?>
 <head>
  <title>PsyPets &gt; Terms of Service</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
<?php
if($user['readtos'] == 'no')
{
?>
     <h4>PsyPets' Terms of Service Have Changed!</h4>
     <p>You must agree to PsyPets' Terms of Service to play.  Since they have been changed, you are being presented them again.</p>
<?php
}

include 'commons/tos.php';

if($user['readtos'] == 'no')
{
?>
     <hr />
     <form method="post">
     <p>To continue playing PsyPets, you must read and agree to these Terms of Service.</p>
     <p>If you are not willing to read or agree to these Terms of Service, log out now.</p>
     <p><input type="hidden" name="action" value="ireadit" /><input type="submit" value="I Agree" /></p>
<?php
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
