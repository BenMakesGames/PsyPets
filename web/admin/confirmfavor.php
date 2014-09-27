<?php
$IGNORE_MAINTENANCE = true;

require_once 'commons/init.php';

$require_petload = "no";

// confirm the session...
require_once "commons/dbconnect.php";
require_once "commons/rpgfunctions.php";
require_once "commons/sessions.php";
require_once "commons/grammar.php";
require_once "commons/formatting.php";
require_once "commons/donationlib.php";
require_once "commons/userlib.php";

if($admin["managedonations"] != "yes")
{
  header("Location: /");
  exit();
}

$recipient = get_user_byuser($_POST["user"]);

if($recipient["idnum"] == 0)
{
  header("Location: adminnewfavor.php?error=nosuchuser&user=" . $_POST["user"]);
  exit();
}

require 'commons/html.php';
?>
 <head>
  <title>PsyPets &gt; Administrative Tools &gt; Favor Management &gt; Record Favor</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/admin/tools.php">Administrative Tools</a> &gt; <a href="/admin/favors.php">Favor Management</a> &gt; Record Favor</h4>
<?php
 if($error_message)
   echo "<p style=\"color:red;\">" . $error_message . "</p>\n";
?>
     <p>If the information below is not correct, please <a href="javascript:history.go(-1);">go back</a> and correct it.</p>
     <form action="/admin/recordfavor.php" method="post">
     <input type="hidden" name="user" value="<?= $_POST["user"] ?>" />
     <input type="hidden" name="favor" value="<?= $_POST["favor"] ?>" />
     <input type="hidden" name="amount" value="<?= $_POST["amount"] ?>" />
     <table>
      <tr>
       <td><p>Login:</p></td>
       <td><p><?= $_POST["user"] ?></p></td>
      </tr>
      <tr>
       <td><p>Resident:</p></td>
       <td><p><?= $recipient["display"] ?></p></td>
      </tr>
      <tr>
       <td><p>Favor:</p></td>
       <td><p><?= $_POST["favor"] ?></p></td>
      </tr>
      <tr>
       <td><p>Value:</p></td>
       <td><p>$<?= (int)$_POST["amount"] ?></p></td>
      </tr>
      <tr>
       <td><p>&nbsp;</p></td>
       <td><input type="submit" value="Confirm" /><br /></td>
      </tr>
     </table>
     </form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
