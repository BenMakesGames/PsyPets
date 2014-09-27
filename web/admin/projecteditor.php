<?php
$IGNORE_MAINTENANCE = true;


require_once 'commons/init.php';

$require_petload = "no";

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';

if($admin['manageitems'] != 'yes')
{
  header('Location: /admin/tools.php');
  exit();
}

if($_GET['edit'] == 'inventions')
  $edittype = 'inventions';
else if($_GET['edit'] == 'mechanics')
  $edittype = 'mechanics';
else if($_GET['edit'] == 'chemistry')
  $edittype = 'chemistry';
else if($_GET['edit'] == 'smiths')
  $edittype = 'smiths';
else if($_GET['edit'] == 'tailors')
  $edittype = 'tailors';
else if($_GET['edit'] == 'leatherworks')
  $edittype = 'leatherworks';
else if($_GET['edit'] == 'jewelry')
  $edittype = 'jewelry';
else if($_GET['edit'] == 'sculptures')
  $edittype = 'sculptures';
else if($_GET['edit'] == 'carpentry')
  $edittype = 'carpentry';
else if($_GET['edit'] == 'paintings')
  $edittype = 'paintings';
else if($_GET['edit'] == 'bindings')
  $edittype = 'bindings';
else
  $edittype = 'crafts';

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Administrative Tools &gt; Project Editor</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/admin/tools.php">Administrative Tools</a> &gt; Project Editor</h4>
     <ul class="tabbed">
      <li<?= $edittype == 'crafts'      ? ' class="activetab"' : '' ?>><a href="?edit=crafts">Crafts</a></li>
      <li<?= $edittype == 'paintings'   ? ' class="activetab"' : '' ?>><a href="?edit=paintings">Paintings</a></li>
      <li<?= $edittype == 'sculptures'  ? ' class="activetab"' : '' ?>><a href="?edit=sculptures">Sculptures</a></li>
      <li<?= $edittype == 'carpentry'   ? ' class="activetab"' : '' ?>><a href="?edit=carpentry">Carpentry</a></li>
      <li<?= $edittype == 'jewelry'     ? ' class="activetab"' : '' ?>><a href="?edit=jewelry">Jewelry</a></li>
      <li<?= $edittype == 'inventions'  ? ' class="activetab"' : '' ?>><a href="?edit=inventions">Electronics</a></li>
      <li<?= $edittype == 'mechanics'   ? ' class="activetab"' : '' ?>><a href="?edit=mechanics">Mechanics</a></li>
      <li<?= $edittype == 'chemistry'   ? ' class="activetab"' : '' ?>><a href="?edit=chemistry">Chemistry</a></li>
      <li<?= $edittype == 'smiths'      ? ' class="activetab"' : '' ?>><a href="?edit=smiths">Smiths</a></li>
      <li<?= $edittype == 'tailors'     ? ' class="activetab"' : '' ?>><a href="?edit=tailors">Tailors</a></li>
      <li<?= $edittype == 'leatherworks'? ' class="activetab"' : '' ?>><a href="?edit=leatherworks">Leatherworks</a></li>
      <li<?= $edittype == 'bindings'    ? ' class="activetab"' : '' ?>><a href="?edit=bindings">Bindings</a></li>
     </ul>
     <ul>
      <li><a href="/admin/newproject.php?edit=<?= $edittype ?>">New project</a></li>
     </ul>
<table>
<tr class="titlerow">
 <th>&nbsp;</th>
 <th>Difficulty</th>
 <th>Ingredients</th>
 <th>Makes</th>
 <th><nobr>In Pattern?</nobr></th>
 <th>Months</th>
 <th>Openness</th>
 <th style="width:100px;"></th>
</tr>
<?php
$item_use = array();

$command = "SELECT * FROM psypets_$edittype ORDER BY difficulty ASC";
$result = mysql_query($command);

$bgcolor = begin_row_class();

while($project = mysql_fetch_assoc($result))
{
?>
<tr class="<?= $bgcolor ?>">
 <td valign="top"><a href="admineditproject.php?type=<?= $edittype ?>&amp;id=<?= $project['idnum'] ?>">edit</a></td>
 <td valign="top" class="centered"><?= $project['difficulty'] ?></td>
 <td valign="top"><?php
  $items = explode(',', $project['ingredients']);
  foreach($items as $item)
  {
    echo '<a href="/encyclopedia2.php?item=' . link_safe($item) . '">' . $item . '</a><br />';
    $item_use[$item]++;
  }
 ?></td>
 <td valign="top"><?= item_text_link($project['makes']) ?></td>
 <td valign="top" class="centered"><?= $project['mazeable'] ?></td>
 <td valign="top" class="centered"><?= $project['min_month'] . ' - ' . $project['max_month'] ?></td>
 <td valign="top" class="centered"><?= $project['min_openness'] ?></td>
 <td>
<?php
echo '<a href="/admin/projectchangetype.php?id=' . $project['idnum'] . '&amp;from=' . $edittype . '&amp;to=crafts">-&gt; crafts</a><br />';
echo '<a href="/admin/projectchangetype.php?id=' . $project['idnum'] . '&amp;from=' . $edittype . '&amp;to=paintings">-&gt; paintings</a><br />';
echo '<a href="/admin/projectchangetype.php?id=' . $project['idnum'] . '&amp;from=' . $edittype . '&amp;to=sculptures">-&gt; sculptures</a><br />';
echo '<a href="/admin/projectchangetype.php?id=' . $project['idnum'] . '&amp;from=' . $edittype . '&amp;to=tailors">-&gt; tailors</a><br />';
echo '<a href="/admin/projectchangetype.php?id=' . $project['idnum'] . '&amp;from=' . $edittype . '&amp;to=leatherworks">-&gt; leatherworks</a><br />';
echo '<a href="/admin/projectchangetype.php?id=' . $project['idnum'] . '&amp;from=' . $edittype . '&amp;to=carpentry">-&gt; carpentry</a><br />';
echo '<a href="/admin/projectchangetype.php?id=' . $project['idnum'] . '&amp;from=' . $edittype . '&amp;to=jewelry">-&gt; jewelry</a><br />';
echo '<a href="/admin/projectchangetype.php?id=' . $project['idnum'] . '&amp;from=' . $edittype . '&amp;to=electronics">-&gt; electronics</a><br />';
echo '<a href="/admin/projectchangetype.php?id=' . $project['idnum'] . '&amp;from=' . $edittype . '&amp;to=mechanics">-&gt; mechanics</a><br />';
echo '<a href="/admin/projectchangetype.php?id=' . $project['idnum'] . '&amp;from=' . $edittype . '&amp;to=chemistry">-&gt; chemistry</a><br />';
echo '<a href="/admin/projectchangetype.php?id=' . $project['idnum'] . '&amp;from=' . $edittype . '&amp;to=bindings">-&gt; bindings</a><br />';
echo '<a href="/admin/projectchangetype.php?id=' . $project['idnum'] . '&amp;from=' . $edittype . '&amp;to=smiths">-&gt; smiths</a><br />';
?>
 </td>
</tr>
<?php
  $bgcolor = alt_row_class($bgcolor);
}
?>
</table>
<h5>Ingredient Frequency</h5>
<table>
<tr class="titlerow">
<th>Item</th>
<th>Frequency</th>
</tr>
<?php
arsort($item_use);

$row_class = begin_row_class();

foreach($item_use as $item=>$count)
{
  echo '<tr class="' . $row_class . '"><td>' . $item . '</td><td>' . $count . '</td></tr>';
  $row_class = alt_row_class($row_class);
}
?>
</table>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
