<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/favorlib.php';

if($this_inventory['itemname'] == '500 Favor Ticket')
  $favor = 500;
else if($this_inventory['itemname'] == '400 Favor Ticket')
  $favor = 400;
else if($this_inventory['itemname'] == '300 Favor Ticket')
  $favor = 300;
else if($this_inventory['itemname'] == '200 Favor Ticket')
  $favor = 200;
else if($this_inventory['itemname'] == '100 Favor Ticket')
  $favor = 100;
else if($this_inventory['itemname'] == '50 Favor Ticket')
  $favor = 50;
else
  die('This item is broken!  (You should probably let an administrator know...)');

if($_GET['step'] == 2)
{
  credit_favor($user, $favor, 'redeemed a ' . $this_inventory['itemname'], $this_inventory['idnum']);

  delete_inventory_byid($this_inventory['idnum']);

  echo '<p>You gained ' . $favor . ' Favor!  Visit the <a href="autofavor.php">Favor Dispenser</a> to check out what you can do with it.</p>';

  $AGAIN_WITH_ANOTHER = true;
}
else
{
  echo '
    <p>Using this Ticket will credit your account with ' . $favor . ' Favor.</p>
    <ul>
     <li><a href="itemaction.php?idnum=' . $this_inventory['idnum'] . '&step=2">Use it!</a></li>
     <li><a href="wherethemoneygoes.php">What Is "Favor"?</a></li>
    </ul>
  ';
}
?>