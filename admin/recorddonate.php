<?php
$_GET['maintenance'] = 'no';

require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$require_petload = "no";

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/donationlib.php';
require_once 'commons/userlib.php';

if($admin["managedonations"] != "yes")
{
  Header("Location: /");
  exit();
}

$donator = get_user_byuser($_POST["user"]);

if($donator["idnum"] == 0)
{
  header('Location: /adminnewdonate.php?error=nosuchuser&user=' . $_POST['user']);
  exit();
}

if($donator['donated'] == 'no')
{
  $command = "UPDATE monster_users SET donated='yes' WHERE idnum=" . $donator['idnum'] . ' LIMIT 1';
  $database->FetchNone(($command, 'admin record donation');
}

$command = 'UPDATE psypets_badges SET paidaccount=\'yes\' WHERE userid=' . $donator['idnum'] . ' LIMIT 1';
$database->FetchNone(($command, 'ipn handler');

add_donation($_POST['name'], ($_POST['anon'] == 'on' || $_POST['anon'] == 'yes') ? 'yes' : 'no', $_POST['email'], $_POST['user'], (int)($_POST['amount'] * 100), (int)($_POST['realamount'] * 100), $_POST['paypalid']);

psymail_user($_POST['user'], 'psypets', 'payment received!',
  'A payment has been received and the favors credited to this account.<br /><br />' .
  'Check your payments and favor history at the {link http://www.psypets.net/myaccount/favorhistory.php Payments & Favors page}, and remember visit the {link http://www.psypets.net/autofavor.php Favor Dispenser} for the list of favors you can claim!'
);

header('Location: ./admindonations.php');
?>
