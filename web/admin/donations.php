<?php
$IGNORE_MAINTENANCE = true;


require_once 'commons/init.php';

$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';

if($admin['managedonations'] != 'yes')
{
  header('Location: /');
  exit();
}

$v = (int)$_GET['view'];

if($_GET['view'] == 4)
  $view = 'ever';
else if($_GET['view'] == 3)
  $view = 'year';
else if($_GET['view'] == 2)
  $view = 'month';
else
{
  $view = 'today';
  $v = 1;
}

$offset = (int)$_GET['offset'];

$command = 'SELECT COUNT(idnum) AS c FROM psypets_payment_records';
$data = $database->FetchSingle($command, 'fetching payment count');

$num_entries = $data['c'];
$num_pages = ceil($data['c'] / 50);

$page = (int)$_GET['page'];

if($page < 1 || $page > $num_pages)
  $page = 1;

$command = 'SELECT * FROM psypets_payment_records ORDER BY idnum DESC LIMIT ' . (($page - 1) * 50) . ',50';
$payments = $database->FetchMultiple($command, 'fetching payments');

$page_list = paginate($page, $num_pages, 'admindonations.php?page=%s');

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Administrative Tools &gt; Payments Management</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/admin/tools.php">Administrative Tools</a> &gt; Payments Management</h4>
     <ul>
      <li><a href="/admin/newdonate.php">Record new payment</a></li>
      <li><a href="/admin/resident.php">Resident Lookup &amp; Tools</a></li>
     </ul>
<?php
 if($error_message)
   echo "<p style=\"color:red;\">" . $error_message . "</p>\n";
?>
     <?= $page_list ?>
     <table>
      <tr class="titlerow">
       <th>Time</th>
       <th>PayPal ID</th>
       <th>Anon</th>
       <th>Full Name</th>
       <th>E-mail</th>
       <th></th>
       <th>Resident</th>
       <th>Amount</th>
       <th></th>
      </tr>
<?php
$bgcolor = begin_row_class();

$total = 0;

foreach($payments as $payment)
{
  if($payment['timestamp'] == 0)
    $timestamp = '<i>unknown</i>';
  else
    $timestamp = local_time($payment['timestamp'], $user['timezone'], $user['daylightsavings']);

  $user = get_user_byid($payment['userid'], 'display,user');
?>
      <tr class="<?= $bgcolor ?>">
       <td valign="top"><nobr><?= $timestamp ?></nobr></td>
       <td valign="top"><a href="https://www.paypal.com/us/vst/id=<?= $payment['paypalid'] ?>"><?= $payment['paypalid'] ?></a></td>
       <td valign="top" align="center"><?= $payment['anonymous'] ?></td>
       <td valign="top"><nobr><?= $payment['name'] ?></nobr></td>
       <td valign="top"><a href="mailto:<?= $payment['email'] ?>"><?= $payment['email'] ?></a></td>
       <td valign="top"><a href="/admin/resident.php?user=<?= $user['user'] ?>"><img src="/gfx/search.gif" border="0" /></a></td>
       <td valign="top"><?= $user['display'] . ' (' . $user['user'] . ')' ?></td>
       <td valign="top" align="right">$<?= ($payment['amount'] - $payment['fee']) / 100 ?></td>
       <td valign="top"><nobr>($<?= $payment['amount'] / 100 ?> payment)</nobr></td>
      </tr>
<?php

  $bgcolor = alt_row_class($bgcolor);
}
?>
     </table>
     <?= $page_list ?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
