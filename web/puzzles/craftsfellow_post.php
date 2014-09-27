<?php
if($challenge['step'] == 0)
  exit();

if($challenge['difficulty'] == 0)
{
  $command = 'SELECT COUNT(*) AS c FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname=\'Chalk\' AND location=\'storage\'';
  $data = fetch_single($command, 'fetching chalk in storage');

  if($data['c'] >= 1)
  {
    $command = 'DELETE FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname=\'Chalk\' AND location=\'storage\' LIMIT 1';
    fetch_none($command, 'deleting chalk');

    $message = '<p>"Oh thank you so much!  My master gets upset quite easily!  You\'ve saved me from a long lecture, or worse!"</p>';

    $success = true;
  }
}
else if($challenge['difficulty'] == 1)
{
  $command = 'SELECT COUNT(*) AS c FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname=\'Pinecone\' AND location=\'storage\'';
  $data = fetch_single($command, 'fetching pinecones in storage');

  if($data['c'] >= 2)
  {
    $command = 'DELETE FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname=\'Pinecone\' AND location=\'storage\' LIMIT 2';
    fetch_none($command, 'deleting pinecones');

    $message = '<p><img src="gfx/monsters/squirrel.png" align="left" height="48" width="48" /> "Thanks, friend!  I won\'t forget you!"</p>';

    $success = true;
  }
}
else if($challenge['difficulty'] == 2)
{
  $command = 'SELECT COUNT(*) AS c FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname=\'Chalk\' AND location=\'storage\'';
  $data = fetch_single($command, 'fetching chalk in storage');

  if($data['c'] >= 6)
  {
    $command = 'DELETE FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname=\'Chalk\' AND location=\'storage\' LIMIT 6';
    fetch_none($command, 'deleting chalk');

    $message = '<p>The wizard takes half of the chalk for himself, leaving the rest with you, and following his careful instruction you both draw a giant seal on the ground.</p>' .
               '<p>"Excellent!  Most excellent!  A job well done!  I suppose we can both be on our ways, then!  Well, perhaps we\'ll meet again someday!  Farewell!"</p>';

    $success = true;
  }
}
else if($challenge['difficulty'] == 3)
{
  $command = 'SELECT COUNT(*) AS c FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname=\'Gossamer\' AND location=\'storage\'';
  $data = fetch_single($command, 'fetching gossamer in storage');

  if($data['c'] >= 1)
  {
    $command = 'DELETE FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname=\'Gossamer\' AND location=\'storage\' LIMIT 1';
    fetch_none($command, 'deleting gossamer');

    $message = '<p>"Thank you!  Thank you, thank you!  You must forgive my haste, as I\'m already a bit behind, but I will not forget the help you gave me here today!"</p>';

    $success = true;
  }
}
else if($challenge['difficulty'] == 4)
{
  $command = 'SELECT COUNT(*) AS c FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname=\'Dark Gossamer\' AND location=\'storage\'';
  $data = fetch_single($command, 'fetching dark gossamer in storage');

  if($data['c'] >= 1)
  {
    $command = 'DELETE FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname=\'Dark Gossamer\' AND location=\'storage\' LIMIT 1';
    fetch_none($command, 'deleting dark gossamer');

    $message = '<p>"Good, good.  That\'ll do," she says, patching up her wing.  "Now if you\'ll excuse me, I have better places to be."</p>';

    $success = true;
  }
}
else
  exit();
?>
