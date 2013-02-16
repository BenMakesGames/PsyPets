<?php
if($challenge['step'] == 0)
  exit();

if($challenge['difficulty'] == 0)
{
  $command = 'SELECT COUNT(*) AS c FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname=\'Redsberries\' AND location=\'storage\'';
  $data = fetch_single($command, 'fetching redsberries in storage');

  if($data['c'] >= 1)
  {
    $command = 'DELETE FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname=\'Redsberries\' AND location=\'storage\' LIMIT 1';
    fetch_none($command, 'deleting redsberries');

    $message = '<p>The Berry Elemental swallows the Redsberries all at once, and then just stands there a moment...</p>' .
               '<p>Just as you think about leaving it looks at you, and nods.  "Thank you... will not forget," and wanders off.</p>';

    $success = true;
  }
}
else if($challenge['difficulty'] == 1)
{
  $command = 'SELECT COUNT(*) AS c FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname=\'White Radish\' AND location=\'storage\'';
  $data = fetch_single($command, 'fetching white radishes in storage');

  if($data['c'] >= 3)
  {
    $command = 'DELETE FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname=\'White Radish\' AND location=\'storage\' LIMIT 3';
    fetch_none($command, 'deleting white radishes');

    $message = '<p>You\'ve never heard of such a strange toll, but you give the man the White Radishes all the same.</p>' .
               '<p>"Alright, then!  Carry on, carry on!" he waves you by.</p>';

    $success = true;
  }
}
else if($challenge['difficulty'] == 2)
{
  $command = 'SELECT COUNT(*) AS c FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname=\'Peanuts\' AND location=\'storage\'';
  $data = fetch_single($command, 'fetching peanuts in storage');

  if($data['c'] >= 6)
  {
    $command = 'DELETE FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname=\'Peanuts\' AND location=\'storage\' LIMIT 6';
    fetch_none($command, 'deleting peanuts');

    $message = '<p>The tiny elephant begins eating the peanuts so excitedly, it forgets to explicitly inform you that it wouldn\'t mind you passing.  You figure it out.</p>';

    $success = true;
  }
}
else if($challenge['difficulty'] == 3)
{
  $command = 'SELECT COUNT(*) AS c FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname=\'Celery\' AND location=\'storage\'';
  $data = fetch_single($command, 'fetching gossamer in storage');

  if($data['c'] >= 6)
  {
    $command = 'DELETE FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname=\'Celery\' AND location=\'storage\' LIMIT 6';
    fetch_none($command, 'deleting gossamer');

    $message = '<p>After handing him the Celery you ask him how he did it, but he refuses to divulge his "ultimate secret".</p>';

    $success = true;
  }
}
else if($challenge['difficulty'] == 4)
{
  $command = 'SELECT COUNT(*) AS c FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname=\'Red Jelly\' AND location=\'storage\'';
  $data = fetch_single($command, 'fetching dark gossamer in storage');

  if($data['c'] >= 9)
  {
    $command = 'DELETE FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname=\'Red Jelly\' AND location=\'storage\' LIMIT 9';
    fetch_none($command, 'deleting dark gossamer');

    $message = '<p>The wizard uses some magics to forge a gigantic cube of Red Jelly, and sets it in motion toward where he believes the Cube to be.  Sure enough, it appears from behind a boulder and attempts to merge with the Red Jelly with messy results...</p>' .
               '<p>You and the wizard successfully make your respective escapes.</p>';

    $success = true;
  }
}
else
  exit();
?>
