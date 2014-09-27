<?php
if($okay_to_be_here !== true)
  exit();

$construction = $house['construction_number'];

$needs_wall = true;
$needs_roof = ($construction % 4 == 0);
$needs_wiring = ($construction % 5 == 0);
$needs_piping = ($construction % 6 == 0);
$needs_window = ($construction % 9 == 0);

$walls = array(1 => 'Brick Wall', 2 => 'Stone Wall', 3 => 'Plaster Wall', 4 => 'Wood Panel Wall', 5 => 'Red Wood Panel Wall', 6 => 'Mural', 7 => 'Egyptian Mural');
$roofs = array(1 => 'Reed Roof', 2 => 'Corrugated Steel Roof', 3 => 'Clay Tile Roof');
$wirings = array(1 => 'Simple Circuit', 2 => 'Complex Circuit');
$pipings = array(1 => 'Copper Piping', 2 => 'Plastic Piping');
$windows = array(1 => 'Window', 2 => 'Little Golden Rectangle Window', 3 => 'Unpainted Door', 4 => 'Painted Door', 5 => 'Red Door');

$consume = array();
$built = false;

if($needs_wall)
{
  $command = '
    SELECT itemname,COUNT(idnum) AS qty
    FROM monster_inventory
    WHERE
      user=' . $database->Quote($user['user']) . ' AND
      location=' . $database->Quote($this_inventory['location']) . ' AND
      itemname ' . $database->In($walls) . '
    GROUP BY(itemname)
  ';
  $wall_items = $database->FetchMultipleBy($command, 'itemname');

  if($_POST['action'] == 'Build!')
  {
    if(array_key_exists($walls[$_POST['wall']], $wall_items))
      $consume[] = $walls[$_POST['wall']];
    else
      $errored = true;
  }
}

if($needs_roof)
{
  $command = '
    SELECT itemname,COUNT(idnum) AS qty
    FROM monster_inventory
    WHERE
      user=' . $database->Quote($user['user']) . ' AND
      location=' . $database->Quote($this_inventory['location']) . ' AND
      itemname ' . $database->In($roofs) . '
    GROUP BY(itemname)
  ';
  $roof_items = $database->FetchMultipleBy($command, 'itemname');

  if($_POST['action'] == 'Build!')
  {
    if(array_key_exists($roofs[$_POST['roof']], $roof_items))
      $consume[] = $roofs[$_POST['roof']];
    else
      $errored = true;
  }
  $bonus_space += 20;
}

if($needs_wiring)
{
  $command = '
    SELECT itemname,COUNT(idnum) AS qty
    FROM monster_inventory
    WHERE
      user=' . $database->Quote($user['user']) . ' AND
      location=' . $database->Quote($this_inventory['location']) . ' AND
      itemname ' . $database->In($wirings) . '
    GROUP BY(itemname)
  ';
  $wiring_items = $database->FetchMultipleBy($command, 'itemname');

  if($_POST['action'] == 'Build!')
  {
    if(array_key_exists($wirings[$_POST['wiring']], $wiring_items))
      $consume[] = $wirings[$_POST['wiring']];
    else
      $errored = true;
  }
}

if($needs_piping)
{
  $command = '
    SELECT itemname,COUNT(idnum) AS qty
    FROM monster_inventory
    WHERE
      user=' . $database->Quote($user['user']) . ' AND
      location=' . $database->Quote($this_inventory['location']) . ' AND
      itemname ' . $database->In($pipings) . '
    GROUP BY(itemname)
  ';
  $piping_items = $database->FetchMultipleBy($command, 'itemname');

  if($_POST['action'] == 'Build!')
  {
    if(array_key_exists($pipings[$_POST['piping']], $piping_items))
      $consume[] = $pipings[$_POST['piping']];
    else
      $errored = true;
  }
}

if($needs_window)
{
  $command = '
    SELECT itemname,COUNT(idnum) AS qty
    FROM monster_inventory
    WHERE
      user=' . $database->Quote($user['user']) . ' AND
      location=' . $database->Quote($this_inventory['location']) . ' AND
      itemname ' . $database->In($windows) . '
    GROUP BY(itemname)
  ';
  $window_items = $database->FetchMultipleBy($command, 'itemname');

  if($_POST['action'] == 'Build!')
  {
    if(array_key_exists($windows[$_POST['window']], $window_items))
      $consume[] = $windows[$_POST['window']];
    else
      $errored = true;
  }
  $bonus_space += 10;
}

$space_gained = 200 + $bonus_space;

if($_POST['action'] == 'Build!')
{
  
  if($errored)
    echo '<p class="failure">You must select a part from each category!</p>';
  else
  {
		$clay_tile_roof = false;
		$brick_wall = false;
	
    foreach($consume as $itemname)
    {
			if($itemname == 'Clay Tile Roof') $clay_tile_roof = true;
			else if($itemname == 'Brick Wall') $brick_wall = true;
		
      $deleted = delete_inventory_byname($user['user'], $itemname, 1, $this_inventory['location']);
      if($deleted == 0)
        die('Failed to delete ' . $itemname . '.  DB error (or cheating).');
    }

    $command = 'UPDATE monster_houses SET maxbulk=maxbulk+' . $space_gained . ',construction_number=construction_number+1 WHERE userid=' . $user['idnum'] . ' LIMIT 1';
    $database->FetchNone($command, 'expanding your house (and your mind?)');

    $built = true;

    $AGAIN_WITH_ANOTHER = true;
		
		if($brick_wall && $clay_tile_roof)
		{
			$badges = get_badges_byuserid($user['idnum']);
			if($badges['brickhouse'] == 'no')
			{
				set_badge($user['idnum'], 'brickhouse');

				echo '<p class="success"><i>(You received the Clay Hut-builder Badge!)</i></p>';
			}
		}
  }
}

if($built)
  echo '<p class="success">Your house has been expanded by ' . ($space_gained / 10) . ' units.</p>';
else
{
  $can_build = true;
?>
<p>To expand your house, you will need...</p>
<form action="itemaction.php?idnum=<?= $this_inventory['idnum'] ?>" method="post">
<?php
  if($needs_wall)
  {
?>
<h5>A Wall</h5>
<?php
    if(count($wall_items) == 0)
    {
      echo '<p class="failure">You have no walls available.  Allowed items are: ';

      foreach($walls as $wall)
        $wall_links[] = item_text_link($wall);

      echo list_nice($wall_links) . '.</p>';

      $can_build = false;
    }
    else
    {
      $rowclass = begin_row_class();

      echo '
        <table>
        <tr class="titlerow"><th></th><th></th><th>Item</th><th>Qty</th></tr>
      ';

      foreach($walls as $id=>$itemname)
      {
        if(!array_key_exists($itemname, $wall_items))
          continue;

        $details = get_item_byname($itemname);
        
        echo '
          <tr class="' . $rowclass . '">
           <td><input type="radio" name="wall" value="' . $id . '" /></td>
           <td class="centered">' . item_display_extra($details) . '</td>
           <td>' . $itemname . '</td>
           <td class="centered">' . $wall_items[$itemname]['qty'] . '</td>
          </tr>
        ';
        
        $rowclass = alt_row_class($rowclass);
      }

      echo '</table>';
    }
  }

  if($needs_roof)
  {
?>
<h5>A Roof</h5>
<?php
    if(count($roof_items) == 0)
    {
      echo '<p class="failure">You have no roofs available.  Allowed items are: ';

      foreach($roofs as $roof)
        $roof_links[] = item_text_link($roof);

      echo list_nice($roof_links) . '.</p>';

      $can_build = false;
    }
    else
    {
      $rowclass = begin_row_class();

      echo '
        <table>
        <tr class="titlerow"><th></th><th></th><th>Item</th><th>Qty</th></tr>
      ';

      foreach($roofs as $id=>$itemname)
      {
        if(!array_key_exists($itemname, $roof_items))
          continue;

        $details = get_item_byname($itemname);

        echo '
          <tr class="' . $rowclass . '">
           <td><input type="radio" name="roof" value="' . $id . '" /></td>
           <td class="centered">' . item_display_extra($details) . '</td>
           <td>' . $itemname . '</td>
           <td class="centered">' . $roof_items[$itemname]['qty'] . '</td>
          </tr>
        ';

        $rowclass = alt_row_class($rowclass);
      }

      echo '</table>';
    }
  }

  if($needs_wiring)
  {
?>
<h5>Wiring</h5>
<?php
    if(count($wiring_items) == 0)
    {
      echo '<p class="failure">You have no wiring available.  Allowed items are: ';

      foreach($wirings as $wiring)
        $wiring_links[] = item_text_link($wiring);

      echo list_nice($wiring_links) . '.</p>';

      $can_build = false;
    }
    else
    {
      $rowclass = begin_row_class();

      echo '
        <table>
        <tr class="titlerow"><th></th><th></th><th>Item</th><th>Qty</th></tr>
      ';

      foreach($wirings as $id=>$itemname)
      {
        if(!array_key_exists($itemname, $wiring_items))
          continue;

        $details = get_item_byname($itemname);

        echo '
          <tr class="' . $rowclass . '">
           <td><input type="radio" name="wiring" value="' . $id . '" /></td>
           <td class="centered">' . item_display_extra($details) . '</td>
           <td>' . $itemname . '</td>
           <td class="centered">' . $wiring_items[$itemname]['qty'] . '</td>
          </tr>
        ';

        $rowclass = alt_row_class($rowclass);
      }

      echo '</table>';
    }
  }

  if($needs_piping)
  {
?>
<h5>Piping</h5>
<?php
    if(count($piping_items) == 0)
    {
      echo '<p class="failure">You have no piping available.  Allowed items are: ';

      foreach($pipings as $piping)
        $piping_links[] = item_text_link($piping);

      echo list_nice($piping_links) . '.</p>';

      $can_build = false;
    }
    else
    {
      $rowclass = begin_row_class();

      echo '
        <table>
        <tr class="titlerow"><th></th><th></th><th>Item</th><th>Qty</th></tr>
      ';

      foreach($pipings as $id=>$itemname)
      {
        if(!array_key_exists($itemname, $piping_items))
          continue;

        $details = get_item_byname($itemname);

        echo '
          <tr class="' . $rowclass . '">
           <td><input type="radio" name="piping" value="' . $id . '" /></td>
           <td class="centered">' . item_display_extra($details) . '</td>
           <td>' . $itemname . '</td>
           <td class="centered">' . $piping_items[$itemname]['qty'] . '</td>
          </tr>
        ';

        $rowclass = alt_row_class($rowclass);
      }

      echo '</table>';
    }
  }

  if($needs_window)
  {
?>
<h5>A Window or Door</h5>
<?php
    if(count($window_items) == 0)
    {
      echo '<p class="failure">You have no windows or doors available.  Allowed items are: ';

      foreach($windows as $window)
        $window_links[] = item_text_link($window);

      echo list_nice($window_links) . '.</p>';

      $can_build = false;
    }
    else
    {
      $rowclass = begin_row_class();

      echo '
        <table>
        <tr class="titlerow"><th></th><th></th><th>Item</th><th>Qty</th></tr>
      ';

      foreach($windows as $id=>$itemname)
      {
        if(!array_key_exists($itemname, $window_items))
          continue;

        $details = get_item_byname($itemname);

        echo '
          <tr class="' . $rowclass . '">
           <td><input type="radio" name="window" value="' . $id . '" /></td>
           <td class="centered">' . item_display_extra($details) . '</td>
           <td>' . $itemname . '</td>
           <td class="centered">' . $window_items[$itemname]['qty'] . '</td>
          </tr>
        ';

        $rowclass = alt_row_class($rowclass);
      }

      echo '</table>';
    }
  }
  
  if($can_build)
    echo '<p><input type="submit" name="action" value="Build!" /></p>';

  echo '
    </form>
    <p>You will gain ' . ($space_gained / 10) . ' units of space when you expand your house in this way.</p>
  ';
}
?>