<?php
$_GET['maintenance'] = 'no';

require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

require_once 'commons/admincheck.php';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';

if($admin['massgift'] != 'yes')
{
  header('Location: /admin/tools.php');
  exit();
}

$ALLOWED_RECIPIENTS = array('all', 'active', 'single');

// gift all
if($_POST['submit'] == 'Send Gift' && in_array($_POST['recipient'], $ALLOWED_RECIPIENTS))
{
  $comment = trim($_POST['comment']);
  $gift_value = $_POST['value'];
  $users = array();

  if($_POST['recipient'] == 'all')
		$command = 'SELECT * FROM `monster_users` WHERE `disabled`=\'no\'';
  else if($_POST['recipient'] == 'active')
  {
    $now = time();
    $yesterday = $now - 24 * 60 * 60;

    $command = "SELECT `user` " .
               "FROM `monster_users` " .
               "WHERE `disabled`='no' AND `lastactivity`>=$yesterday";
	}
  else if($_POST['recipient'] == 'single')
    $command = 'SELECT * FROM monster_users WHERE user=' . $database->Quote($_POST['resident']) . ' LIMIT 1';
	else
		die('$_POST[\'recipient\'] was ' . $_POST['recipient']);
		
	$these_users = $database->FetchMultiple($command);
		
  foreach($these_users as $this_user)
    $users[] = $this_user["user"];

  if($_POST['what'] == 'item')
  {
    $iteminfo = get_item_byname($gift_value);

    if($iteminfo === false)
      $error_message = 'There is no item called "' . $gift_value . '"';
    else
    {
      if(count($users) > 0)
      {
        foreach($users as $this_user)
        {
          if($this_user == $SETTINGS['site_ingame_mailer'])
            continue;

          $database->FetchNone('
            INSERT INTO `monster_inventory`
            (`user`, `itemname`, `message`, `location`, `health`, `changed`)
            VALUES
            (
              ' . quote_smart($this_user)  . ',
              ' . quote_smart($gift_value) . ',
              ' . quote_smart($comment) . ',
              \'storage/incoming\',
              ' . $iteminfo['durability'] . ',
              ' . $now . '
             )
          ');

          flag_new_incoming_items($this_user);
        }

        $success = true;
      }
    }

    $num_residents = count($users);
  }
  else if($_POST["what"] == "money")
  {
    if(is_numeric($gift_value) && (int)$gift_value == $gift_value)
    {
      if($_POST["recipient"] == "all")
      {
        $command = "UPDATE monster_users " .
                   "SET money=money+" . (int)$gift_value . " " .
                   "WHERE 1";
      }
      else if($_POST["recipient"] == "active")
      {
        $now = time();
        $yesterday = $now - 24 * 60 * 60;
        $command = "UPDATE monster_users SET money=money+" . (int)$gift_value . " " .
                   "WHERE `lastactivity`>=$yesterday";
      }
      else if($_POST["recipient"] == "single")
      {
        $command = "UPDATE monster_users SET money=money+" . (int)$gift_value . " " .
                   "WHERE user=" . quote_smart($_POST["resident"]) . " LIMIT 1";
      }

      if($command != "")
      {
        $database->FetchNone($command);

        $num_residents = $database->AffectedRows();
      }
    }
    else
      $error_message = "Please give a whole number value.";
  }
}

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Administrative Tools &gt; Gift Residents</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/admin/tools.php">Administrative Tools</a> &gt; Gift Residents</h4>
<?php
 if($error_message)
   echo "<p style=\"color:red;\">" . $error_message . "</p>\n";

 if($success == true)
   echo "<p style=\"color:blue;\">Gift handed out to $num_residents residents.</p>\n";
?>
     <table>
      <form action="/admin/gifts.php" method="post">
      <tr>
       <td colspan="2">
        <h5>Gift All Users</h5>
       </td>
      </tr>
      <tr>
       <td bgcolor="#f0f0f0">Gift:</td>
       <td>
        <select name="what">
         <option value="item">Object</option>
         <option value="money">Money</option>
        </select>
        <input name="value" value="<?= $gift_value ?>" />
       </td>
      </tr>
      <tr>
       <td bgcolor="#f0f0f0" valign="top">Recipients:</td>
       <td>
        <input type="radio" name="recipient" value="all" />Everyone<br />
        <input type="radio" name="recipient" value="active" />Everyone active within 24 hours<br />
        <input type="radio" name="recipient" value="donators" />Paid accounts with credit<br />
        <input type="radio" name="recipient" value="single" checked />Single user <input name="resident" /><br />
       </td>
      </tr>
      <tr>
       <td bgcolor="#f0f0f0">Comment:</td>
       <td><input name="comment" value="<?= $comment ?>" /></td>
      </tr>
      <tr>
       <td bgcolor="#f0f0f0"><p>&nbsp;</p></td>
       <td align="right">
        <input type="submit" name="submit" value="Send Gift" />
       </td>
      </tr>
      <tr>
       <td colspan=2><p>&nbsp;</p></td>
      </tr>
      </form>
     </table>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
