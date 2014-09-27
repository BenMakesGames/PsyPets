<?php
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/userlib.php';
require_once 'commons/itemlib.php';
require_once 'commons/favorlib.php';

$increments = array(50, 100, 200, 300, 400, 500);

if($_POST['action'] == 'Get Tickets')
{
  foreach($increments as $amount)
  {
    $quantity = (int)$_POST['ticket' . $amount];
  
    if($quantity > 0)
    {
      $tickets[$amount] = $quantity;
      $total_favor += $amount * $quantity;
    }
  }

  if($total_favor == 0)
    $dialog = '<p>You did not select any Favor Tickets to buy...</p>';
  else if($total_favor > $user['favor'])
    $dialog = '<p>That would cost ' . $total_favor . ' Favor, but you only have ' . $user['favor'] . '!</p>';
  else
  {
    $dialog = '<p>Great!  You bought the following tickets:</p><ul>';

    foreach($tickets as $size=>$quantity)
    {
      for($x = 0; $x < $quantity; ++$x)
      {
        $itemname = $size . ' Favor Ticket';

        $id = add_inventory($user['user'], '', $itemname, 'Purchased from the Favor Ticket Shop', 'storage/incoming');

        spend_favor($user, $size, 'bought item - ' . $itemname, $id);
      }

      $dialog .= '<li>' . $size . ' Favor Ticket &times; ' . $quantity . '</li>';

      flag_new_incoming_items($user['user']);
    }

    $dialog .= '
      </ul>
      <p>You\'ll find everything in your <a href="/incoming.php">Incoming</a>.</p>
      <p>Records for this transaction have been added to your <a href="/myaccount/favorhistory.php">Favor History</a>.</p>
    ';
  }
}

include 'commons/html.php';
?>
 <head>
  <title>PsyPets &gt; The Bank &gt; Get Favor Tickets</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/bank.php">The Bank</a> &gt; Get Favor Tickets</h4>
     <ul class="tabbed">
      <li><a href="/bank.php">The Bank</a></li>
      <li><a href="/bank_groupcurrencies.php">Group Currencies</a></li>
      <li><a href="/bank_exchange.php">Exchanges</a></li>
      <li><a href="/ltc.php">License to Commerce</a></li>
      <li><a href="/allowance.php">Allowance Preference</a></li>
      <li class="activetab"><a href="/af_favortickets.php">Get Favor Tickets</a></li>
      <li><a href="/af_favortransfer2.php">Transfer Favor</a></li>
<?= $st_patricks ? '<li class="stpatrick"><a href="/stpatricks.php?where=bank">St. Patrick\'s Day Competition</a></li>' : '' ?>
     </ul>
<?php
// BANKER LAKISHA
echo '<a href="npcprofile.php?npc=Lakisha+Pawlak"><img src="//saffron.psypets.net/gfx/npcs/banker_lakisha.png" align="right" width="350" height="427" alt="(Lakisha the Banker)" /></a>';

include 'commons/dialog_open.php';

if($dialog != '')
  echo $dialog;
else
{
?>
  <p>You can turn your Favor into Favor Tickets for easy exchange with other players.</p>
  <p>For example, a 500 Favor Ticket costs 500 Favor, and when used, credits its user with 500 Favor.  So if you want to trade 500 Favor with someone else, trade a 500 Favor Ticket!</p>
  <p>If you just want to send someone Favor, for example as a gift, I can also <a href="af_favortransfer2.php">transfer Favor directly, without using items</a>.</p>
<?php
}

include 'commons/dialog_close.php';
?>
     <p>You currently have <?= $user['favor'] ?> Favor.</p>
     <ul>
      <li><a href="/buyfavors.php">Support PsyPets; get Favor</a></li>
     </ul>
     <form action="af_favortickets.php" method="post">
     <table>
      <tr class="titlerow">
       <th>Quantity</th>
       <th></th>
       <th>Item</th>
      </tr>
<?php
$rowclass = begin_row_class();

foreach($increments as $size)
{
?>
      <tr class="<?= $rowclass ?>">
       <td><input type="text" name="ticket<?= $size ?>" size="2" maxlength="2" /> &times;</td>
       <td><img src="//saffron.psypets.net/gfx/items/ticket/<?= $size ?>.png" height="32" /></td>
       <td><?= $size ?> Favor Ticket</td>
      </tr>
<?php
  $rowclass = alt_row_class($rowclass);
}
?>
     </table>
     <p><?php
if($user['favor'] >= 0)
  echo '<input type="submit" name="action" value="Get Tickets" />';
else
  echo '<input type="submit" value="Get Tickets" disabled />';
?></p>
     </form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
