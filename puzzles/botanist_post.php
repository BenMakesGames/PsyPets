<?php
if($challenge['step'] == 0)
  exit();

if($challenge['difficulty'] == 1 || $challenge['difficulty'] == 0)
{
  $command = 'SELECT COUNT(*) AS c FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname=\'Narcissus\' AND location=\'storage\'';
  $data = fetch_single($command, 'fetching narcissus in storage');

  if($data['c'] > 0)
  {
    $command = 'DELETE FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname=\'Narcissus\' AND location=\'storage\' LIMIT 1';
    fetch_none($command, 'deleting white radishes');

    $message = '<p>"Thanks a bundle!  With this I can continue my research!</p>';

    $success = true;
  }
}
else if($challenge['difficulty'] == 2)
{
  $command = 'SELECT COUNT(*) AS c FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname=\'Amethyst Rose\' AND location=\'storage\'';
  $data = fetch_single($command, 'fetching white radishes in storage');

  if($data['c'] > 0)
  {
    $command = 'DELETE FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname=\'Amethyst Rose\' AND location=\'storage\' LIMIT 1';
    fetch_none($command, 'deleting white radishes');

    $message = '<p>"Thanks a bundle!  With this I can continue my research!</p>';

    $success = true;
  }
}
else if($challenge['difficulty'] == 3)
{
  $command = 'SELECT COUNT(*) AS c FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname=\'Pansy\' AND location=\'storage\'';
  $data = fetch_single($command, 'fetching white radishes in storage');

  if($data['c'] > 0)
  {
    $command = 'DELETE FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname=\'Pansy\' AND location=\'storage\' LIMIT 1';
    fetch_none($command, 'deleting white radishes');

    $message = '<p>"Thanks a bundle!  With this I can continue my research!</p>';

    $success = true;
  }
}
else if($challenge['difficulty'] == 4)
{
  $command = 'SELECT COUNT(*) AS c FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname=\'White Lotus\' AND location=\'storage\'';
  $data = fetch_single($command, 'fetching white radishes in storage');

  if($data['c'] > 0)
  {
    $command = 'DELETE FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname=\'White Lotus\' AND location=\'storage\' LIMIT 1';
    fetch_none($command, 'deleting white radishes');

    $message = '<p>"Thanks a bundle!  With this I can continue my research!</p>';

    $success = true;
  }
}
else
  exit();
?>
