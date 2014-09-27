<?php
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';

$command = 'SELECT a.user,a.display,a.lastactivity,b.maxbulk FROM monster_users AS a LEFT JOIN monster_houses AS b ON a.idnum=b.userid WHERE a.lastactivity>=' . ($now - 7 * 24 * 60 * 60) . ' ORDER BY maxbulk DESC';
$results = $database->FetchMultiple($command, 'fetching some pet/house stats');

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; City Hall &gt; Help Desk</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
<?php
echo '<p>If a pet wants 100 space to be happy, the following residents (active within the last week) will be above that limit:</p>' .
     '<p><i>(Remember that if this change takes place, the starting house size will be upped to 100.)</i></p>' .
     '<p><i>(Scroll to the bottom to see the number of residents active within the last week vs. the number who are over.)</i></p>' .
     '<table><tr class="titlerow"><th>Resident</th><th>No. of Pets</th><th>Pet Max</th><th>Pet Size</th><th>House Size</th>';

$rowclass = begin_row_class();

foreach($results as $result)
{
  $command = 'SELECT COUNT(idnum) AS c FROM monster_pets WHERE user=' . quote_smart($result['user'])  . ' AND location=\'home\' AND dead=\'no\'';
  $data = $database->FetchSingle($command, 'fetching pets');
  
  $num_pets = $data['c'];
  
  if($result['maxbulk'] > 20000)
  {
    $real_bulk = 20000;
    $bulk_note = '+';
  }
  else
  {
    $real_bulk = $result['maxbulk'];
    $bulk_note = '';
  }

  $max_pets = floor($real_bulk / 100);

  if($max_pets < $num_pets)
  {
    $affected++;
    
    if($real_bulk >= 100)
      $less_affected++;
?>
<tr class="<?= $rowclass ?>">
 <td><?= resident_link($result['display']) ?></td>
 <td class="centered"><?= $num_pets ?></td>
 <td class="centered"><?= $max_pets ?></td>
 <td class="centered"><?= $num_pets * 100 ?></td>
 <td class="centered"><?= $real_bulk . $bulk_note ?></td>
 <td class="centered">
</tr>
<?php
    $rowclass = alt_row_class($rowclass);
  }
}
?>
</table>
<p>Of the residents active within the last week (<?= count($results) ?>), <?= $affected ?> would be affected by this change.  Ignoring residents with less than 100 space, that leaves <?= $less_affected ?> affected residents.</p>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
