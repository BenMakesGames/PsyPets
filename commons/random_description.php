<?php
function random_description()
{
  $random_personalities = array(
    'an alluring',
    'an ambitious',
    'an attentive',
    'a boisterous',
    'a bold',
    'a brash',
    'a brave',
    'a calm',
    'a clever',
    'a concerned',
    'a corageous',
    'a cowardly',
    'a cynical',
    'a debonair',
    'a delightful',
    'a depraved',
    'a determined',
    'an emotional',
    'an emotionless',
    'an energetic',
    'a friendly',
    'a gentle',
    'a grim',
    'a haughty',
    'a hostile',
    'an impatient',
    'an innocent',
    'an intrusive',
    'an introverted',
    'a lazy',
    'a merry',
    'a modest',
    'a nervous',
    'an outgoing',
    'a patient',
    'a proud',
    'a reckless',
    'a reserved',
    'a respectable',
    'a righteous',
    'a romantic',
    'a sensitive',
    'a shifty',
    'a shrewd',
    'a sincere',
    'a smiling',
    'a spirited',
    'a suspicious',
    'a thoughtful',
    'a whining',
    'a wild',
    'a quiet',
  );

  $random_traits = array(
    'brown-haired', // hair
    'black-haired',
    'red-haired',
    'white-haired',
    'blonde',
    'bald',
    'blue-eyed',    // eyes
    'brown-eyed',
    'red-eyed',
    'green-eyed',
    'blind',
    'one-eyed',
    'skinny',       // physical build
    'muscular',
    'round',
    'tall',
    'short',
    'pale',         // skin
    'tanned',
    'dark-skinned',
    'exotic',
    'blue-skinned',
    'wizened',
    'smooth-skinned',
    'bespectacled', // "clothing"
    'perfumed',
    'naked',
    'scarred',      // physical quirks
    'flea-infested',
    'well-dressed',
    'limping',
    'horned',
    'long-tailed',
    'pointy-eared',
  );

  $random_jobs = array(
    'lover', 'gambler', 'stranger', 'begger', 'pirate', 'poet', 'jeweler',
    'scholar', 'captain', 'musician', 'cook', 'sculptor', 'adventurer', 'spy',
    'shopkeep', 'bounty hunter', 'treasure hunter', 'explorer', 'student',
    'priest', 'priestess', 'maid', 'engineer', 'fishmonger', 'street-urchin',
    'dilettante', 'jester', 'artist', 'bagniokeeper',
  );

  return
    $random_personalities[array_rand($random_personalities)] . ', ' .
    $random_traits[array_rand($random_traits)] . ' ' .
    $random_jobs[array_rand($random_jobs)]
  ;
}
?>
