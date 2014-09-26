<?php
if($okay_to_be_here !== true)
  exit();

if($_GET['action'] == 'gaze')
{  
  $AGAIN_WITH_ANOTHER = true;

  delete_inventory_byid($this_inventory['idnum']);

  if(mt_rand(1, 2) == 2)
  {
    echo '
      <p>The eye closes, and you feel the warmth of magic radiating from it.</p>
      <p>Slowly, fibers begin to pull themselves together from out of thin air, until, at length, a bit of Gossamer is assembled!</p>
      <p>You notice, at this point, that the eye itself has vanished.</p>
    ';

    add_inventory($user['user'], '', 'Gossamer', 'Created from a ' . $this_item['itemname'], $this_inventory['location']);
  }
  else
  {
    echo '
      <p>The eye closes, and you feel the warmth of magic radiating from it.</p>
      <p>Slowly, fibers begin to pull themselves together from out of thin air, but a sudden chill shrivels them.</p>
      <p>Something has gone wrong.</p>
      <p>You notice, at this point, that the eye has turned to stone little different from a Small Rock.</p>
    ';

    add_inventory($user['user'], '', 'Small Rock', 'A ruined ' . $this_item['itemname'], $this_inventory['location']);
  }
}
else
{
  echo '<p>Gaze into the ' . $this_item['itemname'] . '?</p>';
  echo '<ul><li><a href="?idnum=' . $this_inventory['idnum'] . '&amp;action=gaze" title="*stare*">じー</a></li></ul>';
}
?>
