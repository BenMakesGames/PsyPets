<?php
if($okay_to_be_here !== true)
  exit();

$max = 220000;
$wished = false;

if($_POST['action'] == 'Wish')
{
  $itemname = trim($_POST['itemname']);

  $command = 'SELECT itemname,custom FROM monster_items WHERE itemname=' . quote_smart($itemname) . ' LIMIT 1';
  $item = $database->FetchSingle($command, 'fetching item');
  
  if($item === false)
    echo '<p class="failure">There is no item by that name.</p>';
  else if($custom != 'no')
    echo '<p class="failure">That item is beyond the One Wish\'s ability to grant.</p>';
  else
  {
    $command = 'SELECT COUNT(idnum) AS c FROM monster_inventory WHERE itemname=' . quote_smart($itemname);
    $data = $database->FetchSingle($command, 'fetching inventory count');
  }
}

if($wished === true)
{
?>
<p>You may ask for any one item you want, <em><strong>however</strong></em>... the rarer the item, the lower the chance your wish will be granted.</p>
<p>Knowing this, what will you ask for?</p>
<form action="itemaction.php?idnum=<?= $this_inventory['idnum'] ?>" method="post">
<p><input type="text" name="itemname" /> <input type="submit" name="action" value="Wish" /></p>
</form>
<p><i>(You may not ask for pay items, or otherwise limited-availability items.  If you accidentally wish for such an item your One Wish will <strong>not</strong> be used up, so if you're in doubt, it doesn't hurt to try.)</i></p>
<?php
}
?>
