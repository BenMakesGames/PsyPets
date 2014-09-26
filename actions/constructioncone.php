<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/projectlib.php';
require_once 'commons/houselib.php';

load_user_projects($user, $projects);

if($_GET['step'] == 2)
{
  $projectid = (int)$_POST['projectid'] - 1;
  
  if(array_key_exists($projectid, $projects))
  {
    if($projects[$projectid]['priority'] == 'no')
    {
      $command = 'UPDATE monster_projects SET priority=\'yes\' WHERE idnum=' . $projects[$projectid]['idnum'] . ' LIMIT 1';
      $database->FetchNone($command, 'prioritizing project');
      
      delete_inventory_byid($this_inventory['idnum']);
      $deleted = true;
    }
  }
}

if($deleted)
{
  echo '<p>You carefully install the ' . $this_inventory['itemname'] . '...</p>';
  $AGAIN_WITH_ANOTHER = true;
}
else
{
  echo '<p>You may use this ' . $this_inventory['itemname'] . ' to encourage pets to work on a project in your house.</p>';

  if(count($projects) > 0)
  {
    echo '
      <p>Which project will you use it on?</p>
      <form action="itemaction.php?idnum=' . $this_inventory['idnum'] . '&step=2" method="post">
      <table>
       <tr class="titlerow">
        <th></th>
        <th></th>
        <th>Project</th>
        <th class="centered">Progress</th>
        <th>Notes</th>
       </tr>
    ';

    $rowclass = begin_row_class();

    foreach($projects as $localid=>$project)
    {
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
        $project_details = array();

      if($project_details === false)
        echo '<tr class="' . $rowclass . '"><td colspan="4">01: broken "' . $project['type'] . '" project :(</td></tr>';
      else if($project['type'] != 'construct' && $project['type'] != '')
      {
        $item = get_item_byname($project_details['makes']);
        $percent = floor($project['progress'] * 100 / $project_details['difficulty']);
        if($percent == 100 && $project['progress'] < $project_details['difficulty'])
          $percent = 99;

        $material_count = array();
        $project_materials = array();

        $materials = explode(',', $project_details['ingredients']);
        foreach($materials as $material)
          $material_count[$material]++;

        arsort($material_count);

        foreach($material_count as $material=>$count)
          $project_materials[] = $material . ($count > 1 ? ' x' . $count : '');
  ?>
   <tr class="<?= $rowclass ?>">
    <td><input type="radio" name="projectid" value="<?= $localid + 1 ?>"<?= $project['priority'] == 'yes' ? ' disabled="disabled"' : '' ?> /></td>
    <td valign="top" align="center"><?= item_display($item, 'onmouseover="Tip(\'<b>Materials</b><br />' . str_replace(array("'", "\""), array("\'", "\\\""), implode('<br />', $project_materials)) . '\')"') ?></td>
    <td valign="top"><?= $item['itemname'] ?></td>
    <td valign="top" align="center"><?= $project['priority'] == 'yes' ? '<img src="gfx/constructioncone.png" alt="" style="vertical-align:text-top;" /> ' : '' ?><?= $percent ?>%<?= $project['priority'] == 'yes' ? ' <img src="gfx/constructioncone.png" alt="(high priority)" title="high priority" style="vertical-align:text-top;" />' : '' ?></td>
    <td valign="top"><?= render_project_notes(format_text($project['notes']), $project['idnum']) ?></td>
   </tr>
  <?php
      }
      else if($project['type'] == "construct")
      {
        $improvement = get_home_improvement_byid($project['itemid']);
        if($improvement === false)
          echo '<tr class="' . $rowclass . '"><td colspan="4">03: broken project :(</td></tr>';
        else
        {
  ?>
   <tr class="<?= $rowclass ?>">
    <td><input type="radio" name="projectid" value="<?= $localid + 1 ?>"<?= $project['priority'] == 'yes' ? ' disabled="disabled"' : '' ?> /></td>
    <td valign="top" align="center"><img src="gfx/homeimprovement.png" alt="home improvement" height="32" /></td>
    <td valign="top"><?= $improvement['name'] ?></td>
    <td valign="top" align="center"><?= $project['priority'] == 'yes' ? '<img src="gfx/constructioncone.png" alt="" style="vertical-align:text-top;" /> ' : '' ?><?= floor($project['progress'] * 100 / $improvement['requirement']) ?>%<?= $project['priority'] == 'yes' ? ' <img src="gfx/constructioncone.png" alt="(high priority)" title="high priority" style="vertical-align:text-top;" />' : '' ?></td>
    <td valign="top"><?= render_project_notes(format_text($project['notes']), $project['idnum']) ?></td>
   </tr>
  <?php
        }
      }
      else
        echo '<tr class="' . $rowclass . '"><td colspan="4">02: broken project :(</td></tr>';

      $rowclass = alt_row_class($rowclass);
    }

    echo '
      </table>
      <p><input type="submit" value="This One" /></p>
      </form>
    ';
  }
  else
    echo '<p>Your pets are not currently working on any project.</p>';
}
?>
