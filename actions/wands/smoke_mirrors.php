<?php
if($okay_to_be_here !== true)
  exit();

if($_POST['action'] == 'Unleash its Power!')
{
  $AGAIN_WITH_ANOTHER = true;

  $database->FetchNone('
    UPDATE monster_inventory
    SET itemname=\'Ruins\'
    WHERE idnum=' . $this_inventory['idnum'] . '
    LIMIT 1
  ');
  
  $items = $database->FetchMultiple('
    SELECT idnum,itemname
    FROM monster_inventory
    WHERE
      user=' . quote_smart($user['user']) . '
      AND location=' . quote_smart($this_inventory['location']) . '
      AND itemname IN (\'Smoke\', \'Mirror\')
    ORDER BY RAND()
    LIMIT 20
  ');
  
  $mirror_ids = array();
  $smoke_ids = array();
  
  foreach($items as $item)
  {
    if($item['itemname'] == 'Smoke')
      $smoke_ids[] = $item['idnum'];
    else // Mirror
      $mirror_ids[] = $item['idnum'];
  }

  $changes = array();
  
  if(count($mirror_ids) > 0)
  {
    $database->FetchNone('
      UPDATE monster_inventory
      SET
        itemname=\'Smoke\',
        changed=' . $now . '
      WHERE idnum ' . $database->In($mirror_ids) . '
      LIMIT ' . count($mirror_ids) . '
    ');

    $mirrors = $database->AffectedRows();

    $changes[] = ucfirst(say_number($mirrors)) . ' ' . ($mirrors == 1 ? 'Mirror was' : 'Mirrors were') . ' transformed into Smoke';
  }

  if(count($smoke_ids) > 0)
  {
    $smoke = $database->FetchNone('
      UPDATE monster_inventory
      SET
        itemname=\'Mirror\',
        changed=' . $now . '
      WHERE idnum ' . $database->In($smoke_ids) . '
      LIMIT ' . count($smoke_ids) . '
    ');

    $smoke = $database->AffectedRows();

    $changes[] = ucfirst(say_number($smoke)) . ' Smoke ' . ($smoke == 1 ? 'was' : 'were') . ' transformed into ' . ($smoke == 1 ? 'a Mirror' : 'Mirrors');
  }

  if(count($changes) == 0)
    echo '<p>Nothing happens... although, the ' . $this_inventory['itemname'] . ' <em>is</em> reduced to Ruins.</p>';
  else
  {
    if(count($changes) == 1)
      echo '<p>' . $changes[0] . '!</p>';
    else
      echo '<p>' . $changes[1] . ', and ' . $changes . '!</p>';
    
    echo '<p>The ' . $this_inventory['itemname'] . ' is reduced to Ruins.</p>';
  }
}
else
{
?>
  <p>The wand twitches with transformative power...</p>
  <form method="post">
  <p><input type="submit" name="action" value="Unleash its Power!" class="bigbutton" /></p>
  </form>
<?php
}
?>
