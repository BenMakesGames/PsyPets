<?php
require_once "commons/dbconnect.php";
require_once "commons/rpgfunctions.php";
require_once "commons/sessions.php";
require_once "commons/formatting.php";
require_once "commons/itemlib.php";
require_once 'commons/petlib.php';

if($admin['clairvoyant'] != 'yes')
{
    header('Location: /');
    exit();
}

$levels = array();

$command = 'SELECT * FROM monster_pets WHERE user!=\'psypets\' ORDER BY level ASC';
$pets = fetch_multiple($command);

foreach($pets as $pet)
{
  $levels[pet_level($pet)]++;
}
?>
<table>
<tr>
<th>Level</th>
<th>Number</th>
</tr>
<?php
foreach($levels as $level=>$count)
{
?>
<tr>
<td><?= $level ?></td>
<td><?= $count ?></td>
</tr>
<?php
}
?>
</table>