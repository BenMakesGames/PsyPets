<?php
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';

$command = 'SELECT idnum,userid,ad,permanent FROM psypets_advertising WHERE permanent=\'yes\' OR expirytime>' . $now;
$broadcasts = $database->FetchMultiple($command, 'fetching currently-running ads');

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Advertising &gt; Running Ads</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="broadcast.php">Advertising</a> &gt; Running Ads</h4>
     <ul class="tabbed">
      <li><a href="broadcast.php">Post Ad</a></li>
      <li class="activetab"><a href="broadcast_ads.php">Running Ads</a></li>
     </ul>
<?php
foreach($broadcasts as $this_ad)
{
  $this_user = get_user_byid($this_ad['userid']);
  $formatted_ad = nl2br(format_text($this_ad['ad']));
?>
     <div id="ingamead" style="float:left;">
      <p><?= $formatted_ad ?></p>
      <p class="centered">(<i>paid for by <a href="userprofile.php?user=<?= link_safe($this_user['display']) ?>"><?= $this_user['display'] ?></a></i>)</p>
     </div>
<?php
}
?>
     <div style="clear:both;"></div>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
