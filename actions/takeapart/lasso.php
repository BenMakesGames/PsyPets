<?php
if($okay_to_be_here !== true)
  exit();

if($_POST['action'] == 'doall')
{
  $database->FetchNone('
    DELETE FROM monster_inventory
    WHERE
      user=' . quote_smart($user['user']) . '
      AND location=' . quote_smart($this_inventory['location']) . '
      AND itemname=' . quote_smart($this_inventory['itemname']) . '
  ');
  
  $quantity = $database->AffectedRows();

  if($quantity > 0)
  {
    add_inventory_quantity($user['user'], '', 'Stringy Rope', '', $this_inventory['location'], $quantity);

    echo '<p>The knots are easily undone, and your efforts are shortly rewarded: you now have ' . $quantity . ' Stringy Ropes.</p>';

    require_once 'commons/statlib.php';
    record_stat($user['idnum'], 'Undid a Lasso', $quantity);
  }
}
else
{
  delete_inventory_byid($this_inventory['idnum']);

  add_inventory($user['user'], '', 'Stringy Rope', '', $this_inventory['location']);

  echo '<p>The knot is easily undone, and your efforts are shortly rewarded: you now have a Stringy Rope.</p>';

  require_once 'commons/statlib.php';
  record_stat($user['idnum'], 'Undid a Lasso', 1);

  $AGAIN_WITH_ANOTHER = true;
  
  $data = $database->FetchSingle('
    SELECT idnum,COUNT(idnum) AS qty
    FROM monster_inventory
    WHERE
      user=' . quote_smart($user['user']) . '
      AND location=' . quote_smart($this_inventory['location']) . '
      AND itemname=' . quote_smart($this_inventory['itemname']) . '
  ');

  if($data['qty'] >= 2)
  {
    echo '
      <div style="border-top: 1px dashed #ccc; padding-bottom:14px;"></div>
      <p>There are ' . $data['qty'] . ' other Lassos in this room.  Would you like to untie <em>all of them?</em></p>
      <form action="/itemaction.php?idnum=' . $data['idnum'] . '" method="post">
      <input type="hidden" name="action" value="doall" />
      <p><input type="submit" value="Yes!  Untie them all!" class="bigbutton" /></p>
      </form>
    ';
  }
}
?>
