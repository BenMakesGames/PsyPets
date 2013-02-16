<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$whereat = 'home';
$wiki = 'Eiffel_Tower';
$require_petload = 'no';
$THIS_ROOM = 'Eiffel Tower';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/inventory.php';
require_once 'commons/messages.php';
require_once 'commons/houselib.php';
require_once 'commons/questlib.php';
require_once 'commons/favorlib.php';
require_once 'commons/messages.php';

if(!addon_exists($house, 'Eiffel Tower'))
{
  header('Location: /myhouse.php');
  exit();
}

$bastille_day = (date('M j') == 'Jul 14');

if($bastille_day)
{
  $freebies = get_quest_value($user['idnum'], 'Bastille Day');
  if($freebies === false)
    add_quest_value($user['idnum'], 'Bastille Day', 0);

  $freebies = get_quest_value($user['idnum'], 'Bastille Day');
}

if($_GET['action'] == 'setprofile')
{
  $user['profile_wall'] = 'profiles/eiffeltower.png';
  $command = 'UPDATE monster_users SET profile_wall=\'profiles/eiffeltower.png\',profile_wall_repeat=\'no\' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
  fetch_none($command, 'setting profile background');
}
else if($_GET['action'] == 'clearprofile')
{
  $user['profile_wall'] = '';
  $command = 'UPDATE monster_users SET profile_wall=\'\' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
  fetch_none($command, 'clearing profile background');
}
else if($_GET['action'] == 'setavatar')
{
  $user['graphic'] = 'national/france.png';
  $command = 'UPDATE monster_users SET graphic=\'national/france.png\' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
  fetch_none($command, 'setting avatar');
}
else if($_POST['action'] == 'Celebrate!' && $bastille_day && $now >= $freebies['value'] + 60 * 60)
{
  $possible_items = array(
    'Redsberry Wine', 'Blueberry Wine',
    'Star of Pearl Sparklers', 'Ruby Star Sparklers',
  );

  update_quest_value($freebies['idnum'], $now);
  $freebies['value'] = $now;
  
  $this_item = $possible_items[array_rand($possible_items)];
  
  add_inventory($user['user'], 'u:' . $user['idnum'], $this_item, 'Bastille Day!', 'home');

  $message = '<p class="success">You received a ' . $this_item . '!</p>';
}
else if($_POST['action'] == 'Get One (300 Favor)')
{
  if($user['favor'] >= 300)
  {
    $id = add_inventory($user['user'], 'u:' . $user['idnum'], 'Eiffel Tower Blueprint', 'Bought from an Eiffel Tower', 'storage/incoming');

    flag_new_incoming_items($user['user']);

    spend_favor($user, 300, 'bought item - Eiffel Tower', $id);
    
    header('Location: /myhouse/addon/eiffel_tower.php?msg=144:Eiffel%20Tower%20Blueprint');
    exit();
  }
  else
    $CONTENT['messages'][] = '<span class="failure">You don\'t have enough Favor - sorry T_T  (<a href="/buyfavors.php">Want to get some?</a>)</span>';
}

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $user['display'] ?>'s House &gt; Eiffel Tower</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/myhouse.php"><?= $user['display'] ?>'s House</a> &gt; Eiffel Tower</h4>
<?php
if(strlen($_GET['msg']) > 0)
  $error_message = form_message(explode(',', $_GET['msg']));

if($error_message)
  echo "<p>$error_message</p>";

echo $message;
room_display($house);
echo '<img src="//' . $SETTINGS['static_domain'] . '/gfx/avatars/national/france.png" width="48" height="48" align="right" alt="Drapeau Tricolore" />',
     '<ul>';

if($user['profile_wall'] == 'profiles/eiffeltower.png')
  echo '<li><i class="dim">Your profile background is the Eiffel Tower</i></li>';
else
  echo '<li><a href="/myhouse/addon/eiffel_tower.php?action=setprofile">Set profile background to Eiffel Tower</a></li>';

if($user['profile_wall'] != '')
  echo '<li><a href="/myhouse/addon/eiffel_tower.php?action=clearprofile">Clear profile background</a></li>';

if($user['graphic'] == 'national/france.png')
  echo '<li><i class="dim">Your avatar is the Drapeau Tricolore</i></li>';
else
  echo '<li><a href="/myhouse/addon/eiffel_tower.php?action=setavatar">Set avatar to the Drapeau Tricolore</a></li>';

echo '</ul>';

if($bastille_day)
{
  echo '<h4><span style="color:#369;">Ba</span><span style="color:#666;">stille</span> <span style="color:#c00;">Day!</span></h4>';

  if($now < $freebies['value'] + 60 * 60)
    echo '<p>You may collect another Bastille Day item in ' . Duration($freebies['value'] + 60 * 60 - $now, 2) . '.</p>';
  else
    echo '<form action="/myhouse/addon/eiffel_tower.php" method="post"><p><input type="submit" name="action" value="Celebrate!" /></p></form>';
}
else
  echo '<p>Don\'t forget to celebrate Bastille Day (July 14th)!</p>';

$blueprint = get_item_byname('Eiffel Tower Blueprint');
?>
<h4>Commission Another</h4>
<p>You may get additional <?= item_text_link('Eiffel Tower Blueprint'); ?>s for 300 Favor<a href="/wherethemoneygoes.php" class="help">?</a> each; you currently have <?= $user['favor'] ?>.</p>
<ul><li><a href="/buyfavors.php">Buy more Favor</a></li></ul>
<table>
<thead>
<tr><th></th><th>Item</th><th></th></tr>
</thead>
<tbody>
<tr>
<td><?= item_display($blueprint) ?></td>
<td><?= $blueprint['itemname'] ?></td>
<td><?php
if($user['favor'] >= 300)
{
  echo '
    <form method="post">
    <input type="submit" name="action" value="Get One (300 Favor)" class="bigbutton" />
    </form>
  ';
}
else
  echo '<input type="button" value="Get One (300 Favor)" disabled="disabled" class="bigbutton" />';
?></td>
</tr>
</tbody>
</table>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
