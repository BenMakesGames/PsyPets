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

$donator = get_user_byuser($_POST["user"]);

if($donator["idnum"] == 0)
{
  header("Location: adminnewdonate.php?error=nosuchuser&user=" . $_POST["user"]);
  exit();
}

$existing_favor = get_favor_bypaypalid($_POST["paypalid"]);
$already_exists = ($existing_favor["paypalid"] == $_POST["paypalid"] && strlen($_POST["paypalid"]) > 0) ? true : false;

require 'commons/html.php';
?>
 <head>
  <title>PsyPets &gt; Administrative Tools &gt; Donation Management &gt; Record Donation</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/admin/tools.php">Administrative Tools</a> &gt; <a href="/admin/donations.php">Donation Management</a> &gt; Record Donation</h4>
<?php
 if($error_message)
   echo "<p style=\"color:red;\">" . $error_message . "</p>\n";
?>
     <p>If the information below is not correct, please <a href="javascript:history.go(-1);">go back</a> and correct it.</p>
     <form action="/admin/recorddonate.php" method="post">
     <input type="hidden" name="name" value="<?= $_POST["name"] ?>" />
     <input type="hidden" name="anon" value="<?= $_POST["anon"] ?>" />
     <input type="hidden" name="email" value="<?= $_POST["email"] ?>" />
     <input type="hidden" name="user" value="<?= $_POST["user"] ?>" />
     <input type="hidden" name="amount" value="<?= $_POST["amount"] ?>" />
     <input type="hidden" name="realamount" value="<?= $_POST["realamount"] ?>" />
     <input type="hidden" name="paypalid" value="<?= $_POST["paypalid"] ?>" />
     <table>
      <tr>
       <th>Full Name:</th>
       <td><?= $_POST["name"] . (($_POST["anon"] == "on" || $_POST["anon"] == "yes") ? " (anonymous)" : "") ?></td>
      </tr>
      <tr>
       <th>E-mail:</th>
       <td><?= $_POST["email"] ?></td>
      </tr>
      <tr>
       <th>Login:</th>
       <td><?= $_POST["user"] ?></td>
      </tr>
      <tr>
       <th>Resident:</th>
       <td><?= $donator["display"] ?></td>
      </tr>
      <tr>
       <th valign="top">PayPal ID:</th>
       <td valign="top"><?= $_POST["paypalid"] ?><?= ($already_exists == true) ? "<br /><font style=\"color:red;\">(a donation with this ID already exists)</font>" : "" ?></td>
      </tr>
      <tr>
       <th>Amount Paid:</th>
       <td>$<?= $_POST["amount"] ?></td>
      </tr>
      <tr>
       <th>Amount Received:</th>
       <td>$<?= $_POST["realamount"] ?></td>
      </tr>
<?php if($already_exists == false) { ?>
      <tr>
       <td></td>
       <td><input type="submit" value="Confirm" /></td>
      </tr>
<?php } ?>
     </table>
     </form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
