<?php
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/itemlib.php';

$projectid = (int)$_GET['id'];

$command = 'SELECT type,projectid FROM monster_projects WHERE idnum=' . $projectid . ' AND userid=' . $user['idnum'] . ' LIMIT 1';
$project = $database->FetchSingle($command, 'fetching project');

if($project === false)
{
  header('Location: /myhouse.php');
  exit();
}

if($project['type'] == 'craft')
  $project_details = get_craft_byid($project['projectid']);
else if($project['type'] == 'engineer')
  $project_details = get_invention_byid($project['projectid']);
else if($project['type'] == 'mechanical')
  $project_details = get_mechanics_byid($project['projectid']);
else if($project['type'] == 'chemistry')
  $project_details = get_chemistry_byid($project['projectid']);
else if($project['type'] == 'smith')
  $project_details = get_smith_byid($project['projectid']);
else if($project['type'] == 'tailor')
  $project_details = get_tailor_byid($project['projectid']);
else if($project['type'] == 'leatherwork')
  $project_details = get_leatherworking_byid($project['projectid']);
else if($project['type'] == 'paint')
  $project_details = get_painting_byid($project['projectid']);
else if($project['type'] == 'jewel')
  $project_details = get_jewelry_byid($project['projectid']);
else if($project['type'] == 'carpenter')
  $project_details = get_carpentry_byid($project['projectid']);
else if($project['type'] == 'sculpture')
  $project_details = get_sculpture_byid($project['projectid']);
else if($project['type'] == 'binding')
  $project_details = get_binding_byid($project['projectid']);
else if($project['type'] == 'gardening')
  $project_details = get_gardening_byid($project['projectid']);
else
  $project_details = false;

if($project_details === false)
{
  header('Location: /myhouse.php');
  exit();
}

$items = explode(',', $project_details['ingredients']);
$got = array();

$command = 'DELETE FROM monster_projects WHERE idnum=' . $projectid . ' LIMIT 1';
$database->FetchNone($command, 'deleting project');

if($database->AffectedRows() == 0)
{
  header('Location: /myhouse.php');
  exit();
}

foreach($items as $item)
{
  if(mt_rand(1, 100) >= 60)
  {
    $got[] = $item;
    add_inventory($user['user'], '', $item, 'Recovered from a pet project', 'home');
  }
}

if(count($got) == 0)
  $msg = '147';
else
  $msg = '148:' . urlencode(implode('&#44; ', $got));

require_once 'commons/statlib.php';
record_stat($user['idnum'], 'Canceled a Project At Home', 1);

header('Location: /myhouse.php?msg=' . $msg);
?>
