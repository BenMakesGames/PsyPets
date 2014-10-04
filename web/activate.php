<?php
namespace PsyPets;

require_once 'commons/rpgfunctions.php';
require_once 'commons/encryption.php';
require_once 'commons/dbconnect.php';
require_once 'commons/formatting.php';
require_once 'commons/houselib.php';
require_once 'commons/userlib.php';
require_once 'commons/itemlib.php';

session_name("monstersession");
session_start();
header("Cache-control: private");

$now = time();

if($_GET["activate"] && $_GET["user"])
{
  $_POST["activateid"] = $_GET["activate"];
  $_POST["user"] = $_GET["user"];
  $_POST["submit"] = "Activate";
}

if($_POST["submit"] == "Activate")
{
  $this_user = get_user_byuser($_POST['user'], 'idnum,user,display,disabled,activated,activateid');

  if($this_user !== false)
  {
    if($this_user["disabled"] == "yes")
    {
      $error_message = "This account has been disabled.";
    }
    else if($this_user["activated"] == "no")
    {
      if($_POST["activateid"] == $this_user["activateid"])
      {
        $database->FetchNone('
					UPDATE `monster_users`
          SET
						`activated`=\'yes\',
						passworddate=' . $database->Quote($now) . ',
						`lastactivity`=' . $database->Quote($now) . '
          WHERE
						`user`=' . $database->Quote($this_user['user']) . '
					LIMIT 1
				');

        $message = "Hi, " . $this_user['display'] . "!  Welcome to your new home!<br />\n<br />\n" .
                   "If you've taken a moment to look around your house you'll have noticed that it came with a few furnishings, and some complementary snacks.<br />\n<br \>\n" .
                   "You'll be interacting with items like these a lot, so it's important to understand how they work:<br />\n<br />\n" .
                   "* Pillows, Paintings, and other furniture have a beneficial effect for your pets when left at home.  Pillows make your pet feel comfortable and safe, while decorative items like Paintings maintain a feeling of importance and accomplishment.  Without objects of comfort, the pets, like any of us, will become irritable and depressed!<br />\n<br />\n" .
                   "* Food items, when fed to your pets, are more than just filling: hand-feeding your pet shows it you love it.  Even better if the food is home made!  One of my favorite recipes is to Prepare two berries together, producing a jelly that is both filling and delicious.<br />\n<br />\n" .
                   "* Items you don't need can be sold for money.  You may only sell items from storage, so move any items you want to sell there, first.  Be careful about selling items early on, however, as you'll find you need many of them.<br />\n<br />\n" .
                   "Once you're settled in your new house, feel free to explore the city!<br />\n<br />\n" .
                   "* The Plaza is generally a fun place to hang out and chat.  People enjoy role-playing, posting art, talking about other games, and lots of other stuff.  Also, the Question & Answer section of The Plaza is a good place to get help.  (Please search The Plaza for answers to your questions before asking for them: it's unfortunate but true that some people have a low tolerance for so-called 'n00bs'.)<br />\n<br />\n" .
                   "* Visit The Bank for information on how to make money - one of the most common concerns of newer players.<br />\n<br />" .
                   "* Be sure to look at the various Services (in the menu at the top of the page), as well.  Several of them provide free services and useful information.<br />\n<br />\n" .
                   "If you have additional questions, please refer to the Help Desk, or the Plaza as mentioned.<br />\n<br />\n" .
                   "Thanks for joining, and good luck with everything!";

        psymail_user($this_user['user'], 'csilloway', 'Welcome to ' . $SETTINGS['site_name'] . '!', $message);

        add_inventory($this_user['user'], '', 'Small Painting', "Paintings and other decorative items in the house make your pets feel accomplished and successful.", "home");
        add_inventory($this_user['user'], '', 'Beginner\'s Cards', "Every half-hour, instead of petting, you may play a game with your pet.", "home");
        add_inventory($this_user['user'], '', 'White Pillow', "Comfort items, such as pillows, make your pet feel safe when kept around the house.", "home");
        add_inventory($this_user['user'], '', 'Sugar', "While sugar cannot be fed directly to pets, it is a common ingredient in many recipes.", "home");
        add_inventory($this_user['user'], '', 'Orange', "Oranges are a delicious and filling treat.", "home");
        add_inventory($this_user['user'], '', 'Rice', "Prepare Rice on its own to create Rice Flour; prepare that to make Rice Noodles!", "home");
        add_inventory($this_user['user'], '', 'Rubble', "Rummaging through debris might reveal something useful...", "home");

        $command = 'UPDATE monster_houses SET lasthour=' . ($now - 60 * 60) . ' WHERE userid=' . $this_user['idnum'] . ' LIMIT 1';
        $database->FetchNone($command, 'activating house');

        header('Location: /activatesuccess.php');
        exit();
      }
      else
        $error_message = 'That is not the correct activation id.';
    }
    else
    {
      $error_message = 'This account has already been activated.';
    }
  }
  else
  {
    $error_message = 'That login name does not exist.';
  }
}

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Activate Account</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4>Activate Account</h4>
     <p>Activate your account using the activation number given to you in the confirmation e-mail.</p>
     <p>If you did not receive your confirmation e-mail, <a href="resendactivation.php">you can have it sent to you again</a>.</p>
     <form method="post">
     <table>
<?php
if($error_message)
{
?>
      <tr>
       <td colspan="2" align="center"><p style="color:red;"><?= $error_message ?></p></td>
      </tr>
<?php
}
else
{
?>
<?php
}
?>
      <tr>
       <td style="padding-left:8px;"><p>Login name:</p></td>
       <td style="padding-right:8px;"><input name="user" maxlength="16" style="width:145px;" value="<?= $cookiename ?>"></td>
      </tr>
      <tr>
       <td style="padding-left:8px;"><p>Activation key:</p></td>
       <td style="padding-right:8px;"><input name="activateid" maxlength="32" style="width:145px;" value="<?= $cookiepass ?>"></td>
      </tr>
      <tr>
       <td colspan="2" align="center">
        <input type="submit" name="submit" value="Activate" style="width:100px;">
       </td>
      </tr>
     </table>
     </form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
