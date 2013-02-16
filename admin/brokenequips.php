<?php
$_GET['maintenance'] = 'no';

require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';

if($user['admin']['clairvoyant'] != 'yes')
{
  header('Location: /admin/tools.php');
  exit();
}

if($_POST['submit'] == 'Unequip' && $user['admin']['manageitems'] == 'yes')
{
  $petids = array();

  foreach($_POST as $key=>$value)
  {
    if(substr($key, 0, 4) == 'pet_')
      $petids[] = (int)substr($key, 4);
  }

  if(count($petids) > 0)
  {
    $command = 'UPDATE monster_pets SET toolid=0 WHERE idnum IN (' . implode(',', $petids) . ') LIMIT ' . count($petids);
    $database->FetchNone(($command, 'unequipping pets');
  }
}

$command = 'SELECT a.idnum,a.user,a.petname,a.toolid,b.idnum AS thistool FROM monster_pets AS a LEFT JOIN monster_inventory AS b ON a.toolid=b.idnum WHERE a.toolid>0 AND b.idnum IS NULL';
$pets = $database->FetchMultiple(($command, 'fetching pets with missing equipment');

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &nbsp; Administrative Tools &nbsp; Item Locator</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/admin/tools.php">Administrative Tools</a> &gt; List of Pets With Missing Equipment</h4>
<?php
print_r($petids);

if(count($pets) > 0)
{
  echo '<p>Found ' . count($pets) . ' such pets.</p>';

  if($user['admin']['manageitems'] == 'yes')
    echo '<form method="post">';

  echo '<table>' .
       '<tr class="titlerow"><th></th><th>Pet ID</th><th>Pet Name</th><th>Owner</th><th>Tool ID</th></tr>';

  $rowstyle = begin_row_class();

  foreach($pets as $pet)
  {
    $owner = get_user_byuser($pet['user'], 'display');
?>
<tr class="<?= $rowstyle ?>">
<td><input type="checkbox" name="pet_<?= $pet['idnum'] ?>" /></td>
<td><?= $pet['idnum'] ?></td>
<td><a href="/petprofile.php?petid=<?= $pet['idnum'] ?>"><?= $pet['petname'] ?></a></td>
<td><?= resident_link($owner['display']) ?></td>
<td><?= $pet['toolid'] ?></td>
</tr>
<?php
    $rowstyle = alt_row_class($rowstyle);
  }
  
  echo '</table>';

  if($user['admin']['manageitems'] == 'yes')
    echo '<p><input type="submit" name="submit" value="Unequip" /></p></form>';
}
else
  echo '<p>There are no such pets.</p>';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
