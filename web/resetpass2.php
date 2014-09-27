<?php
$require_login = 'no';

require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/encryption.php';
require_once 'commons/formatting.php';

$userid = $_GET['userid'];
$resetid = $_GET['resetid'];

$reset_info = $database->FetchSingle('SELECT * FROM monster_passreset WHERE userid=' . (int)$userid . ' AND resetid=' . quote_smart($resetid) . ' LIMIT 1');
if($reset_info !== false)
{
  if($reset_info["timestamp"] + (24 * 60 * 60) < $now)
  {
    $command = 'DELETE FROM `monster_passreset` WHERE userid=' . (int)$userid . ' LIMIT 1';
    $database->FetchNone($command, 'deleting password reset record (1)');

    $display = "toolate";
  }
  else
  {
    $database->FetchNone('UPDATE monster_users SET pass="' . $reset_info['newpass'] . '" WHERE idnum=' . (int)$userid . ' LIMIT 1');
    $database->FetchNone('DELETE FROM `monster_passreset` WHERE userid=' . (int)$userid . ' LIMIT 1');

    header('Location: /resetpasssuccess.php');
    exit();
  }
}
else
  $display = "notfound";

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Reset Lost Password</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h5>Reset Lost Password</h5>
<?php
if($display == "success")
{
?>
     <p>Your password was reset successfully!</p>
     <p>The new password was given in the "<?= $SETTINGS['site_name'] ?> lost password" e-mail that you received in response to the password reset request.</p>
<?php
}
else if($display == "toolate")
{
?>
     <p>The password could not be reset.</p>
     <p>For security you must reset your password within 24 hours of the reset request.</p>
     <p>If you would like, you may <a href="/resetpass.php">request another password reset</a>.</p>
<?php
}
else if($display == "notfound")
{
?>
     <p>The password could not be reset.</p>
     <p>There is no record of a request to reset your password.</p>
     <p>It's possible you tried to reset your password using a link from an old password reset e-mail, however these links are only valid for 24 hours.  If this is the case, you should <a href="/resetpass.php">request another password reset</a> if you need to reset your password again.</p>
<?php
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
