<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/polllib.php';
require_once 'commons/ip.php';

$pollid = (int)$this_inventory["data"];

$poll = get_poll_byid($pollid);

if($poll === false)
{
  echo 'This poll sheet has somehow become invalid.  Really, this shoudln\'t happen.  Please report it in Error Reporting.  It\'s possible many residents are having this problem, in which case it\'d be nice if you took a quick look to see if anyone else has already started a thread about the issue.';
}
else
{
  $options = explode('|', $poll['options']);

  if($_POST['action'] == "submit")
  {
    $vote = (int)$_POST['vote'];
    if($vote < 1 || $vote > count($options))
      echo 'Er... what?';
    else
    {
      cast_vote($pollid, $user['idnum'], getip(), $vote);
      delete_inventory_byid($this_inventory['idnum']);
      echo '<i>You drop the completed form off at the City Hall.</i></p><p>Your vote has been tabulated.  Thanks for your time!';
    }
  }
  else
  {
?>
This poll is being conducted by HERG, in order to better facilitate the Residents of the island informally referred to as "PsyPettia."</p>
<p><?= $poll['title'] ?></p>
<form action="itemaction.php?idnum=<?= $this_inventory['idnum'] ?>" method="post">
<ul class="plainlist">
<?php
    $id = 1;
  
    foreach($options as $option)
    {
?>
 <li><input type="radio" name="vote" value="<?= $id ?>" id="o_<?= $id ?>" /> <label for="o_<?= $id ?>"><?= $option ?></label></li>
<?php
      $id++;
    }
?>
</ul>
<p>Additional comments or questions should be discussed in the appropriate section of The Plaza.</p>
<p><input type="hidden" name="action" value="submit" /><input type="submit" value="Submit" /></p>
</form>
<?php
  }
}
?>

