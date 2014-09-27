<?php
$require_login = 'no';
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/doevent.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/parklib.php';

$errors = array();

$step = 1;

if($_POST['action'] == 'confirm')
{
  $buyfor = get_user_bydisplay($_POST['resident']);
  if($buyfor === false)
    $errors[] = 'Could not find a resident by the name of "' . $_POST['resident'] . '".';

  $favors = (int)$_POST['favors'];
  
  if($favors < 1 || floor($favors) != $favors)
    $errors[] = 'Please enter a natural number of favors (a whole amount greater than zero).';

  $anonymous = ($_POST['anonymous'] == 'on' || $_POST['anonymous'] == 'yes') ? 'yes' : 'no';

  if(count($errors) == 0)
  {
    $command = 'INSERT INTO psypets_paypalipn (timestamp, initiatedby, user, amount, anonymous) VALUES ' .
      '(' . $now . ', ' . quote_smart($user['user']) . ', ' . quote_smart($buyfor['user']) . ', ' . ($favors * 5) . ', \'' . $anonymous . '\')';
    $database->FetchNone($command, 'adding IPN record');
    
    $id = $database->InsertID();
    
    $step = 2;
  }
}

include 'commons/html.php';
?>
 <head>
  <title>PsyPets &gt; Support PsyPets</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php
include 'commons/header_2.php';

if($step == 1)
{
?>
     <h4>Support PsyPets &gt; allopass</h4>
     <p>Some players requested a way to use their cellphones to make "mobile payments"; allopass provides us the means to make this possible.</p>
     <p>Favor can be spent to <a href="af_combinationstation3.php">make your own items</a>, <a href="af_revive2.php">resurrect dead pets</a>, <a href="af_getrare2.php">claim unique items</a>, and more.  Check out the <a href="autofavor.php">Favor Dispenser</a> for a complete list.  (And if there's something else you'd like to do, let me know, and I'll see about adding it to the list!)</p>
     <ul><li><a href="wherethemoneygoes.php">If you have not already done so, please read the "What is "Favor"?" page.</a></li></ul>
<p>allopass does not allow transactions smaller than $4 USD.</p>
<table>
 <thead>
  <tr><th>Amount</th><th>Favor</th><th></th></tr>
 </thead>
 <tbody>
  <tr>
   <td>$4 USD</td>
   <td>400 Favor</td>
   <td>
<!-- Begin Allopass Checkout-Button Code -->
<script type="text/javascript" src="https://payment.allopass.com/buy/checkout.apu?ids=247066&idd=973035&lang=en"></script>
<noscript>
 <a href="https://payment.allopass.com/buy/buy.apu?ids=247066&idd=973035" style="border:0">
  <img src="https://payment.allopass.com/static/buy/button/en/162x56.png" style="border:0" alt="Buy now!" />
 </a>
</noscript>
<!-- End Allopass Checkout-Button Code -->
   </td>
  </tr>
  <tr>
   <td>$5 USD</td>
   <td>500 Favor</td>
   <td><a href="https://payment.allopass.com/buy/buy.apu?ids=247066&idd=972451&country=usa&lang=en">Pay with allopass</a></td>
  </tr>
 </tbody>
</table>
<?php
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
