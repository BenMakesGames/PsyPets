<?php
if($okay_to_be_here !== true)
  exit();

$alphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
$info = explode(";", $this_inventory["data"]);
$guessed = $info[0];
$word = $info[1];
$step = $info[2];

$took_action = false;

if($_GET["action"] == "new" || strlen($word) == 0)
{
  $dictionary = file("actions/games/hangman.txt");

  $guessed = "";
  $word = trim($dictionary[array_rand($dictionary)], "\n\r\t ");
  $step = 0;
   
  $database->FetchNone("UPDATE monster_inventory SET data=" . quote_smart(";$word;0") . " WHERE idnum=" . $_GET["idnum"] . " LIMIT 1");
}
else if($_GET["action"] == "letter")
{
  $letter = strtoupper($_GET["letter"]);
  if(strlen($letter) == 1 && strpos($alphabet, $letter) !== false)
  {
    $guessed .= $letter;
    
    if(strpos($word, $letter) === false)
      $step++;
     
    $command = 'UPDATE monster_inventory SET data=' . quote_smart($guessed . ';' . $word . ';' . $step) . ' WHERE idnum=' . $_GET['idnum'] . ' LIMIT 1';
    $database->FetchNone($command, 'Hangman');

    $took_action = true;
  }
}

$letters = 0;
?>
<p><img src="gfx/game/hangman_<?= $step ?>.png" /></p>
<pre>word : <?php
for($i = 0; $i < strlen($word); ++$i)
{
  $letter = $word{$i};
  if($step == 6 || strpos($guessed, $letter) !== false)
  {
    if($step < 6)
      $letters++;

    echo "$letter ";
  }
  else
    echo "_ ";
}

echo "\n\nguess: ";

if($step == 6 || $letters == strlen($word))
  echo "<s>";

for($i = 0; $i < strlen($alphabet); ++$i)
{
  $letter = $alphabet{$i};
  if($step == 6 || $letters == strlen($word) || strpos($guessed, $letter) !== false)
    echo "$letter ";
  else
    echo "<a href=\"itemaction.php?idnum=" . $_GET["idnum"] . "&action=letter&letter=$letter\">$letter</a> ";
}

if($step == 6 || $letters == strlen($word))
  echo "</s>";

if($letters == strlen($word))
{
  $played_game = $took_action;
  echo "\n\nWIN!  ^_^";
}
else if($step == 6)
{
  $played_game = $took_action;
  echo "\n\nLOSE! x_x";
}

if($played_game)
{
  require_once 'commons/questlib.php';

  $hangman = get_quest_value($user['idnum'], 'hangman games');
  $play_count = (int)$hangman['value'] + 1;

  if($hangman === false)
    add_quest_value($user['idnum'], 'hangman games', $play_count);
  else
    update_quest_value($hangman['idnum'], $play_count);

  $badges = get_badges_byuserid($user['idnum']);
  if($badges['hangman'] == 'no' && $play_count >= 10)
  {
    set_badge($user['idnum'], 'hangman');

    $body = 'We\'re glad you enjoyed our teeny, tiny Hangman game!  Be sure to check out our other fantastic titles:<br /><br />' .
            '* {i Pong Minigame}<br /><br />' .
            '{i}(You earned the Hangman Badge!){/}';

    psymail_user($user['user'], 'ttgcorp', 'Thanks for playing Hangman!', $body);
  }
  else if($badges['execute'] == 'no' && $play_count >= 20)
  {
    set_badge($user['idnum'], 'execute');

    echo '<p><i>(You received the Executioner Badge!)</i></p>';
  }
}

echo "\n\n<a href=\"itemaction.php?idnum=" . $_GET["idnum"] . "&action=new\">reset?</a>";
?></pre>
