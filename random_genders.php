<?php
if(array_key_exists('bold_percent', $_POST))
	$bold_percent = (int)$_POST['bold_percent'];
else
	$bold_percent = 50;

if($bold_percent < 0)
	$bold_percent = 0;
else if($bold_percent > 100)
	$bold_percent = 100;
?>
<style type="text/css">
*
{
	font-family: Arial,Helvetica,sans-serif;
	font-size: 13px;
}

tbody td
{
	text-align: right;
}
</style>
<p>This table attempts to create a random sampling of genders and sexual orientations that more-or-less accurately reflects distributions in real life.</p>
<p>It may be all wrong.  I'd love to hear your guys' feedback.</p>
<p>KEEP IN MIND: the %s do not need to add up to 100!  These numbers represent a co-efficient of attraction... for example, 40% attracted to males and 80% attracted to females means that any attraction possible toward males would be reduced to 40%, while any attraction to females would be reduced to 80%.</p>
<hr />
<form method="post">
<p>%s of <input type="number" name="bold_percent" min="0" max="100" size="3" maxlength="3" value="<?php echo $bold_percent; ?>">% or more are in bold. <input type="submit" value="Update" /></p>
</form>
<hr />
<?php
for($x = 0; $x < 1000; ++$x)
{
	$gender = mt_rand(1, 2) == 1 ? 'male' : 'female';
	
	$var_name = $gender . '_count';
	$$var_name = $$var_name + 1;
	
	if($gender == 'male')
	{
		$pet['attracted_to_males'] = mt_rand(0, mt_rand(0, 100));
		$pet['attracted_to_females'] = mt_rand(mt_rand(0, 50), mt_rand(50, 100));
	}
	else if($gender == 'female')
	{
		$pet['attracted_to_males'] = mt_rand(mt_rand(0, 50), mt_rand(50, 100));
		$pet['attracted_to_females'] = mt_rand(0, mt_rand(0, 100));
	}
	
	$pets[$gender][] = $pet;
}
?>
<p>1000 pets were randomly generated just now, just for you!  <?php echo $male_count ?> male, <?php echo $female_count ?> female.</p>
<p>Reload the page for a new set of 1000.</p>
<table>
 <thead>
 <tr>
  <td></td>
	<td colspan="2" style="text-align:center;">Interest in...</td>
 </tr>
 <tr>
  <td style="text-align:right;">Pet</td><td>Males</td><td>Females</td>
 </tr>
 </thead>
 <tbody>
<?php
foreach($pets as $gender=>$these_pets)
{
	foreach($these_pets as $pet)
	{	
		echo '
			<tr>
			 <td>' . $gender . '</td>
			 <td' . ($pet['attracted_to_males'] >= $bold_percent ? ' style="font-weight:bold;"' : '') . '>' . $pet['attracted_to_males'] . '%</td>
			 <td' . ($pet['attracted_to_females'] >= $bold_percent ? ' style="font-weight:bold;"' : '') . '>' . $pet['attracted_to_females'] . '%</td>
			 <td>' . $note . '</td>
			</tr>
		';
	}
}
?>
 </tbody>
</table>
