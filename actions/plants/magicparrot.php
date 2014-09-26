<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/questlib.php';

$harvest = get_quest_value($user['idnum'], 'Magic Parrot');

$day = 60 * 60 * 20;

if($harvest === false)
{
  add_quest_value($user['idnum'], 'Magic Parrot', $now + $day);

  add_inventory($user['user'], '', 'Green Cracker', 'Given to ' . $user['display'] . ' by a Magic Alabaster Parrot', $this_inventory['location']);
  echo '
    <p>"Bwaaaak!  ' . $user['display'] . ' wanna cracker?  Bwaaaak!"</p>
    <p><i>(You received a Green Cracker.)</i></p>
  ';
}
else if($now > (int)$harvest['value'])
{
  update_quest_value($harvest['idnum'], $now + $day);

  add_inventory($user['user'], '', 'Green Cracker', 'Given to ' . $user['display'] . ' by a Magic Alabaster Parrot', $this_inventory['location']);
  echo '
    <p>"Bwaaaak!  ' . $user['display'] . ' wanna cracker?  Bwaaaak!"</p>
    <p><i>(You received a Green Cracker.)</i></p>
  ';
}
else
{
  $command = 'SELECT * FROM psypets_profilecomments WHERE userid=' . $user['idnum'] . ' ORDER BY idnum DESC LIMIT 10';
  $comments = $database->FetchMultiple($command, 'residentprofile.php');

  if(count($comments) > 0 && mt_rand(1, 4) == 4)
  {
    $comment = $comments[array_rand($comments)];
    echo '<p>"Bwaaaak!  ' . $comment['comment'] . '  Bwaaaak!"</p>';
  }
  else
  {
    switch(mt_rand(1, 14))
    {
      case 1: echo '<p>"Bwaaaak!  I\'ll take a potato chip....and eat it!  Bwaaaak!"</p>'; break;
      case 2: echo '<p>"Bwaaaak!  Hello!  My name is Inigo Montoya!  You killed my father!  Prepare to die!  Bwaaaak!"</p>'; break;
      case 3: echo '<p>"Bwaaaak!  I have a very bad feeling about this!  Bwaaaak!"</p>'; break;
      case 4: echo '<p>"Bwaaaak!  *Ring-ring!  Ring-ring!*  Bwaaaak!"</p>'; break;
      case 5: echo '<p>"Bwaaaak!  It\'s over nine-thousand!  Bwaaaak!"</p>'; break;
      case 6: echo '<p>"Bwaaaak!  I attack the darkness!  Bwaaaak!"</p>'; break;
      case 7: echo '<p>"Bwaaaak!  Longcat is long!  Bwaaaak!"</p>'; break;
      case 8: echo '<p>"Bwaaaak!  THIS!  IS!  SPARTA!!  Bwaaaak!"</p>'; break;
      case 9: echo '<p>"Bwaaaak!  I\'m on a boat, motherfucker!  Bwaaaak!"</p>'; break;
      case 10: echo '<p>"Bwaaaak!  I don\'t even use the internet!  Bwaaaak!"</p>'; break;
      case 11: echo '<p>"Bwaaaak!  All your base are belong to us!  Bwaaaak!"</p>'; break;
      case 12: echo '<p>"Bwaaaak!  OBJECTION!!  Bwaaaak!"</p>'; break;
      case 13: echo '<p>"Bwaaaak!  It\'s a trap!  Bwaaaak!"</p>'; break;
      case 14: echo '<p>"Bwaaaak!  Ma-ia-hii!  Ma-ia-huu!  Ma-ia-ho!  Ma-ia-haha!  Bwaaaak!"</p>'; break;
    }
  }
}

$AGAIN_WITH_SAME = true;
?>
