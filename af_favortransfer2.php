<?php
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/userlib.php';
require_once 'commons/favorlib.php';

if($_POST['action'] == 'transfer' && $user['favor'] >= 50)
{
  $target_user = get_user_bydisplay($_POST['residentname']);
  $amount = (int)$_POST['favors'];

  if($target_user['display'] != $_POST['residentname'] || (int)$target_user['idnum'] == 0)
    $dialog = '<p>Could not find a resident by that name.</p>';
  else if($_POST['residentname'] == $user['display'])
    $dialog = '<p>You can\'t give <em>yourself</em> Favor...</p>';
  else if($amount <= 0 || $amount % 50 != 0)
    $dialog = '<p>The amount of Favor to transfer must be a multiple of 50.  (50, 100, 150, 200, etc.)</p>';
  else if($amount > $user['favor'])
    $dialog = '<p>You do not have that much Favor!</p>';
  else
  {
    $now = time();

    spend_favor($user, $amount, 'favor transfer to ' . $target_user['display'], $target_user['idnum']);
    credit_favor($target_user, $amount, 'favor transfer from ' . $user['display'], $user['idnum']);

    $dialog = '<p>' . $amount . ' Favor was transferred successfully.</p>';

    $mailmessage = "{r " . $user['display'] . "} has transferred $amount Favor to you.  You may spend Favor to revive a dead pet, " .
                   "get a custom item, or any of many other things.  Check out the " . 
                   "{link http://www.psypets.net/autofavor.php Favor Dispenser} for a list of options, " .
                   "and don't forget to thank {r " . $user['display'] . "}!";

    psymail_user($target_user['user'], 'lpawlak', 'Favor transfer!', $mailmessage);

    $_POST = array();
  }
}

include 'commons/html.php';
?>
 <head>
  <title>PsyPets &gt; Favor Dispenser &gt; Transfer Favor</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="bank.php">The Bank</a> &gt; Transfer Favor</h4>
     <ul class="tabbed">
      <li><a href="/bank.php">The Bank</a></li>
      <li><a href="/bank_groupcurrencies.php">Group Currencies</a></li>
      <li><a href="/bank_exchange.php">Exchanges</a></li>
      <li><a href="/ltc.php">License to Commerce</a></li>
      <li><a href="/allowance.php">Allowance Preference</a></li>
      <li><a href="/af_favortickets.php">Get Favor Tickets</a></li>
      <li class="activetab"><a href="/af_favortransfer2.php">Transfer Favor</a></li>
<?= $st_patricks ? '<li class="stpatrick"><a href="/stpatricks.php?where=bank">St. Patrick\'s Day Competition</a></li>' : '' ?>
     </ul>
<?php
// BANKER LAKISHA
echo '<a href="/npcprofile.php?npc=Lakisha+Pawlak"><img src="//saffron.psypets.net/gfx/npcs/banker_lakisha.png" align="right" width="350" height="427" alt="(Lakisha the Banker)" /></a>';

include 'commons/dialog_open.php';

if($dialog != '')
  echo $dialog;
else
{
?>
     <p>If you'd like to give Favor to another Resident, I can transfer it to their account directly for you.</p>
     <p>The amount of Favor to transfer must be a multiple of 50.  (50, 100, 150, 200, etc.)</p>
     <p>If you'd like to <em>trade</em> Favor with another Resident, I recommend using Favor Tickets, <a href="af_favortickets.php">which I can also get for you</a>.</p>
<?php
}

include 'commons/dialog_close.php';
?>
     <p>You currently have <?= $user['favor'] ?> Favor.</p>
     <ul>
      <li><a href="/buyfavors.php">Support PsyPets; get Favor</a></li>
     </ul>
<?php
if($user['favor'] >= 50)
{
?>
     <form action="af_favortransfer2.php" method="post">
     <h5>Amount of Favor</h5>
     <p><input name="favors" maxlength="5" size="5" /> Favor<p>
     <h5>Resident to Receive Favor</h5>
     <p><input name="residentname" maxlength="24" value="<?= $_POST['residentname'] ?>" />&nbsp;<span class="size13">&larr;</span>&nbsp;<select name="buddylist" style="width:200px;" onchange="residentname.value=buddylist.value;">
       <option value=""></option>
<?php
if(strlen($user['friends']) > 0)
{
  $friend_list = explode(',', $user['friends']);

  foreach($friend_list as $idnum)
  {
    $friend = get_user_byid($idnum, 'display');
    if($friend !== false)
      $names[] = $friend['display'];
  }

  sort($names);

  foreach($names as $name)
    echo '<option value="' . $name . '">' . $name . '</option>';
}
?>
      </select>
     </p>
     <p><input type="hidden" name="action" value="transfer" /><input type="submit" value="Transfer" /></p>
     </form>
<?php
}
else
  echo '<p>The minimum amount of Favor which you may transfer is 50.</p>';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
