<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';

if(array_key_exists('idnum', $_GET) && $admin['clairvoyant'] == 'yes')
  $as_user = $_GET['idnum'];
else
  $as_user = $user['idnum'];

$command = 'SELECT * FROM psypets_favor_history WHERE userid=' . $as_user . ' ORDER BY timestamp DESC';
$history = fetch_multiple($command);


$total = 0;
foreach($history as $item)
  $total += $item['value'];

if($total != $user['favor'] && !array_key_exists('idnum', $_GET))
{
  $command = 'UPDATE monster_users SET favor=' . $total . ' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
  fetch_none($command, 'fixing favor total');

  $user['favor'] = $total;

  require_once 'commons/questlib.php';
  
  $favor_mistake = get_quest_value($user['idnum'], 'Favor mistake');

  if($favor_mistake === false)
  {
    require_once 'commons/itemlib.php';

    add_quest_value($user['idnum'], 'Favor mistake', 1);

    add_inventory($user['user'], '', '100 Favor Ticket', 'oh snap!  favor mistake correction!', 'storage/incoming');

    $error_message .= 'Whoa!  It looks like the game was confused about how much Favor you had!  This was probably very confusing for you :|  Sorry about that.</p><p class="failure">It\'s been corrected, but please also accept this 100 Favor Ticket.</p><p class="failure"><i>(Actually, you cannot refuse it :P  It has, in fact, already been placed in your Incoming :P)</i>';
  }
  else
    $error_message .= 'Whoa!  It looks like the game was confused about how much Favor you had!  This was probably very confusing for you :|  Sorry about that.</p><p class="failure">It\'s been corrected.';
}

require 'commons/html.php';
?>
 <head>
  <title>PsyPets &gt; My Account &gt; Favor History</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/myaccount/">My Account</a> &gt; Favor History</h4>
     <ul class="tabbed">
<?php
if($user['childlockout'] == 'no')
{
?>
      <li><a href="/myaccount/profile.php">Resident&nbsp;Profile</a></li>
      <li><a href="/myaccount/searchable.php">Searchable&nbsp;Profile</a></li>
<?php
}
?>
      <li><a href="/myaccount/petprofile.php">Pet&nbsp;Profiles</a></li>
      <li><a href="/myaccount/display.php">Display&nbsp;Settings</a></li>
      <li><a href="/myaccount/behavior.php">Behavior&nbsp;Settings</a></li>
      <li><a href="/myaccount/security.php">Account&nbsp;Management</a></li>
      <li class="activetab"><a href="/myaccount/favorhistory.php">Favor&nbsp;History</a></li>
      <li><a href="/myaccount/contentcontrol.php">Content&nbsp;Control</a></li>
     </ul>
<?php
if($error_message)
  echo "<p style=\"color:red;\">" . $error_message . "</p>\n";
?>
<p>You currently have <?= $user['favor'] ?> Favor.</p>
<table>
 <thead>
  <tr class="titlerow"><th class="centered">When</th><th class="righted">Favor</th><th>Description</th><th>Notes</th></tr>
 </thead>
 <tbody>
<?php
$rowclass = begin_row_class();

foreach($history as $item)
{
  $notes = array();

  if($item['itemid'] > 0)
    $notes[] = 'item id #' . $item['itemid'];
?>
  <tr class="<?= $rowclass ?>">
   <td class="righted"><?= date('M jS, Y', $item['timestamp']) ?></td>
   <td class="righted"><?= $item['value'] > 0 ? '+' . $item['value'] : $item['value'] ?></td>
   <td><?= $item['favor'] ?></td>
   <td><?= implode('<br />', $notes) ?></td>
  </tr>
<?php
  $rowclass = alt_row_class($rowclass);
}
?>
 </tbody>
</table>
<p>Total: <?= $total ?> Favor</p>
<?php include "commons/footer_2.php"; ?>
 </body>
</html>
