<?php
$DEBRIS_ITEMS = array(
  'Glass', 'Glass', 'Glass',
  'Fluff', 'Fluff', 'Fluff',
  'Plastic', 'Plastic',
  'Tin', 'Tin',
  'Iron', 'Iron',
  'Wood', 'Wood',
  'Rubber',
  'Paper',
  'Clay',
  'Copper',
  'Silver',
  'Gold',
  'Zinc',
  'Red Dye',
  'Blue Dye',
  'Yellow Dye',
  'Black Dye',
);

function GenerateItemFromDebris($user, $is_debris = false)
{
  global $DEBRIS_ITEMS;

  if($is_debris && $user['license'] == 'yes')
  {
    $profile = fetch_single('SELECT text,last_update FROM psypets_profile_text WHERE player_id=' . $user['idnum'] . ' LIMIT 1');
    $data = fetch_single('SELECT COUNT(friendid) AS qty FROM psypets_user_friends WHERE userid=' . $user['idnum']);
    
    $friend_count = $data['qty'];
    
    // if your profile is at least 2000 characters, was last updated in the last 3 months, and you have at least 30 friends...
    if(strlen($profile['text']) >= 2000 && $profile['last_update'] > (time() - 90 * 24 * 60 * 60) && $friend_count >= 30)
    {
      if(mt_rand(1, 600) == 1)
        return 'Group Charter';
    }
  }

  if(mt_rand(1, 1000) == 1)
    return 'Mouth';
  else
    return $DEBRIS_ITEMS[array_rand($DEBRIS_ITEMS)];
}
?>
