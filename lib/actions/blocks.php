<?php
if($okay_to_be_here !== true)
  exit();

if($_GET['step'] == 2)
{
	$items = array(
    'Blue D Block', 'Blue F Block', 'Blue J Block', 'Blue G Block', 'Blue O Block',
    'Blue P Block', 'Blue Q Block', 'Blue R Block', 'Blue S Block', 'Blue X Block',

    'Green B Block', 'Green G Block', 'Green H Block', 'Green K Block', 'Green N Block',
    'Green U Block', 'Green V Block', 'Green Y Block', 'Green Z Block',

    'Orange D Block',

    'Purple A Block', 'Purple B Block', 'Purple C Block', 'Purple E Block', 'Purple I Block',
    'Purple J Block', 'Purple M Block', 'Purple N Block', 'Purple O Block', 'Purple T Block',
    'Purple W Block', 'Purple X Block',

    'Red A Block', 'Red C Block', 'Red H Block', 'Red I Block', 'Red L Block', 'Red P Block',
    'Red R Block', 'Red S Block', 'Red Z Block',

    'Yellow F Block', 'Yellow K Block', 'Yellow L Block', 'Yellow M Block', 'Yellow Q Block',
    'Yellow T Block', 'Yellow U Block', 'Yellow V Block', 'Yellow Y Block',
  );

	delete_inventory_byid($this_inventory['idnum']);

	for($x = 0; $x <= 10; ++$x)
	{
	  $itemname = $items[array_rand($items)];
		$itemlist[] = $itemname;
    add_inventory($user['user'], '', $itemname, '', $this_inventory['location']);
	}

  echo 'You received the following: ' . implode(', ', $itemlist) . '.';
	
	$AGAIN_WITH_ANOTHER = true;
}
else
{
?>
If you take the ABC Blocks apart, you'll receive a random assortment of 10 individual lettered blocks, but there's no going back!</p>
<p>Are you cool with that?</p>
<ul>
 <li><a href="itemaction.php?idnum=<?= $this_inventory['idnum'] ?>&amp;step=2">Cool like a fool in a swimming pool!</a></li>
</ul>
<?php
}
?>