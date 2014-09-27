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
  header('Location: ./myhouse.php');
  exit();
}

$petid = (int)$_GET['petid'];

$pet = get_pet_byid($petid);

if($pet['user'] != $user['user'])
{
  $petid = $userpets[0]['idnum'];
  $pet = get_pet_byid($petid);
}
else if($_POST['action'] == 'clearlogs')
{
  clear_logged_events_byuser_bypet($user['idnum'], $pet['idnum']);
  $show_logs = true;
}
else if($_POST['action'] == 'Reset' && PetActivityStatsExist($petid))
{
  if($_POST['notepad'] == 'yes' || $_POST['notepad'] == 'on')
  {
    require_once 'commons/notepadlib.php';

    $id = new_note($user['idnum'], '', 'pet log', $pet['petname'] . ' - ' . date('M jS, Y'), RenderPetActivityStatsText($petid));

    $notepad_message = '<p class="success">My Notepad note created.  (<a href="mynotepad_read.php?id=' . $id . '">Read it.</a>)</p>';
  }
  
  DeletePetActivityStats($petid);
}
else if($_POST['action'] == 'Reset All')
{
  $command = 'DELETE FROM psypets_petstats WHERE petid IN (SELECT idnum FROM monster_pets WHERE user=' . quote_smart($user['user']) . ')';
  $database->FetchNone($command, 'resetting pet statistics');
}

$num_logs = get_logged_events_count_byuser_bypet($user['idnum'], $petid);
$newer_log = newer_log_exists_byuser_bypet($user['idnum'], $petid, $pet['last_log_check']);

if($newer_log)
{
  $logs = get_new_logged_events_byuser_bypet($user['idnum'], $petid, $pet['last_log_check']);

  if(count($logs) > 0)
  {
    if($logs[0]['timestamp'] > $pet['last_log_check'])
    {
      $command = 'UPDATE monster_pets SET last_log_check=' . $logs[0]['timestamp'] . ' WHERE idnum=' . $pet['idnum'] . ' LIMIT 1';
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

include 'commons/html.php';
?>
 <head>
<?php include 'commons/head.php'; ?>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $user['display'] ?>'s House &gt; Pet Logs &gt; <?= $pet['petname'] ?></title>
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
  <style type="text/css">
   #petstats td { text-align: center; }
  </style>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="myhouse.php"><?= $user['display'] ?>'s House</a> &gt; Pet Logs &gt; <?= $pet['petname'] ?></h4>
     <ul class="tabbed">
<?php
foreach($userpets as $this_pet)
{
  if($this_pet['idnum'] == $pet['idnum'])
    echo '<li class="activetab"><a href="petlogs.php?petid=' . $this_pet['idnum'] . '">' . $this_pet['petname'] . '</a></li>';
  else
    echo '<li><a href="petlogs.php?petid=' . $this_pet['idnum'] . '">' . $this_pet['petname'] . '</a></li>';

  echo "\n";
}
?>
     </ul>
<?= $notepad_message ?>
     <div id="activity_stats"<?= $stats_style ?>>
     <ul class="tabbed">
      <li><a href="#" onclick="ShowLogs(); return false;">Details</a></li>
      <li class="activetab">Statistics</li>
     </ul>
<?php
if(PetActivityStatsExist($petid))
{
  $extra_xhtml = '
    <form action="petlogs.php?petid=' . $petid . '" method="post">
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
      <li class="activetab">Details</li>
      <li><a href="#" onclick="ShowStats(); return false;">Statistics</a></li>
     </ul>
     <p>Logged activities older than 1 week are automatically deleted. <i>(<a href="clearallpetlogs.php">Clear all pet logs now</a>.)</i></p>
<?php

if($newer_log)
  echo '<p>These activites are new since the last time you checked ' . $pet['petname'] . '\'s logged activities.  <i>(<a href="petlogs.php?petid=' . $pet['idnum'] . '&page=1">See complete list</a>.)</i></p>';

$page_list = paginate($maxpages, $page, "petlogs.php?petid=$petid&page=%s");

if(count($logs) > 0)
{
  if(!$newer_log)
    echo $page_list;
?>
     <table>
      <tr class="titlerow">
       <th>When</th>
       <th>Event</th>
       <th class="centered">Needs</th>
      </tr>
<?php
  $rowclass = begin_row_class();

  foreach($logs as $event)
  {
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

    if($event['type'] == 'realtime')
      $when = local_time($event['timestamp'], $user['timezone'], $user['daylightsavings']);
    else
      $when = 'Hour #' . $event['timestamp'];
?>
      <tr class="<?= $rowclass ?>">
       <td><nobr><?= $when ?></nobr></td>
       <td><?= $event['description'] ?></td>
       <td><nobr><?= implode('<br />', $needs) ?></nobr></td>
      </tr>
<?php
    $rowclass = alt_row_class($rowclass);
  }
?>
     </table>
<?php
  if(!$newer_log)
    echo $page_list;
}
else
  echo 'There are no new logged activities for this pet.</p>';

echo '<p>There are ' . $num_logs . ' logged activit' . ($num_logs != 1 ? 'ies' : 'y') . ' for this pet.</p>';
if($num_logs > 0)
  echo '     <form action="petlogs.php?petid=' . $pet['idnum'] . '" method="post"><p><input type="hidden" name="action" value="clearlogs" /><input type="submit" value="Clear Pet\'s Logs" class="bigbutton" /></p></form>';
?>
     </div>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
