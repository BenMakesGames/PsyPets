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
  echo '<h4>Support PsyPets</h4>';
  echo '<a href="/npcprofile.php?npc=Lakisha+Pawlak"><img src="//saffron.psypets.net/gfx/npcs/banker_lakisha.png" align="right" width="350" height="427" alt="(Lakisha the Banker)" /></a>';

  include 'commons/dialog_open.php';
?>
     <p>PsyPets has grown to what it is today thanks to support from Residents such as yourself!</p>
     <p>Before "Favor" existed as a currency, I (That Guy Ben) hand-coded custom content for players who were kind of enough to send a few dollars.  Due to increased demand, however, a system was needed, and the "Favor" currency was created.</p>
     <p>For every $5 USD, you will receive 500 Favor.  Favor can be spent to <a href="af_combinationstation3.php">make your own items</a>, <a href="af_revive2.php">resurrect dead pets</a>, <a href="af_getrare2.php">claim unique items</a>, and more.  Check out the <a href="autofavor.php">Favor Dispenser</a> for a complete list.</p>
<?php
  include 'commons/dialog_close.php';
?>
     <ul>
      <li><a href="wherethemoneygoes.php">Ask for more information and details about Favor</a></li>
      <li><a href="writemail.php?sendto=That+Guy+Ben">Suggest an addition to the Favor Dispenser</a></li>
     </ul>
<ul class="tabbed">
 <li class="activetab"><a href="buyfavors.php">PayPal</a></li>
</ul>
     <p><i>(At this time, <a href="https://www.paypal.com">PayPal</a> is the only payment method accepted.  There are plans for a mobile payment system, however it's not yet available - sorry!)</i></p>
     <form action="buyfavors.php" method="post">
     <table>
      <tr>
       <th class="leftbar">Resident:</th>
       <td><input name="resident" maxlength="24" value="<?= $user['display'] ?>" /></td>
       <td>The name of the Resident to receive the Favor.</td>
      </tr>
      <tr>
       <th class="leftbar">Anonymous?</th>
       <td><input type="checkbox" name="anonymous" /></td>
       <td>Check this box if you would like to hide your name (as provided by <a href="https://www.paypal.com/" target="_blank">PayPal</a>) from the Resident receiving the Favor.</td>
      </tr>
      <tr>
       <th class="leftbar">Payment:</th>
       <td><input name="favors" maxlength="2" size="2" /> &times; $5 USD</td>
       <td>For every $5 USD you will receive 500 Favor.</td>
      </tr>
     </table>
     <ul><li>For general information and policies regarding Favor, see <a href="wherethemoneygoes.php">What Is "Favor"?</a>  Do not buy Favor if you do not agree to the terms set out there!</li></ul>
     <p><input type="hidden" name="action" value="confirm" /><input type="submit" value="Next &gt;" /></p>
     </form>
<?php
}
else if($step == 2)
{
?>
     <h4><a href="buyfavors.php">Support PsyPets</a> &gt; Confirm</h4>
<?php
  echo '<a href="/npcprofile.php?npc=Lakisha+Pawlak"><img src="//saffron.psypets.net/gfx/npcs/banker_lakisha.png" align="right" width="350" height="427" alt="(Lakisha the Banker)" /></a>';
  include 'commons/dialog_open.php';

  echo '<p>Let\'s just confirm that we got everything right here...</p>';

  include 'commons/dialog_close.php';
?>
<ul class="tabbed">
 <li class="activetab"><a href="buyfavors.php">PayPal</a></li>
</ul>
     <table>
      <tr>
       <th class="leftbar">Resident:</th>
       <td><?= $buyfor['display'] ?></td>
      </tr>
      <tr>
       <th class="leftbar">Anonymous?</th>
       <td><?= $anonymous ?></td>
      </tr>
      <tr>
       <th class="leftbar">Payment:</th>
       <td>$<?= $favors * 5 ?>.00 USD</td>
      </tr>
      <tr>
       <th class="leftbar">Favor:</th>
       <td><?= $favors * 500 ?></td>
      </tr>
     </table>
     <ul><li>For general information and policies regarding Favor, see <a href="/wherethemoneygoes.php">What Is "Favor"?</a>  Do not buy Favor if you do not agree to the terms set out there!</li></ul>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_xclick" />
<input type="hidden" name="business" value="ben@telkoth.net" />
<input type="hidden" name="item_number" value="<?= $id ?>" />
<input type="hidden" name="item_name" value="PsyPets Favors" />
<input type="hidden" name="amount" value="<?= $favors * 5 ?>" />
<input type="hidden" name="currency_code" value="USD" />
<input type="hidden" name="no_shipping" value="2" />
<input type="hidden" name="return" value="<?= $SETTINGS['protocol'] ?>://www.psypets.net/buyfavors_done.php" />
<input type="hidden" name="notify_url" value="<?= $SETTINGS['protocol'] ?>://www.psypets.net/ipncatch.php" />
<input type="hidden" name="no_note" value="1" />
<input type="hidden" name="image_url" value="<?= $SETTINGS['protocol'] ?>://www.psypets.net/gfx/title_tiny.png" />
<p><a href="buyfavors.php"><img src="gfx/paypal_change.png" alt="&gt; Change" border="0" /></a> <input type="image" src="https://www.paypal.com/en_US/i/btn/btn_buynow_SM.gif" border="0" name="submit" alt="Make payments with PayPal - it's fast, free and secure!" /></p>
</form>
<p>P.S. Thankyouthankyouthankyou <img src="gfx/emote/hee.gif" width="16" height="16" alt="[happy!]" class="inlineimage" /></p>
<?php
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
