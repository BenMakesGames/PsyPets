<?php
function select_multiple_from_storage($user)
{
  $command = '
    SELECT a.itemname,COUNT(a.idnum) AS qty,b.graphic,b.graphictype,b.idnum,b.cursed,b.noexchange
    FROM monster_inventory AS a LEFT JOIN monster_items AS b
    ON a.itemname=b.itemname
    WHERE a.user=' . quote_smart($user) . '
    AND a.location=\'storage\'
    AND b.cursed=\'no\' AND b.noexchange=\'no\'
    GROUP BY a.itemname
    ORDER BY a.itemname ASC
  ';
  
  $groups = fetch_multiple($command, 'fetching items');

  echo '
    <table>
     <thead><tr class="titlerow"><th></th><th></th><th></th><th>Item</th><th>Quantity</th></tr></thead>
     <tbody>
  ';

  $rowclass = begin_row_class();

  foreach($groups as $group)
  {
    echo '
      <tr class="', $rowclass, '">
       <td><span id="action_', $group['idnum'], '"><a href="#" onclick="show_group(', $group['idnum'], '); return false;">&#x25BA;</a></span></td>
       <td><input type="checkbox" name="g_', $group['idnum'], '" onclick="toggle_group(', $group['idnum'], ');"></td>
       <td class="centered">', item_display_extra($group, '', true), '</td>
       <td>', $group['itemname'], '</td>
       <td class="righted">&times;', $group['qty'], '</td>
      </tr>
      <tr class="', $rowclass, '" style="display:none;" id="group_row_', $group['idnum'], '">
       <td colspan="5"><div id="group_', $group['idnum'], '"></div></td>
      </tr>
    ';
    
    $rowclass = alt_row_class($rowclass);
  }

  echo '
     </tbody>
    </table>
  ';
}
?>
