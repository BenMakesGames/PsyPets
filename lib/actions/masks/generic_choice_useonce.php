<?php
if($okay_to_be_here !== true)
  exit();

$options = explode(',', $action_info[2]);

if($_GET['select'] >= 1 && $_GET['select'] <= count($options))
{
  $database->FetchNone('UPDATE monster_users SET graphic=' . quote_smart($options[$_GET['select'] - 1]) . ' WHERE idnum=' . $user['idnum'] . ' LIMIT 1');

  delete_inventory_byid($this_inventory['idnum']);
?>
<i><?= $action_info[4] ?></i></p>
<p><i>(Your avatar has been changed.)</i>
<?php
}
else
{
?>
<i><?= $action_info[3] ?></i></p>
<p><table border=0 cellspacing=0 cellpadding=8>
<tr>
<?php
  foreach($options as $i=>$option)
  {
?>
<td align="center"><a href="itemaction.php?idnum=<?= $_GET['idnum'] ?>&select=<?= $i + 1 ?>"><img src="gfx/avatars/<?= $option ?>" width=48 height=48 border=0 /><br />this one!</a></td>
<?php
  }
?>
</tr>
</table>
<?php
}
?>
