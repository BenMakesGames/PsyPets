<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/houselib.php';
require_once 'commons/houseresources.php';

$possible_items = array(
  'Collared Black Cape',
  'Collared Blue Cape',
  'Collared Purple Cape',
  'Collared Red Cape',
  'Collared Yellow Cape',
);

$project_details = $database->FetchMultipleBy('
  SELECT * FROM `psypets_tailors`
  WHERE makes IN (\'' . implode('\', \'', $possible_items) . '\')
  LIMIT ' . count($possible_items) . '
', 'makes');

get_housestats_byloc($user);

if($_POST['action'] == 'Start Project')
{
  $i = (int)$_POST['project'];

  if(array_key_exists($i, $possible_items) && array_key_exists($possible_items[$i], $project_details))
  {
    $consider_item = $project_details[$possible_items[$i]];

    if(resources_available($consider_item['ingredients']) === true)
    {
      if(expend_resources($consider_item['ingredients'], $user['user']) === false)
      {
        echo "Failed to use up the resources demanded by this project.<br />\n";
        exit();
      }

      $command = 'INSERT INTO monster_projects ' .
                 '(`type`, `userid`, `creator`, `projectid`, `progress`, `notes`) ' .
                 'VALUES (\'tailor\', ' . $user['idnum'] . ', \'u:' . $user['idnum'] . '\', ' . $consider_item['idnum'] . ', 0, ' . quote_smart('You started this tailory.') . ')';
      $database->FetchNone($command, 'starting project');

      echo '<p>You piece together the basis for the ' . $consider_item['makes'] . ' project.</p>';

      delete_inventory_byid($this_inventory['idnum']);

      $AGAIN_WITH_ANOTHER = true;

      require_once 'commons/statlib.php';
      record_stat($user['idnum'], 'Used a Blueprint', 1);
    }
    else
      $not_enough_materials = true;
  }
  else
    echo '<p>no such project</p>';
}

if($not_enough_materials)
  echo '<p>Unfortunately, you don\'t have the materials necessary to start this project.</p>';

if(!$AGAIN_WITH_ANOTHER)
{
?>
<p>This pattern contains instructions for making capes.  Which cape do you want to set up a project for?  (Materials must be in a non-protected room of your house.)</p>
<form method="post">
<p><select name="project">
<?php
foreach($possible_items as $i=>$item)
{
  $materials = explode(',', $project_details[$item]['ingredients']);

  $needed = array();
  $list = array();
  
  foreach($materials as $material)
    $needed[$material]++;

  foreach($needed as $material=>$qty)
    $list[] = $qty . '&times; ' . $material;

  if(resources_available($project_details[$item]['ingredients']) === true)
    echo '<option value="' . $i . '">' . $item . ' (requires ' . list_nice($list) . ')</option>';
  else
    echo '<option value="' . $i . '" disabled="disabled">' . $item . ' (requires ' . list_nice($list) . ')</option>';
}
?>
</select> <input type="submit" name="action" value="Start Project" class="bigbutton" /></p>
</form>
<?php
}
?>
