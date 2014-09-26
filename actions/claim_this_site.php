<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/houselib.php';
require_once 'commons/basementlib.php';

$house = get_house_byuser($user['idnum']);

if($house === false)
{
  echo "Failed to load your house.<br />\n";
  exit();
}

$addons = take_apart(',', $house['addons']);
$have_basement = (array_search('Basement', $addons) !== false);

$words = explode(' ', $this_item['itemname']);
$size = $words[2] * 10;

if($_GET['step'] == 2)
{
  home_improvement($user['locid'], $user['idnum'], $size);
  delete_inventory_byid($this_inventory['idnum']);
  echo ($size / 10) . ' Units of space have been added to your estate.';

  require_once 'commons/statlib.php';
  record_stat($user['idnum'], 'Used a Deed to Expand Your House', 1);
}
else if($_GET['step'] == 3 && $size == 10000 && $have_basement)
{
  levelup_basement($user['idnum'], $user['locid']);
  delete_inventory_byid($this_inventory['idnum']);

  echo 'Another floor has been added to your basement.';

  require_once 'commons/statlib.php';
  record_stat($user['idnum'], 'Used a Deed to Expand Your Basement', 1);
}
else
{
  if($size == 10000 && $have_basement)
  {
?>
You may claim this deed to either add <?= ($size / 10) ?> Units of space to your estate, or to add another floor to your basement (worth 100 space).</p>
<ul>
 <li><a href="itemaction.php?idnum=<?= $this_inventory['idnum'] ?>&step=2">Claim <?= ($size / 10) ?> Units</a></li>
 <li><a href="itemaction.php?idnum=<?= $this_inventory['idnum'] ?>&step=3">Add another floor to the basement</a></li>
</ul>
<?php
  }
  else
  {
?>
If you claim this deed, <?= ($size / 10) ?> Units of space will be added to your estate.</p>
<ul>
 <li><a href="itemaction.php?idnum=<?= $this_inventory['idnum'] ?>&step=2">Claim <?= ($size / 10) ?> Units</a></li>
</ul>
<?php
  }
}
?>
