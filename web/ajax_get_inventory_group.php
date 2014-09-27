<?php
$require_petload = 'no';
$invisible = 'yes';
$AJAX = true;

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';

$itemid = (int)$_POST['itemid'];

echo $itemid . "\n";

$location = $_POST['location'];

if($location != 'storage')
  die('There is no such location as "' . $location . '".');

if($_POST['checked'] == 'true')
  $checked = ' checked="checked"';

$command = 'SELECT itemname,durability FROM monster_items WHERE idnum=' . $itemid . ' LIMIT 1';
$data = $database->FetchSingle($command, 'fetching item #' . $itemid);

if($data === false)
  die('There is no such item.');

$command = 'SELECT idnum,creator,message,message2,health FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND location=' . quote_smart($location) . ' AND itemname=' . quote_smart($data['itemname']);
$items = $database->FetchMultiple($command);

echo '
  <input type="hidden" name="n_', $itemid, '" value="true" />
  <table style="margin-left:3em;">
   <tbody>
';

foreach($items as $item)
{
  if($item['message'] == '' && $item['message2'] == '')
    $note = '<i>no item comment</i>';
  else
    $note = $item['message'] . '<br />' . $item['message2'];

  echo '
    <tr>
     <td><input type="checkbox" name="i_', $item['idnum'], '" class="group_', $itemid, '"', $checked, ' /></td>
     <td>', $note, '</td>
    </tr>
  ';
}

echo '
   </tbody>
  </table>
';
?>