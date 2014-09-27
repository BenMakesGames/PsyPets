<?php
// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/encryption.php';
require_once 'commons/formatting.php';
require_once 'commons/petactivitystats.php';

if(count($userpets) == 0)
{
  header('Location: /myhouse.php');
  exit();
}

$petid = (int)$_GET['petid'];

$this_pet = get_pet_byid($petid);

if($this_pet['user'] != $user['user'])
{
  $petid = $userpets[0]['idnum'];
  $this_pet = get_pet_byid($petid);
}
else if($_POST['action'] == 'clearlogs')
{
  clear_logged_events_byuser_bypet($user['idnum'], $this_pet['idnum']);
  $show_logs = true;
}
else if($_POST['action'] == 'Reset' && PetActivityStatsExist($petid))
{
  if($_POST['notepad'] == 'yes' || $_POST['notepad'] == 'on')
  {
    require_once 'commons/notepadlib.php';

    $id = new_note($user['idnum'], '', 'pet log', $this_pet['petname'] . ' - ' . date('M jS, Y'), RenderPetActivityStatsText($petid));

    $notepad_message = '<p class="success">My Notepad note created.  (<a href="/mynotepad_read.php?id=' . $id . '">Read it.</a>)</p>';
  }
  
  DeletePetActivityStats($petid);
}
else if($_POST['action'] == 'Reset All')
{
  $command = 'DELETE FROM psypets_petstats WHERE petid IN (SELECT idnum FROM monster_pets WHERE user=' . quote_smart($user['user']) . ')';
  $database->FetchNone($command, 'resetting pet statistics');
}

$num_logs = get_logged_events_count_byuser_bypet($user['idnum'], $petid);
$newer_log = newer_log_exists_byuser_bypet($user['idnum'], $petid, $this_pet['last_log_check']);

if($newer_log)
{
  $logs = get_new_logged_events_byuser_bypet($user['idnum'], $petid, $this_pet['last_log_check']);

  if(count($logs) > 0)
  {
    if($logs[0]['timestamp'] > $this_pet['last_log_check'])
    {
      $command = 'UPDATE monster_pets SET last_log_check=' . $logs[0]['timestamp'] . ' WHERE idnum=' . $this_pet['idnum'] . ' LIMIT 1';
      $database->FetchNone($command, 'petlogs.php');
    }
  }
}
else
{
  $maxpages = ceil($num_logs / 20);

  $page = (int)$_GET['page'];

  if($page < 1 || $page > $maxpages)
    $page = 1;

  $logs = get_logged_events_byuser_bypet($user['idnum'], $petid, $page);
}

$stats_style = ' style="display:none;"';
$logs_style = '';

$exp_required = level_exp($this_pet['love_level']);

$extra_stats = $database->FetchMultiple('SELECT stat,value,lastupdate FROM psypets_pet_extra_stats WHERE petid=' . $this_pet['idnum'] . ' ORDER BY lastupdate DESC');

include 'commons/html.php';
?>
 <head>
<?php include 'commons/head.php'; ?>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $user['display'] ?>'s House &gt; Pet Logs &gt; <?= $this_pet['petname'] ?></title>
  <script type="text/javascript" src="//<?= $SETTINGS['static_domain'] ?>/js/petnote1.js"></script>
  <script type="text/javascript" src="//<?= $SETTINGS['static_domain'] ?>/js/protovis.min.js"></script>
  <script type="text/javascript">
   function ShowLogs()
   {
     $('#activity_logs').show();
     $('#activity_stats').hide();
     $('#extra_stats').hide();
   }

   function ShowStats()
   {
     $('#activity_logs').hide();
     $('#activity_stats').show();
     $('#extra_stats').hide();
   }

   function ShowExtraStats()
   {
     $('#activity_logs').hide();
     $('#activity_stats').hide();
     $('#extra_stats').show();
   }
  </script>
  <style type="text/css">
   #petstats td { text-align: center; }
  </style>
 </head>
 <body>
<?php
include 'commons/header_2.php';

$owner = $user;

include 'commons/petprofile/pets.php';
?>
     <ul class="tabbed">
      <li><a href="/petprofile.php?petid=<?= $petid ?>">Summary</a></li>
      <li><a href="/petfamilytree.php?petid=<?= $petid ?>">Family Tree</a></li>
<?php
if($user['user'] == $this_pet['user'] || $user['admin']['clairvoyant'] == 'yes')
  echo '<li class="activetab"><a href="/petlogs.php?petid=' . $petid . '">Activity Logs</a></li>';

echo '<li><a href="/petevents.php?petid=' . $petid . '">Park Event Logs</a></li>';

if($user['user'] == $this_pet['user'] || $user['admin']['clairvoyant'] == 'yes')
{
  echo '<li><a href="/petlevelhistory.php?petid=' . $petid . '">Training History</a></li>';

  if($this_pet['love_exp'] >= $exp_required && $this_pet['zombie'] != 'yes')
    echo '<li><a href="/affectionup.php?petid=' . $petid . '" class="success">Affection Reward!</a></li>';
  if($this_pet['ascend'] == 'yes')
    echo '<li><a href="/petascend.php?petid=' . $petid . '" class="success">Reincarnate!</a></li>';
  if($this_pet['free_respec'] == 'yes')
    echo '<li><a href="/petrespec.php?petid=' . $petid . '" class="success">Retrain!</a></li>';
}

echo '</ul>';
?>
<?= $notepad_message ?>
     <div id="activity_stats"<?= $stats_style ?>>
     <ul class="tabbed">
      <li><a href="#" onclick="ShowLogs(); return false;">Activity Logs</a></li>
      <li class="activetab"><a href="#" onclick="return false;">Activity Statistics</a></li>
      <li><a href="#" onclick="ShowExtraStats(); return false;">Other Statistics</a></li>
     </ul>
<?php
if(PetActivityStatsExist($petid))
{
  $extra_xhtml = '
    <form action="?petid=' . $petid . '" method="post">
    <p><input type="checkbox" name="notepad" /> Save to My Notepad <input type="submit" name="action" value="Reset" />';

  if(count($userpets) > 1)
    $extra_xhtml .= ' <input type="submit" name="action" value="Reset All" onclick="return confirm(\'Reset all of your pets\\\'s statistics?  They will not be saved to My Notepad (even if you checked the little checkbox).\');" />';

  $extra_xhtml .= '</p>
    </form>
  ';
}
else
  $extra_xhtml = '';

echo $extra_xhtml . RenderPetActivityStatsXHTML($petid) . $extra_xhtml;

?>
     </div>
     <div id="activity_logs"<?= $logs_style ?>>
     <ul class="tabbed">
      <li class="activetab"><a href="#" onclick="return false;">Activity Logs</a></li>
      <li><a href="#" onclick="ShowStats(); return false;">Activity Statistics</a></li>
      <li><a href="#" onclick="ShowExtraStats(); return false;">Other Statistics</a></li>
     </ul>
     <p>Logged activities older than 1 week are automatically deleted. <i>(<a href="clearallpetlogs.php">Clear all pet logs now</a>.)</i></p>
<?php
if($newer_log)
  echo '<p>These activites are new since the last time you checked ' . $this_pet['petname'] . '\'s logged activities.  <i>(<a href="petlogs.php?petid=' . $this_pet['idnum'] . '&page=1">See complete list</a>.)</i></p>';
else if(count($logs) > 0)
{
  $page_list = paginate($maxpages, $page, '?petid=' . $petid . '&amp;page=%s');
  echo $page_list;
}

if(count($logs) > 0)
{
  echo '<table>';

  $rowclass = begin_row_class();
  $prev_type = '';
  $prev_time = 0;
  $first = true;

  foreach($logs as $event)
  {
    if($event['type'] == 'hourly')
    {
      if($event['timestamp'] != $prev_time)
      {
        $rowclass = begin_row_class();
        echo '
          <tr class="titlerow">
           <th class="centered"><nobr>' . local_time_short($event['timestamp'], $user['timezone'], $user['daylightsavings']) . '</nobr></th>
           <th><nobr>Hourly Activity</nobr></th>
           <th>Needs</th>
          </tr>
        ';
        
        $first = true;
      }

      if($event['hour'] == $previous_hour)
      {
        $divider = false;
        $when = '';
      }
      else
      {
        $divider = !$first;
        $when = 'Hour #' . $event['hour'];
      }
        
      $previous_hour = $event['hour'];
    }
    else if($event['type'] == 'realtime')
    {
      $previous_hour = 0;
      if($prev_type != 'realtime')
      {
        $rowclass = begin_row_class();
        echo '
          <tr class="titlerow">
           <th class="centered">When</th>
           <th>Event</th>
           <th>Needs</th>
          </tr>
        ';
      }

      $when = local_time_short($event['timestamp'], $user['timezone'], $user['daylightsavings']);
    }

    $first = false;
    $needs = array();

    if($event['energy'] < 0)
      $needs[] = '<span class="failure">-Energy</span>';
    else if($event['energy'] > 0)
      $needs[] = '<span class="success">+Energy</span>';

    if($event['food'] < 0)
      $needs[] = '<span class="failure">-Food</span>';
    else if($event['food'] > 0)
      $needs[] = '<span class="success">+Food</span>';

    if($event['safety'] < 0)
      $needs[] = '<span class="failure">-Safety</span>';
    else if($event['safety'] > 0)
      $needs[] = '<span class="success">+Safety</span>';

    if($event['love'] < 0)
      $needs[] = '<span class="failure">-Love</span>';
    else if($event['love'] > 0)
      $needs[] = '<span class="success">+Love</span>';

    if($event['esteem'] < 0)
      $needs[] = '<span class="failure">-Esteem</span>';
    else if($event['esteem'] > 0)
      $needs[] = '<span class="success">+Esteem</span>';
?>
      <tr class="<?= $rowclass ?>"<?= ($divider ? ' style="border-top: 1px solid #999;"' : '') ?>>
       <td class="centered"><nobr><?= $when ?></nobr></td>
       <td><?= $event['description'] ?></td>
       <td><nobr><?= implode('<br />', $needs) ?></nobr></td>
      </tr>
<?php
    $rowclass = alt_row_class($rowclass);
    $prev_type = $event['type'];
    $prev_time = $event['timestamp'];
  }

  echo '</table>';
}
else
  echo 'There are no new logged activities for this pet.</p>';

if(!$newer_log && count($logs) > 0)
  echo $page_list;

if($num_logs == 1)
  echo '<p>There is 1 logged activity for this pet.</p>';
else
  echo '<p>There are ' . $num_logs . ' logged activities for this pet.</p>';

if($num_logs > 0)
  echo '     <form action="/petlogs.php?petid=' . $this_pet['idnum'] . '" method="post"><p><input type="hidden" name="action" value="clearlogs" /><input type="submit" value="Clear Pet\'s Logs" class="bigbutton" /></p></form>';
?>
     </div>
     <div id="extra_stats"<?= $stats_style ?>>
     <ul class="tabbed">
      <li><a href="#" onclick="ShowLogs(); return false;">Activity Logs</a></li>
      <li><a href="#" onclick="ShowStats(); return false;">Activity Statistics</a></li>
      <li class="activetab"><a href="#" onclick="return false;">Other Statistics</a></li>
     </ul>
<?php
if(count($extra_stats) > 0)
{
?>
     <table>
      <thead>
       <tr class="titlerow">
        <th><nobr>Statistic</nobr></th><th class="righted"><nobr>Value</nobr></th><th class="centered"><nobr>Last Update</nobr></th>
       </tr>
      </thead>
      <tbody>
<?php
$rowclass = begin_row_class();

foreach($extra_stats as $stat)
{
  echo '
    <tr class="' . $rowclass . '">
     <td>' . $stat['stat'] . '</td>
     <td class="righted">' . $stat['value'] . '</td>
     <td class="centered">' . ($stat['lastupdate'] == 0 ? '<i class="dim">unknown</i>' : duration($now - $stat['lastupdate'], 2) . ' ago') . '</td>
    </tr>
  ';

  $rowclass = alt_row_class($rowclass);
}
?>
      </tbody>
     </table>
<?php
}
else
  echo '<p>No additional stats have been recorded about ' . $this_pet['petname'] . '... yet!</p>';
?>
     </div>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
