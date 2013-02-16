<?php
if($challenge['step'] == 0)
  exit();

if($challenge['difficulty'] == 0)
{
  $command = 'SELECT COUNT(*) AS c FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname=\'Small Rock\' AND location=\'storage\'';
  $data = fetch_single($command, 'fetching small rocks in storage');

  if($data['c'] >= 1)
  {
    $command = 'DELETE FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname=\'Small Rock\' AND location=\'storage\' LIMIT 1';
    fetch_none($command, 'deleting small small');

    $message = '
      <p>"Great!  Let me just..."</p>
      <p>*CLANG!*</p>
      <p>The door slides open.</p>
      <p>"Ha!  Perfect!  Thanks to you, this path is now open."</p>
    ';

    $success = true;
  }
}
else if($challenge['difficulty'] == 1)
{
  $command = 'SELECT COUNT(*) AS c FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname=\'Gold\' AND location=\'storage\'';
  $data = fetch_single($command, 'fetching gold in storage');

  if($data['c'] >= 1)
  {
    $command = 'DELETE FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname=\'Gold\' AND location=\'storage\' LIMIT 1';
    fetch_none($command, 'deleting gold');

    $message = '<p>"Yeehaw!  We\'ve done it!</p><p>Well I\'m sure glad you came by.  Maybe we\'ll cross paths ag\'in."</p>';

    $success = true;
  }
}
else if($challenge['difficulty'] == 2)
{
  $command = 'SELECT COUNT(*) AS c FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname=\'Gold\' AND location=\'storage\'';
  $data = fetch_single($command, 'fetching gold in storage');

  if($data['c'] >= 3)
  {
    $command = 'DELETE FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname=\'Gold\' AND location=\'storage\' LIMIT 3';
    fetch_none($command, 'deleting gold');

    $message = '<p style="font-family:monospace;">"Mission accomplished!  Door is now open!  Proceeding!"</p>';

    $success = true;
  }
}
else if($challenge['difficulty'] == 3)
{
  $command = 'SELECT COUNT(*) AS c FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname=\'Argrum Steel\' AND location=\'storage\'';
  $data = fetch_single($command, 'fetching argrum steel in storage');

  if($data['c'] >= 1)
  {
    $command = 'DELETE FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname=\'Argrum Steel\' AND location=\'storage\' LIMIT 1';
    fetch_none($command, 'deleting argrum steel');

    $message = '<p>"Wonderful!  Success!  Haha!"</p><p>The man shakes your hand.</p><p>"Good meeting you.  I must be on my way.  But perhaps we meet again."</p>';

    $success = true;
  }
}
else if($challenge['difficulty'] == 4)
{
  $command = 'SELECT COUNT(*) AS c FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname=\'Small Rock\' AND location=\'storage\'';
  $data = fetch_single($command, 'fetching small rocks in storage');

  if($data['c'] >= 1)
  {
    $command = 'DELETE FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname=\'Small Rock\' AND location=\'storage\' LIMIT 1';
    fetch_none($command, 'deleting small rock');

    $message = '<p>"Brilliant!</p><p>"Well then, I guess we\'d better be on our respective ways.  Pleasure working with you."</p>';

    $success = true;
  }
}
else
  exit();
?>
