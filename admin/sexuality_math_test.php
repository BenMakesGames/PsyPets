<?php
require_once '../commons/relationshiplib.php';
?>
<table>
<thead>
<tr><th>to Same</th><th>to Opposite</th></tr>
</thead>
<tbody>
<?php
for($i = 0; $i < 100000; ++$i)
{
  $attracted_to_same = mt_rand(0, mt_rand(0, 100));
	$attracted_to_opposite = mt_rand(mt_rand(0, 50), mt_rand(50, 100));

  if($attracted_to_same < 20 && $attracted_to_opposite < 20)
    $asexual++;
  else
  {
    if($attracted_to_opposite > $attracted_to_same * 2)
      $straight++;
    else if($attracted_to_same > $attracted_to_opposite * 2)
      $gay++;
    else
      $bi++;
  }
  
  //echo '<tr><td>', preference_description($attracted_to_same), '</td><td>', preference_description($attracted_to_opposite), '</td></tr>';
}
?>
</tbody>
</table>
<h5>Out of 100000</h5>
<p>Gay: <?= round($gay * 100 / 100000) ?>%</p>
<p>Straight: <?= round($straight * 100 / 100000) ?>%</p>
<p>Bi: <?= round($bi * 100 / 100000) ?>%</p>
<p>Asexual: <?= round($asexual * 100 / 100000) ?>%</p>