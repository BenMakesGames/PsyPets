<?php
if($okay_to_be_here !== true)
  exit();

$info = explode(';', $this_inventory['data']);
$step = (int)$info[0];

require_once 'commons/itemlib.php';

if($step == 0)
{
  $items = get_inventory_byuser($user['user'], $this_inventory['location']);

  $key_gold = false;
  $key_silver = false;
  $key_copper = false;

  foreach($items as $item)
  {
    if($item['itemname'] == 'Gold Key')
      $key_gold = true;
    else if($item['itemname'] == 'Silver Key')
      $key_silver = true;
    else if($item['itemname'] == 'Copper Key')
      $key_copper = true;
  }
}
else if($step == 1)
{
  $items = get_inventory_byuser($user['user'], $this_inventory['location']);

  $troll_food = false;

  foreach($items as $item)
  {
    if($item['itemname'] == 'Duck Plushy Covered in Coconut Juice')
      $troll_food = true;
  }
}

$update_data = false;

if($_POST['action'] == 'submit')
{
  if($step == 0)
  {
    if($_POST['key'] == 'gold' && $key_gold)
    {
      delete_inventory_byname($user['user'], 'Gold Key', 1, $this_inventory['location']);
      $step = 3;
      $update_step = true;
    }
    else if($_POST['key'] == 'silver' && $key_silver)
    {
      delete_inventory_byname($user['user'], 'Silver Key', 1, $this_inventory['location']);
      $step = 2;
      $update_step = true;
    }
    else if($_POST['key'] == 'copper' && $key_copper)
    {
      delete_inventory_byname($user['user'], 'Copper Key', 1, $this_inventory['location']);
      $step = 1;
      $update_step = true;
    }
    
    if($update_step)
    {
      require_once 'commons/statlib.php';
      record_stat($user['idnum'], 'Unlocked a ' . $this_inventory['itemname'] . ' with a ' . ucfirst($_POST['key']) . ' Key', 1);
    }
  }
  else if($step == 1)
  {
    if($troll_food)
    {
      delete_inventory_byname($user['user'], 'Duck Plushy Covered in Coconut Juice', 1, $this_inventory['location']);
      delete_inventory_byid($this_inventory['idnum']);
      add_inventory($user['user'], '', 'Wand of Wonder', '', $this_inventory['location']);
    }
    
    $step = 6;
  }
  else if($step == 2 || $step == 4)
  {
    $keyboard = strtolower(trim($_POST['keyboard']));
    $keyboard = str_replace(array(',', '.'), array('', ''), $keyboard);
    if($keyboard == 'the longer these messages are the easier they are to solve')
    {
      delete_inventory_byid($this_inventory['idnum']);
      add_inventory($user['user'], '', 'Treasure Chest', '', $this_inventory['location']);
      
      $step = 7;
    }
    else
    {
      if($step == 2)
      {
        $step = 4;
        $update_step = true;
      }
      else
      {
        delete_inventory_byid($this_inventory['idnum']);
        $step = 9;
      }
    }
  }
  else if($step == 3 || $step == 5)
  {
    $speak = strtolower(trim($_POST['speak']));
    if($speak == 'claudius' || $speak == 'king claudius')
    {
      delete_inventory_byid($this_inventory['idnum']);
      add_inventory($user['user'], '', 'Purple Pebble', '', $this_inventory['location']);

      $step = 8;
    }
    else
    {
      delete_inventory_byid($this_inventory['idnum']);
      $step = 9;
    }
  }
}

if($update_step)
{
  $database->FetchNone("UPDATE monster_inventory SET data='$step' WHERE idnum=" . $this_inventory['idnum'] . " LIMIT 1");
}

if($step == 0)
{
  if($key_gold || $key_silver || $key_copper)
  {
?>
Which key will you use?</p>
<p>
<form action="itemaction.php?idnum=<?= $this_inventory['idnum'] ?>" method="post">
<select name="key">
<?php
if($key_gold)
  echo "<option value=\"gold\">Gold Key</option>\n";
if($key_silver)
  echo "<option value=\"silver\">Silver Key</option>\n";
if($key_copper)
  echo "<option value=\"copper\">Copper Key</option>\n";
?>
</select> <input type="hidden" name="action" value="submit" /><input type="submit" value="Turn That Key" />
</form>
<?php
  }
  else
  {
?>
You don't have anything to open it with.
<?php
  }
}
else if($step == 1)
{
?>
A troll blocks your path across the bridge.</p>
<p>It bellows: "I AM HUNGRY AND LONELY!  BRING ME STUFFED DUCK!  MUST BE COATED WITH COCONUT'S WATER!"</p>
<p>
<?php
  if($troll_food)
  {
?>
<form action="itemaction.php?idnum=<?= $this_inventory["idnum"] ?>" method="post">
<input type="hidden" name="action" value="submit" /><input type="submit" value="give duck to troll" />
</form>
<?php
  }
}
else if($step == 2)
{
?>
A large, mechanical door sits in front of you.  Attached to it by a two cords are a keyboard and a small LCD display.</p>
<p>The LCD display reads: "ENTER PASSWORD."</p>
<p>A yellow sticky note stuck to the corner of the LCD display reads: "RSJ ULATJE RSJMJ YJMMKTJM KEJ, RSJ JKMQJE RSJP KEJ RL MLUBJ."</p>
<p>
<form action="itemaction.php?idnum=<?= $this_inventory["idnum"] ?>" method="post">
<input type="hidden" name="action" value="submit" /><input name="keyboard" /> <input type="submit" value="press enter" />
</form>
<?php
}
else if($step == 3)
{
?>
You stand amidst a brackish bog.</p>
<p>A ghost shares your midst.  It (the ghost) says:</p>
<p>
"My son-in-law's mad -<br />
his friends agree -<br />
he called my adjunct<br />
a fishmonger, you see.<br />
My wife is dead<br />
by my own hand.<br />
I, too, have passed<br />
beyond mortal land.<br />
<br />
Who am I?"<br />
</p>
<p><i>No need to be sneaky - just his name will do.</i></p>
</p>
<p>
<form action="itemaction.php?idnum=<?= $this_inventory["idnum"] ?>" method="post">
<input type="hidden" name="action" value="submit" /><input name="speak" /> <input type="submit" value="speak" />
</form>
<?php
}
else if($step == 4)
{
?>
A large, mechanical door sits in front of you.  Attached to it by a two cords are a keyboard and a small LCD display.</p>
<p>The LCD display reads: "BAD PASSWORD.  LAST CHANCE.  ENTER PASSWORD."</p>
<p>The yellow sticky note still reads (as you'd expect): "RSJ ULATJE RSJMJ YJMMKTJM KEJ, RSJ JKMQJE RSJP KEJ RL MLUBJ."</p>
<p>
<form action="itemaction.php?idnum=<?= $this_inventory["idnum"] ?>" method="post">
<input type="hidden" name="action" value="submit" /><input name="keyboard" /> <input type="submit" value="press enter" />
</form>
<?php
}
else if($step == 6)
{
?>
You cross the bridge without incident and find yourself back home.  You spin around to see the Key Hole spit a Wand of Wonder on to the ground before imploding.
<?php
}
else if($step == 7)
{
?>
You swing open the door and walk out, finding yourself back home.  You spin around to see the Key Hole spit a Treasure Chest on to the ground before imploding.
<?php
}
else if($step == 8)
{
?>
The king nods and stands aside.  All the same, you turn your head to watch him cautiously as you pass.</p>
<p>When you face forward again, you find yourself looking at a corner in your house.  You spin around to see the Key Hole spit a Purple Pebble on to the ground before imploding.
<?php
}
else if($step == 9)
{
?>
Your vision fades, and then you feel as if you're falling, faster and faster.</p>
<p>Your vision returns as you finally come to an unexpectedly soft landing.  From your vantage (lying on your back) you can see the Key Hole above you, imploding.  You get to your feet as quickly as possible, but not in time - the Key Hole is gone.
<?php
}
?>
