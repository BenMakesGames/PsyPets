<?php
$require_login = "no";

// confirm the session...
require_once "commons/dbconnect.php";
require_once "commons/rpgfunctions.php";
require_once "commons/sessions.php";
require_once "commons/formatting.php";

if($_POST["action"] == "sendmail")
{
  $messages = array();

  $_POST["message"] = stripslashes($_POST["message"]);

  if(strlen($_POST["name"]) < 1)
    $messages[] = "Please include a name to go by.";
  if(strlen($_POST["email"]) < 5)
    $messages[] = "I will not be able to respond unless you provide an e-mail address.";
  if(strlen($_POST["message"]) < 8)
    $messages[] = "Please provide some kind of meaningful message.";

  if(count($messages) == 0)
  {
    $name = $_POST["name"];
    $from = $_POST["email"];
    $subject = "psypets form mail: " . $_POST["subject"];
    $message = str_replace(array("&", "<", ">", "\n", "\r"), array("&amp;", "&lt;", "&gt;", "<br>", ""), $_POST["message"]);

    $name = str_replace(array("\n", "\r"), array("", ""), $name);
    $from = str_replace(array("\n", "\r"), array("", ""), $from);
    $subject = str_replace(array("\n", "\r"), array("", ""), $subject);

    $message .= "<br><br>$name";
    if($user["display"] > 0)
      $message .= " (resident " . $user["display"] . ")";

    $message .= "<br>$from";

    $message = "<p style=\"font-family: Arial; font-size: 11pt;\">$message</p>";

    mail($SETTINGS['author_real_name'] . ' <' . $SETTINGS['author_email'] . '>', $subject, $message, "MIME-Version: 1.0\nContent-type: text/html; charset=utf-8\nFrom: " . $name . " <" . $SETTINGS['site_mailer'] . ">");

    $mail_sent = true;
  }
  else
    $mail_sent = false;
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Help Desk &gt; Contact Me</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/help/">Help Desk</a> &gt; Contact Me</h4>
<?php
 if($mail_sent == false)
 {
?>
     <p>If you need to contact me out-of-game for any reason, feel free to use this form.  Please do not use it to contact me about in-game issues - these should be posted to the forums, or mailed to me in-game from the account which is experiencing the problem.</p>
     <h5>Frequently-asked Questions</h5>
     <ul>
      <li>
       <p><strong>"send me my password, I forgot it."</strong></p>
       <p>As it turns out, I can't, but you <em>can</em> use the <a href="resetpass.php">Reset Password</a> form to get a new one.</p>
      </li>
      <li>
       <p><strong>"can you send me my activation e-mail?  I didn't receive it."</strong></p>
       <p>There's a form for this as well, which will also correct your e-mail address in case you've mistyped it: the <a href="resendactivation.php">Resend Activation</a> form.</p>
      </li>
      <li>
       <p><strong>"can you send me my activation e-mail?  I tried, like, a million times, and I <em>still</em> haven't received it."</strong></p>
       <p>Be sure to provide your login name and e-mail address so that I can personally get back to you with the information.</p>
      </li>
     </ul>
     <hr id="mailme" />
<?php
   if(count($messages) > 0)
   {
     echo "     <p style=\"color:red;\">There were some errors:</p>\n";
     echo "     <ul>\n";

     foreach($messages as $message)
       echo "     <li style=\"color:red;\"><p style=\"color:red;\">$message</li>\n";

     echo "     </ul>\n";
   }
?>
     <form action="contactme.php#mailme" method="post">
     <table style="width:404px;">
      <tr>
       <th>Name:</th>
       <td align="right"><input maxlength=64 name="name" value="<?= $_POST["name"] ?>" style="width:300px;" /><br /></td>
      </tr>
      <tr>
       <th>E-mail address:</th>
       <td align="right"><input maxlength=64 name="email" value="<?= $_POST["email"] ?>"  style="width:300px;" /><br /></td>
      </tr>
      <tr>
       <th>Subject:</th>
       <td align="right"><input maxlength=128 name="subject" value="<?= $_POST["subject"] ?>"  style="width:300px;" /><br /></td>
      </tr>
      <tr>
       <th colspan=2>Message:</th>
      </tr>
      <tr>
       <td colspan=2>
        <textarea name="message" style="width:400px;" rows=10><?= $_POST["message"] ?></textarea>
       </td>
      </tr>
      <tr>
       <td colspan=2 align="right">
        <input type="hidden" name="action" value="sendmail" /><input type="submit" value="Send Message" class="bigbutton" />
       </td>
      </tr>
     </table>
     </form>
<?php
 }
 else
 {
?>
     <p>Your message has been sent!</p>
<?php
 }
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
