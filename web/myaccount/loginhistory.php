<?php
require_once 'commons/init.php';

$require_petload = "no";

// confirm the session...
require_once "commons/dbconnect.php";
require_once "commons/rpgfunctions.php";
require_once "commons/sessions.php";
require_once "commons/formatting.php";

if(array_key_exists('as', $_GET) && $user['admin']['manageaccounts'] == 'yes')
  $useras = (int)$_GET['as'];
else
  $useras = (int)$user['idnum'];

$perpage = 20;

$count_info = $database->FetchSingle('SELECT COUNT(*) AS qty FROM monster_loginhistory WHERE userid=' . $useras);
$count = $count_info['qty'];

$max_pages = (int)(($count - 1) / $perpage) + 1;

if(is_numeric($_GET["page"]) && (int)$_GET["page"] == $_GET["page"] && $_GET["page"] > 0 && $_GET["page"] <= $max_pages)
  $page = (int)$_GET["page"];
else
  $page = 1;

$start = ($page - 1) * $perpage;

$command = 'SELECT * FROM monster_loginhistory WHERE userid=' . $useras . " ORDER BY timestamp DESC LIMIT $start,$perpage";
$logins = fetch_multiple($command, 'fetching login history');

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; My Account &gt; Login History</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/myaccount/">My Account</a> &gt; Login History</h4>
     <p>IP addresses link to <a href="http://ws.arin.net/">arin.net</a>'s <i>whois</i> database search.</p>
<?php
$page_list = paginate($max_pages, $page, '/myaccount/loginhistory.php?page=%s');
?>
     <?= $page_list ?>
     <table>
      <tr class="titlerow">
       <th>Timestamp</th>
       <th>IP&nbsp;Address</th>
      </tr>
<?php
$rowclass = begin_row_class();

foreach($logins as $login)
{
?>
      <tr class="<?= $rowclass ?>">
       <td><?= local_time($login["timestamp"], $user["timezone"], $user["daylightsavings"]) ?></td>
       <td><a href="http://ws.arin.net/cgi-bin/whois.pl?queryinput=<?= $login['ipaddress'] ?>"><?= $login['ipaddress'] ?></td>
<?php
  $rowclass = alt_row_class($rowclass);
}
?>
     </table>
     <?= $page_list ?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
