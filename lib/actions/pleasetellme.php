<?php
if($okay_to_be_here !== true)
  exit();

if($_POST['action'] == '<-- this')
{
  $alldone = true;
}
else if($_POST['action'] == '<-- that')
{
  $alldone = true;
}
else if($_POST['action'] == '<-- something completely different')
{
  // email me!

  $alldone = true;
}

if($alldone)
{
  delete_inventory_byid($this_inventory['idnum']);
  
  echo '<p>Thanks!</p>';
}
else
{
?>
<form action="itemaction.php?idnum=<?= $this_inventory['idnum'] ?>" method="post">
<p>Hello!  'Just wanted to ask a quick question!</p>
<p>How did you discover PsyPets?</p>
<table>
 <tr>
  <td>I don't want to say.  It's a secret, or I don't like statistics, or I'm lazy, or something...</td>
  <td><input type="submit" name="action" value="<-- this" /></td>
 </tr>
 <tr>
  <td>I already knew about it!  This is my second (third, fourth...) account, or I came back after a long absence, or something...</td>
  <td><input type="submit" name="action" value="<-- that" /></td>
 </tr>
 <tr>
  <td><textarea name="howww"></textarea></td>
  <td><input type="submit" name="action" value="<-- something completely different" class="bigbutton" /></td>
 </tr>
</table>
</form>
<?php
}
?>
