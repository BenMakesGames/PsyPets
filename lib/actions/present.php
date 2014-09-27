<?php
if($okay_to_be_here !== true)
  exit();

list($gifterid, $opentime, $itemlist) = explode(';', $this_inventory['data']);

$items = explode('|', $itemlist);
$gifter = get_user_byid($gifterid, 'display');

$gifter_name = ($gifter === false ? '<i class="dim">[departed #' . $gifterid . ']</i>' : resident_link($gifter['display'])); 

$shake_desc = '<p>(Shaking the ' . $this_inventory['itemname'] . ' leads you to believe that there ';

mt_srand($this_inventory['idnum']);
if(count($items) < 10)
  $item_guess = count($items) + mt_rand(-1, 1);
else
  $item_guess = floor(count($items) * mt_rand(80, 120) / 100);

if($item_guess < 1)
  $item_guess = 1;

if($item_guess == 1)
  $shake_desc .= 'is 1 item';
else
  $shake_desc .= 'are ' . $item_guess . ' items';
 
$shake_desc .= ' inside.  But you could be wrong.)</p>';  

if($opentime > $now + (24 * 60 * 60)) // more than a day
{
  echo '<p>This gift from ' . $gifter_name . ' may not be opened until ' . date('l M jS, Y', $opentime) . '.</p><p>You\'ll just have to wait.</p>' .
       $shake_desc;
}      
else if($opentime > $now + 60) // more than a minute
{
  echo '<p>This gift from ' . $gifter_name . ' may not be opened for another ' . duration($opentime - $now, 2) . '.</p><p>You\'ll just have to wait.</p>' .
       $shake_desc;
}
else if($opentime > $now) // allllmost
{
  echo '<p>This gift from ' . $gifter_name . ' may not be opened for another minute - just one minute!</p><p>And yes, you\'ll have to wait it out. (So cruel!)</p>' .
       $shake_desc;
}
else if($_GET['step'] == 2)
{
  echo '<p>Opening the ' . $this_inventory['itemname'] . ' reveals:</p><ul><li>' .
       implode('</li><li>', $items) . '</li></ul>';

  delete_inventory_byid($this_inventory['idnum']);

  foreach($items as $itemname)
    add_inventory($user['user'], '', $itemname, $this_inventory['message'], $this_inventory['location']);
  
  $AGAIN_WITH_ANOTHER = true;
}
else
{
  echo '<p>This is a gift from '  . $gifter_name . '.  Open it up?</p>' .
       $shake_desc .
       '<ul><li><a href="itemaction.php?idnum=' . $this_inventory['idnum'] . '&step=2">You have to ask?  Open it up!</a></li></ul>';
}
?>
