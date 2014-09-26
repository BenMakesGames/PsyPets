<?php
/*
Raindrops on roses and whiskers on kittens
Bright copper kettles and warm woolen mittens
Brown paper packages tied up with strings
These are a few of my favorite things

Cream colored ponies and crisp apple strudels
Doorbells and sleigh bells and schnitzel with noodles
Wild geese that fly with the moon on their wings
These are a few of my favorite things

Girls in white dresses with blue satin sashes
Snowflakes that stay on my nose and eyelashes
Silver white winters that melt into springs
These are a few of my favorite things

When the dog bites
When the bee stings
When I'm feeling sad
I simply remember my favorite things
And then I don't feel so bad
*/

if($okay_to_be_here !== true)
  exit();

$AGAIN_WITH_ANOTHER = true;

$item_options = array(
  1 => 'Bright Copper Kettle',
  2 => 'Cream-colored Pony Plushy',
  3 => 'Warm Woolen Mittens',
);

if($_POST['action'] == 'pick')
{
?>
Opening the <?= $this_item["itemname"] ?> reveals:
<?php

  foreach($items as $item)
    add_inventory($user["user"], '', $item, "Found in a " . $this_item["itemname"], $this_inventory["location"]);
}
else
{
?>
Pick three of your favorite things:</p>
<ul>
 <li>
<?php
}
?>
