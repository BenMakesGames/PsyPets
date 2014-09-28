<?php
$IGNORE_MAINTENANCE = true;


require_once 'commons/init.php';

$require_petload = "no";

// confirm the session...
require_once "commons/dbconnect.php";
require_once "commons/rpgfunctions.php";
require_once "commons/sessions.php";
require_once "commons/grammar.php";
require_once "commons/formatting.php";

if($admin['clairvoyant'] != 'yes')
{
  header('Location: /');
  exit();
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &nbsp; Administrative Tools &nbsp; Pet Graphic Use</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/admin/tools.php">Administrative Tools</a> &gt; Pet Graphic Use</h4>
<?php
if($error_message)
  echo "<p style=\"color:red;\">" . $error_message . "</p>\n";

$data = $database->FetchSingle('SELECT COUNT(idnum) AS c FROM monster_pets WHERE graphic NOT LIKE \'%/%\'');
$total = $data['c'];

echo '<p>Total pets reported here: ' . $total . '</p>';

$command = "SELECT graphic,COUNT(graphic) AS c FROM `monster_pets` WHERE graphic NOT LIKE '%/%' GROUP BY (graphic) ORDER BY c DESC";
$graphics = $database->FetchMultiple($command);

?>
<table>
 <thead>
  <tr>
   <th class="centered">Pet</th>
   <th class="centered">Number</th>
   <th class="righted">%</th>
   <th class="righted">Top %</th>
  </tr>
 </thead>
 <tbody>
<?php
$rowclass = begin_row_class();

foreach($graphics as $graphic)
{
  $count = (int)$graphic['c'];
  $url = $graphic['graphic'];
  $total_so_far += $count;
?>
 <tr class="<?= $rowclass ?>">
  <td><img src="//<?= $SETTINGS['static_domain'] ?>/gfx/pets/<?= $url ?>" width="48" height="48" /></td>
  <td class="centered"><?= $count ?></td>
  <td class="righted"><?= round($count * 100 / $total, 2) ?>%</td>
  <td class="righted"><?= round($total_so_far * 100 / $total, 2) ?>%</td>
 </tr>
<?php
  $rowclass = alt_row_class($rowclass);
}
?>
 </tbody>
</table>

<?php include "commons/footer_2.php"; ?>
 </body>
</html>
