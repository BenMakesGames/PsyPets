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
  $dictionary = array(
      'ABHORRENT', 'ABNORMAL', 'ADORABLE', 'ANTIDOTE', 'APPARITION', 'ARCHAIC',
      'ARMOIRE', 'ARSENAL', 'AUSPICIOUS',
      'BANTAM', 'BARROW', 'BEAK', 'BEDLAM', 'BELLOW', 'BLASPHEMOUS', 'BLAZING',
      'BLITZ', 'BLOUSE', 'BROUHAHA', 'BOUNTIFUL', 'BRILLIANT', 'BRIMSTONE',
      'CACOPHONY', 'CALENDAR', 'CANDY', 'CASTAWAY', 'CATERPILLAR', 'CHASM',
      'CHIMNEY', 'CHOCOLATE', 'CIPHER', 'CIVILIZATION', 'COCKTAIL',
      'COLANDER', 'COLLIDE', 'COMMERCIALIZATION', 'CONFOUND', 'CORRODE',
      'CUMULATIVE',
      'DAYBREAK', 'DAZZLE', 'DEADLY', 'DEERSKIN', 'DELICIOUS', 'DIMINISH',
      'DIRTY', 'DISCO', 'DISEASE', 'DIZZY', 'DREAMLAND', 'DROWNED',
      'ELBOW', 'ENDEARING', 'ENDED', 'ENDOWED', 'ENGORGED', 'ENIGMA',
      'ENTRAILS', 'ERADICATE', 'ETERNAL', 'EVASIVE', 'EQUAL', 'EXAMPLE',
      'EXCITING', 'EXEMPLIFY', 'EXTRATERRESTRIAL',
      'FABRIC', 'FABULOUS', 'FAITHFUL', 'FEBRUARY', 'FISHERMAN', 'FLOOZIE',
      'FLOUNDER', 'FLUMMOX', 'FLUTE', 'FOLLOW', 'FOSSIL', 'FRUIT', 'FUSION',
      'GELATIN', 'GHOSTLY', 'GLANCE', 'GOBLIN', 'GOOEY', 'GRAMMAR', 'GREASE',
      'GROWN', 'GUMMY',
      'HALCYON', 'HALLWAY', 'HALO', 'HAMMOCK', 'HAPHAZARDLY', 'HARROW', 'HAZELNUT',
      'HICCUP', 'HONEY', 'HORROR', 'HORSEBACK', 'HOWL', 'HUBBUB',
      'IDIOTIC', 'INCONCEIVABLE', 'INCREDULOUS', 'INFINITY', 'INTERRUPTED',
      'IRKSOME', 'IRRADIATE', 'IRREDEEMABLE',
      'JACKPOT', 'JAGGED', 'JAUNTY', 'JAZZED', 'JELLY', 'JESTER', 'JUBILANT',
      'JUICE', 'JUNK',
      'KABOB', 'KARMA', 'KATYDID', 'KAZOO', 'KEEPSAKE', 'KEROSENE', 'KEYHOLE',
      'KIDNEY', 'KILOBYTE', 'KINSMEN', 'KNAVE', 'KNITTING', 'KNOCKOUT', 'KNOCKOFF',
      'KNOLL', 'KNUCKLE', 'KUDOS', 'KUMQUAT',
      'LABORIOUSLY', 'LANGUISH', 'LAUGHINGLY', 'LITHE', 'LOBOTOMY',
      'LUBRICIOUS', 'LUMINOUS', 'LUSCIOUS',
      'MACABRE', 'MACHETE', 'MAELSTROM', 'MAGICIAN', 'MAINLAND', 'MARMALADE',
      'MASK', 'MINION', 'MOLASSES', 'MOUNTAIN', 'MOXIE', 'MUSEUM', 'MYSTERIOUS',
      'NAGGER', 'NANOTUBE', 'NARRATOR', 'NAUGHT', 'NEARLY', 'NEBULA', 'NECK',
      'NECROMANCER', 'NEOLITHIC', 'NIBBLE', 'NOBODY', 'NOISOME', 'NUMBING',
      'OBVIOUS', 'OCCULT', 'OCCUPANT', 'OCTAGONAL', 'OKRA', 'OMNISCIENT', 'OPPORTUNITY',
      'ORACULAR', 'ORCHID', 'ORGANISM', 'OTHERWORLDLY', 'OUTSOURCE', 'OVERABUNDANT',
      'PENULTIMATE', 'PLAGUED', 'PITCH', 'POOCH', 'POWDER', 'PROLIFERATE', 'PRATTLE',
      'PROTON', 'PTERODACTYL', 'PUMPKIN', 'PUSHY', 'PUTRESCENT', 'PYROMANIA',
      'QUADRILATERAL', 'QUARRY', 'QUARTER', 'QUAKE', 'QUEASY', 'QUEER', 'QUESTION',
      'QUICHE', 'QUIVER', 'QUOTA', 'QUOTE',
      'RAINBOW', 'RAMBUNCTIOUS', 'RANCOROUS', 'RANSACK', 'RAPSCALLION',
      'REPOSE', 'REPOST', 'REPRESENT', 'RISKY', 'ROADKILL', 'ROCKETEER',
      'ROSEMARY', 'RUPTURE', 'RUSH',
      'SAGA', 'SALACIOUS', 'SALSA', 'SASHAY', 'SCALLYWAG', 'SECRECY', 'SERIOUSLY',
      'SILLY', 'SKETCHY', 'SPASM', 'STAGGER', 'STICKY', 'STRATOSPHERE',
      'STRIKING', 'SUNDRY', 'SUPERABUNDANT', 'SUPPLE', 'SQUIRM', 'SYNTAX', 'SYPHON',
      'THURSDAY', 'TOOTHSOME', 'TOXIN', 'TUNIC', 'TRANQUILITY', 'TRANSCENDENTAL',
      'TREASURE', 'TULIP', 'TWITCH',
      'UBIQUITOUS', 'ULTIMATE', 'UMBRELLA', 'UNDONE', 'UNEXPECTED', 'UNMISTAKABLE',
      'UPDRAFT', 'UPLIFTING', 'UTERUS', 'UTILITY',
      'VALLEY', 'VANGUARD', 'VANILLA', 'VAULT', 'VENOM', 'VENTURE', 'VERILY', 'VEXING',
      'VISCID', 'VIVID', 'VOID', 'VOLCANO', 'VOLLEY', 'VOLUPTUOUS', 'VORACIOUS',
      'WAGON', 'WANTON', 'WARHEAD', 'WARRIOR', 'WASTE', 'WAYPOINT', 'WEIRD', 'WELCOME',
      'WHIRL', 'WHISPER', 'WING', 'WOLF', 'WONDER', 'WOMAN', 'WORLD', 'WRITHE',
      'XEROX', 'XYLOPHONE',
      'YACHT', 'YELLOW', 'YAMMER', 'YODEL', 'YOUNGSTER', 'YOWLING', 'YUMMY',
      'ZEALOUS', 'ZEBRA', 'ZILCH', 'ZIPPER',
  );

  $guessed = "";
  $word = $dictionary[array_rand($dictionary)];
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
