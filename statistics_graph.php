<?php
$require_login = "no";
$require_petload = "no";

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/formatting.php';
require_once 'commons/sitestatslib.php';

$term = $_GET['data'];

$points = array(
  'numusers' =>                   array('type' => 'line',    'points' => array('numusers'),                     'colors' => array('#369'),         'legend' => array('Total Accounts')),
  'numactiveusers' =>             array('type' => 'line',    'points' => array('numactiveusers'),               'colors' => array('#369'),         'legend' => array('Players in Last Day')),
  'numweeklyusers' =>             array('type' => 'line',    'points' => array('numweeklyusers'),               'colors' => array('#369'),         'legend' => array('Players in Last Week')),
  'nummonthlyusers' =>            array('type' => 'line',    'points' => array('nummonthlyusers'),              'colors' => array('#369'),         'legend' => array('Players in Last Month')),
  'numpets' =>                    array('type' => 'line',    'points' => array('numpets'),                      'colors' => array('#369'),         'legend' => array('Total Pets')),
  'numactivepets' =>              array('type' => 'line',    'points' => array('numactivepets'),                'colors' => array('#369'),         'legend' => array('Pets in Last Day')),
  'deadpets' =>                   array('type' => 'line',    'points' => array('deadpets'),                     'colors' => array('#c00'),         'legend' => array('Dead Pets (Not Moved-on)')),
  'malepets' =>                   array('type' => 'stacked', 'points' => array('numpets-malepets', 'malepets'), 'colors' => array('#f9f', '#69c'), 'legend' => array('Female Pets', 'Male Pets')),
  'pregnantpets' =>               array('type' => 'line',    'points' => array('pregnantpets'),                 'colors' => array('#f9f'),         'legend' => array('Pregnant Pets')),
  'maxlevel' =>                   array('type' => 'line',    'points' => array('maxlevel'),                     'colors' => array('#369', '#090'), 'legend' => array('Highest Pet Level')),
  'totallevels' =>                array('type' => 'line',    'points' => array('totallevels'),                  'colors' => array('#369'),         'legend' => array('Total Pet Levels')),
  'voucherfavor' =>               array('type' => 'line',    'points' => array('voucherfavor'),                 'colors' => array('#990'),         'legend' => array('Free Favor Collected')),
  'objects' =>                    array('type' => 'line',    'points' => array('objects'),                      'colors' => array('#963'),         'legend' => array('Total Inventory Count')),
  'numposts' =>                   array('type' => 'line',    'points' => array('numposts'),                     'colors' => array('#369'),         'legend' => array('Plaza Posts Made')),
  'numposters' =>                 array('type' => 'line',    'points' => array('numlurkers', 'numposters'),     'colors' => array('#999', '#090'), 'legend' => array('Lurkers', 'Posters')),
  'numlurkers' =>                 array('type' => 'line',    'points' => array('numlurkers', 'numposters'),     'colors' => array('#999', '#090'), 'legend' => array('Lurkers', 'Posters')),
  'pets_per_resident' =>          array('type' => 'line',    'points' => array('numpets/numusers'),             'colors' => array('#990'),         'legend' => array('Average Pets per Player')),
  'active_pets_per_resident' =>   array('type' => 'line',    'points' => array('numactivepets/numactiveusers'), 'colors' => array('#990'),         'legend' => array('Average Pets per Player in Last Day')),
  'female_pets' =>                array('type' => 'stacked', 'points' => array('numpets-malepets', 'malepets'), 'colors' => array('#f9f', '#69c'), 'legend' => array('Female Pets', 'Male Pets')),
  'average_level_per_pet' =>      array('type' => 'line',    'points' => array('totallevels/numpets'),          'colors' => array('#990'),         'legend' => array('Average Pet Level')),
  'total_moneys' =>               array('type' => 'stacked', 'points' => array('savings', 'cash'),              'colors' => array('#060', '#090'), 'legend' => array('Money in Savings', 'Moneys On-hand')),
  'average_money_per_resident' => array('type' => 'line',    'points' => array('(cash+savings)/numusers'),      'colors' => array('#090'),         'legend' => array('Average Moneys per Player')),
  'items_per_resident' =>         array('type' => 'line',    'points' => array('objects/numusers'),             'colors' => array('#990'),         'legend' => array('Average Items per Player'))
);

$data_labels = array_flip($SITE_STATISTICS_LABELS);

if(!array_key_exists($term, $points))
{
  header('Location: /statistics.php');
  exit();
}

$chart_info = $points[$term];

$chart_data = array();
$chart_max = array();
$chart_min = array();

if($user['admin']['clairvoyant'] == 'yes')
{
  $years = (int)$_POST['years'];
  if($years < 1)
    $years = 5;
}
else
  $years = 1;

$data = $database->FetchSingle('SELECT MAX(timestamp) AS max_x FROM monster_statistics');
$max_x = (int)$data['max_x'];

$data = $database->FetchSingle('SELECT MIN(timestamp) AS min_x FROM monster_statistics WHERE timestamp>=' . ($max_x - $years * 365 * 24 * 60 * 60));
$min_x = (int)$data['min_x'];

foreach($chart_info['points'] as $i=>$point)
{
  $chart_max[$i] = false;
  $chart_min[$i] = false;
  
  $results = $database->FetchMultiple('
    SELECT ' . $point . ' AS point,timestamp
    FROM monster_statistics
    WHERE timestamp>=' . $min_x . '
    ORDER BY timestamp ASC
  ');

  foreach($results as $x=>$row)
  {
    $chart_data[$i][] = array('x' => (int)$row['timestamp'], 'y' => $row['point'], 'i' => $i);

    if($chart_max[$i] === false || $chart_max[$i] < $row['point'])
      $chart_max[$i] = $row['point'];

    if($chart_min[$i] === false || $chart_min[$i] > $row['point'])
      $chart_min[$i] = $row['point'];
  }
}

if($chart_info['type'] == 'stacked')
{
  $max_value = array_sum($chart_max);
  $min_value = array_sum($chart_min);
}
else
{
  $max_value = max($chart_max);
  $min_value = min($chart_min);
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Statistics &gt; Graph</title>
<?php include "commons/head.php"; ?>
  <script type="text/javascript" src="http://<?= $SETTINGS['static_domain'] ?>/js/protovis.min.js"></script>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
    <h4><a href="/statistics.php">Statistics</a> &gt; Graph</h4>
    <div id="sad_message" class="failure"></div>
<?php
if($user['admin']['clairvoyant'] == 'yes')
{
?>
    <form method="post">
    <p><input name="years" type="number" min="1" max="5" value="<?= $years ?>" size="1" />-year history <input type="submit" value="View" /></p>
    </form>
<?php
}
?>
    <div style="width: 800px; height: 500px; margin: 0 auto 1em auto; padding: 0; border: 1px solid #ccc;">
     <script type="text/javascript">
      if(!document.implementation.hasFeature("http://www.w3.org/TR/SVG11/feature#BasicStructure", "1.1"))
        $('#sad_message').html('<p>Oh no!  It looks like your browser doesn\'t support SVG rendering, which these pretty graphs rely on.  Usually this means you have Internet Explorer 8 or below, because Internet Explorer is really slow about supporting cool stuff.</p><p>Sorry :(</p>');

      var text_month = [
        'Jan', 'Feb', 'Mar', 'Apr',
        'May', 'Jun', 'Jul', 'Aug',
        'Sep', 'Oct', 'Nov', 'Dec'
      ];

      var data = <?= json_encode($chart_data) ?>;

      var legend = [<?php
$labels = array();
      
foreach($chart_info['legend'] as $i=>$l)
  $labels[] = '{\'i\': ' . $i . ', \'label\': "' . $l . '"}';

echo implode(', ', $labels);
?>];
      
      var colors = <?= json_encode($chart_info['colors']) ?>;
      
      // Sizing and scales.
      var w = 800 - 50,
          h = 500 - 60,
          x = pv.Scale.linear(<?= $min_x ?>, <?= $max_x ?>).range(0, w),
          y = pv.Scale.linear(<?= ($chart_info['type'] == 'line' ? (int)$min_value : 0) ?>, <?= $max_value ?>).range(0, h);

      // The root panel.
      var vis = new pv.Panel()
          .width(w)
          .height(h)
          .bottom(50)
          .left(50)
          .right(0)
          .top(10);

<?php
if($chart_info['type'] == 'stacked')
{
?>
      // The stack layout.
      vis.add(pv.Layout.Stack)
          .layers(data)
          .x(function(d) { return x(d.x); })
          .y(function(d) { return y(d.y); })
        .layer.add(pv.Area)
          .fillStyle(function(d) { return colors[d.i]; });
<?php
}
else if($chart_info['type'] == 'area')
{
?>
      // The area with top line.
      vis.add(pv.Area)
          .data(data[0])
          .bottom(1)
          .left(function(d) { return x(d.x); })
          .height(function(d) { return y(d.y); })
          .fillStyle("rgb(121,173,210)")
        .anchor("top").add(pv.Line)
          .lineWidth(2);
<?php
}
else if($chart_info['type'] == 'line')
{
  foreach($chart_data as $i=>$dummy)
  {
?>
      vis.add(pv.Line)
          .data(data[<?= $i ?>])
          .left(function(d) { return x(d.x); })
          .bottom(function(d) { return y(d.y); })
          .lineWidth(2)
          .strokeStyle(function(d) { return '<?= $chart_info['colors'][$i] ?>'; });
<?php
  }
}
?>

      // X-axis and ticks.
      vis.add(pv.Rule)
          .data(x.ticks())
          .visible(function(d) { return d; })
          .left(x)
          .bottom(-5)
          .height(5)
        .anchor("bottom").add(pv.Label)
          .text(function(d) {
            var x_date = new Date(d * 1000);
            return text_month[x_date.getMonth()] + ' ' + x_date.getFullYear();
          });

      // Y-axis and ticks.
      vis.add(pv.Rule)
          .data(y.ticks(10))
          .bottom(y)
          .strokeStyle(function(d) { return d ? "rgba(0,0,0,.15)" : "#000"; })
        .anchor("left").add(pv.Label)
          .text(function(d) {
            if(d > 1000000)
              return (d / 1000000) + ' mil';
            else if(d > 1000)
              return (d / 1000) + 'k';
            else
              return d;
          });

      // Legend.
      vis.add(pv.Dot)
          .data(legend)
          .bottom(-30)
          .left(function() { return this.index * 120; })
          .size(8)
          .strokeStyle(null)
          .fillStyle(function(d) { return colors[d.i]; })
        .anchor("right").add(pv.Label)
          .text(function(d) { return d.label; });

      vis.render();
     </script>
    </div>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
