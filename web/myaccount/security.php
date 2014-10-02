<?php
require_once 'commons/init.php';

$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/encryption.php';
require_once 'commons/formatting.php';
require_once 'commons/userlib.php';

function mask_email($email)
{
  $at = strpos($email, '@');
  $dot = strrpos($email, '.');

  $name = substr($email, 0, $at);
  $domain = substr($email, $at + 1, $dot - $at - 1);
  $tld = substr($email, $dot + 1);

  return $name[0] . str_repeat('*', strlen($name) - 1) . '@' . $domain[0] . str_repeat('*', strlen($domain) - 1) . '.' . $tld;
}

if($_POST["action"] == "changepassword")
{
  $errored = false;

  if(md5($_POST["oldpass"]) != $user["pass"])
  {
    $oldpass_message = "Password is incorrect.";
    $_POST["oldpass"] = "";
    $errored = true;
  }

  if(strlen($_POST['pass1']) > 50 || strlen($_POST['pass1']) < 6)
  {
    $pass_message = 'Your password must be between 6 and 50 characters.';
    $_POST['pass1'] = '';
    $_POST['pass2'] = '';
    $errored = true;
  }
  else if($_POST['pass1'] != $_POST['pass2'])
  {
    $pass_message = 'Your passwords do not match.';
    $_POST['pass1'] = '';
    $_POST['pass2'] = '';
    $errored = true;
  }

  if(!$errored)
  {
    $md5_pass = md5($_POST['pass1']);
  
    $database->FetchNone("UPDATE `monster_users` SET pass=" . quote_smart($md5_pass) . ",passworddate=" . $now . " WHERE idnum=" . quote_smart($user["idnum"]) . " LIMIT 1");

    $general_message = 'Account updated successfully.';

    $user['passworddate'] = $now;

    $_POST['pass1'] = '';
    $_POST['pass2'] = '';
    $_POST['oldpass'] = '';

    if($md5_pass != $user['pass'])
    {
      require_once 'commons/statlib.php';

      if(record_stat_with_badge($user['idnum'], 'Changed Your Password', 1, 1, 'secure'))
        $general_message .= '</p><p><i>(You received the Security-conscious badge!)</i></p>';
    }
  }
}
else if($_POST["action"] == "updateemail" && $user['childlockout'] == 'no')
{
  $_POST['newemail'] = trim($_POST['newemail']);

  $_POST['newemail'] = preg_replace('/\+[^@]*/', '', $_POST['newemail']); // remove "+...." from end of e-mail address

  if(strlen($_POST['newemail']) < 5)
  {
    $errored = true;

    $email_message = "Please enter a valid e-mail address.";
  }
  else if(strpos($_POST['newemail'], '@psypets.net'))
  {
    $email_message = 'That e-mail address is already in use.';
    $errored = true;
  }
  else
  {
		$existing = $database->FetchSingle('
			SELECT idnum
			FROM monster_users
			WHERE
				email=' . quote_smart($_POST['newemail']) . '
				OR newemail=' . quote_smart($_POST['newemail']) . '
			LIMIT 1
		');
   
    if($existing !== false)
    {
      $errored = true;
      $email_message = 'This e-mail address is already in use.';
    }
    else
    {
      $activekey = rand(100000, 999999);

      $database->FetchNone("UPDATE `monster_users` SET `activateid`='$activekey',`newemail`=" . quote_smart($_POST["newemail"]) . " WHERE idnum=" . $user["idnum"] . " LIMIT 1");

      $message  = "<p>To confirm this e-mail address as your new address, click this link (or copy and paste it into your browser):</p>\n\n";
      $message .= '<p>//' . $SETTINGS['site_domain'] . '/updateemail.php?id=' . $user["idnum"] . "&confirm=$activekey</p>\n\n";

      mail($_POST["newemail"], $SETTINGS['site_name'] . " E-Mail Address Change-o-Matic", $message, "MIME-Version: 1.0\nContent-type: text/html; charset=iso-8859-1\nFrom: sender@psypets.net");

      $general_message = "E-mail address change submitted.  Check your mail at " . $_POST["newemail"] . " to confirm the change.  If you entered the wrong e-mail address just now, go ahead and enter the correct one and click \"Change E-mail\" again.";

      $user["newemail"] = $_POST["newemail"];
    }
  }
}
else if($_GET['action'] == 'nopwchange')
{
  srand($user['passworddate']);
  if($_GET['token'] == md5(rand()))
  {
    $new_time = $now - 62 * 24 * 60 * 60 - mt_rand(0, 60);
  
    $command = 'UPDATE monster_users SET passworddate=' . $new_time  . ' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
    fetch_none($command, 'updating password date');
    
    $user['passworddate'] = $new_time;
    
    $general_message = 'Be safe!';
  }
}

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; My Account &gt; Account Management</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/myaccount/">My Account</a> &gt; Account Management</h4>
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
      <li class="activetab"><a href="/myaccount/security.php">Account&nbsp;Management</a></li>
      <li><a href="/myaccount/favorhistory.php">Favor&nbsp;History</a></li>
      <li><a href="/myaccount/contentcontrol.php">Content&nbsp;Control</a></li>
     </ul>
<?php
if($general_message)
  echo '<p style="color:blue;">' . $general_message . '</p>';

if($now > $user['passworddate'] + (93 * 24 * 60 * 60))
{
  srand($user['passworddate']);
  echo '
    <p class="obstacle">Your password has not been changed in over three months!  You should change your password from time to time to help keep your account secure.</p>
    <ul><li><a href="/myaccount/security.php?action=nopwchange&token=' . md5(rand()) . '">You are wise, ' . $SETTINGS['site_name'] . ', to encourage this practice, however please believe me when I say that a password change is not required at this time.</a></li></ul>
  ';
}
?>
  <table>

   <form method="post">
   <tr class="titlerow">
    <td colspan=2 align="center"><h4>Change Password</h4></td>
    <td></td>
   </tr>
   <tr>
    <td class="leftbar">Old&nbsp;password:</td>
    <td><input name="oldpass" type="password" value="<?= $_POST["oldpass"] ?>"></td>
<?php
 if($oldpass_message)
   echo "    <td><p class=\"failure\">$oldpass_message</p></td>\n";
 else
   echo "    <td></td>\n";
?>
   </tr>
   <tr>
    <td class="leftbar">New&nbsp;password:</td>
    <td><input name="pass1" type="password" value="<?= $_POST["pass1"] ?>"></td>
<?php
 if($pass_message)
   echo "    <td valign=\"center\" rowspan=\"2\"><p class=\"failure\">$pass_message</p></td>\n";
 else
   echo "    <td valign=\"center\" rowspan=\"2\"><p>Must be at least 6 characters long.</p><p class=\"nomargin\">Remember: you can use spaces.  Try using a short sentence for your password: it'll be long, yet easy to remember!</p></td>\n";
?>
   </tr>
   <tr>
    <td class="leftbar">Confirm:</td>
    <td><input name="pass2" type="password" value="<?= $_POST["pass2"] ?>" /></td>
   </tr>
   <tr>
    <td></td>
    <td colspan="2"></td>
   </tr>
   <tr>
    <td colspan="3" align="center"><input type="hidden" name="action" value="changepassword" /><input type="submit" name="submit" value="Change Password" class="bigbutton" /></td>
   </tr>
   <tr>
    <td></td>
    <td colspan="2"></td>
   </tr>
   </form>

<?php
if($user['childlockout'] == 'yes')
{
?>
   <tr class="titlerow">
    <td colspan="2" align="center"><h4>Change E-mail Address</h4></td>
    <td></td>
   </tr>
   <tr>
    <td class="leftbar">Current address:</td>
    <td><input name="oldemail" type="email" value="<?= mask_email($user['email']) ?>" disabled /></td>
    <td>You will need your parent to update your e-mail address for you.</td>
   </tr>
   <tr>
    <td></td>
    <td colspan="2"></td>
   </tr>

<?php
}
else
{
?>
   <form method="post">
   <tr class="titlerow">
    <td colspan="2" align="center"><h4>Change E-mail Address</h4></td>
    <td></td>
   </tr>
   <tr>
    <td class="leftbar">Current&nbsp;address:</td>
    <td><input name="oldemail" type="email" value="<?= mask_email($user['email']) ?>" disabled /></td>
    <td>Your current address is partially masked as a basic security measure.</td>
   </tr>
   <tr>
    <td class="leftbar">New&nbsp;address:</td>
    <td><input name="newemail" type="email" value="<?= $user['newemail'] ?>" /></td>
<?php
 if($email_message)
   echo '<td class="failure">' . $email_message . '</td>';
 else
   echo '<td>A confirmation e-mail will be sent to this address.</td>';
?>
   </tr>
   <tr>
    <td></td>
    <td colspan="2"></td>
   </tr>
   <tr>
    <td colspan="3" align="center"><input type="hidden" name="action" value="updateemail" /><input type="submit" name="submit" value="Change E-mail" class="bigbutton" /></td>
   </tr>
   <tr>
    <td></td>
    <td colspan="2"></td>
   </tr>
   </form>
<?php
}
?>
  </table>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
