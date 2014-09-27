<?php
if($okay_to_be_here !== true)
  exit();

if($_GET['step'] == 2)
{
  $command = "DELETE FROM monster_inventory WHERE (itemname='Demon' OR itemname='Familiar') AND user='" . $user["user"] . "'";
  $database->FetchNone($command, 'removing demons and familiars');

  if($database->AffectedRows() > 0)
  {
    $command = "UPDATE monster_inventory SET itemname='Bronze Vessel', message='', message2='' WHERE idnum=" . $this_inventory['idnum'] . " LIMIT 1";
    $database->FetchNone($command, 'transmuting seal into vessel');

    require_once 'commons/statlib.php';
    record_stat($user['idnum'], 'Used a Seal of Solomon', 1);
?>
After reading the seal the scroll vanishes, replaced with a humming lamp of bronze.
<?php
  }
  else
  {
?>
You read the seal, loud and clear, but nothing happens.
<?php
  }
}
else
{
?>
The Seal of Solomon bottles up both Demons and Familiars indiscriminately.  Are you suuuuure you want to use it?</p>
<ul>
 <li><a href="itemaction.php?idnum=<?= $this_inventory['idnum'] ?>&step=2">Begin the incantations!</a></li>
</ul>
<?php
}
?>
