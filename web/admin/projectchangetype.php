<?php
$IGNORE_MAINTENANCE = true;


require_once 'commons/init.php';

$require_petload = "no";

// confirm the session...
require_once "commons/dbconnect.php";
require_once "commons/rpgfunctions.php";
require_once "commons/sessions.php";

if($admin['manageitems'] != 'yes')
{
  header('Location: /admin/tools.php');
  exit();
}

$id = (int)$_GET['id'];

// ----  FROM

if($_GET['from'] == 'crafts')
{
  $from_type = 'crafts';
  $project_from_type = 'craft';
}
else if($_GET['from'] == 'inventions')
{
  $from_type = 'inventions';
  $project_from_type = 'engineer';
}
else if($_GET['from'] == 'jewelry')
{
  $from_type = 'jewelry';
  $project_from_type = 'jewel';
}
else if($_GET['from'] == 'smiths')
{
  $from_type = 'smiths';
  $project_from_type = 'smith';
}
else if($_GET['from'] == 'sculptures')
{
  $from_type = 'sculptures';
  $project_from_type = 'sculpture';
}
else if($_GET['from'] == 'carpentry')
{
  $from_type = 'carpentry';
  $project_from_type = 'carpenter';
}
else if($_GET['from'] == 'paintings')
{
  $from_type = 'paintings';
  $project_from_type = 'paint';
}
else if($_GET['from'] == 'bindings')
{
  $from_type = 'bindings';
  $project_from_type = 'binding';
}
else if($_GET['from'] == 'mechanics')
{
  $from_type = 'mechanics';
  $project_from_type = 'mechanical';
}
else if($_GET['from'] == 'chemistry')
{
  $from_type = 'chemistry';
  $project_from_type = 'chemistry';
}
else if($_GET['from'] == 'tailors')
{
  $from_type = 'tailors';
  $project_from_type = 'tailor';
}
else if($_GET['from'] == 'leatherworks')
{
  $from_type = 'leatherworks';
  $project_from_type = 'leatherwork';
}
else
  die('Angkor waaaaat?');

// ----  TO

if($_GET['to'] == 'crafts')
{
  $to_type = 'crafts';
  $project_to_type = 'craft';
}
else if($_GET['to'] == 'inventions')
{
  $to_type = 'inventions';
  $project_to_type = 'engineer';
}
else if($_GET['to'] == 'jewelry')
{
  $to_type = 'jewelry';
  $project_to_type = 'jewel';
}
else if($_GET['to'] == 'smiths')
{
  $to_type = 'smiths';
  $project_to_type = 'smith';
}
else if($_GET['to'] == 'sculptures')
{
  $to_type = 'sculptures';
  $project_to_type = 'sculpture';
}
else if($_GET['to'] == 'carpentry')
{
  $to_type = 'carpentry';
  $project_to_type = 'carpenter';
}
else if($_GET['to'] == 'paintings')
{
  $to_type = 'paintings';
  $project_to_type = 'paint';
}
else if($_GET['to'] == 'bindings')
{
  $to_type = 'bindings';
  $project_to_type = 'binding';
}
else if($_GET['to'] == 'mechanics')
{
  $to_type = 'mechanics';
  $project_to_type = 'mechanical';
}
else if($_GET['to'] == 'chemistry')
{
  $to_type = 'chemistry';
  $project_to_type = 'chemistry';
}
else if($_GET['to'] == 'tailors')
{
  $to_type = 'tailors';
  $project_to_type = 'tailor';
}
else if($_GET['to'] == 'leatherworks')
{
  $to_type = 'leatherworks';
  $project_to_type = 'leatherwork';
}
else
  die('Angkor waaaaat?');

if($to_type == $from_type)
  die('from ' . $from_type . ' to ' . $to_type . ', eh? :P');

$command = 'SELECT * FROM psypets_' . $from_type . ' WHERE idnum=' . $id . ' LIMIT 1';
$craft = $database->FetchSingle($command, 'fetching ' . $from_type . ' #' . $id);

if($craft === false)
  die($from_type . ' #' . $id . ' does not exist!');

unset($craft['idnum']);

foreach($craft as $i=>$value)
  $craft[$i] = quote_smart($value);

$command = 'SELECT * FROM psypets_' . $to_type . ' WHERE makes=' . $craft['makes'] . ' LIMIT 1';
$existing = $database->FetchSingle($command, 'fetching existing ' . $to_type . ' "' . $craft['makes'] . '"');

if(array_key_exists('addon', $craft) && !array_key_exists('addon', $existing))
  unset($craft['addon']);

if($existing === false)
{
  $command = 'INSERT INTO psypets_' . $to_type . ' (' . implode(',', array_keys($craft)) . ') VALUES (' . implode(',', $craft) . ')';
  $database->FetchNone($command, 'creating ' . $to_type);

  $new_id = $database->InsertID();

  echo 'created ' . $to_type . '<br />';
}
else
{
  echo $to_type . ' already exists<br />';

  $new_id = $existing['idnum'];
}

$command = 'UPDATE monster_projects SET type=\'' . $project_to_type . '\',projectid=' . $new_id . ' WHERE type=\'' . $project_from_type . '\' AND projectid=' . $id;
$database->FetchNone($command, 'updating existing projects');

echo 'updated ' . $database->AffectedRows() . ' existing projects<br />';

$command = 'DELETE FROM psypets_' . $from_type . ' WHERE idnum=' . $id . ' LIMIT 1';
$database->FetchNone($command, 'deleting ' . $from_type);

echo 'deleted ' . $from_type . '<br />';