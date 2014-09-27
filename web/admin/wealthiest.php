<?php
$IGNORE_MAINTENANCE = true;


require_once 'commons/init.php';

$require_login = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/encryption.php';
require_once 'commons/formatting.php';

if($admin['clairvoyant'] != 'yes')
{
  header('Location: /n404/');
  exit();
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Admin Tools &gt; Wealthiest 10</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/admin/tools.php">Administrative Tools</a> &gt; Wealthiest 10</h4>
     <ol>
<?php
$wealthiest = array();

$command = "SELECT * FROM monster_users WHERE is_npc='no' AND disabled='no' ORDER BY money+savings DESC LIMIT 10";
$result = mysql_query($command);

while($this_user = mysql_fetch_assoc($result))
{
?>
       <li><p><a href="/userprofile.php?user=<?= link_safe($this_user["display"]) ?>"><?= $this_user["display"] ?></a> has <?= $this_user["money"] ?> moneys on-hand and <?= $this_user["savings"] ?> moneys in the bank.</p></li>
<?php
}

mysql_free_result($result);
?>
     </ol>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
