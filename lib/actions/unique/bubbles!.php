<?php
if($okay_to_be_here !== true)
  exit();

$command = '
  SELECT COUNT(idnum) AS qty,itemname
  FROM monster_inventory
  WHERE
    user=' . quote_smart($user['user']) . ' AND
    location=' . quote_smart($this_inventory['location']) . ' AND
    itemname IN (\'Castile Soap\', \'Fire Spice Soap\', \'Lilac Soap\', \'Mint Soap\')
  GROUP BY itemname
';
$soaps = $database->FetchMultipleBy($command, 'itemname', 'fetching soap');

if($_GET['action'] == 'title')
{
  $user['title'] = 'Bubble-blower';

  $command = 'UPDATE monster_users SET title=\'Bubble-blower\' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'updating resident\'s title');
}
else if($_POST['action'] == 'Use')
{
  $itemname = urldecode($_POST['item']);
  
  $available = $soaps[$itemname]['qty'];
  
  if($available > 0)
  {
    $deleted = delete_inventory_byname($user['user'], $itemname, 1, $this_inventory['location']);
    
    if($deleted > 0)
    {
      if($available == 1)
        unset($soaps[$itemname]);
      else
        $soaps[$itemname]['qty']--;

      if($itemname == 'Castile Soap')
        $background = 'profiles/bubbles_brown.jpg';
      else if($itemname == 'Fire Spice Soap')
        $background = 'profiles/bubbles_red.jpg';
      else if($itemname == 'Lilac Soap')
        $background = 'profiles/bubbles_blue.jpg';
      else if($itemname == 'Mint Soap')
        $background = 'profiles/bubbles_green.jpg';

      $user['profile_wall'] = $background;
      $command = 'UPDATE monster_users SET profile_wall=\'' . $background . '\',profile_wall_repeat=\'yes\' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
      $database->FetchNone($command, 'setting profile background');
      
      echo '<p class="success">Your profile background has been changed!</p>';
    }
    else
      echo '<p class="failure">There was a problem using your ' . $itemname . '.  This could be an error or bug.  Try it again, but if you continue to have this problem definitely <a href="admincontact.php">contact an administrator</a>.</p>';
  }
  else
    echo '<p class="failure">You don\'t have any ' . $itemname . ' available!</p>';
}
?>
<h5>Change Your Profile Background</h5>
<?php
if(count($soaps) == 0)
  echo '<p>You need <a href="encyclopedia.php?submit=Search&name=&type=craft/soap&standard=on">soap</a> to blow bubbles!</p>';
else
{
  echo '
    <form action="itemaction.php?idnum=' . $this_inventory['idnum'] . '" method="post">
    <table>
     <thead>
      <tr><th></th><th></th><th>Soap</th><th>Qty</th></tr>
     </thead>
     <tbody>
  ';

  $rowclass = begin_row_class();

  foreach($soaps as $soap)
  {
    $details = get_item_byname($soap['itemname']);

    echo '
      <tr class="' . $rowclass . '">
       <td><input type="radio" name="item" value="' . urlencode($soap['itemname']) . '" /></td>
       <td class="centered">' . item_display_extra($details) . '</td>
       <td>' . $soap['itemname'] . '</td>
       <td class="centered">' . $soap['qty'] . '</td>
      </tr>
    ';

    $rowclass = alt_row_class($rowclass);
  }

  echo '
     </tbody>
    </table>
    <p><input type="submit" name="action" value="Use" /></p>
    </form>
  ';
}
?>
<h5>Change Your Title</h5>
<ul>
 <li><a href="itemaction.php?idnum=<?= $this_inventory['idnum'] ?>&amp;action=title">Bubble-blower</a></li>
</ul>
<?php
?>
