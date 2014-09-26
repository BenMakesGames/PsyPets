<?php
if($okay_to_be_here !== true)
  exit();

$shape = (int)$_POST['form'];

if($_POST['action'] == 'transform' && $shape >= 1 && $shape <= 4)
{
  if($shape == 1)
    $newname = 'Lycanthrope, Form of The Adventurer';
  else if($shape == 2)
    $newname = 'Lycanthrope, Form of The Artisan';
  else if($shape == 3)
    $newname = 'Lycanthrope, Form of The Carpenter';
  else if($shape == 4)
    $newname = 'Lycanthrope, Form of The Hunter';

  $database->FetchNone('UPDATE monster_inventory SET changed=' . $now . ',itemname=' . quote_smart($newname) . ' WHERE idnum=' . $this_inventory['idnum'] . ' LIMIT 1');
?>
The Lycanthrope mutates, even as you hold it, taking on its new form.
<?php
}
else
{
?>
What shape should Lycanthrope take?</p>
<form action="itemaction.php?idnum=<?= $this_inventory['idnum'] ?>" method="post">
<ul class="plainlist">
 <li><input type="radio" name="form" value="1" id="shape1" /> <label for="shape1">Form of The Adventurer</label></li>
 <li><input type="radio" name="form" value="2" id="shape2" /> <label for="shape2">Form of The Artisan</label></li>
 <li><input type="radio" name="form" value="3" id="shape3" /> <label for="shape3">Form of The Carpenter</label></li>
 <li><input type="radio" name="form" value="4" id="shape4" /> <label for="shape4">Form of The Hunter</label></li>
</ul>
<p><input type="hidden" name="action" value="transform" /><input type="submit" value="Transform" /></p>
</form>
<?php
}
?>
