<?php
// keys match project types! (for crafts)
$ACTIVITY_STATS = array(
  'sleep' => 'Slept',
  'eat' => 'Ate',
  'eat_unable' => 'Unable to Eat',
  'beg' => 'Begged for Food',
  'safety' => 'Safety',
  'safety_unable' => 'No Safety Items',
  'love' => 'Love',
  'love_unable' => 'No Love Items',
  'esteem' => 'Esteem',
  'esteem_unable' => 'No Esteem Items',
  'lycanthrope' => 'Changed Form',
  'birth' => 'Gave Birth',
  'hangout' => 'Hung Out with a Friend',
  'hangout_unable' => 'No Friends Available',
  'craft_success' => 'Art Success',
  'craft_failure' => 'Art Failure',
  'craft_unable' => 'No Materials For Art',
  'carpenter_success' => 'Carpentry Success',
  'carpenter_failure' => 'Carpentry Failure',
  'carpenter_unable' => 'No Materials For Carpentry',
  'sculpture_success' => 'Sculpting Success',
  'sculpture_failure' => 'Sculpting Failure',
  'sculpture_unable' => 'No Materials For Sculpting',
  'paint_success' => 'Painting Success',
  'paint_failure' => 'Painting Failure',
  'paint_unable' => 'No Materials For Painting',
  'jewel_success' => 'Jewling Success',
  'jewel_failure' => 'Jewling Failure',
  'jewel_unable' => 'No Materials For Jewling',
  'paint_success' => 'Painting Success',
  'paint_failure' => 'Painting Failure',
  'paint_unable' => 'No Materials For Painting',
  'engineer_success' => 'Electronics Success',
  'engineer_failure' => 'Electronics Failure',
  'engineer_unable' => 'No Materials For Electronics',
  'mechanical_success' => 'Mechanics Success',
  'mechanical_failure' => 'Mechanics Failure',
  'mechanical_unable' => 'No Materials For Mechanics',
  'smith_success' => 'Smithing Success',
  'smith_failure' => 'Smithing Failure',
  'smith_unable' => 'No Materials For Smithing',
  'tailor_success' => 'Tailoring Success',
  'tailor_failure' => 'Tailoring Failure',
  'tailor_unable' => 'No Materials For Tailoring',
  'chemistry_success' => 'Chemistry Success',
  'chemistry_failure' => 'Chemistry Failure',
  'chemistry_unable' => 'No Materials For Chemistry',
  'binding_success' => 'Magic-binding Success',
  'binding_failure' => 'Magic-binding Failure',
  'binding_unable' => 'No Materials For Binding',
  'online_success' => 'Online Game Success',
  'online_failure' => 'Online Game Failure',
  'online_unable' => 'No One Else Online',
  'farming_success' => 'Farming Success',
  'farming_failure' => 'Farming Failure',
  'farming_unable' => 'Farm Silo Full',
  'gather_success' => 'Gathering Success',
  'gather_failure' => 'Gathering Failure',
  'mine_success' => 'Mining Success',
  'mine_failure' => 'Mining Failure',
  'lumberjack_success' => 'Lumberjacking Success',
  'lumberjack_failure' => 'Lumberjacking Failure',
  'adventure_success' => 'Adventuring Success',
  'adventure_failure' => 'Adventuring Failure',
  'hunt_success' => 'Hunting Success',
  'hunt_failure' => 'Hunting Failure',
  'fish_success' => 'Fishing Success',
  'fish_failure' => 'Fishing Failure',
  'gardening_success' => 'Gardening Success',
  'gardening_failure' => 'Gardening Failure',
  'aliens_defeat' => 'Aliens Defeated',
  'aliens_steal' => 'Aliens Stolen From',
  'aliens_sabotage' => 'Alien Devices Sabotaged',
  'aliens_failure' => 'Alien Fight Failures',
);

function PetActivityStatsExist($petid)
{
  $command = 'SELECT petid FROM psypets_petstats WHERE petid=' . $petid . ' LIMIT 1';
  $stats = fetch_single($command, 'fetching pet activity stats');

  return($stats !== false);
}

function DeletePetActivityStats($petid)
{
  $command = 'DELETE FROM psypets_petstats WHERE petid=' . $petid . ' LIMIT 1';
  $stats = $GLOBALS['database']->FetchNone($command);
}

function RenderPetActivityStatsXHTML($petid)
{
  global $ACTIVITY_STATS;

  $command = 'SELECT * FROM psypets_petstats WHERE petid=' . $petid . ' LIMIT 1';
  $stats = fetch_single($command, 'fetching pet activity stats');
  
  if($stats === false)
    return '<p>No activity statistics have been recorded for this pet.</p>';

  $rows = array();

  $rowclass = begin_row_class();

  $pet_stat_data = array();
  $pet_stat_data_total = 0;
  
  foreach($stats as $key=>$value)
  {
    if($key != 'petid' && $value > 0)
    {
      $title = str_replace(array('_success', '_unable', '_failure'), array('', '', ''), $key);
      $pet_stat_data[$title]['title'] = $title;
      $pet_stat_data[$title]['hours'] += $value;
      $pet_stat_data_total += $value;
    }
  }
  
  function stat_compare($a, $b)
  {
    if($a['hours'] < $b['hours'])
      return 1;
    else if($a['hours'] > $b['hours'])
      return -1;
    else
      return 0;
  }
  
  if(count($pet_stat_data) > 0)
  {
    usort($pet_stat_data, stat_compare);
  
    $xhtml .= '<div style="float:right;">';
    $xhtml .= '<div id="pet-stat-graph"></div><p style="text-align:center;" id="pet-stat-graph-title">(hover over pie slices for more)</p>';
    $xhtml .= '
      <script type="text/javascript">
      var pet_stat_data = ' . json_encode($pet_stat_data) . ';
      var a = pv.Scale.linear(0, ' . $pet_stat_data_total . ').range(0, 2 * Math.PI);

      new pv.Panel()
        .canvas(\'pet-stat-graph\')
        .width(300)
        .height(300)
      .add(pv.Wedge)
        .data(pet_stat_data)
        .left(150)
        .bottom(150)
        .outerRadius(140)
        .angle(function(d) { return a(d.hours); })
        .event(\'mouseover\', function(d) { $(\'#pet-stat-graph-title\').html(d.title + \' (\' + (d.hours * 100 / ' . $pet_stat_data_total . ').toFixed(1) + \'%)\'); })
        .event(\'mouseout\', function() { $(\'#pet-stat-graph-title\').html(\'(hover over pie slices for more)\'); })
      .anchor(\'center\').add(pv.Label)
        .visible(function(d) { return(d.hours > ' . $pet_stat_data_total . ' / 40); })
        .text(function(d) { return d.title; })
      .root.render()
      ;
      </script>
    ';
    $xhtml .= '</div>';
  }

  $xhtml .= '
    <table id="petstats">
     <thead><tr class="titlerow"><th>Activity</th><th>Success</th><th>Failure</th><th>Unable</th></tr></thead>
     <tbody>
      <tr class="row"><th>Slept</th><td>' . $stats['sleep'] . '</td><td class="dim">&ndash;</td><td class="dim">&ndash;</td></tr>
      <tr class="altrow"><th>Ate</th><td>' . $stats['eat'] . '</td><td class="dim">&ndash;</td><td>' . $stats['eat_unable'] . '</td></tr>
      <tr class="row"><th>Begged</th><td>' . $stats['beg'] . '</td><td class="dim">&ndash;</td><td class="dim">&ndash;</td></tr>
      <tr class="altrow"><th>Safety</th><td>' . $stats['safety'] . '</td><td class="dim">&ndash;</td><td>' . $stats['safety_unable'] . '</td></tr>
      <tr class="row"><th>Love</th><td>' . $stats['love'] . '</td><td class="dim">&ndash;</td><td>' . $stats['love_unable'] . '</td></tr>
      <tr class="altrow"><th>Esteem</th><td>' . $stats['esteem'] . '</td><td class="dim">&ndash;</td><td>' . $stats['esteem_unable'] . '</td></tr>
      <tr class="row"><th>Socialized</th><td>' . $stats['hangout'] . '</td><td class="dim">&ndash;</td><td>' . $stats['hangout_unable'] . '</td></tr>
      <tr class="altrow"><th>Changed Form</th><td>' . $stats['lycanthrope'] . '</td><td class="dim">&ndash;</td><td class="dim">&ndash;</td></tr>
      <tr class="row"><th>Gave Birth</th><td>' . $stats['birth'] . '</td><td class="dim">&ndash;</td><td class="dim">&ndash;</td></tr>
      <tr class="altrow"><th>Handicrafts</th><td>' . $stats['craft_success'] . '</td><td>' . $stats['craft_failure'] . '</td><td>' . $stats['craft_unable'] . '</td></tr>
      <tr class="row"><th>Tailory</th><td>' . $stats['tailor_success'] . '</td><td>' . $stats['tailor_failure'] . '</td><td>' . $stats['tailor_unable'] . '</td></tr>
      <tr class="altrow"><th>Leatherworks</th><td>' . $stats['leatherwork_success'] . '</td><td>' . $stats['leatherwork_failure'] . '</td><td>' . $stats['leatherwork_unable'] . '</td></tr>
      <tr class="row"><th>Painting</th><td>' . $stats['paint_success'] . '</td><td>' . $stats['paint_failure'] . '</td><td>' . $stats['paint_unable'] . '</td></tr>
      <tr class="altrow"><th>Carpentry</th><td>' . $stats['carpenter_success'] . '</td><td>' . $stats['carpenter_failure'] . '</td><td>' . $stats['carpenter_unable'] . '</td></tr>
      <tr class="row"><th>Jewelry</th><td>' . $stats['jewel_success'] . '</td><td>' . $stats['jewel_failure'] . '</td><td>' . $stats['jewel_unable'] . '</td></tr>
      <tr class="altrow"><th>Sculpture</th><td>' . $stats['sculpture_success'] . '</td><td>' . $stats['sculpture_failure'] . '</td><td>' . $stats['sculpture_unable'] . '</td></tr>
      <tr class="row"><th>Mechanics</th><td>' . $stats['mechanical_success'] . '</td><td>' . $stats['mechanical_failure'] . '</td><td>' . $stats['mechanical_unable'] . '</td></tr>
      <tr class="altrow"><th>Electronics</th><td>' . $stats['engineer_success'] . '</td><td>' . $stats['engineer_failure'] . '</td><td>' . $stats['engineer_unable'] . '</td></tr>
      <tr class="row"><th>Chemistry</th><td>' . $stats['chemistry_success'] . '</td><td>' . $stats['chemistry_failure'] . '</td><td>' . $stats['chemistry_unable'] . '</td></tr>
      <tr class="altrow"><th>Smithing</th><td>' . $stats['smith_success'] . '</td><td>' . $stats['smith_failure'] . '</td><td>' . $stats['smith_unable'] . '</td></tr>
      <tr class="row"><th>Magic-binding</th><td>' . $stats['binding_success'] . '</td><td>' . $stats['binding_failure'] . '</td><td>' . $stats['binding_unable'] . '</td></tr>
      <tr class="altrow"><th>Online Gaming</th><td>' . $stats['online_success'] . '</td><td>' . $stats['online_failure'] . '</td><td>' . $stats['online_unable'] . '</td></tr>
      <tr class="row"><th>Farming</th><td>' . $stats['farming_success'] . '</td><td>' . $stats['farming_failure'] . '</td><td>' . $stats['farming_unable'] . '</td></tr>
      <tr class="altrow"><th>Adventuring</th><td>' . $stats['adventure_success'] . '</td><td>' . $stats['adventure_failure'] . '</td><td class="dim">&ndash;</td></tr>
      <tr class="row"><th>Hunting</th><td>' . $stats['hunt_success'] . '</td><td>' . $stats['hunt_failure'] . '</td><td class="dim">&ndash;</td></tr>
      <tr class="altrow"><th>Fishing</th><td>' . $stats['fish_success'] . '</td><td>' . $stats['fish_failure'] . '</td><td class="dim">&ndash;</td></tr>
      <tr class="row"><th>Gathering</th><td>' . $stats['gather_success'] . '</td><td>' . $stats['gather_failure'] . '</td><td class="dim">&ndash;</td></tr>
      <tr class="altrow"><th>Lumberjacking</th><td>' . $stats['lumberjack_success'] . '</td><td>' . $stats['lumberjack_failure'] . '</td><td class="dim">&ndash;</td></tr>
      <tr class="row"><th>Mining</th><td>' . $stats['mine_success'] . '</td><td>' . $stats['mine_failure'] . '</td><td class="dim">&ndash;</td></tr>
      <tr class="altrow"><th>Gardening</th><td>' . $stats['gardening_success'] . '</td><td>' . $stats['gardening_failure'] . '</td><td class="dim">&ndash;</td></tr>
     </tbody>
    </table>
  ';
  
  if($stats['aliens_defeat'] + $stats['aliens_steal'] + $stats['aliens_defeat'] > 0)
  {
    $xhtml .= '
      <ul>
       <li>' . $stats['aliens_defeat'] . ' Crop Circle Aliens defeated</li>
       <li>' . $stats['aliens_steal'] . ' Crop Circle Aliens stolen from</li>
       <li>' . $stats['aliens_defeat'] . ' Crop Circle Alien devices sabotaged</li>
       <li>Defeated by ' . $stats['aliens_failure'] . ' Crop Circle Aliens</li>
      </ul>
    ';
  }

  return $xhtml;
}

function RenderPetActivityStatsText($petid)
{
  global $ACTIVITY_STATS;

  $command = 'SELECT * FROM psypets_petstats WHERE petid=' . $petid . ' LIMIT 1';
  $stats = fetch_single($command, 'fetching pet activity stats');

  if($stats === false)
    return 'No activity statistics have been recorded for this pet.';

  $rows = array();

  $rowclass = begin_row_class();

  foreach($ACTIVITY_STATS AS $key=>$name)
    $xhtml .= $stats[$key] . ' - ' . $name . "\n";

  return $xhtml;
}
?>
