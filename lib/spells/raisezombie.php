<?php
if($yes_yes_that_is_fine !== true)
  exit();

require_once 'commons/questlib.php';

$raise_quest = get_quest_value($user['idnum'], 'raise zombie');
if($raise_quest !== false)
{
  if($raise_quest['value'] == 0)
  {
    update_quest_value($raise_quest['idnum'], 1);
    $FINISHED_CASTING = true;
  }
}
else
{
  add_quest_value($user['idnum'], 'raise zombie', 1);
  $FINISHED_CASTING = true;
}

if($FINISHED_CASTING)
  echo '<p>The spell is ready.  All that remains is to find a body to raise...</p>';
else
  echo '<p>You have already prepared this spell.  All that remains is to find a body to raise...</p>';
