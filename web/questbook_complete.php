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
    <li><a href="questbook.php">Active Quests</a></li>
    <li class="activetab"><a href="questbook_complete.php">Completed Quests</a></li>
  </ul>
<?php
$completed = 0;

foreach($QUEST_DESCRIPTIONS as $key=>$description)
{
  if(array_key_exists($key, $quest_values) && $quest_values[$key]['value'] == $description['done_step'])
  {
    $completed++;
  
    echo '<h5>',  $description['title'], '</h5><p>', $description['description'], '</p><ul class="plainlist">';

    foreach($description['steps'] as $step=>$desc)
      echo '<li class="dim"><img src="//' . $SETTINGS['static_domain'] . '/gfx/forum/check_white.png" width="16" height="16" class="inlineimage" alt="done:" /> ', $desc, '</li>';

    if($description['other_steps'])
      echo '<li class="dim"><img src="//' . $SETTINGS['static_domain'] . '/gfx/forum/check_white.png" width="16" height="16" class="inlineimage" alt="done:" /> ', $description['other_steps'], '</li>';
    
    echo '</ul>';
  }
}

if($completed == 0)
  echo '<p>You haven\'t completed any quests.</p>';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
