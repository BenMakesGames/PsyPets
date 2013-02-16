<?php
require_once "commons/dbconnect.php";
require_once "commons/rpgfunctions.php";
require_once "commons/sessions.php";
require_once "commons/formatting.php";
require_once "commons/itemlib.php";
require_once 'commons/petlib.php';

$levels = array();

$command = 'SELECT * FROM monster_pets WHERE user!=\'psypets\' ORDER BY level ASC';
$result = mysql_query($command);

while($pet = mysql_fetch_assoc($result))
{
  $levels[pet_level($pet)]++;
}

mysql_free_result($result);

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