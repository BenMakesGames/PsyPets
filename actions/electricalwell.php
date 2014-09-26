<?php
if($okay_to_be_here !== true)
  exit();

$data = (int)$this_inventory['data'];

if($now > $data)
{
  $data = $now + mt_rand(45 * 60, 75 * 60);

  $command = 'UPDATE monster_inventory SET data=\'' . $data . '\' WHERE idnum=' . $this_inventory['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'updating electrical well talk timer');

  $type = mt_rand(1, 4);

  if($type == 1) // food
    $command = 'SELECT makes,ingredients FROM monster_recipes ORDER BY RAND() LIMIT 1';
  else if($type == 2) // craft
    $command = 'SELECT makes,ingredients FROM psypets_crafts ORDER BY RAND() LIMIT 1';
  else if($type == 3) // invention
    $command = 'SELECT makes,ingredients FROM psypets_inventions ORDER BY RAND() LIMIT 1';
  else if($type == 4) // smith
    $command = 'SELECT makes,ingredients FROM psypets_smiths ORDER BY RAND() LIMIT 1';

  $data = $database->FetchSingle($command, 'fetching secret!');  

  echo '<img src="gfx/npcs/electricalwell.png" align="right" width="128" height="128" alt="(An Electrical Well)" />';
  include 'commons/dialog_open.php';

  $intros = array(
    'Hey!  Know what I heard?',
    'Wanna hear something interesting?',
    'I overheard something...'
  );
  
  $outros = array(
    'Well, that\'s what I heard, anyway.',
    'I\'m pretty sure that\'s how it went.',
    'But, you know, that\'s just what I heard.',
  );
  
  echo '<p>' . $intros[array_rand($intros)] . '</p>';
  
  if($type == 1)
    echo '<p>I heard that you can make ' . str_replace(',', ', ', $data['makes']) . ' in your kitchen if you combine ' . str_replace(',', ', ', $data['ingredients']) . '.</p>';
  else
    echo '<p>I heard that pets can make ' . $data['makes'] . ' if they have ' . str_replace(',', ', ', $data['ingredients']) . ' available.</p>';

  echo '<p>' . $outros[array_rand($outros)] . '</p>';

  include 'commons/dialog_close.php';
}
else
  echo 'If Electrical Wells were capable of giving a hard stare, this one would be giving <em>you</em> one.  Apparently it\'s not feeling very talkative at the moment.</p><p>';

?>
