<?php
$wiki = 'Quest_Log';
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/userlib.php';
require_once 'commons/messages.php';
require_once 'commons/questlib.php';
require_once 'commons/questdesclib.php';

$quest_values = get_quest_values_byuserid($user['idnum']);

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $user['display'] ?>'s Quest Log</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php
include 'commons/header_2.php';
?>
  <h4><?= $user['display'] ?>'s Quest Log</h4>
  <ul class="tabbed">
    <li class="activetab"><a href="questbook.php">Active Quests</a></li>
    <li><a href="questbook_complete.php">Completed Quests</a></li>
  </ul>
<?php
$inprogress = 0;

foreach($QUEST_DESCRIPTIONS as $key=>$description)
{
  if(array_key_exists($key, $quest_values) && $quest_values[$key]['value'] < $description['done_step'])
  {
    $inprogress++;

    echo '<h5>',  $description['title'], '</h5><p>', $description['description'], '</p><ul class="plainlist">';

    $show_other_steps = true;

    foreach($description['steps'] as $step=>$desc)
    {
      if($step < $quest_values[$key]['value'])
        echo '<li class="dim"><img src="//' . $SETTINGS['static_domain'] . '/gfx/forum/check_white.png" width="16" height="16" class="inlineimage" alt="done:" /> ', $desc, '</li>';
      else
      {
        echo '<li><img src="//' . $SETTINGS['static_domain'] . '/gfx/forum/arrow_blue.png" width="16" height="16" class="inlineimage" alt="current step:" /> ', $desc, '</li>';
        $show_other_steps = false;
        break;
      }
    }

    if($show_other_steps && $step < $description['done_step'] && $description['other_steps'])
      echo '<li><img src="//' . $SETTINGS['static_domain'] . '/gfx/forum/arrow_blue.png" width="16" height="16" class="inlineimage" alt="current step:" /> ', $description['other_steps'], '</li>';

    echo '</ul>';
  }
}

if($inprogress == 0)
  echo '<p>You\'re not in the middle of any quests.</p>';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
