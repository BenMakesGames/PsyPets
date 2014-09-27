<?php
if($okay_to_be_here !== true)
  exit();

for($x = 0; $x < 1000000; ++$x)
{  
  $favor = mt_rand(10, mt_rand(38, mt_rand(83, 200)));
  $count[$favor]++;
  $total += $favor;
}

$average = $total / 1000000;
?>
<p>Average Favor gain (sample size: 1,000,000) is <?= $average ?>.</p>
<p>If chance of gaining this from the pattern is 1%, then each maze piece + item = <?= $average / 100 ?> Favor.</p>
<p>How much money does maze piece + item tend to cost?  (Look at maze piece; guess at item.)</p>
<p>1500 + item</p>
<p>People seem to sell 100 Favor for 30k moneys; 300 moneys per 1 Favor.</p>
<p>Our free favor should go for about 3000 moneys per 1 Favor?</p>
<table>
<?php
for($i = 10; $i <= 200; ++$i)
{
?>
 <tr><td><?= $i ?> Favor</td><td><?= $count[$i] ?></td></tr>
<?php
}
?>
</table>
