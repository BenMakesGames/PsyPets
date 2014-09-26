<?php
if($okay_to_be_here !== true)
  exit();

$options = array(
  'awesomechick.png',
  'backpackdude.png',
  'caoir.png',
  'coatdude.png',
  'dude.png',
  'hairthings.png',
  'jacketdude.png',
  'kindaglaring.png',
  'munch.png',
  'neatcollar.png',
  'onetinycurl.png',
  'oohvampire.png',
  'populous20.png',
  'sassychick.png',
  'schoolgirl.png',
  'simpledude.png',
  'slyexpression.png',
  'smugpunky.png',
  'stickthinchick.png',
  'uncertain.png'
);

if($_GET['select'] >= 1 && $_GET['select'] <= count($options))
{
	$database->FetchNone("UPDATE monster_users SET graphic=" . quote_smart('weth/' . $options[$_GET['select'] - 1]) . " WHERE idnum=" . $user["idnum"] . " LIMIT 1");
?>
<p><i>The menu fades away, and the WeTH utters a single beep before turning itself off.</i></p>
<?php
}
else
{
?>
<p><i>Although the WeTH is clearly a small piece of a larger machine, it nevertheless manages to power on and operate on its own, presenting you with a menu of options which it projects about an inch off of its surface.</i></p>
<p><table border="0" cellspacing="0" cellpadding="8">
<tr>
<?php
  $x = 1;
  foreach($options as $i=>$option)
  {
?>
<td align="center"><a href="itemaction.php?idnum=<?= $_GET['idnum'] ?>&select=<?= $i + 1 ?>"><img src="gfx/avatars/weth/<?= $option ?>" width=48 height=48 border=0 /><br />this one!</a></td>
<?php
    if($x % 5 == 0)
      echo '</tr><tr>';

    $x++;
  }
?>
</tr>
</table></p>
<?php
}
?>
