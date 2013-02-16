<?php
require_once 'commons/utility.php';

$PERSONALITY_FIELDS = array(
  'open', 'extraverted', 'conscientious', 'playful', 'independent',
);

$EQUIP_FIELDS = array(
  'open', 'extraverted', 'conscientious', 'playful', 'independent',
  'str', 'dex', 'sta', 'int', 'per', 'wit', 'stealth', 'athletics',
  'mining', 'fishing', 'painting', 'sculpting', 'carpentry', 'jeweling',
  'electronics', 'mechanics', 'adventuring', 'hunting', 'gathering', 'smithing',
  'tailoring', 'leather', 'crafting', 'binding', 'chemistry', 'piloting', 'lumberjacking',
  'gardening', 'fertility',
);

function get_equip_message($item, $pet)
{
  $message = '';
  $skills = array();

  if($item['req_str'] > $pet['str'])
    $skills[] = 'strong';

  if($item['req_dex'] > $pet['dex'])
    $skills[] = 'dextrous';

  if($item['req_athletics'] > $pet['athletics'])
    $skills[] = 'athletic';

  if($item['req_sta'] > $pet['sta'])
    $skills[] = 'tough';

  if($item['req_int'] > $pet['int'])
    $skills[] = 'intelligent';

  if($item['req_per'] > $pet['per'])
    $skills[] = 'perceptive';

  if($item['req_wit'] > $pet['wit'])
    $skills[] = 'clever';

  if($pet['zombie'] == 'yes')
    $skills[] = '<strong>alive</strong>';

  if($item['equipl33tonly'] == 'yes')
    $skills[] = 'l33t';

  if(count($skills) > 0)
    $message .= $pet['petname'] . ' is not ' . list_nice($skills, ' or ') . ' enough to use this tool.';

  if($item['equipreincarnateonly'] == 'yes' && $pet['incarnation'] == 1)
    $message .= '<br />Only reincarnated pets may equip this.';

  return $message;
}

function EquipBonusDesc($bonus)
{
  if($bonus >= 10)
    return '<span style="color:#e70">m</span><span style="color:#f00">y</span><span style="color:#c06">t</span><span style="color:#80f">h</span><span style="color:#44f">i</span><span style="color:#06c">c</span> bonus';
  else if($bonus >= 7)
    return 'extreme bonus';
  else if($bonus >= 4)
    return 'major bonus';
  else if($bonus >= 1)
    return 'minor bonus';
  else if($bonus <= -10)
    return '<span style="color:#e70">m</span><span style="color:#f00">y</span><span style="color:#c06">t</span><span style="color:#80f">h</span><span style="color:#44f">i</span><span style="color:#06c">c</span> penalty';
  else if($bonus <= -7)
    return 'extreme penalty';
  else if($bonus <= -4)
    return 'major penalty';
  else if($bonus <= -1)
    return 'minor penalty';
  else
    return '';
}

function EquipLevel($item)
{
  global $EQUIP_FIELDS, $PERSONALITY_FIELDS;
  
  $best = false;

  foreach($EQUIP_FIELDS as $field)
  {
    if($item['equip_' . $field] != 0 && (abs($item['equip_' . $field]) > abs($best) || $best === false))
    {
      // personality changes are always "positive"
      if(in_array($field, $PERSONALITY_FIELDS))
        $best = abs($item['equip_' . $field]);
      else
        $best = $item['equip_' . $field];
    }
  }

  if($best == false)
    return 'none';
  else if($best < 0)
    return $best;
  else
    return '+' . $best;
}
?>
