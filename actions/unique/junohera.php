<?php
if($okay_to_be_here !== true)
  exit();

if($this_inventory['itemname'] == 'Juno')
{
  $command = 'SELECT idnum FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname=\'Feather\' AND location=' . quote_smart($this_inventory['location']) . ' LIMIT 1';
  $feather = $database->FetchSingle($command, 'fetching feather');

  if($feather !== false)
  {
    if($_GET['action'] == 'transform')
    {
      delete_inventory_byname($user['user'], 'Feather', 1, $this_inventory['location']);

      $command = 'UPDATE monster_inventory SET itemname=\'Hera\' WHERE idnum=' . $this_inventory['idnum'] . ' LIMIT 1';
      $database->FetchNone($command, 'Juno->Hera');
      
      echo '<p>The cloak folds itself inside out, revealing a golden circlet adorned with a peacock feather.</p><p>Juno has become Hera!</p>';

      $AGAIN_WITH_SAME = true;
    }
    else
      echo '
        <p>With a Feather, Juno could be transformed into Hera.</p>
        <ul><li><a href="itemaction.php?idnum=' . $this_inventory['idnum'] . '&action=transform">Transfoooorrrrm!</a></li></ul>
      ';
  }
  else
    echo '<p>With a Feather, Juno could be transformed into Hera.  Unfortunately, you don\'t seem to have a Feather around.</p>';
}
else
{
  $command = 'SELECT idnum FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname=\'Silver\' AND location=' . quote_smart($this_inventory['location']) . ' LIMIT 1';
  $silver = $database->FetchSingle($command, 'fetching silver');

  if($silver !== false)
  {
    if($_GET['action'] == 'transform')
    {
      delete_inventory_byname($user['user'], 'Silver', 1, $this_inventory['location']);

      $command = 'UPDATE monster_inventory SET itemname=\'Juno\' WHERE idnum=' . $this_inventory['idnum'] . ' LIMIT 1';
      $database->FetchNone($command, 'Juno->Hera');

      echo '<p>A cape unfurls from inside the crown, which seems to get lost within the folds of fabric.</p><p>Hera has become Juna!</p>';

      $AGAIN_WITH_SAME = true;
    }
    else
      echo '
        <p>With Silver, Hera could be transformed into Juno.</p>
        <ul><li><a href="itemaction.php?idnum=' . $this_inventory['idnum'] . '&action=transform">Transfoooorrrrm!</a></li></ul>
      ';
  }
  else
    echo '<p>With Silver, Hera could be transformed into Juno.  Unfortunately, you don\'t seem to have any Silver around.</p>';
}
?>
