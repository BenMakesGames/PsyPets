<?php
// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/encryption.php';
require_once 'commons/formatting.php';
require_once 'commons/petactivitystats.php';

if($user['breeder'] != 'yes')
{
  header('Location: ./breederslicense.php?dialog=2');
  exit();
}

$saleid = (int)$_GET['id'];

$command = 'SELECT petid FROM psypets_pet_market WHERE idnum=' . $saleid . ' AND expiration>' . $now . ' LIMIT 1';
$sale = $database->FetchSingle($command, 'fetching petid from pet market');

if($sale === false)
{
  header('Location: ./petmarket.php');
  exit();
}

$petid = $sale['petid'];

$pet = get_pet_byid($petid);

$owner = get_user_byuser($pet['user'], 'idnum,display');

$num_logs = get_logged_events_count_byuser_bypet($owner['idnum'], $petid);

$maxpages = ceil($num_logs / 20);

$show_logs = array_key_exists('page', $_GET);

$page = (int)$_GET['page'];

if($page < 1 || $page > $maxpages)
  $page = 1;

$logs = get_logged_events_byuser_bypet($owner['idnum'], $petid, $page);

if($show_logs)
{
  $stats_style = ' style="display:none;"';
  $logs_style = '';
}
else
{
  $stats_style = '';
  $logs_style = ' style="display:none;"';
}

include 'commons/html.php';
?>
 <head>
<?php include 'commons/head.php'; ?>
  <title><?= $SETTINGS['site_name'] ?> &gt; Pet Market &gt; Pet Logs &gt; <?= $owner['display'] ?>'s <?= $pet['petname'] ?></title>
  <script type="text/javascript" src="//<?= $SETTINGS['static_domain'] ?>/js/protovis.min.js"></script>
  <script type="text/javascript">
   function ShowStats()
   {
     $('#activity_logs').hide();
     $('#activity_stats').show();
   }

   function ShowLogs()
   {
     $('#activity_stats').hide();
     $('#activity_logs').show();
   }
  </script>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="petmarket.php">Pet Market</a> &gt; Pet Logs &gt; <?= $owner['display'] ?>'s <?= $pet['petname'] ?></h4>
     <ul>
      <li><a href="/residentprofile.php?resident=<?= link_safe($owner['display']) ?>">View <?= $owner['display'] ?>'s profile</a></li>
      <li><a href="/petprofile.php?petid=<?= $petid ?>">View <?= $pet['petname'] ?>'s profile</a></li>
     </ul>
     <div id="activity_stats"<?= $stats_style ?>>
     <ul class="tabbed">
      <li class="activetab"><a>Statistics</a></li>
      <li><a href="#" onclick="ShowLogs(); return false;">Details</a></li>
     </ul>
<?= RenderPetActivityStatsXHTML($petid) ?>
     </div>
     <div id="activity_logs"<?= $logs_style ?>>
     <ul class="tabbed">
      <li><a href="#" onclick="ShowStats(); return false;">Statistics</a></li>
      <li class="activetab"><a>Details</a></li>
     </ul>
<?php
$page_list = paginate($maxpages, $page, 'salepetlogs.php?id=' . $saleid . '&page=%s');

if(count($logs) > 0)
{
?>
     <?= $page_list ?>
     <table>
      <tr class="titlerow">
       <th>Timestamp</th>
       <th>Event</th>
      </tr>
<?php
  $rowclass = begin_row_class();

  foreach($logs as $event)
  {
?>
      <tr class="<?= $rowclass ?>">
       <td><?= local_time($event['timestamp'], $user['timezone'], $user['daylightsavings']) ?></td>
       <td><?= $event['description'] ?></td>
      </tr>
<?php
    $rowclass = alt_row_class($rowclass);
  }
?>
     </table>
     <?= $page_list ?>
<?php
}

echo '<p>There are ' . $num_logs . ' logged activit' . ($num_logs != 1 ? 'ies' : 'y') . ' for this pet.</p>';
?>
</div>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
