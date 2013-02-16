<?php
if($yes_yes_that_is_fine !== true)
  exit();

$FINISHED_CASTING = true;

for($j = 0; $j < count($userpets); ++$j)
{
  gain_love($userpets[$j], dice_roll(1, 4), true);
  save_pet($userpets[$j], array('love'));
}

echo '<p>The room darkens, then erupts into a dazzling light show.</p>';
?>
