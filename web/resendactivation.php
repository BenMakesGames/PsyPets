<?php
$require_login = 'no';

require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/formatting.php';
require_once 'commons/userlib.php';

if($_POST['submit'] == 'Resend E-mail')
{
  $this_user = get_user_byuser($_POST["user"]);

  $email_user = get_user_byemail($_POST["email"]);

  if(strtolower($this_user["user"]) == strtolower($_POST["user"]))
  {
    if($this_user["pass"] == md5($_POST["pass"]))
    {
      if($this_user["disabled"] == "yes")
      {
        $error_message = "This account has been disabled.";
      }
      else if($this_user["activated"] == "yes")
      {
        $error_message = "This account has already been activated.";
      }
      else if(strtolower($email_user["email"]) == strtolower($_POST["email"]) && $email_user["user"] != $this_user["user"])
      {
        $error_message = "The e-mail address given is already in use by another " . $SETTINGS['site_name'] . " Resident.";
      }
      else
      {
        $command = "UPDATE `monster_users` " .
                   "SET email=" . quote_smart($_POST["email"]) . " " .
                   "WHERE idnum=" . (int)$this_user["idnum"] . " LIMIT 1";

        $database->FetchNone($command, 'updating account e-mail address');

        $activekey = $this_user["activateid"];

        $message = '
          <html><body style="font-family: Arial; font-size: 15px;">

          <p>You have registered for ' . $SETTINGS['site_name'] . ' with the login name ' . $this_user['user'] . ', however your account still needs to be activated!</p>

          <p>Your activation key is "' . $activekey . '" (without the quotes).</p>

          <p>To activate your account, visit <a href="http://' . $SETTINGS['site_domain'] . '/activate.php">http://' . $SETTINGS['site_domain'] . '/activate.php</a> and type in your login name and activation key, or use this link to do it automatically: <a href="http://' . $SETTINGS['site_domain'] . '/activate.php?user=' . $this_user['user'] . '&amp;activate=' . $activekey . '">http://' . $SETTINGS['site_domain'] . '/activate.php?user=' . $this_user['user'] . '&amp;activate=' . $activekey . '</a></p>

          <p>Once your account has been activated you will no longer need the activation key.</p>

          <p><center>&diams; &diams; &diams; &diams; &diams;</center></p>

          <p>' . $SETTINGS['site_name'] . ' has an in-game mailing system.  You have been sent an introductory mail in-game which answers many of the most commonly asked questions, such as "How do I make money?", and explains how some of the basic game mechanics work.</p>

          <p>Please read this mail!</p>

          <p>Your in-game mail is found in your Mailbox, a link for which will be on the left of the screen once you have logged in.  There will also be an envelope icon in the upper-left, notifying you of unread mail, which can be clicked to take you to your Mailbox.</p>

          <p><center>&diams; &diams; &diams; &diams; &diams;</center></p>

          <p>Parents of young children may be interested in Content Control, as the public discussion boards are not always appropriate for all age groups!  After logging in, visit the "My Account" page (the link for which will be in the top-left area of the page).  From there, look for the "Content Control" section.</p>

          </body></html>
        ';

        mail($_POST['email'], $SETTINGS['site_name'] . " account activation", $message, "MIME-Version: 1.0\nContent-type: text/html; charset=utf-8\nFrom: " . $SETTINGS['site_mailer']);

        header('Location: ./resendsuccess.php');
        exit();
      }
    }
    else
    {
      $error_message = "Login name and password do not check out.";
    }
  }
  else
  {
    $error_message = "Login name and password do not check out.";
  }
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Resend Confirmation E-mail</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4>Resend Confirmation E-mail</h4>
     <p>If you have signed up, but have not received your confirmation e-mail, it can be sent to you again.</p>
     <p>If you had mistyped the e-mail address when you signed up using this form will update the e-mail address.</p>
     <p>It's possible that spam filters might be catching the confirmation e-mail.  Check your "spam" folder, if you have one, before requesting another confirmation.</p>
     <p>If nothing seems to be working, <a href="contactme.php">let me know</a>, and I'll see if I can't get things working for you.</p>
<?php
if($error_message != '')
  echo '<ul><li class="failure">' . $error_message . '</li></ul>';
?>
     <form action="resendactivation.php" method="post">
     <table>
      <tr>
       <td>Login name:</td>
       <td><input name="user" maxlength=16 style="width:145px;" value="<?= $_POST["user"] ?>" /></td>
      </tr>
      <tr>
       <td>Password:</td>
       <td><input name="pass" maxlength=32 type="password" style="width:145px;" value="<?= $_POST["pass"] ?>" /></td>
      </tr>
      <tr>
       <td>E-mail address:</td>
       <td><input name="email" maxlength=64 style="width:145px;" value="<?= $_POST["email"] ?>" /></td>
      </tr>
      <tr>
       <td colspan="2" align="center">
        <input type="submit" name="submit" value="Resend E-mail" class="bigbutton" />
       </td>
      </tr>
     </table>
     </form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
