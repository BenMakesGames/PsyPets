<?php
$_GET['maintenance'] = 'no';

require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$require_petload = "no";

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/formatting.php';
require_once 'commons/sitestatslib.php';

$term = trim($_GET['data']);

$chart_info = $points[$term];

$chart_data = array();
$chart_max = array();
$chart_min = array();

if($admin['clairvoyant'] != 'yes')
{
  header('Location: /');
  exit();
}

$i = 0;

$data = $database->FetchSingle('SELECT MAX(date) AS max_x FROM psypets_daily_report_stats WHERE name=' . quote_smart($term));
$max_x = $data['max_x'];

$data = $database->FetchSingle('SELECT MIN(date) AS min_x FROM psypets_daily_report_stats WHERE name=' . quote_smart($term));
$min_x = $data['min_x'];

$chart_max[$i] = false;
$chart_min[$i] = false;

$results = $database->FetchMultiple(('
  SELECT SUM(value) AS point,date
  FROM psypets_daily_report_stats
  WHERE
    date>=' . quote_smart($min_x) . ' AND
    name=' . quote_smart($term) . '
  GROUP BY date
  ORDER BY date ASC
');

foreach($results as $x=>$row)
{
  list($y, $m, $d) = explode('-', $row['date']);
  
  $chart_data[$i][] = array('x' => strtotime($row['date']), 'y' => $row['point'], 'i' => $i);

  if($chart_max[$i] === false || $chart_max[$i] < $row['point'])
    $chart_max[$i] = $row['point'];

  if($chart_min[$i] === false || $chart_min[$i] > $row['point'])
    $chart_min[$i] = $row['point'];
}

$max_value = max($chart_max);
$min_value = min($chart_min);

$data_table = print_r($results, true);

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Administrative Tools &gt; Daily Statistics &gt; Graph</title>
<?php include "commons/head.php"; ?>
  <script type="text/javascript" src="//<?= $SETTINGS['static_domain'] ?>/js/protovis.min.js"></script>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
    <h4><a href="/admin/tools.php">Administrative Tools</a> &gt; <a href="/admin/dailystats.php">Daily Statistics</a> &gt; Graph</h4>
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

      var legend = [{'label': "<?= $term ?>"}];
      
      // Sizing and scales.
      var w = 800 - 50,
          h = 500 - 60,
          x = pv.Scale.linear(<?= strtotime($min_x) ?>, <?= strtotime($max_x) ?>).range(0, w),
          y = pv.Scale.linear(<?= $min_value ?>, <?= $max_value ?>).range(0, h);

      // The root panel.
      var vis = new pv.Panel()
          .width(w)
          .height(h)
          .bottom(50)
          .left(50)
          .right(0)
          .top(10);

          <?php
foreach($chart_data as $i=>$dummy)
{
?>
      vis.add(pv.Line)
          .data(data[<?= $i ?>])
          .left(function(d) { return x(d.x); })
          .bottom(function(d) { return y(d.y); })
          .lineWidth(2)
          .strokeStyle(function(d) { return '#369'; });
<?php
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
            return x_date.getDay() + ' ' + text_month[x_date.getMonth()] + ' ' + x_date.getFullYear();
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
          .fillStyle(function(d) { return '#369'; })
        .anchor("right").add(pv.Label)
          .text(function(d) { return d.label; });

      vis.render();
     </script>
    </div>
    <pre><?= $data_table ?></pre>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
