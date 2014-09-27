<?php
require_once 'commons/init.php';

$require_petload = 'no';
$IGNORE_MAINTENANCE = true;


// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/formatting.php';
require_once 'commons/utility.php';

if($admin['clairvoyant'] != 'yes')
{
  header('Location: /admin/tools.php');
  exit();
}

if($_POST['action'] == 'Clear' && $admin['manageitems'] == 'yes')
{
  $command = 'TRUNCATE TABLE psypets_pawned_for';
  $database->FetchNone($command, 'emptying most pawned-for table');
}

$command = 'SELECT itemname,quantity AS qty FROM psypets_pawned_for ORDER BY quantity DESC';
$pawned = $database->FetchMultiple($command, 'fetching most-pawned items');

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Administrative Tools &gt; Most-wanted Items</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/admin/tools.php">Administrative Tools</a> &gt; Most-wanted Items</h4>
<ul class="tabbed">
 <li><a href="/admin/itemthrowaways.php">Top Throw-away Items</a></li>
 <li class="activetab"><a href="/admin/wanteditems.php">Most-wanted Items</a></li>
 <li><a href="/admin/itemvotes.php">Item Votes</a></li>
</ul>
<?php
if(count($voted_on) > 0)
{
?>
<h5>Residents Voting For "<?= $itemname ?>"</h5>
<table>
 <thead><tr class="titlerow"><th>idnum</th><th>Last IP</th><th>E-mail</th><th>Login name</th><th>Resident name</th></tr></thead>
 <tbody>
<?php
  foreach($voted_on as $resident)
    echo '
      <tr>
       <td>' . $resident['idnum'] . '</td>
       <td>' . $resident['last_ip_address'] . '</td>
       <td>' . $resident['email'] . '</td>
       <td>' . $resident['user'] . '</td>
       <td>' . $resident['display'] . '</td>
      </tr>
    ';
?>
 </tbody>
</table>
<?php
}

if($admin['manageitems'] == 'yes')
  echo '<form method="post" onsubmit="return confirm(\'Really?  Really-really?\');"><p><input type="submit" name="action" value="Clear" /></p></form>';
?>
     <h5>Most Pawned-for Items</h5>
     <div style="width:300px; height:200px; overflow: auto; margin-bottom: 1em;">
      <table>
       <thead><tr class="titlerow"><th>Itemname</th><th>Quantity</th></tr></thead>
       <tbody>
<?php
foreach($pawned as $item)
  echo '<tr><td>' . $item['itemname'] . '</td><td class="centered">' . $item['qty'] . '</td></tr>';
?>
       </tbody>
      </table>
     </div>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
