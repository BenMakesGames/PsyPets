<?php
$wiki = 'Wheel of Fate';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/itemlib.php';
require_once 'commons/formatting.php';
require_once 'commons/timelib.php';
require_once 'commons/statlib.php';

if($user['karma'] == 0)
{
  header('Location: /myhouse.php');
  exit();
}

$karma_prizes = array(
  1 => array('cost' => 1, 'prizes' => array('Potion Ticket', 'Hyperactive Maze Piece Summoning Scroll', 'Magic Three-hour Hourglass')),
  2 => array('cost' => 1, 'prizes' => array('Happy Fun Time Potion', 'Child\'s Play', 'Weak Panacea')),
  3 => array('cost' => 2, 'prizes' => array(
    'Mysterious Bobbin', // tailory
    'Nemesis',           // adventuring
    'Dynamic Catalyst',  // chemistry
    'Eagle\'s Claw',     // hunting
  )),
  4 => array('cost' => 3, 'prizes' => array(
    'Big Bang',
    'Scroll of Musical Instrument Summoning',
    'Fertility Draught',
    'Loaf',
  )),
);

if($_POST['action'] == 'Spin!')
{
  $id = (int)$_POST['id'];
  
  if(!array_key_exists($id, $karma_prizes))
  {
    $message_list[] = '<span class="failure">Oops!  You forgot to select the amount of Karma to use.</span>';
  }
  else if($karma_prizes[$id]['cost'] > $user['karma'])
  {
    $message_list[] = '<span class="failure">You do not have enough Karma :(</span>';
  }
  else
  {
    require_once 'commons/statlib.php';
  
    $cost = $karma_prizes[$id]['cost'];
    $prizes = $karma_prizes[$id]['prizes'];
  
    if($user['karma'] == $cost)
      $command = 'UPDATE monster_users SET karma=0 WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
    else
      $command = 'UPDATE monster_users SET karma=karma-' . $cost . ' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';

    $database->FetchNone($command, 'taking karma');

    $user['karma'] -= $cost;

    record_stat($user['idnum'], 'Karma Spent', $cost);
    
    $item = $prizes[array_rand($prizes)];
    
    add_inventory($user['user'], '', $item, $user['display'] . ' won this from the Wheel of Fate.', 'storage/incoming');
    flag_new_incoming_items($user['user']);

    $CONTENT['messages'][] = '<span class="success">You received <strong>' . $item . '</strong>!  Find it in <a href="/incoming.php">Incoming</a>.</span>';
  }
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Wheel of Fate</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4>Wheel of Fate</h4>
     <p>You have <?= $user['karma'] ?> Karma.  How many will you spend on... <strong><em>the Wheel of Fate!?!</em></strong> <i>*dramatic chord*</i></p>
     <form method="post">
     <table>
      <thead>
       <tr class="titlerow"><th></th><th>Karma</th><th>Possible Prizes</th></tr>
      </thead>
      <tbody>
<?php
$rowclass = begin_row_class();

foreach($karma_prizes as $id=>$karma)
{
?>
       <tr class="<?= $rowclass ?>">
        <td><input type="radio" name="id" value="<?= $id ?>" /></td>
        <td class="centered"><?= $karma['cost'] ?></td>
        <td><?php
  foreach($karma['prizes'] as $prize)
    echo '<a href="encyclopedia2.php?item=' . link_safe($prize) . '">' . $prize . '</a><br />';
?></td>
       </tr>
<?php
  $rowclass = alt_row_class($rowclass);
}
?>
      </tbody>
     </table>
     <p><input type="submit" name="action" value="Spin!" /></p>
     </form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
