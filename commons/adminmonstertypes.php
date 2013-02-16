<?php
$MONSTER_TYPES = array(
  'alien', 'beast', 'bird', 'bug',
  'chimera', 'crustacean', 'demon', 'dinosaur', 'dragon',
  'elemental', 'fae', 'fish',
  'golem', 'human', 'mollusk', 'ooze',
  'pastry', 'plant', 'robot',
  'spirit', 'undead', 'whale'
);

function monster_type_xhtml($default = '')
{
  global $MONSTER_TYPES;

  $string = '<select name="type"><option value=""></option>';
  
  foreach($MONSTER_TYPES as $type)
  {
    if($type == $default)
      $string .= '<option value="' . $type . '" selected>' . $type . '</option>';
    else
      $string .= '<option value="' . $type . '">' . $type . '</option>';
  }
  
  $string .= '</select>';
  
  return $string;
}
?>
