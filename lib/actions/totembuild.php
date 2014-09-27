<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/itemlib.php';
require_once 'commons/totemlib.php';
require_once 'commons/questlib.php';

$totem = get_totem_byuserid($user['idnum']);

if($totem === false)
  $okay = true;
else
{
  $pieces = take_apart(',', $totem['totem']);
  $okay = (count($pieces) < 100);
}
/*
if($user['display'] == 'Anju')
  $okay = false;
*/
if($user['show_totemgardern'] == 'no')
{
  echo '<p><i>You set it up in the lawn outside, but then realize a problem: assuming you collect enough to build a tall totem pole, a strong gust of wind or earthquake could knock the whole thing over.</i></p>' .
       '<p><i>You\'re going to need some kind of supporting structure... maybe the smith, Nina, would know something about it.</i></p>';

  $questval = get_quest_value($user['idnum'], 'TotemGardenActivation');

  if($questval === false)
    add_quest_value($user['idnum'], 'TotemGardenActivation', 1);
}
else if($okay)
{
  if($_GET['step'] == 2)
  {
    delete_inventory_byid($this_inventory["idnum"]);

    $piece = substr($this_item['graphic'], 7, 2);

    if($totem === false)
    {
      echo '<p>You put down the first piece of your totem pole: The ' . $this_item['itemname'] . '.</p>';

      $score = totem_score(array($piece));

      create_totem($user['idnum'], $piece);
      set_totem_score($user['idnum'], $score);
    }
    else
    {
      echo '<p>You add the ' . $this_item['itemname'] . ' to your totem pole.</p>';

      $pieces[] = $piece;

      replace_totem_byuserid($user['idnum'], $pieces);
    }

    require_once 'commons/statlib.php';

    record_stat($user['idnum'], 'Totems Added to Totem Poles', 1);

    echo '<ul><li><a href="/totempoles.php">View my totem pole</a></li></ul>';
  }
  else
  {
?>
<p>Add this totem to your totem pole?</p>
<p><ul>
 <li><a href="itemaction.php?idnum=<?= $this_inventory['idnum'] ?>&step=2">Yes, by all means!</a></li>
 <li><a href="totempoles.php">No - show me my totem pole instead.</a></li>
</ul>
<?php
    $silly_totem_quest = get_quest_value($user['idnum'], 'totem quest');

    if($silly_totem_quest['value'] >= 2 && $this_inventory['itemname'] == 'Silly Totem' && $this_inventory['idnum'] % 10 == 0)
    {
      echo '
        <p>You notice that <em>this</em> Silly Totem has the same markings as the Silly Totem with Markings Matalie once gave you.  Curious.</p>
        <p><img src="/gfx/books/silly-totem-markings.png" width="64" height="48" alt="" /></p>
      ';
    }
  }
}
else
  echo '<p>Your totem pole already has 100 totems.  You cannot build it any higher.</p>';
?>
<h5>Other Totems in This Room</h5>
<?php
$search_time = microtime(true);

$command = 'SELECT a.idnum,a.itemname,b.action,count(a.idnum) AS qty FROM monster_inventory AS a LEFT JOIN monster_items AS b ON a.itemname=b.itemname WHERE a.location=' . quote_smart($this_inventory['location']) . ' AND a.user=' . quote_smart($user['user']) . ' AND b.itemtype=\'craft/sculpture/totem\' AND a.idnum!=' . $this_inventory['idnum'] . ' GROUP BY(a.itemname)';
$others = $database->FetchMultiple($command, 'fetching other totems');

$search_time = microtime(true) - $search_time;

$footer_note = '<br />Took ' . round($search_time, 4) . 's searching for other totems.';

if(count($others) == 0)
{
  echo '<p>None</p>';
}
else
{
  echo '<ul>';
  foreach($others as $other)
  {
    if($other['action'] != '')
      echo '<li><a href="itemaction.php?idnum=' . $other['idnum'] . '">' . $other['qty'] . '&times; ' . $other['itemname'] . '</a></li>';
    else
      echo '<li>' . $other['qty'] . '&times; ' . $other['itemname'] . '</li>';
  }
  echo '</ul>';
}
?>
