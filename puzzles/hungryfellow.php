<?php
if($challenge['step'] == 0)
  exit();

if($challenge['difficulty'] == 0)
{
  $command = 'SELECT COUNT(*) AS c FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname=\'Redsberries\' AND location=\'storage\'';
  $data = fetch_single($command, 'fetching white radishes in storage');
?>
<p>A Berry Elemental stops you on the road.  You get ready for a fight, but then you see its arm dripping Redsberry Wine:  it's wounded, and badly!</p>
<p>"Berries," it breathes.  "Please... need Redsberries..."</p>
<?php
  if($data['c'] < 1)
    echo '<p><i>(You do not have any Redsberries in Storage at this time.)</i></p>';
  else
    echo '<ul><li><a href="?action=go">Give the Berry Elemental some Redsberries</a></li></ul>';
}
else if($challenge['difficulty'] == 1)
{
  $command = 'SELECT COUNT(*) AS c FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname=\'White Radish\' AND location=\'storage\'';
  $data = fetch_single($command, 'fetching white radishes in storage');
?>
<p>You encounter an odd-looking tollbooth...</p>
<p>"Hallo!" a man says, hopping out of the tollbooth.  "I'm afraid I can't let you pass unless you pay the toll.  3 White Radishes, please!"</p>
<?php
  if($data['c'] == 0)
    echo '<p><i>(You do not have any White Radishes in Storage at this time.)</i></p>';
  else if($data['c'] < 3)
    echo '<p><i>(You only have ' . $data['c'] . ' White Radishes in Storage at this time.)</i></p>';
  else
    echo '<ul><li><a href="?action=go">Give the man three White Radishes</a></li></ul>';
}
else if($challenge['difficulty'] == 2)
{
  $command = 'SELECT COUNT(*) AS c FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname=\'Peanuts\' AND location=\'storage\'';
  $data = fetch_single($command, 'fetching celery in storage');
?>
<p>A tiny elephant stops you on the road.  You get the impression it wants peanuts.  Six of them.</p>
<p>Small though it may be, it is surprisingly obstinate...</p>
<?php
  if($data['c'] == 0)
    echo '<p><i>(You do not have any Peanuts in Storage at this time.)</i></p>';
  else if($data['c'] < 6)
    echo '<p><i>(You only have ' . $data['c'] . ' Peanuts in Storage at this time.)</i></p>';
  else
    echo '<ul><li><a href="?action=go">Give the tiny elephant six Peanuts</a></li></ul>';
}
else if($challenge['difficulty'] == 3)
{
  $command = 'SELECT COUNT(*) AS c FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname=\'Celery\' AND location=\'storage\'';
  $data = fetch_single($command, 'fetching celery in storage');
?>
<p>You encounter a strange man who bets you 6 Celery he can always defeat you in a game of rock-paper-scissors.</p>
<p>Somehow, he manages it.</p>
<?php
  if($data['c'] == 0)
    echo '<p><i>(You do not have any Celery in Storage at this time.)</i></p>';
  else if($data['c'] < 6)
    echo '<p><i>(You only have ' . $data['c'] . ' Celery in Storage at this time.)</i></p>';
  else
    echo '<ul><li><a href="?action=go">Give the guy six Celery</a></li></ul>';
}
else if($challenge['difficulty'] == 4)
{
  $command = 'SELECT COUNT(*) AS c FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname=\'Red Jelly\' AND location=\'storage\'';
  $data = fetch_single($command, 'fetching red jelly in storage');
?>
<p>You encounter a distressed-looking wizard...</p>
<p>"Ah, friend, perhaps you can assist me to our mutual benefit! There is a gigantic Gelatinous Cube up ahead - the largest I've ever seen.  I believe fighting it would be to our severe disadvantage, however there is another way!  The Gelatinous Cube is not a very intelligent creature.  With nine Red Jellies, I should be able to make a giant cube of jelly that the Gelantinous Cube will perceive to be one of its own kind.  When it attemptes to merge with the fake, we can escape!  How about it?"</p>
<?php
  if($data['c'] == 0)
    echo '<p><i>(You do not have any Red Jelly in Storage at this time.)</i></p>';
  else if($data['c'] < 9)
    echo '<p><i>(You only have ' . $data['c'] . ' Red Jelly in Storage at this time.)</i></p>';
  else
    echo '<ul><li><a href="?action=go">Give the wizard nine Red Jelly</a></li></ul>';
}
else
  exit();
?>
