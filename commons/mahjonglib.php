<?php
$MAHJONG_META_DATA = array(
  'East Wind' => array('suit' => 'wind', 'class' => 'honor', 'value' => 'east'),
  'South Wind' => array('suit' => 'wind', 'class' => 'honor', 'value' => 'south'),
  'West Wind' => array('suit' => 'wind', 'class' => 'honor', 'value' => 'north'),
  'North Wind' => array('suit' => 'wind', 'class' => 'honor', 'value' => 'west'),
  'Red Dragon' => array('suit' => 'dragon', 'class' => 'honor', 'value' => 'red'),
  'White Dragon' => array('suit' => 'dragon', 'class' => 'honor', 'value' => 'white'),
  'Green Dragon' => array('suit' => 'dragon', 'class' => 'honor', 'value' => 'green'),

  '1 Bamboo' => array('suit' => 'bamboo', 'class' => 'terminal', 'value' => 1),
  '2 Bamboo' => array('suit' => 'bamboo', 'class' => 'simple', 'value' => 2),
  '3 Bamboo' => array('suit' => 'bamboo', 'class' => 'simple', 'value' => 3),
  '4 Bamboo' => array('suit' => 'bamboo', 'class' => 'simple', 'value' => 4),
  '5 Bamboo' => array('suit' => 'bamboo', 'class' => 'simple', 'value' => 5),
  '6 Bamboo' => array('suit' => 'bamboo', 'class' => 'simple', 'value' => 6),
  '7 Bamboo' => array('suit' => 'bamboo', 'class' => 'simple', 'value' => 7),
  '8 Bamboo' => array('suit' => 'bamboo', 'class' => 'simple', 'value' => 8),
  '9 Bamboo' => array('suit' => 'bamboo', 'class' => 'terminal', 'value' => 9),

  '1 Circle' => array('suit' => 'circle', 'class' => 'terminal', 'value' => 1),
  '2 Circle' => array('suit' => 'circle', 'class' => 'simple', 'value' => 2),
  '3 Circle' => array('suit' => 'circle', 'class' => 'simple', 'value' => 3),
  '4 Circle' => array('suit' => 'circle', 'class' => 'simple', 'value' => 4),
  '5 Circle' => array('suit' => 'circle', 'class' => 'simple', 'value' => 5),
  '6 Circle' => array('suit' => 'circle', 'class' => 'simple', 'value' => 6),
  '7 Circle' => array('suit' => 'circle', 'class' => 'simple', 'value' => 7),
  '8 Circle' => array('suit' => 'circle', 'class' => 'simple', 'value' => 8),
  '9 Circle' => array('suit' => 'circle', 'class' => 'terminal', 'value' => 9),

  '1 Character' => array('suit' => 'character', 'class' => 'terminal', 'value' => 1),
  '2 Character' => array('suit' => 'character', 'class' => 'simple', 'value' => 2),
  '3 Character' => array('suit' => 'character', 'class' => 'simple', 'value' => 3),
  '4 Character' => array('suit' => 'character', 'class' => 'simple', 'value' => 4),
  '5 Character' => array('suit' => 'character', 'class' => 'simple', 'value' => 5),
  '6 Character' => array('suit' => 'character', 'class' => 'simple', 'value' => 6),
  '7 Character' => array('suit' => 'character', 'class' => 'simple', 'value' => 7),
  '8 Character' => array('suit' => 'character', 'class' => 'simple', 'value' => 8),
  '9 Character' => array('suit' => 'character', 'class' => 'terminal', 'value' => 9),
);

function is_mahjong_hand($sets, $pair)
{
  global $MAHJONG_META_DATA;

  $hand = true;

  if(count($pair) == 2 && $pair[0] == $pair[1])
    ; // pair
  else
    $hand = false;

  if(count($sets) != 4)
    $hand = false;
  else
  {
    foreach($s = 0; $s <= 4; ++$s)
    {
      $set = $sets[$s];

      if(count($set) == 3)
      {
        if($set[0] == $set[1] && $set[1] == $set[2])
          ; // pung
        else if(
          $MAHJONG_META_DATA[$set[0]]['suit'] == $MAHJONG_META_DATA[$set[1]]['suit'] &&
          $MAHJONG_META_DATA[$set[1]]['suit'] == $MAHJONG_META_DATA[$set[2]]['suit'] &&
          $MAHJONG_META_DATA[$set[0]]['value'] == $MAHJONG_META_DATA[$set[1]]['value'] + 1 &&
          $MAHJONG_META_DATA[$set[1]]['value'] == $MAHJONG_META_DATA[$set[2]]['value'] + 1)
          ; // chow
        else
        {
          $hand = false;
          break;
        }
      }
      else if(count($set) == 4)
      {
        if($set[0] == $set[1] && $set[1] == $set[2] && $set[2] == $set[3])
          ; // kong
        else
        {
          $hand = false;
          break;
        }
      }
      else
      {
        $hand = false;
        break;
      }
    }
  }
  
  return $hand;
}
?>
