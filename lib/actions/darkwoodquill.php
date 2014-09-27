<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/grammar.php';

$button_texts = array(
  'Gratify', 'Indulge', 'Assuage', 'Thrill', 'Oblige', 'Delight',
  'Allay', 'Avail', 'Spoil',
);

$button = $button_texts[array_rand($button_texts)];

$food = (int)$this_inventory['data'];

$command = '
  SELECT a.idnum,a.itemname,a.message,a.message2,b.graphic,b.graphictype
  FROM
    monster_inventory AS a
    LEFT JOIN monster_items AS b
  ON
    a.itemname=b.itemname
  WHERE
    a.user=' . quote_smart($user['user']) . '
    AND a.location=' . quote_smart($this_inventory['location']) . '
    AND (a.message!=\'\' OR a.message2!=\'\')
    AND a.idnum!=' . $this_inventory['idnum'] . '
';
$feed_items = $database->FetchMultipleBy($command, 'idnum', 'fetching food items');

if($_POST['action'] == 'use')
{
  $mollify = array();

  foreach($_POST as $key=>$value)
  {
    if(substr($key, 0, 2) == 'i_' && ($value == 'yes' || $value == 'on'))
    {
      $id = substr($key, 2);
      if(array_key_exists($id, $feed_items))
      {
        $mollify[] = $id;
        unset($feed_items[$id]);
      }
    }
  }
  
  if(count($mollify) > 0)
  {
    $command = '
      UPDATE monster_inventory
      SET message=\'\',message2=\'\'
      WHERE idnum IN (' . implode(',', $mollify) . ')
      LIMIT ' . count($mollify);
    $database->FetchNone($command, 'mollifying');

    $updated = $database->AffectedRows();
    
    $food += $updated;
    
    $command = 'UPDATE monster_inventory SET data=\'' . $food . '\' WHERE idnum=' . $this_inventory['idnum'] . ' LIMIT 1';
    $database->FetchNone($command, 'feeding');

    if($updated > 1)
      echo '<p>The Quill scribbles with a frenzy, removing the comments from the ' . say_number($updated) . ' selected items.</p>';
    else
      echo '<p>The Quill scribbles with a frenzy, removing the comments from the selected item.</p>';
  }
}
else if($food >= 50)
{
  if($_GET['action'] == 'changeavatar')
  {
    $command = 'UPDATE monster_users SET graphic=\'special-secret/book.png\' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
    $database->FetchNone($command, 'updating avatar');
  }

  if($_GET['action'] == 'discardavatar' || $_GET['action'] == 'changeavatar')
  {
    $food -= 50;

    $command = 'UPDATE monster_inventory SET data=\'' . $food . '\' WHERE idnum=' . $this_inventory['idnum'] . ' LIMIT 1';
    $database->FetchNone($command, 'feeding');

    echo '<p>Very well!</p>';
  }
}

if($food >= 50)
{
  echo '
    <p>The Quill offers the following avatar:</p>
    <p><img src="//saffron.psypets.net/gfx/avatars/special-secret/book.png" alt="quill writing in book" /></p>
    <ul>
     <li><a href="itemaction.php?idnum=' . $this_inventory['idnum'] . '&action=changeavatar">Accept it!</a></li>
     <li><a href="itemaction.php?idnum=' . $this_inventory['idnum'] . '&action=discardavatar">Discard it!</a></li>
    </ul>
  ';
}

if(count($feed_items) == 0)
{
  if($food < 50)
    echo '<p>Despite your attempts to rouse it to action, the Quill remains motionless.</p>';
}
else
{
  echo '
    <p>The Quill trembles with anticipation...</p>
    <form action="itemaction.php?idnum=' . $this_inventory['idnum'] . '" method="post">
    <input type="hidden" name="action" value="use" />
    <p><input type="submit" value="' . $button . '" /></p>
    <table>
     <thead>
      <tr class="titlerow">
       <th></th><th></th><th>Item</th><th>Comment</th>
      </tr>
     </thead>
     <tbody>
  ';

  $rowclass = begin_row_class();

  foreach($feed_items as $this_item)
  {
    echo '
      <tr class="' . $rowclass . '">
       <td><input type="checkbox" name="i_' . $this_item['idnum'] . '" /></td>
       <td class="centered">' . item_display($this_item) . '</td>
       <td>' . $this_item['itemname'] . '</td>
       <td>' . $this_item['message'] . '<br />' . $this_item['message2'] . '</td>
      </tr>
    ';
    
    $rowclass = alt_row_class($rowclass);
  }

  echo '
     </tbody>
    </table>
    <p><input type="submit" value="' . $button . '" /></p>
    </form>
  ';
}
?>