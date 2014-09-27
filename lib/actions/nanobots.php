<?php
if($okay_to_be_here !== true)
  exit();

if($this_inventory['itemname'] == 'Nanobot Cloth Blueprint')
{
  $projects = array(
    'White Cloth' => array('Fluff' => 2),
    'Black Cloth' => array('Fluff' => 2, 'Black Dye' => 2),
    'Blue Cloth' => array('Fluff' => 2, 'Blue Dye' => 2),
    'Yellow Cloth' => array('Fluff' => 2, 'Yellow Dye' => 2),
    'Red Cloth' => array('Fluff' => 2, 'Red Dye' => 2),
    'Pink Cloth' => array('Fluff' => 2, 'Red Dye' => 1),
    'Green Cloth' => array('Fluff' => 2, 'Blue Dye' => 1, 'Yellow Dye' => 1),
    'Orange Cloth' => array('Fluff' => 2, 'Red Dye' => 1, 'Yellow Dye' => 1),
    'Purple Cloth' => array('Fluff' => 2, 'Blue Dye' => 1, 'Red Dye' => 1),
  );

  $command = 'SELECT itemname,COUNT(idnum) AS c FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND location LIKE \'home%\' AND location NOT LIKE \'home/$%\' AND itemname IN (\'Fluff\', \'Red Dye\', \'Blue Dye\', \'Yellow Dye\', \'Black Dye\') GROUP BY(itemname)';
  $materials = $database->FetchMultipleBy($command, 'itemname', 'fetching materials for cloths');

  $request = $_GET['project'];

  if(array_key_exists($request, $projects))
  {
    $project = $projects[$request];
    $can_make = true;
    
    foreach($project as $item=>$quantity)
    {
      if($materials[$item]['c'] < $quantity)
      {
        $can_make = false;
        break;
      }
    }
    
    if($can_make)
    {
      $command = 'SELECT idnum FROM psypets_tailors WHERE makes=' . quote_smart($request) . ' LIMIT 1';
      $consider_item = $database->FetchSingle($command, 'fetching ' . $request . ' project');

      foreach($project as $item=>$quantity)
        delete_inventory_fromhome($user['user'], $item, $quantity);

      $command = 'INSERT INTO monster_projects (`type`, `userid`, `creator`, `projectid`, `progress`, `notes`) ' .
                 "VALUES ('tailor', " . $user['idnum'] . ', \'u:' . $user['idnum'] . '\', ' . $consider_item['idnum'] . ", '0', 'You started this tailor.')";
      $database->FetchNone($command, 'adding project from nanobot cloth blueprint');

      $command = 'UPDATE monster_inventory SET itemname=\'Nanobots\' WHERE idnum=' . $this_inventory['idnum'] . ' LIMIT 1'; 
      $database->FetchNone($command, 'reverting nanobots to Nanobots');

      $this_inventory['itemname'] = 'Nanobots';
      
      echo 'The Nanobots converge on the pile of materials necessary.  You watch as the pile, covered in their metalic sheen, begins to take form.  Within moments, the foundations for the ' . $request . ' project have been set up.</p><p>The Nanobots then revert to their normal, Nanobot form.</p><p>'; 
    }
    else
      echo '<span class="failure">You do not have the materials to start that project.</span></p><p>';
  }
}

$go_time = (int)$this_inventory['data'];

$shape = (int)$_POST['form'];

if($_POST['action'] == 'transform' && $now >= $go_time && $shape >= 1 && $shape <= 4)
{
  if($shape == 1)
    $newname = 'Nanobots';
  else if($shape == 2)
    $newname = 'Nanobot Teddy Bear';
  else if($shape == 3)
    $newname = 'Nanobot Cloth Blueprint';

  if($newname != $this_inventory['itemname'])
  {
    $go_time = $now + 60 * 60;
    $command = 'UPDATE monster_inventory SET changed=' . $now . ',itemname=' . quote_smart($newname) . ',data=\'' . $go_time . '\' WHERE idnum=' . $this_inventory['idnum'] . ' LIMIT 1';
    $database->FetchNone($command, 'rearranging nanobots');
?>
The Nanobots quickly and efficiently assemble themselves into the form specified.</p><p>
<?php
  }
  else
    echo 'They are already in that shape!</p><p>';
}

if($now >= $go_time)
{
?>
What shape should the Nanobots take?</p>
<form action="itemaction.php?idnum=<?= $this_inventory['idnum'] ?>" method="post">
<ul class="plainlist">
 <li><input type="radio" name="form" value="1" id="shape1" /> <label for="shape1">Nanobots</label></li>
 <li><input type="radio" name="form" value="2" id="shape2" /> <label for="shape2">Nanobot Teddy Bear</label></li>
 <li><input type="radio" name="form" value="3" id="shape3" /> <label for="shape3">Nanobot Cloth Blueprint</label></li>
</ul>
<p><input type="hidden" name="action" value="transform" /><input type="submit" value="Transform" /></p>
</form>
<?php
}
else
  echo 'The Nanobots are still recharging.  They will be able to rearrange themselves again in ' . duration($go_time - $now, 2) . '.';

if($this_inventory['itemname'] == 'Nanobot Cloth Blueprint')
{
  echo '<h4>Start Project</h4><p>Only materials from non-protected rooms of the house can be used to start a project.</p>';

  foreach($projects as $projectitem=>$stuff)
  {
    $can_make = true;  
    echo '<h5>' . $projectitem . '</h5><p>Requires:</p><ul>';
    
    foreach($stuff as $item=>$quantity)
    {
      if($materials[$item]['c'] < $quantity)
      {
        echo '<li class="failure">' . $item . ' (' .  (int)$materials[$item]['c'] . '/' . $quantity . ')</li>';
        $can_make = false;
      }
      else
        echo '<li>' . $item . ' (' . (int)$materials[$item]['c'] . '/' . $quantity . ')</li>';
    }
    
    echo '</ul>';

    if($can_make)
      echo '<ul><li><a href="itemaction.php?idnum=' . $this_inventory['idnum'] . '&project=' . $projectitem . '">Have Nanobots begin work on ' . $projectitem . '</a></li></ul>';
  }
}
?>
