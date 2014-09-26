<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/userlib.php';
require_once 'commons/mazelib.php';

$deleted = false;

if($user['mazeloc'] == 0)
{
  echo '<p>You read the scroll, but nothing happens.</p>' .
       '<p>Perhaps you\'re missing something...</p>';
}
else if($_POST['action'] == 'scry')
{
  $this_user = get_user_bydisplay($_POST['target'], 'display,activated,disabled,idnum,mazeloc,mazemp');

  if($this_user == false)
    $errors[] = 'Could not find a resident named "' . $_POST['target'] . '"';
  else if($this_user['activated'] == 'no' || $this_user['disabled'] == 'yes')
    $errors[] = 'Could not find a resident named "' . $_POST['target'] . '"';
  else if($this_user['idnum'] == $user['idnum'])
    $errors[] = 'Y-- you can\'t scry on yourself with a ' . $this_item['itemname'] . ' >_>';
  else if($this_user['mazeloc'] == 0)
  {
    $messages[] = 'isn\'t in The Pattern at all!';
    $deleted = true;
  }
  else if($this_user['mazeloc'] == $user['mazeloc'])
  {
    $messages[] = 'is in the exact same space you are!';
    $messages[] = 'has ' . $this_user['mazemp'] . ' MP.';
    $deleted = true;
  }
  else
  {
    $my_tile = get_maze_byid($user['mazeloc']);
    $target_tile = get_maze_byid($this_user['mazeloc']);
    
    if($target_tile['x'] > $my_tile['x'])
      $messages[] = 'is ' . ($target_tile['x'] - $my_tile['x']) . ' to the East';
    else if($target_tile['x'] < $my_tile['x'])
      $messages[] = 'is ' . ($my_tile['x'] - $target_tile['x']) . ' to the West';

    if($target_tile['y'] > $my_tile['y'])
      $messages[] = 'is ' . ($target_tile['y'] - $my_tile['y']) . ' to the South';
    else if($target_tile['y'] < $my_tile['y'])
      $messages[] = 'is ' . ($my_tile['y'] - $target_tile['y']) . ' to the North';

    if($target_tile['z'] > $my_tile['z'])
      $messages[] = 'is ' . ($target_tile['z'] - $my_tile['z']) . ' Down';
    else if($target_tile['z'] < $my_tile['z'])
      $messages[] = 'is ' . ($my_tile['z'] - $target_tile['z']) . ' Up';
/*
    if($user['idnum'] == 1)
      $messages[] = 'your location: (' . $my_tile['x'] . ', ' . $my_tile['y'] . '); their location: (' . $target_tile['x'] . ', ' . $target_tile['y'] . ')';
*/
    $messages[] = 'has ' . $this_user['mazemp'] . ' MP.';
    $deleted = true;
  }
}

if(count($errors) > 0)
  echo '<ul><li>' . implode('</li><li>', $errors) . '</li></ul>';

if(count($messages) > 0)
{
  echo '<p><i>The scroll bursts into flames, which, as they dance, show these words:</i></p><p>' . $this_user['display'] . '...</p>' .
       '<ul><li>' . implode('</li><li>', $messages) . '</li></ul>';
}

if(count($extra_messages) > 0)
  echo '<ul><li>' . implode('</li><li>', $extra_messages) . '</li></ul>';

if($deleted)
{
  delete_inventory_byid($this_inventory['idnum']);
  $AGAIN_WITH_ANOTHER = true;
}
else
{
?>
<script type="text/javascript">
$(function() {
  $('#target_select').change(function() {
    $('#target').val($(this).val());
  });
});
</script>
<p>Who will you scry?</p>
<form action="itemaction.php?idnum=<?= $_GET['idnum'] ?>" method="post">
<p>Target: <input type="text" name="target" id="target" /> <span class="size13">&larr;</span> <select id="target_select"><option value=""></option><?php
$friends = $database->FetchMultiple('
  SELECT b.display
  FROM psypets_user_friends AS a
    LEFT JOIN monster_users AS b
      ON a.friendid=b.idnum
  WHERE a.userid=' . (int)$user['idnum'] . '
  ORDER BY b.display ASC
');

foreach($friends as $friend)
  echo '<option value="' . $friend['display'] . '">' . $friend['display'] . '</option>';
?></select></p>
<p><input type="hidden" name="action" value="scry" /><input type="submit" value="Scry" /></p>
</form>
<?php
}
?>
