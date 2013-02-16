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

$chart_data = array();
$chart_max = array();
$chart_min = array();

if($user['admin']['clairvoyant'] == 'yes')
{
  $years = (int)$_POST['years'];
  if($years < 1)
    $years = 1;
}
else
  $years = 1;

$data = $database->FetchSingle('SELECT MAX(timestamp) AS max_x FROM monster_statistics');
$max_x = (int)$data['max_x'];

$data = $database->FetchSingle('SELECT MIN(timestamp) AS min_x FROM monster_statistics WHERE timestamp>=' . ($max_x - $years * 365 * 24 * 60 * 60));
$min_x = (int)$data['min_x'];

$results = $database->FetchMultiple('
  SELECT nummonthlyusers,timestamp
  FROM monster_statistics
  WHERE timestamp>=' . $min_x . '
  ORDER BY timestamp ASC
');

$chart_max = array(false);
$chart_min = array(false);

$chart_info = array(
  'type' => 'line',
  'colors' => array('#090'),
  'legend' => array('Average Dollars/Player'),
);

$i = 0;

foreach($results as $x=>$row)
{

  $payments = $database->FetchSingle('
    SELECT SUM(value) AS total_money
    FROM psypets_favor_history
    WHERE
      paypalid!=\'\'
      AND timestamp>=' . ($row['timestamp'] - 30 * 24 * 60 * 60) . '
      AND timestamp<=' . $row['timestamp'] . '
  ');

  $value = round($payments['total_money'] / ($row['nummonthlyusers'] * 100), 2);
  
  $chart_data[$i][] = array('x' => (int)$row['timestamp'], 'y' => $value, 'i' => $i);

  if($chart_max[$i] === false || $chart_max[$i] < $value)
    $chart_max[$i] = $value;

  if($chart_min[$i] === false || $chart_min[$i] > $value)
    $chart_min[$i] = $value;
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
  <title><?= $SETTINGS['site_name'] ?> &gt; Statistics &gt; Monthly ARPU</title>
<?php include "commons/head.php"; ?>
  <script type="text/javascript" src="//<?= $SETTINGS['static_domain'] ?>/js/protovis.min.js"></script>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
    <h4><a href="/statistics.php">Statistics</a> &gt; Monthly ARPU</h4>
    <ul class="tabbed">
      <li><a href="statistics_conv.php">Percent of Paying Players</a></li>
      <li class="activetab"><a href="statistics_arpu.php">ARPU</a></li>
      <li><a href="statistics_arppu.php">ARPPU</a></li>
    </ul>
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
            return text_month[x_date.getMonth()] + ' ' + (x_date.getDay() + 1) + ', ' + x_date.getFullYear();
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
              return Math.round(d * 100) / 100.0;
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
