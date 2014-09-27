<?php
if($okay_to_be_here !== true)
  exit();

$AGAIN_WITH_SAME = true;

// find accounts which have the most zombies!
$command = '
  SELECT b.display,COUNT(a.user) AS qty
  FROM monster_pets AS a LEFT JOIN monster_users AS b
  ON a.user=b.user
  WHERE
    a.zombie=\'yes\' AND
    b.is_npc=\'no\'
  GROUP BY(a.user)
  ORDER BY qty DESC
';
$zombies = $database->FetchMultiple($command, 'fetching accounts with zombies!');

if(count($zombies) > 0)
{
  $breaks = (mt_rand(1, 10) == 1);

  echo '
    <p>The machine springs to life, beeping, whirring, and displaying an array of strange characters on its tiny screen.</p><p>After a moment the display clears, then prints a list:</p>
    <table>
     <tr class="titlerow"><th>Resident</th><th>Zombies</th></tr>
  ';

  $rowclass = begin_row_class();
    
  foreach($zombies as $zombie)
  {
    echo '
      <tr class="' . $rowclass . '">
       <td>' . resident_link($zombie['display']) . '</td>
       <td class="centered">' . $zombie['qty'] . '</td>
      </tr>
    ';

    $rowclass = alt_row_class($rowclass);
  }

  echo '
    </table>
    <p><i>(It\'s a tiny screen, but that doesn\'t mean it can\'t have a scrollbar, or something like that >_>)</i></p>
  ';

  if($breaks)
  {
    echo '<p class="failure">The machine makes a terrible grinding noise, lets off a bellow of smoke, and dies.</p>';

    delete_inventory_byid($this_inventory['idnum']);

    add_inventory($user['user'], '', 'Smoke', 'Produced by a ' . $this_inventory['itemname'], $this_inventory['location']);
    add_inventory($user['user'], '', 'Ruins', 'The remains of a ' . $this_inventory['itemname'], $this_inventory['location']);

    $AGAIN_WITH_SAME = false;
    $AGAIN_WITH_ANOTHER = true;
  }
}
else
  echo '
    <p>The machine springs to life, beeping, whirring, and displaying an array of strange characters on its tiny screen.</p><p>After a moment the display clears, then prints a short message:</p>
    <p>"No zombies detected."</p>
  ';
?>
