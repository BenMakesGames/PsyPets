<?php
$_GET['maintenance'] = 'no';

require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$require_petload = "no";

// confirm the session...
require_once "commons/dbconnect.php";
require_once "commons/rpgfunctions.php";
require_once "commons/sessions.php";
require_once "commons/grammar.php";
require_once "commons/formatting.php";

if($admin['manageitems'] != 'yes')
{
  header('Location: /admin/tools.php');
  exit();
}

if($_GET['type'] == 'inventions')
  $edittype = 'inventions';
else if($_GET['type'] == 'smiths')
  $edittype = 'smiths';
else if($_GET['type'] == 'tailors')
  $edittype = 'tailors';
else
  $edittype = 'crafts';

$projectid = (int)$_GET['id'];

$command = 'SELECT * FROM psypets_' . $edittype . ' WHERE idnum=' . $projectid . ' LIMIT 1';
$project = $database->FetchSingle($command, 'fetching ' . $param . ' project #' . $projectid);

if($project === false)
{
  header('Location: /admin/projecteditor.php?edit=' . $edittype);
  exit();
}

if($_POST['action'] == 'Save' || $_POST['action'] == 'Save and Back')
{
  $updates = array();

  if((int)$_POST['difficulty'] != $project['difficulty'])
    $updates[] = 'difficulty=' . (int)$_POST['difficulty'];

  if($_POST['pattern'] == 'on' || $_POST['pattern'] == 'yes')
  {
    if($project['mazeable'] != 'yes')
      $updates[] = 'mazeable=\'yes\'';
  }
  else
  {
    if($project['mazeable'] == 'yes')
      $updates[] = 'mazeable=\'no\'';
  }

  if((int)$_POST['min_month'] != $project['min_month'])
    $updates[] = 'min_month=' . (int)$_POST['min_month'];

  if((int)$_POST['max_month'] != $project['max_month'])
    $updates[] = 'max_month=' . (int)$_POST['max_month'];

  if((int)$_POST['openness'] != $project['min_openness'])
    $updates[] = 'min_openness=' . (int)$_POST['openness'];

  if(count($updates) > 0)
  {
    $command = 'UPDATE psypets_' . $edittype . ' SET ' . implode(',', $updates) . ' WHERE idnum=' . $projectid . ' LIMIT 1';
    $database->FetchNone(($command, 'saving ' . $param . ' project #' . $projectid);
    
    if($_POST['action'] == 'Save')
      header('Location: /admin/editproject.php?type=' . $edittype . '&id=' . $projectid);
    else
      header('Location: /admin/projecteditor.php?edit=' . $edittype);

    exit();
  }
}

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Administrative Tools &gt; Project Editor &gt; Edit</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/admin/tools.php">Administrative Tools</a> &gt; <a href="/admin/projecteditor.php?edit=<?= $edittype ?>">Project Editor</a> &gt; Edit</h4>
     <form method="post">
     <table>
      <tr>
       <th>Difficulty</th><td><input type="text" name="difficulty" size="2" maxlength="2" value="<?= $project['difficulty'] ?>" /></td>
      </tr>
      <tr>
       <th>Ingredients</th><td><textarea name="ingredients" cols="40" rows="3" disabled="disabled"><?= htmlentities($project['ingredients']) ?></textarea></td>
      </tr>
      <tr>
       <th>Makes</th><td><input type="text" name="itemname" maxlength="64" disabled="disabled" value="<?= htmlentities($project['makes']) ?>" /></td>
      </tr>
      <tr>
       <th>In Pattern?</th><td><input type="checkbox" name="pattern"<?= $project['mazeable'] == 'yes' ? ' checked="checked"' : '' ?> /></td>
      </tr>
      <tr>
       <th>Month Range</th><td><input type="text" name="min_month" size="2" maxlength="2" value="<?= $project['min_month'] ?>" /> - <input type="text" name="max_month" size="2" maxlength="2" value="<?= $project['max_month'] ?>" /></td>
      </tr>
      <tr>
       <th>Openness Prereq</th><td><input type="text" name="openness" size="2" maxlength="2" value="<?= $project['min_openness'] ?>" /></td>
      </tr>
     </table>
     <p><input type="submit" name="action" value="Save" /> <input type="submit" name="action" value="Save and Back" class="bigbutton" /></p>
     </form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
