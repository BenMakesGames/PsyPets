<?php
$require_login = "no";

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';

$primary_server_needed = 95;
$image_server_needed = 40;
$broadcasting_server_needed = 40;

$start_of_month = mktime(0, 0, 0, date('n'), 0);

$command = 'SELECT SUM(amount)-SUM(fee) AS money FROM psypets_payment_records WHERE timestamp>=' . $start_of_month;
$data = $database->FetchSingle($command, 'fetching money');

$money = $data['money'] / 100;
$total = $money;

$total_needed = $primary_server_needed + $image_server_needed + $broadcasting_server_needed;

$primary_server = min($money, $primary_server_needed);
$money -= $primary_server;

$image_server = min($money, $image_server_needed);
$money -= $image_server;

$broadcasting_server = min($money, $broadcasting_server_needed);
$money -= $broadcasting_server;

$savings = $money;

include 'commons/html.php';
?>
 <head>
  <title>PsyPets &gt; Help Desk &gt; What Is "Favor"?</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/help/">Help Desk</a> &gt; What Is "Favor"?</h4>
     <p>Long ago I had the strange experience of someone wanting, for the first time, to simply give me money because they thought PsyPets was awesome.  Even though they did so because they liked PsyPets, I felt bad just accepting the money, so I offered to make a custom item for the person as way of thanks.  Thus began a tradition of creating custom content for PsyPets in exchange for money.</p>
     <p>Since then, PsyPets has grown, and I've become unable to personally respond to every request.  The <a href="autofavor.php">Favor Dispenser</a>, and Favor system as a whole, were therefore developed, to allow me to handle requests in a timely fashion.</p>
     <p>Despite the less personal feel brought on by this systemization, I hope people think of the <a href="autofavor.php">Favor Dispenser</a> not as a store, but as my way of saying "thanks" for your saying "thanks" for my making PsyPets! (If that makes any sense at all :P)  I love knowing that people are enjoying something I created, but if you feel compelled to further support the game, with your hard-earned money, first of all: thank you!  But also: please take advantage of the Favor system, and if you have a special request you'd like to make in exchange for your support, <a href="userprofile.php?user=That+Guy+Ben">feel free to ask</a>.</p>
     <p>&mdash; "That Guy" Ben Hendel-Doying</p>
     <h4>The Nitty-Gritty</h4>
     <ul class="spacedlist">
      <li>Favor is an in-game currency used to buy special services and items, including pet resurrections, the creation of custom items, and more.</li>
      <li>Favor can be spent at the <a href="autofavor.php">Favor Dispenser</a>, or at other player's shops.</li>
      <li>Favor may be purchased in blocks of 500.  500 Favor costs $5 (that's US dollars).</li>
      <li>Payments are accepted <em>only</em> through PayPal.  This is to ensure that the money safely gets from you to me, and that both sides receive a receipt.</li>
      <li>If you choose to pay me through PayPal directly (rather than using the in-game form to buy Favor), be sure to include the name of the Resident to credit with the payment!</li>
      <li>The credited Resident will receive an in-game mail notifying them of the payment made in their name.  That person may also review their Favor History to see when they received credit for the payment, who made it, and their balance.</li>
      <li>Once the account has received its purchased Favor, there are no refunds!</p></li>
      <li>The way Favor can be spent in-game may change!  I reserve the right to add new things to buy, remove old things, or reprice existing things at any time.</li>
      <li><strong>Making fraudulent payments will get your account(s) banned!</strong>  This should be obvious.  PayPal also takes fraud very seriously.  Do not do it.</p></li>
     </ul>
     <p>If you have any other questions about making payments, feel free to contact me <a href="userprofile.php?user=That+Guy+Ben">in-game</a> or by <a href="contactme.php">e-mail</a>.</p>
     <ul>
      <li><a href="/buyfavors.php">Support PsyPets; get Favor</a></li>
     </ul>
     <h4>Where The Money Goes</h4>
     <p>For the curious: total <?= date('F \'y') ?> contributions, and PsyPets' expenses.</p>
     <table>
      <tr class="titlerow">
       <th>Item</th>
       <th class="centered">Needed</th>
       <th class="centered">Received</th>
       <th class="centered">Progress Bar!</th>
      </tr>
      <tr>
       <td>Main Server</td>
       <td align="right"><?= $primary_server_needed ?>.00 USD</td>
       <td align="right"><?= number_format($primary_server, 2) ?> USD</td>
       <td><img src="gfx/red_shim.gif" height="10" width="<?= round($primary_server * 100 / $primary_server_needed) ?>" title="<?= round($primary_server * 100 / $primary_server_needed) ?>%" /></td>
      </tr>
      <tr class="altrow">
       <td>Image Server</td>
       <td align="right"><?= $image_server_needed ?>.00 USD</td>
       <td align="right"><?= number_format($image_server, 2) ?> USD</td>
       <td><img src="gfx/red_shim.gif" height="10" width="<?= round($image_server * 100 / $image_server_needed) ?>" title="<?= round($image_server * 100 / $image_server_needed) ?>%" /></td>
      </tr>
      <tr>
       <td>Broadcasting Server</td>
       <td align="right"><?= $broadcasting_server_needed ?>.00 USD</td>
       <td align="right"><?= number_format($broadcasting_server, 2) ?> USD</td>
       <td><img src="gfx/red_shim.gif" height="10" width="<?= round($broadcasting_server * 100 / $broadcasting_server_needed) ?>" title="<?= round($broadcasting_server * 100 / $broadcasting_server_needed) ?>%" /></td>
      </tr>
      <tr class="altrow">
       <td>Extra</td>
       <td align="right">--</td>
       <td align="right"><?= number_format($savings, 2) ?> USD</td>
       <td></td>
      </tr>
      <tr>
       <th>Total</th>
       <td align="right"><?= number_format($total_needed, 2) ?> USD</td>
       <td align="right"><?= number_format($total, 2) ?> USD</td>
       <td><img src="gfx/shim.gif" height="1" width="100" alt="" /></td>
      </tr>
     </table>
     <p>Not receiving enough money doesn't mean PsyPets gets shut down!  It just means I pay for the costs myself :)</p>
     <h4>Thanks!</h4>
     <p>Your contributions mean a lot!</p>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
