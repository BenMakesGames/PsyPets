<?php
$whereat = 'graveyard';
$wiki = 'The_Graveyard';
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/userlib.php';
require_once 'commons/gravelib.php';
require_once 'commons/formatting.php';
require_once 'commons/questlib.php';
require_once 'commons/messages.php';

$page = (int)$_GET['plot'];

$message = '';

if($_GET['personal'] == 'yes')
{
  $graveyardpages = get_graveyard_pages_by_user($user['idnum']);

  if($graveyardpages == 0)
  {
    $fetch_graveyard = true;
    $message = '<p class="failure">None of your pets have been buried here.</p>';
  }
  else
  {
    $fetch_graveyard = false;

    if($page < 1)
      $page = 1;
    else if($page > $graveyardpages)
      $page = $graveyardpages;

    $tombstones = get_graveyard_by_user($page, $user['idnum']);
  }
}
else
  $fetch_graveyard = true;

if($fetch_graveyard)
{
  $graveyardpages = get_graveyard_pages();

  if($page < 1)
    $page = 1;
  else if($page > $graveyardpages)
    $page = $graveyardpages;

  $tombstones = get_graveyard($page);

  $paginate = paginate($graveyardpages, $page, 'graveyard.php?plot=%s');
}
else
  $paginate = paginate($graveyardpages, $page, 'graveyard.php?personal=yes&plot=%s');

$raise_quest = get_quest_value($user['idnum'], 'raise zombie');
if($raise_quest !== false)
  $raise_one = ($raise_quest['value'] > 0);
else
  $raise_one = false;

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; The Graveyard</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4>The Graveyard</h4>
<?php
if(array_key_exists('msg', $_GET))
  echo '<p>' . form_message(explode(',', $_GET['msg'])) . '</p>';

echo $message;

if($raise_one)
  echo '<p>Click on the tombstone of the pet you would like to raise.</p>';

if($fetch_graveyard)
  echo '<ul><li><a href="graveyard.php?personal=yes">Show only tombstones belonging to pets I owned</a></li></ul>';
?>
     <p>Plot: <?= $paginate ?></p>
     <table>
      <tr>
<?php
$i = 1;
foreach($tombstones as $tomb)
{
  if(($i - 1) % 8 == 0 && $i != 1)
    echo '<tr>';

  $owner = get_user_byid($tomb['ownerid']);
  if($owner === false)
    $owner['display'] = 'Departed #' . $tomb['ownerid'];

  $epitaph = htmlentities($tomb['epitaph'], ENT_QUOTES, 'UTF-8');

  $mouseover = "onmouseover=\"Tip('<table><tr><th bgcolor=\\'#f0f0f0\\'>Name</th><td>" . tip_safe($tomb['petname']) . "</td></tr><tr><th bgcolor=\\'#f0f0f0\\'>Owner</th><td>" . tip_safe($owner['display']) . "</td></tr><tr><th bgcolor=\\'#f0f0f0\\' valign=\\'top\\'>Epitaph</th><td>" . tip_safe($epitaph) . "</td></tr></table>');\"";

  $textepitaph = $owner['display'] . "'s " . $tomb['petname'];
  if(strlen($tomb['epitaph']) > 0)
    $textepitaph .= ': ' . $epitaph;

  if($tomb['ghost'] == 'yes' && date('m') == 10)
    $filename = 'gfx/pets/dead/tombstone_' . ($tomb['tombstone'] < 10 ? ('0' . $tomb['tombstone']) : $tomb['tombstone']) . '_ghost.gif';
  else
    $filename = 'gfx/pets/dead/tombstone_' . ($tomb['tombstone'] < 10 ? ('0' . $tomb['tombstone']) : $tomb['tombstone']) . '.png';

  if($tomb['tombstone'] == 0)
    echo '<td><a href="fillgrave.php?plot=' . $page . '&id=' . $tomb['idnum'] . '"><img src="' . $filename . '" width="48" height="48" alt="' . $textepitaph . '" ' . $mouseover . ' border="0" /></a></td>';
  else if($tomb['ownerid'] == $user['idnum'])
    echo '<td><a href="editepitaph.php?id=' . $tomb['idnum'] . '"><img src="' . $filename . '" width="48" height="48" alt="' . $textepitaph . '" style="border: 2px;" ' . $mouseover . ' /></a></td>';
  else if($raise_one)
    echo '<td><a href="graveyard_raise.php?id=' . $tomb['idnum'] . '"><img src="' . $filename . '" width="48" height="48" alt="' . $textepitaph . '" ' . $mouseover . ' border="0" /></a></td>';
  else
    echo '<td><img src="' . $filename . '" width="48" height="48" alt="' . $textepitaph . '" ' . $mouseover . ' /></td>';

  if($i % 8 == 0)
    echo '</tr>';

  $i++;
}
?>
      </tr>
     </table>
     <p>Plot: <?= $paginate ?></p>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
