<?php
function record_item_acquisition($itemname, $quantity)
{
  $command = '
    UPDATE psypets_pawned_for
    SET quantity=quantity+' . $quantity . '
    WHERE
      itemname=' . quote_smart($itemname) . '
    LIMIT 1
  ';

  fetch_none($command, 'updating item acquisition record');

  if($GLOBALS['database']->AffectedRows() == 0)
  {
    $command = '
      INSERT INTO psypets_pawned_for
      (itemname, quantity)
      VALUES (' . quote_smart($itemname) . ', ' . $quantity . ')
    ';

    fetch_none($command, 'inserting item acquisition record');
  }
}

function record_item_disposal($itemname, $type, $quantity)
{
  $command = '
    UPDATE psypets_gamesold
    SET quantity=quantity+' . $quantity . '
    WHERE
      itemname=' . quote_smart($itemname) . ' AND
      transaction=\'' . $type . '\'
    LIMIT 1
  ';
  
  fetch_none($command, 'updating item disposal record');
  
  if($GLOBALS['database']->AffectedRows() == 0)
  {
    $command = '
      INSERT INTO psypets_gamesold
      (itemname, transaction, quantity)
      VALUES (' . quote_smart($itemname) . ', \'' . $type . '\', ' . $quantity . ')
    ';
    
    fetch_none($command, 'inserting item disposal record');
  }
}
?>