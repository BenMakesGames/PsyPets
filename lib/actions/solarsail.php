<?php
if($okay_to_be_here !== true)
  exit();

$shape = (int)$_POST['form'];

if($_POST['action'] == 'transform' && $shape >= 1 && $shape <= 2)
{
  if($shape == 1)
    $newname = 'Solar Sail';
  else if($shape == 2)
    $newname = 'Solar Sail (folded)';

  $database->FetchNone('UPDATE monster_inventory SET changed=' . $now . ',itemname=' . quote_smart($newname) . ' WHERE idnum=' . $this_inventory['idnum'] . ' LIMIT 1');
?>
<p>The sail bends and twists in ways you have difficulty comprenending before settling into its new form.</p>
<?php
}
else
{
?>
<p>What shape should the sail take?</p>
<form method="post">
<ul class="plainlist">
 <li><input type="radio" name="form" value="1" id="shape1" /> <label for="shape1">Solar Sail</label></li>
 <li><input type="radio" name="form" value="2" id="shape2" /> <label for="shape2">Solar Sail (folded)</label></li>
</ul>
<p><input type="hidden" name="action" value="transform" /><input type="submit" value="(Un)fold" /></p>
</form>
<?php
}
?>
