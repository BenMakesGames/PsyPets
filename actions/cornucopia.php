<?php
if($okay_to_be_here !== true)
  exit();

$foods = array(
  'Banana', 'Beet', 'Blueberries', 'Carrot', 'Corn', 'Cucumber', 'Delicious', 'Greenish Leaf',
  'Kumquat', 'Leafy Cabbage', 'Olives', 'Onion', 'Orange', 'Pamplemousse', 'Peanuts', 'Peppers', 'Pineapple',
  'Pomegranate', 'Potato', 'Prickly Green', 'Redsberries', 'Rice', 'Sour Lime', 'Spaghetti Squash',
  'Sugar Beet', 'Tomato', 'Wheat', 'White Radish', 'Yam',
);

require_once 'commons/questlib.php';

$harvest = get_quest_value($user['idnum'], 'Cornucopia');

$data = (int)$harvest['value'];
$day = 60 * 60 * 30;

$ripe = false;
$harvested = false;

if($harvest === false)
{
  $ripe = true;

  add_quest_value($user['idnum'], 'Cornucopia', 0);
}
else if($now > $data)
  $ripe = true;

if($_POST['action'] == 'harvest' && $ripe === true)
{
  if(array_key_exists($_POST['food'], $foods))
  {
    $data = $now + $day;

    update_quest_value($harvest['idnum'], $data);

    add_inventory($user['user'], '', $foods[$_POST['food']], 'Harvested from a ' . $this_inventory['itemname'], $this_inventory['location']);

    $ripe = false;
    $harvested = true;
  }
}

if($harvested)
  echo '<p>You pluck ' . $foods[$_POST['food']] . ' from the ' . $this_inventory['itemname'] . '.</p>';
else if($ripe)
{
?>
<form method="post">
<table>
 <tr>
<?php
if($this_inventory['itemname'] == 'Cornucopia')
  echo '<td valign="top"><img src="gfx/large_cornucopia.png" alt="" /></td>';
?>
  <td>
   <p>What will you take from the <?= $this_inventory['itemname'] ?>?</p>
   <p><select name="food">
<?php
  foreach($foods as $index=>$food)
    echo '    <option value="' . $index . '">' . $food . '</option>' . "\n";
?>
   </select>&nbsp;<input type="hidden" name="action" value="harvest" /><input type="submit" value="Harvest" /></p>
  </td>
 </tr>
</table>
</form>
<?php
}
else
  echo '<p>The ' . $this_inventory['itemname'] . ' is not yet ready to share its bounty.</p>';
?>
