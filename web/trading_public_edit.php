<?php
$wiki = 'Trading_House';
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/messages.php';
require_once 'commons/userlib.php';
require_once 'commons/publictradinglib.php';
require_once 'commons/psypetsformatting.php';

if($NO_PVP)
{
  header('Location: /lostdata.php');
  exit();
}

if($user['license'] == 'no')
{
  header('Location: /ltc.php?dialog=2');
  exit();
}

$tradeid = (int)$_GET['id'];

$command = 'SELECT * FROM psypets_trading_house_requests WHERE idnum=' . $tradeid . ' LIMIT 1';
$trade = $database->FetchSingle($command, 'trading_public.php');

if($trade === false)
{
  header('Location: /trading_public2.php');
  exit();
}

if($trade['userid'] != $user['idnum'])
{
  header('Location: ./trading_public_view.php?id=' . $tradeid);
  exit();
}

if($_POST['action'] == 'Post')
{
  $sdesc = trim($_POST['sdesc']);
  $ldesc = trim($_POST['ldesc']);
  
  if($sdesc == '')
    $message_list[] = '<span class="failure">You need to tell us what you\'re asking for!</span>';
  else
  {
    update_trade_description($tradeid, $sdesc, $ldesc);
    
    header('Location: ./trading_public_view.php?id=' . $tradeid);
    exit();
  }

  $ldesc = psypets_unformat_text($ldesc);
}
else
{
  $sdesc = $trade['sdesc'];
  $ldesc = psypets_unformat_text($trade['ldesc']);
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Trading House &gt; <?= $trade['sdesc'] ?> &gt; Edit</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="trading_public2.php">Trading House</a> &gt; <a href="trading_public_view.php?id=<?= $tradeid ?>"><?= $trade['sdesc'] ?></a> &gt; Edit</h4>
     <form action="trading_public_edit.php?id=<?= $tradeid ?>" method="post">
     <h5>Asking</h5>
     <p>A short description stating what you want for trade.  Trading House searches will search this description.</p>
     <p><input type="text" name="sdesc" maxlength="40" style="width:500px;" value="<?= $sdesc ?>" /></p>
     <h5>Long Description (optional)</h5>
     <p>For additional conditions, or anything else you want to say.  Trading House searches do not search this description.</p>
     <p><textarea cols="80" rows="6" style="width:500px;" name="ldesc"><?= $ldesc ?></textarea></p>
     <p><input type="submit" name="action" value="Post" /></p>
     </form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
