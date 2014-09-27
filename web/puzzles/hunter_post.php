<?php
if($challenge['step'] == 0)
  exit();

if($challenge['difficulty'] == 0)
{
  $command = 'SELECT COUNT(*) AS c FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname=\'Jerky\' AND location=\'storage\'';
  $data = fetch_single($command, 'fetching Jerky in storage');

  if($data['c'] >= 1)
  {
    $command = 'DELETE FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname=\'Jerky\' AND location=\'storage\' LIMIT 1';
    fetch_none($command, 'deleting Jerky');

    $success = true;
  }
}
else if($challenge['difficulty'] == 1)
{
  $command = 'SELECT COUNT(*) AS c FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname=\'Bear Trap\' AND location=\'storage\'';
  $data = fetch_single($command, 'fetching bear traps in storage');

  if($data['c'] >= 1)
  {
    $command = 'DELETE FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname=\'Bear Trap\' AND location=\'storage\' LIMIT 1';
    fetch_none($command, 'deleting bear traps');

    $success = true;
  }
}
else if($challenge['difficulty'] == 2)
{
  $command = 'SELECT COUNT(*) AS c FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname=\'Net\' AND location=\'storage\'';
  $data = fetch_single($command, 'fetching nets in storage');

  if($data['c'] >= 1)
  {
    $command = 'DELETE FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname=\'Net\' AND location=\'storage\' LIMIT 1';
    fetch_none($command, 'deleting net');

    $success = true;
  }
}
else if($challenge['difficulty'] == 3)
{
  $command = 'SELECT COUNT(*) AS c FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname=\'Compound Bow\' AND location=\'storage\'';
  $data = fetch_single($command, 'fetching compound bow in storage');

  if($data['c'] >= 1)
  {
    $command = 'DELETE FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname=\'Compound Bow\' AND location=\'storage\' LIMIT 1';
    fetch_none($command, 'deleting compound bow');

    $success = true;
  }
}
else if($challenge['difficulty'] == 4)
{
  $command = 'SELECT COUNT(*) AS c FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname=\'Bronze Spear\' AND location=\'storage\'';
  $data = fetch_single($command, 'fetching bronze spear in storage');

  if($data['c'] >= 2)
  {
    $command = 'DELETE FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname=\'Bronze Spear\' AND location=\'storage\' LIMIT 2';
    fetch_none($command, 'deleting bronze spear');

    $success = true;
  }
}
else
  exit();

if($success)
  $message = '<p>The hunter thanks you for your assistance before moving on to the next target...</p>';
?>
