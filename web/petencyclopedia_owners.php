<?php
$require_petload = 'no';
$require_login = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/petgraphics.php';

$i = (int)$_GET['i'] - 1;

if($i < 0 || $i >= count($PET_GRAPHICS))
{
  header('Location: ./petencyclopedia.php');
  exit();
}

$graphic = $PET_GRAPHICS[$i];

$command = 'SELECT COUNT(DISTINCT(user)) AS c FROM `monster_pets` WHERE graphic=\'' . $graphic . '\'';
$data = $database->FetchSingle($command, 'fetching owner count');

$num_owners = $data['c'];

$pages = ceil($num_owners / 20);

$page = (int)$_GET['page'];

if($page < 1 || $page > $pages)
  $page = 1;

$pages = paginate($pages, $page, 'petencyclopedia_owners.php?i=' . ($i + 1) . '&amp;page=%s');

$owners_command = 'SELECT b.display,COUNT(a.idnum) AS c FROM `monster_pets` AS a LEFT JOIN monster_users AS b ON a.user=b.user WHERE a.graphic=' . quote_smart($graphic) . ' GROUP BY(a.user) ORDER BY display ASC LIMIT ' . (($page - 1) * 20) . ',20';
$owners = $database->FetchMultiple($owners_command, 'fetching number of pets in-game');

$petshelter_command = 'SELECT COUNT(idnum) AS c FROM monster_pets WHERE user=\'psypets\' AND graphic=' . quote_smart($graphic) . ' AND last_check<' . $now;
$data = $database->FetchSingle($petshelter_command, 'fetching number for sale at pet shelter');
$petsheltersold = (int)$data['c'];

if($user['breeder'] == 'yes')
{
  $petmarket_command = 'SELECT COUNT(m.idnum) AS c FROM psypets_pet_market AS m JOIN monster_pets AS p ON m.petid=p.idnum WHERE m.expiration>=' . $now . ' AND p.graphic=' . quote_smart($graphic);
  $data = $database->FetchSingle($petmarket_command, 'fetching number for sale at pet shelter');
  
  $petmarketsold = (int)$data['c'];
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Pet Encyclopedia</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
<table>
<tr><td><h4><a href="petencyclopedia.php?page=<?= ceil(($i + 1) / 20) ?>">Pet Encyclopedia</a> &gt;</h4></td><td><img src="gfx/pets/<?= $graphic ?>" width="48" height="48" /></td></tr>
</table>
<ul>
<?php
if($petsheltersold > 1)
  echo '<li>', $petsheltersold, ' are available at the <a href="petshelter.php">Pet Shelter</a>.</li>';
else if($petsheltersold == 1)
  echo '<li>1 is available at the <a href="petshelter.php">Pet Shelter</a>.</li>';
else
  echo '<li>None are available at the <a href="petshelter.php">Pet Shelter</a>.</li>';

if($user['breeder'] == 'yes')
{
  if($petmarketsold > 1)
    echo '<li>', $petmarketsold, ' are available at the <a href="petmarket.php">Pet Market</a>.</li>';
  else if($petmarketsold == 1)
    echo '<li>1 is available at the <a href="petmarket.php">Pet Market</a>.</li>';
  else
    echo '<li>None are available at the <a href="petmarket.php">Pet Market</a>.</li>';
}
?>
</ul>
<?php
if(count($owners) > 0)
{
?>
<?= $pages ?>
<table>
<tr class="titlerow">
 <th>Resident</th><th>Number Owned</th>
</tr>
<?php
$rowclass = begin_row_class();

foreach($owners as $owner)
{
?>
<tr class="<?= $rowclass ?>">
 <td><?= resident_link($owner['display']) ?></td>
 <td class="righted"><?= $owner['c'] ?></td>
</tr>
<?php
  $rowclass = alt_row_class($rowclass);
}
?>
</table>
<?= $pages ?>
<?php
}
else
  echo '<ul><li>No one has this pet.</li></ul>';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
