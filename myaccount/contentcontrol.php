<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$require_petload = 'no';

// confirm the session...
require_once "commons/dbconnect.php";
require_once "commons/rpgfunctions.php";
require_once "commons/sessions.php";
require_once "commons/grammar.php";
require_once "commons/encryption.php";
require_once "commons/formatting.php";
require_once "commons/userlib.php";

if($user['parentalaccess'] == 'yes' || $user['parentalpassword'] == '')
{
  if($_POST['action'] == 'updatesettings')
  {
    $command = 'UPDATE monster_users SET parentalaccess=\'yes\',parentalpassword=' . quote_smart($_POST['password']) . ',parentalemail=' . quote_smart($_POST['email']) . ' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
    fetch_none($command, 'changing content control settings');

    $user['parentalaccess'] = 'yes';
    $user['parentalpassword'] = $_POST['password'];
    $user['parentalemail'] = $_POST['email'];
    
    $message = '<span class="success">General Settings were updated successfully.</span>';
  }
  else if($_POST['action'] == 'childtoggle')
  {
    if($user['childlockout'] == 'yes')
      $user['childlockout'] = 'no';
    else
      $user['childlockout'] = 'yes';

    $command = 'UPDATE monster_users SET childlockout=' . quote_smart($user['childlockout']) . ' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
    fetch_none($command, 'toggling child lock-out');
    
    $message = '<span class="success">Child Lock-out is now ' . ($user['childlockout'] == 'yes' ? 'ENABLED' : 'DISABLED') . '.</span>';
  }
  else if($_POST['action'] == 'lock')
  {
    $command = 'UPDATE monster_users SET parentalaccess=\'no\' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
    fetch_none($command, 'disabling parental access');

    $user['parentalaccess'] = 'no';
  }
}
else
{
  if($_POST['action'] == 'login')
  {
    if($_POST['password'] == $user['parentalpassword'])
    {
      $command = 'UPDATE monster_users SET parentalaccess=\'yes\' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
      fetch_none($command, 'enabling parental access');

      $user['parentalaccess'] = 'yes';
    }
    else
      $message = '<span class="failure">That password is not correct.</span>';
  }
  else if($_GET['action'] == 'email')
  {
    mail($user['parentalemail'], $SETTINGS['site_name'] . ' Content Control password', 'Your password for the Content Control panel is:  ' . $user['parentalpassword'], "MIME-Version: 1.0\nContent-type: text/html; charset=utf-8\nFrom: " . $SETTINGS["site_mailer"]);
    $message = '<span class="progress">The password has been e-mailed.  It should arrive very soon.  Like within a minute.</span>';
  }
}

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; My Account &gt; Content Control</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/myaccount/">My Account</a> &gt; Content Control</h4>
     <ul class="tabbed">
<?php
if($user['childlockout'] == 'no')
{
?>
      <li><a href="/myaccount/profile.php">Resident&nbsp;Profile</a></li>
      <li><a href="/myaccount/searchable.php">Searchable&nbsp;Profile</a></li>
<?php
}
?>
      <li><a href="/myaccount/petprofile.php">Pet&nbsp;Profiles</a></li>
      <li><a href="/myaccount/display.php">Display&nbsp;Settings</a></li>
      <li><a href="/myaccount/behavior.php">Behavior&nbsp;Settings</a></li>
      <li><a href="/myaccount/security.php">Account&nbsp;Management</a></li>
      <li><a href="/myaccount/favorhistory.php">Favor&nbsp;History</a></li>
      <li class="activetab"><a href="/myaccount/contentcontrol.php">Content&nbsp;Control</a></li>
     </ul>
<?php
if($message)
  echo '<p>' . $message . '</p>';

if($user['parentalaccess'] == 'yes' || $user['parentalpassword'] == '')
{
  if($user['parentalpassword'] != '')
  {
?>
     <p>Don't forget to lock this page when you're done!  Just in case, this page will automatically lock when you log out of <?= $SETTINGS['site_name'] ?>.</p>
     <form method="post">
     <p><input type="hidden" name="action" value="lock" /><input type="submit" value="Lock This Page" class="bigbutton" /></p>
     </form>

<?php
  }
?>
     <h5>General Settings</h5>
     <form method="post">
     <table>
      <tr>
       <td>Password:</td>
       <td><input name="password" value="<?= $user['parentalpassword'] ?>" /></td>
       <td>Leaving this blank means you do not want to password-protect this page.</td>
      </tr>
      <tr>
       <td>E-mail:</td>
       <td><input name="email" value="<?= $user['parentalemail'] ?>" /></td>
       <td>In the event you forget your content control password, it can be sent to this e-mail address.</td>
      </tr>
      <tr>
       <td></td>
       <td colspan="2">
        <input type="hidden" name="action" value="updatesettings" /><input type="submit" value="Update" />
       </td>
      </tr>
     </table>
     </form>
     
     <h5>Child Lock-out</h5>
     <p>Status: <strong><?= $user['childlockout'] == 'yes' ? 'ENABLED' : 'DISABLED' ?></strong></p>
     <p>When enabled, Child Lock-out prevents this account from accessing the following pages and features:</p>
     <ul>
      <li>The Plaza</li>
      <li>Favorite Threads</li>
      <li>Groups</li>
      <li>My Groups</li>
      <li>Advertising</li>
      <li>Directory</li>
      <li>player profile pages</li>
      <li>ability to change e-mail address of the account</li>
     </ul>
     <form method="post">
     <p><input type="hidden" name="action" value="childtoggle" /><input type="submit" value="Toggle" /></p>
     </form>
<?php
}
else
{
?>
     <p>Enter the password to access Content Control settings:</p>
     <form method="post">
     <p><input type="password" name="password" /> <input type="hidden" name="action" value="login" /><input type="submit" value="Login" /></p>
     </form>
<?php
  if($user['parentalemail'] != '')
  {
?>
     <ul>
      <li><a href="/myaccount/contentcontrol.php?action=email">I forgot this password - e-mail it to me at <?= $user['parentalemail'] ?></a></li>
     </ul>
<?php
  }
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
