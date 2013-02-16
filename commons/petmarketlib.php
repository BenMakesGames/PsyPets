<?php
function pet_market_details(&$pet)
{
  global $now;

  $details = array();

  if(strpos($pet['graphic'], '/') !== false)
    $details[] = '(Custom pet graphic)';

  $details[] = 'Level ' . pet_level($pet);
  $details[] = ucfirst(gender_description($pet['gender'], $pet['prolific']));

  if($pet['dead'] == 'no')
  {
    if($pet['energy'] <= 0)
      $details[] = '<span class="failure">Is exhausted.</span>';

    if($pet['food'] <= 0)
      $details[] = '<span class="failure">Is starving.</span>';

    if($pet['safety'] <= 0)
      $details[] = '<span class="failure">Cowers in a corner.</span>';

    if($pet['love'] <= 0)
      $details[] = '<span class="failure">Whines at you.</span>';

    if($pet['esteem'] <= 0)
      $details[] = '<span class="failure">Seems depressed.</span>';

    if($pet['pregnant_asof'] >= 20 * 24)
      $details[] = 'Is near birthing!';
    else if($pet['pregnant_asof'] >= 10 * 24)
      $details[] = 'Is very pregnant!';
    else if($pet['pregnant_asof'] > 0)
      $details[] = 'Is pregnant!';

    else if($mypet['changed'] == 'yes')
      $pet['graphic'] = 'were/form_' . ($mypet['idnum'] % 2 + 1) . '.png';
  }
  else
  {
    $i = $pet['idnum'] % 4 + 1;
    if($i < 10) $i = "0$i";
    $pet['graphic'] = 'dead/tombstone_' . $i . '.png';

    $details[] = '<span class="failure">Is dead!</span>';
  }

  return $details;
}
?>
