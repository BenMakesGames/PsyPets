<?php
if($okay_to_be_here !== true)
  exit();

$AGAIN_WITH_ANOTHER = true;

echo '<i>At the top of the scroll is simply the instruction to write down the names of your pets. ';

if(count($userpets) == 0)
{
  echo 'Having no pets of your own, you save the scroll for later.</i>';
}
else
{
  echo 'As you write your pets\' names, text appears around them to form sentences...</i></p>';

  $stat_list = array(
    "str" => "stronger", "dex" => "more agile", "sta" => "tougher", "per" => "more perceptive",
    "int" => "more intelligent", "wit" => "faster thinking",
  );

  $average = array();

  foreach($userpets as $num=>$pet)
  {
    $stat = array_rand($stat_list);
    $pet_level = pet_level($pet);

    if(isset($average[$pet_level][$stat]) === false)
    {
      $command = 'SELECT AVG(`' . $stat . '`) AS `average` FROM monster_pets';
      $data = $database->FetchSingle($command, 'selecting average stat score');

      $average[$pet_level][$stat] = $data['average'];
    }

    $avg = $average[$pet_level][$stat];

    if($pet[$stat] > $avg * 1.2)
      echo '<p>' . $pet['petname'] . '... is ' . $stat_list[$stat] . ' than most other pets.</p>';
    else if($avg > $pet[$stat] * 1.2)
      echo '<p>' . $pet['petname'] . '... most other pets are ' . $stat_list[$stat] . '.</p>';
    else
      echo '<p>' . $pet['petname'] . '... isn\'t ' . $stat_list[$stat] . ' than most other pets, yet most other pets aren\'t ' . $stat_list[$stat] . ', either.</p>';
  }

  // delete yourself
  delete_inventory_byid($_GET['idnum']);
?>
<p><i>Having finished writing, the scroll reduces itself to a fine dust before being blown away.</i>
<?php
}
?>
