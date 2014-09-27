<?php
if($challenge['step'] == 0)
  exit();

if($challenge['difficulty'] == 1 || $challenge['difficulty'] == 0)
{
  $command = 'SELECT COUNT(*) AS c FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname=\'Narcissus\' AND location=\'storage\'';
  $data = fetch_single($command, 'fetching white radishes in storage');
?>
<p>You encounter a lost-looking botanist...</p>
<p>"Good day.  I don't suppose you could give me a hand?  I'm looking for a Narcissus flower.  I'm a botanist, you see."</p>
<?php
  if($data['c'] == 0)
    echo '<p><i>(You do not have any Narcissus flowers in Storage at this time.)</i></p>';
  else
    echo '<ul><li><a href="?action=go">Give the botanist a Narcissus flower</a></li></ul>';
}
else if($challenge['difficulty'] == 2)
{
  $command = 'SELECT COUNT(*) AS c FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname=\'Amethyst Rose\' AND location=\'storage\'';
  $data = fetch_single($command, 'fetching white radishes in storage');
?>
<p>You encounter a lost-looking botanist...</p>
<p>"Good day.  I don't suppose you could give me a hand?  I'm looking for an Amethyst Rose.  I'm a botanist, you see."</p>
<?php
  if($data['c'] == 0)
    echo '<p><i>(You do not have any Amethyst Roses in Storage at this time.)</i></p>';
  else
    echo '<ul><li><a href="?action=go">Give the botanist an Amethyst Rose</a></li></ul>';
}
else if($challenge['difficulty'] == 3)
{
  $command = 'SELECT COUNT(*) AS c FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname=\'Pansy\' AND location=\'storage\'';
  $data = fetch_single($command, 'fetching white radishes in storage');
?>
<p>You encounter a lost-looking botanist...</p>
<p>"Good day.  I don't suppose you could give me a hand?  I'm looking for a Pansy.  I'm a botanist, you see."</p>
<?php
  if($data['c'] == 0)
    echo '<p><i>(You do not have any Pansies in Storage at this time.)</i></p>';
  else
    echo '<ul><li><a href="?action=go">Give the botanist a Pansy</a></li></ul>';
}
else if($challenge['difficulty'] == 4)
{
  $command = 'SELECT COUNT(*) AS c FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname=\'White Lotus\' AND location=\'storage\'';
  $data = fetch_single($command, 'fetching white radishes in storage');
?>
<p>You encounter a lost-looking botanist...</p>
<p>"Good day.  I don't suppose you could give me a hand?  I'm looking for a White Lotus.  I'm a botanist, you see."</p>
<?php
  if($data['c'] == 0)
    echo '<p><i>(You do not have any White Lotuses in Storage at this time.)</i></p>';
  else
    echo '<ul><li><a href="?action=go">Give the botanist a White Lotus</a></li></ul>';
}
else
  exit();
?>
