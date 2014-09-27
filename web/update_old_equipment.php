<?php
$require_petload = 'no';
$IGNORE_MAINTENANCE = true;

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/formatting.php';
require_once 'commons/utility.php';

if($user['idnum'] != 1)
  die();

$command = '
  SELECT *
  FROM `monster_items`
  WHERE equip_is_revised = \'no\'
  AND custom != \'yes\'
  ORDER BY idnum ASC
  LIMIT 1
';
$item = $database->FetchSingle($command, 'fetching item');

if($_POST['action'] == 'Submit')
{
  $keys = array(
    'req_str',
    'req_dex',
    'req_sta',
    'req_per',
    'req_int',
    'req_wit',
    'equip_open',
    'equip_extraverted',
    'equip_conscientious',
    'equip_str',
    'equip_dex',
    'equip_sta',
    'equip_per',
    'equip_int',
    'equip_wit',
    'equip_mining',
    'equip_fishing',
    'equip_painting',
    'equip_sculpting',
    'equip_carpentry',
    'equip_jeweling',
    'equip_electronics',
    'equip_mechanics',
    'equip_adventuring',
    'equip_hunting',
    'equip_gathering',
    'equip_smithing',
    'equip_tailoring',
    'equip_crafting',
    'equip_binding',
    'equip_chemistry',
    'equip_piloting',
    'equip_stealth',
    'equip_athletics',
    'equip_fertility'
  );

  foreach($keys as $key)
  {
    $values[] = $key . '=' . (int)$_POST[$key];
  }

  $command = 'UPDATE monster_items SET ' . implode(', ', $values) . ', equip_is_revised=\'yes\' WHERE itemname=' . quote_smart($item['itemname']) . ' LIMIT 1';
  $database->FetchNone($command, 'updating item');

  header('Location: ./update_old_equipment.php');
  exit();
}

$require = explode(',', $item['equipreqs']);
$give = explode(',', $item['equipeffect']);

function render_stat_list($stats)
{
  $stat_names = array(
    'str', 'dex', 'sta', 'per', 'int', 'wit', 'bra', 'athletics', 'stealth',
    'sur', 'cra', 'eng', 'min', 'cap', 'smi',
    'fertility', 'tai', 'pil'
  );

  foreach($stats as $i=>$value)
  {
    if($value != 0)
      echo $stat_names[$i] . ': ' . $value . '<br />';
  }
}

$new_stats = array(
  'req_str' => $require[0] + ceil($require[6] / 2) + ceil($require[7] / 2),
  'req_dex' => $require[1] + floor($require[6] / 2) + ceil($require[8] / 2) + floor($require[7] / 2),
  'req_sta' => $require[2] + $require[9],
  'req_per' => $require[3] + floor($require[8] / 2) + ceil($require[10] / 2),
  'req_int' => $require[4] + $require[11] + floor($require[10] / 2),
  'req_wit' => $require[5],
  
  'equip_open' => 0,
  'equip_extraverted' => 0,
  'equip_conscientious' => 0,
  'equip_str' => $give[0],
  'equip_dex' => $give[1],
  'equip_sta' => $give[2],
  'equip_per' => $give[3],
  'equip_int' => $give[4],
  'equip_wit' => $give[5],
  'equip_mining' => (int)$give[12],
  'equip_fishing' => 0,
  'equip_painting' => 0,
  'equip_sculpting' => 0,
  'equip_carpentry' => 0,
  'equip_jeweling' => 0,
  'equip_electronics' => $give[11],
  'equip_mechanics' => 0,
  'equip_adventuring' => $give[6],
  'equip_hunting' => $give[9],
  'equip_gathering' => 0,
  'equip_smithing' => (int)$give[14],
  'equip_tailoring' => (int)$give[16],
  'equip_crafting' => $give[10],
  'equip_binding' => 0,
  'equip_chemistry' => 0,
  'equip_piloting' => (int)$give[17],
  'equip_stealth' => $give[8],
  'equip_athletics' => $give[7],
  'equip_fertility' => (int)$give[15],
);


$command = 'SELECT COUNT(idnum) AS c  FROM `monster_items`
  WHERE equip_is_revised = \'no\'
  AND custom != \'yes\'
';
$data = $database->FetchSingle($command, 'fetching item count');

$number_left = $data['c'];

$rowclass = begin_row_class();

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Administrative Tools &gt; Most-wanted Items</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4>Updating Old Equipment</h4>
<p>(<?= $number_left ?> more to go, including this one...)</p>
<table>
 <tr>
  <td><?= item_display_extra($item) ?></td>
  <td><b><?= $item['itemname'] ?></b> (<?= $item['itemtype'] ?>)<br />custom = <?= $item['custom'] ?></td>
 </tr>
</table>
<p><b>Materials:</b> <?= $item['recycle_for'] ?></p>
<h5>Old Data</h5>
<table>
 <tr>
  <th valign="top">Requires</th>
  <td><?= render_stat_list($require) ?></td>
 </tr>
 <tr>
  <th valign="top">Gives</th>
  <td><?= render_stat_list($give) ?></td>
 </tr>
</table>
<?php
if($item['equipreincarnateonly'] != 'no')
  echo '<p>This equipment is for reincarnated pets only!</p>';
if($item['equipl33tonly'] != 'no')
  echo '<p>This equipment is for l33t pets only! &gt;_&gt;</p>';
if($give[9] != 0)
  echo '<p class="failure">This equipment used to boost Survival!  Make sure it now boosts the appropriate gathering/hunting-type skill!</p>';
if($give[10] != 0)
  echo '<p class="failure">This equipment used to boost Crafts!  Make sure it now boosts the appropriate crafting skill!</p>';
if($give[11] != 0)
  echo '<p class="failure">This equipment used to boost Invention!  Make sure it now boosts the appropriate science-y skill!</p>';
if($give[13] != 0)
  echo '<p class="failure">This equipment used to boost Capturing!  (Oh my!)  Redirect those points elsewhere.</p>';
?>
<h5>New Data</h5>
<form action="update_old_equipment.php" method="post">
<table>
<tr class="<?= $rowclass ?>">
<?php
$i = 0;
foreach($new_stats as $key=>$value)
{
  $i++;
?>
 <th class="righted"><?= $key ?></th>
 <td style="padding-right:4em;"><input type="text" name="<?= $key ?>" value="<?= $value ?>" size="3" maxlength="3" /></td>
<?php
  if($i % 3 == 0)
  {
    $rowclass = alt_row_class($rowclass);
    echo '</tr><tr class="' . $rowclass . '">';
  }
}
?>
</tr>
</table>
<p><input type="submit" name="action" value="Submit" /></p>
</form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
