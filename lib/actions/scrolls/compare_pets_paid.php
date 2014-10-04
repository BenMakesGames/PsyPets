<?php
if($okay_to_be_here !== true)
  exit();

$use_time = (int)$this_inventory['data'];

if($now < $use_time)
{
  $minutes = ceil(($use_time - $now) / 60);

  echo '<p>You can barely make out traces of words on the top of the scroll - they\'re impossible to read!  You get the feeling though that, given a little time, the words will make themselves visible again.</p><p><i>*cough*' . str_replace(' ', '', ShortScale::toCardinal($minutes)) . 'moreminutes*cough*</i></p>';
}
else if(count($userpets) == 0)
{
  echo '<p>At the top of the scroll is the instruction to write down the names of your pets. Having no pets of your own, you save the scroll for later.</p>';
}
else if(count($userpets) == 1)
{
  echo '<p>At the top of the scroll is the instruction to write down the names of your pets. You write down the name of your pet, but nothing happens.  Maybe one isn\'t enough...</p>';
}
else
{
  echo '<p>At the top of the scroll is the instruction to write down the names of your pets.  After doing so, the names begin to rearrange themselves!</p>';

  $stat_list = array(
    "str" => "strongest", "dex" => "most agile", "sta" => "toughest", "per" => "most perceptive",
    "int" => "intelligent", "wit" => "fastest-thinking",
    
    'bra' => 'most skilled in combat',
    'athletics' => 'most athletic',
    'stealth' => 'sneakiest',
    'sur' => 'most skilled hunting',
    'cra' => 'most crafting',
    'eng' => 'the most skilled electrical engineer',
    'mechanics' => 'the most skilled mechanical engineer',
    'smi' => 'most skilled smithing',
    'tai' => 'most skilled with a needle',
    'leather' => 'most skilled leather-worker',
    'pil' => 'most skilled as a pilot',
    'fishing' => 'most skilled fishing',
    'mining' => 'most skilled mining',
    'gathering' => 'most outdoorsy',
    'painting' => 'most skilled painting',
    'chemistry' => 'the most educated chemist',
    'carpentry' => 'the most skilled carpenter',
    'jeweling' => 'most skilled jeweling',
    'sculpting' => 'most skilled sculpting',
    'binding' => 'the best at channeling magic',
  );

  $average = array();

  $stat = array_rand($stat_list);

  $command = 'SELECT * FROM monster_pets WHERE user=' . quote_smart($user['user']) . ' AND location=\'home\' ORDER BY `' . $stat . '` DESC,(str+dex+sta+per+`int`+wit+bra+athletics+stealth+sur+gathering+fishing+mining+cra+painting+carpentry+jeweling+sculpting+eng+mechanics+chemistry+smi+tai+binding+pil) DESC';
  $my_pets = $database->FetchMultiple($command, 'fetching your pets');

  echo '<div style="margin-left: 2em;"><p>Who is ' . $stat_list[$stat] . '?</p><ol>';

  foreach($my_pets as $pet)
    echo '<li>' . $pet['petname'] . '</li>';
  
  echo '</ol></div>';

  $command = 'UPDATE monster_inventory SET data=\'' . ($now + 60 * 60) . '\' WHERE idnum=' . $this_inventory['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'updating use time');
?>
<p>Having read the list, the words begin to fade, and after a few seconds you're left with what appears to be a blank piece of paper, but you can feel that there's magic in it still!</p><p>Perhaps it just needs time to regenerate its power?</p><p><i>*cough*youhavetowaitanhour*cough*</i></p>
<?php
}
