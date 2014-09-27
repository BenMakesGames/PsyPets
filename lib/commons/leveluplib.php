<?php
function suggested_stats($petid)
{
  $command = 'SELECT * FROM psypets_petstats WHERE petid=' . $petid . ' LIMIT 1';
  $stats = fetch_single($command, 'fetching pet stats');
  
  if($stats === false)
    return false;

  $does = array(
    'construction' => $stats['construction_success'] + $stats['construction_failure'] / 2 + $stats['construction_unable'],
    'handicraft' => $stats['craft_success'] + $stats['craft_failure'] / 2 + $stats['craft_unable'],
    'carpenter' => $stats['carpenter_success'] + $stats['carpenter_failure'] / 2 + $stats['carpenter_unable'],
    'sculpture' => $stats['sculpture_success'] + $stats['sculpture_failure'] / 2 + $stats['sculpture_unable'],
    'jewelry' => $stats['jewel_success'] + $stats['jewel_failure'] / 2 + $stats['jewel_unable'] ,
    'painting' => $stats['paint_success'] + $stats['paint_failure'] / 2 + $stats['paint_unable'],
    'electronics' => $stats['engineer_success'] + $stats['engineer_failure'] / 2 + $stats['engineer_unable'],
    'mechanics' => $stats['mechanical_success'] + $stats['mechanical_failure'] / 2 + $stats['mechanical_unable'],
    'smithing' => $stats['smith_success'] + $stats['smith_failure'] / 2 + $stats['smith_unable'],
    'tailory' => $stats['tailor_success'] + $stats['tailor_failure'] / 2 + $stats['tailor_unable'],
    'chemistry' => $stats['chemistry_success'] + $stats['chemistry_failure'] / 2 + $stats['chemistry_unable'],
    'binding' => $stats['binding_success'] + $stats['binding_failure'] / 2 + $stats['binding_unable'],
    'gathering' => $stats['gather_success'] + $stats['gather_failure'] / 2,
    'gardening' => $stats['gardening_success'] + $stats['gardening_failure'] / 2,
    'mining' => $stats['mine_success'] + $stats['mine_failure'] / 2,
    'lumberjacking' => $stats['lumberjack_success'] + $stats['lumberjack_failure'] / 2,
    'adventuring' => $stats['adventure_success'] + $stats['adventure_failure'] / 2,
    'hunting' => $stats['hunt_success'] + $stats['hunt_failure'] / 2,
    'fishing' => $stats['fish_success'] + $stats['fish_failure'] / 2,
    'vhagst' => $stats['online_success'] + $stats['online_failure'] / 2,
  );
    

  $suggest = array(
    'str' => $does['adventuring'] + $does['hunting'] + $does['lumberjacking'] + $does['mining'] + $does['carpenter'] + $does['smithing'],
    'dex' => $does['adventuring'] + $does['fishing'] + $does['hunting'] + $does['vhagst'] + $does['carpenter'] + $does['handicraft'] + $does['jewelry'] + $does['painting'] + $does['sculpture'] + $does['tailory'],
    'sta' => $does['adventuring'] + $does['gathering'] + $does['hunting'] / 3 + $does['lumberjacking'] + $does['mining'] + $does['binding'] + $does['smithing'],
    'per' => $does['fishing'] + $does['gathering'] + $does['hunting'] + $does['lumberjacking'] + $does['mining'] + $does['carpenter'] + $does['chemistry'] + $does['electronics'] + $does['gardening'] + $does['handicraft'] + $does['jewelry'] + $does['mechanics'] + $does['painting'] + $does['sculpture'] + $does['tailory'],
    'int' => $does['gathering'] + $does['chemistry'] + $does['electronics'] + $does['gardening'] + $does['handicraft'] + $does['jewelry'] + $does['binding'] + $does['mechanics'] + $does['painting'] + $does['sculpture'] + $does['smithing'] + $does['tailory'],
    'wit' => $does['adventuring'] / 3 + $does['hunting'] / 3 + $does['vhagst'] + $does['chemistry'] + $does['electronics'] + $does['binding'] + $does['mechanics'],
    'bra' => $does['adventuring'],
    'athletics' => $does['adventuring'] / 3 + $does['hunting'] / 3,
    'stealth' => $does['adventuring'] / 3 + $does['fishing'] / 2 + $does['hunting'] / 2 + $does['vhagst'],
    'sur' => $does['adventuring'] / 3 + $does['hunting'],
    'gathering' => $does['gathering'] + $does['lumberjacking'] + $does['gardening'],
    'fishing' => $does['fishing'],
    'mining' => $does['mining'],
    'cra' => $does['handicraft'],
    'painting' => $does['painting'],
    'carpentry' => $does['carpenter'],
    'jeweling' => $does['jewelry'],
    'sculpting' => $does['sculpture'],
    'eng' => $does['electronics'],
    'mechanics' => $does['mechanics'],
    'chemistry' => $does['chemistry'],
    'smi' => $does['smithing'],
    'tai' => $does['tailory'],
    'binding' => $does['binding'],
  );

  $highest_value = 0;

  foreach($suggest as $stat=>$value)
  {
    if($value > $highest_value)
      $highest_value = $value;
  }

  return $suggest;
}
?>
