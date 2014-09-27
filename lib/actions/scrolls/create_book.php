<?php
if($okay_to_be_here !== true)
  exit();

if($_GET['step'] == 2)
{
  $AGAIN_WITH_ANOTHER = true;
  
  $itemdata = $database->FetchSingle("SELECT itemname FROM monster_items WHERE itemtype LIKE 'print/book%' AND rare='no' AND custom='no' ORDER BY rand() LIMIT 1");
  
  $itemname = $itemdata["itemname"];
  
  add_inventory($user['user'], 'u:' . $user['idnum'], $itemname, 'A ' . $this_inventory['itemname'] . ' wrote this', $this_inventory['location']);
  add_inventory($user['user'], $this_inventory['creator'], 'Feather', 'The remains of a ' . $this_inventory['itemname'], $this_inventory['location']);
  
  delete_inventory_byid($this_inventory['idnum']);
  
  echo '<p>The ', $this_inventory['itemname'], ' begins to move on its own, scribbling out words into thin air.  Pages flutter from the spots where words are written, falling into perfect order and finally being bound into what you recognize as ', $itemname, '.</p>';
  
  if(mt_rand(1, 20) == 1)
    echo '
      <p>Apparently not satisified with its work so far, the ', $this_inventory['itemname'], ' scribbles out one last page before dropping to the ground as a lifeless Feather:</p>
      <p><img src="//saffron.psypets.net/gfx/dialog/magic-quill.png" alt="" width="260" height="370" /></p>
      <p>As you reach out the grab the page, your hand passes through it, and it vanishes.</p>
      <p>Hm!</p>
    ';
  else
    echo '<p>The ', $this_inventory['itemname'] , ', having done its job, returns to your hand as a lifeless Feather.</p>';
}
else
{
?>
<p>The Quill twitches in your hand, as if it has a will of its own.</p>
<ul>
 <li><a href="itemaction.php?idnum=<?= $this_inventory['idnum'] ?>&step=2">Let it write!</a></li>
</ul>
<?php
}
?>
