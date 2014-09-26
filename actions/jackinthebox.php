<?php
if($okay_to_be_here !== true)
  exit();

$data = explode(';', $this_inventory['data']);
$line = (int)$data[0];
$next_time = (int)$data[1];

$lines = array(
  0 => "'Round and 'round the mulberry bush,",
  1 => "The monkey chased the weasel,",
  2 => "The monkey thought 'twas all in jest",
  3 => "*Pop!* goes the weasel."
);

$AGAIN_WITH_ANOTHER = true;
$AGAIN_WITH_SAME = true;

if($now >= $next_time)
{
  echo '<img src="gfx/items/musicnote.png" /> ' . $lines[$line];

  $line++;
  $next_time = $now + (5 * 60);

  if($line == 4)
  {
    if(mt_rand(1, 2) == 1)
    {
      $new_item = 'Monkey-in-a-Box';
      $animal = 'monkey';
      $comment = 'Wait, shouldn\'t it be a weasel?';
    }
    else
    {
      $new_item = 'Weasel-in-a-Box';
      $animal = 'weasel';
      $comment = 'Or maybe it\'s a monkey.  It\'s hard to tell.';
    }

    delete_inventory_byid($this_inventory['idnum']);
    add_inventory($this_inventory['user'], $this_inventory['creator'], $new_item, $this_inventory['message'], $this_inventory['location']);

    echo '</p><p><i>A little ' . $animal . ' wearing a red hat pops out of the box';

    if(substr($this_inventory['location'], 0, 4) == 'home')
    {
      $command = 'SELECT * FROM monster_pets WHERE user=' . quote_smart($user['user']) . ' AND location=\'home\' AND dead=\'no\' AND sleeping=\'no\' AND food>0 AND energy>0 ORDER BY RAND() LIMIT 1';
      $pet = $database->FetchSingle($command, '??? in the box!');
  
      if($pet !== false)
      {
        echo ', to ' . $pet['petname'] . '\'s great amusement';

        gain_safety($pet, max_safety($pet));
        gain_love($pet, max_love($pet));
        gain_esteem($pet, max_esteem($pet));

        save_pet($pet, array('safety', 'love', 'esteem'));
      }
    }
  
    echo '.  ' . $comment . '</i></p>';
  }
  else
  {
    $command = 'UPDATE monster_inventory SET data=\'' . ($line == 4 ? 0 : $line) . ';' . $next_time . '\' WHERE idnum=' . $this_inventory['idnum'] . ' LIMIT 1';
    $database->FetchNone($command, 'singing jack-in-the-box song');
  }
}
else
  echo '<i>The anticipation about what may happen next is far too exciting!  Best wait until you\'ve calmed down a little before proceeding... &gt;_&gt;</i>';
?>
