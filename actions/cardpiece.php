<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/mazelib.php';
require_once 'commons/questlib.php';

$piece = generate_maze_piece($this_inventory['itemname']);
?>
<i>This small card has the following image painted on its surface:</i></p>
<p><img src="<?= "gfx/maze/$piece.png" ?>" alt="" />
<?php
if($user['show_pattern'] == 'no')
{
  echo '</p><p><i>What you are meant to do with it, however... perhaps Thaddeus, the Alchemist, knows something.</i>';

  $questval = get_quest_value($user['idnum'], 'PatternActivation');

  if($questval === false)
    add_quest_value($user['idnum'], 'PatternActivation', 1);
}
