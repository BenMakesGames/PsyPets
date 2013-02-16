<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$whereat = 'home';
$wiki = 'Shrine';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/inventory.php';
require_once 'commons/checkpet.php';
require_once 'commons/messages.php';
require_once 'commons/houselib.php';
require_once 'commons/shrinelib.php';

if(!addon_exists($house, 'Shrine'))
{
  header('Location: /myhouse.php');
  exit();
}

$shrine_hours = simulate_shrine($user['idnum']);

$shrine = get_shrine_byuserid($user['idnum']);

if($shrine === false)
  $shrine = create_shrine($user['idnum']);

if($shrine === false)
  die('Error loading or creating Shrine.  This shouldn\'t happen unless the game is having weird database problems >_>');

$spells = take_apart(';', $shrine['spells']);

$spellid = (int)$_GET['spell'];
$this_spell = false;
$spell_data = false;
$spell_index = false;

foreach($spells as $index=>$spell)
{
  $data = explode(',', $spell);

  if($spellid == $data[0] && $data[1] >= $SPELL_DETAILS[$data[0]][0])
  {
    $this_spell = $SPELL_DETAILS[$data[0]];
    $spell_data = $data;
    $spell_index = $index;
    break;
  }
}

if($this_spell === false)
{
  header('Location: /myhouse/addon/shrine.php?notready');
  exit();
}

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $user['display'] ?>'s House &gt; Shrine</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/myhouse.php"><?= $user['display'] ?>'s House</a> &gt; <a href="/myhouse/addon/shrine.php">Shrine</a> &gt; <?= $this_spell[1] ?> &gt; Cast</h4>
<?php
room_display($house);

$yes_yes_that_is_fine = true;

$file = WEB_ROOT . '/spells/' . $this_spell[2];

if(file_exists($file))
{
  require $file;

  if($FINISHED_CASTING === true)
  {
    $spell_data[1] = floor(($spell_data[1] - $this_spell[0]) / 2);
    if($spell_data[1] > 0)
      $spells[$spell_index] = $spell_data[0] . ',' . $spell_data[1];
    else
      unset($spells[$spell_index]);

    $command = 'UPDATE psypets_shrines SET spells=' . quote_smart(implode(';', $spells)) . ' WHERE userid=' . $shrine['userid'] . ' LIMIT 1';
    fetch_none($command, 'expending spell');

    require_once 'commons/statlib.php';

    if(record_stat_with_badge($user['idnum'], 'Shrine Spells Cast', 1, 10, 'wizardhat'))
      echo '<p><i>(You received the Powerful Wizard badge!)</i></p>';
  }
}
else
  echo '<p>Error loading spell action.  Please notify <a href="/admincontact.php">an administrator</a>.</p>';
?>
<ul><li><a href="/myhouse/addon/shrine.php">Back to your Shrine</a></li></ul>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
