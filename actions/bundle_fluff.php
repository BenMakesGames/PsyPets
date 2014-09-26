<?php
if($okay_to_be_here !== true)
  exit();

$data = $database->FetchSingle('
  SELECT COUNT(idnum) AS qty
  FROM monster_inventory
  WHERE
    user=' . quote_smart($user['user']) . '
    AND location=' . quote_smart($this_inventory['location']) . '
    AND itemname=\'Fluff\'
');

$fluff_count = (int)$data['qty'];

if($fluff_count >= 500)
{
  if($_GET['action'] == 'bundle')
  {
    $deleted = delete_inventory_byname($user['user'], 'Fluff', 500, $this_inventory['location']);
    
    if($deleted < 500)
    {
      add_inventory_quantity($user['user'], '', 'Fluff', '', $this_inventory['location'], $deleted);
      echo '<p>Are you suuuuuure you have 500 Fluff available?  It <em>seems</em> like you only have ' . $deleted . '.</p>';
      $AGAIN_WITH_SAME = true;
    }
    else
    {
      delete_inventory_byid($this_inventory['idnum']);
      add_inventory($user['user'], 'u:' . $user['idnum'], 'Bale of Fluff', 'Bound together by ' . $user['display'], $this_inventory['location']);
      echo '<p>It\'s a bit of work, but you manage to compress the 500 Fluff down into a single Bale of Fluff.</p>';
      $AGAIN_WITH_ANOTHER = true;
    }
  }
  else
  {
    echo
      '<p>Will you bundle 500 Fluff into a single Bale of Fluff?</p>',
      '<ul><li><a href="?idnum=' . $this_inventory['idnum'] . '&action=bundle">Totally.</a></li></ul>'
    ;
  }
}
else
{
  echo '<p>It occurs to you that this belt could be used to bundle up some Fluff into a Bale of Fluff.  But you\'d probably need about - hm - 500 Fluff to make a good, solid bale.</p>';
  echo '<p>You know... roughly... (okay, <em>precisely</em> &gt;_&gt;  And you have ' . $fluff_count . ' at the moment, so...)</p>';
}
?>
