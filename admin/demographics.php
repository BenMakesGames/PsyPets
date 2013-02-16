<?php
$_GET['maintenance'] = 'no';

require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$require_petload = "no";

// confirm the session...
require_once "commons/dbconnect.php";
require_once "commons/rpgfunctions.php";
require_once "commons/sessions.php";
require_once "commons/grammar.php";
require_once "commons/formatting.php";

if($admin["clairvoyant"] != "yes")
{
  header('Location: /');
  exit();
}

if($_POST['recency'])
  $recency = (int)$_POST['recency'];
else
  $recency = 672;

$last_activity = $now - $recency * 60 * 60;

$command = "SELECT a.age,a.gender FROM monster_profiles AS a LEFT JOIN monster_users AS b ON a.idnum=b.idnum WHERE b.is_npc='no' AND b.disabled='no' AND a.enabled='yes' AND a.age>0 AND b.lastactivity>" . $last_activity;
$result = mysql_query($command);

$min_age = 512;
$max_age = 0;

while($profile = mysql_fetch_assoc($result))
{
  if($profile["age"] < $min_age)
    $min_age = $profile["age"];
  if($profile["age"] > $max_age)
    $max_age = $profile["age"];

  $age[$profile["age"]]++;
  $gender[$profile["gender"]]++;
  $age_bygenger[$profile["gender"]][$profile["age"]]++;
  $gender_byage[$profile["age"]][$profile["gender"]]++;
}

mysql_free_result($result);

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &nbsp; Administrative Tools &nbsp; Resident Demographics</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/admin/tools.php">Administrative Tools</a> &gt; Resident Demographics</h4>
<?php
if($error_message)
  echo "<p style=\"color:red;\">" . $error_message . "</p>\n";
?>
  <form method="post">
  <p>Active within the last <input type="number" name="recency" value="<?= $recency ?>" /> hours <input type="submit" value="Search" /></p>
  </form>
  <p>Male: <?= $gender['male'] ?></p>
  <p>Female: <?= $gender['female'] ?></p>
  <p>None: <?= $gender['none'] ?></p>
  <table>
   <tr class="titlerow">
    <th>Age</th>
    <th>Male</th>
    <th>Female</th>
    <th>None</th>
    <th>Total</th>
    <th></th>
   </tr>
<?php
 $row = begin_row_class();
for($i = $min_age; $i <= $max_age; ++$i)
{
?>
   <tr class="<?= $row ?>">
    <th><?= $i ?></th>
    <td><?= $gender_byage[$i]['male'] ?></td>
    <td><?= $gender_byage[$i]['female'] ?></td>
    <td><?= $gender_byage[$i]['none'] ?></td>
    <td><?= $age[$i] ?></td>
    <td><div style="float:left;height:8px;width:<?= (int)$gender_byage[$i]['none'] * 2 ?>px; background-color: #999;"></div><div style="float:left;height:8px;width:<?= (int)$gender_byage[$i]['male'] * 2 ?>px; background-color: #369;"></div><div style="float:left;height:8px;width:<?= (int)$gender_byage[$i]['female'] * 2 ?>px; background-color: #f9c;"></div></td>
   </tr>
<?php
   $row = alt_row_class($row);
}

echo '</table>';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
