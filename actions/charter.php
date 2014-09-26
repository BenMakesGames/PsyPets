<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/grouplib.php';

$step = 1;

if($_POST['action'] == 'create')
{
  $name = $_POST['groupname'];

  if(strlen($name) < 4 || strlen($name) > 24)
  {
    $display_message = "Group names must be between 4 and 24 characters.";
    $errored = true;
  }
  else if(preg_match("/[^a-zA-Z0-9À-ö_ .!?~'-]/", $name))
  {
    $display_message = "Please only use alphanumeric characters (or some punctuation)";
    $errored = true;
  }
  else if(preg_match("/[^a-zA-Z]/", $name{0}))
  {
    $display_message = "Group names name must start with a letter.";
    $errored = true;
  }
  else
  {
    $group = get_group_byname($name);
    if($group === false)
    {
      // delete this item
      delete_inventory_byid($this_inventory['idnum']);

      // create the group
      $groupid = create_group($name, $user['idnum']);

      // join your own group
      $groups = take_apart(',', $user['groups']);
      $groups[] = $groupid;
      update_user_groups($user['idnum'], $groups);

      $step = 2;

      require_once 'commons/statlib.php';
      record_stat($user['idnum'], 'Created a Group', 1);
    }
    else
      $message = 'That name has already been taken.  Sorry.';
  }
}

if($step == 1)
{
  if($message)
    echo '<span class="failure">' . $message . '</span>';
  else
  {
?>
With this Charter you can start a new group.</p>
<p>Please pick a group name, but remember: the name you choose now is the name you're stuck with forever, so choose wisely!
<?php
  }
?>
</p>
<form action="itemaction.php?idnum=<?= $this_inventory['idnum'] ?>" method="post" onsubmit="return confirm(String.fromCharCode(34) + document.getElementById('groupname').value + String.fromCharCode(34) + ', right?');">
<p><b>Group Name:</b>&nbsp;<input name="groupname" maxlength="24" size="25" id="groupname" /></p>
<p><input type="hidden" name="action" value="create" /><input type="submit" value="Create Group" class="bigbutton" /></p>
</form>
<?php
}
else if($step == 2)
{
?>
<p>Success!</p>
<ul>
 <li><a href="grouppage.php?id=<?= $groupid ?>">Go to group page</a></li>
</ul>
<?php
  $AGAIN_WITH_ANOTHER = true;
}
else
  echo 'meh?  step ' . $step . '?';
?>
