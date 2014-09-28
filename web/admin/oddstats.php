<?php
$IGNORE_MAINTENANCE = true;


require_once 'commons/init.php';

$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';

if($admin['clairvoyant'] != 'yes')
{
  header('Location: /admin/tools.php');
  exit();
}

$tabs = array(
  1 => 'Book Graphics',
  3 => 'Durability Ratings',
  4 => 'Same-Material Projects',
);

$tab = (int)$_GET['tab'];

if(!array_key_exists($tab, $tabs))
  $tab = 1;

if($tab == 1)
{
  $command = 'SELECT COUNT(*) AS c, graphic FROM `monster_items` WHERE custom=\'no\' AND graphic LIKE \'%book%\' GROUP BY graphic ORDER BY c DESC';
  $result = $database->FetchMultiple($command, 'adminoddstats.php');

  $content = '<table><tr class="titlerow"><th></th><th>Graphic</th><th>Count</th></tr>';

  $class = begin_row_class();
  foreach($result as $item)
  {
    $content .= '<tr class="' . $class . '"><td class="centered"><img src="//' . $SETTINGS['static_domain'] . '/gfx/items/' . $item['graphic'] . '" /></td><td>' . $item['graphic'] . '</td><td class="centered">' . $item['c'] . '</td></tr>';
    $class = alt_row_class($class);
  }

  $content .= '</table>';

  $comment = 'Book graphic usage distribution.';
}
else if($tab == 3)
{
  $command = 'SELECT COUNT(*) AS c,durability,itemname FROM monster_items GROUP BY durability ORDER BY durability DESC';
  $result = $database->FetchMultiple($command, 'adminoddstats.php');

  $content = '<table><tr class="titlerow"><th>Durability</th><th>Count</th><th></th></tr>';

  $class = begin_row_class();
  foreach($result as $item)
  {
    $content .= '<tr class="' . $class . '"><td class="centered">' . $item['durability'] . '</td><td class="centered">' . $item['c'] . '</td><td>' . ($item['c'] == 1 ? $item['itemname'] : '') . '</td></tr>';
    $class = alt_row_class($class);
  }

  $content .= '</table>';

  $comment = 'Item durability distribution.';
}
else if($tab == 4)
{
  $GROUPS = array('crafts', 'inventions', 'smiths');
  
  foreach($GROUPS as $group)
  {
    $command = 'SELECT COUNT(*) AS c,ingredients,GROUP_CONCAT(makes) AS makes FROM `psypets_' . $group . '` GROUP BY(ingredients) ORDER BY c DESC LIMIT 20';
    $result = $database->FetchMultiple($command, 'fetching same-material ' . $group);

    $content .= '<h5>' . $group . '</h5>' .
                '<table><tr class="titlerow"><th>Count</th><th>Materials</th><th>Makes</th></tr>';

    $class = begin_row_class();
    foreach($result as $item)
    {
      $content .= '<tr class="' . $class . '"><td class="centered">' . $item['c'] . '</td><td>' . $item['ingredients'] . '</td><td>' . $item['makes'] . '</tr>';
      $class = alt_row_class($class);
    }

    $content .= '</table>';
    
    $comment = 'Top 20 projects which have the same materials, from each crafting category.';
  }
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Administrative Tools &gt; Odd Statistics</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/admin/tools.php">Administrative Tools</a> &gt; Odd Statistics</h4>
<ul class="tabbed">
<?php
foreach($tabs as $i=>$name)
{
  if($i == $tab)
    echo '<li class="activetab">';
  else
    echo '<li>';

  echo '<a href="adminoddstats.php?tab=' . $i . '">' . $name . '</a></li>' . "\n";
}
?>
</ul>
     <p><?= $comment ?></p>
     <?= $content ?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
